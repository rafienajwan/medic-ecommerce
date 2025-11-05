<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SessionExpiry
{
    /**
     * Handle an incoming request.
     *
     * Session expiry: 60 seconds without activity
     * Implements sliding window - session extends on each request
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $sessionId = session()->getId();
        $idleTimeout = 60; // 60 seconds

        // Get session from database
        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->first();

        if ($session) {
            $lastActivity = $session->last_activity;
            $currentTime = time();
            $idleTime = $currentTime - $lastActivity;

            // If idle time exceeds 60 seconds, logout
            if ($idleTime > $idleTimeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Session expired due to inactivity',
                        'expired' => true
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Your session has expired due to inactivity.');
            }

            // Update last_activity for sliding window (auto-updated by Laravel)
            // Also update custom expires_at
            DB::table('sessions')
                ->where('id', $sessionId)
                ->update([
                    'expires_at' => now()->addSeconds($idleTimeout),
                ]);
        }

        return $next($request);
    }
}
