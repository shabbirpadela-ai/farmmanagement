<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Base controller.  Provides helpers for rendering views and sending JSON.
 */
class Controller
{
    /** Cached request body (php://input is a stream readable only once). */
    private ?array $_body = null;
    /**
     * Render a PHP view file, optionally wrapped in a layout.
     *
     * @param string $view  Relative path under app/Views/, e.g. 'dashboard/index'
     * @param array  $data  Variables to extract into the view scope
     * @param bool   $layout Whether to wrap with the default layout
     */
    protected function render(string $view, array $data = [], bool $layout = true): void
    {
        // Make variables available in view scope
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View not found: {$view}";
            return;
        }

        if ($layout) {
            $layoutHeader = dirname(__DIR__) . '/Views/layouts/header.php';
            $layoutFooter = dirname(__DIR__) . '/Views/layouts/footer.php';

            if (file_exists($layoutHeader)) {
                require $layoutHeader;
            }
            require $viewFile;
            if (file_exists($layoutFooter)) {
                require $layoutFooter;
            }
        } else {
            require $viewFile;
        }
    }

    /**
     * Send a JSON response and exit.
     */
    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    /**
     * Send a success JSON response.
     */
    protected function ok(mixed $data = null): void
    {
        $this->json(['ok' => true, 'data' => $data]);
    }

    /**
     * Send an error JSON response.
     */
    protected function fail(string $error, int $status = 400): void
    {
        $this->json(['ok' => false, 'error' => $error], $status);
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Get request body as decoded JSON array (cached so php://input is read only once).
     */
    protected function jsonBody(): array
    {
        if ($this->_body === null) {
            $raw = file_get_contents('php://input');
            if (!$raw) {
                $this->_body = [];
            } else {
                $data = json_decode($raw, true);
                $this->_body = is_array($data) ? $data : [];
            }
        }
        return $this->_body;
    }

    /**
     * Get a sanitized POST field value.
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Validate CSRF token from request header or body.
     */
    protected function verifyCsrf(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN']
              ?? $_POST['_csrf']
              ?? $this->jsonBody()['_csrf']
              ?? null;

        if (!$token || !hash_equals((string)($_SESSION['csrf_token'] ?? ''), (string)$token)) {
            $this->fail('Invalid CSRF token', 403);
        }
    }
}
