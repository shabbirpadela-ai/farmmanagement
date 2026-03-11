<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

class Crate
{
    public static function getStock(): int
    {
        $row = Db::first('SELECT quantity FROM crate_stock WHERE id = 1');
        return (int)($row['quantity'] ?? 0);
    }

    public static function adjust(int $qty, string $type, string $source): void
    {
        $pdo = \App\Core\Db::getInstance();
        $pdo->beginTransaction();

        try {
            Db::execute(
                'UPDATE crate_stock SET quantity = GREATEST(0, quantity + ?) WHERE id = 1',
                [$qty]
            );

            $newBalance = self::getStock();

            Db::insert(
                'INSERT INTO crate_movements (type, quantity, source, balance, created_at)
                 VALUES (?, ?, ?, ?, NOW())',
                [$type, $qty, $source, $newBalance]
            );

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function movements(int $limit = 50): array
    {
        return Db::all(
            'SELECT * FROM crate_movements ORDER BY created_at DESC LIMIT ?',
            [$limit]
        );
    }

    public static function soldToday(): int
    {
        $today = date('Y-m-d');
        $row   = Db::first(
            "SELECT COALESCE(SUM(ABS(quantity)),0) AS total
             FROM crate_movements
             WHERE type='sale' AND DATE(created_at)=?",
            [$today]
        );
        return (int)($row['total'] ?? 0);
    }

    public static function damagedTotal(): int
    {
        $row = Db::first(
            "SELECT COALESCE(SUM(ABS(quantity)),0) AS total
             FROM crate_movements WHERE type='damaged'"
        );
        return (int)($row['total'] ?? 0);
    }
}
