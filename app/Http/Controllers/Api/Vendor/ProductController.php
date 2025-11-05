<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Get all products for the authenticated vendor
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $products = Product::where('vendor_id', $vendor->id)
            ->with('category')
            ->latest()
            ->paginate(20);

        return response()->json($products);
    }

    /**
     * Store a new product
     */
    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        if ($vendor->status !== 'approved') {
            return response()->json(['message' => 'Vendor must be approved to create products'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'sku' => 'required|string|max:100|unique:products,sku',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048', // Only images, max 2MB
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Validate it's actually an image
            if (!$image->isValid()) {
                throw ValidationException::withMessages([
                    'image' => ['The uploaded file is not valid.']
                ]);
            }

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Store in storage/app/public/products
            $path = $image->storeAs('products', $filename, 'public');

            $validated['image_url'] = '/storage/' . $path;
        }

        $validated['vendor_id'] = $vendor->id;
        $validated['is_active'] = true;

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product->load('category')
        ], 201);
    }

    /**
     * Update a product
     */
    public function update(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:200',
            'sku' => 'sometimes|string|max:100|unique:products,sku,' . $id,
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png,gif,webp|max:2048', // Only images
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Validate it's actually an image
            if (!$image->isValid()) {
                throw ValidationException::withMessages([
                    'image' => ['The uploaded file is not valid.']
                ]);
            }

            // Delete old image if exists
            if ($product->image_url && str_starts_with($product->image_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $product->image_url);
                Storage::disk('public')->delete($oldPath);
            }

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Store in storage/app/public/products
            $path = $image->storeAs('products', $filename, 'public');

            $validated['image_url'] = '/storage/' . $path;
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product->load('category')
        ]);
    }

    /**
     * Delete a product
     */
    public function destroy($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);

        // Delete image if exists
        if ($product->image_url && str_starts_with($product->image_url, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $product->image_url);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Get all categories for dropdown
     */
    public function categories()
    {
        $categories = Category::select('id', 'name', 'slug')->orderBy('name')->get();

        return response()->json(['data' => $categories]);
    }
}
