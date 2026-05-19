<?php
/*(add, remove, update quantity, clear)
Handle cart merging when a guest logs in
Add item timestamps (time of adding to cart) using PHP DateTime
Cart expiry logic: abandon carts older than X hours (cron-style cleanup using time())
Stock availability check before adding to cart*/

session_start();
require_once 'app\models\cartMod.php';

class CartController
{

    private cartModel $cart;
    public function __construct($dsn, $username, $password)
    {
        $this->cart = new cartModel($dsn, $username, $password);
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function handleCart()
    {
        $action    = $_POST['action'] ?? null;
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
        $quantity  = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if (!$action) {
            return $this->respond(400, ['error' => 'No action provided']);
        }

        $isLoggedIn = is_logged_in();
        $userId     = $isLoggedIn ? $_SESSION['user_id'] : null;
        $this->handleExpiry();
        switch ($action) {

            case 'add':
                $this->addToCart($isLoggedIn, $userId, $productId, $quantity);
                break;

            case 'update':
                $this->updateCart($isLoggedIn, $userId, $productId, $quantity);
                break;

            case 'remove':
                $this->removeFromCart($isLoggedIn, $userId, $productId);
                break;

            case 'clear':
                $this->clearCart($isLoggedIn, $userId);
                break;

            default:
                $this->respond(400, ['error' => 'Invalid action']);
        }
    }
    public function addToCart($isLoggedIn, $userId, $productId, $quantity)
    {

        if (!$productId || $quantity <= 0) {
            exit("Invalid input");
        }

        if (!$this->cart->checkStock($productId, $quantity)) {
            exit("Out of stock");
        }
        if ($isLoggedIn) {

           $this->cart->addOrUpdate($userId, $productId, $quantity);
        } else {

            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = [
                    'quantity' => $quantity,
                    'added_at' => time()
                ];
            }
        }
        $this->respond(200, ['message' => 'Item added to cart successfully.']);
    }
    public function updateCart($isLoggedIn, $userId, $productId, $quantity)
    {

        if (!$productId || $quantity < 0) {
            exit("Invalid input");
        }

        if ($isLoggedIn) {
           
           $this->cart->addOrUpdate($userId, $productId, $quantity);
        } else {
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
        }
         $this->respond(200, ['message' => 'Cart updated']);
    }
    public function removeFromCart($isLoggedIn, $userId, $productId)
    {
        if (!$productId) {
            exit("Invalid input");
        }

        if ($isLoggedIn) {
            $this->cart->remove($userId, $productId);
        } else {
            unset($_SESSION['cart'][$productId]);
        }

        $this->respond(200, ['message' => 'Item removed']);
    }
    public function clearCart($isLoggedIn, $userId)
    {

        if ($isLoggedIn) {
            $this->cart->clear($userId);
        }

        unset($_SESSION['cart']);
         $_SESSION['cart'] = [];

        $this->respond(200, ['message' => 'Cart cleared']);
    }
  private function handleExpiry()
    {
        $expiry = 7200; // 2 hours

        if (isset($_SESSION['cart_last_activity'])) {
            if (time() - $_SESSION['cart_last_activity'] > $expiry) {
                $_SESSION['cart'] = [];
            }
        }

        $_SESSION['cart_last_activity'] = time();
    }
    public function mergeCart($userId)
    {
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $productId => $item) {
                $this->cart->addOrUpdate(
                    $userId,
                    $productId,
                    $item['quantity']
                );
            }

            $_SESSION['cart'] = [];
        }
    }
    public function onUserLogin(int $userId): void
{
    $this->mergeCart($userId);
}

    private function respond(int $statusCode, array $data): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
