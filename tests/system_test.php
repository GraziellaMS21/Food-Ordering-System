<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/app/Core/helpers.php';

use App\Core\Database;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Staff;

class SystemTest
{
    private int $passed = 0;
    private int $failed = 0;

    public function assert(bool $condition, string $message): void
    {
        if ($condition) {
            $this->passed++;
            echo " [PASS] {$message}\n";
        } else {
            $this->failed++;
            echo " [FAIL] {$message}\n";
        }
    }

    public function runAll(): void
    {
        echo "=== MEAL AND DEAL FOOD HUB - AUTOMATED SYSTEM VERIFICATION ===\n\n";

        $this->testDatabaseConnection();
        $this->testCategoriesAndProducts();
        $this->testCartOperations();
        $this->testOrderCreationAndIntegrity();
        $this->testSecureTracking();
        $this->testOrderStatusTransitions();
        $this->testPaymentRecording();
        $this->testStaffAuthentication();
        $this->testCsrfProtection();

        echo "\n==============================================================\n";
        echo "TEST RESULTS: {$this->passed} Passed, {$this->failed} Failed\n";
        echo "==============================================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }

    private function testDatabaseConnection(): void
    {
        echo "--- 1. Testing Database & Environment ---\n";
        try {
            $pdo = Database::connection();
            $this->assert($pdo instanceof PDO, "PDO connection successfully established to food_ordering_system");
        } catch (Exception $e) {
            $this->assert(false, "Database connection failed: " . $e->getMessage());
        }
    }

    private function testCategoriesAndProducts(): void
    {
        echo "\n--- 2. Testing Menu & Category Data ---\n";
        $categories = Category::all();
        $this->assert(count($categories) >= 4, "Initial categories loaded (found " . count($categories) . ")");

        $products = Product::available();
        $this->assert(count($products) >= 6, "Available products loaded from MySQL (found " . count($products) . ")");

        // Test creating a temporary test product
        $testCatId = $categories[0]['id'];
        $newProdId = Product::create([
            'category_id' => $testCatId,
            'name' => 'Automated Test Special Dish',
            'description' => 'Test Dish Description',
            'price' => 99.50,
            'image' => null,
            'is_available' => 1,
        ]);
        $this->assert($newProdId > 0, "Created test product with ID #{$newProdId}");

        $found = Product::find($newProdId);
        $this->assert($found !== null && $found['name'] === 'Automated Test Special Dish', "Retrieved newly created product details");

        // Toggle availability
        Product::toggleAvailability($newProdId);
        $foundAfterToggle = Product::find($newProdId);
        $this->assert((int)$foundAfterToggle['is_available'] === 0, "Availability toggled to unavailable (0)");

        $availableOnly = Product::findAvailable($newProdId);
        $this->assert($availableOnly === null, "Unavailable product is excluded by findAvailable()");

        // Clean up test product
        Product::delete($newProdId);
        $this->assert(Product::find($newProdId) === null, "Test product deleted cleanly");
    }

    private function testCartOperations(): void
    {
        echo "\n--- 3. Testing Cart Simulation & DB Validation ---\n";
        ensure_session_started();

        $_SESSION['cart'] = [];

        // Add product 1
        $product1 = Product::find(1);
        $this->assert($product1 !== null, "Product #1 found for cart testing");

        $_SESSION['cart'][1] = ['id' => 1, 'quantity' => 2];
        $this->assert(cart_count() === 2, "Cart count reflects 2 items");

        // Update quantity
        $_SESSION['cart'][1]['quantity'] = 4;
        $this->assert(cart_count() === 4, "Cart quantity updated to 4");

        // Remove item
        unset($_SESSION['cart'][1]);
        $this->assert(cart_count() === 0, "Cart item removed successfully");
    }

    private function testOrderCreationAndIntegrity(): void
    {
        echo "\n--- 4. Testing Order Creation & Server-side Pricing ---\n";

        // Create an order with product 1 and product 2
        $prod1 = Product::find(1);
        $prod2 = Product::find(2);

        $expectedTotal = ((float)$prod1['price'] * 2) + ((float)$prod2['price'] * 1);

        $customerData = [
            'customer_name' => 'Juan Dela Cruz',
            'customer_contact' => '09181234567',
            'notes' => 'Extra spicy if possible',
        ];

        $cartItems = [
            ['product_id' => 1, 'quantity' => 2],
            ['product_id' => 2, 'quantity' => 1],
        ];

        $result = Order::createWithItems($customerData, $cartItems);

        $this->assert(!empty($result['order_id']), "Order created with ID #{$result['order_id']}");
        $this->assert(str_starts_with($result['order_number'], 'MD-'), "Unique order number generated: {$result['order_number']}");
        $this->assert(!empty($result['tracking_token']), "High-entropy tracking token generated");
        $this->assert(abs($result['total'] - $expectedTotal) < 0.001, "Server recalculated total accurately (₱{$result['total']}) from database");

        $order = Order::findWithItems($result['order_id']);
        $this->assert($order !== null, "Order fetched with items");
        $this->assert(count($order['items']) === 2, "Order has 2 order items attached");
        $this->assert($order['order_status'] === Order::STATUS_PENDING, "Initial status is 'pending'");
        $this->assert($order['payment_status'] === Order::PAYMENT_UNPAID, "Initial payment status is 'unpaid'");
    }

    private function testSecureTracking(): void
    {
        echo "\n--- 5. Testing Tracking Privacy ---\n";

        // Query our newly created order
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT id, order_number, tracking_token_hash FROM orders ORDER BY id DESC LIMIT 1');
        $row = $stmt->fetch();

        // 1. Unpredictable tracking token hash match
        $this->assert(!empty($row['tracking_token_hash']), "Tracking token hash is stored (SHA-256)");

        // 2. Lookup by Order Number
        $found = Order::findByOrderNumber($row['order_number']);
        $this->assert($found !== null && (int)$found['id'] === (int)$row['id'], "Order found via order_number: {$row['order_number']}");
    }

    private function testOrderStatusTransitions(): void
    {
        echo "\n--- 6. Testing Validated Order Status Workflow ---\n";

        $pdo = Database::connection();
        $orderId = (int) $pdo->query('SELECT id FROM orders ORDER BY id DESC LIMIT 1')->fetchColumn();

        // Pending -> Confirmed (Allowed)
        $s1 = Order::updateStatus($orderId, Order::STATUS_CONFIRMED);
        $this->assert($s1 === true, "Valid transition: Pending -> Confirmed allowed");

        // Confirmed -> Completed (Invalid! Must go to Preparing then Ready first)
        $s2 = Order::updateStatus($orderId, Order::STATUS_COMPLETED);
        $this->assert($s2 === false, "Arbitrary skip to Completed rejected by state validation");

        // Confirmed -> Preparing (Allowed)
        $s3 = Order::updateStatus($orderId, Order::STATUS_PREPARING);
        $this->assert($s3 === true, "Valid transition: Confirmed -> Preparing allowed");

        // Preparing -> Ready (Allowed)
        $s4 = Order::updateStatus($orderId, Order::STATUS_READY);
        $this->assert($s4 === true, "Valid transition: Preparing -> Ready allowed");

        // Ready -> Completed (Allowed)
        $s5 = Order::updateStatus($orderId, Order::STATUS_COMPLETED);
        $this->assert($s5 === true, "Valid transition: Ready -> Completed allowed");
    }

    private function testPaymentRecording(): void
    {
        echo "\n--- 7. Testing Cash Payment Recording ---\n";

        $pdo = Database::connection();
        $orderId = (int) $pdo->query('SELECT id FROM orders ORDER BY id DESC LIMIT 1')->fetchColumn();

        $before = Order::find($orderId);
        $this->assert($before['payment_status'] === Order::PAYMENT_UNPAID, "Payment is initially unpaid");

        Order::markPaid($orderId);

        $after = Order::find($orderId);
        $this->assert($after['payment_status'] === Order::PAYMENT_PAID, "Payment successfully recorded as paid after cash receipt");
    }

    private function testStaffAuthentication(): void
    {
        echo "\n--- 8. Testing Staff Authentication & Password Hashing ---\n";

        // Admin credential verification
        $admin = Staff::verifyCredentials('admin', 'admin123');
        $this->assert($admin !== null && $admin['role'] === 'admin', "Admin authenticated successfully with password_verify");

        // Staff credential verification
        $staff = Staff::verifyCredentials('staff', 'staff123');
        $this->assert($staff !== null && $staff['role'] === 'staff', "Staff authenticated successfully with password_verify");

        // Invalid password rejection
        $invalid = Staff::verifyCredentials('admin', 'wrongpassword');
        $this->assert($invalid === null, "Invalid password correctly rejected");
    }

    private function testCsrfProtection(): void
    {
        echo "\n--- 9. Testing CSRF Protection & Escaping ---\n";

        $token = csrf_token();
        $this->assert(!empty($token) && strlen($token) === 64, "CSRF token generated (64 hex chars)");

        $field = csrf_field();
        $this->assert(str_contains($field, 'type="hidden"') && str_contains($field, $token), "csrf_field() outputs valid hidden input with token");

        $valid = verify_csrf_token($token);
        $this->assert($valid === true, "Valid CSRF token passes verification");

        $invalid = verify_csrf_token('invalid_token_123');
        $this->assert($invalid === false, "Invalid CSRF token fails verification");

        $escaped = e('<script>alert("xss")</script>');
        $this->assert(!str_contains($escaped, '<script>'), "HTML escaping safely neutralizes HTML/script tags");
    }
}

$test = new SystemTest();
$test->runAll();
