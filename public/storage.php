<?php
// Pequeño servidor seguro para servir archivos desde la carpeta /storage
// Uso: /storage.php?f=cache/serpapi/slug.jpg

require_once __DIR__ . '/../core/Session.php';

// Definir constantes si no están
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', BASE_PATH . '/storage');

$file = $_GET['f'] ?? '';
// Normalizar y evitar directory traversal
$file = ltrim($file, "\/\\");
if (strpos($file, '..') !== false) {
    http_response_code(400);
    echo 'Bad request';
    exit;
}

$full = STORAGE_PATH . DIRECTORY_SEPARATOR . $file;
if (!file_exists($full) || !is_file($full)) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

$mime = mime_content_type($full) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=86400');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
header('Content-Length: ' . filesize($full));
readfile($full);
exit;
