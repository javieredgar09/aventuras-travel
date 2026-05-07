<?php
/**
 * diagnose_payment_issue.php - Diagnostica el problema de montos en recibos
 */

$dbHost = 'localhost';
$dbName = 'aventuras';
$dbUser = 'root';
$dbPass = '';

$pdo = new PDO(
    "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
    $dbUser, $dbPass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "=== DIAGNÓSTICO DE SISTEMA DE PAGOS ===\n\n";

// Get contract
$stmt = $pdo->prepare(
    "SELECT id, codigo, valor_total FROM contratos WHERE codigo LIKE ? LIMIT 1"
);
$stmt->execute(['%CCPA-2026%']);
$contrato = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contrato) {
    echo "No se encontró el contrato CCPA-2026-012\n";
    echo "Buscando cualquier contrato escolar:\n";
    $stmt = $pdo->query("SELECT id, codigo, valor_total FROM contratos WHERE tipo = 'grupal' LIMIT 1");
    $contrato = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$contrato) {
    die("No hay contratos en la base de datos\n");
}

$cid = $contrato['id'];
echo "Contrato: {$contrato['codigo']}\n";
echo "Valor Total: \${$contrato['valor_total']}\n";
echo "---\n\n";

// Get all payments for this contract
echo "=== PAGOS DEL CONTRATO ===\n";
$stmt = $pdo->prepare(
    "SELECT id, monto, estado, concepto, fecha_pago, fecha_aprobacion, recibo_url, excedente, created_at
     FROM pagos 
     WHERE contrato_id = ?
     ORDER BY created_at ASC"
);
$stmt->execute([$cid]);
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($pagos)) {
    echo "No hay pagos registrados\n";
} else {
    $montoTotal = 0;
    foreach ($pagos as $i => $p) {
        echo "\nPago #" . ($i + 1) . ":\n";
        echo "  ID: {$p['id']}\n";
        echo "  Monto: \${$p['monto']}\n";
        echo "  Concepto: {$p['concepto']}\n";
        echo "  Estado: {$p['estado']}\n";
        echo "  Recibo: {$p['recibo_url']}\n";
        echo "  Excedente: {$p['excedente']}\n";
        echo "  Creado: {$p['created_at']}\n";
        $montoTotal += (float)$p['monto'];
    }
    echo "\n---\n";
    echo "Total registrado: \$$montoTotal\n";
}

// Get cuotas summary
echo "\n=== CUOTAS DEL CONTRATO ===\n";
$stmt = $pdo->prepare(
    "SELECT numero_cuota, monto_esperado, monto_pagado, estado
     FROM plan_cuotas
     WHERE tipo_entidad = 'contrato' AND entidad_id = ?
     ORDER BY numero_cuota ASC"
);
$stmt->execute([$cid]);
$cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cuotas)) {
    echo "No hay cuotas registradas\n";
} else {
    $cuotaTotalEsperado = 0;
    $cuotaTotalPagado = 0;
    foreach ($cuotas as $c) {
        echo "Cuota {$c['numero_cuota']}: Esperado \${$c['monto_esperado']} | Pagado \${$c['monto_pagado']} | Estado: {$c['estado']}\n";
        $cuotaTotalEsperado += (float)$c['monto_esperado'];
        $cuotaTotalPagado += (float)$c['monto_pagado'];
    }
    echo "\nTotales cuotas:\n";
    echo "  Total Esperado: \$$cuotaTotalEsperado\n";
    echo "  Total Pagado en Cuotas: \$$cuotaTotalPagado\n";
    echo "  Saldo en Cuotas: \$" . max(0, $cuotaTotalEsperado - $cuotaTotalPagado) . "\n";
}

// Get saldo from contract
echo "\n=== SALDO EN CONTRATO ===\n";
$stmt = $pdo->prepare("SELECT saldo FROM contratos WHERE id = ?");
$stmt->execute([$cid]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Saldo registrado en contrato: \${$result['saldo']}\n";

// Calculate expected saldo
$totalPagosAprobados = array_reduce($pagos, function($carry, $p) {
    return $carry + (($p['estado'] === 'aprobado') ? (float)$p['monto'] : 0);
}, 0);
$expectedSaldo = max(0, (float)$contrato['valor_total'] - $totalPagosAprobados);
echo "Saldo calculado (valor_total - pagos_aprobados): \$$expectedSaldo\n";

if (abs($expectedSaldo - (float)$result['saldo']) > 0.01) {
    echo "\n⚠️  INCONSISTENCIA DETECTADA\n";
    echo "   El saldo en contrato no coincide con el cálculo esperado\n";
}

echo "\n=== PROBLEMA IDENTIFICADO ===\n";
if (!empty($pagos)) {
    $lastPago = end($pagos);
    echo "Último pago (ID {$lastPago['id']}): \${$lastPago['monto']}\n";
    echo "Recibo: {$lastPago['recibo_url']}\n";
    if ($lastPago['recibo_url']) {
        echo "✓ Recibo generado\n";
        echo "El recibo debe mostrar \${$lastPago['monto']} como 'TOTAL PAGADO'\n";
    }
}
?>
