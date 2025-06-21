<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User; // Import User model
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskServiceInterface
{
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
    ): LengthAwarePaginator;

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task;

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return bool
     */
    public function updateTask(Task $task, array $data): bool;

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function deleteTask(Task $task): ?bool;

    /**
     * Restore a soft-deleted task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function restoreTask(Task $task): ?bool; // Added method

    /**
     * Permanently delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function forceDeleteTask(Task $task): ?bool; // Added method
}
