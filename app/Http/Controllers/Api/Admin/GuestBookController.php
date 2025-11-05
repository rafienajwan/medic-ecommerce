<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\GuestBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestBookController extends Controller
{
    /**
     * Get all guest book entries (including pending)
     */
    public function index(Request $request)
    {
        $query = GuestBook::with(['user', 'approver']);

        // Filter by approval status
        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->boolean('is_approved'));
        }

        $entries = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($entries);
    }

    /**
     * Approve guest book entry
     */
    public function approve($id)
    {
        $entry = GuestBook::findOrFail($id);

        $entry->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        AuditLog::log('guestbook_approved', $entry, 'Guest book entry approved', [
            'entry_id' => $entry->id,
            'author' => $entry->name
        ]);

        return response()->json([
            'message' => 'Guest book entry approved successfully',
            'entry' => $entry->load(['user', 'approver'])
        ]);
    }

    /**
     * Unapprove guest book entry
     */
    public function unapprove($id)
    {
        $entry = GuestBook::findOrFail($id);

        $entry->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        AuditLog::log('guestbook_unapproved', $entry, 'Guest book entry unapproved', [
            'entry_id' => $entry->id,
            'author' => $entry->name
        ]);

        return response()->json([
            'message' => 'Guest book entry unapproved successfully',
            'entry' => $entry->load(['user', 'approver'])
        ]);
    }

    /**
     * Delete guest book entry
     */
    public function destroy($id)
    {
        $entry = GuestBook::findOrFail($id);

        AuditLog::log('guestbook_deleted_by_admin', $entry, 'Guest book entry deleted by admin', [
            'entry_id' => $entry->id,
            'author' => $entry->name,
            'message_preview' => substr($entry->message, 0, 100)
        ]);

        $entry->delete();

        return response()->json(['message' => 'Guest book entry deleted successfully']);
    }
}
