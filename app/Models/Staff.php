<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Staff
{
    public static function findByUsername(string $username): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM staff WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $staff = $stmt->fetch();

        return $staff ?: null;
    }

    public static function find(int|string $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, name, username, role, is_active, created_at, updated_at FROM staff WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $staff = $stmt->fetch();

        return $staff ?: null;
    }

    public static function verifyCredentials(string $username, string $password): ?array
    {
        $staff = self::findByUsername($username);

        if (!$staff) {
            return null;
        }

        if ((int) $staff['is_active'] !== 1) {
            return null;
        }

        if (password_verify($password, $staff['password'])) {
            unset($staff['password']);
            return $staff;
        }

        return null;
    }

    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT id, name, username, role, is_active, created_at FROM staff ORDER BY name ASC');
        return $stmt->fetchAll();
    }
}
