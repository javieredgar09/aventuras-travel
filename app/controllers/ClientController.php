<?php
/**
 * ClientController.php - Dashboard de clientes (familiar, grupal, representante)
 */
class ClientController extends Controller {

    /**
     * Dashboard principal del cliente
     */
    public function dashboard(): void {
        $user = $this->auth();
        $contratoModel = new Contrato();
        $clienteModel = new Cliente();
        $notifModel = new Notificacion();

        $cliente = $clienteModel->findByUsuarioId($user['id']);
        $contrato = null;
        $contratos = [];
        $vouchers = [];
        $pago_completo = false;

        // Auto-crear registro de cliente si el usuario tiene rol de cliente pero no tiene perfil
        if (in_array($user['rol'], ['cliente_familiar', 'cliente_colegio']) && !$cliente) {
            $tipo = ($user['rol'] === 'cliente_colegio') ? 'colegio' : 'familiar';
            $clienteModel->create([
                'usuario_id' => $user['id'],
                'tipo'       => $tipo,
            ]);
            $cliente = $clienteModel->findByUsuarioId($user['id']);
        }

        if ($user['rol'] === 'cliente_familiar' && $cliente) {
            $contratos = $this->findClientContracts($contratoModel, $cliente, $user);
            if (!empty($contratos)) {
                $contrato = $contratoModel->getFullDetails($contratos[0]['id']);
            }
        } elseif ($user['rol'] === 'cliente_colegio') {
            // Cliente grupal ve solo su contrato del grupo
            $grupoModel = new Grupo();
            $grupos = $this->db->fetchAll(
                "SELECT g.* FROM grupos g 
                 JOIN contratos co ON co.grupo_id = g.id
                 JOIN pasajeros p ON p.contrato_id = co.id
                 WHERE p.nombre = ? AND p.apellido LIKE ?",
                [$user['nombre'], '%' . $user['apellido'] . '%']
            );
            if (!empty($grupos)) {
                $grupoContratos = $contratoModel->getByGrupoId($grupos[0]['id']);
                if (!empty($grupoContratos)) {
                    $contrato = $contratoModel->getFullDetails($grupoContratos[0]['id']);
                }
            } else {
                $this->flash('warning', 'No se encontró un grupo asociado a su cuenta. Contacte a su representante.');
                // continuar y renderizar vista vacía en lugar de redirigir
            }
        }

        // Calculate Payment Status & Vouchers
        if ($contrato) {
            $cuotaModel = new Cuota();
            $voucherModel = new Voucher();
            
            $sum = $cuotaModel->getSummary('contrato', $contrato['id']);
            $pago_completo = ($sum['suma_esperada'] > 0 && $sum['suma_pendiente'] <= 0);
            
            if ($pago_completo) {
                $vouchers = $voucherModel->getByEntidad('contrato', $contrato['id']);
            }
        }

        $notificaciones = $notifModel->getUnreadByUser($user['id']);

        // Extraer sub-datos del contrato para las vistas
        $vuelos = $contrato['vuelos'] ?? [];
        $pasajeros = $contrato['pasajeros'] ?? [];
        $pagos = $contrato['pagos'] ?? [];
        $servicios = $contrato['servicios'] ?? [];

        $data = [
            'title'           => 'Mi Viaje - Aventuras Travel',
            'user'            => $user,
            'cliente'         => $cliente,
            'contrato'        => $contrato,
            'contratos'       => $contratos,
            'vuelos'          => $vuelos,
            'pasajeros'       => $pasajeros,
            'pagos'           => $pagos,
            'servicios'       => $servicios,
            'vouchers'        => $vouchers,
            'pago_completo'   => $pago_completo,
            'notificaciones'  => $notificaciones,
            'csrf_token'      => $this->generateCsrfToken(),
            'flash'           => $this->getFlash(),
        ];

        if ($user['rol'] === 'cliente_colegio') {
            $this->render('client/group/dashboard', $data, 'client');
        } else {
            $this->render('client/family/dashboard', $data, 'client');
        }
    }

