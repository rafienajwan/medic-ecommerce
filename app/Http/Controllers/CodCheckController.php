<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Vendor;
use Illuminate\Http\Request;

class CodCheckController extends Controller
{
    /**
     * Check if COD is available based on user city and vendor cities in cart
     */
    public function checkCodAvailability(Request $request)
    {
        $validated = $request->validate([
            'shipping_city' => 'required|string',
        ]);

        $userCity = strtolower(trim($validated['shipping_city']));

        // Get user's cart
        $cart = Cart::with(['items.product.vendor.user'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Group items by vendor
        $vendorCities = [];
        $incompatibleVendors = [];
        $compatibleVendors = [];

        foreach ($cart->items as $item) {
            $vendor = $item->product->vendor;
            if ($vendor && $vendor->user) {
                $vendorCity = strtolower(trim($vendor->user->city));
                $vendorId = $vendor->id;

                if (!isset($vendorCities[$vendorId])) {
                    $vendorInfo = [
                        'vendor_id' => $vendorId,
                        'vendor_name' => $vendor->business_name,
                        'vendor_city' => $vendor->user->city,
                        'products' => [],
                    ];

                    if ($vendorCity === $userCity) {
                        $vendorInfo['cod_available'] = true;
                        $compatibleVendors[] = $vendorInfo;
                    } else {
                        $vendorInfo['cod_available'] = false;
                        $incompatibleVendors[] = $vendorInfo;
                    }

                    $vendorCities[$vendorId] = $vendorInfo;
                }

                // Add product to vendor's product list
                $vendorCities[$vendorId]['products'][] = [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                ];
            }
        }

        $codAvailable = empty($incompatibleVendors);

        return response()->json([
            'success' => true,
            'cod_available' => $codAvailable,
            'user_city' => $validated['shipping_city'],
            'compatible_vendors' => array_values($compatibleVendors),
            'incompatible_vendors' => array_values($incompatibleVendors),
            'message' => $codAvailable
                ? 'COD tersedia untuk semua produk di keranjang Anda'
                : 'COD tidak tersedia. Beberapa produk dari vendor di kota berbeda.',
            'details' => $codAvailable
                ? 'Semua vendor berada di kota yang sama dengan Anda'
                : 'Vendor yang tidak sama kota: ' . implode(', ', array_map(function($v) {
                    return $v['vendor_name'] . ' (' . $v['vendor_city'] . ')';
                }, $incompatibleVendors))
        ]);
    }
}
