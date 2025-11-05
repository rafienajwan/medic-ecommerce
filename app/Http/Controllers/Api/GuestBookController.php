<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\GuestBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GuestBookController extends Controller
{
    /**
     * Get all approved guest book entries (public)
     */
    public function index()
    {
        $entries = GuestBook::with(['user', 'approver'])
            ->approved()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($entries);
    }

    /**
     * Store new guest book entry
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required_without:user_id', 'nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'message' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'is_approved' => false, // Require admin approval
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
            $data['name'] = Auth::user()->name;
            $data['email'] = Auth::user()->email;
        } else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
        }

        $entry = GuestBook::create($data);

        AuditLog::log('guestbook_created', $entry, 'New guest book entry created');

        return response()->json([
            'message' => 'Thank you for your message! It will be published after admin approval.',
            'entry' => $entry
        ], 201);
    }

    /**
     * Delete own entry (for logged-in users)
     */
    public function destroy($id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $entry = GuestBook::where('user_id', Auth::id())->findOrFail($id);

        AuditLog::log('guestbook_deleted', $entry, 'Guest book entry deleted by user');

        $entry->delete();

        return response()->json(['message' => 'Entry deleted successfully']);
    }
}
