<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;

class DashboardController extends Controller
{
    public function index(Request $request): void
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
            "SELECT * FROM tasks WHERE user_id = ? AND status = 'pending' AND due_date IS NOT NULL AND due_date >= CURDATE() AND deleted_at IS NULL ORDER BY due_date ASC LIMIT 5",
            [$userId]
        );

        $recentActivities = $db->fetchAll(
            "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 10",
            [$userId]
        );

        $this->render('dashboard/index', [
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'overdueTasks' => $overdueTasks,
            'completionPercent' => $completionPercent,
            'upcomingTasks' => $upcomingTasks,
            'recentActivities' => $recentActivities,
        ]);
    }
}
