<?php $title = 'Register'; $subtitle = 'Create a new account'; ?>
<?php ob_start(); ?>
<form method="POST" action="/register">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?= escape($_POST['name'] ?? '') ?>" required autofocus>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?= escape($_POST['email'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required minlength="8">
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Register</button>
</form>
<p class="text-center mt-3">
    <a href="/login">Already have an account? Sign in</a>
</p>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/guest.php'; ?>
