<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 10);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ]
        ]);
    }

    /**
     * Get user detail
     */
    public function show($id)
    {
        $user = User::with(['vendor', 'orders'])->findOrFail($id);

        return response()->json([
            'data' => $user
        ]);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['sometimes', 'in:customer,admin'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'email', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        AuditLog::log('user_updated', $user, 'User updated by admin', [
            'updated_fields' => array_keys($data)
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot delete your own account'
            ], 400);
        }

        // Prevent deleting admin accounts
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Cannot delete admin accounts'
            ], 400);
        }

        AuditLog::log('user_deleted', $user, 'User deleted by admin', [
            'deleted_user_name' => $user->name,
            'deleted_user_email' => $user->email
        ]);

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Get user statistics
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'vendors' => User::whereHas('vendor')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}
