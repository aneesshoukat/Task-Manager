<?php $flashes = \App\Core\Session::getFlashes(); ?>
<?php if (!empty($flashes)): ?>
    <?php foreach ($flashes as $key => $messages): ?>
        <?php if ($key === 'error'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= escape(is_array($messages) ? implode('<br>', $messages) : $messages) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($key === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= escape(is_array($messages) ? implode('<br>', $messages) : $messages) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($key === 'errors' && is_array($messages)): ?>
            <?php foreach ($messages as $field => $errors): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= escape($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
