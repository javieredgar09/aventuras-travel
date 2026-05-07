<?php
/**
 * descargar-recibo.php — Sirve recibos PDF directamente, sin pasar por el framework MVC.
 * Evita compresión GZIP y headers de seguridad que corrompen binarios.
 *
 * Uso: /aventuras/public/descargar-recibo.php?id=42&mode=inline|download
 */

// Inicializar sesión para verificar autenticación
session_start();

define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');

// Verificar autenticación (debe tener sesión activa)
if (empty($_SESSION['user'])) {
    http_response_code(403);
    die('Acceso denegado. Inicie sesión.');
}

// Obtener parámetros
$pagoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mode = isset($_GET['mode']) && $_GET['mode'] === 'download' ? 'attachment' : 'inline';

if ($pagoId <= 0) {
    http_response_code(400);
    die('ID de pago inválido.');
}

// Conectar a BD para obtener el archivo
require_once BASE_PATH . '/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Error de conexión.');
}

$stmt = $pdo->prepare("SELECT id, recibo_url, contrato_id FROM pagos WHERE id = ?");
$stmt->execute([$pagoId]);
$pago = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pago || empty($pago['recibo_url'])) {
    http_response_code(404);
    die('Recibo no encontrado para este pago.');
}

// Verificar que el usuario tiene acceso (admin o dueño del contrato)
$user = $_SESSION['user'];
$isAdmin = ($user['rol'] ?? '') === 'admin';

if (!$isAdmin && !empty($pago['contrato_id'])) {
    // Verificar propiedad del contrato
    $stmt2 = $pdo->prepare("SELECT cliente_id FROM contratos WHERE id = ?");
    $stmt2->execute([(int)$pago['contrato_id']]);
    $contrato = $stmt2->fetch(PDO::FETCH_ASSOC);

    if ($contrato) {
        $stmt3 = $pdo->prepare("SELECT id FROM clientes WHERE usuario_id = ?");
        $stmt3->execute([(int)$user['id']]);
        $cliente = $stmt3->fetch(PDO::FETCH_ASSOC);

        if (!$cliente || (int)$cliente['id'] !== (int)$contrato['cliente_id']) {
            // Verificar si es representante del grupo
            $stmt4 = $pdo->prepare("SELECT g.id FROM contratos c JOIN grupos g ON c.grupo_id = g.id WHERE c.id = ? AND g.representante_id = ?");
            $stmt4->execute([(int)$pago['contrato_id'], (int)$user['id']]);
            if (!$stmt4->fetch()) {
                http_response_code(403);
                die('No tiene permiso para acceder a este recibo.');
            }
        }
    }
}

// Servir el archivo PDF
$filepath = STORAGE_PATH . '/recibos/' . basename($pago['recibo_url']);

if (!file_exists($filepath)) {
    http_response_code(404);
    die('Archivo de recibo no encontrado en el servidor.');
}

$filesize = filesize($filepath);
$cleanName = 'Recibo_' . pathinfo($pago['recibo_url'], PATHINFO_FILENAME) . '.pdf';

// Limpiar cualquier output buffer
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Headers para PDF
header('Content-Type: application/pdf');
header('Content-Disposition: ' . $mode . '; filename="' . $cleanName . '"');
header('Content-Length: ' . $filesize);
header('Content-Transfer-Encoding: binary');
header('Cache-Control: private, no-transform, no-store, must-revalidate');
header('Pragma: public');
header('Expires: 0');
header('Accept-Ranges: bytes');

// Desactivar compresión
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
}
ini_set('zlib.output_compression', 'Off');

// Enviar archivo
readfile($filepath);
exit(0);
