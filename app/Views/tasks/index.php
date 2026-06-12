<?php $title = 'Tasks'; ?>
<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-list-task"></i> Tasks</h1>
    <div>
        <a href="/tasks/create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Task</a>
        <a href="/tasks/export" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export CSV</a>
        <a href="/tasks/trashed" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Trash</a>
    </div>
</div>

<form method="GET" class="row g-2 mb-4">
    <div class="col-md-4">
        <input type="text" class="form-control" name="search" placeholder="Search tasks..." value="<?= escape($filters['search'] ?? '') ?>">
    </div>
    <div class="col-md-2">
        <select class="form-select" name="status">
            <option value="">All Status</option>
            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="priority">
            <option value="">All Priority</option>
            <option value="low" <?= ($filters['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
            <option value="medium" <?= ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
            <option value="high" <?= ($filters['priority'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="sort">
            <option value="latest" <?= ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Latest</option>
            <option value="oldest" <?= ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest</option>
            <option value="priority" <?= ($filters['sort'] ?? '') === 'priority' ? 'selected' : '' ?>>Priority</option>
            <option value="due_date" <?= ($filters['sort'] ?? '') === 'due_date' ? 'selected' : '' ?>>Due Date</option>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
    </div>
</form>

<?php if (empty($tasks)): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="text-muted mt-3">No tasks found.</p>
        <a href="/tasks/create" class="btn btn-primary">Create your first task</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr class="<?= $task['status'] === 'completed' ? 'table-success' : '' ?>">
                        <td>
                            <a href="/tasks/<?= $task['id'] ?>" class="text-decoration-none fw-semibold">
                                <?= escape($task['title']) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-<?= $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'secondary') ?>">
                                <?= escape(ucfirst($task['priority'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($task['status'] === 'completed'): ?>
                                <span class="badge bg-success">Completed</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><?= escape($task['due_date'] ?? '-') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/tasks/<?= $task['id'] ?>" class="btn btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                <a href="/tasks/<?= $task['id'] ?>/edit" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <?php if ($task['status'] !== 'completed'): ?>
                                    <a href="/tasks/<?= $task['id'] ?>/complete" class="btn btn-outline-success" title="Complete"><i class="bi bi-check-lg"></i></a>
                                <?php endif; ?>
                                <a href="/tasks/<?= $task['id'] ?>/delete" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this task?')"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php require __DIR__ . '/../partials/pagination.php' ?>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
