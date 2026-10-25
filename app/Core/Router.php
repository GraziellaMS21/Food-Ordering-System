<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(
        string $method,
        string $path,
        array $handler
    ): void {
        $path = $this->normalizePath($path);

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '' ? '/' : $path;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (!is_string($path)) {
            http_response_code(400);
            echo '400 - Invalid request path';
            return;
        }

        $path = $this->normalizePath($path);
        $method = strtoupper($method);
        $pathExists = false;

        foreach ($this->routes as $route) {
            $params = $this->matchPath($route['path'], $path);

            if ($params === null) {
                continue;
            }

            $pathExists = true;

            if ($route['method'] !== $method) {
                continue;
            }

            [$controllerClass, $action] = $route['handler'];

            if (
                !class_exists($controllerClass) ||
                !method_exists($controllerClass, $action)
            ) {
                if (!headers_sent()) {
                    http_response_code(501);
                }
                echo '501 - Controller action not implemented';
                return;
            }

            $controller = new $controllerClass();

            $controller->$action(...array_values($params));

            return;
        }

        if ($pathExists) {
            if (!headers_sent()) {
                header('Allow: ' . $this->allowedMethods($path));
                http_response_code(405);
            }
            echo '405 - Method not allowed';
            return;
        }

        if (!headers_sent()) {
            http_response_code(404);
        }
        echo '404 - Page not found';
    }

    private function matchPath(
        string $routePath,
        string $requestPath
    ): ?array {
        $segments = explode('/', trim($routePath, '/'));
        $regexSegments = [];

        foreach ($segments as $segment) {
            if (
                preg_match(
                    '/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/',
                    $segment,
                    $matches
                )
            ) {
                $regexSegments[] =
                    '(?P<' . $matches[1] . '>[^/]+)';
            } else {
                $regexSegments[] = preg_quote($segment, '#');
            }
        }

        $pattern = '#^/' . implode('/', $regexSegments) . '/?$#';

        if (!preg_match($pattern, $requestPath, $matches)) {
            return null;
        }

        $params = [];

        foreach ($segments as $segment) {
            if (
                preg_match(
                    '/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/',
                    $segment,
                    $name
                )
            ) {
                $params[] = rawurldecode($matches[$name[1]]);
            }
        }

        return $params;
    }

    private function allowedMethods(string $path): string
    {
        $methods = [];

        foreach ($this->routes as $route) {
            if ($this->matchPath($route['path'], $path) !== null) {
                $methods[] = $route['method'];
            }
        }

        return implode(', ', array_unique($methods));
    }
}
