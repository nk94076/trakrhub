<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Service extends Model
{
    protected static string $table = 'services';
    protected static bool $softDeletes = true;

    public static function published(): array
    {
        return static::where(['status' => 'published'], 'sort_order ASC');
    }

    public static function findBySlug(string $slug): ?array
    {
        return static::firstWhere(['slug' => $slug, 'status' => 'published']);
    }
}
