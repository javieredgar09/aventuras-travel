<?php
/** API Controller: Promociones */
class PromocionApiController extends Controller {
    public function index(): void {
        $model = new Promocion();
        $this->json(['data' => $model->all()]);
    }
    public function activas(): void {
        $model = new Promocion();
        $this->json(['data' => $model->getActivas()]);
    }
}
