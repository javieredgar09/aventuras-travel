<?php
require __DIR__ . '/../core/Database.php';
define('DB_HOST','localhost');
define('DB_NAME','aventuras');
define('DB_USER','root');
define('DB_PASS','');
$db = Database::getInstance();

echo "=== PAGOS TABLE ===\n";
$cols = $db->fetchAll("SHOW COLUMNS FROM pagos");
foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . " | " . ($c['Default'] ?? 'NULL') . "\n";

echo "\n=== COMPROBANTES TABLE ===\n";
$cols = $db->fetchAll("SHOW COLUMNS FROM comprobantes");
foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . " | " . ($c['Default'] ?? 'NULL') . "\n";

echo "\n=== VOUCHERS TABLE ===\n";
$cols = $db->fetchAll("SHOW COLUMNS FROM vouchers");
foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . " | " . ($c['Default'] ?? 'NULL') . "\n";

echo "\n=== ARCHIVOS TABLE ===\n";
$cols = $db->fetchAll("SHOW COLUMNS FROM archivos");
foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . " | " . ($c['Default'] ?? 'NULL') . "\n";

echo "\n=== SAMPLE PAGOS (5) ===\n";
$rows = $db->fetchAll("SELECT * FROM pagos LIMIT 5");
foreach ($rows as $r) print_r($r);

echo "\n=== SAMPLE COMPROBANTES ===\n";
$rows = $db->fetchAll("SELECT * FROM comprobantes LIMIT 5");
foreach ($rows as $r) print_r($r);

echo "\n=== SAMPLE VOUCHERS ===\n";
$rows = $db->fetchAll("SELECT * FROM vouchers LIMIT 5");
foreach ($rows as $r) print_r($r);

echo "\n=== SAMPLE ARCHIVOS ===\n";
$rows = $db->fetchAll("SELECT * FROM archivos LIMIT 5");
foreach ($rows as $r) print_r($r);

echo "\n=== PLAN_CUOTAS TABLE ===\n";
try {
    $cols = $db->fetchAll("SHOW COLUMNS FROM plan_cuotas");
    foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . " | " . ($c['Default'] ?? 'NULL') . "\n";
} catch (Exception $e) {
    echo "Table not found\n";
}
