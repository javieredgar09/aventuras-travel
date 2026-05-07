<?php
/**
 * PaymentController.php - Gestión de pagos (admin)
 */
class PaymentController extends Controller {

    public function index(): void {
        $pagoModel   = new Pago();
        $grupoModel  = new Grupo();
        $contratoModel = new Contrato();
        $cuotaModel  = new Cuota();

        $monthlyVolume = $pagoModel->getMonthlyVolume();
        $totalApproved = $pagoModel->getTotalApproved();
        $awaiting      = $pagoModel->getAwaitingReview();
        $transactions  = $pagoModel->getRecentTransactions(50);

        // Build hierarchical data: groups → contracts → cuotas
        $allGrupos        = $grupoModel->getAllWithStats();
        $gruposFamiliares = [];
        $gruposEscolares  = [];

        foreach ($allGrupos as $grupo) {
            $gid      = (int) $grupo['id'];
            $contratos = $contratoModel->getByGrupoId($gid);
            
            $grupoData = $grupo;
            $grupoData['contratos'] = [];
            $grupoPagado = 0;
            $grupoTotal  = (float) ($grupo['valor_total'] ?? 0);

            foreach ($contratos as $contrato) {
                $cid = (int) $contrato['id'];
                $contrato['cuotas']           = $cuotaModel->getByEntidad('contrato', $cid);
                $contrato['resumen']          = $cuotaModel->getSummary('contrato', $cid);
                $contrato['total_pagado_real'] = $pagoModel->getTotalApprovedByContrato($cid);
                $contrato['pagos_pendientes'] = $pagoModel->countPendingByContrato($cid);
                $grupoPagado += $contrato['total_pagado_real'];
                $grupoData['contratos'][] = $contrato;
            }

            $grupoData['total_pagado_real'] = $grupoPagado;
            $grupoData['saldo_real']        = max(0, $grupoTotal - $grupoPagado);

            if ($grupo['tipo'] === 'familiar') {
                $gruposFamiliares[] = $grupoData;
            } else {
                $gruposEscolares[] = $grupoData;
            }
        }

        // Count pending by type
        $pendFamiliar = 0;
        $pendEscolar  = 0;
        foreach ($awaiting as $a) {
            if (($a['grupo_tipo'] ?? '') === 'familiar') $pendFamiliar++;
            else $pendEscolar++;
        }

        $data = [
            'title'            => 'Gestión de Pagos - Aventuras Travel',
            'awaiting'         => $awaiting,
            'transactions'     => $transactions,
            'monthlyVolume'    => $monthlyVolume,
            'totalApproved'    => $totalApproved,
            'gruposFamiliares' => $gruposFamiliares,
            'gruposEscolares'  => $gruposEscolares,
            'pendFamiliar'     => $pendFamiliar,
            'pendEscolar'      => $pendEscolar,
            'csrf_token'       => $this->generateCsrfToken(),
            'flash'            => $this->getFlash(),
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
                $this->flash('exito', 'Pago aprobado. Las cuotas se actualizaron y el cliente fue notificado.');
            } else {
                $this->flash('error', 'No se pudo aprobar el pago (ya procesado o no encontrado).');
            }
        } catch (Exception $e) {
            error_log('PaymentController::approve error: ' . $e->getMessage());
            $this->flash('error', 'Error al aprobar el pago: ' . $e->getMessage());
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
        $pago      = $pagoModel->find((int) $id);

        if (!$pago || $pago['estado'] !== 'pendiente') {
            $this->flash('error', 'Pago no encontrado o ya procesado.');
            $this->redirect('/admin/payments');
            return;
        }

        $notas = trim($this->input('notas_rechazo', 'Comprobante inválido o no corresponde al monto requerido.'));

        $pagoModel->update((int) $id, [
            'estado'      => 'rechazado',
            'notas_admin' => $notas,
        ]);

        // Notify client
        require_once __DIR__ . '/../services/PaymentService.php';
        (new PaymentService())->notifyClientRejection((int) $id, $notas);

        $this->flash('exito', 'Pago rechazado. El cliente fue notificado.');
        $this->redirect('/admin/payments');
    }

    /**
     * Admin registers a payment on behalf of a client (auto-approved)
     */
    public function registerAdmin(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/payments');
            return;
        }

        $contratoId = (int) $this->input('contrato_id');
        $monto      = (float) $this->input('monto');
        $concepto   = trim($this->input('concepto', ''));

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
            $cuotaIds = array_values(array_filter(array_map('intval', $_POST['cuota_ids']), fn($id) => $id > 0));
        }

        // Handle optional comprobante upload
        $comprobante = null;
        if (!empty($_FILES['comprobante']['name'])) {
            $allowed = ['application/pdf', 'image/jpeg', 'image/png'];
            $maxSize = 5 * 1024 * 1024;
            $file    = $_FILES['comprobante'];

            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $realMime = $finfo->file($file['tmp_name']);
            if (!in_array($realMime, $allowed) || $file['size'] > $maxSize) {
                $this->flash('error', 'Archivo inválido. Solo PDF, JPG, PNG (máx 5MB).');
                $this->redirect('/admin/payments');
                return;
            }

            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $hash     = bin2hex(random_bytes(16));
            $filename = $hash . '.' . $ext;
            $destDir  = STORAGE_PATH . '/comprobantes';
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
            $this->flash('exito', 'Pago de $' . number_format($monto, 2) . ' registrado y aprobado. Recibo PDF generado. Cuotas actualizadas y saldo descontado.');
        } else {
            $this->flash('error', 'Error al registrar el pago. Revise los datos e inténtelo de nuevo.');
        }

        $this->redirect('/admin/payments');
    }

    /**
     * Download the generated receipt PDF for a payment (admin)
     */
    public function downloadReceipt(string $id): void {
        $pagoModel = new Pago();
        $pago = $pagoModel->find((int) $id);

        if (!$pago || empty($pago['recibo_url'])) {
            $this->flash('error', 'Recibo no encontrado para este pago.');
            $this->redirect('/admin/payments');
            return;
        }

        // Delegar al FileController que ya sirve archivos correctamente
        $fc = new FileController();
        $fc->serve('recibos', $pago['recibo_url']);
    }

    /**
     * Regenerate receipt PDF for a payment (admin)
     * POST /admin/payments/regenerate/{id}
     */
    public function regenerate(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/payments');
            return;
        }

        $pagoId = (int) $id;
        $pagoModel = new Pago();
        $pago = $pagoModel->find($pagoId);

        if (!$pago || $pago['estado'] !== 'aprobado') {
            $this->flash('error', 'Pago no encontrado o no está aprobado.');
            $this->redirect('/admin/payments');
            return;
        }

        try {
            // Delete old receipt if exists
            if (!empty($pago['recibo_url'])) {
                $oldPath = STORAGE_PATH . '/recibos/' . basename($pago['recibo_url']);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Regenerate receipt
            require_once __DIR__ . '/../services/ReceiptService.php';
            $receiptService = new ReceiptService();
            $result = $receiptService->generate($pagoId, []);

            if ($result) {
                $this->flash('exito', 'Recibo regenerado exitosamente. El monto mostrado es ahora correcto: $' . number_format($pago['monto'], 2));
            } else {
                $this->flash('error', 'Error al regenerar el recibo.');
            }
        } catch (Exception $e) {
            error_log('PaymentController::regenerate error: ' . $e->getMessage());
            $this->flash('error', 'Error al regenerar el recibo: ' . $e->getMessage());
        }

        $this->redirect('/admin/payments');
    }
}
