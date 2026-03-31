<?php
/**
 * Script seguro para detectar y corregir columnas `contrato_id` faltantes
 * en tablas que el sistema espera (pasajeros, vuelos, servicios, pagos, archivos)
 * y crear la tabla `plan_cuotas` si no existe.
 * Ejecución: php scripts/fix_missing_contrato_columns.php
 */

require_once __DIR__ . '/../core/Database.php';

echo "Iniciando verificación de columnas contrato_id...\n";
$db = Database::getInstance();
$pdo = $db->getConnection();
$schema = 'aventuras';

$tables = ['pasajeros','vuelos','servicios','pagos','archivos'];

function columnExists(PDO $pdo, string $schema, string $table, string $column): bool {
    $sql = "SELECT COUNT(*) as c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$schema, $table, $column]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    return ((int)($r['c'] ?? 0)) > 0;
}

function tableExists(PDO $pdo, string $schema, string $table): bool {
    $sql = "SELECT COUNT(*) as c FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$schema, $table]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    return ((int)($r['c'] ?? 0)) > 0;
}

foreach ($tables as $t) {
    try {
        if (!tableExists($pdo, $schema, $t)) {
            echo "Aviso: la tabla {$t} no existe — omitiendo.\n";
            continue;
        }

        if (!columnExists($pdo, $schema, $t, 'contrato_id')) {
            echo "Agregando columna contrato_id a {$t}...\n";
            $pdo->exec("ALTER TABLE `{$t}` ADD COLUMN `contrato_id` INT UNSIGNED NULL AFTER `id`");
            echo "  - columna agregada\n";
            // Índice
            try {
                $pdo->exec("ALTER TABLE `{$t}` ADD INDEX `idx_contrato` (`contrato_id`)");
                echo "  - índice idx_contrato agregado\n";
            } catch (Exception $ie) {
                echo "  - no se pudo agregar índice (ya existe?): " . $ie->getMessage() . "\n";
            }
            // FK (intentar agregar si la tabla contratos existe)
            if (tableExists($pdo, $schema, 'contratos')) {
                try {
                    $fk = "fk_{$t}_contrato";
                    $pdo->exec("ALTER TABLE `{$t}` ADD CONSTRAINT `{$fk}` FOREIGN KEY (`contrato_id`) REFERENCES `contratos`(`id`) ON DELETE CASCADE");
                    echo "  - FK {$fk} agregada\n";
                } catch (Exception $fe) {
                    echo "  - no se pudo agregar FK (posible FK existente): " . $fe->getMessage() . "\n";
                }
            }
        } else {
            echo "La tabla {$t} ya tiene columna contrato_id.\n";
        }
    } catch (Exception $e) {
        echo "Error procesando tabla {$t}: " . $e->getMessage() . "\n";
    }
}

// Verificar / crear plan_cuotas
if (!tableExists($pdo, $schema, 'plan_cuotas')) {
    echo "Tabla plan_cuotas no existe. Creando tabla plan_cuotas...\n";
    $sqlCreate = <<<SQL
CREATE TABLE `plan_cuotas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `contrato_id` INT UNSIGNED NOT NULL,
  `numero_cuota` INT NOT NULL DEFAULT 1,
  `monto` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `fecha_vencimiento` DATE DEFAULT NULL,
  `estado` ENUM('pendiente','pagado','vencido') NOT NULL DEFAULT 'pendiente',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_contrato` (`contrato_id`),
  FOREIGN KEY (`contrato_id`) REFERENCES `contratos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
    try {
        $pdo->exec($sqlCreate);
        echo "  - plan_cuotas creada correctamente.\n";
    } catch (Exception $e) {
        echo "  - error creando plan_cuotas: " . $e->getMessage() . "\n";
    }
} else {
    echo "La tabla plan_cuotas ya existe. Verificando columna contrato_id...\n";
    if (!columnExists($pdo, $schema, 'plan_cuotas', 'contrato_id')) {
        try {
            $pdo->exec("ALTER TABLE `plan_cuotas` ADD COLUMN `contrato_id` INT UNSIGNED NULL AFTER `id`");
            $pdo->exec("ALTER TABLE `plan_cuotas` ADD INDEX `idx_contrato` (`contrato_id`)");
            $pdo->exec("ALTER TABLE `plan_cuotas` ADD CONSTRAINT `fk_plan_cuotas_contrato` FOREIGN KEY (`contrato_id`) REFERENCES `contratos`(`id`) ON DELETE CASCADE");
            echo "  - columna contrato_id agregada a plan_cuotas.\n";
        } catch (Exception $e) {
            echo "  - no se pudo modificar plan_cuotas: " . $e->getMessage() . "\n";
        }
    } else {
        echo "  - plan_cuotas ya tiene contrato_id.\n";
    }
}

echo "Verificación completa. Si hubo errores, revísalos y pégame la salida.\n";

return 0;

?>