    /**
     * Vista de servicios / Mi Viaje
     */
    public function services(): void {
        $user = $this->auth();
        $contratoModel = new Contrato();
        $clienteModel = new Cliente();

        $cliente = $clienteModel->findByUsuarioId($user['id']);
        if (in_array($user['rol'], ['cliente_familiar', 'cliente_colegio']) && !$cliente) {
            $clienteModel->create(['usuario_id' => $user['id'], 'tipo' => $user['rol'] === 'cliente_colegio' ? 'colegio' : 'familiar']);
            $cliente = $clienteModel->findByUsuarioId($user['id']);
        }
        $contrato = null;

        if ($user['rol'] === 'cliente_familiar' && $cliente) {
            $contratos = $this->findClientContracts($contratoModel, $cliente, $user);
            if (!empty($contratos)) {
                $contrato = $contratoModel->getFullDetails($contratos[0]['id']);
            }
        } elseif ($user['rol'] === 'cliente_colegio') {
            $grupoModel = new Grupo();
            $grupos = $this->db->fetchAll(
                "SELECT g.* FROM grupos g 
                 JOIN contratos co ON co.grupo_id = g.id
                 JOIN pasajeros p ON p.contrato_id = co.id
                 WHERE p.nombre = ? AND p.apellido LIKE ?",
                [$user['nombre'], '%' . $user['apellido'] . '%']
            );
            if (!empty($grupos)) {
                $grupoContratos = $contratoModel->getByGrupoId($grupos[0]['id']);
                if (!empty($grupoContratos)) {
                    $contrato = $contratoModel->getFullDetails($grupoContratos[0]['id']);
                }
            }
        }

        $data = [
            'title'      => 'Mis Servicios - Aventuras Travel',
            'user'       => $user,
            'cliente'    => $cliente,
            'contrato'   => $contrato,
            'vuelos'     => $contrato['vuelos'] ?? [],
            'pasajeros'  => $contrato['pasajeros'] ?? [],
            'servicios'  => $contrato['servicios'] ?? [],
            'flash'      => $this->getFlash(),
        ];
        $this->render('client/services', $data, 'client');
    }

    /**
     * Vista de contrato individual
     */
    public function contract(string $id): void {
        $user = $this->auth();
        $contratoModel = new Contrato();
        $contrato = $contratoModel->getFullDetails((int) $id);

        if (!$contrato) {
            $this->flash('error', 'Contrato no encontrado.');
            $this->redirect('/client/dashboard');
            return;
        }

        // Authorization: el contrato debe pertenecer al cliente autenticado
        $clienteModel = new Cliente();
        $cliente = $clienteModel->findByUsuarioId($user['id']);

        $authorized = false;
        if ($user['rol'] === 'cliente_familiar' && $cliente) {
            if (!empty($contrato['cliente_id']) && (int)$contrato['cliente_id'] === (int)$cliente['id']) {
                $authorized = true;
            }
        } elseif ($user['rol'] === 'cliente_colegio') {
            // Verificar que el contrato pertenezca a un grupo al que el usuario pertenece
            $grupoModel = new Grupo();
            $grupos = $grupoModel->getByRepresentante($user['id']);
            if (!empty($grupos)) {
                foreach ($grupos as $g) {
                    if (!empty($contrato['grupo_id']) && (int)$contrato['grupo_id'] === (int)$g['id']) {
                        $authorized = true;
                        break;
                    }
                }
            }
        }

        if (!$authorized) {
            $this->flash('error', 'No estás autorizado para ver este contrato.');
            $this->redirect('/client/dashboard');
            return;
        }

        $data = [
            'title'      => $contrato['codigo'] . ' - Aventuras Travel',
            'user'       => $user,
            'contrato'   => $contrato,
            'csrf_token' => $this->generateCsrfToken(),
        ];
        $this->render('client/contract', $data, 'client');
    }

