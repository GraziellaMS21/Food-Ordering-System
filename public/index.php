<?php
declare(strict_types=1);

use App\Core\Router;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/app/Core/helpers.php';

$router = new Router();

require dirname(__DIR__) . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);