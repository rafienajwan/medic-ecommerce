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
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category)
                  ->orWhere('id', $request->category);
            });
        }

        // Search by name or description
        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%")
                  ->orWhere('sku', 'ILIKE', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 12);
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
