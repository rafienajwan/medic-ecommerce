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
        // MIDDLEWARE DISABLED COMPLETELY - LET JETSTREAM HANDLE SESSION!
        // This middleware is causing conflicts with Jetstream's auth_session middleware
        // Jetstream already has built-in session management, we don't need custom logic

        return $next($request);
    }
}
