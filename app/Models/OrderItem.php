<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class OrderItem
{
    /**
     * Create an order item using an existing PDO connection (supports transactions).
     */
    public static function create(
        PDO $pdo,
        int $orderId,
        ?int $productId,
        string $productName,
        float $unitPrice,
        int $quantity
    ): int {
        $stmt = $pdo->prepare('
            INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity)
            VALUES (:order_id, :product_id, :product_name, :unit_price, :quantity)
        ');

        $stmt->execute([
            'order_id' => $orderId,
            'product_id' => $productId,
            'product_name' => $productName,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Get all items belonging to an order.
     */
    public static function getByOrderId(int|string $orderId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            SELECT oi.*, p.image AS current_product_image
            FROM order_items oi
            LEFT JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = :order_id
            ORDER BY oi.id ASC
        ');
        $stmt->execute(['order_id' => $orderId]);

        return $stmt->fetchAll();
    }
}
