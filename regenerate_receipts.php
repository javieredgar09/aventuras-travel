<?php
/**
 * regenerate_receipts.php - Regenera todos los recibos con la lógica correcta
 * Uso: php regenerate_receipts.php [contrato_codigo]
 * 
 * Ejemplo:
 *   php regenerate_receipts.php CCPA-2026-012     (regenerar recibos de un contrato específico)
 *   php regenerate_receipts.php                   (regenerar todos los recibos)
 */

define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');

// Load config
require_once BASE_PATH . '/config.php';

// Include core classes
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Model.php';

// Include models
require_once BASE_PATH . '/app/models/Pago.php';
require_once BASE_PATH . '/app/models/Contrato.php';
require_once BASE_PATH . '/app/models/Grupo.php';
require_once BASE_PATH . '/app/models/Cuota.php';

// Include services
require_once BASE_PATH . '/vendor/fpdf/fpdf.php';
require_once BASE_PATH . '/app/services/ReceiptService.php';

// Init DB
$db = Database::getInstance();
$db->connect();

echo "=== REGENERADOR DE RECIBOS ===\n\n";

// Get contract filter if provided
$contratoFilter = $argc > 1 ? $argv[1] : '';

// Get all approved payments
$pagoModel = new Pago();

if ($contratoFilter) {
    echo "Buscando pagos para contrato: $contratoFilter\n";
    $stmt = $db->pdo->prepare(
        "SELECT p.id FROM pagos p
         LEFT JOIN contratos c ON p.contrato_id = c.id
         WHERE c.codigo LIKE ? AND p.estado = 'aprobado'
         ORDER BY p.created_at DESC"
    );
    $stmt->execute(["%$contratoFilter%"]);
} else {
    echo "Buscando todos los pagos aprobados...\n";
    $stmt = $db->pdo->prepare(
        "SELECT id FROM pagos WHERE estado = 'aprobado' ORDER BY created_at DESC LIMIT 100"
    );
    $stmt->execute();
}

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($results)) {
    echo "No se encontraron pagos para regenerar.\n";
    exit(1);
}

echo "Total de pagos encontrados: " . count($results) . "\n\n";
echo "Presione ENTER para continuar o Ctrl+C para cancelar...\n";
fgets(STDIN);

$receiptService = new ReceiptService();
$regenerated = 0;
$failed = 0;
$skipped = 0;

foreach ($results as $row) {
    $pagoId = (int)$row['id'];
    $pago = $pagoModel->find($pagoId);
    
    if (!$pago) {
        echo "[$pagoId] ERROR: Pago no encontrado\n";
        $failed++;
        continue;
    }
    
    $montoActual = (float)($pago['monto'] ?? 0);
    $conceptoActual = $pago['concepto'] ?? 'Pago';
    
    echo "[$pagoId] {$conceptoActual}: \${$montoActual}... ";
    
    try {
        // Delete old receipt if exists
        if (!empty($pago['recibo_url'])) {
            $oldPath = STORAGE_PATH . '/recibos/' . basename($pago['recibo_url']);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
        
        // Regenerate receipt
        $result = $receiptService->generate($pagoId, []);
        
        if ($result) {
            echo "✓ OK\n";
            $regenerated++;
        } else {
            echo "✗ FALLO\n";
            $failed++;
        }
    } catch (Exception $e) {
        echo "✗ EXCEPTION: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n=== RESULTADOS ===\n";
echo "Regenerados: $regenerated\n";
echo "Fallidos: $failed\n";
echo "Saltados: $skipped\n";
echo "\nTotal procesados: " . ($regenerated + $failed + $skipped) . " de " . count($results) . "\n";

if ($failed === 0) {
    echo "\n✓ Regeneración completada exitosamente\n";
    exit(0);
} else {
    echo "\n⚠️  Hubo algunos errores durante la regeneración\n";
    exit(1);
}
?>
