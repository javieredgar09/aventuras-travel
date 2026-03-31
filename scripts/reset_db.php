<?php
/**
 * scripts/reset_db.php
 * Script CLI para dejar la base de datos en "cero" (vaciar datos de ventas, pagos, contratos, grupos, etc.)
 * USO: php scripts/reset_db.php --yes
 * Nota: Este script ES DESTRUCTIVO. No borra la tabla `usuarios`.
 */

if (php_sapi_name() !== 'cli') {
    echo "Ejecutar desde CLI: php scripts/reset_db.php --yes\n";
    exit(1);
}

$force = in_array('--yes', $argv, true);
if (!$force) {
    echo "Esto eliminará TODOS los datos de grupos, contratos, pasajeros, pagos, comprobantes, servicios y archivos.\n";
    echo "Si estás seguro, vuelve a ejecutar con --yes\n";
    exit(1);
}

require_once __DIR__ . '/../core/Database.php';

try {
    $dbInstance = Database::getInstance();
    $pdo = $dbInstance->getConnection();

    echo "Desactivando checks de FK...\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    $tables = [
        'comprobantes',
        'pagos',
        'servicios_grupo',
        'servicios',
        'vuelos',
        'pasajeros',
        'archivos',
        'contratos',
        'grupos',
        'clientes',
        'notificaciones',
        'promociones'
    ];

    foreach ($tables as $t) {
        echo "Truncating {$t}... ";
        $pdo->exec("TRUNCATE TABLE `{$t}`");
        echo "OK\n";
    }

    // Optional: clear storage comprobantes and vouchers
    $storagePaths = [__DIR__ . '/../storage/comprobantes', __DIR__ . '/../storage/vouchers'];
    foreach ($storagePaths as $path) {
        if (is_dir($path)) {
            echo "Limpiando archivos en {$path}...\n";
            $files = glob($path . '/*');
            foreach ($files as $f) {
                if (is_file($f)) @unlink($f);
            }
        }
    }

    echo "Reactivando checks de FK...\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    echo "Operación completa. Las tablas listadas han quedado vacías.\n";
    echo "Nota: la tabla `usuarios` NO fue tocada. Si quieres reiniciar también usuarios, notifícame.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

return 0;
