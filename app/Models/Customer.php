<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

class Customer
{
    public static function all(): array
    {
        return Db::all('SELECT * FROM customers ORDER BY name');
    }

    public static function findById(int $id): ?array
    {
        return Db::first('SELECT * FROM customers WHERE id = ?', [$id]);
    }

    public static function create(array $data): string
    {
        return Db::insert(
            'INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)',
            [
                $data['name'],
                $data['email']   ?? '',
                $data['phone']   ?? '',
                $data['address'] ?? '',
            ]
        );
    }

    public static function update(int $id, array $data): int
    {
        return Db::execute(
            'UPDATE customers SET name=?, email=?, phone=?, address=? WHERE id=?',
            [
                $data['name'],
                $data['email']   ?? '',
                $data['phone']   ?? '',
                $data['address'] ?? '',
                $id,
            ]
        );
    }

    public static function delete(int $id): int
    {
        return Db::execute('DELETE FROM customers WHERE id = ?', [$id]);
    }

    public static function adjustBalance(int $id, float $amount): void
    {
        Db::execute(
            'UPDATE customers SET balance = balance + ? WHERE id = ?',
            [$amount, $id]
        );
    }

    public static function totalOutstanding(): float
    {
        $row = Db::first('SELECT COALESCE(SUM(balance),0) AS total FROM customers');
        return (float)($row['total'] ?? 0);
    }
}
