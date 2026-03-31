<?php
class Promocion extends Model {
    protected $table = 'promociones';

    public function getActivas(): array {
        return $this->where('activa = 1 AND fecha_fin >= CURDATE()', [], 'fecha_fin ASC');
    }

    public function getAllWithStatus(): array {
        return $this->db->fetchAll(
            "SELECT *, 
                    CASE WHEN activa = 1 AND fecha_fin >= CURDATE() THEN 'active'
                         WHEN fecha_fin < CURDATE() THEN 'expired'
                         ELSE 'inactive' END as status_label,
                    DATEDIFF(fecha_fin, CURDATE()) as days_remaining
             FROM promociones ORDER BY activa DESC, fecha_fin DESC"
        );
    }
}
