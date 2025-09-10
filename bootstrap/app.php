<?php

use App\Http\Middleware\EnsureUserIsSubscribed;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Register the admin and coach middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminRoleMiddleware::class,
            'coach' => \App\Http\Middleware\CoachRoleMiddleware::class,
            'subscribed' => EnsureUserIsSubscribed::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
