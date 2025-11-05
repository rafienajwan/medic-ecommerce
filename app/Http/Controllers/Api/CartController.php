<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get user's cart with items
     */
    public function index(Request $request)
    {
        $cart = $this->getOrCreateCart();

        $cart->load(['items.product.category']);

        return response()->json([
            'cart' => $cart,
            'total' => $cart->calculateTotal(),
            'items_count' => $cart->getTotalItemsCount()
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);

        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient stock available'
            ], 400);
        }

        if (!$product->is_active) {
            return response()->json([
                'message' => 'Product is not available'
            ], 400);
        }

        $cart = $this->getOrCreateCart();

        // Check if item already in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($product->stock < $newQuantity) {
                return response()->json([
                    'message' => 'Cannot add more items. Insufficient stock.'
                ], 400);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $product->price
            ]);
        } else {
            // Create new cart item
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price
            ]);
        }

        AuditLog::log('cart_item_added', $cartItem, 'Added product to cart', [
            'product_id' => $product->id,
            'quantity' => $request->quantity
        ]);

        $cart->load(['items.product']);

        return response()->json([
            'message' => 'Item added to cart',
            'cart' => $cart,
            'total' => $cart->calculateTotal()
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = $this->getOrCreateCart();
        $cartItem = $cart->items()->findOrFail($itemId);
        $product = $cartItem->product;

        // Check stock
        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient stock available'
            ], 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'price' => $product->price
        ]);

        AuditLog::log('cart_item_updated', $cartItem, 'Updated cart item quantity');

        $cart->load(['items.product']);

        return response()->json([
            'message' => 'Cart item updated',
            'cart' => $cart,
            'total' => $cart->calculateTotal()
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy($itemId)
    {
        $cart = $this->getOrCreateCart();
        $cartItem = $cart->items()->findOrFail($itemId);

        AuditLog::log('cart_item_removed', $cartItem, 'Removed item from cart');

        $cartItem->delete();

        $cart->load(['items.product']);

        return response()->json([
            'message' => 'Item removed from cart',
            'cart' => $cart,
            'total' => $cart->calculateTotal()
        ]);
    }

    /**
     * Clear all items from cart
     */
    public function clear()
    {
        $cart = $this->getOrCreateCart();

        AuditLog::log('cart_cleared', $cart, 'Cleared all items from cart');

        $cart->clearCart();

        return response()->json([
            'message' => 'Cart cleared',
            'cart' => $cart,
            'total' => 0
        ]);
    }

    /**
     * Get or create cart for current user
     */
    private function getOrCreateCart()
    {
        $user = Auth::user();

        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['session_id' => session()->getId()]
        );

        return $cart;
    }
}
