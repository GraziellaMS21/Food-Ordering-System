<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/app/Core/helpers.php';

use App\Core\Router;

class RouteTest
{
    private int $passed = 0;
    private int $failed = 0;

    public function assert(bool $cond, string $msg): void
    {
        if ($cond) {
            $this->passed++;
            echo " [PASS] {$msg}\n";
        } else {
            $this->failed++;
            echo " [FAIL] {$msg}\n";
        }
    }

    private function dispatchRoute(string $method, string $uri, array $sessionData = []): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $_SESSION = $sessionData;
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;

        $router = new Router();
        require dirname(__DIR__) . '/routes/web.php';

        ob_start();
        $router->dispatch($method, $uri);
        $output = ob_get_clean();

        $code = http_response_code();

        return [
            'status' => $code ?: 200,
            'body' => $output,
        ];
    }

    public function run(): void
    {
        echo "=== HTTP ROUTE AND DISPATCH VERIFICATION ===\n\n";

        // 1. Home page
        $res = $this->dispatchRoute('GET', '/');
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Meal') && str_contains($res['body'], 'Deal'), "GET / loads homepage with brand content");

        // 2. Menu page
        $res = $this->dispatchRoute('GET', '/menu');
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Menu'), "GET /menu loads menu browsing view");

        // 3. Menu item detail
        $res = $this->dispatchRoute('GET', '/menu/1');
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Chicken Adobo'), "GET /menu/1 displays specific product");

        // 4. Cart page (empty state)
        $res = $this->dispatchRoute('GET', '/cart');
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Your cart is empty'), "GET /cart displays empty cart view");

        // 5. Track page
        $res = $this->dispatchRoute('GET', '/orders/track');
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Track Your Order'), "GET /orders/track displays lookup form");

        // 6. Staff login page
        $res = $this->dispatchRoute('GET', '/staff/login');
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Staff Portal Sign In'), "GET /staff/login displays login form");

        // 7. Unknown route returns 404
        $res = $this->dispatchRoute('GET', '/unknown/route/path');
        $this->assert(str_contains($res['body'], '404 - Page not found'), "GET /unknown/route/path returns 404 Page not found");

        // 8. Admin route with authenticated staff session
        $res = $this->dispatchRoute('GET', '/admin', [
            'staff_user' => ['id' => 1, 'name' => 'System Admin', 'username' => 'admin', 'role' => 'admin']
        ]);
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Staff Overview'), "GET /admin loads dashboard when authenticated");

        // 9. Admin orders page with authenticated staff
        $res = $this->dispatchRoute('GET', '/admin/orders', [
            'staff_user' => ['id' => 1, 'name' => 'System Admin', 'username' => 'admin', 'role' => 'admin']
        ]);
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Order Management'), "GET /admin/orders loads orders list when authenticated");

        // 10. Admin products page with authenticated staff
        $res = $this->dispatchRoute('GET', '/admin/products', [
            'staff_user' => ['id' => 1, 'name' => 'System Admin', 'username' => 'admin', 'role' => 'admin']
        ]);
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Products Management'), "GET /admin/products loads products list when authenticated");

        // 11. Admin categories page with authenticated staff
        $res = $this->dispatchRoute('GET', '/admin/categories', [
            'staff_user' => ['id' => 1, 'name' => 'System Admin', 'username' => 'admin', 'role' => 'admin']
        ]);
        $this->assert($res['status'] === 200 && str_contains($res['body'], 'Menu Categories'), "GET /admin/categories loads categories when authenticated");

        echo "\n==============================================================\n";
        echo "ROUTE TEST RESULTS: {$this->passed} Passed, {$this->failed} Failed\n";
        echo "==============================================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }
}

$routeTest = new RouteTest();
$routeTest->run();
