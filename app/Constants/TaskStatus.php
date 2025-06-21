<?php

namespace App\Constants;

/**
 * Defines constants for Task statuses.
 */
class TaskStatus
{
    public const PENDING = 'pending';
    public const COMPLETED = 'completed';

    /**
     * Get all possible task statuses.
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::COMPLETED,
        ];
    }
}
