<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\Response; // Import Response for policy methods

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Spatie: Users with 'view all tasks' permission can view any task.
        // Otherwise, any authenticated user can view their *own* tasks (filtered in repository).
        return $user->can('view all tasks'); // Always true if authenticated
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): Response
    {
        // Spatie: If the user has 'view all tasks' permission, they can view any task.
        if ($user->can('view all tasks')) {
            return Response::allow();
        }

        // Otherwise, a user can view a task only if they own it.
        // If soft-deleted, it must be owned by the user to view.
        if ($task->trashed()) {
            return $user->id === $task->user_id
                ? Response::allow()
                : Response::deny('You do not own this trashed task.');
        }

        return $user->id === $task->user_id
            ? Response::allow()
            : Response::deny('You do not own this task.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create tasks.
        return $user->can('manage tasks');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): Response
    {
        // Spatie: If the user has 'manage tasks' permission, they can update any task.
        if ($user->can('manage tasks')) {
            return Response::allow();
        }

        // Otherwise, a user can update a task if they own it and it's not soft-deleted.
        return ($user->id === $task->user_id && !$task->trashed())
            ? Response::allow()
            : Response::deny('You do not own this task or it has been deleted.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): Response
    {
        // Spatie: If the user has 'manage tasks' permission, they can delete any task.
        if ($user->can('manage tasks')) {
            return Response::allow();
        }

        // Otherwise, a user can delete (soft delete) a task if they own it and it's not already soft-deleted.
        return ($user->id === $task->user_id && !$task->trashed())
            ? Response::allow()
            : Response::deny('You do not own this task or it has already been deleted.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): Response
    {
        // Spatie: If the user has 'manage tasks' permission, they can restore any task.
        if ($user->can('manage tasks')) {
            return Response::allow();
        }

        // Otherwise, a user can restore a task if they own it and it is soft-deleted.
        return ($user->id === $task->user_id && $task->trashed())
            ? Response::allow()
            : Response::deny('You cannot restore this task.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): Response
    {
        // Spatie: If the user has 'manage tasks' permission, they can force delete any task.
        if ($user->can('manage tasks')) {
            return Response::allow();
        }

        // Otherwise, a user can permanently delete a task if they own it and it is soft-deleted.
        return ($user->id === $task->user_id && $task->trashed())
            ? Response::allow()
            : Response::deny('You cannot permanently delete this task.');
    }
}
