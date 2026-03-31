<?php
/** API Controller: Clientes */
class ClienteApiController extends Controller {
    public function index(): void {
        $model = new Cliente();
        $this->json(['data' => $model->getAllWithUsuario()]);
    }
    public function show(string $id): void {
        $model = new Cliente();
        $cliente = $model->getWithUsuario((int) $id);
        if (!$cliente) { $this->json(['error' => 'Cliente no encontrado'], 404); return; }
        $this->json(['data' => $cliente]);
    }
}
