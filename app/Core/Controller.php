<?php

namespace App\Core;

class Controller
{
    /**
     * Render a view with an optional layout.
     */
    protected function render(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        View::render($view, $data, $layout);
    }

    /**
     * Redirect to a given URL.
     */
    protected function redirect(string $url): void
    {
        if (!headers_sent()) {
            header("Location: {$url}");
        }
        exit;
    }

    /**
     * Return a JSON response.
     */
    protected function json(mixed $data, int $status = 200): void
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Validate CSRF token or terminate request with 403 Forbidden.
     */
    protected function validateCsrf(): void
    {
        if (!verify_csrf_token()) {
            http_response_code(403);
            die('403 Forbidden - Invalid or expired CSRF token. Please refresh and try again.');
        }
    }

    /**
     * Check if current request is AJAX / JSON request.
     */
    protected function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
            || (!empty($_POST['ajax']));
    }

    /**
     * Set a flash message.
     */
    protected function setFlash(string $type, string $message): void
    {
        set_flash($type, $message);
    }
}
