<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Moteur de templates PHP natif : une vue est rendue puis injectée dans un layout.
 */
final class View
{
    public function __construct(private readonly string $path)
    {
    }

    /** @var array<string, mixed> Métadonnées définies par la vue pour le layout (title, description…). */
    private array $shared = [];

    /** @param array<string, mixed> $data */
    public function render(string $template, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $this->shared = [];
        $content = $this->renderFile($template, $data);

        if ($layout === null) {
            return $content;
        }

        return $this->renderFile($layout, ['content' => $content] + $this->shared + $data);
    }

    /** Appelé depuis un template : $this->set('title', '…'). */
    public function set(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    /** @param array<string, mixed> $data */
    public function partial(string $template, array $data = []): string
    {
        return $this->renderFile($template, $data);
    }

    /** @param array<string, mixed> $__data */
    private function renderFile(string $__template, array $__data): string
    {
        $__file = $this->path . '/' . $__template . '.php';
        if (!is_file($__file)) {
            throw new \RuntimeException("Vue introuvable : $__template");
        }

        extract($__data, EXTR_SKIP);
        ob_start();
        try {
            require $__file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return (string) ob_get_clean();
    }
}
