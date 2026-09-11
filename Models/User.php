<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class User extends Model
{
    protected static string $table = 'users';
    protected static bool $softDeletes = true;

    public static function findByEmail(string $email): ?array
    {
        return static::firstWhere(['email' => $email]);
    }

    public static function withRole(int $id): ?array
    {
        return Database::fetchOne(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = :id AND u.deleted_at IS NULL',
            ['id' => $id]
        );
    }

    public static function permissions(int $userId): array
    {
        $rows = Database::fetchAll(
            'SELECT p.slug
             FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.id
             JOIN users u ON u.role_id = rp.role_id
             WHERE u.id = :id',
            ['id' => $userId]
        );

        return array_column($rows, 'slug');
    }
}
