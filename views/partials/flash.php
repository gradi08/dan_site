<?php
$session = \App\Core\App::get()->session();
$messages = array_filter([
    'success' => $session->getFlash('success'),
    'info'    => $session->getFlash('info'),
]);
?>
<?php if ($messages): ?>
    <div class="wrap">
        <?php foreach ($messages as $type => $message): ?>
            <p class="flash flash--<?= e($type) ?>" role="status"><?= e($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
