<?php require_once VIEW_PATH . 'partials/header.php' ?>

<div class="container">
    <div class="main-grid">
        <div class="card">
            <h2>Your Cart</h2>

            <div class="cart-box">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Action</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($cartItems)): ?>
                            <tr>
                                <td colspan="4">Your cart is empty. <a href="/catalog">Continue shopping</a></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?= sanitize($item['name']) ?></td>
                                    <td><?= (int) $item['quantity'] ?></td>
                                    <td>
                                        <form method="POST" action="/cart/remove">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                            <button type="submit">Remove</button>
                                        </form>
                                    </td>
                                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div class="card details">
                <h3>Purchase Order Details</h3>
                <p>
                    <span>Subtotal</span>
                    <span id="subtotal" class="total-price">
                        $<?= number_format($subtotal ?? 0, 2) ?>
                    </span>
                </p>
                <p>
                    <span>Tax</span>
                    <span id="tax">
                        $<?= number_format($tax ?? 0, 2) ?>
                    </span>
                </p>
                <p>
                    <span>Discount</span>
                    <span id="discount">
                        -$<?= number_format($discount ?? 0, 2) ?>
                    </span>
                </p>
                <p class="total">
                    <span>Total</span>
                    <span id="total" class="total-price">
                        $<?= number_format($total ?? 0, 2) ?>
                    </span>
                </p>
            </div>

            <div class="card details">
                <div class="checkout">
                    <a href="/checkout"><button>Proceed to Checkout</button></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!--<script src="/assets/javascript/script.js"></script>-->

<?php require_once VIEW_PATH . 'partials/footer.php' ?>