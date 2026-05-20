<?php
require_login();
class OrderController {
    public function index() {
        require_once VIEW_PATH . '/products/checkout.php';
    }
    public function place() {

        // (Later you can validate + save to DB)

        $_SESSION['success'] = "Order placed successfully!";

        $_SESSION['success'] = "Order placed successfully!";
    }
}
