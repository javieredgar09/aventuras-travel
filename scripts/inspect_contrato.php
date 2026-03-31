<?php
// scripts/inspect_contrato.php
// Uso: php scripts/inspect_contrato.php [contrato_id]
require_once __DIR__ . '/../core/Database.php';

$id = $argv[1] ?? null;
if (!$id) {
    echo "Uso: php scripts/inspect_contrato.php [contrato_id]\n";
    exit(1);
}

$db = Database::getInstance();

echo "Inspeccionando contrato id={$id}\n\n";

try {
    $contrato = $db->fetchOne('SELECT * FROM contratos WHERE id = ?', [(int)$id]);
    if (!$contrato) {
        echo "Contrato no encontrado.\n";
        exit(1);
    }
    echo "Contrato row:\n" . json_encode($contrato, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n\n";

    $grupo = null;
    if (!empty($contrato['grupo_id'])) {
        $grupo = $db->fetchOne('SELECT * FROM grupos WHERE id = ?', [(int)$contrato['grupo_id']]);
    }
    echo "Grupo vinculado:\n" . json_encode($grupo, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n\n";

    $cliente = null;
    if (!empty($contrato['cliente_id'])) {
        $cliente = $db->fetchOne('SELECT * FROM clientes WHERE id = ?', [(int)$contrato['cliente_id']]);
    }
    echo "Cliente vinculado:\n" . json_encode($cliente, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n\n";

    $usuario = null;
    if (!empty($cliente['usuario_id'])) {
        $usuario = $db->fetchOne('SELECT * FROM usuarios WHERE id = ?', [(int)$cliente['usuario_id']]);
    }
    echo "Usuario vinculado (cliente->usuario):\n" . json_encode($usuario, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n\n";

    $pasajeros = $db->fetchAll('SELECT * FROM pasajeros WHERE contrato_id = ? ORDER BY tipo, nombre', [(int)$id]);
    echo "Pasajeros (count=" . count($pasajeros) . "):\n" . json_encode($pasajeros, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n\n";

    $pagos = $db->fetchAll('SELECT * FROM pagos WHERE contrato_id = ? ORDER BY fecha_vencimiento', [(int)$id]);
    echo "Pagos (count=" . count($pagos) . "):\n" . json_encode($pagos, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n\n";

    $cuotas = $db->fetchAll('SELECT * FROM plan_cuotas WHERE contrato_id = ? ORDER BY numero_cuota', [(int)$id]);
    echo "Plan de cuotas (count=" . count($cuotas) . "):\n" . json_encode($cuotas, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n\n";

    $servicios = $db->fetchAll('SELECT * FROM servicios WHERE contrato_id = ? ORDER BY fecha_inicio', [(int)$id]);
    echo "Servicios (count=" . count($servicios) . "):\n" . json_encode($servicios, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n\n";

    $vuelos = $db->fetchAll('SELECT * FROM vuelos WHERE contrato_id = ? ORDER BY fecha_salida', [(int)$id]);
    echo "Vuelos (count=" . count($vuelos) . "):\n" . json_encode($vuelos, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n\n";

} catch (Exception $e) {
    echo "Error inspeccionando contrato: " . $e->getMessage() . "\n";
    exit(1);
}

exit(0);
