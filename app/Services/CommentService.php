<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Logger;

class CommentService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getTaskComments(int $taskId, int $userId): array
    {
        $task = $this->db->fetch(
            "SELECT id FROM tasks WHERE id = ? AND user_id = ? AND deleted_at IS NULL",
            [$taskId, $userId]
        );

        if (!$task) {
            return [];
        }

        return $this->db->fetchAll(
            "SELECT c.*, u.name as user_name, u.avatar as user_avatar
             FROM task_comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.task_id = ?
             ORDER BY c.created_at ASC",
            [$taskId]
        );
    }

    public function createComment(int $taskId, int $userId, string $comment): ?int
    {
        $task = $this->db->fetch(
            "SELECT id FROM tasks WHERE id = ? AND user_id = ? AND deleted_at IS NULL",
            [$taskId, $userId]
        );

        if (!$task) {
            return null;
        }

        $commentId = $this->db->insert('task_comments', [
            'task_id' => $taskId,
            'user_id' => $userId,
            'comment' => $comment,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => 'comment_created',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Logger::info('Comment created', ['comment_id' => $commentId, 'task_id' => $taskId, 'user_id' => $userId]);
        return $commentId;
    }

    public function updateComment(int $commentId, int $userId, string $comment): bool
    {
        $existing = $this->db->fetch(
            "SELECT * FROM task_comments WHERE id = ? AND user_id = ?",
            [$commentId, $userId]
        );

        if (!$existing) {
            return false;
        }

        $this->db->update('task_comments', [
            'comment' => $comment,
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$commentId]);

        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => 'comment_updated',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Logger::info('Comment updated', ['comment_id' => $commentId, 'user_id' => $userId]);
        return true;
    }

    public function deleteComment(int $commentId, int $userId): bool
    {
        $existing = $this->db->fetch(
            "SELECT tc.* FROM task_comments tc
             JOIN tasks t ON tc.task_id = t.id
             WHERE tc.id = ? AND (tc.user_id = ? OR t.user_id = ?)",
            [$commentId, $userId, $userId]
        );

        if (!$existing) {
            return false;
        }

        $this->db->delete('task_comments', 'id = ?', [$commentId]);

        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => 'comment_deleted',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Logger::info('Comment deleted', ['comment_id' => $commentId, 'user_id' => $userId]);
        return true;
    }
}
