<?php
/*(add, remove, update quantity, clear)
Handle cart merging when a guest logs in
Add item timestamps (time of adding to cart) using PHP DateTime
Cart expiry logic: abandon carts older than X hours (cron-style cleanup using time())
Stock availability check before adding to cart*/

require_once APP_PATH . 'models/cartMod.php';

class CartController
{

    private cartModel $cart;
    public function __construct() {
        $this->cart = new cartModel();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

public function index(): void {
        require_login();

        $isLoggedIn = is_logged_in();
        $userId     = $isLoggedIn ? $_SESSION['user_id'] : null;

        $this->handleExpiry();

        $cartItems = $isLoggedIn
            ? $this->cart->getByUser($userId)
            : $this->getSessionCartItems();

        $subtotal = array_sum(array_map(
            fn($item) => $item['price'] * $item['quantity'],
            $cartItems
        ));
        $tax      = round($subtotal * 0.15, 2);
        $discount = 0;
        $total    = $subtotal + $tax - $discount;

        require_once VIEW_PATH . 'cart.php';
    }

    public function add(): void {
        verify_csrf_token();
        $productId  = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
        $quantity   = isset($_POST['quantity'])   ? (int)$_POST['quantity']   : 1;
        $isLoggedIn = is_logged_in();
        $userId     = $isLoggedIn ? $_SESSION['user_id'] : null;
        $this->handleExpiry();
        $this->addToCart($isLoggedIn, $userId, $productId, $quantity);
    }

    public function remove(): void {
        verify_csrf_token();
        $productId  = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
        $isLoggedIn = is_logged_in();
        $userId     = $isLoggedIn ? $_SESSION['user_id'] : null;
        $this->removeFromCart($isLoggedIn, $userId, $productId);
    }

    public function addToCart($isLoggedIn, $userId, $productId, $quantity): void {
        if (!$productId || $quantity <= 0) {
            $this->respond(400, ['error' => 'Invalid input']);
            return;
        }
        if (!$this->cart->checkStock($productId, $quantity)) {
            $this->respond(400, ['error' => 'Out of stock']);
            return;
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

    public function updateCart($isLoggedIn, $userId, $productId, $quantity): void {
        if (!$productId || $quantity < 0) {
            $this->respond(400, ['error' => 'Invalid input']);
            return;
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

    public function removeFromCart($isLoggedIn, $userId, $productId): void {
        if (!$productId) {
            $this->respond(400, ['error' => 'Invalid input']);
            return;
        }
        if ($isLoggedIn) {
            $this->cart->remove($userId, $productId);
        } else {
            unset($_SESSION['cart'][$productId]);
        }
        $this->respond(200, ['message' => 'Item removed']);
    }

    public function clearCart($isLoggedIn, $userId): void {
        if ($isLoggedIn) {
            $this->cart->clear($userId);
        }
        unset($_SESSION['cart']);
        $_SESSION['cart'] = [];
        $this->respond(200, ['message' => 'Cart cleared']);
    }

    public function onUserLogin(int $userId): void {
        $this->mergeCart($userId);
    }

    public function mergeCart(int $userId): void {
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $productId => $item) {
                $this->cart->addOrUpdate($userId, $productId, $item['quantity']);
            }
            $_SESSION['cart'] = [];
        }
    }

    private function getSessionCartItems(): array {
        $items = [];
        foreach ($_SESSION['cart'] as $productId => $item) {
            $product = (new ProductModel())->getById($productId);
            if ($product) {
                $items[] = [
                    'product_id' => $productId,
                    'name'       => $product['name'],
                    'price'      => $product['price'],
                    'quantity'   => $item['quantity']
                ];
            }
        }
        return $items;
    }

    private function handleExpiry(): void {
        $expiry = 7200;
        if (isset($_SESSION['cart_last_activity'])) {
            if (time() - $_SESSION['cart_last_activity'] > $expiry) {
                $_SESSION['cart'] = [];
            }
        }
        $_SESSION['cart_last_activity'] = time();
    }

    private function respond(int $statusCode, array $data): void {
        if ($statusCode === 200) {
            header('Location: /cart');
        } else {
            $_SESSION['cart_error'] = $data['error'] ?? 'Something went wrong';
            header('Location: /cart');
        }
        exit;
    }
}
?>