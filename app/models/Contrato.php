<?php
class Contrato extends Model {
    protected $table = 'contratos';

    public function getByClienteId(int $clienteId): array {
        return $this->where('cliente_id = ?', [$clienteId], 'fecha_salida DESC');
    }

    public function getByGrupoId(int $grupoId): array {
        return $this->where('grupo_id = ?', [$grupoId], 'fecha_salida DESC');
    }

    public function getFullDetails(int $id): ?array {
        $contrato = $this->find($id);
        if (!$contrato) return null;

        $contrato['grupo'] = $this->db->fetchOne("SELECT * FROM grupos WHERE id = ?", [$contrato['grupo_id']]);
        // Determinar imagen hero para la vista: preferir imagen del grupo, luego cache local, luego buscar vía SerpApi
        $contrato['hero_image'] = null;
        $destinoQuery = $contrato['destino'] ?: ($contrato['grupo']['nombre'] ?? 'destino');
        // 1) Si el grupo tiene imagen válida (URL), usarla
        if (!empty($contrato['grupo']['imagen'])) {
            $img = $contrato['grupo']['imagen'];
            if (filter_var($img, FILTER_VALIDATE_URL)) {
                $contrato['hero_image'] = $img;
            } else {
                // si es nombre de archivo relativo dentro de storage, servirlo vía storage.php
                $localPath = STORAGE_PATH . '/' . ltrim($img, '/');
                if (file_exists($localPath)) {
                    $contrato['hero_image'] = Router::url('/storage.php') . '?f=' . ltrim($img, '/');
                }
            }
        }

        // 2) Si no hay imagen, intentar cache local por destino
        if (empty($contrato['hero_image'])) {
            $slug = preg_replace('/[^a-z0-9_-]/i', '-', strtolower(trim($destinoQuery)));
            $cacheDir = STORAGE_PATH . '/cache/serpapi';
            if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);
            $cacheFile = $cacheDir . '/' . $slug . '.jpg';
            if (file_exists($cacheFile)) {
                $contrato['hero_image'] = Router::url('/storage.php') . '?f=cache/serpapi/' . $slug . '.jpg';
            }
        }

