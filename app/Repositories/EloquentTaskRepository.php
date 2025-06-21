<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User; // Import User model
use Illuminate\Database\Eloquent\Builder; // Import Builder for type hinting
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache; // Added for caching
use App\Constants\TaskStatus; // Import the constants file
use App\Builders\TaskBuilder; // Import TaskBuilder for type hinting

class EloquentTaskRepository implements TaskRepositoryInterface
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
        User $user,
        ?string $search = null,
        ?string $statusFilter = null,
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 10,
        bool $withTrashed = false
    ): LengthAwarePaginator {
        // Define common tags for tasks lists
        $tags = ['tasks'];
        if ($user->can('view all tasks')) {
            $tags[] = 'global_tasks'; // More specific tag for global view
        } else {
            $tags[] = 'user_tasks:' . ($user->id ?? 'guest'); // More specific tag for user tasks
        }

        // Build a unique key for this specific query within the tags
        $cacheKey = implode('_', [
            md5($search ?? ''),
            $statusFilter ?? 'null',
            $sortBy,
            $sortOrder,
            request()->get('page', 1),
            $withTrashed ? 'true' : 'false'
        ]);

        // Cache the results using tags
        return Cache::tags($tags)->remember($cacheKey, 60, function () use ($user, $search, $statusFilter, $sortBy, $sortOrder, $perPage, $withTrashed) {
            /** @var TaskBuilder $query */
            $query = Task::query(); // This will return an instance of TaskBuilder due to newEloquentBuilder on Task model

            $query->withTrashed($withTrashed);

            // Spatie: If the user does NOT have the 'view all tasks' permission,
            // scope the query to only their tasks.
            if (!$user->can('view all tasks')) {
                $query->where('user_id', $user->id);
            }

            // Use the dynamic search and filter methods from TaskBuilder
            $query->search($search, ['title', 'description'])
                  ->statusFilter($statusFilter, 'status')
                  ->sortBy($sortBy, $sortOrder);

            return $query->applyPagination($perPage);
        });
    }

    /**
     * Find a task by its ID.
     *
     * @param int $id
     * @param bool $withTrashed Whether to include soft-deleted tasks when finding.
     * @return Task|null
     */
    public function find(int $id, bool $withTrashed = false): ?Task
    {
        // For single item lookup, we can still use simple cache keys.
        // These will be invalidated explicitly by invalidateTaskCaches.
        $cacheKey = "task:{$id}_with_trashed:{$withTrashed}";
        return Cache::remember($cacheKey, 60, function () use ($id, $withTrashed) {
            if ($withTrashed) {
                return Task::withTrashed()->find($id);
            }
            return Task::find($id);
        });
    }

    /**
     * Helper to invalidate relevant cache keys after CUD operations.
     * @param Task $task
     * @return void
     */
    protected function invalidateTaskCaches(Task $task): void
    {
        $userId = $task->user_id ?? 'guest';

        // Invalidate specific task find() caches
        Cache::forget("task:{$task->id}_with_trashed:false");
        Cache::forget("task:{$task->id}_with_trashed:true");

        // Flush all caches associated with the 'tasks' tag (broad invalidate for global lists)
        // This implicitly covers 'global_tasks' as well.
        Cache::tags(['tasks'])->flush();

        // Flush all caches associated with this specific user's tasks
        Cache::tags(['user_tasks:' . $userId])->flush();
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task
    {
        $task = Task::create($data);
        $this->invalidateTaskCaches($task); // Invalidate caches after creation
        return $task;
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return bool
     */
    public function update(Task $task, array $data): bool
    {
        $result = $task->update($data);
        if ($result) {
            $this->invalidateTaskCaches($task); // Invalidate caches after update
        }
        return $result;
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function delete(Task $task): ?bool
    {
        $result = $task->delete(); // This performs a soft delete
        if ($result) {
            $this->invalidateTaskCaches($task); // Invalidate caches after soft deletion
        }
        return $result;
    }

    /**
     * Restore a soft-deleted task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function restore(Task $task): ?bool
    {
        $result = $task->restore();
        if ($result) {
            $this->invalidateTaskCaches($task); // Invalidate caches after restoration
        }
        return $result;
    }

    /**
     * Permanently delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function forceDelete(Task $task): ?bool
    {
        $result = $task->forceDelete();
        if ($result) {
            $this->invalidateTaskCaches($task); // Invalidate caches after permanent deletion
        }
        return $result;
    }
}
