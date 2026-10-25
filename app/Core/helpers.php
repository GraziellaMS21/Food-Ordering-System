<?php

declare(strict_types=1);

if (!function_exists('ensure_session_started')) {
    /**
     * Safely ensure session is started without headers-sent warning.
     */
    function ensure_session_started(): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
    }
}

if (!function_exists('e')) {
    /**
     * Safely escape HTML characters.
     */
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate or return existing CSRF token.
     */
    function csrf_token(): string
    {
        ensure_session_started();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate hidden input field with CSRF token.
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
    }
}

if (!function_exists('verify_csrf_token')) {
    /**
     * Verify submitted CSRF token.
     */
    function verify_csrf_token(?string $token = null): bool
    {
        ensure_session_started();

        $token = $token ?? $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (!empty($token) && hash_equals($sessionToken, $token)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('format_price')) {
    /**
     * Format a number as Philippine Peso.
     */
    function format_price(float|int|string|null $amount): string
    {
        $val = (float) ($amount ?? 0);
        return '₱' . number_format($val, 2);
    }
}

if (!function_exists('cart_count')) {
    /**
     * Return total number of items in the cart.
     */
    function cart_count(): int
    {
        ensure_session_started();

        $cart = $_SESSION['cart'] ?? [];
        $total = 0;
        foreach ($cart as $item) {
            $total += (int) ($item['quantity'] ?? 0);
        }

        return $total;
    }
}

if (!function_exists('auth_user')) {
    /**
     * Get currently authenticated staff user, or null.
     */
    function auth_user(): ?array
    {
        ensure_session_started();

        return $_SESSION['staff_user'] ?? null;
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if currently logged in user is admin.
     */
    function is_admin(): bool
    {
        $user = auth_user();
        return $user !== null && ($user['role'] ?? '') === 'admin';
    }
}

if (!function_exists('set_flash')) {
    /**
     * Set a flash message for the next request.
     */
    function set_flash(string $type, string $message): void
    {
        ensure_session_started();

        $_SESSION['flash'][$type][] = $message;
    }
}

if (!function_exists('get_flashes')) {
    /**
     * Retrieve and clear all flash messages.
     */
    function get_flashes(): array
    {
        ensure_session_started();

        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        return $flashes;
    }
}
