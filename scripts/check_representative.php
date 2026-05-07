<?php
/**
 * check_representative.php — Verificar estado de representantes en la BD
 */
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
require BASE_PATH . '/core/Database.php';

$db = Database::getInstance();

echo "=== VERIFICACIÓN DE REPRESENTANTES ===\n\n";

// 1. Usuarios representantes
$reps = $db->fetchAll("SELECT id, codigo, nombre, apellido, email, rol FROM usuarios WHERE rol = 'representante'");
echo "[1] Usuarios con rol 'representante': " . count($reps) . "\n";
foreach ($reps as $r) {
    echo "    ID={$r['id']} | {$r['codigo']} | {$r['nombre']} {$r['apellido']} | {$r['email']}\n";
}

// 2. Grupos asociados a representantes
echo "\n[2] Grupos con representante_id:\n";
foreach ($reps as $r) {
    $grupos = $db->fetchAll("SELECT id, nombre, destino, tipo FROM grupos WHERE representante_id = ?", [$r['id']]);
    echo "    Rep {$r['codigo']} (ID={$r['id']}): " . count($grupos) . " grupo(s)\n";
    foreach ($grupos as $g) {
        echo "        Grupo #{$g['id']}: {$g['nombre']} → {$g['destino']} ({$g['tipo']})\n";
        
        // Contratos del grupo
        $contratos = $db->fetchAll("SELECT id, codigo, titular_nombre, valor_total, destino FROM contratos WHERE grupo_id = ?", [$g['id']]);
        echo "        Contratos: " . count($contratos) . "\n";
        foreach ($contratos as $c) {
            echo "            #{$c['id']} {$c['codigo']} — {$c['titular_nombre']} — \${$c['valor_total']}\n";
        }
        
        // Plan de cuotas
        foreach ($contratos as $c) {
            $cuotas = $db->fetchAll("SELECT * FROM plan_cuotas WHERE tipo_entidad = 'contrato' AND entidad_id = ?", [$c['id']]);
            echo "        Plan cuotas contrato #{$c['id']}: " . count($cuotas) . " cuotas\n";
        }
    }
}

echo "\n[3] Tabla plan_cuotas total:\n";
$total = $db->fetchOne("SELECT COUNT(*) as cnt FROM plan_cuotas");
echo "    Total registros: {$total['cnt']}\n";

echo "\nDone.\n";
