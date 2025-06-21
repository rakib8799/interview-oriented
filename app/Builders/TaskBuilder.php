<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Constants\TaskStatus; // Import the constants file

class TaskBuilder extends Builder
{
    /**
     * Apply the withTrashed condition to the query.
     *
     * @param bool $withTrashed
     * @return static
     */
    public function withTrashed(bool $withTrashed = true): static
    {
        if ($withTrashed) {
            return $this->withTrashed();
        }
        return $this;
    }

    /**
     * Apply the search filter to the query.
     *
     * @param string|null $search
     * @param array $columns The columns to search within. Defaults to ['title', 'description'].
     * @return static
     */
    public function search(?string $search, array $columns = ['title', 'description']): static
    {
        if ($search) {
            return $this->where(function ($query) use ($search, $columns) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }
        return $this;
    }

    /**
     * Apply the status filter to the query.
     *
     * @param string|null $statusFilter ('pending' or 'completed')
     * @param string $column The column to filter by status. Defaults to 'status'.
     * @return static
     */
    public function statusFilter(?string $statusFilter, string $column = 'status'): static
    {
        if ($statusFilter) {
            if ($statusFilter === TaskStatus::PENDING) {
                return $this->where($column, TaskStatus::PENDING);
            } elseif ($statusFilter === TaskStatus::COMPLETED) {
                return $this->where($column, TaskStatus::COMPLETED);
            }
        }
        return $this;
    }

    /**
     * Apply sorting to the query.
     *
     * @param string $sortBy
     * @param string $sortOrder ('asc' or 'desc')
     * @return static
     */
    public function sortBy(string $sortBy, string $sortOrder = 'desc'): static
    {
        return $this->orderBy($sortBy, $sortOrder);
    }

    /**
     * Apply pagination to the query.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function applyPagination(int $perPage = 10): LengthAwarePaginator
    {
        return $this->paginate($perPage);
    }
}
