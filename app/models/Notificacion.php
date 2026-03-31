<?php
class Notificacion extends Model {
    protected $table = 'notificaciones';

    public function getUnreadByUser(int $usuarioId): array {
        return $this->where('usuario_id = ? AND leida = 0', [$usuarioId], 'created_at DESC');
    }

    public function countUnread(int $usuarioId): int {
        return $this->count('usuario_id = ? AND leida = 0', [$usuarioId]);
    }
}
