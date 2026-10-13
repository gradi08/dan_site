<?php

declare(strict_types=1);

use App\Core\Env;

return [
    'name'     => Env::get('APP_NAME', 'VideGrenier+'),
    'env'      => Env::get('APP_ENV', 'production'),
    'debug'    => Env::bool('APP_DEBUG', false),
    'url'      => rtrim((string) Env::get('APP_URL', ''), '/'),
    'timezone' => Env::get('APP_TIMEZONE', 'Africa/Kinshasa'),

    'author' => [
        'name' => Env::get('APP_AUTHOR', "Gradi N'chaki"),
        'url'  => Env::get('APP_AUTHOR_URL', 'https://gradinchaki.vercel.app'),
    ],

    'database' => [
        'driver'   => Env::get('DB_DRIVER', 'sqlite'), // mysql | sqlite
        'host'     => Env::get('DB_HOST', '127.0.0.1'),
        'port'     => (int) Env::get('DB_PORT', '3306'),
        'name'     => Env::get('DB_NAME', 'videgrenier'),
        'user'     => Env::get('DB_USER', 'root'),
        'password' => Env::get('DB_PASSWORD', ''),
        // Chemin relatif résolu depuis la racine du projet.
        'path'     => (static function (string $p): string {
            return preg_match('#^([a-zA-Z]:)?[\\\\/]#', $p) ? $p : BASE_PATH . '/' . $p;
        })(Env::get('DB_PATH', 'storage/database.sqlite')),
    ],

    'uploads' => [
        'dir'       => BASE_PATH . '/public/uploads',
        'url'       => 'uploads',
        'max_bytes' => 5 * 1024 * 1024,
        'max_side'  => 1600,
    ],

    'phone' => [
        'country_code' => Env::get('PHONE_COUNTRY_CODE', '+243'),
    ],

    'pagination' => [
        'per_page' => 12,
    ],
];
