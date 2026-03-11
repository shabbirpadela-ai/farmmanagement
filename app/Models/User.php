<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

class User
{
    public static function findByUsername(string $username): ?array
    {
        return Db::first(
            'SELECT * FROM users WHERE username = ? LIMIT 1',
            [$username]
        );
    }

    public static function findById(int $id): ?array
    {
        return Db::first(
            'SELECT id, username, full_name, email, role, status FROM users WHERE id = ?',
            [$id]
        );
    }

    public static function all(): array
    {
        return Db::all(
            'SELECT id, username, full_name, email, role, status, created_at FROM users ORDER BY id'
        );
    }

    public static function create(array $data): string
    {
        return Db::insert(
            'INSERT INTO users (username, full_name, email, role, password_hash, status)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['username'],
                $data['full_name'],
                $data['email'] ?? '',
                $data['role'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['status'] ?? 'active',
            ]
        );
    }

    public static function updateStatus(int $id, string $status): int
    {
        return Db::execute(
            'UPDATE users SET status = ? WHERE id = ?',
            [$status, $id]
        );
    }

    public static function changePassword(int $id, string $newPassword): int
    {
        return Db::execute(
            'UPDATE users SET password_hash = ? WHERE id = ?',
            [password_hash($newPassword, PASSWORD_BCRYPT), $id]
        );
    }
}
