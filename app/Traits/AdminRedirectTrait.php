<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

trait AdminRedirectTrait
{
    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole(string $route = 'budget.index', string $adminRoute = 'users.index', array $parameters = []): RedirectResponse
    {
        // Check if user is admin (role = 1) and redirect accordingly
        if (Auth::user()->role == 1) {
            return redirect()->intended(route($adminRoute, $parameters, absolute: false));
        }

        return redirect()->intended(route($route, $parameters, absolute: false));
    }
}
