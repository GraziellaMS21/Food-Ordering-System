<?php

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\MenuController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Controllers\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\OrderController as AdminOrderController;

/** @var Router $router */

// Customer pages
$router->get('/', [HomeController::class, 'index']);

$router->get('/menu', [MenuController::class, 'index']);
$router->get('/menu/{id}', [MenuController::class, 'show']);

// Shopping cart
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);

// Checkout and orders
$router->get('/checkout', [OrderController::class, 'checkout']);
$router->post('/orders', [OrderController::class, 'store']);
$router->get('/orders/confirmation', [OrderController::class, 'confirmation']);
$router->get('/orders/track', [OrderController::class, 'track']);

// Staff authentication
$router->get('/staff/login', [AuthController::class, 'showLogin']);
$router->post('/staff/login', [AuthController::class, 'login']);
$router->post('/staff/logout', [AuthController::class, 'logout']);

// Admin dashboard
$router->get('/admin', [DashboardController::class, 'index']);

// Admin product management
$router->get('/admin/products', [ProductController::class, 'index']);
$router->get('/admin/products/create', [ProductController::class, 'create']);
$router->post('/admin/products/store', [ProductController::class, 'store']);
$router->get('/admin/products/{id}/edit', [ProductController::class, 'edit']);
$router->post('/admin/products/{id}/update', [ProductController::class, 'update']);
$router->post('/admin/products/{id}/delete', [ProductController::class, 'delete']);

// Admin category management
$router->get('/admin/categories', [CategoryController::class, 'index']);
$router->post('/admin/categories/store', [CategoryController::class, 'store']);

// Admin order management
$router->get('/admin/orders', [AdminOrderController::class, 'index']);
$router->get('/admin/orders/{id}', [AdminOrderController::class, 'show']);
$router->post('/admin/orders/status', [AdminOrderController::class, 'updateStatus']);
$router->post('/admin/orders/payment', [AdminOrderController::class, 'markPaid']);
