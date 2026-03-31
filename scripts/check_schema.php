<?php
// scripts/check_schema.php - imprime columnas y conteos para diagnóstico de esquema
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/core/Database.php';
try {
    $db = Database::getInstance();
    $tables = ['usuarios','clientes','grupos','contratos','servicios_grupo','servicios','vuelos'];
    foreach ($tables as $t) {
        echo "--- TABLE: {$t} ---\n";
        try {
            $cols = $db->fetchAll("SHOW COLUMNS FROM {$t}");
            if ($cols === false || empty($cols)) {
                echo "(no columns or table missing)\n";
            } else {
                foreach ($cols as $c) {
                    echo $c['Field'] . "\t" . $c['Type'] . "\t" . ($c['Null'] ?? '') . "\t" . ($c['Key'] ?? '') . "\n";
                }
            }
            $cnt = $db->fetchOne("SELECT COUNT(*) as c FROM {$t}");
            echo "COUNT: " . ($cnt['c'] ?? 'N/A') . "\n";
        } catch (Exception $e) {
            echo "Error reading table {$t}: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    echo "--- INFORMATION_SCHEMA check for usuarios.email ---\n";
    try {
        $rows = $db->fetchAll("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios'");
        $cols = array_map(fn($r) => $r['COLUMN_NAME'], $rows ?: []);
        echo "usuarios columns: " . implode(', ', $cols) . "\n";
        echo "email present: " . (in_array('email', $cols) ? 'YES' : 'NO') . "\n";
    } catch (Exception $e) {
        echo "Error querying INFORMATION_SCHEMA: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "Fallo al obtener instancia DB: " . $e->getMessage() . "\n";
}
