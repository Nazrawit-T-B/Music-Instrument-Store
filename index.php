<?php
define('BASE_PATH',  __DIR__ . '/');
define('APP_PATH',   BASE_PATH . 'app/');
define('VIEW_PATH',  APP_PATH  . 'views/');
define('UPLOAD_PATH', BASE_PATH . 'uploads/');

require_once BASE_PATH . 'config/config.php';
require_once BASE_PATH . 'config/database.php';
require_once APP_PATH . 'helpers/helpers.php';

// Autoload all classes from app/
spl_autoload_register(function (string $class): void {
    $dirs = ['controllers', 'models', 'helpers'];
    foreach ($dirs as $dir) {
        $file = APP_PATH . $dir . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

session_start();

require_once BASE_PATH . 'routes.php';
?>
