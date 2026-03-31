<?php

class Voucher extends Model {
    protected $table = 'vouchers';

    /**
     * Get vouchers by group or contract
     */
    public function getByEntidad(string $tipo, int $entidadId): array {
        return $this->where('tipo_entidad = ? AND entidad_id = ?', [$tipo, $entidadId], 'fecha_subida DESC');
    }
}
