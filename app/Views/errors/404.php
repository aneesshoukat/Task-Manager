<?php $title = 'Not Found'; ?>
<?php ob_start(); ?>
<div class="text-center py-5">
    <h1 class="display-1 text-warning">404</h1>
    <h3>Page Not Found</h3>
    <p class="text-muted">The page you requested does not exist.</p>
    <a href="/dashboard" class="btn btn-primary">Go to Dashboard</a>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
