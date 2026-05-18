<?php
    //main functionality 
    //model that syncs session-based guest cart with a DB-persisted cart for logged-in users
    /*
    $configFile = __DIR__ . '/database.php';
    if (!file_exists($configFile)) {
        $configFile = __DIR__ . '/../database.php';
    }
    if (!file_exists($configFile)) {
        throw new RuntimeException('Database configuration file not found.');
    }

    require_once $configFile;*/
    
    require_once 'database.php';
    if (!isset($dsn) || !isset($username) || !isset($password)) {
    throw new RuntimeException('Database configuration variables are missing.');
}


    try {

        $conn = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        throw $e;
    }
    function checkStock($conn,$productId,$quantity){
        
        $stmt=$conn->prepare("SELECT stock FROM products WHERE id=?");

        $stmt->execute([$productId]);

        $product=$stmt->fetch();

        if(!$product) return false;

        return $product['stock']>=$quantity;
    }
    function addOrUpdate($conn,$userId,$productId,$quantity){

        $stmt=$conn->prepare("SELECT quantity FROM cart_items WHERE user_id=? And product_id=?");
        $stmt->execute([$userId, $productId]);

        $existing=$stmt->fetch();

        if ($existing) {
        $newQuantity = $existing['quantity'] + $quantity;

        $update = $conn->prepare("
            UPDATE cart_items
            SET quantity = ?
            WHERE user_id = ? AND product_id = ?
        ");
        $update->execute([$newQuantity, $userId, $productId]);

    } else {
        $insert = $conn->prepare("
            INSERT INTO cart_items (user_id, product_id, quantity)
            VALUES (?, ?, ?)
        ");
        $insert->execute([$userId, $productId, $quantity]);
    }
    }
?>