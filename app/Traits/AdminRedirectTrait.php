<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

trait AdminRedirectTrait
{
    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole(
        string $userRoute = 'budget.index',
        string $adminRoute = 'users.index',
        string $coachRoute = 'coach.dashboard',
        array $parameters = []
    ): RedirectResponse {
        $role = Auth::user()->role ?? null;

        // role 1 → admin
        if ($role === 1) {
            return redirect()->route($adminRoute, $parameters);
        }

        // role 2 → coach
        if ($role === 2) {
            return redirect()->route($coachRoute, $parameters);
        }

        // default (role 0 or null) → user budget
        return redirect()->route($userRoute, $parameters);
    }
}
