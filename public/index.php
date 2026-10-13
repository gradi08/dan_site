<?php

declare(strict_types=1);

// Serveur PHP intégré : laisser passer les fichiers statiques existants.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($file !== __FILE__ && is_file($file)) {
        return false;
    }
}

/** @var \App\Core\App $app */
$app = require dirname(__DIR__) . '/src/bootstrap.php';
$app->run();
