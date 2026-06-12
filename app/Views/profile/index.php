<?php $title = 'Profile'; ?>
<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-person"></i> Profile</h1>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="mb-3">
                    <?php if ($user['avatar']): ?>
                        <img src="/uploads/<?= escape($user['avatar']) ?>" class="rounded-circle" width="150" height="150" alt="Avatar">
                    <?php else: ?>
                        <i class="bi bi-person-circle display-1 text-muted"></i>
                    <?php endif; ?>
                </div>
                <h5><?= escape($user['name']) ?></h5>
                <p class="text-muted"><?= escape($user['email']) ?></p>
                <form method="POST" action="/profile/avatar" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="file" class="form-control form-control-sm mb-2" name="avatar" accept="image/*">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Upload Avatar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Edit Profile</div>
            <div class="card-body">
                <form method="POST" action="/profile">
                    <?= csrf_field() ?>
                    <?= method_field('PUT') ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= escape($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= escape($user['email']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
