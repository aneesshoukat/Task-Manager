<?php $title = 'Edit Task'; ?>
<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-pencil"></i> Edit Task</h1>
    <a href="/tasks" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/tasks/<?= $task['id'] ?>">
            <?= csrf_field() ?>
            <?= method_field('PUT') ?>
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="<?= escape($task['title']) ?>" required maxlength="255">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= escape($task['description'] ?? '') ?></textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="low" <?= $task['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= $task['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= $task['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?= escape($task['due_date'] ?? '') ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Task</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
