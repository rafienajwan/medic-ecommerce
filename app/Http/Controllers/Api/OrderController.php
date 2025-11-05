<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * Get specific order
     */
    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.product', 'payment'])
            ->findOrFail($id);

        return response()->json($order);
    }

    /**
     * Checkout - Create order from cart
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => ['required', 'in:prepaid,postpaid,cod,paypal'],
            'shipping_address' => ['required', 'string'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
            // For prepaid payment
            'card_number' => ['required_if:payment_method,prepaid', 'nullable', 'string'],
            'card_holder' => ['required_if:payment_method,prepaid', 'nullable', 'string'],
            'card_expiry' => ['required_if:payment_method,prepaid', 'nullable', 'string'],
            'card_cvv' => ['required_if:payment_method,prepaid', 'nullable', 'string'],
            // For PayPal
            'paypal_email' => ['required_if:payment_method,paypal', 'nullable', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = $cart->calculateTotal();
            $tax = $subtotal * 0.10; // 10% tax
            $shippingFee = 10000; // Fixed shipping fee
            $total = $subtotal + $tax + $shippingFee;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_phone' => $request->shipping_phone,
                'notes' => $request->notes,
            ]);

            // Create order items and decrease product stock
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;

                // Check stock availability
                if ($product->stock < $cartItem->quantity) {
                    throw new \Exception("Product {$product->name} is out of stock");
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->price * $cartItem->quantity,
                ]);

                // Decrease stock
                $product->decreaseStock($cartItem->quantity);
            }

            // Create payment record
            $payment = $this->processPayment($order, $request);

            // Clear cart
            $cart->clearCart();

            // Log order creation
            AuditLog::log('order_created', $order, 'Order created successfully', [
                'order_number' => $order->order_number,
                'total' => $total
            ]);

            DB::commit();

            // Generate and send invoice
            try {
                $this->generateAndSendInvoice($order);
            } catch (\Exception $e) {
                // Log error but don't fail the order
                \Log::error('Failed to generate invoice: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['items.product', 'payment'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment (mock implementation)
     */
    private function processPayment(Order $order, Request $request)
    {
        $paymentData = [
            'order_id' => $order->id,
            'amount' => $order->total,
            'method' => $request->payment_method,
            'status' => 'pending',
        ];

        // Mock payment processing based on method
        switch ($request->payment_method) {
            case 'prepaid':
                // Simulate credit card payment
                $paymentData['transaction_ref'] = 'TXN-' . strtoupper(uniqid());
                $paymentData['card_last_four'] = substr($request->card_number, -4);
                $paymentData['payment_details'] = [
                    'card_holder' => $request->card_holder,
                    'card_type' => $this->detectCardType($request->card_number),
                ];
                // Simulate immediate payment
                $paymentData['status'] = 'completed';
                $paymentData['paid_at'] = now();
                break;

            case 'paypal':
                // Simulate PayPal payment
                $paymentData['transaction_ref'] = 'PAYPAL-' . strtoupper(uniqid());
                $paymentData['payment_details'] = [
                    'paypal_email' => $request->paypal_email,
                ];
                $paymentData['status'] = 'completed';
                $paymentData['paid_at'] = now();
                break;

            case 'cod':
            case 'postpaid':
                // Payment on delivery
                $paymentData['status'] = 'pending';
                $paymentData['payment_details'] = [
                    'method_type' => 'Cash on Delivery',
                ];
                break;
        }

        $payment = Payment::create($paymentData);

        // Update order status if payment completed
        if ($payment->isCompleted()) {
            $order->update(['status' => 'processing']);
        }

        return $payment;
    }

    /**
     * Detect card type (mock)
     */
    private function detectCardType($cardNumber)
    {
        $firstDigit = substr($cardNumber, 0, 1);

        return match($firstDigit) {
            '4' => 'Visa',
            '5' => 'Mastercard',
            '3' => 'American Express',
            default => 'Unknown'
        };
    }

    /**
     * Generate and send invoice PDF
     */
    private function generateAndSendInvoice(Order $order)
    {
        $order->load(['items.product', 'payment', 'user']);

        // Generate PDF
        $pdf = Pdf::loadView('emails.invoice', ['order' => $order]);

        // Save PDF to storage
        $filename = "invoice-{$order->order_number}.pdf";
        $path = storage_path("app/invoices/{$filename}");

        if (!file_exists(storage_path('app/invoices'))) {
            mkdir(storage_path('app/invoices'), 0755, true);
        }

        $pdf->save($path);

        // Send email (mock - using log driver)
        try {
            Mail::send('emails.invoice-notification', ['order' => $order], function ($message) use ($order, $path) {
                $message->to($order->user->email)
                    ->subject("Invoice - Order {$order->order_number}")
                    ->attach($path);
            });

            AuditLog::log('invoice_sent', $order, 'Invoice sent to customer email');
        } catch (\Exception $e) {
            \Log::error('Failed to send invoice email: ' . $e->getMessage());
        }
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        $order->load(['items.product', 'payment', 'user']);

        $pdf = Pdf::loadView('emails.invoice', ['order' => $order]);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    /**
     * Cancel order (customer can cancel before shipping)
     */
    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'message' => 'Order cannot be cancelled. It has already been ' . $order->status
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Restore product stock
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            // Update order status
            $order->update(['status' => 'cancelled']);

            // Update payment status if exists
            if ($order->payment) {
                $order->payment->update(['status' => 'cancelled']);
            }

            AuditLog::log('order_cancelled', $order, 'Order cancelled by customer', [
                'order_number' => $order->order_number,
                'reason' => 'Customer request'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Order cancelled successfully. Stock has been restored.',
                'order' => $order->load(['items.product', 'payment'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
