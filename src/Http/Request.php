<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, mixed> $files
     * @param array<string, mixed> $server
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly string $basePath,
        private readonly array $query = [],
        private readonly array $body = [],
        private readonly array $files = [],
        private readonly array $server = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        $uriPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
        $basePath = self::detectBasePath($_SERVER['SCRIPT_NAME'] ?? '', $uriPath);

        $path = substr($uriPath, strlen($basePath)) ?: '/';
        $path = '/' . trim($path, '/');

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        return new self(
            $method === 'HEAD' ? 'GET' : $method,
            $path,
            $basePath,
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER,
        );
    }

    /**
     * Prend en charge un déploiement à la racine, dans un sous-dossier,
     * ou derrière le .htaccess racine qui réécrit vers /public.
     */
    public static function detectBasePath(string $scriptName, string $uriPath): string
    {
        $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($base !== '' && !str_starts_with($uriPath, $base) && str_ends_with($base, '/public')) {
            $base = substr($base, 0, -strlen('/public'));
        }

        return str_starts_with($uriPath, $base) ? $base : '';
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $value = $this->body[$key] ?? $default;

        return is_string($value) ? trim($value) : $value;
    }

    /**
     * @param list<string> $keys
     * @return array<string, mixed>
     */
    public function only(array $keys): array
    {
        $out = [];
        foreach ($keys as $key) {
            $out[$key] = $this->input($key, '');
        }

        return $out;
    }

    /** @return array<string, mixed>|null */
    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        return is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE ? $file : null;
    }

    /**
     * Fichiers d'un champ multiple (name="images[]"), au format d'une entrée $_FILES par fichier.
     *
     * @return list<array<string, mixed>>
     */
    public function files(string $key): array
    {
        $raw = $this->files[$key] ?? null;
        if (!is_array($raw) || !is_array($raw['name'] ?? null)) {
            return ($file = $this->file($key)) ? [$file] : [];
        }

        $files = [];
        foreach (array_keys($raw['name']) as $i) {
            if (($raw['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $files[] = [
                'name'     => $raw['name'][$i],
                'type'     => $raw['type'][$i] ?? '',
                'tmp_name' => $raw['tmp_name'][$i] ?? '',
                'error'    => $raw['error'][$i],
                'size'     => $raw['size'][$i] ?? 0,
            ];
        }

        return $files;
    }

    /** Vrai si PHP a rejeté le corps de la requête car il dépasse post_max_size. */
    public function exceedsPostMaxSize(): bool
    {
        return $this->method === 'POST'
            && $this->body === []
            && $this->files === []
            && (int) ($this->server['CONTENT_LENGTH'] ?? 0) > 0;
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function isSecure(): bool
    {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off')
            || ($this->server['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }
}
