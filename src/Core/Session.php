<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Session sécurisée avec messages flash, anciennes saisies et erreurs de validation.
 */
final class Session
{
    private const FLASH = '_flash';
    private const FLASH_NEXT = '_flash_next';

    public function __construct(bool $secure)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name('vg_session');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_start();

        // Les données flash de la requête précédente deviennent lisibles, puis expirent.
        $_SESSION[self::FLASH] = $_SESSION[self::FLASH_NEXT] ?? [];
        $_SESSION[self::FLASH_NEXT] = [];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $p['path'],
                'secure'   => $p['secure'],
                'httponly' => $p['httponly'],
                'samesite' => $p['samesite'],
            ]);
        }
        session_destroy();
    }

    public function flash(string $key, mixed $value): void
    {
        $_SESSION[self::FLASH_NEXT][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION[self::FLASH][$key] ?? $default;
    }
}
