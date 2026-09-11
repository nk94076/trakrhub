<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class Setting extends Model
{
    protected static string $table = 'settings';

    /** All settings as a flat [group][key] => value map, cached per-request. */
    private static ?array $cache = null;

    public static function all(string $orderBy = 'id DESC'): array
    {
        if (self::$cache === null) {
            self::$cache = [];

            foreach (Database::fetchAll('SELECT * FROM settings') as $row) {
                self::$cache[$row['group']][$row['key']] = $row['value'];
            }
        }

        return self::$cache;
    }

    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        $all = self::all();

        return $all[$group][$key] ?? $default;
    }

    public static function set(string $group, string $key, mixed $value, string $type = 'text'): void
    {
        Database::query(
            'INSERT INTO settings (`group`, `key`, `value`, type)
             VALUES (:group, :key, :value, :type)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), type = VALUES(type)',
            ['group' => $group, 'key' => $key, 'value' => $value, 'type' => $type]
        );

        self::$cache = null;
    }
}
