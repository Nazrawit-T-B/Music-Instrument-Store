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
    
    // run a query and return all rows
    protected function query(string $sql, array $params = []): array {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // return one row
    protected function queryOne(string $sql, array $params = []): ?array {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // run INSERT/UPDATE/DELETE and return affected rows
    protected function execute(string $sql, array $params = []): int {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    protected function lastInsertId(): string {
        return $this->conn->lastInsertId();
    }

    // Example method to use the connection
    public function getConnection() {
        return $this->conn;
    }
}
?>
