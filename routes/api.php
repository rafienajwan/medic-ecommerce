<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\GuestBookController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductReviewController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\VendorDashboardController;
use App\Http\Controllers\OrderController as MainOrderController;
use App\Http\Controllers\CodCheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes - accessible by guests
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Products - public access (guests can view)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Guest Book - public read, anyone can post (moderation required)
Route::get('/guestbook', [GuestBookController::class, 'index']);
Route::post('/guestbook', [GuestBookController::class, 'store']);

// Product Reviews - public read
Route::get('/products/{productId}/reviews', [ProductReviewController::class, 'index']);

// Protected routes - require authentication
// Using Sanctum guard for SPA authentication (session-based for stateful domains)
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    // Activity tracking
    Route::post('/activity/ping', [ActivityController::class, 'ping']);
    Route::get('/activity/status', [ActivityController::class, 'status']);

    // Cart routes - require login
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::patch('/cart/{itemId}', [CartController::class, 'update']);
    Route::delete('/cart/{itemId}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // COD Check - validate if COD available based on location
    Route::post('/cod/check', [CodCheckController::class, 'checkCodAvailability']);

    // Order routes - require login
    Route::get('/orders', [MainOrderController::class, 'index']);
    Route::get('/orders/{id}', [MainOrderController::class, 'show']);
    Route::post('/orders', [MainOrderController::class, 'store']);
    Route::get('/orders/{id}/invoice', [MainOrderController::class, 'downloadInvoice']);

    // Legacy routes
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/confirm-delivery', [OrderController::class, 'confirmDelivery']); // Customer confirm delivery

    // Guest Book - logged-in users can delete own entries
    Route::delete('/guestbook/{id}', [GuestBookController::class, 'destroy']);

    // Product Reviews - logged-in users can post
    Route::post('/reviews', [ProductReviewController::class, 'store']);
    Route::get('/reviews/my', [ProductReviewController::class, 'myReviews']);
    Route::delete('/reviews/{id}', [ProductReviewController::class, 'destroy']);

    // Vendor routes - customer can apply to become vendor
    Route::get('/vendor/status', [VendorController::class, 'status']);
    Route::post('/vendor/apply', [VendorController::class, 'apply']);
    Route::put('/vendor/update', [VendorController::class, 'update']);
    Route::put('/vendor/profile', [VendorController::class, 'updateProfile']);
    Route::get('/vendor', [VendorController::class, 'show']);

    // Vendor Dashboard - for vendors to manage their business
    Route::get('/vendor/dashboard', [VendorDashboardController::class, 'index']);

    // Vendor Product Management - only for approved vendors
    Route::middleware(['vendor.approved'])->prefix('vendor')->group(function () {
        Route::get('/products', [\App\Http\Controllers\Api\Vendor\ProductController::class, 'index']);
        Route::get('/products/categories', [\App\Http\Controllers\Api\Vendor\ProductController::class, 'categories']);
        Route::post('/products', [\App\Http\Controllers\Api\Vendor\ProductController::class, 'store']);
        Route::put('/products/{id}', [\App\Http\Controllers\Api\Vendor\ProductController::class, 'update']);
        Route::patch('/products/{id}', [\App\Http\Controllers\Api\Vendor\ProductController::class, 'update']);
        Route::post('/products/{id}', [\App\Http\Controllers\Api\Vendor\ProductController::class, 'update']); // For FormData with _method=PUT
        Route::delete('/products/{id}', [\App\Http\Controllers\Api\Vendor\ProductController::class, 'destroy']);

        // Vendor Orders
        Route::get('/orders', [\App\Http\Controllers\Api\Vendor\OrderController::class, 'index']);
        Route::patch('/orders/{orderId}/status', [\App\Http\Controllers\Api\Vendor\OrderController::class, 'updateStatus']);
        Route::get('/dashboard/stats', [\App\Http\Controllers\Api\Vendor\OrderController::class, 'dashboardStats']);
    });    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        // Vendor management
        Route::get('/vendors', [\App\Http\Controllers\Api\Admin\VendorController::class, 'index']);
        Route::get('/vendors/pending', [\App\Http\Controllers\Api\Admin\VendorController::class, 'pending']);
        Route::get('/vendors/{id}', [\App\Http\Controllers\Api\Admin\VendorController::class, 'show']);
        Route::post('/vendors/{id}/approve', [\App\Http\Controllers\Api\Admin\VendorController::class, 'approve']);
        Route::post('/vendors/{id}/reject', [\App\Http\Controllers\Api\Admin\VendorController::class, 'reject']);

        // Product management
        Route::get('/products', [\App\Http\Controllers\Api\Admin\ProductController::class, 'index']);
        Route::post('/products', [\App\Http\Controllers\Api\Admin\ProductController::class, 'store']);
        Route::put('/products/{id}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'update']);
        Route::delete('/products/{id}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'destroy']);

        // Order management
        Route::get('/orders', [\App\Http\Controllers\Api\Admin\OrderController::class, 'index']);
        Route::get('/orders/{id}', [\App\Http\Controllers\Api\Admin\OrderController::class, 'show']);
        Route::patch('/orders/{id}/status', [\App\Http\Controllers\Api\Admin\OrderController::class, 'updateStatus']);

        // Reports
        Route::get('/reports/monthly', [\App\Http\Controllers\Api\Admin\OrderController::class, 'monthlyReport']);

        // User management
        Route::get('/users', [\App\Http\Controllers\Api\Admin\UserController::class, 'index']);
        Route::get('/users/statistics', [\App\Http\Controllers\Api\Admin\UserController::class, 'statistics']);
        Route::get('/users/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'show']);
        Route::put('/users/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'update']);
        Route::delete('/users/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'destroy']);

        // Guest Book management
        Route::get('/guestbook', [\App\Http\Controllers\Api\Admin\GuestBookController::class, 'index']);
        Route::post('/guestbook/{id}/approve', [\App\Http\Controllers\Api\Admin\GuestBookController::class, 'approve']);
        Route::post('/guestbook/{id}/unapprove', [\App\Http\Controllers\Api\Admin\GuestBookController::class, 'unapprove']);
        Route::delete('/guestbook/{id}', [\App\Http\Controllers\Api\Admin\GuestBookController::class, 'destroy']);
    });
});
