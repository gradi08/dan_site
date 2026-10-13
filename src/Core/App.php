<?php

declare(strict_types=1);

namespace App\Core;

use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Service\Auth;
use App\Service\ImageUploader;
use App\Service\LoginThrottle;
use PDO;
use Throwable;

/**
 * Point d'entrée applicatif et conteneur de services (instanciation paresseuse).
 */
final class App
{
    private static ?self $instance = null;

    /** @var array<string, object> */
    private array $services = [];

    private ?Request $request = null;

    /** @param array<string, mixed> $config */
    public function __construct(private readonly array $config)
    {
        self::$instance = $this;
    }

    public static function get(): self
    {
        return self::$instance ?? throw new \LogicException('Application non initialisée.');
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $value = $this->config;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function request(): Request
    {
        return $this->request ??= Request::fromGlobals();
    }

    public function db(): PDO
    {
        return $this->service(PDO::class, fn () => Database::connect($this->config['database']));
    }

    public function session(): Session
    {
        return $this->service(Session::class, fn () => new Session($this->request()->isSecure()));
    }

    public function csrf(): Csrf
    {
        return $this->service(Csrf::class, fn () => new Csrf($this->session()));
    }

    public function view(): View
    {
        return $this->service(View::class, fn () => new View(BASE_PATH . '/views'));
    }

    public function users(): UserRepository
    {
        return $this->service(UserRepository::class, fn () => new UserRepository($this->db()));
    }

    public function articles(): ArticleRepository
    {
        return $this->service(ArticleRepository::class, fn () => new ArticleRepository($this->db()));
    }

    public function auth(): Auth
    {
        return $this->service(Auth::class, fn () => new Auth($this->session(), $this->users()));
    }

    public function throttle(): LoginThrottle
    {
        return $this->service(LoginThrottle::class, fn () => new LoginThrottle($this->db()));
    }

    public function uploader(): ImageUploader
    {
        return $this->service(ImageUploader::class, fn () => new ImageUploader(
            $this->config['uploads']['dir'],
            $this->config['uploads']['max_bytes'],
            $this->config['uploads']['max_side'],
        ));
    }

    public function run(): void
    {
        $request = $this->request();

        try {
            $router = new Router();
            (require BASE_PATH . '/config/routes.php')($router);
            $response = $router->dispatch($request);
        } catch (HttpException $e) {
            $response = $this->renderError($e->getStatus(), $e->getMessage());
        } catch (Throwable $e) {
            error_log((string) $e);
            $response = $this->config['debug']
                ? new Response('<pre>' . e((string) $e) . '</pre>', 500)
                : $this->renderError(500);
        }

        $response->withSecurityHeaders()->send();
    }

    private function renderError(int $status, string $message = ''): Response
    {
        try {
            $html = $this->view()->render('pages/errors/error', ['status' => $status, 'message' => $message]);
        } catch (Throwable) {
            $html = "<h1>Erreur $status</h1>";
        }

        return new Response($html, $status);
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @param callable(): T $factory
     * @return T
     */
    private function service(string $id, callable $factory): object
    {
        return $this->services[$id] ??= $factory();
    }
}
