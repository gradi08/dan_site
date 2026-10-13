<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Env;

define('BASE_PATH', dirname(__DIR__));

// Composer si disponible, sinon autoloader PSR-4 minimal (hébergement mutualisé sans Composer).
if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require BASE_PATH . '/vendor/autoload.php';
} else {
    spl_autoload_register(static function (string $class): void {
        if (str_starts_with($class, 'App\\')) {
            $file = BASE_PATH . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
            if (is_file($file)) {
                require $file;
            }
        }
    });
}

require_once BASE_PATH . '/src/Support/helpers.php';

Env::load(BASE_PATH . '/.env');

$config = require BASE_PATH . '/config/app.php';

date_default_timezone_set($config['timezone']);
error_reporting(E_ALL);
ini_set('display_errors', $config['debug'] ? '1' : '0');
ini_set('log_errors', '1');

return new App($config);
