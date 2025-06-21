<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TaskRepositoryInterface;
use App\Repositories\EloquentTaskRepository;
use App\Services\TaskServiceInterface;
use App\Services\TaskService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the TaskRepositoryInterface to its Eloquent implementation
        $this->app->bind(
            TaskRepositoryInterface::class,
            EloquentTaskRepository::class
        );

        // Bind the TaskServiceInterface to its concrete TaskService implementation
        $this->app->bind(
            TaskServiceInterface::class,
            TaskService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
