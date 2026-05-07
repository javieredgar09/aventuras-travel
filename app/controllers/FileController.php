<?php
class FileController extends Controller {

    /**
     * Servir archivos desde /storage de forma segura
     * Ruta: GET /storage/{type}/{filename}
     */
    public function serve(string $type, string $filename): void {
        // Validar tipo permitido
        $allowedTypes = ['comprobantes', 'vouchers', 'contratos', 'promociones', 'recibos'];
        if (!in_array($type, $allowedTypes)) {
            http_response_code(404);
            echo 'No encontrado';
            exit;
        }

        // Sanitizar filename - solo alfanuméricos, guión, punto
        $filename = basename($filename);
        if (!preg_match('/^[a-zA-Z0-9_\-]+\.[a-zA-Z0-9]+$/', $filename)) {
            http_response_code(400);
            echo 'Nombre de archivo inválido';
            exit;
        }

        $filePath = STORAGE_PATH . '/' . $type . '/' . $filename;

        if (!file_exists($filePath) || !is_file($filePath)) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            exit;
        }

        // Detectar MIME type real
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($filePath);

        // Solo permitir tipos seguros
        $safeMimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ];
        if (!in_array($mime, $safeMimes)) {
            http_response_code(403);
            echo 'Tipo de archivo no permitido';
            exit;
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filePath));
        // Recibos: forzar descarga; otros: mostrar inline
        $disposition = ($type === 'recibos') ? 'attachment' : 'inline';
        header('Content-Disposition: ' . $disposition . '; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=3600');
        readfile($filePath);
        exit;
    }

    /**
     * Subir contrato PDF para un contrato existente
     * Ruta: POST /admin/contracts/{id}/upload
     */
    public function uploadContract(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        $contratoId = (int) $id;
        $contrato = (new Contrato())->find($contratoId);
        if (!$contrato) {
            $this->flash('error', 'Contrato no encontrado.');
            $this->redirect('/admin/contracts');
            return;
        }

        if (empty($_FILES['contrato_pdf']) || $_FILES['contrato_pdf']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Debe adjuntar un archivo.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        $file = $_FILES['contrato_pdf'];

        // Validar con finfo (MIME real)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file['tmp_name']);
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];

        if (!in_array($realMime, $allowedMimes)) {
            $this->flash('error', 'Solo se permiten archivos PDF, JPG o PNG.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        if ($file['size'] > 10 * 1024 * 1024) { // 10MB para contratos
            $this->flash('error', 'El archivo excede el tamaño máximo de 10MB.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        $hash = bin2hex(random_bytes(16));
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newName = $hash . '.' . $ext;
        $storagePath = STORAGE_PATH . '/contratos';

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $storagePath . '/' . $newName)) {
            $this->flash('error', 'Error al guardar el archivo.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        // Guardar en tabla archivos
        $archivoModel = new Archivo();
        $archivoModel->create([
            'contrato_id'     => $contratoId,
            'pasajero_id'     => null,
            'tipo'            => 'contrato',
            'nombre_original' => $file['name'],
            'nombre_hash'     => $newName,
            'ruta'            => 'contratos/' . $newName,
            'mime_type'       => $realMime,
            'tamano'          => $file['size'],
            'subido_por'      => $_SESSION['user']['id'] ?? null,
        ]);

        $this->flash('exito', 'Contrato subido correctamente.');
        $this->redirect('/admin/contracts/' . $id);
    }

    /**
     * Subir voucher de servicio para un grupo o contrato
     * Ruta: POST /admin/contracts/{id}/voucher
     */
    public function uploadVoucher(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        $contratoId = (int) $id;
        $contrato = (new Contrato())->find($contratoId);
        if (!$contrato) {
            $this->flash('error', 'Contrato no encontrado.');
            $this->redirect('/admin/contracts');
            return;
        }

        if (empty($_FILES['voucher_file']) || $_FILES['voucher_file']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Debe adjuntar un archivo.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        $file = $_FILES['voucher_file'];
        $tipoVoucher = $this->input('tipo_voucher', 'general');
        $titulo = trim($this->input('titulo', 'Voucher de Servicio'));

        // Validar con finfo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file['tmp_name']);
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];

        if (!in_array($realMime, $allowedMimes)) {
            $this->flash('error', 'Solo se permiten archivos PDF, JPG o PNG.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            $this->flash('error', 'El archivo excede el tamaño máximo de 10MB.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        $hash = bin2hex(random_bytes(16));
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newName = $hash . '.' . $ext;
        $storagePath = STORAGE_PATH . '/vouchers';

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $storagePath . '/' . $newName)) {
            $this->flash('error', 'Error al guardar el archivo.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        // Determinar entidad
        $grupoId = $contrato['grupo_id'] ?? null;
        $tipoEntidad = $grupoId ? 'grupo' : 'contrato';
        $entidadId = $grupoId ?: $contratoId;

        $voucherModel = new Voucher();
        $voucherModel->create([
            'entidad_id'   => $entidadId,
            'tipo_entidad' => $tipoEntidad,
            'tipo_voucher' => $tipoVoucher,
            'titulo'       => $titulo,
            'archivo_url'  => $newName,
        ]);

        $this->flash('exito', 'Voucher de servicio subido correctamente.');
        $this->redirect('/admin/contracts/' . $id);
    }
}
