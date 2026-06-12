<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Services\CommentService;

class CommentApiController
{
    private CommentService $commentService;

    public function __construct()
    {
        $this->commentService = new CommentService();
    }

    public function index(Request $request, array $params): void
    {
        $taskId = (int) $params['id'];
        $comments = $this->commentService->getTaskComments($taskId, $request->userId());

        if (empty($comments) && !$this->taskBelongsToUser($taskId, $request->userId())) {
            Response::json(['success' => false, 'message' => 'Task not found.'], 404);
            return;
        }

        $items = array_map(fn($c) => (new CommentResource())->format($c), $comments);

        Response::json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request, array $params): void
    {
        $taskId = (int) $params['id'];
        $comment = trim($request->input('comment', ''));

        if (empty($comment)) {
            Response::json(['success' => false, 'message' => 'Comment is required.'], 422);
            return;
        }

        $commentId = $this->commentService->createComment($taskId, $request->userId(), $comment);

        if ($commentId === null) {
            Response::json(['success' => false, 'message' => 'Task not found.'], 404);
            return;
        }

        $db = Database::getInstance();
        $newComment = $db->fetch(
            "SELECT c.*, u.name as user_name, u.avatar as user_avatar
             FROM task_comments c JOIN users u ON c.user_id = u.id
             WHERE c.id = ?", [$commentId]
        );

        Response::json([
            'success' => true,
            'message' => 'Comment added.',
            'data' => (new CommentResource())->format($newComment),
        ], 201);
    }

    public function update(Request $request, array $params): void
    {
        $commentId = (int) $params['cid'];
        $comment = trim($request->input('comment', ''));

        if (empty($comment)) {
            Response::json(['success' => false, 'message' => 'Comment is required.'], 422);
            return;
        }

        $updated = $this->commentService->updateComment($commentId, $request->userId(), $comment);

        if (!$updated) {
            Response::json(['success' => false, 'message' => 'Comment not found.'], 404);
            return;
        }

        $db = Database::getInstance();
        $updatedComment = $db->fetch(
            "SELECT c.*, u.name as user_name, u.avatar as user_avatar
             FROM task_comments c JOIN users u ON c.user_id = u.id
             WHERE c.id = ?", [$commentId]
        );

        Response::json([
            'success' => true,
            'message' => 'Comment updated.',
            'data' => (new CommentResource())->format($updatedComment),
        ]);
    }

    public function destroy(Request $request, array $params): void
    {
        $commentId = (int) $params['cid'];
        $deleted = $this->commentService->deleteComment($commentId, $request->userId());

        if (!$deleted) {
            Response::json(['success' => false, 'message' => 'Comment not found.'], 404);
            return;
        }

        Response::json(['success' => true, 'message' => 'Comment deleted.']);
    }

    private function taskBelongsToUser(int $taskId, int $userId): bool
    {
        $db = Database::getInstance();
        $task = $db->fetch("SELECT id FROM tasks WHERE id = ? AND user_id = ?", [$taskId, $userId]);
        return $task !== null;
    }
}
