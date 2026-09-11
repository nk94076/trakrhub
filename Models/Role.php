<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class Role extends Model
{
    protected static string $table = 'roles';

    public static function withUserCounts(): array
    {
        return Database::fetchAll(
            'SELECT r.*, COUNT(u.id) AS user_count
             FROM roles r
             LEFT JOIN users u ON u.role_id = r.id AND u.deleted_at IS NULL
             GROUP BY r.id
             ORDER BY r.id ASC'
        );
    }

    public static function permissionIds(int $roleId): array
    {
        $rows = Database::fetchAll(
            'SELECT permission_id FROM role_permissions WHERE role_id = :role_id',
            ['role_id' => $roleId]
        );

        return array_map(static fn ($r) => (int) $r['permission_id'], $rows);
    }

    public static function syncPermissions(int $roleId, array $permissionIds): void
    {
        Database::query('DELETE FROM role_permissions WHERE role_id = :role_id', ['role_id' => $roleId]);

        foreach (array_unique(array_map('intval', $permissionIds)) as $permissionId) {
            Database::query(
                'INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)',
                ['role_id' => $roleId, 'permission_id' => $permissionId]
            );
        }
    }
}
