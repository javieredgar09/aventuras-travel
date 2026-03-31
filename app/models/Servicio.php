<?php
class Servicio extends Model {
    protected $table = 'servicios';

    public function getByContrato(int $contratoId): array {
        return $this->where('contrato_id = ?', [$contratoId], 'fecha_inicio ASC');
    }
}
