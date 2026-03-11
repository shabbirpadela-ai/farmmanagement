<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Database singleton wrapper.
 * Provides a single shared PDO connection for the request lifetime.
 */
class Db
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            require_once dirname(__DIR__, 2) . '/config/database.php';
            self::$instance = connectDB();
        }
        return self::$instance;
    }

    /**
     * Execute a prepared statement and return all rows.
     */
    public static function all(string $sql, array $params = []): array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute a prepared statement and return first row or null.
     */
    public static function first(string $sql, array $params = []): ?array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Execute a prepared statement (INSERT/UPDATE/DELETE) and return affected rows.
     */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Execute an INSERT and return the last insert ID.
     */
    public static function insert(string $sql, array $params = []): string
    {
        self::execute($sql, $params);
        return self::getInstance()->lastInsertId();
    }
}
