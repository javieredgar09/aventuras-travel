<?php
class Pasajero extends Model {
    protected $table = 'pasajeros';

    public function getByContrato(int $contratoId): array {
        return $this->where('contrato_id = ?', [$contratoId], 'tipo ASC, nombre ASC');
    }
}
