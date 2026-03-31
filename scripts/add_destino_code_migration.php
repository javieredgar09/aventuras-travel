<?php
// scripts/add_destino_code_migration.php
// Añade columna destino_code a tablas grupos y contratos si no existen

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/core/Database.php';

$db = Database::getInstance();
$schema = 'aventuras';
$targets = [
    ['table' => 'grupos', 'column' => 'destino_code', 'sql' => "ALTER TABLE grupos ADD COLUMN destino_code VARCHAR(200) DEFAULT NULL"],
    ['table' => 'contratos', 'column' => 'destino_code', 'sql' => "ALTER TABLE contratos ADD COLUMN destino_code VARCHAR(200) DEFAULT NULL"],
];

foreach ($targets as $t) {
    try {
        $row = $db->fetchOne("SELECT COUNT(*) as cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?", [$schema, $t['table'], $t['column']]);
        $cnt = (int) ($row['cnt'] ?? 0);
        if ($cnt > 0) {
            echo "La columna {$t['column']} ya existe en {$t['table']}.\n";
            continue;
        }

        echo "Agregando columna {$t['column']} a {$t['table']}... ";
        $db->query($t['sql']);
        echo "OK\n";
    } catch (Exception $e) {
        echo "Error aplicando cambio en {$t['table']}: " . $e->getMessage() . "\n";
    }
}

echo "Migración finalizada. Verifica con phpmyadmin o con SELECT * FROM grupos LIMIT 1;\n";
