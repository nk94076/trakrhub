<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class Page extends Model
{
    protected static string $table = 'pages';
    protected static bool $softDeletes = true;

    public static function findBySlug(string $slug): ?array
    {
        return static::firstWhere(['slug' => $slug, 'status' => 'published']);
    }

    public static function sectionsFor(int $pageId): array
    {
        $rows = Database::fetchAll(
            'SELECT * FROM page_sections WHERE page_id = :page_id AND status = "published" ORDER BY sort_order ASC',
            ['page_id' => $pageId]
        );

        foreach ($rows as &$row) {
            $row['content'] = json_decode((string) $row['content'], true) ?: [];
        }

        return $rows;
    }
}
