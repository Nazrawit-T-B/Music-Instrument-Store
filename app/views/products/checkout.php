<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>

<link rel="stylesheet" href="/assets/css/checkout.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<header>
    <h1>Checkout</h1>
</header>

<div class="main-content">

    <form method="POST" action="/checkout">

        <div class="container">

            <!-- PERSONAL INFO -->
            <div class="card">
                <h3>Personal Information</h3>

                <label>Full Name</label>
                <input type="text" name="name" required>

                <label>Email Address</label>
                <input type="email" name="email" required>

                <label>Phone Number</label>
                <div class="phone-input">
                    <select>
                        <option>+251 (Ethiopia)</option>
                    </select>
                    <input type="tel" name="phone" required>
                </div>

                <label>Number of Rental Days</label>
                <input type="number" name="days" min="1" value="1">

                <label>Delivery Address</label>
                <input type="text" name="address" required>
            </div>

            <!-- PAYMENT -->
            <div class="card">
                <h3>Payment Information</h3>

                <label>Payment Method</label>
                <select name="payment">
                    <option>Cash on Delivery</option>
                    <option>CBE Birr</option>
                    <option>Tele Birr</option>
                    <option>HelloCash</option>
                </select>

                <label>Payment Mobile Number</label>
                <div class="phone-input">
                    <select>
                        <option>+251 (Ethiopia)</option>
                    </select>
                    <input type="tel" name="payment_phone">
                </div>

                <label>Additional Notes</label>
                <input type="text" name="notes">
            </div>

        </div>

        <button class="order-button">Place Order</button>

    </form>

</div>

<footer>
    <p>© 2026 Music Store</p>
</footer>

</body>
</html>
