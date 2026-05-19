<?php
require_once 'config\database.php';

if (!isset($dsn) || !isset($username) || !isset($password)) {
    throw new RuntimeException('Database configuration variables are missing.');
}

class ParentModel {
    
    protected $conn;

    public function __construct($dsn, $username, $password) {
        try {
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    // Example method to use the connection
    public function getConnection() {
        return $this->conn;
    }
}
?>
