<?php

declare(strict_types=1);

use App\Core\App;

/** Échappe une valeur pour un contexte HTML. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL relative à la racine de l'application (gère les sous-dossiers). */
function url(string $path = '', array $query = []): string
{
    $base = App::get()->request()->basePath();
    $url = $base . '/' . ltrim($path, '/');
    $query = array_filter($query, static fn ($v) => $v !== null && $v !== '');

    return $query ? $url . '?' . http_build_query($query) : $url;
}

/** URL d'un fichier statique avec cache-busting basé sur la date de modification. */
function asset(string $path): string
{
    $file = BASE_PATH . '/public/' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '1';

    return url($path) . '?v=' . $version;
}

/** URL publique d'une image d'annonce (fichier uploadé ou URL distante pour les données de démo). */
function image_url(?string $image): ?string
{
    if ($image === null || $image === '') {
        return null;
    }
    if (preg_match('#^https://#', $image)) {
        return $image;
    }

    return url(App::get()->config('uploads.url') . '/' . rawurlencode($image));
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(App::get()->csrf()->token()) . '">';
}

function old(string $key, mixed $default = ''): string
{
    $old = App::get()->session()->getFlash('old', []);

    return (string) ($old[$key] ?? $default);
}

/** Message d'erreur de validation pour un champ, ou null. */
function error(string $key): ?string
{
    return App::get()->session()->getFlash('errors', [])[$key] ?? null;
}

/** Attributs ARIA reliant un champ à son message d'erreur serveur. */
function field_attrs(string $key): string
{
    return error($key) !== null
        ? sprintf('aria-invalid="true" aria-describedby="%1$s-error"', e($key))
        : sprintf('aria-describedby="%1$s-error"', e($key));
}

function field_error(string $key): string
{
    return App::get()->view()->partial('partials/field-error', ['name' => $key]);
}

function current_user(): ?array
{
    return App::get()->auth()->user();
}

function is_active(string $path): bool
{
    $current = App::get()->request()->path();
    $path = '/' . trim($path, '/');

    return $path === '/' ? $current === '/' : str_starts_with($current, $path);
}

/** Date relative lisible en français : « il y a 3 jours ». */
function time_ago(string $datetime): string
{
    $diff = time() - strtotime($datetime);

    return match (true) {
        $diff < 60        => "à l'instant",
        $diff < 3600      => 'il y a ' . intdiv($diff, 60) . ' min',
        $diff < 86400     => 'il y a ' . intdiv($diff, 3600) . ' h',
        $diff < 172800    => 'hier',
        $diff < 2592000   => 'il y a ' . intdiv($diff, 86400) . ' jours',
        default           => 'le ' . date('d/m/Y', strtotime($datetime)),
    };
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $letters = array_map(static fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));

    return implode('', $letters) ?: '?';
}
