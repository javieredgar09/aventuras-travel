<?php
/**
 * ReportController.php - Reportes y Estadísticas
 */
class ReportController extends Controller {

    public function index(): void {
        $grupoModel = new Grupo();
        $db = Database::getInstance();
        
        $stats = $grupoModel->getDashboardStats();
        $monthlyRevenue = $db->fetchAll("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as mes, SUM(monto) as total
            FROM pagos
            WHERE estado = 'aprobado'
            GROUP BY mes
            ORDER BY mes DESC LIMIT 12
        ");

        $salesByDest = $db->fetchAll("
            SELECT destino, COUNT(*) as cantidad, SUM(valor_total) as ingresos 
            FROM grupos 
            GROUP BY destino 
            ORDER BY ingresos DESC LIMIT 5
        ");

        $data = [
            'title'          => 'Reportes Generales - Aventuras Travel',
            'stats'          => $stats,
            'monthlyRevenue' => array_reverse($monthlyRevenue),
            'salesByDest'    => $salesByDest,
        ];
        
        $this->render('admin/reports/index', $data, 'admin');
    }

    public function exportCsv(): void {
        $type = $_GET['tipo'] ?? 'ventas';
        $filename = "reporte_{$type}_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // BOM for excel utf8 issue
        
        $db = Database::getInstance();

        if ($type === 'ventas') {
            fputcsv($output, ['ID', 'Nombre', 'Tipo', 'Destino', 'Pasajeros', 'Valor Total', 'Pagado', 'Saldo']);
            $grupos = $db->fetchAll("SELECT * FROM grupos ORDER BY id DESC");
            foreach ($grupos as $g) {
                fputcsv($output, [
                    $g['id'],
                    $g['nombre'],
                    $g['tipo'],
                    $g['destino'],
                    $g['total_pasajeros'],
                    $g['valor_total'],
                    $g['total_pagado'],
                    $g['saldo_pendiente']
                ]);
            }
        } elseif ($type === 'pagos') {
            fputcsv($output, ['ID', 'Entidad', 'Concepto', 'Monto', 'Fecha', 'Estado']);
            $pagos = $db->fetchAll("
                SELECT p.*, COALESCE(c.codigo, g.nombre) as entidad_nombre
                FROM pagos p
                LEFT JOIN contratos c ON p.contrato_id = c.id
                LEFT JOIN grupos g ON p.grupo_id = g.id
                ORDER BY p.id DESC
            ");
            foreach ($pagos as $p) {
                fputcsv($output, [
                    $p['id'],
                    $p['entidad_nombre'],
                    $p['concepto'],
                    $p['monto'],
                    $p['created_at'],
                    $p['estado']
                ]);
            }
        }
        
        fclose($output);
        exit;
    }
}
