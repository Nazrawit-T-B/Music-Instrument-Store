<?php
/**
 * Search Results View
 * Displays search results in a grid layout with filters and pagination
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?php echo htmlspecialchars(APP_NAME); ?></title>
    <link rel="stylesheet" href="/assets/css/catalog.css">
</head>
<body>
    <div class="catalog-container">
        <header class="catalog-header">
            <h1>Search Results</h1>
            <?php if (!empty($searchQuery)): ?>
                <p class="subtitle">
                    Results for: <strong><?php echo htmlspecialchars($searchQuery); ?></strong>
                </p>
            <?php endif; ?>
        </header>

        <div class="catalog-layout">
            <!-- Filter Sidebar -->
            <aside class="filters-sidebar">
                <h2>Refine Search</h2>
                <form method="GET" action="/search" class="filters-form">
                    <input type="hidden" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    
                    <!-- Category Filter -->
                    <fieldset class="filter-group">
                        <legend>Category</legend>
                        <div class="filter-options">
                            <label>
                                <input type="radio" name="category" value="" <?php echo empty($filters['category_id']) ? 'checked' : ''; ?>>
                                All Categories
                            </label>
                            <?php foreach ($categories as $category): ?>
                                <label>
                                    <input type="radio" name="category" value="<?php echo (int)$category['id']; ?>" 
                                        <?php echo ($filters['category_id'] ?? null) == $category['id'] ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <!-- Price Filter -->
                    <fieldset class="filter-group">
                        <legend>Price Range</legend>
                        <div class="price-inputs">
                            <label>
                                Min: $
                                <input type="number" name="min_price" min="0" step="0.01" 
                                    value="<?php echo htmlspecialchars($filters['minPrice'] ?? ''); ?>" 
                                    placeholder="<?php echo htmlspecialchars((string)$priceRange['min']); ?>">
                            </label>
                            <label>
                                Max: $
                                <input type="number" name="max_price" min="0" step="0.01" 
                                    value="<?php echo htmlspecialchars($filters['maxPrice'] ?? ''); ?>" 
                                    placeholder="<?php echo htmlspecialchars((string)$priceRange['max']); ?>">
                            </label>
                        </div>
                    </fieldset>

                    <!-- Sort Order -->
                    <fieldset class="filter-group">
                        <legend>Sort By</legend>
                        <select name="sort" class="sort-select">
                            <option value="relevance" <?php echo ($filters['sort'] ?? 'relevance') === 'relevance' ? 'selected' : ''; ?>>Relevance</option>
                            <option value="price_asc" <?php echo ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php echo ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name_asc" <?php echo ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                            <option value="name_desc" <?php echo ($filters['sort'] ?? '') === 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                        </select>
                    </fieldset>

                    <button type="submit" class="btn-filter">Apply Filters</button>
                    <a href="/search?q=<?php echo htmlspecialchars($searchQuery); ?>" class="btn-reset">Reset Filters</a>
                </form>
            </aside>

            <!-- Search Results Grid -->
            <main class="products-main">
                <?php if (empty($searchQuery)): ?>
                    <div class="empty-state">
                        <h2>Enter a Search Query</h2>
                        <p>Please enter a product name, brand, or keyword to search.</p>
                        <form method="GET" action="/search" class="search-form-inline">
                            <input type="text" name="q" placeholder="Search products..." required>
                            <button type="submit" class="btn-primary">Search</button>
                        </form>
                    </div>
                <?php elseif (empty($products)): ?>
                    <div class="empty-state">
                        <h2>No Results Found</h2>
                        <p>
                            Sorry, we couldn't find any products matching 
                            <strong><?php echo htmlspecialchars($searchQuery); ?></strong>
                        </p>
                        <p>Try adjusting your filters or search with different keywords.</p>
                        <a href="/catalog" class="btn-primary">Browse Catalog</a>
                    </div>
                <?php else: ?>
                    <div class="results-info">
                        <p>
                            Found <strong><?php echo htmlspecialchars((string)$totalCount); ?></strong> 
                            product<?php echo $totalCount !== 1 ? 's' : ''; ?> 
                            matching <strong><?php echo htmlspecialchars($searchQuery); ?></strong>
                        </p>
                    </div>

                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if (!empty($product['im'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['im']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             loading="lazy">
                                    <?php else: ?>
                                        <div class="placeholder-image">No Image</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">
                                        <a href="/product/<?php echo (int)$product['id']; ?>">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </a>
                                    </h3>
                                    <?php if (!empty($product['brand'])): ?>
                                        <p class="product-brand">
                                            <?php echo htmlspecialchars($product['brand']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="product-category">
                                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                    </p>
                                    <div class="product-footer">
                                        <span class="product-price">
                                            $<?php echo htmlspecialchars(number_format((float)$product['price'], 2)); ?>
                                        </span>
                                        <a href="/product/<?php echo (int)$product['id']; ?>" class="btn-view">View</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="<?php echo htmlspecialchars('/search?q=' . urlencode($searchQuery) . '&page=1' . $this->buildSearchQueryString($filters)); ?>" class="page-link">First</a>
                                <a href="<?php echo htmlspecialchars('/search?q=' . urlencode($searchQuery) . '&page=' . ($page - 1) . $this->buildSearchQueryString($filters)); ?>" class="page-link">Previous</a>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            
                            if ($start > 1): ?>
                                <span class="page-dots">...</span>
                            <?php endif;
                            
                            for ($i = $start; $i <= $end; $i++):
                                $active = $i === $page ? ' active' : '';
                                ?>
                                <a href="<?php echo htmlspecialchars('/search?q=' . urlencode($searchQuery) . '&page=' . $i . $this->buildSearchQueryString($filters)); ?>" 
                                   class="page-link<?php echo $active; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor;
                            
                            if ($end < $totalPages): ?>
                                <span class="page-dots">...</span>
                            <?php endif; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="<?php echo htmlspecialchars('/search?q=' . urlencode($searchQuery) . '&page=' . ($page + 1) . $this->buildSearchQueryString($filters)); ?>" class="page-link">Next</a>
                                <a href="<?php echo htmlspecialchars('/search?q=' . urlencode($searchQuery) . '&page=' . $totalPages . $this->buildSearchQueryString($filters)); ?>" class="page-link">Last</a>
                            <?php endif; ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Helper function to build query string from filters for search
 */
function buildSearchQueryString(array $filters): string {
    $parts = [];
    if (!empty($filters['category_id'])) {
        $parts[] = 'category=' . (int)$filters['category_id'];
    }
    if (!empty($filters['minPrice'])) {
        $parts[] = 'min_price=' . (float)$filters['minPrice'];
    }
    if (!empty($filters['maxPrice'])) {
        $parts[] = 'max_price=' . (float)$filters['maxPrice'];
    }
    if (!empty($filters['sort'])) {
        $parts[] = 'sort=' . htmlspecialchars($filters['sort']);
    }
    return $parts ? '&' . implode('&', $parts) : '';
}
?>
