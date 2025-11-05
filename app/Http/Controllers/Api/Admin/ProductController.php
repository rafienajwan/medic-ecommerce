<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Get all products (including inactive)
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'vendor.user']);

        // Filter by active status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by vendor
        if ($request->has('vendor_id') && $request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('sku', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $products
        ]);
    }

    /**
     * Create new product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:200'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url'],
            'is_active' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create($request->all());

        AuditLog::log('product_created', $product, 'Admin created new product', [
            'product_name' => $product->name
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load(['category', 'vendor'])
        ], 201);
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:200'],
            'sku' => ['sometimes', 'string', 'max:100', 'unique:products,sku,' . $id],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url'],
            'is_active' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->all());

        AuditLog::log('product_updated', $product, 'Admin updated product');

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->load(['category', 'vendor'])
        ]);
    }

    /**
     * Delete product (soft delete)
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        AuditLog::log('product_deleted', $product, 'Admin deleted product', [
            'product_name' => $product->name
        ]);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
