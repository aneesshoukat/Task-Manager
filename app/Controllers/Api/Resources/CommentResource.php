<?php

declare(strict_types=1);

namespace App\Controllers\Api;

class CommentResource
{
    public function format(?array $comment): ?array
    {
        if ($comment === null) {
            return null;
        }

        return [
            'id' => (int) $comment['id'],
            'task_id' => (int) $comment['task_id'],
            'user_id' => (int) $comment['user_id'],
            'user_name' => $comment['user_name'] ?? '',
            'user_avatar' => $comment['user_avatar'] ?? null,
            'comment' => $comment['comment'],
            'created_at' => $comment['created_at'],
            'updated_at' => $comment['updated_at'],
        ];
    }
}
