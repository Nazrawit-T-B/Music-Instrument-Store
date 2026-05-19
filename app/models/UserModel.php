<?php
class UserModel extends ParentModel {

    public function findByEmail(string $email): ?array {
        return $this->queryOne(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            [$email]
        );
    }

    public function findById(int $id): ?array {
        return $this->queryOne(
            'SELECT id, name, email, role, created_at FROM users WHERE id = ?',
            [$id]
        );
    }

    public function create(string $name, string $email, string $password): int {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $this->execute(
            'INSERT INTO users (name, email, password) VALUES (?, ?, ?)',
            [$name, $email, $hash]
        );

        return (int) $this->lastInsertId();
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function emailExists(string $email): bool {
        $row = $this->queryOne(
            'SELECT id FROM users WHERE email = ?',
            [$email]
        );
        return $row !== null;
    }
}
?>
