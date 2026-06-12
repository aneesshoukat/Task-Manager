<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\CommentService;
use App\Services\CsrfService;

class CommentController extends Controller
{
    private CommentService $commentService;
    private CsrfService $csrfService;

    public function __construct()
    {
        $this->commentService = new CommentService();
        $this->csrfService = new CsrfService();
    }

    public function store(Request $request, array $params): void
    {
        $taskId = (int) $params['id'];
        $comment = trim($request->post('comment', ''));

        if (empty($comment)) {
            Session::flash('error', 'Comment cannot be empty.');
            Response::redirect('/tasks/' . $taskId);
            return;
        }

        $this->commentService->createComment($taskId, $request->userId(), $comment);
        Session::flash('success', 'Comment added.');
        Response::redirect('/tasks/' . $taskId);
    }

    public function edit(Request $request, array $params): void
    {
        $taskId = (int) $params['id'];
        $commentId = (int) $params['cid'];

        $db = \App\Core\Database::getInstance();
        $comment = $db->fetch(
            "SELECT c.*, u.name as user_name FROM task_comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.id = ? AND c.user_id = ?",
            [$commentId, $request->userId()]
        );

        if (!$comment) {
            Session::flash('error', 'Comment not found.');
            Response::redirect('/tasks/' . $taskId);
            return;
        }

        $this->csrfService->generate();
        $this->render('tasks/edit_comment', ['comment' => $comment, 'taskId' => $taskId]);
    }

    public function update(Request $request, array $params): void
    {
        $taskId = (int) $params['id'];
        $commentId = (int) $params['cid'];
        $comment = trim($request->post('comment', ''));

        if (empty($comment)) {
            Session::flash('error', 'Comment cannot be empty.');
            Response::redirect('/tasks/' . $taskId . '/comments/' . $commentId . '/edit');
            return;
        }

        $updated = $this->commentService->updateComment($commentId, $request->userId(), $comment);

        if (!$updated) {
            Session::flash('error', 'Comment not found.');
        } else {
            Session::flash('success', 'Comment updated.');
        }

        Response::redirect('/tasks/' . $taskId);
    }

    public function destroy(Request $request, array $params): void
    {
        $taskId = (int) $params['id'];
        $commentId = (int) $params['cid'];

        $this->commentService->deleteComment($commentId, $request->userId());
        Session::flash('success', 'Comment deleted.');
        Response::redirect('/tasks/' . $taskId);
    }
}
