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

        // Fetch contract documents, vouchers, and comprobantes per payment
        $archivosContrato = [];
        $vouchersContrato = [];
        foreach ($contratos as $c) {
            $archivosContrato[$c['id']] = $this->db->fetchAll(
                "SELECT * FROM archivos WHERE contrato_id = ? ORDER BY tipo, created_at DESC", [$c['id']]
            );
            $vouchersContrato[$c['id']] = $this->db->fetchAll(
                "SELECT * FROM vouchers WHERE tipo_entidad = 'contrato' AND entidad_id = ? ORDER BY fecha_subida DESC", [$c['id']]
            );
        }

        // Fetch comprobantes for each pago
        foreach ($data['pagos'] as &$pg) {
            $pg['comprobantes'] = $this->db->fetchAll(
                "SELECT * FROM comprobantes WHERE pago_id = ? ORDER BY created_at DESC", [$pg['id']]
            );
        }
        unset($pg);

        $data['archivos']  = $archivosContrato;
        $data['vouchers']  = $vouchersContrato;

        $this->render('client/payments', $data, 'client');
    }

    /**
     * Registrar nuevo pago (Familiar o Colegio – queda pendiente de validación admin)
     */
    public function registerPayment(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->redirect('/client/payments');
            return;
        }

        $user       = $this->auth();
        $contratoId = (int)$this->input('contrato_id');
        $monto      = (float)$this->input('monto');
        $concepto   = trim($this->input('concepto', 'Abono a cuenta'));
        $metodoPago = $this->input('metodo_pago', 'Efectivo');
        $banco      = $this->input('banco', '');
        $monedaPago = $this->input('moneda_pago', 'PEN');

        // Validaciones básicas
        $metodosValidos = ['Efectivo', 'Transferencia bancaria', 'Depósito bancario', 'Yape', 'Plin'];
        if (!in_array($metodoPago, $metodosValidos)) $metodoPago = 'Efectivo';

        $bancosValidos = ['BCP', 'BBVA Continental', 'Interbank', 'Scotiabank', ''];
        if (!in_array($banco, $bancosValidos)) $banco = '';

        if (!in_array($monedaPago, ['PEN', 'USD'])) $monedaPago = 'PEN';

        if ($contratoId <= 0) {
            $this->flash('error', 'Debe seleccionar un contrato.');
            $this->redirect('/client/payments');
            return;
        }

        if ($monto <= 0) {
            $this->flash('error', 'El monto debe ser mayor a 0.');
            $this->redirect('/client/payments');
            return;
        }

        if (empty($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Debe adjuntar un comprobante de pago (PDF, JPG o PNG).');
            $this->redirect('/client/payments');
            return;
        }

        $file         = $_FILES['comprobante'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        $maxSize      = 5 * 1024 * 1024;

        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file['tmp_name']);
        if (!in_array($realMime, $allowedTypes)) {
            $this->flash('error', 'Tipo de archivo no permitido. Solo PDF, JPG, PNG.');
            $this->redirect('/client/payments');
            return;
        }
        if ($file['size'] > $maxSize) {
            $this->flash('error', 'El archivo excede el tamaño máximo de 5MB.');
            $this->redirect('/client/payments');
            return;
        }

        // Guardar archivo comprobante
        $hash        = bin2hex(random_bytes(16));
        $extension   = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName     = $hash . '.' . $extension;
        $storagePath = STORAGE_PATH . '/comprobantes';
        if (!is_dir($storagePath)) mkdir($storagePath, 0755, true);
        move_uploaded_file($file['tmp_name'], $storagePath . '/' . $newName);

        // Usar PaymentService para registrar correctamente con grupo_id y notificar admin
        require_once __DIR__ . '/../services/PaymentService.php';
        $svc = new PaymentService();
        $ok  = $svc->registerClientPayment(
            $contratoId,
            $monto,
            $concepto,
            $newName,
            $metodoPago,
            $banco,
            $monedaPago,
            (int)$user['id']
        );

        if ($ok) {
            $this->flash('exito', '✅ Comprobante enviado correctamente. El equipo de Aventuras Travel revisará tu pago y recibirás una notificación al aprobar.');
        } else {
            $this->flash('error', 'Error al registrar el pago. Inténtalo de nuevo.');
        }
        $this->redirect('/client/payments');
    }

    /**
     * Dashboard del representante (grupo) — vista completa de contratos y pagos
     */
    public function leaderDashboard(): void {
        $user = $this->auth();
        if ($user['rol'] !== 'representante') {
            $this->redirect('/client/dashboard');
            return;
        }

        $grupoModel = new Grupo();
        $contratoModel = new Contrato();
        $cuotaModel = new Cuota();
        $notifModel = new Notificacion();

        $grupos = $grupoModel->getByRepresentante($user['id']);
        $allContratos = [];

        // Recopilar contratos de TODOS los grupos del representante
        foreach ($grupos as $grupo) {
            $gContratos = $contratoModel->getByGrupoId($grupo['id']);
            foreach ($gContratos as &$c) {
                $c['grupo_nombre'] = $grupo['nombre'];
                $c['grupo_destino'] = $grupo['destino'] ?? '';

                // Enriquecer con datos de pasajeros
                $c['pasajeros'] = $this->db->fetchAll(
                    "SELECT * FROM pasajeros WHERE contrato_id = ? ORDER BY tipo, nombre", [$c['id']]
                );

                // Nombre del titular
                if (empty($c['titular_nombre']) && !empty($c['cliente_id'])) {
                    $cli = $this->db->fetchOne(
                        "SELECT u.nombre, u.apellido FROM clientes cl JOIN usuarios u ON cl.usuario_id = u.id WHERE cl.id = ?",
                        [$c['cliente_id']]
                    );
                    if ($cli) {
                        $c['titular_nombre'] = trim($cli['nombre'] . ' ' . $cli['apellido']);
                    }
                }

                // Resumen de cuotas
                $sum = $cuotaModel->getSummary('contrato', $c['id']);
                $c['cuota_esperada'] = $sum['suma_esperada'];
                $c['cuota_pagada'] = $sum['suma_pagada'];
                $c['cuota_pendiente'] = $sum['suma_pendiente'];
                $c['total_cuotas'] = $sum['total_cuotas'];

                // Cuotas vencidas (atrasadas)
                $vencidas = $this->db->fetchOne(
                    "SELECT COUNT(*) as cnt FROM plan_cuotas 
                     WHERE tipo_entidad = 'contrato' AND entidad_id = ? 
                     AND estado != 'pagada' AND fecha_vencimiento < CURDATE()",
                    [$c['id']]
                );
                $c['cuotas_vencidas'] = (int)($vencidas['cnt'] ?? 0);

                // Estado de pago derivado
                if ($sum['suma_esperada'] > 0 && $sum['suma_pendiente'] <= 0) {
                    $c['estado_pago'] = 'pagado';
                } elseif ($c['cuotas_vencidas'] > 0) {
                    $c['estado_pago'] = 'atrasado';
                } elseif ($sum['suma_pagada'] > 0) {
                    $c['estado_pago'] = 'parcial';
                } else {
                    $c['estado_pago'] = 'pendiente';
                }

                // Porcentaje pagado
                $c['pct_pagado'] = $sum['suma_esperada'] > 0
                    ? round(($sum['suma_pagada'] / $sum['suma_esperada']) * 100)
                    : 0;
            }
            unset($c);
            $allContratos = array_merge($allContratos, $gContratos);
        }

        // Estadísticas globales
        $totalContratos = count($allContratos);
        $paidCount = 0;
        $pendingCount = 0;
        $overdueCount = 0;
        $totalRecaudado = 0;
        $totalDeuda = 0;
        foreach ($allContratos as $c) {
            $totalRecaudado += $c['cuota_pagada'];
            $totalDeuda += $c['cuota_esperada'];
            if ($c['estado_pago'] === 'pagado') $paidCount++;
            elseif ($c['estado_pago'] === 'atrasado') $overdueCount++;
            else $pendingCount++;
        }

        $notificaciones = $notifModel->getUnreadByUser($user['id']);

        $data = [
            'title'          => 'Panel Representante - Aventuras Travel',
            'user'           => $user,
            'grupo'          => $grupos[0] ?? null,
            'grupos'         => $grupos,
            'contratos'      => $allContratos,
            'stats'          => [
                'total'     => $totalContratos,
                'paid'      => $paidCount,
                'pending'   => $pendingCount,
                'overdue'   => $overdueCount,
                'recaudado' => $totalRecaudado,
                'deuda'     => $totalDeuda,
            ],
            'notificaciones' => $notificaciones,
            'csrf_token'     => $this->generateCsrfToken(),
            'flash'          => $this->getFlash(),
        ];
        $this->render('client/group/leader', $data, 'client');
    }

    /**
     * Panel de pagos del representante — puede pagar por cualquier contrato del grupo
     */
    public function leaderPayments(): void {
        $user = $this->auth();
        if ($user['rol'] !== 'representante') {
            $this->redirect('/client/payments');
            return;
        }

        $grupoModel = new Grupo();
        $contratoModel = new Contrato();
        $cuotaModel = new Cuota();
        $pagoModel = new Pago();

        $grupos = $grupoModel->getByRepresentante($user['id']);
        $allContratos = [];
        $cuotasPorContrato = [];
        $resumen = ['esperado' => 0, 'pagado' => 0, 'pendiente' => 0];
        $allPagos = [];

        foreach ($grupos as $grupo) {
            $gContratos = $contratoModel->getByGrupoId($grupo['id']);
            foreach ($gContratos as &$c) {
                $c['grupo_nombre'] = $grupo['nombre'];

                // Pasajeros para búsqueda
                $c['pasajeros'] = $this->db->fetchAll(
                    "SELECT nombre, apellido, tipo FROM pasajeros WHERE contrato_id = ? ORDER BY tipo, nombre", [$c['id']]
                );

                // Titular
                if (empty($c['titular_nombre']) && !empty($c['cliente_id'])) {
                    $cli = $this->db->fetchOne(
                        "SELECT u.nombre, u.apellido FROM clientes cl JOIN usuarios u ON cl.usuario_id = u.id WHERE cl.id = ?",
                        [$c['cliente_id']]
                    );
                    if ($cli) {
                        $c['titular_nombre'] = trim($cli['nombre'] . ' ' . $cli['apellido']);
                    }
                }

                // Cuotas pendientes
                $ctas = $cuotaModel->getByEntidad('contrato', $c['id']);
                $pendientes = [];
                foreach ($ctas as $ct) {
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

                // Resumen
                $sum = $cuotaModel->getSummary('contrato', $c['id']);
                $c['cuota_esperada'] = $sum['suma_esperada'];
                $c['cuota_pagada'] = $sum['suma_pagada'];
                $c['cuota_pendiente'] = $sum['suma_pendiente'];
                $resumen['esperado'] += $sum['suma_esperada'];
                $resumen['pagado'] += $sum['suma_pagada'];
                $resumen['pendiente'] += $sum['suma_pendiente'];

                // Pagos del contrato
                $pgs = $pagoModel->getByEntidad('contrato', $c['id']);
                foreach ($pgs as &$pg) {
                    $pg['contrato_codigo'] = $c['codigo'];
                    $pg['titular_nombre'] = $c['titular_nombre'] ?? '';
                    $allPagos[] = $pg;
                }
                unset($pg);
            }
            unset($c);
            $allContratos = array_merge($allContratos, $gContratos);
        }

        $data = [
            'title'             => 'Pagos del Grupo - Aventuras Travel',
            'user'              => $user,
            'grupo'             => $grupos[0] ?? null,
            'contratos'         => $allContratos,
            'pagos'             => $allPagos,
            'resumen'           => $resumen,
            'cuotasPorContrato' => $cuotasPorContrato,
            'csrf_token'        => $this->generateCsrfToken(),
            'flash'             => $this->getFlash(),
        ];
        $this->render('client/group/payments', $data, 'client');
    }

    /**
     * Registrar pago desde el panel del representante
     */
    public function leaderRegisterPayment(): void {
        $user = $this->auth();
        if ($user['rol'] !== 'representante') {
            $this->flash('error', 'No autorizado.');
            $this->redirect('/login');
            return;
        }

        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->redirect('/leader/payments');
            return;
        }

        $contratoId = (int)$this->input('contrato_id');
        $monto      = (float)$this->input('monto');
        $concepto   = trim($this->input('concepto', 'Abono a cuenta'));
        $metodoPago = $this->input('metodo_pago', 'Efectivo');
        $banco      = $this->input('banco', '');
        $monedaPago = $this->input('moneda_pago', 'PEN');

        // Verificar que el contrato pertenece a un grupo del representante
        $grupoModel = new Grupo();
        $grupos    = $grupoModel->getByRepresentante($user['id']);
        $grupoIds  = array_column($grupos, 'id');

        $contrato = $this->db->fetchOne("SELECT id, grupo_id FROM contratos WHERE id = ?", [$contratoId]);
        if (!$contrato || !in_array((int)$contrato['grupo_id'], $grupoIds)) {
            $this->flash('error', 'El contrato no pertenece a tu grupo.');
            $this->redirect('/leader/payments');
            return;
        }

        if ($contratoId <= 0 || $monto <= 0) {
            $this->flash('error', 'Contrato y monto son requeridos.');
            $this->redirect('/leader/payments');
            return;
        }

        if (empty($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Debe adjuntar un comprobante de pago.');
            $this->redirect('/leader/payments');
            return;
        }

        $file         = $_FILES['comprobante'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        $maxSize      = 5 * 1024 * 1024;

        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file['tmp_name']);
        if (!in_array($realMime, $allowedTypes)) {
            $this->flash('error', 'Tipo de archivo no permitido. Solo PDF, JPG, PNG.');
            $this->redirect('/leader/payments');
            return;
        }
        if ($file['size'] > $maxSize) {
            $this->flash('error', 'El archivo excede el tamaño máximo de 5MB.');
            $this->redirect('/leader/payments');
            return;
        }

        // Guardar comprobante
        $hash        = bin2hex(random_bytes(16));
        $extension   = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName     = $hash . '.' . $extension;
        $storagePath = STORAGE_PATH . '/comprobantes';
        if (!is_dir($storagePath)) mkdir($storagePath, 0755, true);
        move_uploaded_file($file['tmp_name'], $storagePath . '/' . $newName);

        // Usar PaymentService: guarda grupo_id correctamente y notifica admins
        require_once __DIR__ . '/../services/PaymentService.php';
        $svc = new PaymentService();
        $ok  = $svc->registerClientPayment(
            $contratoId,
            $monto,
            $concepto,
            $newName,
            $metodoPago,
            $banco,
            $monedaPago,
            (int)$user['id']
        );

        if ($ok) {
            $this->flash('exito', '✅ Comprobante enviado. El equipo de Aventuras Travel revisará el pago y notificará al aprobar.');
        } else {
            $this->flash('error', 'Error al registrar el pago. Inténtalo de nuevo.');
        }
        $this->redirect('/leader/payments');
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

    /**
     * Página de Soporte al Cliente
     */
    public function soporte(): void {
        $user = $this->auth();

        $data = [
            'title'      => 'Soporte – Aventuras Travel',
            'user'       => $user,
            'flash'      => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
        ];

        $this->render('client/soporte', $data, 'client');
    }

    /**
     * Download the receipt PDF for a payment (client or leader)
     * Verifies the payment belongs to the authenticated user's contract
     */
    public function downloadReceipt(string $id): void {
        $user = $this->auth();
        $pagoModel = new Pago();
        $pago = $pagoModel->find((int) $id);

        if (!$pago || empty($pago['recibo_url'])) {
            $this->flash('error', 'Recibo no encontrado.');
            $this->redirect('/client/payments');
            return;
        }

        // Verify ownership: the payment's contract must belong to this client
        $authorized = false;
        $contratoId = (int)($pago['contrato_id'] ?? 0);

        if ($contratoId > 0) {
            $clienteModel = new Cliente();
            $cliente = $clienteModel->findByUsuarioId($user['id']);

            if ($user['rol'] === 'cliente_familiar' && $cliente) {
                // Check if contract belongs to this client
                $contrato = $this->db->fetchOne(
                    "SELECT id FROM contratos WHERE id = ? AND cliente_id = ?",
                    [$contratoId, $cliente['id']]
                );
                if ($contrato) $authorized = true;

                // Fallback: check via usuario_id
                if (!$authorized) {
                    $contrato = $this->db->fetchOne(
                        "SELECT co.id FROM contratos co
                         JOIN clientes cl ON co.cliente_id = cl.id
                         WHERE co.id = ? AND cl.usuario_id = ?",
                        [$contratoId, $user['id']]
                    );
                    if ($contrato) $authorized = true;
                }
            } elseif ($user['rol'] === 'representante') {
                // Representante: check if contract's group belongs to them
                $grupoModel = new Grupo();
                $grupos = $grupoModel->getByRepresentante($user['id']);
                $grupoIds = array_column($grupos, 'id');

                $contrato = $this->db->fetchOne(
                    "SELECT grupo_id FROM contratos WHERE id = ?",
                    [$contratoId]
                );
                if ($contrato && in_array((int)$contrato['grupo_id'], $grupoIds)) {
                    $authorized = true;
                }
            } elseif ($user['rol'] === 'cliente_colegio') {
                // Grupo escolar: check if they are a passenger in this contract
                $match = $this->db->fetchOne(
                    "SELECT id FROM pasajeros WHERE contrato_id = ? AND nombre = ? AND apellido LIKE ?",
                    [$contratoId, $user['nombre'], '%' . $user['apellido'] . '%']
                );
                if ($match) $authorized = true;
            }
        }

        if (!$authorized) {
            $this->flash('error', 'No estás autorizado para descargar este recibo.');
            $this->redirect('/client/payments');
            return;
        }

        // Delegar al FileController que ya sirve archivos correctamente
        $fc = new FileController();
        $fc->serve('recibos', $pago['recibo_url']);
    }
}

