<?php
/**
 * Fix Receipt Generation - Regenerate recibos with correct amounts
 * Usage: php fix_receipts.php
 */

define('BASE_PATH', dirname(__DIR__));

// Manual DB config
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
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

// Simplify Database class stub
class Database {
    private static $instance;
    private $pdo;
    
    private function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public static function setInstance($pdo) {
        self::$instance = new self($pdo);
    }
    
    public static function getInstance() {
        return self::$instance;
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function update($table, $data, $where, $params) {
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $sql = "UPDATE $table SET $set WHERE $where";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_merge(array_values($data), $params));
    }
}

Database::setInstance($pdo);

// Include needed files
require_once BASE_PATH . '/vendor/fpdf/fpdf.php';
require_once BASE_PATH . '/app/services/ReceiptService.php';

// Get all approved payments with receipts
$stmt = $pdo->prepare(
    "SELECT id, monto, recibo_url, created_at 
     FROM pagos 
     WHERE estado = 'aprobado' 
     AND recibo_url IS NOT NULL 
     ORDER BY created_at DESC"
);
$stmt->execute();
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total pagos aprobados con recibos: " . count($pagos) . "\n\n";

if (empty($pagos)) {
    die("No approved payments with receipts found.\n");
}

// Show the last few
echo "=== Últimos 5 pagos aprobados ===\n";
foreach (array_slice($pagos, 0, 5) as $p) {
    echo "ID: {$p['id']} | Monto: {$p['monto']} | Recibo: {$p['recibo_url']}\n";
}

echo "\n¿Regenerar recibos? (s/n): ";
$input = trim(fgets(STDIN));

if ($input !== 's') {
    die("Cancelado.\n");
}

$receiptService = new ReceiptService();
$regenerated = 0;

foreach ($pagos as $pago) {
    echo "Regenerando recibo para pago {$pago['id']} (monto: {$pago['monto']})... ";
    
    try {
        // Generate should work but we need to pass the actual monto
        $result = $receiptService->generate((int)$pago['id'], []);
        if ($result) {
            echo "OK ✓\n";
            $regenerated++;
        } else {
            echo "Error en generate()\n";
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Resultados ===\n";
echo "Recibos regenerados: $regenerated de " . count($pagos) . "\n";
?>
