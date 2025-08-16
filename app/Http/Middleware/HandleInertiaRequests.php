<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'subscription' => $user ? [
                    'status' => $user->subscription_status ?? 'inactive',
                    'expires_at' => $user->subscription_expires_at,
                    'package' => $user->subscription_package,
                    // Consider access active as long as not expired, regardless of status
                    'is_active' => $user->subscription_expires_at && $user->subscription_expires_at > now(),
                    // Explicit flag to indicate cancelled but still within access window
                    'is_within_grace' => ($user->subscription_status === 'cancelled') && $user->subscription_expires_at && $user->subscription_expires_at > now(),
                ] : null,
            ],
        ];
    }
}
