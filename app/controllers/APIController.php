<?php
class APIController {

    private ProductModel $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function listProducts(): void {
        $category = sanitize($_GET['category'] ?? '');
        $search   = sanitize($_GET['search']   ?? '');

        if ($category !== '') {
            $products = $this->productModel->getByCategory($category);
        } elseif ($search !== '') {
            $products = $this->productModel->search($search);
        } else {
            $products = $this->productModel->getAll();
        }

        $this->respond(200, $products);
    }

    public function getProduct(string $id): void {
        $product = $this->productModel->getById((int) $id);

        if (!$product) {
            $this->respond(404, null, 'Product not found');
            return;
        }

        $this->respond(200, $product);
    }

    private function respond(int $status, mixed $data, ?string $error = null): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode([
            'success' => $status < 400,
            'data'    => $data,
            'error'   => $error
        ]);
        exit;
    }
}