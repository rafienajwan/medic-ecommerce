<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * VendorController - Manage vendor registration for customers
 */
class VendorController extends Controller
{
    /**
     * Get current user's vendor status
     */
    public function status(Request $request)
    {
        $user = $request->user();

        if (!$user->hasVendor()) {
            return response()->json([
                'has_vendor' => false,
                'status' => null,
                'vendor' => null,
            ]);
        }

        return response()->json([
            'has_vendor' => true,
            'status' => $user->vendor->status,
            'vendor' => $user->vendor,
        ]);
    }

    /**
     * Apply to become a vendor
     */
    public function apply(Request $request)
    {
        $user = $request->user();

        // Check if already has vendor account
        if ($user->hasVendor()) {
            return response()->json([
                'message' => 'Anda sudah memiliki akun vendor',
                'vendor' => $user->vendor,
            ], 400);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'business_type' => 'required|string|max:100',
            'business_address' => 'required|string',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'required|email|max:100',
            'tax_id' => 'nullable|string|max:50',
            'business_license' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create vendor application
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'business_name' => $request->business_name,
            'description' => $request->description,
            'business_type' => $request->business_type,
            'business_address' => $request->business_address,
            'business_phone' => $request->business_phone,
            'business_email' => $request->business_email,
            'tax_id' => $request->tax_id,
            'business_license' => $request->business_license,
            'status' => 'pending',
        ]);

        // Log activity
        AuditLog::log(
            'vendor_application',
            $vendor,
            "User {$user->name} applied to become vendor: {$vendor->business_name}"
        );

        return response()->json([
            'message' => 'Permohonan vendor berhasil diajukan. Menunggu persetujuan admin.',
            'vendor' => $vendor,
        ], 201);
    }

    /**
     * Update vendor application (only if pending or rejected)
     */
    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user->hasVendor()) {
            return response()->json([
                'message' => 'Anda belum memiliki akun vendor',
            ], 404);
        }

        $vendor = $user->vendor;

        // Only allow update if pending or rejected
        if ($vendor->isApproved()) {
            return response()->json([
                'message' => 'Vendor yang sudah disetujui tidak dapat diubah',
            ], 400);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'business_name' => 'sometimes|required|string|max:150',
            'description' => 'nullable|string',
            'business_type' => 'sometimes|required|string|max:100',
            'business_address' => 'sometimes|required|string',
            'business_phone' => 'sometimes|required|string|max:20',
            'business_email' => 'sometimes|required|email|max:100',
            'tax_id' => 'nullable|string|max:50',
            'business_license' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update vendor
        $vendor->update($request->only([
            'business_name',
            'description',
            'business_type',
            'business_address',
            'business_phone',
            'business_email',
            'tax_id',
            'business_license',
        ]));

        // If was rejected, reset to pending
        if ($vendor->isRejected()) {
            $vendor->update([
                'status' => 'pending',
                'rejection_reason' => null,
            ]);
        }

        // Log activity
        AuditLog::log(
            'vendor_update',
            $vendor,
            "Vendor application updated: {$vendor->business_name}"
        );

        return response()->json([
            'message' => 'Data vendor berhasil diperbarui',
            'vendor' => $vendor->fresh(),
        ]);
    }

    /**
     * Update vendor profile (for approved vendors)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user->hasVendor()) {
            return response()->json([
                'message' => 'Anda belum memiliki akun vendor',
            ], 404);
        }

        $vendor = $user->vendor;

        // Validate request
        $validator = Validator::make($request->all(), [
            'business_name' => 'sometimes|required|string|max:150',
            'description' => 'nullable|string',
            'business_type' => 'sometimes|required|string|max:100',
            'business_address' => 'sometimes|required|string',
            'business_phone' => 'sometimes|required|string|max:20',
            'business_email' => 'sometimes|required|email|max:100',
            'tax_id' => 'nullable|string|max:50',
            'business_license' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update vendor profile
        $vendor->update($request->only([
            'business_name',
            'description',
            'business_type',
            'business_address',
            'business_phone',
            'business_email',
            'tax_id',
            'business_license',
        ]));

        // Log activity
        AuditLog::log(
            'vendor_profile_update',
            $vendor,
            "Vendor profile updated: {$vendor->business_name}"
        );

        return response()->json([
            'message' => 'Vendor profile berhasil diperbarui',
            'vendor' => $vendor->fresh(),
        ]);
    }

    /**
     * Get vendor details
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if (!$user->hasVendor()) {
            return response()->json([
                'message' => 'Anda belum memiliki akun vendor',
            ], 404);
        }

        return response()->json([
            'vendor' => $user->vendor,
        ]);
    }
}
