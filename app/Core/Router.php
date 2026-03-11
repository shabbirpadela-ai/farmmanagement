<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Simple URL router.
 *
 * Usage:
 *   $router = new Router();
 *   $router->get('/dashboard', [DashboardController::class, 'index']);
 *   $router->post('/api/auth/login', [AuthApiController::class, 'login']);
 *   $router->dispatch();
 */
class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch the current HTTP request to the matching route handler.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $_SERVER['REQUEST_URI']    ?? '/';

        // Strip query string
        $path = parse_url($uri, PHP_URL_PATH);
        $path = '/' . trim((string)$path, '/');
        if ($path === '/') {
            $path = '/';
        }

        // Look for exact match first
        if (isset($this->routes[$method][$path])) {
            $this->callHandler($this->routes[$method][$path]);
            return;
        }

        // Try pattern matching for routes with parameters, e.g. /api/customers/{id}
        foreach (($this->routes[$method] ?? []) as $pattern => $handler) {
            $regex = $this->patternToRegex($pattern);
            if (preg_match($regex, $path, $matches)) {
                array_shift($matches); // remove full match
                $this->callHandler($handler, $matches);
                return;
            }
        }

        // 404
        http_response_code(404);
        if ($this->isApiRequest($path)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Not found']);
        } else {
            echo '<h1>404 Not Found</h1>';
        }
    }

    private function patternToRegex(string $pattern): string
    {
        $regex = preg_replace('/\{[a-zA-Z_]+\}/', '([^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }

    private function callHandler(array $handler, array $params = []): void
    {
        [$class, $method] = $handler;
        $controller = new $class();
        $controller->$method(...$params);
    }

    private function isApiRequest(string $path): bool
    {
        return str_starts_with($path, '/api');
    }
}
