<?php
// scripts/fix_schema.php
// Comprueba y aplica cambios mínimos al esquema para evitar errores por columnas/enum faltantes.
// Ejecuta con: php scripts/fix_schema.php

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/core/Database.php';

$db = Database::getInstance();

function columnExists($db, $table, $column) {
    $row = $db->fetchOne("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?", [$table, $column]);
    return !empty($row);
}

function getColumnType($db, $table, $column) {
    $row = $db->fetchOne("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?", [$table, $column]);
    return $row['COLUMN_TYPE'] ?? null;
}

function safeAlter($db, $sql) {
    try {
        $db->query($sql);
        echo "OK: {$sql}\n";
    } catch (Exception $e) {
        echo "ERROR: {$sql} -> " . $e->getMessage() . "\n";
    }
}

echo "=== Verificando columnas esenciales ===\n";
// usuarios.email
if (!columnExists($db, 'usuarios', 'email')) {
    echo "Añadiendo columna usuarios.email...\n";
    safeAlter($db, "ALTER TABLE usuarios ADD COLUMN email VARCHAR(150) DEFAULT NULL");
} else {
    echo "usuarios.email existe.\n";
}

// usuarios.telefono
if (!columnExists($db, 'usuarios', 'telefono')) {
    echo "Añadiendo columna usuarios.telefono...\n";
    safeAlter($db, "ALTER TABLE usuarios ADD COLUMN telefono VARCHAR(20) DEFAULT NULL");
} else {
    echo "usuarios.telefono existe.\n";
}

// contratos titular fields
$contratoCols = ['titular_nombre','titular_correo','titular_telefono','fecha_firma','total_cuotas','meses_pago','tipo_pago'];
foreach ($contratoCols as $col) {
    if (!columnExists($db, 'contratos', $col)) {
        echo "Añadiendo contratos.{$col}...\n";
        switch ($col) {
            case 'titular_nombre': safeAlter($db, "ALTER TABLE contratos ADD COLUMN titular_nombre VARCHAR(200) DEFAULT NULL"); break;
            case 'titular_correo': safeAlter($db, "ALTER TABLE contratos ADD COLUMN titular_correo VARCHAR(200) DEFAULT NULL"); break;
            case 'titular_telefono': safeAlter($db, "ALTER TABLE contratos ADD COLUMN titular_telefono VARCHAR(50) DEFAULT NULL"); break;
            case 'fecha_firma': safeAlter($db, "ALTER TABLE contratos ADD COLUMN fecha_firma DATE DEFAULT NULL"); break;
            case 'total_cuotas': safeAlter($db, "ALTER TABLE contratos ADD COLUMN total_cuotas INT DEFAULT 0"); break;
            case 'meses_pago': safeAlter($db, "ALTER TABLE contratos ADD COLUMN meses_pago VARCHAR(255) DEFAULT NULL"); break;
            case 'tipo_pago': safeAlter($db, "ALTER TABLE contratos ADD COLUMN tipo_pago VARCHAR(50) DEFAULT NULL"); break;
        }
    } else {
        echo "contratos.{$col} existe.\n";
    }
}

echo "=== Normalizando ENUMs / valores esperados ===\n";
// usuarios.rol debe incluir cliente_colegio
$rolType = getColumnType($db, 'usuarios', 'rol');
if ($rolType && strpos($rolType, 'cliente_colegio') === false) {
    echo "Modificando usuarios.rol para incluir 'cliente_colegio'...\n";
    // Construir nuevo enum: mantener valores actuales y añadir cliente_colegio
    // Leer valores actuales
    preg_match_all("/'([^']+)'/", $rolType, $m);
    $values = $m[1] ?? [];
    if (!in_array('cliente_colegio', $values)) $values[] = 'cliente_colegio';
    $valsSql = implode("','", $values);
    $sql = "ALTER TABLE usuarios MODIFY COLUMN rol ENUM('{$valsSql}') NOT NULL DEFAULT 'cliente_familiar'";
    safeAlter($db, $sql);
} else {
    echo "usuarios.rol ya incluye cliente_colegio o no se pudo leer tipo.\n";
}

// contratos.tipo incluir 'colegio'
$tipoC = getColumnType($db, 'contratos', 'tipo');
if ($tipoC && strpos($tipoC, 'colegio') === false) {
    echo "Modificando contratos.tipo para incluir 'colegio'...\n";
    preg_match_all("/'([^']+)'/", $tipoC, $m);
    $values = $m[1] ?? [];
    if (!in_array('colegio', $values)) $values[] = 'colegio';
    $valsSql = implode("','", $values);
    $sql = "ALTER TABLE contratos MODIFY COLUMN tipo ENUM('{$valsSql}') NOT NULL";
    safeAlter($db, $sql);
} else {
    echo "contratos.tipo ya incluye colegio o no se pudo leer tipo.\n";
}

// clientes.tipo incluir 'colegio'
$tipoCl = getColumnType($db, 'clientes', 'tipo');
if ($tipoCl && strpos($tipoCl, 'colegio') === false) {
    echo "Modificando clientes.tipo para incluir 'colegio'...\n";
    preg_match_all("/'([^']+)'/", $tipoCl, $m);
    $values = $m[1] ?? [];
    if (!in_array('colegio', $values)) $values[] = 'colegio';
    $valsSql = implode("','", $values);
    $sql = "ALTER TABLE clientes MODIFY COLUMN tipo ENUM('{$valsSql}') NOT NULL";
    safeAlter($db, $sql);
} else {
    echo "clientes.tipo ya incluye colegio o no se pudo leer tipo.\n";
}


echo "=== Comprobación final de columnas 'email' ===\n";
try {
    $rows = $db->fetchAll("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios'");
    $cols = array_map(fn($r) => $r['COLUMN_NAME'], $rows ?: []);
    echo "usuarios columns: " . implode(', ', $cols) . "\n";
} catch (Exception $e) {
    echo "Error leyendo INFORMATION_SCHEMA: " . $e->getMessage() . "\n";
}

echo "=== Terminado ===\n";
