<?php
require __DIR__ . '/../core/Database.php';
define('DB_HOST','localhost');
define('DB_NAME','aventuras');
define('DB_USER','root');
define('DB_PASS','');

$db = Database::getInstance();

// 1. Add columns
try {
    $db->query("ALTER TABLE pagos ADD COLUMN banco VARCHAR(100) DEFAULT NULL AFTER metodo_pago, ADD COLUMN moneda_pago ENUM('PEN','USD') DEFAULT 'PEN' AFTER banco");
    echo "Columns added OK\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Columns already exist\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// 2. Update existing pagos with realistic payment details
$updates = [
    // Contrato 1 - Rodríguez
    [1, 'Transferencia bancaria', 'BCP', 'USD'],
    [2, 'Transferencia bancaria', 'BBVA Continental', 'PEN'],
    [3, 'Depósito bancario', 'Interbank', 'PEN'],

    // Contrato 2 - García
    [7, 'Transferencia bancaria', 'BCP', 'USD'],

    // Contrato 3 - López
    [8, 'Depósito bancario', 'Scotiabank', 'PEN'],
];

foreach ($updates as $u) {
    $db->query(
        "UPDATE pagos SET metodo_pago = ?, banco = ?, moneda_pago = ? WHERE id = ?",
        [$u[1], $u[2], $u[3], $u[0]]
    );
}

// Update remaining pagos that have NULL method
$db->query("UPDATE pagos SET metodo_pago = 'Efectivo', banco = NULL, moneda_pago = 'PEN' WHERE metodo_pago IS NULL OR metodo_pago = ''");

echo "Payment details updated OK\n";

// 3. Insert sample archivos (contracts) for existing contratos
$archivos = [
    [1, 'contrato', 'Contrato_AV-2026-001.pdf', 'contract_001_sample.pdf', 'contratos/contract_001_sample.pdf', 'application/pdf', 102400],
    [2, 'contrato', 'Contrato_AV-2026-002.pdf', 'contract_002_sample.pdf', 'contratos/contract_002_sample.pdf', 'application/pdf', 98304],
];

foreach ($archivos as $a) {
    $exists = $db->fetchOne("SELECT id FROM archivos WHERE contrato_id = ? AND tipo = 'contrato'", [$a[0]]);
    if (!$exists) {
        $db->query(
            "INSERT INTO archivos (contrato_id, tipo, nombre_original, nombre_hash, ruta, mime_type, tamano, subido_por) VALUES (?, ?, ?, ?, ?, ?, ?, 1)",
            [$a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6]]
        );
    }
}
echo "Sample archivos inserted OK\n";

// 4. Insert sample vouchers for contratos
$vouchers = [
    [1, 'contrato', 'hotel', 'Voucher Hotel Hard Rock Punta Cana', 'voucher_hotel_001.pdf'],
    [1, 'contrato', 'seguro', 'Póliza Travel Guard Plus', 'voucher_seguro_001.pdf'],
    [2, 'contrato', 'hotel', 'Voucher Hotel Cancún Resort', 'voucher_hotel_002.pdf'],
];

foreach ($vouchers as $v) {
    $exists = $db->fetchOne("SELECT id FROM vouchers WHERE entidad_id = ? AND tipo_voucher = ?", [$v[0], $v[2]]);
    if (!$exists) {
        $db->query(
            "INSERT INTO vouchers (entidad_id, tipo_entidad, tipo_voucher, titulo, archivo_url) VALUES (?, ?, ?, ?, ?)",
            [$v[0], $v[1], $v[2], $v[3], $v[4]]
        );
    }
}
echo "Sample vouchers inserted OK\n";

echo "\nDone!\n";
