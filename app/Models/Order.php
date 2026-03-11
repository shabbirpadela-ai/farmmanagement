<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

class Order
{
    public static function all(array $filters = []): array
    {
        $sql    = 'SELECT o.*, c.name AS customer_name
                   FROM orders o
                   JOIN customers c ON c.id = o.customer_id
                   WHERE 1=1';
        $params = [];

        if (!empty($filters['date_from'])) {
            $sql     .= ' AND o.date >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql     .= ' AND o.date <= ?';
            $params[] = $filters['date_to'];
        }

        $sql .= ' ORDER BY o.date DESC, o.id DESC';
        return Db::all($sql, $params);
    }

    public static function findById(int $id): ?array
    {
        return Db::first(
            'SELECT o.*, c.name AS customer_name
             FROM orders o JOIN customers c ON c.id = o.customer_id
             WHERE o.id = ?',
            [$id]
        );
    }

    public static function items(int $orderId): array
    {
        return Db::all(
            'SELECT * FROM order_items WHERE order_id = ?',
            [$orderId]
        );
    }

    public static function create(array $data, array $items): string
    {
        $pdo = \App\Core\Db::getInstance();
        $pdo->beginTransaction();

        try {
            $total = array_sum(array_map(
                fn($i) => (float)$i['qty'] * (float)$i['unit_price'],
                $items
            ));

            $paid   = (float)($data['paid_amount'] ?? 0);
            $balance = $total - $paid;

            $orderId = Db::insert(
                'INSERT INTO orders (customer_id, date, total, paid, balance, payment_method, payment_status, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    (int)$data['customer_id'],
                    $data['date'] ?? date('Y-m-d'),
                    $total,
                    $paid,
                    $balance,
                    $data['payment_method'] ?? 'cash',
                    $balance <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                    $data['notes'] ?? '',
                ]
            );

            foreach ($items as $item) {
                Db::insert(
                    'INSERT INTO order_items (order_id, product, quantity, unit_price, line_total)
                     VALUES (?, ?, ?, ?, ?)',
                    [
                        $orderId,
                        $item['product'],
                        (int)$item['qty'],
                        (float)$item['unit_price'],
                        (float)$item['qty'] * (float)$item['unit_price'],
                    ]
                );
            }

            // Update customer balance
            Customer::adjustBalance((int)$data['customer_id'], $balance);

            $pdo->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function recordPayment(int $orderId, float $amount, string $method): void
    {
        $pdo = \App\Core\Db::getInstance();
        $pdo->beginTransaction();

        try {
            $order = self::findById($orderId);
            if (!$order) {
                throw new \RuntimeException('Order not found');
            }

            $newPaid    = (float)$order['paid'] + $amount;
            $newBalance = (float)$order['total'] - $newPaid;
            $status     = $newBalance <= 0 ? 'paid' : 'partial';

            Db::execute(
                'UPDATE orders SET paid=?, balance=?, payment_method=?, payment_status=? WHERE id=?',
                [
                    $newPaid,
                    max(0, $newBalance),
                    $method,
                    $status,
                    $orderId,
                ]
            );

            Db::insert(
                'INSERT INTO payments (order_id, amount, method, created_at)
                 VALUES (?, ?, ?, NOW())',
                [$orderId, $amount, $method]
            );

            // Reduce customer balance
            Customer::adjustBalance((int)$order['customer_id'], -$amount);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function todayRevenue(): float
    {
        $today = date('Y-m-d');
        $row   = Db::first(
            'SELECT COALESCE(SUM(paid),0) AS total FROM orders WHERE date = ?',
            [$today]
        );
        return (float)($row['total'] ?? 0);
    }

    public static function todayCash(): float
    {
        $today = date('Y-m-d');
        $row   = Db::first(
            "SELECT COALESCE(SUM(paid),0) AS total FROM orders WHERE date=? AND payment_method='cash'",
            [$today]
        );
        return (float)($row['total'] ?? 0);
    }

    public static function todayBank(): float
    {
        $today = date('Y-m-d');
        $row   = Db::first(
            "SELECT COALESCE(SUM(paid),0) AS total FROM orders WHERE date=? AND payment_method='bank'",
            [$today]
        );
        return (float)($row['total'] ?? 0);
    }
}
