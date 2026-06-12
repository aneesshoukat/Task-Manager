<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class DashboardApiController
{
    public function stats(Request $request): void
    {
        $userId = $request->userId();
        $db = Database::getInstance();

        $totalTasks = (int) $db->fetch(
            "SELECT COUNT(*) as count FROM tasks WHERE user_id = ? AND deleted_at IS NULL",
            [$userId]
        )['count'];

        $completedTasks = (int) $db->fetch(
            "SELECT COUNT(*) as count FROM tasks WHERE user_id = ? AND status = 'completed' AND deleted_at IS NULL",
            [$userId]
        )['count'];

        $pendingTasks = $totalTasks - $completedTasks;

        $overdueTasks = (int) $db->fetch(
            "SELECT COUNT(*) as count FROM tasks WHERE user_id = ? AND status = 'pending' AND due_date IS NOT NULL AND due_date < CURDATE() AND deleted_at IS NULL",
            [$userId]
        )['count'];

        $completionPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $upcomingTasks = $db->fetchAll(
            "SELECT id, title, priority, due_date FROM tasks WHERE user_id = ? AND status = 'pending' AND due_date IS NOT NULL AND due_date >= CURDATE() AND deleted_at IS NULL ORDER BY due_date ASC LIMIT 5",
            [$userId]
        );

        Response::json([
            'success' => true,
            'data' => (new DashboardResource())->format([
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'overdue_tasks' => $overdueTasks,
                'completion_percent' => $completionPercent,
                'upcoming_tasks' => $upcomingTasks,
            ]),
        ]);
    }
}
