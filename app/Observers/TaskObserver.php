<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Log; // For logging

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        Log::info("TaskObserver: Task '{$task->title}' (ID: {$task->id}) created.");
        // Example: You could send an internal notification, update a dashboard count, etc.
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        Log::info("TaskObserver: Task '{$task->title}' (ID: {$task->id}) updated.");
        // Note: Specific actions for status change are in TaskService for business logic.
        // This observer handles all updates.
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        // This event fires AFTER the soft delete has occurred, but before force deletion.
        Log::info("TaskObserver: Task '{$task->title}' (ID: {$task->id}) soft-deleted.");
        // Example: You might trigger a job to move related files to an archive,
        // or decrement a user's task count.
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        Log::info("TaskObserver: Task '{$task->title}' (ID: {$task->id}) restored.");
    }

    /**
     * Handle the Task "forceDeleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        Log::info("TaskObserver: Task '{$task->title}' (ID: {$task->id}) permanently deleted.");
        // Example: This is where you'd clean up truly associated records or files
        // that should not be kept after a hard delete.
    }
}
