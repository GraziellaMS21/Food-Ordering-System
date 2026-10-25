<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Staff;

class AuthController extends Controller
{
    /**
     * Show staff sign-in form.
     */
    public function showLogin(): void
    {
        if (auth_user() !== null) {
            $this->redirect('/admin');
            return;
        }

        $this->render('auth/login', [
            'title' => 'Staff Login - Meal & Deal',
        ], 'layouts/main');
    }

    /**
     * Authenticate staff member.
     */
    public function login(): void
    {
        $this->validateCsrf();

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Please enter both your username and password.');
            $this->redirect('/staff/login');
            return;
        }

        $staff = Staff::verifyCredentials($username, $password);

        if (!$staff) {
            $this->setFlash('error', 'Invalid username or password, or account is inactive.');
            $this->redirect('/staff/login');
            return;
        }

        // Regenerate session ID upon successful authentication for session fixation protection
        session_regenerate_id(true);

        $_SESSION['staff_user'] = [
            'id' => $staff['id'],
            'name' => $staff['name'],
            'username' => $staff['username'],
            'role' => $staff['role'],
        ];

        $this->setFlash('success', "Welcome back, {$staff['name']}!");
        $this->redirect('/admin');
    }

    /**
     * Log out staff member.
     */
    public function logout(): void
    {
        $this->validateCsrf();

        unset($_SESSION['staff_user']);
        session_regenerate_id(true);

        $this->setFlash('info', 'You have been successfully signed out.');
        $this->redirect('/staff/login');
    }
}
