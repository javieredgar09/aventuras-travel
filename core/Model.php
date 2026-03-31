<?php
/**
 * Model.php - Base model con CRUD genérico
 * Aventuras Travel - Core
 */
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Ejecutar una consulta que retorna una fila (helper para consultas ad-hoc)
     */
    public function fetchOneRaw(string $sql, array $params = []): ?array {
        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Ejecutar una consulta que retorna múltiples filas (helper para consultas ad-hoc)
     */
    public function fetchAllRaw(string $sql, array $params = []): array {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Encontrar por ID
     */
    public function find(int $id): ?array {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    /**
     * Obtener todos los registros
     */
    public function all(string $orderBy = 'id DESC', int $limit = 100): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY {$orderBy} LIMIT ?",
            [$limit]
        );
    }

    /**
     * Buscar por condición
     */
    public function where(string $condition, array $params = [], string $orderBy = 'id DESC'): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$condition} ORDER BY {$orderBy}",
            $params
        );
    }

    /**
     * Buscar primero por condición
     */
    public function findWhere(string $condition, array $params = []): ?array {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE {$condition} LIMIT 1",
            $params
        );
    }

    /**
     * Crear registro
     */
    public function create(array $data): int {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Actualizar registro por ID
     */
    public function update(int $id, array $data): int {
        return $this->db->update($this->table, $data, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Eliminar registro por ID
     */
    public function destroy(int $id): int {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Contar registros
     */
    public function count(string $where = '1=1', array $params = []): int {
        return $this->db->count($this->table, $where, $params);
    }

    /**
     * Paginación
     */
    public function paginate(int $page = 1, int $perPage = 10, string $where = '1=1', array $params = [], string $orderBy = 'id DESC'): array {
        $total = $this->count($where, $params);
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy} LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];
    }
}
