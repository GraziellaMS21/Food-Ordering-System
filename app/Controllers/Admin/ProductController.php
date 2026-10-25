<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;
use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * List all products.
     */
    public function index(): void
    {
        AuthMiddleware::check();

        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $categories = Category::all();
        $products = Product::all($categoryId);

        $this->render('admin/products/index', [
            'title' => 'Product Management',
            'products' => $products,
            'categories' => $categories,
            'selectedCategoryId' => $categoryId,
        ], 'layouts/admin');
    }

    /**
     * Show form to create a new product.
     */
    public function create(): void
    {
        AuthMiddleware::check();

        $categories = Category::all();

        $this->render('admin/products/create', [
            'title' => 'Add Menu Product',
            'categories' => $categories,
        ], 'layouts/admin');
    }

    /**
     * Store a new product.
     */
    public function store(): void
    {
        AuthMiddleware::check();
        $this->validateCsrf();

        $name = trim($_POST['name'] ?? '');
        $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
        $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
        $description = trim($_POST['description'] ?? '');
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;

        if (empty($name)) {
            $this->setFlash('error', 'Product name is required.');
            $this->redirect('/admin/products/create');
            return;
        }

        if ($price === false || $price < 0) {
            $this->setFlash('error', 'Valid, non-negative price is required.');
            $this->redirect('/admin/products/create');
            return;
        }

        $imagePath = $this->handleImageUpload();

        Product::create([
            'category_id' => $categoryId,
            'name' => $name,
            'description' => !empty($description) ? $description : null,
            'price' => $price,
            'image' => $imagePath,
            'is_available' => $isAvailable,
        ]);

        $this->setFlash('success', "Product \"{$name}\" created successfully.");
        $this->redirect('/admin/products');
    }

    /**
     * Show edit form for a product.
     */
    public function edit(string $id): void
    {
        AuthMiddleware::check();

        $product = Product::find((int) $id);
        if (!$product) {
            $this->setFlash('error', 'Product not found.');
            $this->redirect('/admin/products');
            return;
        }

        $categories = Category::all();

        $this->render('admin/products/edit', [
            'title' => 'Edit ' . $product['name'],
            'product' => $product,
            'categories' => $categories,
        ], 'layouts/admin');
    }

    /**
     * Update an existing product.
     */
    public function update(string $id): void
    {
        AuthMiddleware::check();
        $this->validateCsrf();

        $productId = (int) $id;
        $product = Product::find($productId);

        if (!$product) {
            $this->setFlash('error', 'Product not found.');
            $this->redirect('/admin/products');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
        $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
        $description = trim($_POST['description'] ?? '');
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;

        if (empty($name)) {
            $this->setFlash('error', 'Product name is required.');
            $this->redirect("/admin/products/{$productId}/edit");
            return;
        }

        if ($price === false || $price < 0) {
            $this->setFlash('error', 'Valid, non-negative price is required.');
            $this->redirect("/admin/products/{$productId}/edit");
            return;
        }

        $newImage = $this->handleImageUpload();
        $imagePath = $newImage !== null ? $newImage : $product['image'];

        // Option to clear image
        if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
            $imagePath = null;
        }

        Product::update($productId, [
            'category_id' => $categoryId,
            'name' => $name,
            'description' => !empty($description) ? $description : null,
            'price' => $price,
            'image' => $imagePath,
            'is_available' => $isAvailable,
        ]);

        $this->setFlash('success', "Product \"{$name}\" updated successfully.");
        $this->redirect('/admin/products');
    }

    /**
     * Delete a product.
     */
    public function delete(string $id): void
    {
        AuthMiddleware::check();
        $this->validateCsrf();

        $productId = (int) $id;
        $product = Product::find($productId);

        if ($product) {
            Product::delete($productId);
            $this->setFlash('success', "Product \"{$product['name']}\" deleted.");
        }

        $this->redirect('/admin/products');
    }

    /**
     * Helper to safely process image uploads.
     */
    private function handleImageUpload(): ?string
    {
        if (empty($_FILES['image']['name']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            // Check if text image URL was provided
            $textUrl = trim($_POST['image_url'] ?? '');
            return !empty($textUrl) ? $textUrl : null;
        }

        $file = $_FILES['image'];
        $maxSize = 2 * 1024 * 1024; // 2MB limit

        if ($file['size'] > $maxSize) {
            return null;
        }

        $allowedMimes = [
            'image/jpeg' => '.jpg',
            'image/png'  => '.png',
            'image/webp' => '.webp',
            'image/gif'  => '.gif',
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowedMimes[$mime])) {
            return null;
        }

        $ext = $allowedMimes[$mime];
        $filename = 'prod_' . bin2hex(random_bytes(8)) . $ext;
        $destinationDir = dirname(__DIR__, 2) . '/public/assets/images/uploads/';
        $destination = $destinationDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return '/assets/images/uploads/' . $filename;
        }

        return null;
    }
}
