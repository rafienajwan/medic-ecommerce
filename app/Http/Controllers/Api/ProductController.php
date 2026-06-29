<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'vendor.user'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category)
                  ->orWhere('id', $request->category);
            });
        }

        // Search by name or description
        if ($request->filled('q')) {
            $search = strtolower(trim($request->q));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(sku) LIKE ?', ["%{$search}%"]);
            });
        }

        // Sort
        $allowedSorts = ['created_at', 'name', 'price', 'stock'];
        $sortBy = in_array($request->get('sort_by'), $allowedSorts, true)
            ? $request->get('sort_by')
            : 'created_at';
        $sortOrder = strtolower($request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min(max((int) $request->get('per_page', 12), 1), 50);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    /**
     * Display the specified product
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'vendor.user'])
            ->findOrFail($id);

        if (!$product->is_active) {
            return response()->json([
                'message' => 'Product not available'
            ], 404);
        }

        return response()->json($product);
    }
}
