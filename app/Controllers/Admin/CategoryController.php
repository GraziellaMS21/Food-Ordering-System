<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * List all categories and display the category creation form.
     */
    public function index(): void
    {
        AuthMiddleware::check();

        $categories = Category::all();

        $this->render('admin/categories/index', [
            'title' => 'Category Management',
            'categories' => $categories,
        ], 'layouts/admin');
    }

    /**
     * Store a new category.
     */
    public function store(): void
    {
        AuthMiddleware::check();
        $this->validateCsrf();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $this->setFlash('error', 'Category name is required.');
            $this->redirect('/admin/categories');
            return;
        }

        $existing = Category::findByName($name);
        if ($existing) {
            $this->setFlash('error', 'A category with this name already exists.');
            $this->redirect('/admin/categories');
            return;
        }

        Category::create($name, !empty($description) ? $description : null);

        $this->setFlash('success', "Category \"{$name}\" created successfully.");
        $this->redirect('/admin/categories');
    }
}
