<?php
class PassengerController extends Controller {
    public function index(): void {
        $data = [
            'title'      => 'Passengers - Aventuras Travel',
            'pasajeros'  => $this->db->fetchAll(
                "SELECT p.*, co.codigo as contrato_codigo, co.destino
                 FROM pasajeros p
                 JOIN contratos co ON p.contrato_id = co.id
                 ORDER BY p.nombre ASC LIMIT 100"
            ),
        ];
        $this->render('admin/passengers/index', $data, 'admin');
    }
}
