<?php $title = 'Login'; $subtitle = 'Sign in to your account'; ?>
<?php ob_start(); ?>
<form method="POST" action="/login">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?= escape($_POST['email'] ?? '') ?>" required autofocus>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Sign In</button>
</form>
<p class="text-center mt-3">
    <a href="/register">Don't have an account? Register</a>
</p>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/guest.php'; ?>
