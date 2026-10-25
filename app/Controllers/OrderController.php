<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;

class OrderController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function checkout(): void
    {
        $rawCart = $_SESSION['cart'] ?? [];

        if (empty($rawCart)) {
            $this->setFlash('warning', 'Your cart is empty. Please select some food items first.');
            $this->redirect('/menu');
            return;
        }

        $cartItems = [];
        $total = 0.0;
        $hasUnavailable = false;

        foreach ($rawCart as $productId => $item) {
            $product = Product::find((int) $productId);
            if (!$product || (int) $product['is_available'] !== 1) {
                $hasUnavailable = true;
                continue;
            }

            $quantity = (int) ($item['quantity'] ?? 1);
            $subtotal = (float) $product['price'] * $quantity;
            $total += $subtotal;

            $cartItems[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'category_name' => $product['category_name'] ?? '',
                'price' => (float) $product['price'],
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        if ($hasUnavailable) {
            $this->setFlash('error', 'Some items in your cart became unavailable. Please review your cart.');
            $this->redirect('/cart');
            return;
        }

        if (empty($cartItems)) {
            $this->setFlash('error', 'No available items in cart to checkout.');
            $this->redirect('/menu');
            return;
        }

        $this->render('orders/checkout', [
            'title' => 'Checkout - Pickup Order',
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    /**
     * Store a new customer order.
     */
    public function store(): void
    {
        $this->validateCsrf();

        $rawCart = $_SESSION['cart'] ?? [];
        if (empty($rawCart)) {
            $this->setFlash('error', 'Your cart is empty.');
            $this->redirect('/menu');
            return;
        }

        $customerName = trim($_POST['customer_name'] ?? '');
        $customerContact = trim($_POST['customer_contact'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        // Validation
        if (empty($customerName) || mb_strlen($customerName) < 2) {
            $this->setFlash('error', 'Please provide your full name for order pickup identification.');
            $this->redirect('/checkout');
            return;
        }

        if (empty($customerContact) || mb_strlen($customerContact) < 7) {
            $this->setFlash('error', 'Please provide a valid contact number (at least 7 digits) so we can reach you if needed.');
            $this->redirect('/checkout');
            return;
        }

        $cartItems = [];
        foreach ($rawCart as $productId => $item) {
            $cartItems[] = [
                'product_id' => (int) $productId,
                'quantity' => (int) ($item['quantity'] ?? 1),
            ];
        }

        try {
            $result = Order::createWithItems([
                'customer_name' => $customerName,
                'customer_contact' => $customerContact,
                'notes' => $notes,
            ], $cartItems);

            // Clear shopping cart
            unset($_SESSION['cart']);

            $this->setFlash('success', 'Your order has been placed successfully!');
            $this->redirect('/orders/confirmation?token=' . urlencode($result['tracking_token']));
        } catch (Exception $e) {
            $this->setFlash('error', 'Failed to place order: ' . $e->getMessage());
            $this->redirect('/checkout');
        }
    }

    /**
     * Show order confirmation page.
     */
    public function confirmation(): void
    {
        $token = trim($_GET['token'] ?? '');

        if (empty($token)) {
            $this->redirect('/orders/track');
            return;
        }

        $order = Order::findByTrackingToken($token);

        if (!$order) {
            $this->setFlash('error', 'Order not found or tracking link is invalid.');
            $this->redirect('/orders/track');
            return;
        }

        $items = OrderItem::getByOrderId($order['id']);

        $this->render('orders/confirmation', [
            'title' => 'Order Confirmed - ' . $order['order_number'],
            'order' => $order,
            'items' => $items,
            'trackingToken' => $token,
        ]);
    }

    /**
     * Customer order tracking.
     */
    public function track(): void
    {
        $token = trim($_GET['token'] ?? '');
        $orderNumber = trim($_GET['order_number'] ?? '');
        $contact = trim($_GET['contact'] ?? '');

        $order = null;
        $items = [];
        $searched = false;

        if (!empty($token)) {
            $searched = true;
            $order = Order::findByTrackingToken($token);
        } elseif (!empty($orderNumber)) {
            $searched = true;
            $found = Order::findByOrderNumber($orderNumber);

            // If contact is provided, verify match for privacy protection
            if ($found) {
                if (!empty($contact)) {
                    $cleanContact = preg_replace('/[^0-9]/', '', $contact);
                    $cleanOrderContact = preg_replace('/[^0-9]/', '', $found['customer_contact'] ?? '');
                    if ($cleanContact === $cleanOrderContact || str_ends_with($cleanOrderContact, $cleanContact)) {
                        $order = $found;
                    } else {
                        $this->setFlash('error', 'Contact number does not match this order number.');
                    }
                } else {
                    $order = $found;
                }
            }
        }

        if ($order) {
            $items = OrderItem::getByOrderId($order['id']);
        } elseif ($searched && empty($_SESSION['flash']['error'])) {
            $this->setFlash('error', 'No order found matching the provided details.');
        }

        $this->render('orders/track', [
            'title' => 'Track Your Order',
            'order' => $order,
            'items' => $items,
            'token' => $token,
            'orderNumber' => $orderNumber,
            'contact' => $contact,
            'searched' => $searched,
        ]);
    }
}
