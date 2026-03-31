<?php

class Pago extends Model {
    protected $table = 'pagos';

    /**
     * Get payments by group or contract
     */
    public function getByEntidad(string $tipo, int $entidadId): array {
        return $this->where('entidad_tipo = ? AND (grupo_id = ? OR contrato_id = ?)', 
                            [$tipo, $entidadId, $entidadId], 
                            'created_at DESC');
    }

    /**
     * Get all pending payments (for admin review) with full group/contract info
     */
    public function getAwaitingReview(): array {
        return $this->db->fetchAll(
            "SELECT p.*, 
                    co.codigo as contrato_codigo,
                    co.tipo as contrato_tipo,
                    co.valor_total as contrato_valor,
                    COALESCE(co.titular_nombre, CONCAT(u.nombre, ' ', u.apellido)) as cliente_nombre,
                    g.id as group_id,
                    g.nombre as grupo_nombre,
                    g.tipo as grupo_tipo,
                    g.destino as grupo_destino
             FROM pagos p
             LEFT JOIN contratos co ON p.contrato_id = co.id
             LEFT JOIN grupos g ON COALESCE(co.grupo_id, p.grupo_id) = g.id
             LEFT JOIN clientes c ON co.cliente_id = c.id
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             WHERE p.estado = 'pendiente'
             ORDER BY g.tipo ASC, g.nombre ASC, co.codigo ASC, p.created_at ASC"
        );
    }

    /**
     * Get all transactions with full group/contract info
     */
    public function getRecentTransactions(int $limit = 50): array {
        return $this->db->fetchAll(
            "SELECT p.*, 
                    co.codigo as contrato_codigo,
                    co.tipo as contrato_tipo,
                    COALESCE(co.titular_nombre, CONCAT(u.nombre, ' ', u.apellido)) as cliente_nombre,
                    g.id as group_id,
                    g.nombre as grupo_nombre,
                    g.tipo as grupo_tipo
             FROM pagos p
             LEFT JOIN contratos co ON p.contrato_id = co.id
             LEFT JOIN grupos g ON COALESCE(co.grupo_id, p.grupo_id) = g.id
             LEFT JOIN clientes c ON co.cliente_id = c.id
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             ORDER BY g.tipo ASC, g.nombre ASC, p.created_at DESC
             LIMIT ?", [$limit]
        );
    }

    /**
     * Get monthly volume of approved payments
     */
    public function getMonthlyVolume(): float {
        $result = $this->db->fetchOne(
            "SELECT COALESCE(SUM(monto), 0) as volume FROM pagos 
             WHERE estado = 'aprobado' AND MONTH(COALESCE(fecha_aprobacion, created_at)) = MONTH(CURRENT_DATE()) AND YEAR(COALESCE(fecha_aprobacion, created_at)) = YEAR(CURRENT_DATE())"
        );
        return (float) $result['volume'];
    }

    /**
     * Get total approved for a contract
     */
    public function getTotalApprovedByContrato(int $contratoId): float {
        $result = $this->db->fetchOne(
            "SELECT COALESCE(SUM(monto), 0) as total FROM pagos WHERE contrato_id = ? AND estado = 'aprobado'",
            [$contratoId]
        );
        return (float) $result['total'];
    }

    /**
     * Get total approved globally
     */
    public function getTotalApproved(): float {
        $result = $this->db->fetchOne(
            "SELECT COALESCE(SUM(monto), 0) as total FROM pagos WHERE estado = 'aprobado'"
        );
        return (float) $result['total'];
    }
}
