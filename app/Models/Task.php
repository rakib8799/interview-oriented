<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import for relationship
use Illuminate\Database\Eloquent\SoftDeletes; // Import SoftDeletes trait
use Carbon\Carbon; // Import Carbon for date formatting
use App\Constants\TaskStatus; // Import the constants file
use App\Builders\TaskBuilder; // Import our custom TaskBuilder

class Task extends Model
{
    use HasFactory, SoftDeletes; // Use SoftDeletes trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Added for authentication
        'title',
        'description',
        'due_date',
        'status',
        'file_path', // Added for file upload
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date', // Cast due_date to a Carbon instance
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['due_date_formatted', 'is_overdue', 'file_url'];

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \App\Builders\TaskBuilder
     */
    public function newEloquentBuilder($query): TaskBuilder
    {
        return new TaskBuilder($query);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get the task's due date formatted.
     *
     * @return string
     */
    public function getDueDateFormattedAttribute(): string
    {
        return $this->due_date ? Carbon::parse($this->due_date)->format('M d, Y') : 'N/A';
    }

    /**
     * Determine if the task is overdue.
     *
     * @return bool
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status === TaskStatus::PENDING;
    }

    /**
     * Get the URL for the uploaded file.
     *
     * @return string|null
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Set the task's title, ensuring it's capitalized.
     *
     * @param string $value
     * @return void
     */
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = ucfirst(strtolower($value));
    }

    /**
     * Set the task's status, ensuring it's always lowercase.
     *
     * @param string $value
     * @return void
     */
    public function setStatusAttribute(string $value): void
    {
        $this->attributes['status'] = strtolower($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    | These can now be moved to the TaskBuilder for more flexibility.
    */

    /**
     * Scope a query to only include completed tasks.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     * No longer needed if using TaskBuilder method.
     */
    // public function scopeCompleted($query)
    // {
    //     return $query->where('status', TaskStatus::COMPLETED);
    // }

    /**
     * Scope a query to only include pending tasks.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     * No longer needed if using TaskBuilder method.
     */
    // public function scopePending($query)
    // {
    //     return $query->where('status', TaskStatus::PENDING);
    // }
}
