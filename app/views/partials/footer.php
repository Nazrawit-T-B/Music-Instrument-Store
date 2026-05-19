<footer class="footer">
    <div class="footer-container">
        <!-- Left -->
        <div class="footer-left">
            <div class="unleash-text">
                <span class="unleash">unleash</span>
                <span class="your">YOUR</span>
                <span class="inner">INNER</span>
                <span class="artist">ART<br>IST</span>
            </div>
        </div>

        <!-- Center -->
        <nav class="nav-links">
            <ul>
                <li><a href="/home">Home</a></li>
                <li><a href="/home#about-section">About Us</a></li>
                <li><a href="/catalog">Catalog</a></li>
                <li><a href="/cart">Cart</a></li>
            </ul>
        </nav>

        <!-- Right -->
        <div class="footer-right">
            <div class="signup-form">
                <?php if (!is_logged_in()): ?>
                    <a href="/register"><h3>Sign Up</h3></a>
                <?php endif; ?>
                <p>Get updates on our latest releases and sales</p>
            </div>
            <div class="social-icons">
                <a href="#" class="social-icon instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="#" class="social-icon facebook">
                    <i class="fa-brands fa-facebook"></i>
                </a>
                <a href="#" class="social-icon twitter">
                    <i class="fa-brands fa-x-twitter"></i>
                </a>
                <a href="#" class="social-icon youtube">
                    <i class="fa-brands fa-youtube"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>