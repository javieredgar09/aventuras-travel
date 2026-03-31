<?php
/**
 * index.php - Front Controller / Entry Point
 * Aventuras Travel
 */

// Definir constantes base
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('STORAGE_PATH', BASE_PATH . '/storage');

// Autoloading simple
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/core/',
        BASE_PATH . '/app/controllers/',
        BASE_PATH . '/app/controllers/api/',
        BASE_PATH . '/app/models/',
        BASE_PATH . '/app/middlewares/',
        BASE_PATH . '/app/services/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Iniciar sesión segura
Session::start();

// Cargar rutas
require_once BASE_PATH . '/routes/web.php';
require_once BASE_PATH . '/routes/api.php';

// Despachar
Router::dispatch();
