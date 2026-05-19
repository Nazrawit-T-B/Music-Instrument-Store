<?php
class Database {
    private static ?PDO $instance = null;

    private function __construct() {} // prevent direct instantiation

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $host = 'localhost';
            $dbname = 'music_store';
            $user = 'root';
            $password = '';

            try {
                self::$instance = new PDO(
                    "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                    $user,
                    $password,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                error_log($e->getMessage());
                die('Database connection failed.');
            }
        }
        return self::$instance;
    }
}
?>