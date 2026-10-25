<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index(): void
    {
        $rawCart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $total = 0.0;
        $hasUnavailableItems = false;

        foreach ($rawCart as $productId => $item) {
            $productId = (int) $productId;
            $quantity = (int) ($item['quantity'] ?? 1);

            $product = Product::find($productId);

            if (!$product) {
                // Product no longer exists in DB
                unset($_SESSION['cart'][$productId]);
                $hasUnavailableItems = true;
                continue;
            }

            $isAvailable = (int) $product['is_available'] === 1;
            if (!$isAvailable) {
                $hasUnavailableItems = true;
            }

            $unitPrice = (float) $product['price'];
            $subtotal = $unitPrice * $quantity;

            if ($isAvailable) {
                $total += $subtotal;
            }

            $cartItems[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'category_name' => $product['category_name'] ?? '',
                'price' => $unitPrice,
                'image' => $product['image'],
                'is_available' => $isAvailable,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        if ($hasUnavailableItems) {
            $this->setFlash('warning', 'Some items in your cart are currently unavailable or sold out and cannot be ordered.');
        }

        $this->render('cart/index', [
            'title' => 'Your Shopping Cart',
            'cartItems' => $cartItems,
            'total' => $total,
            'hasUnavailableItems' => $hasUnavailableItems,
        ]);
    }

    /**
     * Add product to cart.
     */
    public function add(): void
    {
        $this->validateCsrf();

        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
        $referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/menu';

        if ($productId <= 0 || $quantity <= 0) {
            $errorMsg = 'Invalid product or quantity specified.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $errorMsg], 400);
            }
            $this->setFlash('error', $errorMsg);
            $this->redirect($referer);
            return;
        }

        $product = Product::findAvailable($productId);
        if (!$product) {
            $errorMsg = 'Sorry, this product is currently unavailable or out of stock.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $errorMsg], 404);
            }
            $this->setFlash('error', $errorMsg);
            $this->redirect($referer);
            return;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = min(99, $_SESSION['cart'][$productId]['quantity'] + $quantity);
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $productId,
                'quantity' => min(99, $quantity),
            ];
        }

        $successMsg = "Added \"{$product['name']}\" to your cart.";

        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'message' => $successMsg,
                'cart_count' => cart_count(),
                'product_name' => $product['name'],
            ]);
        }

        $this->setFlash('success', $successMsg);
        $this->redirect($referer);
    }

    /**
     * Update quantity of an item in the cart.
     */
    public function update(): void
    {
        $this->validateCsrf();

        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

        if ($productId <= 0) {
            $this->setFlash('error', 'Invalid product.');
            $this->redirect('/cart');
            return;
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
            $this->setFlash('info', 'Item removed from your cart.');
        } else {
            $quantity = min(99, $quantity);
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
            $this->setFlash('success', 'Cart updated successfully.');
        }

        $this->redirect('/cart');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(): void
    {
        $this->validateCsrf();

        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $this->setFlash('info', 'Item removed from your cart.');
        }

        $this->redirect('/cart');
    }
}
