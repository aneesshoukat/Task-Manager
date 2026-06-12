<?php $title = 'Trashed Tasks'; ?>
<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-trash"></i> Trashed Tasks</h1>
    <a href="/tasks" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<?php if (empty($tasks)): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="text-muted mt-3">Trash is empty.</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Deleted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= escape($task['title']) ?></td>
                        <td><?= escape($task['deleted_at']) ?></td>
                        <td>
                            <a href="/tasks/<?= $task['id'] ?>/restore" class="btn btn-sm btn-success"><i class="bi bi-arrow-counterclockwise"></i> Restore</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
