<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    /**
     * Get all vendors with filters
     */
    public function index(Request $request)
    {
        $query = Vendor::with(['user', 'approver']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $vendors = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($vendors);
    }

    /**
     * Get pending vendor applications
     */
    public function pending()
    {
        $vendors = Vendor::with(['user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'count' => $vendors->count(),
            'vendors' => $vendors,
        ]);
    }

    /**
     * Approve vendor application
     */
    public function approve(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        if ($vendor->isApproved()) {
            return response()->json([
                'message' => 'Vendor sudah disetujui sebelumnya'
            ], 400);
        }

        $vendor->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        // Log activity
        AuditLog::log(
            'vendor_approved',
            $vendor,
            "Admin approved vendor: {$vendor->business_name} for user {$vendor->user->name}"
        );

        return response()->json([
            'message' => 'Vendor berhasil disetujui',
            'vendor' => $vendor->load(['user', 'approver'])
        ]);
    }

    /**
     * Reject vendor application with reason
     */
    public function reject(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        if ($vendor->isApproved()) {
            return response()->json([
                'message' => 'Tidak dapat menolak vendor yang sudah disetujui'
            ], 400);
        }

        // Validate rejection reason
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:10|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Alasan penolakan harus diisi (minimal 10 karakter)',
                'errors' => $validator->errors(),
            ], 422);
        }

        $vendor->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        // Log activity
        AuditLog::log(
            'vendor_rejected',
            $vendor,
            "Admin rejected vendor: {$vendor->business_name}. Reason: {$request->reason}"
        );

        return response()->json([
            'message' => 'Permohonan vendor ditolak',
            'vendor' => $vendor->load(['user'])
        ]);
    }

    /**
     * Get vendor details
     */
    public function show($id)
    {
        $vendor = Vendor::with(['user', 'approver', 'products'])->findOrFail($id);

        return response()->json([
            'vendor' => $vendor,
        ]);
    }
}
