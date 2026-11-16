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

    public function create(): void
    {
        AuthMiddleware::check();

        $this->render('admin/categories/create', [
            'title' => 'Add Menu Category',
        ], 'layouts/admin');
    }

    public function edit(string $id): void
    {
        AuthMiddleware::check();

        $category = Category::find((int) $id);
        if (!$category) {
            $this->setFlash('error', 'Category not found.');
            $this->redirect('/admin/categories');
            return;
        }

        $this->render('admin/categories/edit', [
            'title' => 'Edit ' . $category['name'],
            'category' => $category,
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
            $this->redirect('/admin/categories/create');
            return;
        }

        $existing = Category::findByName($name);
        if ($existing) {
            $this->setFlash('error', 'A category with this name already exists.');
            $this->redirect('/admin/categories/create');
            return;
        }

        Category::create($name, !empty($description) ? $description : null);

        $this->setFlash('success', "Category \"{$name}\" created successfully.");
        $this->redirect('/admin/categories');
    }

    public function update(string $id): void
    {
        AuthMiddleware::check();
        $this->validateCsrf();

        $categoryId = (int) $id;
        $category = Category::find($categoryId);
        if (!$category) {
            $this->setFlash('error', 'Category not found.');
            $this->redirect('/admin/categories');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $this->setFlash('error', 'Category name is required.');
            $this->redirect("/admin/categories/{$categoryId}/edit");
            return;
        }

        $existing = Category::findByName($name);
        if ($existing && (int)$existing['id'] !== $categoryId) {
            $this->setFlash('error', 'A category with this name already exists.');
            $this->redirect("/admin/categories/{$categoryId}/edit");
            return;
        }

        Category::update($categoryId, $name, !empty($description) ? $description : null);

        $this->setFlash('success', "Category \"{$name}\" updated successfully.");
        $this->redirect('/admin/categories');
    }

    public function delete(string $id): void
    {
        AuthMiddleware::check();
        $this->validateCsrf();

        $categoryId = (int) $id;
        $category = Category::find($categoryId);
        if (!$category) {
            $this->setFlash('error', 'Category not found.');
            $this->redirect('/admin/categories');
            return;
        }

        // Prevent deleting categories with products
        $pdo = \App\Core\Database::connection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $stmt->execute(['id' => $categoryId]);
        $count = (int) $stmt->fetchColumn();
        if ($count > 0) {
            $this->setFlash('error', 'Cannot delete a category that still has products.');
            $this->redirect('/admin/categories');
            return;
        }

        Category::delete($categoryId);

        $this->setFlash('success', "Category \"{$category['name']}\" deleted successfully.");
        $this->redirect('/admin/categories');
    }
}
