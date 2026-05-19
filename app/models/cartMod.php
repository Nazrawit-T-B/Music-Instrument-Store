<?php
    require_once APP_PATH . 'models/ParentModel.php';
    class cartModel extends ParentModel {
        public function getByUser(int $userId): array {
            $stmt = $this->db->prepare(
                "SELECT ci.product_id, ci.quantity, p.name, p.price
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = ?"
            );
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }
            
        public function checkStock($productId, $quantity) {
            $stmt = $this->db->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) return false;

            return $product['stock'] >= $quantity;
        }

        public function addOrUpdate($userId, $productId, $quantity) {
            $stmt = $this->db->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $newQuantity = $existing['quantity'] + $quantity;
                $update = $this->db->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $update->execute([$newQuantity, $userId, $productId]);
            } else {
                $insert = $this->db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $insert->execute([$userId, $productId, $quantity]);
            }
        }
        public function remove($userId, $productId) {
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
        }
        public function clear($userId) {
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->execute([$userId]);
        }
        /*public function Update($userId, $productId, $quantity) {
            $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $userId, $productId]);
        }*/
    }
?>