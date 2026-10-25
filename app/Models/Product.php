<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Product
{
    public static function all(?int $categoryId = null): array
    {
        $pdo = Database::connection();
        $sql = '
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
        ';
        $params = [];

        if ($categoryId !== null && $categoryId > 0) {
            $sql .= ' WHERE p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        $sql .= ' ORDER BY c.name ASC, p.name ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function available(?int $categoryId = null): array
    {
        $pdo = Database::connection();
        $sql = '
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.is_available = 1
        ';
        $params = [];

        if ($categoryId !== null && $categoryId > 0) {
            $sql .= ' AND p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        $sql .= ' ORDER BY c.name ASC, p.name ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int|string $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        return $product ?: null;
    }

    public static function findAvailable(int|string $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.id = :id AND p.is_available = 1
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        return $product ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            INSERT INTO products (category_id, name, description, price, image, is_available)
            VALUES (:category_id, :name, :description, :price, :image, :is_available)
        ');
        $stmt->execute([
            'category_id' => !empty($data['category_id']) ? (int) $data['category_id'] : null,
            'name' => trim($data['name']),
            'description' => !empty($data['description']) ? trim($data['description']) : null,
            'price' => (float) $data['price'],
            'image' => !empty($data['image']) ? trim($data['image']) : null,
            'is_available' => isset($data['is_available']) ? (int) (bool) $data['is_available'] : 1,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int|string $id, array $data): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            UPDATE products
            SET category_id = :category_id,
                name = :name,
                description = :description,
                price = :price,
                image = :image,
                is_available = :is_available
            WHERE id = :id
        ');

        return $stmt->execute([
            'id' => $id,
            'category_id' => !empty($data['category_id']) ? (int) $data['category_id'] : null,
            'name' => trim($data['name']),
            'description' => !empty($data['description']) ? trim($data['description']) : null,
            'price' => (float) $data['price'],
            'image' => !empty($data['image']) ? trim($data['image']) : null,
            'is_available' => isset($data['is_available']) ? (int) (bool) $data['is_available'] : 1,
        ]);
    }

    public static function delete(int|string $id): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function toggleAvailability(int|string $id): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            UPDATE products
            SET is_available = CASE WHEN is_available = 1 THEN 0 ELSE 1 END
            WHERE id = :id
        ');
        return $stmt->execute(['id' => $id]);
    }
}
