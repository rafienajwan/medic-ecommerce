<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user has vendor account
        if (!$user || !$user->hasVendor()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda belum mendaftar sebagai vendor'
                ], 403);
            }
            return redirect('/vendor/apply')->with('error', 'Anda belum mendaftar sebagai vendor');
        }

        // Check if vendor is approved
        if (!$user->vendor->isApproved()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Menunggu persetujuan admin'
                ], 403);
            }
            return redirect('/vendor/apply')->with('info', 'Menunggu persetujuan admin');
        }

        return $next($request);
    }
}
