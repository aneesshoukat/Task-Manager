<?php

declare(strict_types=1);

namespace App\Controllers\Api;

class TaskResource
{
    public function format(?array $task): ?array
    {
        if ($task === null) {
            return null;
        }

        return [
            'id' => (int) $task['id'],
            'title' => $task['title'],
            'description' => $task['description'],
            'priority' => $task['priority'],
            'status' => $task['status'],
            'due_date' => $task['due_date'],
            'created_at' => $task['created_at'],
            'updated_at' => $task['updated_at'],
        ];
    }
}