    /**
     * Panel de pagos del cliente
     */
    public function payments(): void {
        $user = $this->auth();
        $clienteModel = new Cliente();
        $contratoModel = new Contrato();
        $cuotaModel = new Cuota();
        $pagoModel = new Pago();
        
        $cliente = $clienteModel->findByUsuarioId($user['id']);
        if (in_array($user['rol'], ['cliente_familiar', 'cliente_colegio']) && !$cliente) {
            $clienteModel->create(['usuario_id' => $user['id'], 'tipo' => $user['rol'] === 'cliente_colegio' ? 'colegio' : 'familiar']);
            $cliente = $clienteModel->findByUsuarioId($user['id']);
        }
        
        $contratos = [];
        $cuotas = [];
        $pagos = [];
        $resumen = ['esperado' => 0, 'pagado' => 0, 'pendiente' => 0];
        $cuotasPorContrato = [];

        if ($cliente) {
            $contratos = $this->findClientContracts($contratoModel, $cliente, $user);
            
            // Recopilar cuotas y pagos de todos los contratos
            foreach ($contratos as $c) {
                // Cuotas
                $ctas = $cuotaModel->getByEntidad('contrato', $c['id']);
                $pendientes = [];
                foreach ($ctas as &$ct) {
                    $ct['contrato_codigo'] = $c['codigo'];
                    $cuotas[] = $ct;
                    if ($ct['estado'] !== 'pagada') {
                        $pendientes[] = [
                            'numero'   => (int)$ct['numero_cuota'],
                            'concepto' => $ct['concepto'] ?? ('Cuota ' . $ct['numero_cuota']),
                            'esperado' => (float)$ct['monto_esperado'],
                            'pagado'   => (float)$ct['monto_pagado'],
                            'faltante' => round((float)$ct['monto_esperado'] - (float)$ct['monto_pagado'], 2),
                            'fecha'    => $ct['fecha_vencimiento'] ?? '',
                            'estado'   => $ct['estado'],
                        ];
                    }
                }
                $cuotasPorContrato[$c['id']] = $pendientes;
                
                // Pagos
                $pgs = $pagoModel->getByEntidad('contrato', $c['id']);
                foreach ($pgs as &$pg) {
                    $pg['contrato_codigo'] = $c['codigo'];
                    $pagos[] = $pg;
                }
                
                // Resumen: preferir cuotas si existen, sino usar datos del contrato
                $sum = $cuotaModel->getSummary('contrato', $c['id']);
                if ($sum['suma_esperada'] > 0) {
                    $resumen['esperado'] += $sum['suma_esperada'];
                    $resumen['pagado'] += $sum['suma_pagada'];
                    $resumen['pendiente'] += $sum['suma_pendiente'];
                } else {
                    // Fallback: usar valor_total y total_pagado del contrato
                    $fullContract = $contratoModel->getFullDetails((int)$c['id']);
                    $vt = (float)($fullContract['valor_total'] ?? $c['valor_total'] ?? 0);
                    $tp = (float)($fullContract['total_pagado'] ?? $c['total_pagado'] ?? 0);
                    $resumen['esperado'] += $vt;
                    $resumen['pagado'] += $tp;
                    $resumen['pendiente'] += ($vt - $tp);
                    
                    // Construir cuotas a partir de pagos si hay pagos pero no cuotas
                    if (empty($ctas) && !empty($pgs)) {
                        foreach ($pgs as $pg) {
                            $cuotas[] = [
                                'concepto' => $pg['concepto'] ?? 'Cuota',
                                'fecha_vencimiento' => $pg['fecha_vencimiento'] ?? $pg['created_at'] ?? '',
                                'monto_esperado' => (float)($pg['monto'] ?? 0),
                                'estado' => ($pg['estado'] === 'aprobado') ? 'pagada' : 'pendiente',
                                'contrato_codigo' => $c['codigo'],
                            ];
                        }
                    }
                }
            }
        }

        $data = [
            'title'      => 'Mis Pagos - Aventuras Travel',
            'user'       => $user,
            'contratos'  => $contratos,
            'cuotas'     => $cuotas,
            'pagos'      => $pagos,
            'resumen'    => $resumen,
            'cuotasPorContrato' => $cuotasPorContrato ?? [],
            'csrf_token' => $this->generateCsrfToken(),
            'flash'      => $this->getFlash(),
        ];
        $this->render('client/payments', $data, 'client');
    }

