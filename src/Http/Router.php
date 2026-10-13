<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\App;

/**
 * Routeur minimal : paramètres {id} numériques, middlewares 'auth' / 'guest',
 * vérification CSRF automatique sur toute requête POST.
 */
final class Router
{
    /** @var list<array{method: string, regex: string, handler: array{class-string, string}, middleware: list<string>}> */
    private array $routes = [];

    /** @param array{class-string, string} $handler */
    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    /** @param array{class-string, string} $handler */
    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    /** Redirection permanente (ex. anciennes URL *.php de la v1). */
    public function redirect(string $from, string $to): void
    {
        $this->add('GET', $from, [RedirectTo::class, $to], []);
    }

    /**
     * @param array{class-string, string} $handler
     * @param list<string>                $middleware
     */
    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        $quoted = preg_quote(rtrim($path, '/') ?: '/', '#');
        $regex = preg_replace('#\\\\\{(\w+)\\\\\}#', '(?P<$1>\d+)', $quoted);
        $this->routes[] = [
            'method'     => $method,
            'regex'      => '#^' . $regex . '$#',
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $allowed = [];

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $request->path(), $matches)) {
                continue;
            }
            if ($route['method'] !== $request->method()) {
                $allowed[] = $route['method'];
                continue;
            }

            $params = array_map('intval', array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));

            if ($redirect = $this->runMiddleware($route['middleware'], $request)) {
                return $redirect;
            }

            [$class, $action] = $route['handler'];

            if ($class === RedirectTo::class) {
                return Response::redirect(url($action), 301);
            }

            return (new $class(App::get()))->{$action}($request, ...$params);
        }

        throw $allowed !== [] ? new HttpException(405) : HttpException::notFound();
    }

    /** @param list<string> $middleware */
    private function runMiddleware(array $middleware, Request $request): ?Response
    {
        $app = App::get();

        if ($request->isPost() && !$app->csrf()->validate($request->input('_token'))) {
            throw new HttpException(419, 'Votre session a expiré. Rechargez la page et réessayez.');
        }

        foreach ($middleware as $name) {
            if ($name === 'auth' && !$app->auth()->check()) {
                if ($request->method() === 'GET') {
                    $app->session()->set('intended', $request->path());
                }
                $app->session()->flash('info', 'Connectez-vous pour continuer.');

                return Response::redirect(url('connexion'));
            }
            if ($name === 'guest' && $app->auth()->check()) {
                return Response::redirect(url('mes-annonces'));
            }
        }

        return null;
    }
}
