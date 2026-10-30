<?php

require_once __DIR__ . '/config/config.php';

class Database
{
    private static $connection = null;

    public static function connect()
    {
        if (self::$connection === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST()
                . ';port=' . Config::DB_PORT()
                . ';dbname=' . Config::DB_NAME()
                . ';charset=utf8mb4';

            self::$connection = new PDO(
                $dsn,
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        }

        return self::$connection;
    }
}