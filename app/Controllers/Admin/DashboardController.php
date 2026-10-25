<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(): void
    {
        AuthMiddleware::check();

        $stats = Order::getDashboardStats();
        $recentOrders = Order::all(null, null, null);
        $recentOrders = array_slice($recentOrders, 0, 8); // Top 8 most recent
        $products = Product::all();

        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'totalProducts' => count($products),
        ], 'layouts/admin');
    }
}
