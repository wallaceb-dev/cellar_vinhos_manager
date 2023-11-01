<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use App\Observers\PermissionObserver;
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
        Permission::observe(PermissionObserver::class);
        Gate::before(fn (User $user, $ability) => $user->hasPermissionTo($ability));
    }
}
