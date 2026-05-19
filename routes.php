<?php
$uri    = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];
$uri = str_replace('Music-Instrument-Store/', '', $uri);

$routes = [
    'GET' => [
        ''                => [AuthController::class,    'showLogin'],
        'register'        => [AuthController::class,    'showRegister'],
        'login'           => [AuthController::class,    'showLogin'],
        'logout'          => [AuthController::class,    'logout'],
        'home'            => [HomeController::class,    'index'],
        'catalog'         => [ProductController::class, 'index'],
        'product/(\d+)'   => [ProductController::class, 'show'],
        'cart'            => [CartController::class,    'index'],
        'checkout'        => [OrderController::class,   'index'],
        'api/products'    => [APIController::class,     'listProducts'],
        'api/products/(\d+)' => [APIController::class, 'getProduct'],
        'api/rates'       => [APIController::class,     'getRates'],
    ],
    'POST' => [
        'register'        => [AuthController::class,    'register'],
        'login'           => [AuthController::class,    'login'],
        'cart/add'        => [CartController::class,    'add'],
        'cart/remove'     => [CartController::class,    'remove'],
        'checkout'        => [OrderController::class,   'place'],
        'admin/products'  => [AdminController::class,   'create'],
    ],
];

$methodRoutes = $routes[$method] ?? [];
foreach ($methodRoutes as $pattern => [$class, $action]) {
    if (preg_match("#^{$pattern}$#", $uri, $matches)) {
        $params = array_slice($matches, 1); // captured groups (e.g. product ID)
        $controller = new $class();
        call_user_func_array([$controller, $action], $params);
        exit;
    }
}

// 404 fallback
http_response_code(404);
require_once VIEW_PATH . '404.php';
?>
