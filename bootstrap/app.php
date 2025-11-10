<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\SessionExpiry::class, // Auto-logout after 1 min (60s) idle
        ]);

        // CRITICAL: Add Sanctum's stateful middleware to API routes
        // This allows Sanctum to recognize session-based authentication for SPA
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Alias for middleware
        $middleware->alias([
            'session.expiry' => \App\Http\Middleware\SessionExpiry::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'vendor.approved' => \App\Http\Middleware\EnsureVendorApproved::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
