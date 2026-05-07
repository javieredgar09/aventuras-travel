<?php
$dbHost = 'localhost';
$dbName = 'aventuras';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser, $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Count pagos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pagos");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total de pagos: {$result['total']}\n";
    
    // Get all pagos
    $stmt = $pdo->query(
        "SELECT id, contrato_id, monto, estado, concepto, recibo_url FROM pagos LIMIT 20"
    );
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n=== PAGOS ===\n";
    foreach ($rows as $row) {
        echo "ID: {$row['id']} | Monto: {$row['monto']} | Estado: {$row['estado']} | Recibo: {$row['recibo_url']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
