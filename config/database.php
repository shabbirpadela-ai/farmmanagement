<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';

/**
 * Returns a PDO connection using credentials from .env.
 * Throws RuntimeException on failure so callers can handle it cleanly.
 */
function connectDB(): PDO
{
    $host    = env('DB_HOST', 'localhost');
    $dbname  = env('DB_NAME', '');
    $user    = env('DB_USER', '');
    $pass    = env('DB_PASS', '');
    $charset = 'utf8mb4';

    if ($dbname === '' || $user === '') {
        throw new RuntimeException(
            'Database not configured. Set DB_NAME and DB_USER in your .env file.'
        );
    }

    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    return new PDO($dsn, $user, $pass, $options);
}