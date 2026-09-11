<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Permission extends Model
{
    protected static string $table = 'permissions';

    /** All permissions grouped by their `group` column, for checkbox-list rendering. */
    public static function grouped(): array
    {
        $grouped = [];

        foreach (static::all('`group` ASC, id ASC') as $permission) {
            $grouped[$permission['group']][] = $permission;
        }

        return $grouped;
    }
}
