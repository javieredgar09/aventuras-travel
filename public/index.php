<?php
/**
 * index.php - Front Controller / Entry Point
 * Aventuras Travel
 */

// ── BASE PATH ─────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('STORAGE_PATH', BASE_PATH . '/storage');

// ── AUTOLOADING ───────────────────────────────────────────
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

// ── CONFIGURACIÓN CENTRAL ─────────────────────────────────
require_once BASE_PATH . '/config.php';

// ── HEADERS DE SEGURIDAD HTTP ─────────────────────────────
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
if (APP_ENV === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// ── HANDLER GLOBAL DE EXCEPCIONES ─────────────────────────
set_exception_handler(function (Throwable $e) {
    if (APP_ENV === 'production') {
        error_log('[EXCEPTION] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        http_response_code(500);
        $errorView = BASE_PATH . '/app/views/errors/500.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo '<h1>Error interno del servidor</h1><p>Por favor intenta más tarde.</p>';
        }
    } else {
        http_response_code(500);
        echo '<pre style="background:#1B3A4B;color:#4ABED9;padding:1rem;border-radius:8px;">';
        echo '<strong>⚠ Excepción PHP:</strong>' . PHP_EOL;
        echo htmlspecialchars($e->getMessage()) . PHP_EOL . PHP_EOL;
        echo $e->getTraceAsString();
        echo '</pre>';
    }
    exit;
});

// ── SESIÓN SEGURA ─────────────────────────────────────────
Session::start();

// ── RUTAS ─────────────────────────────────────────────────
require_once BASE_PATH . '/routes/web.php';
require_once BASE_PATH . '/routes/api.php';

// ── DISPATCH ──────────────────────────────────────────────
Router::dispatch();
