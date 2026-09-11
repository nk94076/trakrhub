<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class PageSection extends Model
{
    protected static string $table = 'page_sections';

    public static function forPageAdmin(int $pageId): array
    {
        return Database::fetchAll(
            'SELECT * FROM page_sections WHERE page_id = :page_id ORDER BY sort_order ASC',
            ['page_id' => $pageId]
        );
    }

    public static function nextSortOrder(int $pageId): int
    {
        $row = Database::fetchOne(
            'SELECT COALESCE(MAX(sort_order), 0) AS max_order FROM page_sections WHERE page_id = :page_id',
            ['page_id' => $pageId]
        );

        return ((int) ($row['max_order'] ?? 0)) + 1;
    }

    public static function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Database::query(
                'UPDATE page_sections SET sort_order = :sort_order WHERE id = :id',
                ['sort_order' => $index + 1, 'id' => (int) $id]
            );
        }
    }
}
