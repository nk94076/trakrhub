<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimal ActiveRecord-ish base model over PDO prepared statements.
 * Concrete models set $table (and optionally $softDeletes) and inherit
 * find/all/where/create/update/delete.
 */
abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static bool $softDeletes = false;

    public static function find(int $id): ?array
    {
        $sql = 'SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id';

        if (static::$softDeletes) {
            $sql .= ' AND deleted_at IS NULL';
        }

        return Database::fetchOne($sql, ['id' => $id]);
    }

    public static function findTrashed(int $id): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id',
            ['id' => $id]
        );
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        $sql = 'SELECT * FROM ' . static::$table;

        if (static::$softDeletes) {
            $sql .= ' WHERE deleted_at IS NULL';
        }

        $sql .= ' ORDER BY ' . $orderBy;

        return Database::fetchAll($sql);
    }

    public static function paginate(int $page = 1, int $perPage = 20, array $where = [], string $orderBy = 'id DESC'): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        [$whereSql, $params] = static::buildWhere($where);

        $countRow = Database::fetchOne(
            'SELECT COUNT(*) AS total FROM ' . static::$table . $whereSql,
            $params
        );
        $total = (int) ($countRow['total'] ?? 0);

        $rows = Database::fetchAll(
            'SELECT * FROM ' . static::$table . $whereSql . ' ORDER BY ' . $orderBy . ' LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params
        );

        return [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    protected static function buildWhere(array $where): array
    {
        $conditions = [];
        $params = [];

        if (static::$softDeletes && !array_key_exists('deleted_at', $where)) {
            $conditions[] = 'deleted_at IS NULL';
        }

        foreach ($where as $column => $value) {
            if ($column === 'deleted_at' && $value === null) {
                $conditions[] = 'deleted_at IS NULL';
                continue;
            }

            $conditions[] = "`{$column}` = :{$column}";
            $params[$column] = $value;
        }

        $sql = $conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions);

        return [$sql, $params];
    }

    public static function where(array $conditions, string $orderBy = 'id DESC'): array
    {
        [$whereSql, $params] = static::buildWhere($conditions);

        return Database::fetchAll(
            'SELECT * FROM ' . static::$table . $whereSql . ' ORDER BY ' . $orderBy,
            $params
        );
    }

    public static function firstWhere(array $conditions): ?array
    {
        [$whereSql, $params] = static::buildWhere($conditions);

        return Database::fetchOne('SELECT * FROM ' . static::$table . $whereSql . ' LIMIT 1', $params);
    }

    public static function create(array $data): int
    {
        $columns = array_keys($data);
        $quotedColumns = array_map(static fn ($c) => "`{$c}`", $columns);
        $placeholders = array_map(static fn ($c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::$table,
            implode(', ', $quotedColumns),
            implode(', ', $placeholders)
        );

        Database::query($sql, $data);

        return (int) Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $assignments = implode(', ', array_map(static fn ($c) => "`{$c}` = :{$c}", array_keys($data)));
        $data['id'] = $id;

        $sql = 'UPDATE ' . static::$table . ' SET ' . $assignments . ' WHERE ' . static::$primaryKey . ' = :id';

        return Database::query($sql, $data)->rowCount() >= 0;
    }

    public static function delete(int $id): bool
    {
        if (static::$softDeletes) {
            return static::update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        }

        $sql = 'DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id';

        return Database::query($sql, ['id' => $id])->rowCount() > 0;
    }

    public static function restore(int $id): bool
    {
        return static::update($id, ['deleted_at' => null]);
    }

    public static function forceDelete(int $id): bool
    {
        $sql = 'DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id';

        return Database::query($sql, ['id' => $id])->rowCount() > 0;
    }

    public static function count(array $where = []): int
    {
        [$whereSql, $params] = static::buildWhere($where);
        $row = Database::fetchOne('SELECT COUNT(*) AS total FROM ' . static::$table . $whereSql, $params);

        return (int) ($row['total'] ?? 0);
    }
}
