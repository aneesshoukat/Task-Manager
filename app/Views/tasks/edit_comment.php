<?php $title = 'Edit Comment'; ?>
<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-pencil"></i> Edit Comment</h1>
    <a href="/tasks/<?= $taskId ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/tasks/<?= $taskId ?>/comments/<?= $comment['id'] ?>">
            <?= csrf_field() ?>
            <?= method_field('PUT') ?>
            <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea class="form-control" id="comment" name="comment" rows="4" required><?= escape($comment['comment']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Comment</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
