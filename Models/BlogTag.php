<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class BlogTag extends Model
{
    protected static string $table = 'blog_tags';

    public static function findOrCreateByName(string $name): int
    {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name) ?? '', '-'));
        $existing = static::firstWhere(['slug' => $slug]);

        if ($existing !== null) {
            return (int) $existing['id'];
        }

        return static::create(['name' => trim($name), 'slug' => $slug]);
    }
}
