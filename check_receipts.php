<?php
require 'config.php';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER, DB_PASS
);

// Get all payments for CCPA-2026-012
$stmt = $pdo->prepare(
    "SELECT p.id, p.contrato_id, p.monto, p.estado, p.concepto, p.fecha_aprobacion, p.created_at, p.recibo_url
     FROM pagos p
     WHERE p.contrato_id = (SELECT id FROM contratos WHERE codigo LIKE '%CCPA-2026-012%')
     ORDER BY p.created_at DESC LIMIT 10"
);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== PAGOS DEL CONTRATO CCPA-2026-012 ===\n\n";
foreach ($rows as $row) {
    echo "ID Pago: {$row['id']}\n";
    echo "Monto: {$row['monto']}\n";
    echo "Concepto: {$row['concepto']}\n";
    echo "Estado: {$row['estado']}\n";
    echo "Recibo: {$row['recibo_url']}\n";
    echo "Creado: {$row['created_at']}\n";
    echo "---\n";
}
?>
