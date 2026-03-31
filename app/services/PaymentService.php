<?php
/**
 * PaymentService.php - Handles flexible payment approval logic
 */
class PaymentService {
    
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Approves a payment and cascades the amount to pending installments.
     * If $cuotaIds is provided, those cuotas are paid first (in order), then cascade to remaining.
     */
    public function approvePayment(int $pagoId, array $cuotaIds = []): bool {
        $pagoModel = new Pago();
        $cuotaModel = new Cuota();
        
        $pago = $pagoModel->find($pagoId);
        
        if (!$pago || $pago['estado'] !== 'pendiente') {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $montoDisponible = (float) $pago['monto'];
            
            // Determine entity: always use contrato if available
            $contratoId = !empty($pago['contrato_id']) ? (int) $pago['contrato_id'] : null;
            $grupoId = !empty($pago['grupo_id']) ? (int) $pago['grupo_id'] : null;
            
            if ($contratoId) {
                $tipoEntidad = 'contrato';
                $entidadId = $contratoId;
            } elseif ($grupoId) {
                $tipoEntidad = 'grupo';
                $entidadId = $grupoId;
            } else {
                $this->db->rollBack();
                return false;
            }

            // Get pending installments sorted by older due dates first
            $cuotasPendientes = $cuotaModel->getPendingByEntidad($tipoEntidad, $entidadId);

            // If specific cuotas were selected, reorder: selected first, then the rest
            if (!empty($cuotaIds)) {
                $selected = [];
                $rest = [];
                foreach ($cuotasPendientes as $cuota) {
                    if (in_array((int)$cuota['id'], $cuotaIds, true)) {
                        $selected[] = $cuota;
                    } else {
                        $rest[] = $cuota;
                    }
                }
                $cuotasPendientes = array_merge($selected, $rest);
            }

            foreach ($cuotasPendientes as $cuota) {
                if ($montoDisponible <= 0) break;

                $esperado = (float) $cuota['monto_esperado'];
                $pagado = (float) $cuota['monto_pagado'];
                $faltante = $esperado - $pagado;

                if ($faltante <= 0) continue;

                if ($montoDisponible >= $faltante) {
                    $cuotaModel->update((int)$cuota['id'], [
                        'monto_pagado' => $esperado,
                        'estado'       => 'pagada'
                    ]);
                    $montoDisponible -= $faltante;
                } else {
                    $cuotaModel->update((int)$cuota['id'], [
                        'monto_pagado' => $pagado + $montoDisponible,
                        'estado'       => 'parcial'
                    ]);
                    $montoDisponible = 0;
                }
            }

            // Update original payment
            $pagoModel->update($pagoId, [
                'estado'           => 'aprobado',
                'fecha_aprobacion' => date('Y-m-d'),
                'fecha_pago'       => date('Y-m-d'),
                'excedente'        => $montoDisponible
            ]);

            // Update contract saldo if applicable
            if ($contratoId) {
                $totalAprobado = $pagoModel->getTotalApprovedByContrato($contratoId);
                $contrato = (new Contrato())->find($contratoId);
                if ($contrato) {
                    $nuevoSaldo = max(0, (float) $contrato['valor_total'] - $totalAprobado);
                    (new Contrato())->update($contratoId, ['saldo' => $nuevoSaldo]);
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error approving payment $pagoId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register a payment from admin (auto-approve with cascade).
     * $cuotaIds = specific cuotas to pay first.
     */
    public function registerAdminPayment(int $contratoId, float $monto, string $concepto, ?string $comprobante = null, array $cuotaIds = []): bool {
        $pagoModel = new Pago();

        try {
            $pagoId = $pagoModel->create([
                'contrato_id'     => $contratoId,
                'entidad_tipo'    => 'contrato',
                'concepto'        => $concepto,
                'monto'           => $monto,
                'fecha_vencimiento' => date('Y-m-d'),
                'estado'          => 'pendiente',
                'comprobante_url' => $comprobante,
                'metodo_pago'     => 'admin',
            ]);

            return $this->approvePayment($pagoId, $cuotaIds);
        } catch (Exception $e) {
            error_log("Error registering admin payment: " . $e->getMessage());
            return false;
        }
    }
}
