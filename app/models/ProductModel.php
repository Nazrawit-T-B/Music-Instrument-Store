<?php
class ProductModel extends ParentModel {

    /**
     * Get all products with filtering, sorting, and pagination
     * 
     * @param array $filters - ['category_id', 'minPrice', 'maxPrice', 'sort']
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 12): array {
        $conditions = [];
        $params = [];

        // Build WHERE clause
        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = :category_id';
            $params[':category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['minPrice'])) {
            $conditions[] = 'p.price >= :minPrice';
            $params[':minPrice'] = (float)$filters['minPrice'];
        }

        if (!empty($filters['maxPrice'])) {
            $conditions[] = 'p.price <= :maxPrice';
            $params[':maxPrice'] = (float)$filters['maxPrice'];
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Determine sort order
        $sortMap = [
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name_asc'   => 'p.name ASC',
            'name_desc'  => 'p.name DESC',
            'newest'     => 'p.id DESC',
        ];
        $orderBy = $sortMap[$filters['sort'] ?? 'newest'] ?? 'p.id DESC';

        // Calculate offset
        $offset = (int)(($page - 1) * $perPage);

        $sql = "
            SELECT 
                p.id, p.name, p.brand, p.price, p.descri, p.im, p.stock, p.category_id,
                c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            {$whereClause}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $params[':limit'] = $perPage;
        $params[':offset'] = $offset;

        return $this->query($sql, $params);
    }

    /**
     * Get single product by ID with category info
     * 
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array {
        $sql = "
            SELECT 
                p.id, p.name, p.brand, p.price, p.descri, p.im, p.stock, p.category_id,
                c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ";

        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * Get products by category (using category name/slug)
     * 
     * @param string $slug - category name (converted to slug match)
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByCategory(string $slug, int $page = 1, int $perPage = 12): array {
        // Convert slug to category_id by matching category name
        $categoryId = $this->getCategoryIdBySlug($slug);
        
        if (!$categoryId) {
            return [];
        }

        $filters = ['category_id' => $categoryId];
        return $this->getAll($filters, $page, $perPage);
    }

    /**
     * Search products by query with optional filters
     * Searches in name, brand, and description
     * 
     * @param string $query
     * @param array $filters - ['category_id', 'minPrice', 'maxPrice', 'sort']
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function search(string $query, array $filters = [], int $page = 1, int $perPage = 12): array {
        $conditions = [];
        $params = [];

        // Search condition
        $searchPattern = '%' . $query . '%';
        $conditions[] = '(p.name LIKE :query OR p.brand LIKE :query OR p.descri LIKE :query)';
        $params[':query'] = $searchPattern;

        // Category filter
        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = :category_id';
            $params[':category_id'] = (int)$filters['category_id'];
        }

        // Price filters
        if (!empty($filters['minPrice'])) {
            $conditions[] = 'p.price >= :minPrice';
            $params[':minPrice'] = (float)$filters['minPrice'];
        }

        if (!empty($filters['maxPrice'])) {
            $conditions[] = 'p.price <= :maxPrice';
            $params[':maxPrice'] = (float)$filters['maxPrice'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $conditions);

        // Sort order
        $sortMap = [
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name_asc'   => 'p.name ASC',
            'name_desc'  => 'p.name DESC',
            'relevance'  => 'CASE WHEN p.name LIKE :query_exact THEN 1 ELSE 2 END ASC, p.id DESC',
        ];
        $orderBy = $sortMap[$filters['sort'] ?? 'relevance'] ?? 'p.id DESC';
        
        if (strpos($orderBy, ':query_exact') !== false) {
            $params[':query_exact'] = $query;
        }

        $offset = (int)(($page - 1) * $perPage);

        $sql = "
            SELECT 
                p.id, p.name, p.brand, p.price, p.descri, p.im, p.stock, p.category_id,
                c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            {$whereClause}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $params[':limit'] = $perPage;
        $params[':offset'] = $offset;

        return $this->query($sql, $params);
    }

    /**
     * Get total count of products matching filters (for pagination)
     * 
     * @param array $filters
     * @param string|null $searchQuery - if provided, counts search results
     * @return int
     */
    public function getTotalCount(array $filters = [], ?string $searchQuery = null): int {
        $conditions = [];
        $params = [];

        // Search condition if query provided
        if ($searchQuery !== null) {
            $searchPattern = '%' . $searchQuery . '%';
            $conditions[] = '(p.name LIKE :query OR p.brand LIKE :query OR p.descri LIKE :query)';
            $params[':query'] = $searchPattern;
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = :category_id';
            $params[':category_id'] = (int)$filters['category_id'];
        }

        // Price filters
        if (!empty($filters['minPrice'])) {
            $conditions[] = 'p.price >= :minPrice';
            $params[':minPrice'] = (float)$filters['minPrice'];
        }

        if (!empty($filters['maxPrice'])) {
            $conditions[] = 'p.price <= :maxPrice';
            $params[':maxPrice'] = (float)$filters['maxPrice'];
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT COUNT(*) as total FROM products p {$whereClause}";
        $result = $this->queryOne($sql, $params);

        return (int)($result['total'] ?? 0);
    }

    /**
     * Get related products from the same category
     * Returns max 4 products excluding the given product ID
     * 
     * @param int $productId
     * @param int $categoryId
     * @return array
     */
    public function getRelated(int $productId, int $categoryId): array {
        $sql = "
            SELECT 
                p.id, p.name, p.brand, p.price, p.im, p.stock, p.category_id,
                c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = :category_id 
              AND p.id != :product_id
              AND p.stock > 0
            ORDER BY RAND()
            LIMIT 4
        ";

        return $this->query($sql, [
            ':category_id' => $categoryId,
            ':product_id' => $productId,
        ]);
    }

    /**
     * Get all categories for filter sidebar
     * 
     * @return array
     */
    public function getCategories(): array {
        $sql = "SELECT id, name FROM categories ORDER BY name ASC";
        return $this->query($sql);
    }

    /**
     * Get category name by category ID
     * 
     * @param int $categoryId
     * @return string|null
     */
    public function getCategoryName(int $categoryId): ?string {
        $sql = "SELECT name FROM categories WHERE id = :id";
        $result = $this->queryOne($sql, [':id' => $categoryId]);
        return $result['name'] ?? null;
    }

    /**
     * Convert category slug/name to category ID
     * Matches category name using slug conversion (spaces to hyphens)
     * 
     * @param string $slug
     * @return int|null
     */
    private function getCategoryIdBySlug(string $slug): ?int {
        // Convert slug back to potential category names
        // Support both slug format (hyphens) and exact name match
        $nameFromSlug = str_replace('-', ' ', $slug);
        
        $sql = "
            SELECT id FROM categories 
            WHERE LOWER(name) = LOWER(:name) 
               OR LOWER(REPLACE(name, ' ', '-')) = LOWER(:slug)
            LIMIT 1
        ";

        $result = $this->queryOne($sql, [
            ':name' => $nameFromSlug,
            ':slug' => $slug,
        ]);

        return $result ? (int)$result['id'] : null;
    }

    /**
     * Get price range for filters
     * Returns min and max prices in catalog
     * 
     * @return array ['min' => float, 'max' => float]
     */
    public function getPriceRange(): array {
        $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE stock > 0";
        $result = $this->queryOne($sql);
        
        return [
            'min' => (float)($result['min_price'] ?? 0),
            'max' => (float)($result['max_price'] ?? 0),
        ];
    }
}
?>
