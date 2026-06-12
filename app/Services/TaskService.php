<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Logger;

class TaskService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getUserTasks(int $userId, array $filters = []): array
    {
        $where = 'user_id = ? AND deleted_at IS NULL';
        $params = [$userId];

        if (!empty($filters['status'])) {
            $where .= ' AND status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $where .= ' AND priority = ?';
            $params[] = $filters['priority'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (title LIKE ? OR description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['from'])) {
            $where .= ' AND due_date >= ?';
            $params[] = $filters['from'];
        }

        if (!empty($filters['to'])) {
            $where .= ' AND due_date <= ?';
            $params[] = $filters['to'];
        }

        $sortMap = [
            'latest' => 'created_at DESC',
            'oldest' => 'created_at ASC',
            'priority' => "FIELD(priority, 'high', 'medium', 'low')",
            'due_date' => 'due_date ASC',
        ];

        $orderBy = $sortMap[$filters['sort'] ?? 'latest'] ?? 'created_at DESC';
        $page = max(1, (int) ($filters['page'] ?? 1));
        $limit = min(50, max(1, (int) ($filters['limit'] ?? 20)));

        return $this->paginateTasks($where, $params, $orderBy, $page, $limit);
    }

    public function getTask(int $taskId, int $userId): ?array
    {
        $task = $this->db->fetch(
            "SELECT * FROM tasks WHERE id = ? AND user_id = ? AND deleted_at IS NULL",
            [$taskId, $userId]
        );

        return $task ?: null;
    }

    public function createTask(int $userId, array $data): int
    {
        $taskId = $this->db->insert('tasks', [
            'user_id' => $userId,
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'pending',
            'due_date' => $data['due_date'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => 'task_created',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Logger::info('Task created', ['task_id' => $taskId, 'user_id' => $userId]);
        return $taskId;
    }

    public function updateTask(int $taskId, int $userId, array $data): bool
    {
        $task = $this->getTask($taskId, $userId);
        if (!$task) {
            return false;
        }

        $updateData = [];
        foreach (['title', 'description', 'priority', 'status', 'due_date'] as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        $this->db->update('tasks', $updateData, 'id = ?', [$taskId]);

        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => 'task_updated',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Logger::info('Task updated', ['task_id' => $taskId, 'user_id' => $userId]);
        return true;
    }

    public function deleteTask(int $taskId, int $userId): bool
    {
        $task = $this->getTask($taskId, $userId);
        if (!$task) {
            return false;
        }

        $this->db->update('tasks', [
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$taskId]);

        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => 'task_deleted',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Logger::info('Task soft-deleted', ['task_id' => $taskId, 'user_id' => $userId]);
        return true;
    }

    public function restoreTask(int $taskId, int $userId): bool
    {
        $task = $this->db->fetch(
            "SELECT * FROM tasks WHERE id = ? AND user_id = ? AND deleted_at IS NOT NULL",
            [$taskId, $userId]
        );

        if (!$task) {
            return false;
        }

        $this->db->update('tasks', [
            'deleted_at' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$taskId]);

        Logger::info('Task restored', ['task_id' => $taskId, 'user_id' => $userId]);
        return true;
    }

    public function completeTask(int $taskId, int $userId): bool
    {
        return $this->updateTask($taskId, $userId, ['status' => 'completed']);
    }

    public function getTrashedTasks(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM tasks WHERE user_id = ? AND deleted_at IS NOT NULL ORDER BY deleted_at DESC",
            [$userId]
        );
    }

    public function exportToCsv(int $userId): string
    {
        $tasks = $this->db->fetchAll(
            "SELECT title, description, priority, status, due_date, created_at FROM tasks WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC",
            [$userId]
        );

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Title', 'Description', 'Priority', 'Status', 'Due Date', 'Created At']);

        foreach ($tasks as $task) {
            fputcsv($output, $task);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    public function importFromCsv(int $userId, string $filePath): array
    {
        $imported = 0;
        $errors = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) {
                    continue;
                }

                $data = [
                    'title' => $row[0] ?? '',
                    'description' => $row[1] ?? '',
                    'priority' => in_array($row[2] ?? '', ['low', 'medium', 'high']) ? $row[2] : 'medium',
                    'status' => in_array($row[3] ?? '', ['pending', 'completed']) ? $row[3] : 'pending',
                    'due_date' => $row[4] ?? null,
                ];

                if (empty($data['title'])) {
                    continue;
                }

                $this->createTask($userId, $data);
                $imported++;
            }

            fclose($handle);
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    private function paginateTasks(string $where, array $params, string $orderBy, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $total = (int) $this->db->fetch(
            "SELECT COUNT(*) as count FROM tasks WHERE {$where}",
            $params
        )['count'];

        $items = $this->db->fetchAll(
            "SELECT * FROM tasks WHERE {$where} ORDER BY {$orderBy} LIMIT ? OFFSET ?",
            [...$params, $limit, $offset]
        );

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => (int) ceil($total / $limit),
        ];
    }
}
