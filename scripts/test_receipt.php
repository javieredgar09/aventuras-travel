<?php
/**
 * Test: regenerar recibo para pago 42 con nuevo formato
 */
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
require_once BASE_PATH . '/config.php';
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/app/services/ReceiptService.php';

$service = new ReceiptService();

// Regenerar para pago 42 (el que tiene recibo antiguo)
$result = $service->generate(42, [
    ['numero' => 3, 'concepto' => 'Cuota 3 — Abril', 'esperado' => 1583.33, 'aplicado' => 1583.33, 'tipo' => 'completa'],
    ['numero' => 4, 'concepto' => 'Cuota 4 — Mayo',  'esperado' => 1583.33, 'aplicado' => 1583.33, 'tipo' => 'completa'],
]);

echo "Resultado: " . ($result ?: 'ERROR') . "\n";

if ($result && file_exists(STORAGE_PATH . '/recibos/' . $result)) {
    $size = filesize(STORAGE_PATH . '/recibos/' . $result);
    $header = file_get_contents(STORAGE_PATH . '/recibos/' . $result, false, null, 0, 5);
    echo "Archivo: $result\n";
    echo "Tamaño: $size bytes\n";
    echo "PDF válido: " . ($header === '%PDF-' ? 'SÍ' : 'NO') . "\n";
}
