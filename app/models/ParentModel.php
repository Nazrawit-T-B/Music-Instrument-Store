<?php
require_once BASE_PATH . 'config\database.php';

abstract class ParentModel {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    //run a query and return all rows
    protected function query(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    //return one row
    protected function queryOne(string $sql, array $params = []): ?array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    //run INSERT/UPDATE/DELETE and return affected rows
    protected function execute(string $sql, array $params = []): int {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    protected function lastInsertId(): string {
        return $this->db->lastInsertId();
    }
}
?>
