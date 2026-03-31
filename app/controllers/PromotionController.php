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

        $model = new Promocion();
        $model->create([
            'titulo'       => $this->input('titulo'),
            'descripcion'  => $this->input('descripcion'),
            'destino'      => $this->input('destino'),
            'descuento'    => $this->input('descuento'),
            'fecha_inicio' => $this->input('fecha_inicio'),
            'fecha_fin'    => $this->input('fecha_fin'),
            'activa'       => 1,
        ]);

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
        $model->update((int) $id, [
            'titulo'       => $this->input('titulo'),
            'descripcion'  => $this->input('descripcion'),
            'destino'      => $this->input('destino'),
            'descuento'    => $this->input('descuento'),
            'fecha_inicio' => $this->input('fecha_inicio'),
            'fecha_fin'    => $this->input('fecha_fin'),
        ]);

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
}
