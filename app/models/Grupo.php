<?php
class Grupo extends Model {
    protected $table = 'grupos';

    /**
     * Todos los grupos con estadísticas
     */
    public function getAllWithStats(?string $tipo = null): array {
        $where = '';
        $params = [];
        if ($tipo) {
            $where = "WHERE g.tipo = ?";
            $params[] = $tipo;
        }
        return $this->db->fetchAll(
            "SELECT g.*,
                (SELECT COUNT(*) FROM pasajeros p WHERE p.grupo_id = g.id) as total_pasajeros,
                (SELECT COUNT(*) FROM contratos c WHERE c.grupo_id = g.id) as total_contratos,
                (SELECT COALESCE(SUM(pa.monto), 0) FROM pagos pa WHERE (pa.grupo_id = g.id OR pa.contrato_id IN (SELECT c2.id FROM contratos c2 WHERE c2.grupo_id = g.id)) AND pa.estado = 'aprobado') as total_pagado
             FROM grupos g
             {$where}
             ORDER BY g.created_at DESC", $params
        );
    }

    /**
     * Detalle completo de un grupo
     */
    public function getFullDetails(int $id): ?array {
        $grupo = $this->find($id);
        if (!$grupo) return null;

        $grupo['servicios'] = $this->db->fetchAll(
            "SELECT * FROM servicios_grupo WHERE grupo_id = ? AND activo = 1", [$id]
        );
        $grupo['pasajeros'] = $this->db->fetchAll(
            "SELECT * FROM pasajeros WHERE grupo_id = ? ORDER BY tipo, nombre", [$id]
        );

        if ($grupo['tipo'] === 'colegio') {
            $grupo['contratos'] = $this->db->fetchAll(
                "SELECT c.*,
                    (SELECT COUNT(*) FROM pasajeros p WHERE p.contrato_id = c.id) as num_pasajeros,
                    (SELECT COALESCE(SUM(pa.monto), 0) FROM pagos pa WHERE pa.contrato_id = c.id AND pa.estado = 'aprobado') as pagado
                 FROM contratos c WHERE c.grupo_id = ? ORDER BY c.codigo", [$id]
            );
        }

        // Pagos del grupo (familiar) o de todos sus contratos (colegio)
        if ($grupo['tipo'] === 'familiar') {
            $grupo['pagos'] = $this->db->fetchAll(
                "SELECT * FROM pagos WHERE grupo_id = ? AND entidad_tipo = 'grupo' ORDER BY cuota_numero", [$id]
            );
        } else {
            $grupo['pagos'] = $this->db->fetchAll(
                "SELECT pa.*, c.codigo as contrato_codigo
                 FROM pagos pa
                 LEFT JOIN contratos c ON pa.contrato_id = c.id
                 WHERE pa.grupo_id = ? OR pa.contrato_id IN (SELECT id FROM contratos WHERE grupo_id = ?)
                 ORDER BY pa.created_at DESC", [$id, $id]
            );
        }

        // Calcular totales
        $grupo['total_pagado'] = array_sum(array_map(fn($p) => $p['estado'] === 'aprobado' ? (float)$p['monto'] : 0, $grupo['pagos']));
        $grupo['saldo_pendiente'] = (float)$grupo['valor_total'] - $grupo['total_pagado'];

        return $grupo;
    }

    /**
     * Estadísticas generales para dashboard
     */
    public function getDashboardStats(): array {
        $totalGrupos = $this->count();
        $familiares = $this->count("tipo = 'familiar'");
        $colegios = $this->count("tipo = 'colegio'");
        $pasajeros = $this->db->fetchOne("SELECT COUNT(*) as t FROM pasajeros WHERE grupo_id IS NOT NULL");
        $recaudado = $this->db->fetchOne("SELECT COALESCE(SUM(monto), 0) as t FROM pagos WHERE estado = 'aprobado'");
        $pendiente = $this->db->fetchOne("SELECT COALESCE(SUM(monto), 0) as t FROM pagos WHERE estado IN ('pendiente','atrasado')");

        return [
            'total_grupos'    => $totalGrupos,
            'familiares'      => $familiares,
            'colegios'        => $colegios,
            'total_pasajeros' => $pasajeros['t'] ?? 0,
            'total_recaudado' => $recaudado['t'] ?? 0,
            'saldo_pendiente' => $pendiente['t'] ?? 0,
        ];
    }

    /**
     * Datos para gráfico de pagos por mes
     */
    public function getPaymentsByMonth(): array {
        return $this->db->fetchAll(
            "SELECT DATE_FORMAT(fecha_pago, '%Y-%m') as mes,
                    SUM(CASE WHEN estado='aprobado' THEN monto ELSE 0 END) as pagado,
                    SUM(CASE WHEN estado IN ('pendiente','atrasado') THEN monto ELSE 0 END) as pendiente
             FROM pagos
             WHERE fecha_vencimiento >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY mes
             ORDER BY mes"
        );
    }

    public function getWithRepresentante(int $id): ?array {
        return $this->db->fetchOne(
            "SELECT g.*, u.nombre as rep_nombre, u.apellido as rep_apellido, u.email as rep_email
             FROM grupos g
             LEFT JOIN usuarios u ON g.representante_id = u.id
             WHERE g.id = ?", [$id]
        );
    }

    public function getByRepresentante(int $usuarioId): array {
        return $this->db->fetchAll("SELECT * FROM grupos WHERE representante_id = ?", [$usuarioId]);
    }
}
