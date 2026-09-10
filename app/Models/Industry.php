<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Industry extends Model
{
    protected static string $table = 'industries';
    protected static bool $softDeletes = true;

    public static function published(): array
    {
        return static::where(['status' => 'published'], 'sort_order ASC');
    }
}
