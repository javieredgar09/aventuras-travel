<?php

class Cuota extends Model {
    protected $table = 'plan_cuotas';

    /**
     * Get cuotas by group or contract
     */
    public function getByEntidad(string $tipo, int $entidadId): array {
        return $this->where('tipo_entidad = ? AND entidad_id = ?', [$tipo, $entidadId], 'fecha_vencimiento ASC');
    }

    /**
     * Get pending cuotas ordered by due date (for cascading payments)
     */
    public function getPendingByEntidad(string $tipo, int $entidadId): array {
        return $this->where('tipo_entidad = ? AND entidad_id = ? AND estado != ?', 
                            [$tipo, $entidadId, 'pagada'], 
                            'fecha_vencimiento ASC');
    }

    /**
     * Summarize the debt state
     */
    public function getSummary(string $tipo, int $entidadId): array {
        $result = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total_cuotas,
                SUM(monto_esperado) as suma_esperada,
                SUM(monto_pagado) as suma_pagada,
                SUM(monto_esperado - monto_pagado) as suma_pendiente
             FROM plan_cuotas 
             WHERE tipo_entidad = ? AND entidad_id = ?",
            [$tipo, $entidadId]
        );
        
        return [
            'total_cuotas' => (int)($result['total_cuotas'] ?? 0),
            'suma_esperada' => (float)($result['suma_esperada'] ?? 0),
            'suma_pagada' => (float)($result['suma_pagada'] ?? 0),
            'suma_pendiente' => (float)($result['suma_pendiente'] ?? 0),
        ];
    }
}
