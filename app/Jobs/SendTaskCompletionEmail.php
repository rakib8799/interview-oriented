<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail; // For sending email (mocked)
use Illuminate\Support\Facades\Log; // For logging

class SendTaskCompletionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $task;

    /**
     * Create a new job instance.
     *
     * @param Task $task
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // In a real application, you would send an actual email here.
        // For demonstration, we'll just log it.
        Log::info("Sending task completion email for Task ID: {$this->task->id} to User ID: {$this->task->user_id}");
        // Example of how you might send an email using Laravel Mail:
        // Mail::to($this->task->user->email)->send(new TaskCompletedMail($this->task));

        // Simulate some work
        sleep(2); // Simulate a delay for email sending

        Log::info("Task completion email sent successfully for Task ID: {$this->task->id}.");
    }
}
