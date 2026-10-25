<?php

namespace App\Core;

use PDO;

class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $configFile = dirname(__DIR__, 2) . '/config/app.php';
            $config = file_exists($configFile) ? require $configFile : [];
            $dbConfig = $config['database'] ?? [];

            $host = $dbConfig['host'] ?? '127.0.0.1';
            $port = $dbConfig['port'] ?? '3306';
            $database = $dbConfig['database'] ?? 'food_ordering_system';
            $username = $dbConfig['username'] ?? 'root';
            $password = $dbConfig['password'] ?? '';

            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";

            self::$connection = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$connection;
    }
}