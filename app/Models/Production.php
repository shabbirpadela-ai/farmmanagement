<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

class Production
{
    public static function all(array $filters = []): array
    {
        $sql    = 'SELECT p.*, u.full_name AS employee_name
                   FROM production_entries p
                   LEFT JOIN users u ON u.id = p.user_id
                   WHERE 1=1';
        $params = [];

        if (!empty($filters['house_id'])) {
            $sql     .= ' AND p.house_id = ?';
            $params[] = $filters['house_id'];
        }
        if (!empty($filters['date_from'])) {
            $sql     .= ' AND p.date >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql     .= ' AND p.date <= ?';
            $params[] = $filters['date_to'];
        }

        $sql .= ' ORDER BY p.date DESC, p.id DESC';

        if (!empty($filters['limit'])) {
            $sql     .= ' LIMIT ?';
            $params[] = (int)$filters['limit'];
        }

        return Db::all($sql, $params);
    }

    public static function findById(int $id): ?array
    {
        return Db::first('SELECT * FROM production_entries WHERE id = ?', [$id]);
    }

    public static function create(array $data): string
    {
        return Db::insert(
            'INSERT INTO production_entries
             (date, house_id, crates, loose_eggs, total_eggs,
              grade_large, grade_medium, grade_small, grade_pullet, grade_broken,
              feed_kg, feed_type, comments, user_id)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $data['date'],
                $data['house_id'],
                (int)$data['crates'],
                (int)($data['loose_eggs'] ?? 0),
                (int)$data['total_eggs'],
                (int)($data['grade_large']  ?? 0),
                (int)($data['grade_medium'] ?? 0),
                (int)($data['grade_small']  ?? 0),
                (int)($data['grade_pullet'] ?? 0),
                (int)($data['grade_broken'] ?? 0),
                (float)($data['feed_kg']    ?? 0),
                $data['feed_type'] ?? 'layer',
                $data['comments']  ?? '',
                (int)($data['user_id']      ?? 0),
            ]
        );
    }

    public static function delete(int $id): int
    {
        return Db::execute('DELETE FROM production_entries WHERE id = ?', [$id]);
    }

    /** Summary totals for today */
    public static function todaySummary(): array
    {
        $today = date('Y-m-d');
        $row   = Db::first(
            'SELECT COALESCE(SUM(total_eggs),0) AS total_eggs,
                    COALESCE(SUM(crates),0)      AS total_crates,
                    COALESCE(SUM(loose_eggs),0)  AS total_loose,
                    COALESCE(SUM(grade_large),0)  AS large,
                    COALESCE(SUM(grade_medium),0) AS medium,
                    COALESCE(SUM(grade_small),0)  AS small,
                    COALESCE(SUM(grade_pullet),0) AS pullet,
                    COALESCE(SUM(grade_broken),0) AS broken
             FROM production_entries WHERE date = ?',
            [$today]
        );
        return $row ?? [];
    }

    /** 7-day production data for chart */
    public static function weeklyChart(): array
    {
        return Db::all(
            'SELECT date, SUM(total_eggs) AS total
             FROM production_entries
             WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY date ORDER BY date ASC'
        );
    }
}
