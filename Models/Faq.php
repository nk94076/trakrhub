<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Faq extends Model
{
    protected static string $table = 'faqs';

    public static function byGroup(string $group): array
    {
        return static::where(['status' => 'published', 'group' => $group], 'sort_order ASC');
    }
}
