<?php require_once VIEW_PATH . 'partials/header.php' ?>
<link rel="stylesheet" href="/assets/css/styles.css">
<div class="container" id="hero-section">
    <div class="left">
        <div class="hero_text">Music Instrument Market</div>
        <p class="hero_desc">Expand your collection and find the perfect instrument with us.</p>
        <a href="/catalog">
            <div class="btn">
                Select in the catalog
                <div class="circle-icon">➜</div>
            </div>
        </a>
        <div class="search-bar">
            <label for="search"></label>
            <input type="search" id="search" placeholder="Search Products">
        </div>
        <div id="search-results"></div>
    </div>
    <img class="vinyl" src="/media/productimages/vinyl-record.jpg" alt="instrument" />
</div>

<script src="/assets/javascript/productNames.js"></script>
<script src="/assets/javascript/search.js"></script>

<?php require_once VIEW_PATH . 'partials/footer.php' ?>