    /**
     * Registrar nuevo pago (Flexible)
     */
    public function registerPayment(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->redirect('/client/payments');
            return;
        }

        $contratoId = (int)$this->input('contrato_id');
        $monto = (float)$this->input('monto');
        $concepto = $this->input('concepto', 'Abono a cuenta');

        if ($monto <= 0) {
            $this->flash('error', 'El monto debe ser mayor a 0.');
            $this->redirect('/client/payments');
            return;
        }

        if (empty($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Debe adjuntar un comprobante de pago.');
            $this->redirect('/client/payments');
            return;
        }

        $file = $_FILES['comprobante'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            $this->flash('error', 'Tipo de archivo no permitido. Solo PDF, JPG, PNG.');
            $this->redirect('/client/payments');
            return;
        }

        if ($file['size'] > $maxSize) {
            $this->flash('error', 'El archivo excede el tamaño máximo de 5MB.');
            $this->redirect('/client/payments');
            return;
        }

        // Guardar archivo
        $hash = bin2hex(random_bytes(16));
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = $hash . '.' . $extension;
        $storagePath = STORAGE_PATH . '/comprobantes';

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $storagePath . '/' . $newName);

        // Registrar pago (como transacción pendiente)
        $pagoModel = new Pago();
        $pagoModel->create([
            'contrato_id'       => $contratoId,
            'entidad_tipo'      => 'contrato',
            'monto'             => $monto,
            'concepto'          => $concepto,
            'estado'            => 'pendiente',
            'comprobante_url'   => $newName,
            'fecha_vencimiento' => date('Y-m-d'),
        ]);

