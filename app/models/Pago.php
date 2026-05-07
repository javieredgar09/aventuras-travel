<?php

class Pago extends Model {
    protected $table = 'pagos';

    /**
     * Get payments by contrato_id (primary lookup)
     * Falls back to grupo_id if contrato_id not set
     */
    public function getByEntidad(string $tipo, int $entidadId): array {
        if ($tipo === 'contrato') {
            return $this->where('contrato_id = ?', [$entidadId], 'created_at DESC');
        }
        // For 'grupo': get all pagos linked to any contract of that group
        return $this->db->fetchAll(
            "SELECT p.* FROM pagos p
             LEFT JOIN contratos co ON p.contrato_id = co.id
             WHERE co.grupo_id = ? OR p.grupo_id = ?
             ORDER BY p.created_at DESC",
            [$entidadId, $entidadId]
        );
    }

    /**
     * Get all pending payments (for admin review) with full group/contract/client info
     */
    public function getAwaitingReview(): array {
        return $this->db->fetchAll(
            "SELECT p.*,
                    co.codigo       AS contrato_codigo,
                    co.tipo         AS contrato_tipo,
                    co.valor_total  AS contrato_valor,
                    co.titular_nombre,
                    co.titular_correo,
                    co.titular_telefono,
                    COALESCE(
                        co.titular_nombre,
                        CONCAT(u.nombre, ' ', u.apellido),
                        'Sin nombre'
                    )               AS cliente_nombre,
                    COALESCE(u.email, co.titular_correo) AS cliente_email,
                    g.id            AS group_id,
                    g.nombre        AS grupo_nombre,
                    g.tipo          AS grupo_tipo,
                    g.destino       AS grupo_destino
             FROM pagos p
             LEFT JOIN contratos co ON p.contrato_id = co.id
             LEFT JOIN grupos g    ON COALESCE(co.grupo_id, p.grupo_id) = g.id
             LEFT JOIN clientes c  ON co.cliente_id = c.id
             LEFT JOIN usuarios u  ON c.usuario_id = u.id
             WHERE p.estado = 'pendiente'
             ORDER BY p.created_at ASC"
        );
    }

    /**
     * Get all transactions with full group/contract info
     */
    public function getRecentTransactions(int $limit = 50): array {
        return $this->db->fetchAll(
            "SELECT p.*,
                    co.codigo       AS contrato_codigo,
                    co.tipo         AS contrato_tipo,
                    COALESCE(
                        co.titular_nombre,
                        CONCAT(u.nombre, ' ', u.apellido),
                        'Sin nombre'
                    )               AS cliente_nombre,
                    g.id            AS group_id,
                    g.nombre        AS grupo_nombre,
                    g.tipo          AS grupo_tipo
             FROM pagos p
             LEFT JOIN contratos co ON p.contrato_id = co.id
             LEFT JOIN grupos g    ON COALESCE(co.grupo_id, p.grupo_id) = g.id
             LEFT JOIN clientes c  ON co.cliente_id = c.id
             LEFT JOIN usuarios u  ON c.usuario_id = u.id
             ORDER BY p.created_at DESC
             LIMIT ?", [$limit]
        );
    }

    /**
     * Get monthly volume of approved payments
     */
    public function getMonthlyVolume(): float {
        $result = $this->db->fetchOne(
            "SELECT COALESCE(SUM(monto), 0) as volume FROM pagos
             WHERE estado = 'aprobado'
               AND MONTH(COALESCE(fecha_aprobacion, fecha_pago, created_at)) = MONTH(CURRENT_DATE())
               AND YEAR(COALESCE(fecha_aprobacion, fecha_pago, created_at))  = YEAR(CURRENT_DATE())"
        );
        return (float) $result['volume'];
    }

    /**
     * Get total approved for a contract
     */
    public function getTotalApprovedByContrato(int $contratoId): float {
        $result = $this->db->fetchOne(
            "SELECT COALESCE(SUM(monto), 0) as total FROM pagos
             WHERE contrato_id = ? AND estado = 'aprobado'",
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

    /**
     * Get pending payments count for a contract
     */
    public function countPendingByContrato(int $contratoId): int {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt FROM pagos WHERE contrato_id = ? AND estado = 'pendiente'",
            [$contratoId]
        );
        return (int) ($result['cnt'] ?? 0);
    }
}
