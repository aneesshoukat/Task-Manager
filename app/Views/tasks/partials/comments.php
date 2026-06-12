<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-chat-dots"></i> Comments (<?= count($comments ?? []) ?>)</span>
    </div>
    <div class="card-body">
        <?php if (empty($comments ?? [])): ?>
            <p class="text-muted mb-3">No comments yet.</p>
        <?php else: ?>
            <div class="mb-3">
                <?php foreach ($comments as $comment): ?>
                    <div class="d-flex mb-3 border-bottom pb-3">
                        <div class="me-3">
                            <?php if (!empty($comment['user_avatar'])): ?>
                                <img src="/uploads/<?= escape($comment['user_avatar']) ?>" class="rounded-circle" width="40" height="40" alt="">
                            <?php else: ?>
                                <i class="bi bi-person-circle fs-3 text-muted"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong><?= escape($comment['user_name'] ?? 'Unknown') ?></strong>
                                <small class="text-muted"><?= escape($comment['created_at']) ?></small>
                            </div>
                            <p class="mb-1 mt-1"><?= nl2br(escape($comment['comment'])) ?></p>
                            <?php if ((int) $comment['user_id'] === $userId): ?>
                                <div class="d-flex gap-2">
                                    <a href="/tasks/<?= $taskId ?>/comments/<?= $comment['id'] ?>/edit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    <a href="/tasks/<?= $taskId ?>/comments/<?= $comment['id'] ?>/delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this comment?')"><i class="bi bi-trash"></i></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/tasks/<?= $taskId ?>/comments">
            <?= csrf_field() ?>
            <div class="mb-2">
                <textarea class="form-control" name="comment" rows="2" placeholder="Write a comment..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send"></i> Add Comment</button>
        </form>
    </div>
</div>
