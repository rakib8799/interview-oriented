<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log; // For logging

class SendCompletionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3; // Retry the listener up to 3 times on failure

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TaskCompleted $event
     * @return void
     */
    public function handle(TaskCompleted $event): void
    {
        Log::info("SendCompletionNotification: Received TaskCompleted event for Task ID: {$event->task->id}.");
        // In a real application, this listener might send a real-time notification
        // (e.g., via WebSocket, push notification, or another internal system).
        // For demonstration, we'll just log.

        // Simulate some work
        // throw new \Exception("Simulating a listener failure for retry test."); // Uncomment to test retries
        sleep(1);

        Log::info("SendCompletionNotification: Notification sent for Task ID: {$event->task->id}.");
    }

    /**
     * Handle a job that is failing.
     *
     * @param TaskCompleted $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(TaskCompleted $event, \Throwable $exception): void
    {
        Log::error("SendCompletionNotification: Failed for Task ID: {$event->task->id}. Reason: {$exception->getMessage()}");
    }
}
