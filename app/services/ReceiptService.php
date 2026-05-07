<?php
/**
 * ReceiptService.php - Genera recibos de pago profesionales en PDF usando FPDF
 * Aventuras Travel
 * RUC: 10475951587
 */

require_once BASE_PATH . '/vendor/fpdf/fpdf.php';

class ReceiptService {

    private Database $db;
    private const RUC = '10475951587';
    private const EMPRESA = 'AVENTURAS TRAVEL';
    private const SUBTITULO = 'Agencia de Viajes y Turismo';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Genera el recibo PDF para un pago aprobado.
     */
    public function generate(int $pagoId, array $cascadeDistribution = []): ?string {
        try {
            $pago = $this->getPaymentData($pagoId);
            if (!$pago || $pago['estado'] !== 'aprobado') {
                error_log("ReceiptService: Pago $pagoId no encontrado o no aprobado");
                return null;
            }

            $pago['cascade'] = $cascadeDistribution;

            // Obtener número correlativo
            $correlativo = $this->getCorrelativo($pagoId);
            $pago['correlativo'] = $correlativo;

            $pdf = $this->buildPDF($pago);

            // Guardar archivo
            $recibosDir = STORAGE_PATH . '/recibos';
            if (!is_dir($recibosDir)) {
                mkdir($recibosDir, 0755, true);
            }

            $filename = 'REC-' . str_pad($correlativo, 6, '0', STR_PAD_LEFT) . '.pdf';
            $fullPath = $recibosDir . '/' . $filename;

            $pdf->Output('F', $fullPath);
            $this->db->update('pagos', ['recibo_url' => $filename], 'id = ?', [$pagoId]);

            return $filename;
        } catch (Exception $e) {
            error_log("ReceiptService::generate error pago $pagoId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene número correlativo basado en total de recibos existentes
     */
    private function getCorrelativo(int $pagoId): int {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pagos WHERE recibo_url IS NOT NULL AND recibo_url != '' AND id < ?",
            [$pagoId]
        );
        return ((int)($result['total'] ?? 0)) + 1;
    }

    /**
     * Obtiene todos los datos necesarios para el recibo
     */
    private function getPaymentData(int $pagoId): ?array {
        $pago = $this->db->fetchOne("SELECT * FROM pagos WHERE id = ?", [$pagoId]);
        if (!$pago) return null;

        $contrato = null;
        if (!empty($pago['contrato_id'])) {
            $contrato = $this->db->fetchOne("SELECT * FROM contratos WHERE id = ?", [(int)$pago['contrato_id']]);
        }

        $grupo = null;
        $grupoId = $contrato['grupo_id'] ?? $pago['grupo_id'] ?? null;
        if ($grupoId) {
            $grupo = $this->db->fetchOne("SELECT * FROM grupos WHERE id = ?", [(int)$grupoId]);
        }

        // Datos del cliente
        $clienteNombre = '';
        $clienteEmail = '';
        $clienteTelefono = '';

        if ($contrato) {
            $clienteNombre = $contrato['titular_nombre'] ?? '';
            $clienteEmail = $contrato['titular_correo'] ?? '';
            $clienteTelefono = $contrato['titular_telefono'] ?? '';

            if (empty($clienteNombre) && !empty($contrato['cliente_id'])) {
                $cli = $this->db->fetchOne(
                    "SELECT u.nombre, u.apellido, u.email, u.telefono
                     FROM clientes cl JOIN usuarios u ON cl.usuario_id = u.id WHERE cl.id = ?",
                    [(int)$contrato['cliente_id']]
                );
                if ($cli) {
                    $clienteNombre = trim(($cli['nombre'] ?? '') . ' ' . ($cli['apellido'] ?? ''));
                    $clienteEmail = $cli['email'] ?? '';
                    $clienteTelefono = $cli['telefono'] ?? '';
                }
            }
        }

        // Total pagado y saldo
        $totalPagado = 0;
        $saldoPendiente = 0;
        if ($contrato) {
            $sum = $this->db->fetchOne(
                "SELECT COALESCE(SUM(monto), 0) as total FROM pagos WHERE contrato_id = ? AND estado = 'aprobado'",
                [(int)$contrato['id']]
            );
            $totalPagado = (float)($sum['total'] ?? 0);
            $valorTotal = (float)($contrato['valor_total'] ?? 0);
            $saldoPendiente = max(0, $valorTotal - $totalPagado);
        }

        $pago['contrato'] = $contrato;
        $pago['grupo'] = $grupo;
        $pago['cliente_nombre'] = $clienteNombre ?: 'No especificado';
        $pago['cliente_email'] = $clienteEmail;
        $pago['cliente_telefono'] = $clienteTelefono;
        $pago['total_pagado_contrato'] = $totalPagado;
        $pago['saldo_pendiente'] = $saldoPendiente;

        return $pago;
    }

    /**
     * Construye el PDF del recibo profesional
     */
    private function buildPDF(array $pago): FPDF {
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();

        $pw = 190;
        // IMPORTANTE: Siempre usar el monto del pago ACTUAL, no acumulado
        $montoActual = (float)($pago['monto'] ?? 0);
        $monto = $montoActual;
        $correlativo = $pago['correlativo'] ?? 0;
        $reciboNum = str_pad($correlativo, 6, '0', STR_PAD_LEFT);
        $fechaPago = !empty($pago['fecha_pago']) ? $pago['fecha_pago'] : date('Y-m-d');
        $fechaAprobacion = !empty($pago['fecha_aprobacion']) ? $pago['fecha_aprobacion'] : date('Y-m-d');

        // Determinar moneda del grupo y símbolo de moneda al inicio
        $moneda = 'USD';
        if (!empty($pago['grupo']['moneda_grupo'])) {
            $moneda = strtoupper($pago['grupo']['moneda_grupo']);
        } elseif (!empty($pago['moneda_grupo'])) {
            $moneda = strtoupper($pago['moneda_grupo']);
        }
        $monedaSymbol = $moneda === 'PEN' ? 'S/ ' : '$ ';

        // Colores
        $pR = 10; $pG = 42; $pB = 47;       // petroleo
        $aR = 0;  $aG = 150; $aB = 136;      // turquesa
        $sR = 16; $sG = 185; $sB = 129;      // verde

        // ══════════════════════════════════════════════════════════
        // HEADER CON LOGO
        // ══════════════════════════════════════════════════════════
        $pdf->SetFillColor($pR, $pG, $pB);
        $pdf->Rect(0, 0, 210, 48, 'F');

        // Franja turquesa decorativa
        $pdf->SetFillColor($aR, $aG, $aB);
        $pdf->Rect(0, 48, 210, 2, 'F');

        // Logo
        $logoPath = BASE_PATH . '/public/img/sin_fondo.png';
        if (file_exists($logoPath)) {
            try {
                // Verificar que la imagen sea válida antes de intentar incluirla
                $imageInfo = getimagesize($logoPath);
                if ($imageInfo && in_array($imageInfo[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG])) {
                    $pdf->Image($logoPath, 12, 5, 30, 0, 'PNG');
                } else {
                    throw new Exception('Imagen no válida');
                }
            } catch (Exception $e) {
                // Fallback: dibujar texto del logo
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetXY(12, 12);
                $pdf->Cell(30, 7, 'LOGO', 0, 0, 'C');
            }
        } else {
            // Fallback: dibujar texto del logo
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetXY(12, 12);
            $pdf->Cell(30, 7, 'LOGO', 0, 0, 'C');
        }

        // Nombre empresa (al lado del logo)
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(52, 8);
        $pdf->Cell(60, 7, $this->u(self::EMPRESA), 0, 1, 'L');

        // Subtítulo y RUC
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(52, 16);
        $pdf->Cell(60, 4, $this->u(self::SUBTITULO), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(52, 21);
        $pdf->Cell(60, 4, $this->u('RUC: ' . self::RUC), 0, 1, 'L');

        // Recibo info derecha
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(120, 6);
        $pdf->Cell(80, 8, $this->u('RECIBO DE PAGO'), 0, 1, 'R');

        // Número correlativo grande
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor($aR, $aG, $aB);
        $pdf->SetXY(120, 16);
        $pdf->Cell(80, 8, $this->u('N' . chr(176) . ' ' . $reciboNum), 0, 1, 'R');

        // Fecha de emisión
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(200, 220, 220);
        $pdf->SetXY(120, 26);
        $pdf->Cell(80, 5, $this->u('Fecha: ' . date('d/m/Y', strtotime($fechaPago))), 0, 1, 'R');

        // Hora de emisión
        $pdf->SetXY(120, 32);
        $pdf->Cell(80, 5, $this->u('Emitido: ' . date('d/m/Y H:i')), 0, 1, 'R');

        // ══════════════════════════════════════════════════════════
        // DATOS DEL CLIENTE
        // ══════════════════════════════════════════════════════════
        $y = 57;
        $this->secTitle($pdf, 'DATOS DEL CLIENTE', $y, $aR, $aG, $aB);
        $y += 8;

        $pdf->SetFillColor(248, 250, 252);
        $pdf->Rect(10, $y, $pw, 20, 'F');
        $pdf->SetDrawColor(230, 230, 230);
        $pdf->Rect(10, $y, $pw, 20, 'D');

        $this->kv($pdf, 14, $y + 3, 'Cliente:', $pago['cliente_nombre']);
        $this->kv($pdf, 110, $y + 3, 'Contrato:', $pago['contrato']['codigo'] ?? '-');
        $this->kv($pdf, 14, $y + 11, 'Correo:', $pago['cliente_email'] ?: '-');
        $destino = $pago['contrato']['destino'] ?? ($pago['grupo']['destino'] ?? '-');
        $this->kv($pdf, 110, $y + 11, 'Destino:', $destino);

        // ══════════════════════════════════════════════════════════
        // DESCRIPCIÓN DEL PAGO
        // ══════════════════════════════════════════════════════════
        $y += 28;
        $this->secTitle($pdf, 'DESCRIPCI' . chr(211) . 'N DEL PAGO', $y, $aR, $aG, $aB);
        $y += 8;

        $concepto = $pago['concepto'] ?? 'Pago de cuota';
        $metodo = $pago['metodo_pago'] ?? 'Efectivo';
        $banco = $pago['banco'] ?? '';

        // Descripción como texto
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(50, 50, 50);
        $pdf->SetXY(10, $y);
        $descripcion = 'Concepto: ' . $concepto;
        if ($banco) $descripcion .= ' | Banco: ' . $banco;
        $descripcion .= ' | M' . chr(233) . 'todo: ' . $metodo . ' | Moneda del Contrato: ' . $moneda;
        $pdf->MultiCell($pw, 5, $this->u($descripcion), 0, 'L');
        $y = $pdf->GetY() + 3;

        // ══════════════════════════════════════════════════════════
        // DISTRIBUCIÓN EN CUOTAS
        // ══════════════════════════════════════════════════════════
        $cascade = $pago['cascade'] ?? [];
        if (!empty($cascade)) {
            $this->secTitle($pdf, 'DISTRIBUCI' . chr(211) . 'N DEL PAGO EN CUOTAS', $y, $aR, $aG, $aB);
            $y += 8;

            // Table header
            $pdf->SetFillColor($pR, $pG, $pB);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(10, $y);
            $pdf->Cell(15, 7, $this->u('N' . chr(176)), 0, 0, 'C', true);
            $pdf->Cell(65, 7, $this->u('  Cuota'), 0, 0, 'L', true);
            $pdf->Cell(35, 7, $this->u('Valor Cuota'), 0, 0, 'C', true);
            $pdf->Cell(35, 7, $this->u('Monto Aplicado'), 0, 0, 'R', true);
            $pdf->Cell(40, 7, $this->u('Estado'), 0, 1, 'C', true);
            $y += 7;

            $fill = false;
            foreach ($cascade as $c) {
                $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
                $pdf->SetTextColor(40, 40, 40);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetXY(10, $y);
                $pdf->Cell(15, 7, $c['numero'], 0, 0, 'C', true);
                $pdf->Cell(65, 7, $this->u('  ' . $this->trunc($c['concepto'], 35)), 0, 0, 'L', true);
                $pdf->Cell(35, 7, $this->u($monedaSymbol . number_format($c['esperado'], 2)), 0, 0, 'C', true);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(35, 7, $this->u($monedaSymbol . number_format($c['aplicado'], 2)), 0, 0, 'R', true);

                if ($c['tipo'] === 'completa') {
                    $pdf->SetTextColor($sR, $sG, $sB);
                    $txt = chr(214) . ' COMPLETA';
                } else {
                    $pdf->SetTextColor(220, 150, 0);
                    $txt = '~ PARCIAL';
                }
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(40, 7, $this->u($txt), 0, 1, 'C', true);
                $y += 7;
                $fill = !$fill;
            }
            $y += 3;
        }

        // ══════════════════════════════════════════════════════════
        // MONTO TOTAL + ESTADO
        // ══════════════════════════════════════════════════════════
        
        // Caja del monto total del PAGO ACTUAL
        $pdf->SetFillColor($aR, $aG, $aB);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(100, $y);
        $pdf->Cell(45, 12, $this->u('  MONTO PAGADO:'), 0, 0, 'L', true);
        $pdf->SetFont('Arial', 'B', 14);
        // MOSTRAR SIEMPRE EL MONTO ACTUAL DE ESTE PAGO
        $pdf->Cell(45, 12, $this->u($monedaSymbol . number_format($monto, 2) . '  '), 0, 1, 'R', true);
        $y += 15;

        // Estado badge
        $pdf->SetFillColor($sR, $sG, $sB);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY(10, $y);
        $pdf->Cell(50, 7, $this->u('  ESTADO: APROBADO'), 0, 0, 'L', true);

        $pdf->SetFillColor(248, 250, 252);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(70, 7, $this->u('  Fecha Pago: ' . date('d/m/Y', strtotime($fechaPago))), 0, 0, 'L', true);
        $pdf->Cell(70, 7, $this->u('Aprobaci' . chr(243) . 'n: ' . date('d/m/Y', strtotime($fechaAprobacion)) . '  '), 0, 1, 'R', true);
        $y += 12;

        // ══════════════════════════════════════════════════════════
        // RESUMEN DEL CONTRATO
        // ══════════════════════════════════════════════════════════
        if ($pago['contrato']) {
            $this->secTitle($pdf, 'RESUMEN DEL CONTRATO', $y, $aR, $aG, $aB);
            $y += 8;
            $valorTotal = (float)($pago['contrato']['valor_total'] ?? 0);
            $colW = $pw / 3;

            $pdf->SetFillColor(248, 250, 252);
            $pdf->SetXY(10, $y);
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->Cell($colW, 5, $this->u('Valor Total del Contrato'), 1, 0, 'C', true);
            $pdf->Cell($colW, 5, $this->u('Total Pagado a la Fecha'), 1, 0, 'C', true);
            $pdf->Cell($colW + 0.3, 5, $this->u('Saldo Pendiente'), 1, 1, 'C', true);

            $pdf->SetXY(10, $y + 5);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetTextColor($pR, $pG, $pB);
            $pdf->Cell($colW, 8, $this->u($monedaSymbol . number_format($valorTotal, 2)), 1, 0, 'C');
            $pdf->SetTextColor($aR, $aG, $aB);
            $pdf->Cell($colW, 8, $this->u($monedaSymbol . number_format($pago['total_pagado_contrato'], 2)), 1, 0, 'C');
            $pend = $pago['saldo_pendiente'];
            $pdf->SetTextColor($pend <= 0 ? $sR : 220, $pend <= 0 ? $sG : 50, $pend <= 0 ? $sB : 50);
            $pdf->Cell($colW + 0.3, 8, $this->u($monedaSymbol . number_format($pend, 2)), 1, 1, 'C');
        }

        // ══════════════════════════════════════════════════════════
        // PIE DE PÁGINA
        // ══════════════════════════════════════════════════════════
        $pdf->SetFillColor($pR, $pG, $pB);
        $pdf->Rect(0, 272, 210, 25, 'F');

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(10, 274);
        $pdf->Cell(95, 4, $this->u(self::EMPRESA . ' | RUC: ' . self::RUC), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(95, 4, $this->u('Recibo N' . chr(176) . ' ' . $reciboNum . ' | Documento electr' . chr(243) . 'nico'), 0, 1, 'R');

        $pdf->SetXY(10, 279);
        $pdf->SetTextColor(180, 200, 200);
        $pdf->Cell(95, 4, $this->u('info@aventurastravel.com'), 0, 0, 'L');
        $pdf->Cell(95, 4, $this->u('www.aventurastravel.com'), 0, 1, 'R');

        // Nota legal
        $pdf->SetFont('Arial', 'I', 6);
        $pdf->SetTextColor(170, 190, 190);
        $pdf->SetXY(10, 284);
        $pdf->Cell($pw, 4, $this->u('Comprobante digital emitido por ' . self::EMPRESA . '. Generado el ' . date('d/m/Y H:i') . '.'), 0, 0, 'C');

        return $pdf;
    }

    // ── Helpers ──

    private function secTitle(FPDF $pdf, string $title, float $y, int $r, int $g, int $b): void {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->SetXY(10, $y);
        $pdf->Cell(190, 5, $this->u($title), 0, 1, 'L');
    }

    private function kv(FPDF $pdf, float $x, float $y, string $label, string $value): void {
        $pdf->SetXY($x, $y);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(22, 5, $this->u($label), 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->Cell(60, 5, $this->u($this->trunc($value, 35)), 0, 0);
    }

    private function u(string $text): string {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
    }

    private function trunc(string $text, int $max): string {
        return mb_strlen($text) > $max ? mb_substr($text, 0, $max - 3) . '...' : $text;
    }
}
