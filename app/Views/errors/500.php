<?php $title = 'Server Error'; ?>
<?php ob_start(); ?>
<div class="text-center py-5">
    <h1 class="display-1 text-danger">500</h1>
    <h3>Internal Server Error</h3>
    <p class="text-muted">Something went wrong. Please try again later.</p>
    <a href="/dashboard" class="btn btn-primary">Go to Dashboard</a>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
