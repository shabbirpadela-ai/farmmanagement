<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

class Purchase
{
    public static function all(array $filters = []): array
    {
        $sql    = 'SELECT * FROM purchases WHERE 1=1';
        $params = [];

        if (!empty($filters['date_from'])) {
            $sql     .= ' AND date >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql     .= ' AND date <= ?';
            $params[] = $filters['date_to'];
        }

        $sql .= ' ORDER BY date DESC';
        return Db::all($sql, $params);
    }

    public static function create(array $data): string
    {
        return Db::insert(
            'INSERT INTO purchases (date, item_name, category, quantity, unit, unit_cost, total_cost, supplier, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['date']      ?? date('Y-m-d'),
                $data['item_name'],
                $data['category']  ?? 'other',
                (float)$data['quantity'],
                $data['unit']      ?? 'kg',
                (float)($data['unit_cost']   ?? 0),
                (float)($data['total_cost']  ?? 0),
                $data['supplier']  ?? '',
                $data['notes']     ?? '',
            ]
        );
    }

    public static function delete(int $id): int
    {
        return Db::execute('DELETE FROM purchases WHERE id = ?', [$id]);
    }
}
