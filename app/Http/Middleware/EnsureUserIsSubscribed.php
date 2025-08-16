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

		// Allow access if the user has an unexpired subscription window,
		// regardless of status (e.g., cancelled but not yet expired)
		if ($user && $user->subscription_expires_at && $user->subscription_expires_at > now()) {
			return $next($request);
		}

		// Otherwise, block and redirect to subscription page
		return redirect()->route('subscription.plans')
			->with('warning', 'Please subscribe to access the Prosperity Tools.');
	}
}
