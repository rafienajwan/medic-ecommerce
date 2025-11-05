<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Handle activity ping from client
     * Simple ping to check authentication - middleware handles session update
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Don't update database here - let middleware handle it
        // This prevents race conditions and conflicts

        return response()->json([
            'message' => 'Activity recorded',
            'expires_in' => 1800 // 30 minutes in seconds
        ]);
    }

    /**
     * Check session status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false
            ]);
        }

        $sessionId = $request->session()->getId();
        $currentTime = time();

        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->first();

        if ($session) {
            $lastActivity = $session->last_activity;
            $timeRemaining = 300 - ($currentTime - $lastActivity); // 5 minutes

            return response()->json([
                'authenticated' => true,
                'time_remaining' => max(0, $timeRemaining),
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'authenticated' => false
        ]);
    }
}
