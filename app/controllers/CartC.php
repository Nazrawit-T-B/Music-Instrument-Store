<?php
    /*(add, remove, update quantity, clear)
Handle cart merging when a guest logs in
Add item timestamps (time of adding to cart) using PHP DateTime
Cart expiry logic: abandon carts older than X hours (cron-style cleanup using time())
Stock availability check before adding to cart*/

    session_start();
    require_once 'app\models\cartMod.php';

    class CartController{

        private cartModel $cart;
        public function __construct($dsn,$username,$password)
        {
            $this->cart=new cartModel($dsn,$username,$password);
        }


        public function addToCart() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            $this->respond(401, ['error' => 'Unauthorized. Please log in.']);
            return;
        }

        $userId    = $_SESSION['user_id'];
        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : null;
        $quantity  = isset($_POST['quantity'])   ? (int) $_POST['quantity']   : null;

        if (!$productId || !$quantity || $quantity < 1) {
            $this->respond(400, ['error' => 'Invalid product ID or quantity.']);
            return;
        }

        if (!$this->cart->checkStock($productId, $quantity)) {
            $this->respond(409, ['error' => 'Insufficient stock for the requested quantity.']);
            return;
        }

        $this->cart->addOrUpdate($userId, $productId, $quantity);
        $this->respond(200, ['message' => 'Item added to cart successfully.']);
    }

    public function updateCart(){
        //code for updating the cart according to the session information
    }

    private function respond(int $statusCode, array $data): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

?>