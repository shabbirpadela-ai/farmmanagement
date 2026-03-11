<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Session-based authentication and role-check helper.
 */
class Auth
{
    private const SESSION_USER_KEY  = 'auth_user';
    private const SESSION_CSRF_KEY  = 'csrf_token';

    /**
     * Start the PHP session (call once in the front controller).
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $name = env('SESSION_NAME') ?? 'dhf_session';
            $lifetime = (int)(env('SESSION_LIFETIME') ?? 7200);

            session_name($name);
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path'     => '/',
                'secure'   => (env('APP_ENV') === 'production'),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }

        // Generate CSRF token if not present
        if (empty($_SESSION[self::SESSION_CSRF_KEY])) {
            $_SESSION[self::SESSION_CSRF_KEY] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Return true if a user is currently authenticated.
     */
    public static function check(): bool
    {
        return !empty($_SESSION[self::SESSION_USER_KEY]);
    }

    /**
     * Return the currently authenticated user array, or null.
     */
    public static function user(): ?array
    {
        return $_SESSION[self::SESSION_USER_KEY] ?? null;
    }

    /**
     * Return the current user's role, or null.
     */
    public static function role(): ?string
    {
        return $_SESSION[self::SESSION_USER_KEY]['role'] ?? null;
    }

    /**
     * Check if the current user has the given role.
     */
    public static function hasRole(string $role): bool
    {
        return self::role() === $role;
    }

    /**
     * Require authentication; redirect to login if missing.
     * Should be called at the top of protected page controllers.
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Require a specific role; send 403 or redirect to dashboard.
     */
    public static function requireRole(string $role): void
    {
        self::requireAuth();
        if (self::role() !== $role) {
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Store user data in session (called after successful login).
     */
    public static function login(array $user): void
    {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        $_SESSION[self::SESSION_USER_KEY] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'name'     => $user['full_name'],
            'role'     => $user['role'],
            'email'    => $user['email'] ?? '',
        ];
    }

    /**
     * Destroy the session (logout).
     */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Return the current CSRF token.
     */
    public static function csrfToken(): string
    {
        return $_SESSION[self::SESSION_CSRF_KEY] ?? '';
    }

    /**
     * Validate a CSRF token against the session.
     */
    public static function validateCsrf(string $token): bool
    {
        return hash_equals(
            (string)($_SESSION[self::SESSION_CSRF_KEY] ?? ''),
            $token
        );
    }
}
