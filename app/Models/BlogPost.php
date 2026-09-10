<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class BlogPost extends Model
{
    protected static string $table = 'blog_posts';
    protected static bool $softDeletes = true;

    public static function recentPublished(int $limit = 3): array
    {
        return Database::fetchAll(
            "SELECT * FROM blog_posts
             WHERE status = 'published' AND published_at <= NOW() AND deleted_at IS NULL
             ORDER BY published_at DESC
             LIMIT " . max(1, $limit)
        );
    }
}
