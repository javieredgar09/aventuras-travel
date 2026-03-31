<?php
/**
 * PaymentController.php - Gestión de pagos (admin)
 */
class PaymentController extends Controller {

    public function index(): void {
        $pagoModel = new Pago();
        $grupoModel = new Grupo();
        $contratoModel = new Contrato();
        $cuotaModel = new Cuota();

        $monthlyVolume = $pagoModel->getMonthlyVolume();
        $totalApproved = $pagoModel->getTotalApproved();
        $awaiting = $pagoModel->getAwaitingReview();
        $transactions = $pagoModel->getRecentTransactions(50);

        // Build hierarchical data: groups → contracts → cuotas
        $allGrupos = $grupoModel->getAllWithStats();
        $gruposFamiliares = [];
        $gruposEscolares = [];

        foreach ($allGrupos as $grupo) {
            $gid = (int) $grupo['id'];
            $contratos = $contratoModel->getByGrupoId($gid);
            
            $grupoData = $grupo;
            $grupoData['contratos'] = [];
            $grupoPagado = 0;
            $grupoTotal = (float) ($grupo['valor_total'] ?? 0);

            foreach ($contratos as $contrato) {
                $cid = (int) $contrato['id'];
                $contrato['cuotas'] = $cuotaModel->getByEntidad('contrato', $cid);
                $contrato['resumen'] = $cuotaModel->getSummary('contrato', $cid);
                $contrato['total_pagado_real'] = $pagoModel->getTotalApprovedByContrato($cid);
                $grupoPagado += $contrato['total_pagado_real'];
                $grupoData['contratos'][] = $contrato;
            }

            $grupoData['total_pagado_real'] = $grupoPagado;
            $grupoData['saldo_real'] = max(0, $grupoTotal - $grupoPagado);

            if ($grupo['tipo'] === 'familiar') {
                $gruposFamiliares[] = $grupoData;
            } else {
                $gruposEscolares[] = $grupoData;
            }
        }

        // Count pending by type
        $pendFamiliar = 0;
        $pendEscolar = 0;
        foreach ($awaiting as $a) {
            if (($a['grupo_tipo'] ?? '') === 'familiar') $pendFamiliar++;
            else $pendEscolar++;
        }

        $data = [
            'title'             => 'Gestión de Pagos - Aventuras Travel',
            'awaiting'          => $awaiting,
            'transactions'      => $transactions,
            'monthlyVolume'     => $monthlyVolume,
            'totalApproved'     => $totalApproved,
            'gruposFamiliares'  => $gruposFamiliares,
            'gruposEscolares'   => $gruposEscolares,
            'pendFamiliar'      => $pendFamiliar,
            'pendEscolar'       => $pendEscolar,
            'csrf_token'        => $this->generateCsrfToken(),
            'flash'             => $this->getFlash(),
        ];
        $this->render('admin/payments/index', $data, 'admin');
    }

    public function approve(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/payments');
            return;
        }

        require_once __DIR__ . '/../services/PaymentService.php';
        $paymentService = new PaymentService();

        try {
            if ($paymentService->approvePayment((int) $id)) {
                $this->flash('exito', 'Pago aprobado exitosamente. Cuotas actualizadas en cascada.');
            } else {
                $this->flash('error', 'Error al aprobar el pago o ya fue procesado.');
            }
        } catch (Exception $e) {
            $this->flash('error', 'Error al aprobar el pago.');
        }

        $this->redirect('/admin/payments');
    }

    public function reject(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/payments');
            return;
        }

        $pagoModel = new Pago();
        $pagoModel->update((int) $id, [
            'estado'      => 'rechazado',
            'notas_admin' => $this->input('notas_rechazo', 'Pago rechazado por administrador')
        ]);

        $this->flash('exito', 'Pago rechazado.');
        $this->redirect('/admin/payments');
    }

    /**
     * Admin registers a payment on behalf of a client
     */
    public function registerAdmin(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/payments');
            return;
        }

        $contratoId = (int) $this->input('contrato_id');
        $monto = (float) $this->input('monto');
        $concepto = trim($this->input('concepto', ''));

        if ($contratoId <= 0 || $monto <= 0) {
            $this->flash('error', 'Contrato y monto son requeridos.');
            $this->redirect('/admin/payments');
            return;
        }

        // Verify contract exists
        $contrato = (new Contrato())->find($contratoId);
        if (!$contrato) {
            $this->flash('error', 'Contrato no encontrado.');
            $this->redirect('/admin/payments');
            return;
        }

        if (empty($concepto)) {
            $concepto = 'Pago registrado por administrador';
        }

        // Get selected cuota IDs
        $cuotaIds = [];
        if (!empty($_POST['cuota_ids']) && is_array($_POST['cuota_ids'])) {
            $cuotaIds = array_map('intval', $_POST['cuota_ids']);
            $cuotaIds = array_filter($cuotaIds, fn($id) => $id > 0);
        }

        // Handle optional comprobante upload
        $comprobante = null;
        if (!empty($_FILES['comprobante']['name'])) {
            $allowed = ['application/pdf', 'image/jpeg', 'image/png'];
            $maxSize = 5 * 1024 * 1024;
            $file = $_FILES['comprobante'];

            if (!in_array($file['type'], $allowed) || $file['size'] > $maxSize) {
                $this->flash('error', 'Archivo inválido. Solo PDF, JPG, PNG (máx 5MB).');
                $this->redirect('/admin/payments');
                return;
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $hash = bin2hex(random_bytes(16));
            $filename = $hash . '.' . $ext;
            $destDir = STORAGE_PATH . '/comprobantes';
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);

            if (!move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename)) {
                $this->flash('error', 'Error al subir el comprobante.');
                $this->redirect('/admin/payments');
                return;
            }
            $comprobante = $filename;
        }

        require_once __DIR__ . '/../services/PaymentService.php';
        $paymentService = new PaymentService();

        if ($paymentService->registerAdminPayment($contratoId, $monto, $concepto, $comprobante, $cuotaIds)) {
            $this->flash('exito', "Pago de $" . number_format($monto, 2) . " registrado y aprobado. Cuotas actualizadas.");
        } else {
            $this->flash('error', 'Error al registrar el pago.');
        }

        $this->redirect('/admin/payments');
    }
}
