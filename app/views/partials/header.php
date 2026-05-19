<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>The Fine Tune</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/header.css">
    <link rel="stylesheet" href="/assets/css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link id="favicon" rel="icon" type="image/jpg" href="/media/productimages/favicon.jpg">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="/home" class="nav-logo">
                <img src="/media/productimages/logo.png" alt="Music Instrument Store Logo" class="header-logo">
                <svg class="notes" viewBox="0 0 100 100">
                    <text x="20" y="80">🎵</text>
                    <text x="50" y="90">🎶</text>
                    <text x="70" y="80">🎵</text>
                    <text x="90" y="90">🎶</text>
                </svg>
                <p class="logo-text">The Fine Tune</p>
            </a>
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/home" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="/catalog" class="nav-link">Catalog</a></li>
            <li class="nav-item"><a href="/cart" class="nav-link">Cart</a></li>
            <?php if (is_logged_in()): ?>
                <li class="nav-item"><a href="/logout" class="nav-link">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a href="/login" class="nav-link">Sign-In</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>