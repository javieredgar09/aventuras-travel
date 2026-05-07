<?php
$dbHost = 'localhost';
$dbName = 'aventuras';
$dbUser = 'root';
$dbPass = '';

$pdo = new PDO(
    "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
    $dbUser, $dbPass
);

// Get last 15 approved payments
$stmt = $pdo->prepare(
    "SELECT p.id, p.contrato_id, p.monto, p.estado, p.concepto, p.fecha_aprobacion, p.created_at, p.recibo_url,
            c.codigo
     FROM pagos p
     LEFT JOIN contratos c ON p.contrato_id = c.id
     WHERE p.estado = 'aprobado'
     ORDER BY p.created_at DESC LIMIT 15"
);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== ÚLTIMOS PAGOS APROBADOS ===\n\n";
foreach ($rows as $row) {
    echo "ID: {$row['id']} | Contrato: {$row['codigo']} | Monto: \${$row['monto']} | Concepto: {$row['concepto']} | Recibo: {$row['recibo_url']}\n";
}
?>
