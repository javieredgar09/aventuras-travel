<?php
class Cliente extends Model {
    protected $table = 'clientes';

    public function findByUsuarioId(int $usuarioId): ?array {
        return $this->findWhere('usuario_id = ?', [$usuarioId]);
    }

    public function getWithUsuario(int $id): ?array {
        return $this->db->fetchOne(
            "SELECT c.*, u.nombre, u.apellido, u.email, u.telefono, u.codigo
             FROM clientes c
             JOIN usuarios u ON c.usuario_id = u.id
             WHERE c.id = ?", [$id]
        );
    }

    public function getAllWithUsuario(): array {
        return $this->db->fetchAll(
            "SELECT c.*, u.nombre, u.apellido, u.email, u.telefono, u.codigo, u.rol
             FROM clientes c
             JOIN usuarios u ON c.usuario_id = u.id
             ORDER BY c.created_at DESC"
        );
    }
}
