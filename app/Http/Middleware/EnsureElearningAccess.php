<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureElearningAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!($user->elearning_access ?? false)) {
            // Store intended URL to redirect after payment success
            session(['elearning.intended_url' => $request->fullUrl()]);
            return redirect()->route('elearning.paywall');
        }

        return $next($request);
    }
}

