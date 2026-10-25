<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * List all customer orders with filtering.
     */
    public function index(): void
    {
        AuthMiddleware::check();

        $status = !empty($_GET['status']) ? trim($_GET['status']) : null;
        $paymentStatus = !empty($_GET['payment_status']) ? trim($_GET['payment_status']) : null;
        $search = !empty($_GET['search']) ? trim($_GET['search']) : null;

        $orders = Order::all($status, $paymentStatus, $search);

        $this->render('admin/orders/index', [
            'title' => 'Order Management',
            'orders' => $orders,
            'selectedStatus' => $status,
            'selectedPaymentStatus' => $paymentStatus,
            'search' => $search,
        ], 'layouts/admin');
    }

    /**
     * View detailed order breakdown.
     */
    public function show(string $id): void
    {
        AuthMiddleware::check();

        $orderId = (int) $id;
        $order = Order::findWithItems($orderId);

        if (!$order) {
            $this->setFlash('error', 'Order not found.');
            $this->redirect('/admin/orders');
            return;
        }

        $currentStatus = $order['order_status'];
        $allowedNextStatuses = Order::VALID_TRANSITIONS[$currentStatus] ?? [];

        $this->render('admin/orders/show', [
            'title' => 'Order ' . $order['order_number'],
            'order' => $order,
            'allowedNextStatuses' => $allowedNextStatuses,
        ], 'layouts/admin');
    }

    /**
     * Update order status with transition validation.
     */
    public function updateStatus(): void
    {
        AuthMiddleware::check();
        $this->validateCsrf();

        $orderId = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
        $newStatus = trim($_POST['status'] ?? '');

        if ($orderId <= 0 || empty($newStatus)) {
            $this->setFlash('error', 'Invalid order or status.');
            $this->redirect('/admin/orders');
            return;
        }

        $updated = Order::updateStatus($orderId, $newStatus);

        if ($updated) {
            $this->setFlash('success', "Order status successfully updated to \"" . ucwords(str_replace('_', ' ', $newStatus)) . "\".");
        } else {
            $this->setFlash('error', 'Invalid status transition requested.');
        }

        $this->redirect("/admin/orders/{$orderId}");
    }

    /**
     * Record cash payment received at counter.
     */
    public function markPaid(): void
    {
        AuthMiddleware::check();
        $this->validateCsrf();

        $orderId = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;

        if ($orderId <= 0) {
            $this->setFlash('error', 'Invalid order ID.');
            $this->redirect('/admin/orders');
            return;
        }

        Order::markPaid($orderId);

        $this->setFlash('success', 'Cash payment recorded successfully. Order is now marked as Paid.');
        $this->redirect("/admin/orders/{$orderId}");
    }
}
