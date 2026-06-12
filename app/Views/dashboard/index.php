<?php $title = 'Dashboard'; ?>
<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Total Tasks</h5>
                <p class="display-6"><?= $totalTasks ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <h5 class="card-title text-success">Completed</h5>
                <p class="display-6"><?= $completedTasks ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h5 class="card-title text-warning">Pending</h5>
                <p class="display-6"><?= $pendingTasks ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h5 class="card-title text-danger">Overdue</h5>
                <p class="display-6"><?= $overdueTasks ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Completion</div>
            <div class="card-body">
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-success" style="width: <?= $completionPercent ?>%">
                        <?= $completionPercent ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Upcoming Tasks</span>
                <a href="/tasks" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($upcomingTasks)): ?>
                    <p class="text-muted mb-0">No upcoming tasks.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($upcomingTasks as $task): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="/tasks/<?= $task['id'] ?>"><?= escape($task['title']) ?></a>
                                <span class="badge bg-<?= $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'secondary') ?>">
                                    <?= escape($task['due_date']) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Recent Activity</div>
            <div class="card-body">
                <?php if (empty($recentActivities)): ?>
                    <p class="text-muted mb-0">No recent activity.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentActivities as $activity): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?= escape(ucfirst(str_replace('_', ' ', $activity['action']))) ?></span>
                                <small class="text-muted"><?= escape($activity['created_at']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
