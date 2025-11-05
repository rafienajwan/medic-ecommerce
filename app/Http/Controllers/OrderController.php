<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['items.product', 'payment'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['items.product', 'payment'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:100',
            'shipping_province' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'payment_method' => 'required|in:prepaid,cod,paypal',
            'bank_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        // Validate bank_type for prepaid
        if ($validated['payment_method'] === 'prepaid' && empty($validated['bank_type'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bank type is required for credit/debit card payment'
            ], 400);
        }

        // Get user's cart
        $cart = Cart::with('items.product')
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Check stock availability
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$item->product->name}"
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = $cart->items->sum(function ($item) {
                return $item->price * $item->quantity;
            });
            $tax = $subtotal * 0.11; // 11% PPN
            $shipping_fee = 15000; // Flat rate for now
            $total = $subtotal + $tax + $shipping_fee;

            // Create order
            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'shipping_name' => $validated['shipping_name'],
                'shipping_email' => $validated['shipping_email'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'payment_method' => $validated['payment_method'],
                'bank_type' => $validated['bank_type'] ?? null,
                'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_fee' => $shipping_fee,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            // Create order items and reduce stock
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->price * $cartItem->quantity,
                ]);

                // Reduce product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'method' => $validated['payment_method'],
                'status' => 'pending',
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
            ]);

            // Generate and save PDF invoice
            $invoicePath = $this->generateInvoice($order->fresh(['items.product', 'user']));
            $order->update(['invoice_path' => $invoicePath]);

            // Send email with invoice
            $this->sendInvoiceEmail($order->fresh(['items.product', 'user']), $invoicePath);

            // Clear cart
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $order->fresh(['items.product', 'payment'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function generateInvoice($order)
    {
        $pdf = Pdf::loadView('invoices.order', ['order' => $order]);

        $filename = 'invoice-' . $order->order_number . '.pdf';
        $path = 'invoices/' . $filename;

        // Save to storage
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    protected function sendInvoiceEmail($order, $invoicePath)
    {
        try {
            $pdfPath = storage_path('app/public/' . $invoicePath);

            Mail::send('emails.invoice', ['order' => $order], function ($message) use ($order, $pdfPath) {
                $message->to($order->shipping_email, $order->shipping_name)
                    ->subject('Invoice for Order ' . $order->order_number)
                    ->attach($pdfPath, [
                        'as' => 'invoice-' . $order->order_number . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to send invoice email: ' . $e->getMessage());
        }
    }

    public function downloadInvoice($id)
    {
        if (!Auth::check()) {
            abort(401);
        }

        $order = Order::with(['items.product', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($order->invoice_path && Storage::disk('public')->exists($order->invoice_path)) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            return $disk->download($order->invoice_path);
        }

        // Regenerate if not exists
        $invoicePath = $this->generateInvoice($order);
        $order->update(['invoice_path' => $invoicePath]);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->download($invoicePath);
    }
}
