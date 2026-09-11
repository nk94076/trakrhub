<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class MenuItem extends Model
{
    protected static string $table = 'menu_items';

    public static function forMenuAdmin(int $menuId): array
    {
        return Database::fetchAll(
            'SELECT * FROM menu_items WHERE menu_id = :menu_id ORDER BY sort_order ASC',
            ['menu_id' => $menuId]
        );
    }

    public static function nextSortOrder(int $menuId): int
    {
        $row = Database::fetchOne(
            'SELECT COALESCE(MAX(sort_order), 0) AS max_order FROM menu_items WHERE menu_id = :menu_id',
            ['menu_id' => $menuId]
        );

        return ((int) ($row['max_order'] ?? 0)) + 1;
    }

    public static function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Database::query(
                'UPDATE menu_items SET sort_order = :sort_order WHERE id = :id',
                ['sort_order' => $index + 1, 'id' => (int) $id]
            );
        }
    }
}
