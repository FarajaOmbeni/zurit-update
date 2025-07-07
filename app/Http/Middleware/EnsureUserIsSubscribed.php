<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow admin users to bypass subscription check
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // Check if user has active subscription
        if (
            !$user ||
            $user->subscription_status !== 'active' ||
            !$user->subscription_expires_at ||
            $user->subscription_expires_at < now()
        ) {

            // Redirect to subscription page with message
            return redirect()->route('subscription.plans')
                ->with('warning', 'Please subscribe to access the Prosperity Tools.');
        }

        return $next($request);
    }
}
