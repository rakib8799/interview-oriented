<?php

namespace App\Providers;

use App\Models\Task; // Import Task Model
use App\Observers\TaskObserver; // Import Task Observer
use App\Events\TaskCompleted; // Import TaskCompleted Event
use App\Listeners\SendCompletionNotification; // Import SendCompletionNotification Listener
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TaskCompleted::class => [ // Register TaskCompleted event with its listener
            SendCompletionNotification::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        Task::class => [TaskObserver::class], // Register TaskObserver for the Task model
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
