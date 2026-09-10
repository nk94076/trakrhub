<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class CaseStudy extends Model
{
    protected static string $table = 'case_studies';
    protected static bool $softDeletes = true;

    public static function published(string $orderBy = 'sort_order ASC'): array
    {
        return static::where(['status' => 'published'], $orderBy);
    }

    public static function findBySlug(string $slug): ?array
    {
        return static::firstWhere(['slug' => $slug, 'status' => 'published']);
    }
}
