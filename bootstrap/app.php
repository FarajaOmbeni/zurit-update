<?php

use App\Http\Middleware\EnsureUserIsSubscribed;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Register middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminRoleMiddleware::class,
            'coach' => \App\Http\Middleware\CoachRoleMiddleware::class,
            'subscribed' => EnsureUserIsSubscribed::class,
            'elearning.access' => \App\Http\Middleware\EnsureElearningAccess::class,
            'course.access' => \App\Http\Middleware\EnsureCourseAccess::class,
        ]);

        // Exempt UploadThing API from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'api/uploadthing',
            'api/uploadthing/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'The uploaded file is too large. Please reduce the file size or contact your administrator to increase upload limits.',
                    'max_size' => ini_get('upload_max_filesize'),
                    'current_limits' => [
                        'upload_max_filesize' => ini_get('upload_max_filesize'),
                        'post_max_size' => ini_get('post_max_size'),
                    ]
                ], 413);
            }
            
            return back()->withErrors([
                'error' => 'The uploaded file is too large. Please reduce the file size or contact your administrator to increase upload limits.'
            ])->withInput();
        });
    })->create();
