<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

class Inventory
{
    public static function all(): array
    {
        return Db::all('SELECT * FROM inventory_items ORDER BY name');
    }

    public static function findById(int $id): ?array
    {
        return Db::first('SELECT * FROM inventory_items WHERE id = ?', [$id]);
    }

    public static function transactions(array $filters = []): array
    {
        $sql    = 'SELECT t.*, i.name AS item_name
                   FROM inventory_transactions t
                   JOIN inventory_items i ON i.id = t.item_id
                   WHERE 1=1';
        $params = [];

        if (!empty($filters['item_id'])) {
            $sql     .= ' AND t.item_id = ?';
            $params[] = (int)$filters['item_id'];
        }

        $sql .= ' ORDER BY t.created_at DESC';

        if (!empty($filters['limit'])) {
            $sql     .= ' LIMIT ?';
            $params[] = (int)$filters['limit'];
        }

        return Db::all($sql, $params);
    }

    public static function addPurchase(array $data): string
    {
        $pdo = \App\Core\Db::getInstance();
        $pdo->beginTransaction();

        try {
            // Ensure item exists; create if not
            $item = Db::first(
                'SELECT * FROM inventory_items WHERE id = ?',
                [(int)$data['item_id']]
            );

            if (!$item) {
                throw new \RuntimeException('Inventory item not found');
            }

            $qty = (float)$data['quantity'];

            // Update item stock
            Db::execute(
                'UPDATE inventory_items
                 SET quantity = quantity + ?, purchased = purchased + ?
                 WHERE id = ?',
                [$qty, $qty, (int)$data['item_id']]
            );

            // Record transaction
            $id = Db::insert(
                'INSERT INTO inventory_transactions
                 (item_id, type, quantity, unit_cost, supplier, notes, created_at)
                 VALUES (?, "purchase", ?, ?, ?, ?, NOW())',
                [
                    (int)$data['item_id'],
                    $qty,
                    (float)($data['unit_cost'] ?? 0),
                    $data['supplier'] ?? '',
                    $data['notes']    ?? '',
                ]
            );

            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function recordUsage(array $data): string
    {
        $pdo = \App\Core\Db::getInstance();
        $pdo->beginTransaction();

        try {
            $qty = (float)$data['quantity'];

            Db::execute(
                'UPDATE inventory_items
                 SET quantity = GREATEST(0, quantity - ?), used = used + ?
                 WHERE id = ?',
                [$qty, $qty, (int)$data['item_id']]
            );

            $id = Db::insert(
                'INSERT INTO inventory_transactions
                 (item_id, type, quantity, notes, created_at)
                 VALUES (?, "usage", ?, ?, NOW())',
                [
                    (int)$data['item_id'],
                    $qty,
                    $data['notes'] ?? '',
                ]
            );

            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function createItem(array $data): string
    {
        return Db::insert(
            'INSERT INTO inventory_items (name, quantity, purchased, used, supplier, unit_cost)
             VALUES (?, 0, 0, 0, ?, ?)',
            [
                $data['name'],
                $data['supplier'] ?? '',
                (float)($data['unit_cost'] ?? 0),
            ]
        );
    }
}
