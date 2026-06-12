<?php $title = escape($task['title']); ?>
<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-card-text"></i> <?= escape($task['title']) ?></h1>
    <div>
        <a href="/tasks/<?= $task['id'] ?>/edit" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <a href="/tasks" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3 fw-bold">Status</div>
            <div class="col-md-9">
                <?php if ($task['status'] === 'completed'): ?>
                    <span class="badge bg-success">Completed</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Pending</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3 fw-bold">Priority</div>
            <div class="col-md-9">
                <span class="badge bg-<?= $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'secondary') ?>">
                    <?= escape(ucfirst($task['priority'])) ?>
                </span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3 fw-bold">Due Date</div>
            <div class="col-md-9"><?= escape($task['due_date'] ?? 'Not set') ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3 fw-bold">Created</div>
            <div class="col-md-9"><?= escape($task['created_at']) ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3 fw-bold">Updated</div>
            <div class="col-md-9"><?= escape($task['updated_at']) ?></div>
        </div>
        <?php if ($task['description']): ?>
            <div class="row">
                <div class="col-md-3 fw-bold">Description</div>
                <div class="col-md-9">
                    <p class="mb-0"><?= nl2br(escape($task['description'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
