<?php
/**
 * PaymentService.php - Handles flexible payment approval and registration logic
 */
class PaymentService {
    
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Approves a payment and cascades the amount to pending instalments.
     * If $cuotaIds is provided, those cuotas are paid first (in order), then cascade to remaining.
     * Returns the payment row (with all enriched data) or false on failure.
     */
    public function approvePayment(int $pagoId, array $cuotaIds = []): bool {
        $pagoModel  = new Pago();
        $cuotaModel = new Cuota();
        
        $pago = $pagoModel->find($pagoId);
        
        if (!$pago || $pago['estado'] !== 'pendiente') {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $montoDisponible = (float) $pago['monto'];
            
            // Determine entity: always prefer contrato
            $contratoId = !empty($pago['contrato_id']) ? (int) $pago['contrato_id'] : null;
            $grupoId    = !empty($pago['grupo_id'])    ? (int) $pago['grupo_id']    : null;
            
            if ($contratoId) {
                $tipoEntidad = 'contrato';
                $entidadId   = $contratoId;
            } elseif ($grupoId) {
                $tipoEntidad = 'grupo';
                $entidadId   = $grupoId;
            } else {
                $this->db->rollBack();
                return false;
            }

            // Get pending instalments sorted by older due dates first
            $cuotasPendientes = $cuotaModel->getPendingByEntidad($tipoEntidad, $entidadId);

            // If specific cuotas were selected, reorder: selected first, then the rest
            if (!empty($cuotaIds)) {
                $selected = [];
                $rest     = [];
                foreach ($cuotasPendientes as $cuota) {
                    if (in_array((int)$cuota['id'], $cuotaIds, true)) {
                        $selected[] = $cuota;
                    } else {
                        $rest[] = $cuota;
                    }
                }
                $cuotasPendientes = array_merge($selected, $rest);
            }

            // Apply payment amount to cuotas in cascade and capture distribution
            $cascadeDistribution = [];
            foreach ($cuotasPendientes as $cuota) {
                if ($montoDisponible <= 0) break;

                $esperado = (float) $cuota['monto_esperado'];
                $pagado   = (float) $cuota['monto_pagado'];
                $faltante = $esperado - $pagado;

                if ($faltante <= 0) continue;

                if ($montoDisponible >= $faltante) {
                    $cuotaModel->update((int)$cuota['id'], [
                        'monto_pagado' => $esperado,
                        'estado'       => 'pagada'
                    ]);
                    $cascadeDistribution[] = [
                        'numero'    => (int)$cuota['numero_cuota'],
                        'concepto'  => $cuota['concepto'] ?? 'Cuota ' . $cuota['numero_cuota'],
                        'esperado'  => $esperado,
                        'aplicado'  => round($faltante, 2),
                        'tipo'      => 'completa',
                    ];
                    $montoDisponible -= $faltante;
                } else {
                    $cuotaModel->update((int)$cuota['id'], [
                        'monto_pagado' => round($pagado + $montoDisponible, 2),
                        'estado'       => 'parcial'
                    ]);
                    $cascadeDistribution[] = [
                        'numero'    => (int)$cuota['numero_cuota'],
                        'concepto'  => $cuota['concepto'] ?? 'Cuota ' . $cuota['numero_cuota'],
                        'esperado'  => $esperado,
                        'aplicado'  => round($montoDisponible, 2),
                        'tipo'      => 'parcial',
                    ];
                    $montoDisponible = 0;
                }
            }

            // Update original payment status and add currency information
            $updateData = [
                'estado'           => 'aprobado',
                'fecha_aprobacion' => date('Y-m-d'),
                'fecha_pago'       => date('Y-m-d'),
                'excedente'        => round($montoDisponible, 2),
            ];

            // Obtener moneda del grupo o contrato
            if ($contratoId) {
                $contrato = (new Contrato())->find($contratoId);
                if ($contrato) {
                    $monedaGrupo = $contrato['moneda_contrato'] ?? 'USD';
                    $updateData['moneda_grupo'] = $monedaGrupo;
                }
            } elseif ($grupoId) {
                $grupo = (new Grupo())->find($grupoId);
                if ($grupo) {
                    $monedaGrupo = $grupo['moneda_grupo'] ?? 'USD';
                    $updateData['moneda_grupo'] = $monedaGrupo;
                }
            }

            // Registrar moneda en que se realizó el pago (para tracking de conversiones)
            $updateData['moneda_pago_original'] = $pago['moneda_pago_original'] ?? 'USD';

            $pagoModel->update($pagoId, $updateData);

            // Update contract saldo to reflect real payments
            if ($contratoId) {
                $totalAprobado = $pagoModel->getTotalApprovedByContrato($contratoId);
                $contrato = (new Contrato())->find($contratoId);
                if ($contrato) {
                    $nuevoSaldo = max(0, (float) $contrato['valor_total'] - $totalAprobado);
                    (new Contrato())->update($contratoId, ['saldo' => round($nuevoSaldo, 2)]);
                }
            }

            $this->db->commit();

            // Generate payment receipt PDF
            try {
                require_once __DIR__ . '/../services/ReceiptService.php';
                $receiptService = new ReceiptService();
                $receiptService->generate($pagoId, $cascadeDistribution);
            } catch (Exception $receiptEx) {
                error_log("PaymentService::approvePayment - Receipt generation failed for pago $pagoId: " . $receiptEx->getMessage());
                // Non-blocking: payment is still approved even if receipt fails
            }

            // Send notification email to client (non-blocking)
            $this->notifyClientApproval($pago, $contratoId);

            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("PaymentService::approvePayment error for pago $pagoId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register a payment from admin (auto-approve with cascade).
     * $cuotaIds = specific cuotas to pay first.
     */
    public function registerAdminPayment(int $contratoId, float $monto, string $concepto, ?string $comprobante = null, array $cuotaIds = []): bool {
        $pagoModel = new Pago();
        
        // Get grupo_id from contract
        $contrato = (new Contrato())->find($contratoId);
        $grupoId  = $contrato ? (int)($contrato['grupo_id'] ?? 0) : 0;

        try {
            $insertData = [
                'contrato_id'       => $contratoId,
                'grupo_id'          => $grupoId ?: null,
                'entidad_tipo'      => 'contrato',
                'concepto'          => $concepto,
                'monto'             => $monto,
                'fecha_vencimiento' => date('Y-m-d'),
                'estado'            => 'pendiente',
                'comprobante_url'   => $comprobante,
                'metodo_pago'       => 'admin',
            ];

            $pagoId = $pagoModel->create($insertData);

            return $this->approvePayment($pagoId, $cuotaIds);
        } catch (Exception $e) {
            error_log("PaymentService::registerAdminPayment error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register a client-submitted payment (pending admin approval).
     * Notifies admin via stored notification.
     */
    public function registerClientPayment(
        int    $contratoId,
        float  $monto,
        string $concepto,
        string $comprobante,
        string $metodoPago  = 'Efectivo',
        string $banco       = '',
        string $monedaPago  = 'PEN',
        int    $clienteUserId = 0
    ): bool {
        $pagoModel = new Pago();
        $contrato  = (new Contrato())->find($contratoId);

        if (!$contrato) return false;

        $grupoId = (int)($contrato['grupo_id'] ?? 0);

        try {
            $pagoId = $pagoModel->create([
                'contrato_id'       => $contratoId,
                'grupo_id'          => $grupoId ?: null,
                'entidad_tipo'      => 'contrato',
                'monto'             => $monto,
                'concepto'          => $concepto,
                'estado'            => 'pendiente',
                'comprobante_url'   => $comprobante,
                'metodo_pago'       => $metodoPago,
                'banco'             => $banco ?: null,
                'moneda_pago'       => $monedaPago,
                'fecha_vencimiento' => date('Y-m-d'),
            ]);

            // Create internal notification for admins
            $this->notifyAdminNewPayment($contrato, $monto, $concepto, $clienteUserId);

            return (bool)$pagoId;
        } catch (Exception $e) {
            error_log("PaymentService::registerClientPayment error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send approval notification email to client
     */
    private function notifyClientApproval(array $pago, ?int $contratoId): void {
        try {
            if (!$contratoId) return;

            $contrato = (new Contrato())->find($contratoId);
            if (!$contrato) return;

            $email = $contrato['titular_correo'] ?? null;
            $nombre = $contrato['titular_nombre'] ?? 'Cliente';

            // If no email in contract, try via cliente/usuario
            if (!$email && !empty($contrato['cliente_id'])) {
                $cli = $this->db->fetchOne(
                    "SELECT u.email, CONCAT(u.nombre,' ',u.apellido) as nombre
                     FROM clientes cl JOIN usuarios u ON cl.usuario_id = u.id
                     WHERE cl.id = ?",
                    [(int)$contrato['cliente_id']]
                );
                if ($cli) {
                    $email  = $cli['email'];
                    $nombre = $cli['nombre'];
                }
            }

            if (!$email) return;

            require_once __DIR__ . '/../services/EmailService.php';
            $emailSvc = new EmailService();
            $emailSvc->sendPaymentApproval(
                $email,
                $nombre,
                $contrato['codigo'] ?? '—',
                (float)$pago['monto']
            );

            // Create internal notification for the client user
            if (!empty($contrato['cliente_id'])) {
                $cliUser = $this->db->fetchOne(
                    "SELECT usuario_id FROM clientes WHERE id = ?",
                    [(int)$contrato['cliente_id']]
                );
                if ($cliUser) {
                    $this->db->insert('notificaciones', [
                        'usuario_id' => $cliUser['usuario_id'],
                        'tipo'       => 'exito',
                        'titulo'     => '✅ Pago aprobado',
                        'mensaje'    => 'Tu pago de $' . number_format((float)$pago['monto'], 2)
                                      . ' para el contrato ' . ($contrato['codigo'] ?? '') . ' fue aprobado.',
                        'leida'      => 0,
                        'enlace'     => '/client/payments',
                    ]);
                }
            }

        } catch (Exception $e) {
            error_log("PaymentService::notifyClientApproval error: " . $e->getMessage());
        }
    }

    /**
     * Create internal notification for admins when a client registers a payment
     */
    private function notifyAdminNewPayment(array $contrato, float $monto, string $concepto, int $clienteUserId): void {
        try {
            // Get all admin users
            $admins = $this->db->fetchAll(
                "SELECT id FROM usuarios WHERE rol IN ('admin','superadmin') AND activo = 1"
            );

            $clienteNombre = $contrato['titular_nombre'] ?? 'Cliente';
            if (!$clienteNombre && $clienteUserId) {
                $usr = $this->db->fetchOne(
                    "SELECT CONCAT(nombre,' ',apellido) as nombre FROM usuarios WHERE id = ?",
                    [$clienteUserId]
                );
                if ($usr) $clienteNombre = $usr['nombre'];
            }

            $mensaje = "💳 Nuevo comprobante de pago de \${$monto} registrado por {$clienteNombre} "
                     . "para el contrato " . ($contrato['codigo'] ?? '—') . ". Concepto: {$concepto}";

            foreach ($admins as $admin) {
                $this->db->insert('notificaciones', [
                    'usuario_id' => $admin['id'],
                    'tipo'       => 'advertencia',
                    'titulo'     => '💳 Nuevo pago por validar',
                    'mensaje'    => $mensaje,
                    'leida'      => 0,
                    'enlace'     => '/admin/payments',
                ]);
            }
        } catch (Exception $e) {
            error_log("PaymentService::notifyAdminNewPayment error: " . $e->getMessage());
        }
    }

    /**
     * Notify client that their payment was rejected
     */
    public function notifyClientRejection(int $pagoId, string $notas = ''): void {
        try {
            $pago     = (new Pago())->find($pagoId);
            if (!$pago || empty($pago['contrato_id'])) return;

            $contrato = (new Contrato())->find((int)$pago['contrato_id']);
            if (!$contrato) return;

            $email  = $contrato['titular_correo'] ?? null;
            $nombre = $contrato['titular_nombre'] ?? 'Cliente';

            if (!$email && !empty($contrato['cliente_id'])) {
                $cli = $this->db->fetchOne(
                    "SELECT u.email, CONCAT(u.nombre,' ',u.apellido) as nombre
                     FROM clientes cl JOIN usuarios u ON cl.usuario_id = u.id
                     WHERE cl.id = ?",
                    [(int)$contrato['cliente_id']]
                );
                if ($cli) {
                    $email  = $cli['email'];
                    $nombre = $cli['nombre'];
                }
            }

            require_once __DIR__ . '/../services/EmailService.php';
            $emailSvc = new EmailService();

            if ($email) {
                $emailSvc->sendPaymentRejection(
                    $email,
                    $nombre,
                    $contrato['codigo'] ?? '—',
                    (float)$pago['monto'],
                    $notas
                );
            }

            // Internal notification for client
            if (!empty($contrato['cliente_id'])) {
                $cliUser = $this->db->fetchOne(
                    "SELECT usuario_id FROM clientes WHERE id = ?",
                    [(int)$contrato['cliente_id']]
                );
                if ($cliUser) {
                    $this->db->insert('notificaciones', [
                        'usuario_id' => $cliUser['usuario_id'],
                        'tipo'       => 'error',
                        'titulo'     => '❌ Pago rechazado',
                        'mensaje'    => 'Tu pago de $' . number_format((float)$pago['monto'], 2)
                                      . ' para el contrato ' . ($contrato['codigo'] ?? '') . ' fue rechazado.'
                                      . ($notas ? ' Motivo: ' . $notas : ''),
                        'leida'      => 0,
                        'enlace'     => '/client/payments',
                    ]);
                }
            }
        } catch (Exception $e) {
            error_log("PaymentService::notifyClientRejection error: " . $e->getMessage());
        }
    }
}
