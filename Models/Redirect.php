<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class Redirect extends Model
{
    protected static string $table = 'redirects';

    public static function findByPath(string $path): ?array
    {
        return static::firstWhere(['from_path' => $path]);
    }

    public static function recordHit(int $id): void
    {
        Database::query('UPDATE redirects SET hits = hits + 1 WHERE id = :id', ['id' => $id]);
    }
}
