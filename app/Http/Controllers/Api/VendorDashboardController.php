<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Check if user has vendor account
        if (!$user->vendor) {
            return response()->json([
                'message' => 'You do not have a vendor account'
            ], 403);
        }

        $vendor = $user->vendor;

        // Get products stats
        $totalProducts = Product::where('vendor_id', $vendor->id)->count();
        $activeProducts = Product::where('vendor_id', $vendor->id)
            ->where('is_active', true)
            ->count();
        $lowStockProducts = Product::where('vendor_id', $vendor->id)
            ->where('stock', '<', 10)
            ->count();

        // Get orders stats (orders containing vendor's products)
        $vendorOrderItems = OrderItem::whereHas('product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->with('order');

        $totalOrders = $vendorOrderItems->distinct('order_id')->count('order_id');

        $pendingOrders = OrderItem::whereHas('product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->whereHas('order', function ($query) {
            $query->where('status', 'pending');
        })->distinct('order_id')->count('order_id');

        // Calculate total revenue (from completed orders this month)
        $totalRevenue = OrderItem::whereHas('product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->whereHas('order', function ($query) {
            $query->where('status', 'delivered')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        })->sum(DB::raw('price * quantity'));

        // Get recent products (last 5)
        $recentProducts = Product::where('vendor_id', $vendor->id)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent orders (last 5 orders containing vendor's products)
        $recentOrders = OrderItem::whereHas('product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })
        ->with(['order.user', 'product'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($orderItem) {
            return [
                'id' => $orderItem->order->id,
                'user' => [
                    'name' => $orderItem->order->user->name ?? 'N/A',
                ],
                'product_name' => $orderItem->product->name,
                'amount' => $orderItem->price * $orderItem->quantity,
                'status' => $orderItem->order->status,
                'created_at' => $orderItem->order->created_at,
            ];
        });

        return response()->json([
            'stats' => [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'total_revenue' => $totalRevenue ?? 0,
                'low_stock_products' => $lowStockProducts,
            ],
            'products' => $recentProducts,
            'orders' => $recentOrders,
        ]);
    }
}
