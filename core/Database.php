<?php
/**
 * Database.php - Conexión PDO Singleton
 * Aventuras Travel - Core
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = 'localhost';
        $dbname = 'aventuras';
        $username = 'root';
        $password = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
            $this->pdo->exec("SET NAMES utf8mb4");
        } catch (PDOException $e) {
            throw new RuntimeException("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Obtener instancia singleton de la base de datos
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener la conexión PDO
     */
    public function getConnection(): PDO {
        return $this->pdo;
    }

    /**
     * Ejecutar consulta con prepared statements
     */
    public function query(string $sql, array $params = []): PDOStatement {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (Exception $e) {
            $msg = "[Database::query] SQL Error: " . $e->getMessage() . "\nSQL: " . $sql . "\nParams: " . json_encode($params);
            error_log($msg);
            // También mostrar en consola cuando se ejecuta en CLI para facilitar debug
            if (php_sapi_name() === 'cli') {
                echo $msg . "\n";
            }
            throw $e;
        }
    }

    /**
     * Obtener un solo registro
     */
    public function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener múltiples registros
     */
    public function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insertar y devolver el ID
     */
    public function insert(string $table, array $data): int {
        // Filtrar solo columnas existentes en la tabla para evitar errores de esquema
        $stmtCols = $this->pdo->query("SHOW COLUMNS FROM `{$table}`");
        $colsInfo = $stmtCols->fetchAll(PDO::FETCH_ASSOC);
        $validCols = array_map(fn($c) => $c['Field'], $colsInfo ?: []);

        $filtered = array_intersect_key($data, array_flip($validCols));
        if (empty($filtered)) {
            throw new RuntimeException("Insert fallido: ningún campo válido proporcionado para la tabla {$table}");
        }

        $columns = implode(', ', array_keys($filtered));
        $placeholders = implode(', ', array_fill(0, count($filtered), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($filtered));
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Actualizar registros
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int {
        // Filtrar solo columnas existentes antes de actualizar
        $stmtCols = $this->pdo->query("SHOW COLUMNS FROM `{$table}`");
        $colsInfo = $stmtCols->fetchAll(PDO::FETCH_ASSOC);
        $validCols = array_map(fn($c) => $c['Field'], $colsInfo ?: []);
        $filtered = array_intersect_key($data, array_flip($validCols));
        if (empty($filtered)) {
            throw new RuntimeException("Update fallido: ningún campo válido proporcionado para la tabla {$table}");
        }

        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($filtered)));
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        $stmt = $this->query($sql, array_merge(array_values($filtered), $whereParams));
        return $stmt->rowCount();
    }

    /**
     * Eliminar registros
     */
    public function delete(string $table, string $where, array $params = []): int {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Contar registros
     */
    public function count(string $table, string $where = '1=1', array $params = []): int {
        $sql = "SELECT COUNT(*) as total FROM {$table} WHERE {$where}";
        $result = $this->fetchOne($sql, $params);
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Start transaction
     */
    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollBack(): bool {
        return $this->pdo->rollBack();
    }

    // Prevenir clonación y deserialización
    private function __clone() {}
    public function __wakeup() { throw new RuntimeException("Cannot unserialize singleton"); }
}
