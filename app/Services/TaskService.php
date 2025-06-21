<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User; // Import User model
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Jobs\SendTaskCompletionEmail; // Import the Job
use App\Events\TaskCompleted; // Import the Event
use Illuminate\Support\Facades\Log; // For logging

class TaskService implements TaskServiceInterface
{
    protected $taskRepository;

    /**
     * TaskService constructor.
     * @param TaskRepositoryInterface $taskRepository
     */
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Get tasks based on user permissions with optional filters, sorting, and pagination.
     *
     * @param User $user The authenticated user.
     * @param string|null $search Search term for title or description.
     * @param string|null $statusFilter Filter by task status ('pending' or 'completed').
     * @param string $sortBy Column to sort by (e.g., 'created_at', 'title', 'due_date', 'status').
     * @param string $sortOrder Sort order ('asc' or 'desc').
     * @param int $perPage Number of tasks per page for pagination.
     * @param bool $withTrashed Whether to include soft-deleted tasks.
     * @return LengthAwarePaginator
     */
    public function getAllTasks(
        User $user, // Changed from ?int $userId to User $user
        ?string $search = null,
        ?string $statusFilter = null,
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 10,
        bool $withTrashed = false
    ): LengthAwarePaginator {
        // Business logic could be applied here before calling the repository.
        // For instance, if an admin wants to see trashed tasks by default.
        return $this->taskRepository->all($user, $search, $statusFilter, $sortBy, $sortOrder, $perPage, $withTrashed);
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        $task = $this->taskRepository->create($data);
        Log::info('Task created successfully via service.', ['task_id' => $task->id, 'user_id' => $task->user_id]);
        return $task;
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return bool
     */
    public function updateTask(Task $task, array $data): bool
    {
        $oldStatus = $task->status;
        $result = $this->taskRepository->update($task, $data);

        // Dispatch job and event if status changes to 'completed'
        if ($result && $oldStatus === 'pending' && $task->status === 'completed') {
            // Dispatch a job to the queue
            // SendTaskCompletionEmail::dispatch($task);
            // Dispatch an event
            TaskCompleted::dispatch($task);
            Log::info('Task status updated to completed, job and event dispatched.', ['task_id' => $task->id]);
        }
        return $result;
    }

    /**
     * Delete a task (soft delete).
     *
     * @param Task $task
     * @return bool|null
     */
    public function deleteTask(Task $task): ?bool
    {
        $result = $this->taskRepository->delete($task);
        if ($result) {
            Log::info('Task soft-deleted successfully via service.', ['task_id' => $task->id]);
        }
        return $result;
    }

    /**
     * Restore a soft-deleted task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function restoreTask(Task $task): ?bool
    {
        $result = $this->taskRepository->restore($task);
        if ($result) {
            Log::info('Task restored successfully via service.', ['task_id' => $task->id]);
        }
        return $result;
    }

    /**
     * Permanently delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function forceDeleteTask(Task $task): ?bool
    {
        $result = $this->taskRepository->forceDelete($task);
        if ($result) {
            Log::info('Task permanently deleted successfully via service.', ['task_id' => $task->id]);
        }
        return $result;
    }
}
