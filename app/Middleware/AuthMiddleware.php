<?php

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Enforce that a staff member is authenticated.
     */
    public static function check(): void
    {
        ensure_session_started();

        if (empty($_SESSION['staff_user']['id'])) {
            set_flash('error', 'Please log in to access the staff portal.');
            if (!headers_sent()) {
                header('Location: /staff/login');
            }
            exit;
        }
    }

    /**
     * Enforce that the authenticated staff member has the admin role.
     */
    public static function requireAdmin(): void
    {
        self::check();

        if (($_SESSION['staff_user']['role'] ?? '') !== 'admin') {
            set_flash('error', 'Administrator privileges are required for this action.');
            if (!headers_sent()) {
                header('Location: /admin');
            }
            exit;
        }
    }
}
