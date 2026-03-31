<?php
/** API Controller: Contratos */
class ContratoApiController extends Controller {
    public function index(): void {
        $model = new Contrato();
        $this->json(['data' => $model->getRecentWithClient(50)]);
    }
    public function show(string $id): void {
        $model = new Contrato();
        $contrato = $model->getFullDetails((int) $id);
        if (!$contrato) { $this->json(['error' => 'Contrato no encontrado'], 404); return; }
        $this->json(['data' => $contrato]);
    }
}
