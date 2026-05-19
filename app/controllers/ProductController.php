<?php
class ProductController {
    private ProductModel $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    /**
     * Display catalog with filtering and pagination
     */
    public function index(): void {
        // Get pagination values
        $page = (int)($_GET['page'] ?? 1);
        $page = max($page, 1);
        $perPage = 12;

        // Get filter values (sanitized)
        $filters = [];
        
        if (!empty($_GET['category'])) {
            $filters['category_id'] = (int)sanitize($_GET['category']);
        }

        if (!empty($_GET['min_price'])) {
            $filters['minPrice'] = (float)sanitize($_GET['min_price']);
        }

        if (!empty($_GET['max_price'])) {
            $filters['maxPrice'] = (float)sanitize($_GET['max_price']);
        }

        if (!empty($_GET['sort'])) {
            $allowedSorts = ['price_asc', 'price_desc', 'name_asc', 'name_desc', 'newest'];
            $sort = sanitize($_GET['sort']);
            $filters['sort'] = in_array($sort, $allowedSorts) ? $sort : 'newest';
        }

        // Fetch products and counts
        $products = $this->productModel->getAll($filters, $page, $perPage);
        $totalCount = $this->productModel->getTotalCount($filters);
        $categories = $this->productModel->getCategories();
        $priceRange = $this->productModel->getPriceRange();

        // Calculate pagination
        $totalPages = ceil($totalCount / $perPage);
        $totalPages = max($totalPages, 1);
        $page = min($page, $totalPages);

        // Extract variables for view
        extract([
            'products' => $products,
            'categories' => $categories,
            'priceRange' => $priceRange,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'perPage' => $perPage,
            'filters' => $filters,
            'currentPage' => 'catalog',
        ]);

        require_once VIEW_PATH . 'products/catalog.php';
    }

    /**
     * Display product detail page
     */
    public function show(int $id): void {
        $id = (int)$id;
        $product = $this->productModel->getById($id);

        if (!$product) {
            http_response_code(404);
            require_once VIEW_PATH . '404.php';
            return;
        }

        // Get related products
        $relatedProducts = $this->productModel->getRelated($id, (int)$product['category_id']);

        extract([
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'currentPage' => 'product',
        ]);

        require_once VIEW_PATH . 'products/product_detail.php';
    }

    /**
     * Handle product search
     */
    public function search(): void {
        $query = sanitize($_GET['q'] ?? '');
        $page = (int)($_GET['page'] ?? 1);
        $page = max($page, 1);
        $perPage = 12;

        $filters = [];

        // Category filter
        if (!empty($_GET['category'])) {
            $filters['category_id'] = (int)sanitize($_GET['category']);
        }

        // Price filters
        if (!empty($_GET['min_price'])) {
            $filters['minPrice'] = (float)sanitize($_GET['min_price']);
        }

        if (!empty($_GET['max_price'])) {
            $filters['maxPrice'] = (float)sanitize($_GET['max_price']);
        }

        // Sort
        if (!empty($_GET['sort'])) {
            $allowedSorts = ['price_asc', 'price_desc', 'name_asc', 'name_desc', 'relevance'];
            $sort = sanitize($_GET['sort']);
            $filters['sort'] = in_array($sort, $allowedSorts) ? $sort : 'relevance';
        }

        // Perform search
        $products = $query ? $this->productModel->search($query, $filters, $page, $perPage) : [];
        $totalCount = $query ? $this->productModel->getTotalCount($filters, $query) : 0;
        $categories = $this->productModel->getCategories();
        $priceRange = $this->productModel->getPriceRange();

        // Calculate pagination
        $totalPages = ceil($totalCount / $perPage);
        $totalPages = max($totalPages, 1);
        $page = min($page, $totalPages);

        extract([
            'products' => $products,
            'searchQuery' => $query,
            'categories' => $categories,
            'priceRange' => $priceRange,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'perPage' => $perPage,
            'filters' => $filters,
            'currentPage' => 'search',
        ]);

        require_once VIEW_PATH . 'products/search_results.php';
    }
}
?>
