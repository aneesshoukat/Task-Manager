<?php

declare(strict_types=1);

namespace App\Controllers\Api;

class DashboardResource
{
    public function format(array $stats): array
    {
        return [
            'total_tasks' => $stats['total_tasks'],
            'completed_tasks' => $stats['completed_tasks'],
            'pending_tasks' => $stats['pending_tasks'],
            'overdue_tasks' => $stats['overdue_tasks'],
            'completion_percent' => $stats['completion_percent'],
            'upcoming_tasks' => array_map(function ($task) {
                return [
                    'id' => (int) $task['id'],
                    'title' => $task['title'],
                    'priority' => $task['priority'],
                    'due_date' => $task['due_date'],
                ];
            }, $stats['upcoming_tasks']),
        ];
    }
}
