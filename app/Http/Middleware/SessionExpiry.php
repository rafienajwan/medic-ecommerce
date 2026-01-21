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
     * Session expiry: configurable via SESSION_IDLE_TIMEOUT env variable (default: 60 seconds)
     * Implements sliding window - session extends on each request
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip session expiry check for logout and login routes
        if ($request->is('logout') || $request->routeIs('logout') ||
            $request->is('login') || $request->routeIs('login') ||
            $request->is('register') || $request->routeIs('register')) {
            return $next($request);
        }

        if (!Auth::check()) {
            return $next($request);
        }

        $sessionId = session()->getId();
        $idleTimeout = (int) config('session.idle_timeout', 60); // Configurable timeout in seconds

        // Get session from database
        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->first();

        if ($session) {
            $lastActivity = $session->last_activity;
            $currentTime = time();
            $idleTime = $currentTime - $lastActivity;

            // If idle time exceeds timeout, logout
            if ($idleTime > $idleTimeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $timeoutMessage = $idleTimeout >= 60
                    ? round($idleTimeout / 60) . ' minute(s)'
                    : $idleTimeout . ' seconds';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "Session expired due to inactivity ({$timeoutMessage})",
                        'expired' => true
                    ], 401);
                }

                return redirect()->route('login')->with('error', "Your session has expired due to inactivity ({$timeoutMessage}).");
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
