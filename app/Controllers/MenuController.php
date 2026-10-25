<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;

class MenuController extends Controller
{
    public function index(): void
    {
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        $categories = Category::all();
        $products = Product::available($categoryId);

        if ($search !== null && $search !== '') {
            $lowerSearch = mb_strtolower($search);
            $products = array_filter($products, function ($p) use ($lowerSearch) {
                return str_contains(mb_strtolower($p['name']), $lowerSearch)
                    || str_contains(mb_strtolower($p['description'] ?? ''), $lowerSearch);
            });
        }

        $activeCategory = null;
        if ($categoryId) {
            foreach ($categories as $cat) {
                if ((int) $cat['id'] === $categoryId) {
                    $activeCategory = $cat;
                    break;
                }
            }
        }

        $this->render('menu/index', [
            'title' => $activeCategory ? $activeCategory['name'] . ' Menu' : 'Food Menu',
            'categories' => $categories,
            'products' => $products,
            'activeCategoryId' => $categoryId,
            'activeCategory' => $activeCategory,
            'search' => $search,
        ]);
    }

    public function show(string $id): void
    {
        $productId = (int) $id;
        $product = Product::find($productId);

        if (!$product) {
            http_response_code(404);
            $this->setFlash('error', 'Food item not found.');
            $this->redirect('/menu');
            return;
        }

        $this->render('menu/show', [
            'title' => $product['name'],
            'product' => $product,
        ]);
    }
}
