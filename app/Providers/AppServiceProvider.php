<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        // Checks using can(), @can, or Gate::allows will return true for developer/superadmin
        Gate::before(function ($user, $ability) {
            if ($user && ($user->hasRole('Super Admin') || $user->role === 'Super Admin' || $user->email === 'admin@gudichemicals.com')) {
                return true;
            }
            return null;
        });
    }
}
