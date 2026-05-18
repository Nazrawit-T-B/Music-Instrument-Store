<?php
//main functionality
/*(add, remove, update quantity, clear)
Handle cart merging when a guest logs in
Add item timestamps (time of adding to cart) using PHP DateTime
Cart expiry logic: abandon carts older than X hours (cron-style cleanup using time())
Stock availability check before adding to cart*/
    session_start();
    require_once 'CartModel.php';

    $action=$_POST['action'] ?? null; 
    $productId=$_POST['product_id'] ?? null;
    $quantity=$_POST['quantity'] ?? 1;
    if(!$action || !$productId){
        echo json_encode(['status'=>'error','message'=>'Invalid request']);
        exit;
    }
    if(isset($_SESSION['cart_last_activity'])){

        if(time()- $_SESSION['cart_last_activity']>7200){
            unset($_SESSION['cart']);
        }

    }
    $_SESSION['cart_last_activity']=time();

    if(!isset($_SESSION['cart'])){
        $_SESSION['cart']=[];
    }
    $isLoggedIn=isset($_SESSION['user_id']);
    $userId= $_SESSION['user_id'] ?? null;

    switch($action){
        case 'add':
            if(!$productId || $quantity <=0){
                exit("Invalid input");
            }

            if(!checkStock($conn,$productId,$quantity)){
                exit("Out of stock");
            }
            if ($isLoggedIn) {
            
            addOrUpdate($conn, $userId, $productId, $quantity);
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
         break;
        case 'update':
            if (!$productId || $quantity < 0) {
            exit("Invalid input");
        }

            if ($isLoggedIn) {
                // Simplest approach → overwrite quantity
                $stmt = $conn->prepare("
                    UPDATE cart_items
                    SET quantity = ?
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->execute([$quantity, $userId, $productId]);

            } else {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity'] = $quantity;
                }
            }

            break;

        case 'remove':
            if (!$productId) {
                exit("Invalid input");
            }

            if ($isLoggedIn) {
                $stmt = $conn->prepare("
                    DELETE FROM cart_items
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->execute([$userId, $productId]);

            } else {
                unset($_SESSION['cart'][$productId]);
            }

            break;
        case 'clear':

            if ($isLoggedIn) {
                $stmt = $conn->prepare("
                    DELETE FROM cart_items
                    WHERE user_id = ?
                ");
                $stmt->execute([$userId]);
            }

            unset($_SESSION['cart']);
            break;
    }
    //endswitch;
    echo "Cart Updated Successfully";
?>