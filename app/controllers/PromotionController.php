<?php
/**
 * PromotionController.php - CRUD de promociones
 */
class PromotionController extends Controller {

    public function index(): void {
        $model = new Promocion();
        $promociones = $model->getAllWithStatus();
        $data = [
            'title'       => 'Promotions - Aventuras Travel',
            'promociones' => $promociones,
            'csrf_token'  => $this->generateCsrfToken(),
            'flash'       => $this->getFlash(),
        ];
        $this->render('admin/promotions/index', $data, 'admin');
    }

    public function create(): void {
        $data = [
            'title'      => 'New Promotion - Aventuras Travel',
            'csrf_token' => $this->generateCsrfToken(),
        ];
        $this->render('admin/promotions/create', $data, 'admin');
    }

    public function store(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/promotions/create');
            return;
        }

        $data = [
            'titulo'       => $this->input('titulo'),
            'descripcion'  => $this->input('descripcion'),
            'destino'      => $this->input('destino'),
            'descuento'    => $this->input('descuento'),
            'fecha_inicio' => $this->input('fecha_inicio'),
            'fecha_fin'    => $this->input('fecha_fin'),
            'activa'       => 1,
        ];

        $imagen = $this->handleImageUpload();
        if ($imagen) {
            $data['imagen'] = $imagen;
        }

        $model = new Promocion();
        $model->create($data);

        $this->flash('exito', 'Promoción creada exitosamente.');
        $this->redirect('/admin/promotions');
    }

    public function edit(string $id): void {
        $model = new Promocion();
        $promo = $model->find((int) $id);
        if (!$promo) {
            $this->redirect('/admin/promotions');
            return;
        }

        $data = [
            'title'      => 'Edit Promotion - Aventuras Travel',
            'promo'      => $promo,
            'csrf_token' => $this->generateCsrfToken(),
        ];
        $this->render('admin/promotions/edit', $data, 'admin');
    }

    public function update(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/promotions');
            return;
        }

        $model = new Promocion();
        $data = [
            'titulo'       => $this->input('titulo'),
            'descripcion'  => $this->input('descripcion'),
            'destino'      => $this->input('destino'),
            'descuento'    => $this->input('descuento'),
            'fecha_inicio' => $this->input('fecha_inicio'),
            'fecha_fin'    => $this->input('fecha_fin'),
        ];

        $imagen = $this->handleImageUpload();
        if ($imagen) {
            // Eliminar imagen anterior
            $promo = $model->find((int) $id);
            if ($promo && !empty($promo['imagen'])) {
                $oldPath = STORAGE_PATH . '/promociones/' . $promo['imagen'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $data['imagen'] = $imagen;
        }

        // Si se marcó eliminar imagen
        if ($this->input('eliminar_imagen') && !$imagen) {
            $promo = $promo ?? $model->find((int) $id);
            if ($promo && !empty($promo['imagen'])) {
                $oldPath = STORAGE_PATH . '/promociones/' . $promo['imagen'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $data['imagen'] = null;
        }

        $model->update((int) $id, $data);

        $this->flash('exito', 'Promoción actualizada.');
        $this->redirect('/admin/promotions');
    }

    public function delete(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/promotions');
            return;
        }

        $model = new Promocion();
        $promo = $model->find((int) $id);
        if ($promo && !empty($promo['imagen'])) {
            $imgPath = STORAGE_PATH . '/promociones/' . $promo['imagen'];
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }
        $model->destroy((int) $id);
        $this->flash('exito', 'Promoción eliminada.');
        $this->redirect('/admin/promotions');
    }

    public function toggle(string $id): void {
        $model = new Promocion();
        $promo = $model->find((int) $id);
        if ($promo) {
            $model->update((int) $id, ['activa' => $promo['activa'] ? 0 : 1]);
        }
        $this->redirect('/admin/promotions');
    }

    /**
     * Manejar subida de imagen de promoción
     * Tamaño recomendado: 800x450px (16:9), máx 2MB
     */
    private function handleImageUpload(): ?string {
        if (empty($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['imagen'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($file['size'] > $maxSize) {
            $this->flash('error', 'La imagen no debe superar 2MB.');
            return null;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowedMimes[$mime])) {
            $this->flash('error', 'Formato no permitido. Use JPG, PNG o WebP.');
            return null;
        }

        $ext = $allowedMimes[$mime];
        $hash = bin2hex(random_bytes(16));
        $filename = 'promo_' . $hash . '.' . $ext;
        $dest = STORAGE_PATH . '/promociones/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $this->flash('error', 'Error al subir la imagen.');
            return null;
        }

        return $filename;
    }
}
