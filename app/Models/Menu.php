<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class Menu extends Model
{
    protected static string $table = 'menus';

    public static function itemsForLocation(string $location): array
    {
        return Database::fetchAll(
            'SELECT mi.*
             FROM menu_items mi
             JOIN menus m ON m.id = mi.menu_id
             WHERE m.location = :location
               AND mi.parent_id IS NULL
               AND mi.status = "published"
             ORDER BY mi.sort_order ASC',
            ['location' => $location]
        );
    }
}
