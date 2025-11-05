<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
{
    /**
     * Get reviews for a product
     */
    public function index($productId)
    {
        $reviews = ProductReview::with(['user'])
            ->where('product_id', $productId)
            ->approved()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = ProductReview::where('product_id', $productId)
            ->approved()
            ->selectRaw('
                AVG(rating) as average_rating,
                COUNT(*) as total_reviews,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            ')
            ->first();

        return response()->json([
            'reviews' => $reviews,
            'statistics' => $stats
        ]);
    }

    /**
     * Store new review
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:200'],
            'review' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user already reviewed this product
        $existingReview = ProductReview::where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this product'
            ], 400);
        }

        // Check if verified purchase
        $isVerifiedPurchase = false;
        if ($request->order_id) {
            $order = Order::where('id', $request->order_id)
                ->where('user_id', Auth::id())
                ->whereHas('items', function ($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                })
                ->whereIn('status', ['delivered', 'processing', 'shipped'])
                ->first();

            $isVerifiedPurchase = $order !== null;
        }

        $review = ProductReview::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'review' => $request->review,
            'is_verified_purchase' => $isVerifiedPurchase,
            'is_approved' => true, // Auto-approve
        ]);

        AuditLog::log('review_created', $review, 'Product review created', [
            'product_id' => $request->product_id,
            'rating' => $request->rating
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review->load('user')
        ], 201);
    }

    /**
     * Get user's own reviews
     */
    public function myReviews()
    {
        $reviews = ProductReview::with(['product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($reviews);
    }

    /**
     * Delete own review
     */
    public function destroy($id)
    {
        $review = ProductReview::where('user_id', Auth::id())->findOrFail($id);

        AuditLog::log('review_deleted', $review, 'Review deleted by user');

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
