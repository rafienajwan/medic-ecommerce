<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get all orders containing vendor's products
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        // Get order items that contain this vendor's products
        $orderItems = OrderItem::whereHas('product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })
        ->with(['order.user', 'product'])
        ->latest()
        ->paginate(20);

        // Transform data for easier display
        $data = $orderItems->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'customer_name' => $item->order->user->name ?? 'N/A',
                'customer_email' => $item->order->user->email ?? 'N/A',
                'customer_phone' => $item->order->user->phone ?? 'N/A',
                'shipping_address' => $item->order->shipping_address,
                'product_name' => $item->product->name,
                'product_image' => $item->product->image_url,
                'product_sku' => $item->product->sku,
                'quantity' => $item->quantity,
                'amount' => $item->subtotal,
                'status' => $item->order->status,
                'payment_method' => $item->order->payment_method,
                'created_at' => $item->created_at,
                'updated_at' => $item->order->updated_at,
            ];
        });

        return response()->json([
            'data' => $data,
            'links' => $orderItems->linkCollection(),
            'from' => $orderItems->firstItem(),
            'to' => $orderItems->lastItem(),
            'total' => $orderItems->total(),
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $orderId)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        // Check if this order contains vendor's products
        $order = Order::whereHas('items.product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->findOrFail($orderId);

        // Update order status
        $order->update([
            'status' => $validated['status']
        ]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }

    /**
     * Get vendor dashboard stats
     */
    public function dashboardStats()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        // Get order items with vendor's products
        $orderItems = OrderItem::whereHas('product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->with('order')->get();

        $stats = [
            'total_orders' => $orderItems->count(),
            'pending_orders' => $orderItems->filter(fn($item) => $item->order->status === 'pending')->count(),
            'processing_orders' => $orderItems->filter(fn($item) => $item->order->status === 'processing')->count(),
            'shipped_orders' => $orderItems->filter(fn($item) => $item->order->status === 'shipped')->count(),
            'delivered_orders' => $orderItems->filter(fn($item) => $item->order->status === 'delivered')->count(),
        ];

        return response()->json($stats);
    }
}
