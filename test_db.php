<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

try {
    $pdo = Database::connection();

    $statement = $pdo->query(
        'SELECT COUNT(*) FROM products'
    );

    $count = $statement->fetchColumn();

    echo "Database connected successfully!<br>";
    echo "Products found: " . (int) $count;
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed. Check your configuration.";
}