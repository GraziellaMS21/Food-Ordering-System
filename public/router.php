<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if (
    $path !== '/' &&
    str_starts_with($path, '/') &&
    is_file($file)
) {
    return false;
}

require __DIR__ . '/index.php';