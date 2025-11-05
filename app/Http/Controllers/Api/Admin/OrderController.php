<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get all orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by order number
        if ($request->has('search') && $request->search) {
            $query->where('order_number', 'ILIKE', "%{$request->search}%");
        }

        // Date range filter
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $orders
        ]);
    }

    /**
     * Get single order detail
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);

        return response()->json([
            'data' => $order
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;

        $updateData = ['status' => $request->status];

        if ($request->has('notes')) {
            $updateData['notes'] = $order->notes ? $order->notes . "\n" . $request->notes : $request->notes;
        }

        // If cancelling, restore stock
        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }

        $order->update($updateData);

        AuditLog::log('order_status_updated', $order, 'Admin updated order status', [
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'order_number' => $order->order_number
        ]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order->load(['user', 'items.product'])
        ]);
    }

    /**
     * Generate monthly transaction report
     */
    public function monthlyReport(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $startDate = \Carbon\Carbon::parse($from)->startOfDay();
        $endDate = \Carbon\Carbon::parse($to)->endOfDay();

        // Get statistics
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->sum('total');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        $totalCustomers = Order::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Payment methods breakdown
        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->pluck('count', 'payment_method')
            ->toArray();

        // Order status breakdown
        $orderStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Top products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('sum(order_items.quantity) as total_sold'),
                DB::raw('sum(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Top vendors
        $topVendors = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('vendors', 'products.vendor_id', '=', 'vendors.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'vendors.business_name',
                DB::raw('count(distinct orders.id) as total_orders'),
                DB::raw('sum(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('vendors.id', 'vendors.business_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'total_customers' => $totalCustomers,
                'avg_order_value' => round($avgOrderValue, 2),
                'payment_methods' => $paymentMethods,
                'order_status' => $orderStatus,
                'top_products' => $topProducts,
                'top_vendors' => $topVendors,
            ]
        ]);
    }
}
