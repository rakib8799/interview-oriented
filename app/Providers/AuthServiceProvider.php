<?php

namespace App\Providers;

use App\Models\Task; // Import Task model
use App\Policies\TaskPolicy; // Import TaskPolicy
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // Import Gate facade

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Task::class => TaskPolicy::class, // Register the TaskPolicy
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Implicitly grants access for users with 'admin' role to all permissions via Gate::before.
        // This is a common pattern with Spatie for super-admins.
        Gate::before(function ($user, $ability) {
            // Check if the user has the 'admin' role
            // This is useful for super-admin access that bypasses granular permission checks
            if ($user->hasRole('admin')) {
                return true; // Grants access to all abilities
            }
            return null; // Let policies and other gates handle it
        });
    }
}
