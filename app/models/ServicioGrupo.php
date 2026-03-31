<?php
/**
 * ServicioGrupo.php - Servicios personalizables por grupo
 */
class ServicioGrupo extends Model {
    protected $table = 'servicios_grupo';

    public function getByGrupo(int $grupoId): array {
        return $this->where('grupo_id = ? AND activo = 1', [$grupoId]);
    }

    /**
     * Sincronizar servicios: elimina los antiguos y crea los nuevos
     */
    public function syncServicios(int $grupoId, array $servicios): void {
        // Desactivar anteriores
        $this->db->update('servicios_grupo', ['activo' => 0], 'grupo_id = ?', [$grupoId]);

        foreach ($servicios as $servicio) {
            $this->create([
                'grupo_id'      => $grupoId,
                'servicio_tipo' => $servicio['tipo'],
                'detalle_json'  => json_encode($servicio['detalle'], JSON_UNESCAPED_UNICODE),
                'activo'        => 1,
            ]);
        }
    }

    /**
     * Iconos y labels para cada tipo de servicio
     */
    public static function getServiceMeta(): array {
        return [
            'hotel'       => ['icon' => 'hotel',           'label' => 'Hotel',       'emoji' => '🏨'],
            'traslados'   => ['icon' => 'airport_shuttle', 'label' => 'Traslados',   'emoji' => '🚐'],
            'excursiones' => ['icon' => 'hiking',          'label' => 'Excursiones', 'emoji' => '⛰️'],
            'vuelos'      => ['icon' => 'flight',          'label' => 'Vuelos',      'emoji' => '✈️'],
            'seguro'      => ['icon' => 'shield',          'label' => 'Seguro',      'emoji' => '🛡️'],
        ];
    }
}
