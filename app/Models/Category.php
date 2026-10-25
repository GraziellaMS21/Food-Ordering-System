<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Category
{
    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('
            SELECT c.*, COUNT(p.id) AS products_count
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            GROUP BY c.id
            ORDER BY c.name ASC
        ');

        return $stmt->fetchAll();
    }

    public static function find(int|string $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $category = $stmt->fetch();

        return $category ?: null;
    }

    public static function findByName(string $name): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $name]);
        $category = $stmt->fetch();

        return $category ?: null;
    }

    public static function create(string $name, ?string $description = null): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            INSERT INTO categories (name, description)
            VALUES (:name, :description)
        ');
        $stmt->execute([
            'name' => trim($name),
            'description' => $description !== null ? trim($description) : null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int|string $id, string $name, ?string $description = null): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            UPDATE categories
            SET name = :name, description = :description
            WHERE id = :id
        ');

        return $stmt->execute([
            'id' => $id,
            'name' => trim($name),
            'description' => $description !== null ? trim($description) : null,
        ]);
    }

    public static function delete(int|string $id): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
