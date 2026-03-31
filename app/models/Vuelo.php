<?php
class Vuelo extends Model {
    protected $table = 'vuelos';

    public function getByContrato(int $contratoId): array {
        return $this->where('contrato_id = ?', [$contratoId], 'fecha_salida ASC');
    }
}
