<?php $title = 'Forbidden'; ?>
<?php ob_start(); ?>
<div class="text-center py-5">
    <h1 class="display-1 text-danger">403</h1>
    <h3>Forbidden</h3>
    <p class="text-muted">You do not have permission to access this page.</p>
    <a href="/dashboard" class="btn btn-primary">Go to Dashboard</a>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
