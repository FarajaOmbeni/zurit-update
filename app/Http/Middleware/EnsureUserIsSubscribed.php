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
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // If the user or the subscription test fails, boot them out
        if (! $user?->hasActiveSubscription()) {
            return redirect()
                ->route('subscription.plans')
                ->with('flash', [
                    'type' => 'warning',
                    'message' => 'You need an active subscription to continue.',
                ]);
        }

        return $next($request);
    }
}
