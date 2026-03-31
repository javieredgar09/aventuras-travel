<?php
/**
 * AdminController.php - Dashboard administrativo
 */
class AdminController extends Controller {

    public function dashboard(): void {
        $contratoModel = new Contrato();
        $promocionModel = new Promocion();
        $pagoModel = new Pago();

        $stats = $contratoModel->getStats();
        $promociones = $promocionModel->getAllWithStatus();
        $recentTransactions = $pagoModel->getRecentTransactions(5);

        // Prepare sparkline data: last 6 months (labels + totals)
        $months = [];
        $sparkRecaudado = [];
        $sparkContratos = [];
        $sparkPasajeros = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = new DateTime("-{$i} months");
            $m = (int) $dt->format('n');
            $y = (int) $dt->format('Y');
            $months[] = $dt->format('M');

            $rec = $pagoModel->fetchOneRaw(
                "SELECT COALESCE(SUM(monto),0) as total FROM pagos WHERE estado = 'aprobado' AND MONTH(COALESCE(fecha_aprobacion, created_at)) = ? AND YEAR(COALESCE(fecha_aprobacion, created_at)) = ?",
                [$m, $y]
            );
            $sparkRecaudado[] = (float) ($rec['total'] ?? 0);

            $ct = $contratoModel->fetchOneRaw(
                "SELECT COUNT(*) as total FROM contratos WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?",
                [$m, $y]
            );
            $sparkContratos[] = (int) ($ct['total'] ?? 0);

            $ps = $contratoModel->fetchOneRaw(
                "SELECT COUNT(*) as total FROM pasajeros WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?",
                [$m, $y]
            );
            $sparkPasajeros[] = (int) ($ps['total'] ?? 0);
        }

        $data = [
            'title'               => 'Executive Dashboard - Aventuras Travel',
            'stats'               => $stats,
            'promociones'         => $promociones,
            'recentTransactions'  => $recentTransactions,
            'spark_labels'        => $months,
            'spark_recaudado'     => $sparkRecaudado,
            'spark_contratos'     => $sparkContratos,
            'spark_pasajeros'     => $sparkPasajeros,
            'csrf_token'          => $this->generateCsrfToken(),
            'flash'               => $this->getFlash(),
        ];
        $this->render('admin/dashboard', $data, 'admin');
    }
}