        // 3) Si sigue vacío, intentar obtener imagen vía SerpApi y cachearla
        if (empty($contrato['hero_image'])) {
            try {
                $svc = new SerpApiService();
                $imgUrl = $svc->getFirstImage($destinoQuery);
                if ($imgUrl && filter_var($imgUrl, FILTER_VALIDATE_URL)) {
                    // Descargar y guardar en cache si es posible
                    $imgData = @file_get_contents($imgUrl);
                    if ($imgData) {
                        $slug = preg_replace('/[^a-z0-9_-]/i', '-', strtolower(trim($destinoQuery)));
                        $cacheDir = STORAGE_PATH . '/cache/serpapi';
                        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);
                        $cacheFile = $cacheDir . '/' . $slug . '.jpg';
                        @file_put_contents($cacheFile, $imgData);
                        if (file_exists($cacheFile)) {
                            $contrato['hero_image'] = Router::url('/storage.php') . '?f=cache/serpapi/' . $slug . '.jpg';
                        } else {
                            $contrato['hero_image'] = $imgUrl;
                        }
                    } else {
                        $contrato['hero_image'] = $imgUrl; // fallback remoto
                    }
                }
            } catch (Exception $e) {
                // no interrumpir flujo por fallo en la API
                error_log('SerpApi image fetch failed: ' . $e->getMessage());
            }
        }
        // Cliente / Titular information
        $contrato['cliente'] = null;
        $contrato['cliente_nombre'] = null;
        $contrato['cliente_email'] = null;
        $contrato['cliente_telefono'] = null;
        if (!empty($contrato['cliente_id'])) {
            $cliente = $this->db->fetchOne("SELECT * FROM clientes WHERE id = ?", [$contrato['cliente_id']]);
            if ($cliente) {
                $usuario = $this->db->fetchOne("SELECT * FROM usuarios WHERE id = ?", [$cliente['usuario_id'] ?? 0]);
                $contrato['cliente'] = $cliente;
                if ($usuario) {
                    $contrato['cliente_nombre'] = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''));
                    $contrato['cliente_email'] = $usuario['email'] ?? null;
                    $contrato['cliente_telefono'] = $usuario['telefono'] ?? null;
                }
            }
        }

        // Si no existe cliente pero el contrato tiene campos de titular (titular_nombre, titular_correo, titular_telefono), usarlos
        if (empty($contrato['cliente_nombre']) && !empty($contrato['titular_nombre'])) {
            $contrato['cliente_nombre'] = $contrato['titular_nombre'];
        }
        if (empty($contrato['cliente_email']) && !empty($contrato['titular_correo'])) {
            $contrato['cliente_email'] = $contrato['titular_correo'];
        }
        if (empty($contrato['cliente_telefono']) && !empty($contrato['titular_telefono'])) {
            $contrato['cliente_telefono'] = $contrato['titular_telefono'];
        }

        // Plan de cuotas si existe
        $contrato['plan_cuotas'] = $this->db->fetchAll("SELECT * FROM plan_cuotas WHERE tipo_entidad = 'contrato' AND entidad_id = ? ORDER BY numero_cuota", [$id]);
        $contrato['servicios_grupo'] = $this->db->fetchAll(
            "SELECT * FROM servicios_grupo WHERE grupo_id = ? AND activo = 1", [$contrato['grupo_id']]
        );

        $contrato['pasajeros'] = $this->db->fetchAll(
            "SELECT * FROM pasajeros WHERE contrato_id = ? ORDER BY tipo, nombre", [$id]
        );
        $contrato['vuelos'] = $this->db->fetchAll(
            "SELECT * FROM vuelos WHERE contrato_id = ? ORDER BY fecha_salida", [$id]
        );
        $contrato['servicios'] = $this->db->fetchAll(
            "SELECT * FROM servicios WHERE contrato_id = ? ORDER BY fecha_inicio", [$id]
        );
        $contrato['pagos'] = $this->db->fetchAll(
            "SELECT * FROM pagos WHERE contrato_id = ? ORDER BY fecha_vencimiento", [$id]
        );

        // Calcular total_pagado sumando pagos aprobados
        $sumAprobados = $this->db->fetchOne(
            "SELECT COALESCE(SUM(monto), 0) as total FROM pagos WHERE contrato_id = ? AND estado = 'aprobado'", [$id]
        );
        $contrato['total_pagado'] = (float)($sumAprobados['total'] ?? 0);

        // Moneda: usar 'PEN' por defecto si no está definida
        if (empty($contrato['moneda'])) {
            $contrato['moneda'] = 'PEN';
        }

        return $contrato;
    }

    public function getRecentWithClient(int $limit = 10): array {
        return $this->db->fetchAll(
            "SELECT co.*, 
                    COALESCE(CONCAT(u.nombre, ' ', u.apellido), g.nombre) as cliente_nombre,
                    u.codigo as cliente_codigo
             FROM contratos co
             LEFT JOIN clientes c ON co.cliente_id = c.id
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             LEFT JOIN grupos g ON co.grupo_id = g.id
             ORDER BY co.created_at DESC LIMIT ?", [$limit]
        );
    }

    public function getStats(): array {
        $activeCount = $this->count("estado = 'activo'");
        $totalCount = $this->count();
        
        $pasajeros = $this->db->fetchOne("SELECT COUNT(*) as total FROM pasajeros");
        $recaudado = $this->db->fetchOne("SELECT COALESCE(SUM(monto), 0) as total FROM pagos WHERE estado = 'aprobado'");
        $pendiente = $this->db->fetchOne("SELECT COALESCE(SUM(monto), 0) as total FROM pagos WHERE estado != 'aprobado'");

        return [
            'total_contratos'   => $totalCount,
            'contratos_activos' => $activeCount,
            'total_pasajeros'   => $pasajeros['total'] ?? 0,
            'total_recaudado'   => $recaudado['total'] ?? 0,
            'saldo_pendiente'   => $pendiente['total'] ?? 0,
        ];
    }
}
