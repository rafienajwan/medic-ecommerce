<?php

use App\Models\Product;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home/Welcome Page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

// Public Products Page
Route::get('/products', function () {
    $products = Product::with(['category', 'vendor.user'])
        ->where('is_active', true)
        ->when(request('q'), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        })
        ->when(request('category'), function ($query, $category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        })
        ->paginate(12);

    // Debug: Log jumlah produk
    \Log::info('Products count: ' . $products->total());

    return Inertia::render('Products/Index', [
        'products' => $products,
    ]);
});

Route::get('/products/{id}', function ($id) {
    $product = Product::with(['category', 'vendor.user'])->findOrFail($id);

    return Inertia::render('Products/Show', [
        'product' => $product,
    ]);
});

// Guest Book Page
Route::get('/guestbook', function () {
    $guestbooks = \App\Models\GuestBook::with('user')
        ->approved()
        ->latest()
        ->paginate(10);

    return Inertia::render('GuestBook/Index', [
        'guestbooks' => $guestbooks,
    ]);
});

// Protected Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Cart
    Route::get('/cart', function () {
        return Inertia::render('Cart/Index');
    });

    // Checkout
    Route::get('/checkout', function () {
        return Inertia::render('Checkout/Index');
    });

    // Orders
    Route::get('/orders', function () {
        return Inertia::render('Orders/Index');
    });

    Route::get('/orders/{id}', function ($id) {
        return Inertia::render('Orders/Show', [
            'orderId' => $id,
        ]);
    });

    // Profile - Edit user profile
    Route::get('/profile', function () {
        return Inertia::render('Profile/Show');
    })->name('profile.show');

    // Vendor Application
    Route::get('/vendor/apply', function () {
        return Inertia::render('Vendor/Apply');
    });

    // Vendor Dashboard - Only for approved vendors
    Route::middleware(['vendor.approved'])->group(function () {
        Route::get('/vendor/dashboard', function () {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $vendor = $user->vendor;

            return Inertia::render('Vendor/Dashboard', [
                'vendor' => $vendor,
            ]);
        });

        // Vendor Profile
        Route::get('/vendor/profile', function () {
            return Inertia::render('Vendor/Profile');
        });

        // Vendor Products
        Route::get('/vendor/products', function () {
            return Inertia::render('Vendor/Products/Index');
        });

        Route::get('/vendor/products/create', function () {
            return Inertia::render('Vendor/Products/Create');
        });

        Route::get('/vendor/products/{id}/edit', function ($id) {
            return Inertia::render('Vendor/Products/Edit', [
                'productId' => $id,
            ]);
        });

        // Vendor Orders
        Route::get('/vendor/orders', function () {
            return Inertia::render('Vendor/Orders/Index');
        });
    });

    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Admin/Dashboard');
        });

        Route::get('/products', function () {
            return Inertia::render('Admin/Products/Index');
        });

        Route::get('/orders', function () {
            return Inertia::render('Admin/Orders/Index');
        });

        Route::get('/vendors', function () {
            return Inertia::render('Admin/Vendors/Index');
        });

        Route::get('/reports', function () {
            return Inertia::render('Admin/Reports/Index');
        });

        Route::get('/users', function () {
            return Inertia::render('Admin/Users/Index');
        });

        Route::get('/guestbook', function () {
            return Inertia::render('Admin/GuestBook/Index');
        });
    });
});
