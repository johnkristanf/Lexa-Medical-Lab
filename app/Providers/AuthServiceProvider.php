<?php

namespace App\Providers;

use App\Models\User;
use App\Role;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('admin', function (User $user) {
            return $user->role_id === Role::ADMIN->value
                ? Response::allow()
                : Response::deny('You must be an administrator.');
        });

        Gate::define('manage-medical', function (User $user) {
            return in_array($user->role_id, [
                Role::ADMIN->value,
                Role::MEDICAL_STAFF->value,
            ])
                ? Response::allow()
                : Response::deny('You are not authorized to manage medical.');
        });


        Gate::define('manage-inventory-supplies', function (User $user) {
            return in_array($user->role_id, [
                Role::ADMIN->value,
                Role::INVENTORY_OFFICER->value,
            ])
                ? Response::allow()
                : Response::deny('You are not authorized to manage inventory supplies.');
        });
        
    }
}
