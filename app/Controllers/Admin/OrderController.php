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
        $sort = !empty($_GET['sort']) ? trim($_GET['sort']) : null;

        // Prioritize exact tracking code match: open directly
        if (!empty($search)) {
            $exact = Order::findByOrderNumber($search);
            if ($exact) {
                $this->redirect('/admin/orders/' . (int) $exact['id']);
                return;
            }
        }

        // Determine sort
        if ($sort === null) {
            $activeStatuses = ['', 'pending', 'confirmed', 'preparing', 'ready'];
            if ($status === null || in_array($status, $activeStatuses, true) || $paymentStatus === 'unpaid') {
                $sort = 'oldest';
            } else {
                $sort = 'newest';
            }
        }

        $orders = Order::all($status, $paymentStatus, $search, $sort);

        $this->render('admin/orders/index', [
            'title' => 'Order Management',
            'orders' => $orders,
            'selectedStatus' => $status,
            'selectedPaymentStatus' => $paymentStatus,
            'search' => $search,
            'sort' => $sort,
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
        // Remove confirmed (only via payment) and keep cancel
        $allowedNextStatuses = array_values(array_filter($allowedNextStatuses, function ($s) {
            return $s !== Order::STATUS_CONFIRMED;
        }));

        // Pay-first: if unpaid, only cancel allowed
        if ($order['payment_status'] !== Order::PAYMENT_PAID) {
            $allowedNextStatuses = array_values(array_filter($allowedNextStatuses, function ($s) {
                return $s === Order::STATUS_CANCELLED;
            }));
        }

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
        $expectedUpdatedAt = $_POST['expected_updated_at'] ?? null;

        if ($orderId <= 0 || empty($newStatus)) {
            $this->setFlash('error', 'Invalid order or status.');
            $this->redirect('/admin/orders');
            return;
        }

        $user = auth_user();
        $changedBy = $user['id'] ?? null;
        $changedByName = $user['name'] ?? ($user['username'] ?? null);

        $before = Order::find($orderId);
        $updated = Order::updateStatus($orderId, $newStatus, $changedBy, $changedByName, $expectedUpdatedAt);

        if ($updated) {
            $this->setFlash('success', "Order status successfully updated to \"" . ucwords(str_replace('_', ' ', $newStatus)) . "\".");
        } else {
            $after = Order::find($orderId);
            if ($expectedUpdatedAt !== null && $after && ($after['updated_at'] ?? '') !== $expectedUpdatedAt) {
                $this->setFlash('error', 'This order was updated by another staff member. Current state reloaded.');
            } elseif ($before && $before['payment_status'] !== Order::PAYMENT_PAID && in_array($newStatus, [Order::STATUS_PREPARING, Order::STATUS_READY, Order::STATUS_COMPLETED], true)) {
                $this->setFlash('error', 'Record payment first.');
            } else {
                $this->setFlash('error', 'Invalid status transition requested.');
            }
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
        $cashReceived = $_POST['cash_received'] ?? null;
        $expectedUpdatedAt = $_POST['expected_updated_at'] ?? null;

        if ($orderId <= 0) {
            $this->setFlash('error', 'Invalid order ID.');
            $this->redirect('/admin/orders');
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            $this->setFlash('error', 'Order not found.');
            $this->redirect('/admin/orders');
            return;
        }

        $total = (float) $order['total_amount'];

        // Validate cash received
        if ($cashReceived === null || $cashReceived === '' || !is_numeric($cashReceived)) {
            $this->setFlash('error', 'Please enter the cash received.');
            $this->redirect("/admin/orders/{$orderId}");
            return;
        }
        $cashReceived = (float) $cashReceived;
        if ($cashReceived < $total) {
            $this->setFlash('error', 'Cash received is less than the total amount. Insufficient cash.');
            $this->redirect("/admin/orders/{$orderId}");
            return;
        }

        // Duplicate / cancelled checks
        if ($order['payment_status'] === Order::PAYMENT_PAID) {
            $this->setFlash('error', 'This order has already been paid.');
            $this->redirect("/admin/orders/{$orderId}");
            return;
        }
        if ($order['order_status'] === Order::STATUS_CANCELLED) {
            $this->setFlash('error', 'A cancelled order cannot be paid.');
            $this->redirect("/admin/orders/{$orderId}");
            return;
        }

        // Stale check
        if ($expectedUpdatedAt !== null && ($order['updated_at'] ?? '') !== $expectedUpdatedAt) {
            $this->setFlash('error', 'This order was updated by another staff member. Current state reloaded.');
            $this->redirect("/admin/orders/{$orderId}");
            return;
        }

        $user = auth_user();
        $changedBy = $user['id'] ?? null;
        $changedByName = $user['name'] ?? ($user['username'] ?? null);

        $ok = Order::markPaid($orderId, $cashReceived, $changedBy, $changedByName, $expectedUpdatedAt);

        if ($ok) {
            $after = Order::find($orderId);
            $change = (float) ($after['change_given'] ?? ($cashReceived - $total));
            $this->setFlash('success', 'Payment recorded. Change: ' . format_price($change));
        } else {
            $this->setFlash('error', 'Could not record payment. Please try again.');
        }

        $this->redirect("/admin/orders/{$orderId}");
    }
}
