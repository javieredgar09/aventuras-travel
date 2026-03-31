<?php
/** API Controller: Pagos */
class PagoApiController extends Controller {
    public function index(): void {
        $model = new Pago();
        $this->json(['data' => $model->getRecentTransactions(50)]);
    }
    public function show(string $id): void {
        $model = new Pago();
        $pago = $model->find((int) $id);
        if (!$pago) { $this->json(['error' => 'Pago no encontrado'], 404); return; }
        $this->json(['data' => $pago]);
    }
    public function store(): void {
        // Validar CSRF
        if (!$this->verifyCsrfToken()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
            return;
        }
        // Validar campos requeridos
        $contratoId = (int) $this->input('contrato_id');
        $concepto = $this->input('concepto');
        $monto = (float) $this->input('monto');
        $fecha = $this->input('fecha_vencimiento');

        if ($contratoId <= 0 || empty($concepto) || $monto <= 0 || empty($fecha)) {
            $this->json(['error' => 'Campos requeridos: contrato_id, concepto, monto, fecha_vencimiento'], 422);
            return;
        }

        $model = new Pago();
        $id = $model->create([
            'contrato_id'       => $contratoId,
            'concepto'          => $concepto,
            'monto'             => $monto,
            'fecha_vencimiento' => $fecha,
            'estado'            => 'pendiente',
        ]);
        $this->json(['data' => $model->find($id), 'message' => 'Pago creado'], 201);
    }
}
