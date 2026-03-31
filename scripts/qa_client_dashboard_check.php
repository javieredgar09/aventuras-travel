<?php
// QA rápido: verifica que los archivos principales de la nueva vista cliente existen
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/core/Database.php';

$checks = [
  'layout' => BASE_PATH . '/app/views/layouts/client.php',
  'dashboard' => BASE_PATH . '/app/views/client/dashboard.php',
  'partials' => BASE_PATH . '/app/views/client/partials',
  'css' => BASE_PATH . '/public/assets/css/client_dashboard.css',
  'js' => BASE_PATH . '/public/assets/js/client_dashboard.js',
];

foreach ($checks as $k => $path) {
    if (file_exists($path)) echo "OK: {$k} -> {$path}\n";
    else echo "MISSING: {$k} -> {$path}\n";
}

// Intentar conectar a DB y contar grupos
try {
    $db = Database::getInstance();
    $g = $db->fetchOne('SELECT COUNT(*) as c FROM grupos');
    echo "DB: grupos total = " . ($g['c'] ?? '0') . "\n";
} catch (Exception $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}

echo "QA listo. Abre /client/dashboard en el navegador (usuario autenticado) para ver el resultado visual.\n";
