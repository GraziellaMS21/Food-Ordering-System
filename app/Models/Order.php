<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class Order
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PAID = 'paid';

    /**
     * Allowed status transitions.
     */
    public const VALID_TRANSITIONS = [
        self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
        self::STATUS_CONFIRMED => [self::STATUS_PREPARING, self::STATUS_CANCELLED],
        self::STATUS_PREPARING => [self::STATUS_READY, self::STATUS_CANCELLED],
        self::STATUS_READY => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
        self::STATUS_COMPLETED => [],
        self::STATUS_CANCELLED => [],
    ];

    /**
     * Generate a unique human-readable order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'MD-' . date('Ymd');
        $random = strtoupper(bin2hex(random_bytes(2))); // 4 hex chars
        return "{$prefix}-{$random}";
    }

    /**
     * Generate an unpredictable tracking token and its SHA-256 hash.
     *
     * @return array{token: string, hash: string}
     */
    public static function generateTrackingToken(): array
    {
        $token = bin2hex(random_bytes(32)); // 64 hex characters
        $hash = hash('sha256', $token);
        return ['token' => $token, 'hash' => $hash];
    }

    /**
     * Create an order and its items in a single atomic transaction.
     *
     * @param array $customerData ['name' => ..., 'contact' => ..., 'notes' => ...]
     * @param array $cartItems array of ['product_id' => int, 'quantity' => int]
     * @return array{order_id: int, order_number: string, tracking_token: string, total: float}
     * @throws Exception
     */
    public static function createWithItems(array $customerData, array $cartItems): array
    {
        if (empty($cartItems)) {
            throw new Exception('Cart is empty.');
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            // 1. Validate items and compute fresh total from database prices
            $totalAmount = 0.0;
            $validatedItems = [];

            foreach ($cartItems as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new Exception('Invalid product or quantity in cart.');
                }

                // Query database directly for current price and availability
                $stmt = $pdo->prepare('SELECT id, name, price, is_available FROM products WHERE id = :id LIMIT 1');
                $stmt->execute(['id' => $productId]);
                $product = $stmt->fetch();

                if (!$product) {
                    throw new Exception("Product #{$productId} is no longer available.");
                }

                if ((int) $product['is_available'] !== 1) {
                    throw new Exception("Product \"{$product['name']}\" is currently sold out or unavailable.");
                }

                $unitPrice = (float) $product['price'];
                $subtotal = $unitPrice * $quantity;
                $totalAmount += $subtotal;

                $validatedItems[] = [
                    'product_id' => (int) $product['id'],
                    'product_name' => $product['name'],
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                ];
            }

            if (empty($validatedItems)) {
                throw new Exception('No valid items to order.');
            }

            // 2. Generate unique order number and secure tracking token
            $orderNumber = self::generateOrderNumber();
            $tokenData = self::generateTrackingToken();

            // 3. Insert order record
            $orderStmt = $pdo->prepare('
                INSERT INTO orders (
                    order_number, tracking_token_hash, customer_name,
                    customer_contact, notes, total_amount,
                    order_status, payment_method, payment_status
                ) VALUES (
                    :order_number, :tracking_token_hash, :customer_name,
                    :customer_contact, :notes, :total_amount,
                    :order_status, :payment_method, :payment_status
                )
            ');

            $orderStmt->execute([
                'order_number' => $orderNumber,
                'tracking_token_hash' => $tokenData['hash'],
                'customer_name' => trim($customerData['customer_name'] ?? ''),
                'customer_contact' => !empty($customerData['customer_contact']) ? trim($customerData['customer_contact']) : null,
                'notes' => !empty($customerData['notes']) ? trim($customerData['notes']) : null,
                'total_amount' => $totalAmount,
                'order_status' => self::STATUS_PENDING,
                'payment_method' => 'cash',
                'payment_status' => self::PAYMENT_UNPAID,
            ]);

            $orderId = (int) $pdo->lastInsertId();

            // 4. Insert each order item
            foreach ($validatedItems as $item) {
                OrderItem::create(
                    $pdo,
                    $orderId,
                    $item['product_id'],
                    $item['product_name'],
                    $item['unit_price'],
                    $item['quantity']
                );
            }

            $pdo->commit();

            return [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'tracking_token' => $tokenData['token'],
                'total' => $totalAmount,
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Find order by secure tracking token.
     */
    public static function findByTrackingToken(string $token): ?array
    {
        $hash = hash('sha256', trim($token));
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE tracking_token_hash = :hash LIMIT 1');
        $stmt->execute(['hash' => $hash]);
        $order = $stmt->fetch();

        return $order ?: null;
    }

    /**
     * Find order by order number.
     */
    public static function findByOrderNumber(string $orderNumber): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE order_number = :order_number LIMIT 1');
        $stmt->execute(['order_number' => trim($orderNumber)]);
        $order = $stmt->fetch();

        return $order ?: null;
    }

    /**
     * Find order by ID.
     */
    public static function find(int|string $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();

        return $order ?: null;
    }

    /**
     * Find order with its items.
     */
    public static function findWithItems(int|string $id): ?array
    {
        $order = self::find($id);
        if (!$order) {
            return null;
        }

        $order['items'] = OrderItem::getByOrderId($order['id']);
        return $order;
    }

    /**
     * Get all orders with optional filtering.
     */
    public static function all(?string $status = null, ?string $paymentStatus = null, ?string $search = null): array
    {
        $pdo = Database::connection();
        $sql = 'SELECT * FROM orders WHERE 1=1';
        $params = [];

        if (!empty($status)) {
            $sql .= ' AND order_status = :status';
            $params['status'] = $status;
        }

        if (!empty($paymentStatus)) {
            $sql .= ' AND payment_status = :payment_status';
            $params['payment_status'] = $paymentStatus;
        }

        if (!empty($search)) {
            $sql .= ' AND (order_number LIKE :search OR customer_name LIKE :search OR customer_contact LIKE :search)';
            $params['search'] = '%' . trim($search) . '%';
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Update order status with transition validation.
     */
    public static function updateStatus(int|string $id, string $newStatus): bool
    {
        $order = self::find($id);
        if (!$order) {
            return false;
        }

        $currentStatus = $order['order_status'];

        // Check if transition is allowed
        $allowed = self::VALID_TRANSITIONS[$currentStatus] ?? [];
        if (!in_array($newStatus, $allowed, true) && $newStatus !== $currentStatus) {
            return false;
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE orders SET order_status = :status WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'status' => $newStatus,
        ]);
    }

    /**
     * Record cash payment.
     */
    public static function markPaid(int|string $id): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE orders SET payment_status = :status WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'status' => self::PAYMENT_PAID,
        ]);
    }

    /**
     * Summary statistics for the admin dashboard.
     */
    public static function getDashboardStats(): array
    {
        $pdo = Database::connection();

        $stats = [
            'pending' => 0,
            'preparing' => 0,
            'ready' => 0,
            'completed_today' => 0,
            'revenue_today' => 0.0,
            'total_orders_today' => 0,
        ];

        // Counts by status
        $stmt = $pdo->query('
            SELECT order_status, COUNT(*) as count
            FROM orders
            GROUP BY order_status
        ');
        foreach ($stmt->fetchAll() as $row) {
            if ($row['order_status'] === self::STATUS_PENDING) {
                $stats['pending'] = (int) $row['count'];
            } elseif ($row['order_status'] === self::STATUS_PREPARING) {
                $stats['preparing'] = (int) $row['count'];
            } elseif ($row['order_status'] === self::STATUS_READY) {
                $stats['ready'] = (int) $row['count'];
            }
        }

        // Today's cash collected and completed orders
        $stmtToday = $pdo->query('
            SELECT
                COUNT(*) as total_orders,
                SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as revenue,
                SUM(CASE WHEN order_status = "completed" THEN 1 ELSE 0 END) as completed
            FROM orders
            WHERE DATE(created_at) = CURDATE()
        ');
        $todayData = $stmtToday->fetch();
        if ($todayData) {
            $stats['total_orders_today'] = (int) ($todayData['total_orders'] ?? 0);
            $stats['revenue_today'] = (float) ($todayData['revenue'] ?? 0.0);
            $stats['completed_today'] = (int) ($todayData['completed'] ?? 0);
        }

        return $stats;
    }
}