        $this->flash('exito', 'Pago registrado exitosamente. Será revisado por el equipo.');
        $this->redirect('/client/payments');
    }

    /**
     * Dashboard del representante (grupo)
     */
    public function leaderDashboard(): void {
        $user = $this->auth();
        if ($user['rol'] !== 'representante') {
            $this->redirect('/client/dashboard');
            return;
        }

        $grupoModel = new Grupo();
        $contratoModel = new Contrato();
        $notifModel = new Notificacion();

        $grupos = $grupoModel->getByRepresentante($user['id']);
        $contratos = [];
        $contrato = null;

        if (!empty($grupos)) {
            $contratos = $contratoModel->getByGrupoId($grupos[0]['id']);
            if (!empty($contratos)) {
                $contrato = $contratoModel->getFullDetails($contratos[0]['id']);
            }
        }

        // Stats
        $totalContracts = count($contratos);
        $paidCount = 0;
        $pendingCount = 0;
        $overdueCount = 0;
        if ($contrato && !empty($contrato['pagos'])) {
            foreach ($contrato['pagos'] as $p) {
                if ($p['estado'] === 'aprobado') $paidCount++;
                elseif ($p['estado'] === 'pendiente') $pendingCount++;
                elseif ($p['estado'] === 'atrasado') $overdueCount++;
            }
        }

        $notificaciones = $notifModel->getUnreadByUser($user['id']);

        $data = [
            'title'          => 'Group Leader Panel - Aventuras Travel',
            'user'           => $user,
            'grupo'          => $grupos[0] ?? null,
            'contrato'       => $contrato,
            'contratos'      => $contratos,
            'stats'          => [
                'total'   => $contrato ? count($contrato['pasajeros']) : 0,
                'paid'    => $paidCount,
                'pending' => $pendingCount,
                'overdue' => $overdueCount,
            ],
            'notificaciones' => $notificaciones,
            'csrf_token'     => $this->generateCsrfToken(),
            'flash'          => $this->getFlash(),
        ];
        $this->render('client/group/leader', $data, 'client');
    }

    /**
     * Lista de contratos del representante 
     */
    public function leaderContracts(): void {
        $user = $this->auth();
        $grupoModel = new Grupo();
        $contratoModel = new Contrato();

        $grupos = $grupoModel->getByRepresentante($user['id']);
        $allContracts = [];
        foreach ($grupos as $grupo) {
            $contracts = $contratoModel->getByGrupoId($grupo['id']);
            $allContracts = array_merge($allContracts, $contracts);
        }

        $data = [
            'title'     => 'All Contracts - Aventuras Travel',
            'user'      => $user,
            'contratos' => $allContracts,
        ];
        $this->render('client/group/contracts', $data, 'client');
    }

    /**
     * Buscar contratos del cliente con fallbacks:
     * 1. Por cliente_id directo
     * 2. Por JOIN clientes→usuarios (evita desync de IDs)
     * 3. Por código de usuario = código de contrato
     * 4. Por nombre del titular
     * Auto-vincula contratos huérfanos al cliente actual.
     */
    private function findClientContracts(Contrato $contratoModel, array $cliente, array $user): array {
        // 1. Búsqueda directa por cliente_id
        $contratos = $contratoModel->getByClienteId($cliente['id']);
        if (!empty($contratos)) return $contratos;

        // 2. Fallback: buscar contratos vinculados a CUALQUIER registro clientes con mismo usuario_id
        $contratos = $this->db->fetchAll(
            "SELECT co.* FROM contratos co
             JOIN clientes cl ON co.cliente_id = cl.id
             WHERE cl.usuario_id = ? AND co.estado = 'activo'
             ORDER BY co.fecha_salida DESC",
            [$user['id']]
        );
        if (!empty($contratos)) {
            // Re-vincular al cliente actual si el ID difiere
            foreach ($contratos as $c) {
                if ((int)($c['cliente_id'] ?? 0) !== (int)$cliente['id']) {
                    $contratoModel->update((int)$c['id'], ['cliente_id' => $cliente['id']]);
                }
            }
            return $contratos;
        }

        // 3. Fallback: buscar contrato cuyo código coincida con el código del usuario
        if (!empty($user['codigo'])) {
            $match = $this->db->fetchOne(
                "SELECT * FROM contratos WHERE codigo = ? AND estado = 'activo'",
                [$user['codigo']]
            );
            if ($match) {
                if (empty($match['cliente_id'])) {
                    $contratoModel->update((int)$match['id'], ['cliente_id' => $cliente['id']]);
                }
                return [$match];
            }
        }

        // 4. Fallback: buscar por nombre del titular
        $nombre = trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
        if ($nombre) {
            $contratos = $this->db->fetchAll(
                "SELECT * FROM contratos WHERE titular_nombre LIKE ? AND estado = 'activo' ORDER BY fecha_salida DESC",
                ['%' . $nombre . '%']
            );
            if (!empty($contratos)) {
                if (empty($contratos[0]['cliente_id'])) {
                    $contratoModel->update((int)$contratos[0]['id'], ['cliente_id' => $cliente['id']]);
                }
                return $contratos;
            }
        }

        // 5. Fallback para clientes de colegio: buscar por grupo → pasajeros
        if (in_array($user['rol'] ?? '', ['cliente_colegio'])) {
            $contratos = $this->db->fetchAll(
                "SELECT DISTINCT co.* FROM contratos co
                 JOIN grupos g ON co.grupo_id = g.id
                 JOIN pasajeros p ON p.contrato_id = co.id
                 WHERE p.nombre = ? AND p.apellido LIKE ?
                 ORDER BY co.fecha_salida DESC",
                [$user['nombre'], '%' . ($user['apellido'] ?? '') . '%']
            );
            if (!empty($contratos)) {
                // Vincular al cliente
                if (empty($contratos[0]['cliente_id'])) {
                    $contratoModel->update((int)$contratos[0]['id'], ['cliente_id' => $cliente['id']]);
                }
                return $contratos;
            }
        }

        return [];
    }
}
