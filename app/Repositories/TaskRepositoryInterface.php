<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User; // Import User model
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
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
    public function all(
        User $user, // Changed from ?int $userId to User $user
        ?string $search = null,
        ?string $statusFilter = null,
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 10,
        bool $withTrashed = false
    ): LengthAwarePaginator;

    /**
     * Find a task by its ID.
     *
     * @param int $id
     * @param bool $withTrashed Whether to include soft-deleted tasks when finding.
     * @return Task|null
     */
    public function find(int $id, bool $withTrashed = false): ?Task;

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task;

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return bool
     */
    public function update(Task $task, array $data): bool;

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function delete(Task $task): ?bool;

    /**
     * Restore a soft-deleted task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function restore(Task $task): ?bool;

    /**
     * Permanently delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function forceDelete(Task $task): ?bool;
}
