<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index(): void
    {
        $categories = Category::all();
        $products = Product::available();

        $this->render('home/index', [
            'title' => 'Home - Delicious Canteen Meals',
            'categories' => $categories,
            'products' => array_slice($products, 0, 6), // show up to 6 featured items on home
        ]);
    }
}
