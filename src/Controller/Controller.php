<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\App;
use App\Http\Response;

abstract class Controller
{
    public function __construct(protected readonly App $app)
    {
    }

    /** @param array<string, mixed> $data */
    protected function view(string $template, array $data = [], int $status = 200): Response
    {
        return new Response($this->app->view()->render($template, $data), $status);
    }

    protected function redirect(string $path, array $query = []): Response
    {
        return Response::redirect(url($path, $query));
    }

    /**
     * Renvoie vers le formulaire avec les erreurs et la saisie (hors mots de passe).
     *
     * @param array<string, string> $errors
     * @param array<string, mixed>  $old
     */
    protected function backWithErrors(string $path, array $errors, array $old = []): Response
    {
        $session = $this->app->session();
        $session->flash('errors', $errors);
        $session->flash('old', array_diff_key($old, array_flip(['password', 'password_confirmation', 'current_password'])));

        return $this->redirect($path);
    }

    protected function success(string $message): void
    {
        $this->app->session()->flash('success', $message);
    }

    protected function userId(): int
    {
        return (int) $this->app->auth()->id();
    }
}
