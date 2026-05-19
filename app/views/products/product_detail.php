<?php
/**
 * Product Detail View
 * Displays full product information with add-to-cart form and related products
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars(APP_NAME); ?></title>
    <link rel="stylesheet" href="/assets/css/details.css">
</head>
<body>
    <div class="product-detail-container">
        
        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb">
            <a href="/catalog">Catalog</a>
            <span class="separator">/</span>
            <a href="/catalog?category=<?php echo (int)$product['category_id']; ?>">
                <?php echo htmlspecialchars($product['category_name'] ?? 'Products'); ?>
            </a>
            <span class="separator">/</span>
            <span class="current"><?php echo htmlspecialchars($product['name']); ?></span>
        </nav>

        <!-- Main Product Section -->
        <main class="product-main">
            <div class="product-detail-grid">
                
                <!-- Product Image -->
                <div class="product-image-section">
                    <?php if (!empty($product['im'])): ?>
                        <img src="<?php echo htmlspecialchars($product['im']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="product-image-large">
                    <?php else: ?>
                        <div class="placeholder-image-large">No Image Available</div>
                    <?php endif; ?>
                </div>

                <!-- Product Information -->
                <div class="product-info-section">
                    <div class="product-meta">
                        <span class="category-badge">
                            <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                        </span>
                        <?php if (!empty($product['brand'])): ?>
                            <span class="brand-badge">
                                <?php echo htmlspecialchars($product['brand']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="product-title">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h1>

                    <div class="product-pricing">
                        <span class="price">
                            $<?php echo htmlspecialchars(number_format((float)$product['price'], 2)); ?>
                        </span>
                        <span class="stock-status <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                            <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                        </span>
                    </div>

                    <!-- Description -->
                    <?php if (!empty($product['descri'])): ?>
                        <div class="product-description">
                            <h3>Description</h3>
                            <p><?php echo htmlspecialchars($product['descri']); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Product Specifications -->
                    <div class="product-specs">
                        <h3>Details</h3>
                        <table class="specs-table">
                            <?php if (!empty($product['brand'])): ?>
                                <tr>
                                    <td class="spec-label">Brand:</td>
                                    <td><?php echo htmlspecialchars($product['brand']); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="spec-label">Category:</td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="spec-label">Price:</td>
                                <td>$<?php echo htmlspecialchars(number_format((float)$product['price'], 2)); ?></td>
                            </tr>
                            <tr>
                                <td class="spec-label">Stock:</td>
                                <td><?php echo (int)$product['stock']; ?> units available</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Add to Cart Form -->
                    <?php if ($product['stock'] > 0): ?>
                        <form method="POST" action="/cart/add" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                            
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <div class="quantity-input">
                                    <button type="button" class="qty-btn" onclick="decreaseQty()">−</button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                           max="<?php echo (int)$product['stock']; ?>" class="qty-field">
                                    <button type="button" class="qty-btn" onclick="increaseQty()">+</button>
                                </div>
                            </div>

                            <button type="submit" class="btn-add-to-cart">Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <div class="out-of-stock-message">
                            <p>This product is currently out of stock.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Share Section -->
                    <div class="product-share">
                        <p>Share this product:</p>
                        <div class="share-buttons">
                            <button class="share-btn facebook" title="Share on Facebook">f</button>
                            <button class="share-btn twitter" title="Share on Twitter">𝕏</button>
                            <button class="share-btn email" title="Share via Email">@</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Related Products Section -->
        <?php if (!empty($relatedProducts)): ?>
            <section class="related-products">
                <h2>Related Products</h2>
                <div class="related-products-grid">
                    <?php foreach (array_slice($relatedProducts, 0, 4) as $related): ?>
                        <div class="related-product-card">
                            <div class="product-image">
                                <?php if (!empty($related['im'])): ?>
                                    <img src="<?php echo htmlspecialchars($related['im']); ?>" 
                                         alt="<?php echo htmlspecialchars($related['name']); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="placeholder-image">No Image</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">
                                    <a href="/product/<?php echo (int)$related['id']; ?>">
                                        <?php echo htmlspecialchars($related['name']); ?>
                                    </a>
                                </h3>
                                <p class="product-price">
                                    $<?php echo htmlspecialchars(number_format((float)$related['price'], 2)); ?>
                                </p>
                                <a href="/product/<?php echo (int)$related['id']; ?>" class="btn-view">View</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <script>
        function increaseQty() {
            const field = document.getElementById('quantity');
            const max = parseInt(field.max);
            const current = parseInt(field.value);
            if (current < max) {
                field.value = current + 1;
            }
        }

        function decreaseQty() {
            const field = document.getElementById('quantity');
            const current = parseInt(field.value);
            if (current > 1) {
                field.value = current - 1;
            }
        }
    </script>
</body>
</html>
