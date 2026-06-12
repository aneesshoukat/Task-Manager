<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public static function all(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM " . static::$table . " ORDER BY id DESC";
        return $instance->db->fetchAll($sql);
    }

    public static function find(int|string $id): ?array
    {
        $instance = new static();
        $sql = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?";
        return $instance->db->fetch($sql, [$id]);
    }

    public static function where(string $column, mixed $value): array
    {
        $instance = new static();
        $sql = "SELECT * FROM " . static::$table . " WHERE {$column} = ? ORDER BY id DESC";
        return $instance->db->fetchAll($sql, [$value]);
    }

    public static function whereFirst(string $column, mixed $value): ?array
    {
        $instance = new static();
        $sql = "SELECT * FROM " . static::$table . " WHERE {$column} = ? LIMIT 1";
        return $instance->db->fetch($sql, [$value]);
    }

    public static function create(array $data): int
    {
        $instance = new static();
        return $instance->db->insert(static::$table, $data);
    }

    public static function update(array $data, string $where, array $whereParams = []): int
    {
        $instance = new static();
        return $instance->db->update(static::$table, $data, $where, $whereParams);
    }

    public static function delete(string $where, array $params = []): int
    {
        $instance = new static();
        return $instance->db->delete(static::$table, $where, $params);
    }

    public static function count(string $where = '', array $params = []): int
    {
        $instance = new static();
        $sql = "SELECT COUNT(*) as count FROM " . static::$table;
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $result = $instance->db->fetch($sql, $params);
        return (int) ($result['count'] ?? 0);
    }

    public static function paginate(int $page = 1, int $limit = 20, string $where = '', array $params = [], string $orderBy = 'id DESC'): array
    {
        $instance = new static();
        $offset = ($page - 1) * $limit;

        $countSql = "SELECT COUNT(*) as count FROM " . static::$table;
        $selectSql = "SELECT * FROM " . static::$table;

        if ($where) {
            $countSql .= " WHERE {$where}";
            $selectSql .= " WHERE {$where}";
        }

        $selectSql .= " ORDER BY {$orderBy} LIMIT ? OFFSET ?";

        $total = (int) $instance->db->fetch($countSql, $params)['count'];
        $items = $instance->db->fetchAll($selectSql, [...$params, $limit, $offset]);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => (int) ceil($total / $limit),
        ];
    }
}
