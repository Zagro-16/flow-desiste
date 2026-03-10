<?php if (!empty($_SESSION['flash'])): ?>
    <?php foreach ($_SESSION['flash'] as $type => $messages): ?>
        <?php foreach ((array)$messages as $message): ?>
            <div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div>
        <?php endforeach; ?>
    <?php endforeach; unset($_SESSION['flash']); ?>
<?php endif; ?>
