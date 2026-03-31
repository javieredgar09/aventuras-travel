<?php
/**
 * Vista: Mis Pagos — Design System "The Elevated Explorer"
 * Variables: contratos, cuotas, pagos, resumen, user, csrf_token
 */
$contratos = $contratos ?? [];
$cuotas    = $cuotas ?? [];
$pagos     = $pagos ?? [];
$resumen   = $resumen ?? ['esperado' => 0, 'pagado' => 0, 'pendiente' => 0];

if (!function_exists('fmoney')) {
    function fmoney(float $a, string $c = 'USD'): string {
        $s = match(strtoupper($c)) { 'USD','$' => '$', 'EUR' => '€', 'PEN' => 'S/', default => strtoupper($c).' ' };
        return $s . number_format($a, 2);
    }
}

$moneda = 'USD';
$primerContrato = !empty($contratos) ? $contratos[0] : null;
$codigoContrato = htmlspecialchars($primerContrato['codigo'] ?? '');
$destino = htmlspecialchars($primerContrato['destino'] ?? 'Mi Viaje');
if ($primerContrato && !empty($primerContrato['moneda'])) {
    $moneda = $primerContrato['moneda'];
}

// Imagen hero por destino
$heroImages = [
    'cancún'     => 'https://images.unsplash.com/photo-1510097467424-192d713fd8b2?w=1200&q=80',
    'cancun'     => 'https://images.unsplash.com/photo-1510097467424-192d713fd8b2?w=1200&q=80',
    'punta cana' => 'https://images.unsplash.com/photo-1580237072617-771c3ecc4a24?w=1200&q=80',
    'cusco'      => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1200&q=80',
    'lima'       => 'https://images.unsplash.com/photo-1531968455001-5c5272a67c71?w=1200&q=80',
    'miami'      => 'https://images.unsplash.com/photo-1535498730771-e735b998cd64?w=1200&q=80',
    'cartagena'  => 'https://images.unsplash.com/photo-1583997052301-0fc38714e428?w=1200&q=80',
];
$heroImg = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80';
if ($primerContrato) {
    $destLower = strtolower($primerContrato['destino'] ?? '');
    foreach ($heroImages as $key => $url) {
        if (strpos($destLower, $key) !== false) { $heroImg = $url; break; }
    }
}
?>

<!-- HERO -->
<section class="relative h-64 md:h-80 rounded-[2rem] overflow-hidden mb-8 group shadow-2xl shadow-primary/10">
    <img class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?= htmlspecialchars($heroImg) ?>" alt="<?= $destino ?>">
    <div class="absolute inset-0 bg-gradient-to-t from-secondary/80 to-transparent z-10"></div>
    <div class="absolute bottom-0 left-0 p-8 z-20">
        <?php if ($codigoContrato): ?>
        <span class="bg-primary-container text-on-primary-container px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase mb-3 inline-block">Contrato <?= $codigoContrato ?></span>
        <?php endif; ?>
        <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter">Viaje a <?= $destino ?></h1>
        <p class="text-secondary-fixed mt-2 font-medium">Estado de Cuenta y Cronograma de Pagos</p>
    </div>
</section>

<!-- BENTO GRID -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- FINANCIAL SUMMARY (left) -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-surface-container-low rounded-[2rem] p-6">
            <h2 class="text-sm font-bold text-secondary uppercase tracking-widest mb-6">Resumen Financiero</h2>
            <div class="space-y-6">
                <div class="p-4 bg-surface-container-lowest rounded-xl">
                    <p class="text-xs text-outline mb-1 font-semibold uppercase">Valor Total del Contrato</p>
                    <p class="text-3xl font-black text-on-surface"><?= fmoney($resumen['esperado'], $moneda) ?></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-primary/10 rounded-xl border-l-4 border-primary">
                        <p class="text-xs text-primary mb-1 font-bold uppercase">Pagado</p>
                        <p class="text-xl font-bold text-primary"><?= fmoney($resumen['pagado'], $moneda) ?></p>
                    </div>
                    <div class="p-4 bg-error-container/30 rounded-xl border-l-4 border-error">
                        <p class="text-xs text-error mb-1 font-bold uppercase">Pendiente</p>
                        <p class="text-xl font-bold text-error"><?= fmoney($resumen['pendiente'], $moneda) ?></p>
                    </div>
                </div>
            </div>
            <div class="mt-8 space-y-3">
                <button onclick="document.getElementById('modalPago').classList.remove('hidden')" class="w-full py-4 bg-gradient-to-r from-primary to-primary-container text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">payments</span> Pagar Ahora
                </button>
                <button class="w-full py-3 bg-surface-container-highest text-on-surface-variant font-semibold rounded-xl hover:bg-surface-dim transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">download</span> Descargar Recibos
                </button>
            </div>
        </div>

        <!-- Penalty Policy -->
        <div class="bg-tertiary-fixed rounded-[2rem] p-6 border border-tertiary-container/50">
            <div class="flex items-start gap-4">
                <div class="bg-white p-2 rounded-xl shrink-0">
                    <span class="material-symbols-outlined text-tertiary">warning</span>
                </div>
                <div>
                    <h3 class="font-bold text-on-tertiary-container">Política de Penalidad</h3>
                    <p class="text-sm text-on-tertiary-fixed-variant mt-1 leading-relaxed">
                        Se aplicará una penalidad de <span class="font-bold">+$10 cada 2 días</span> de retraso después del periodo de gracia de 2 días.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- SCHEDULE & BREAKDOWN (right 2 cols) -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Payment Breakdown by Passenger (from pagos) -->
        <?php if (!empty($pagos)): ?>
        <div class="bg-white rounded-[2rem] overflow-hidden">
            <div class="px-6 py-4 bg-surface-container-high flex justify-between items-center">
                <h2 class="text-sm font-bold text-secondary uppercase tracking-widest">Historial de Transacciones</h2>
                <span class="material-symbols-outlined text-outline">receipt_long</span>
            </div>
            <div class="divide-y divide-surface-container">
                <?php foreach ($pagos as $p): ?>
                <div class="p-5 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-xl">payments</span>
                        </div>
                        <div>
                            <p class="font-bold text-on-surface"><?= htmlspecialchars($p['concepto'] ?? 'Pago') ?></p>
                            <p class="text-xs text-on-surface-variant"><?= !empty($p['created_at']) ? date('d M Y - H:i', strtotime($p['created_at'])) : '' ?></p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="font-black text-on-surface"><?= fmoney((float)($p['monto'] ?? 0), $moneda) ?></p>
                        <?php
                        $estadoColor = match($p['estado'] ?? 'pendiente') {
                            'aprobado' => 'bg-primary text-white',
                            'rechazado' => 'bg-error text-white',
                            default => 'bg-error-container text-error'
                        };
                        $estadoTexto = match($p['estado'] ?? 'pendiente') {
                            'aprobado' => 'Pagado',
                            'rechazado' => 'Rechazado',
                            default => 'En Revisión'
                        };
                        ?>
                        <span class="text-[10px] <?= $estadoColor ?> px-2 py-0.5 rounded-full font-bold uppercase tracking-tighter"><?= $estadoTexto ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Payment Schedule (Cronograma) -->
        <div class="bg-white rounded-[2rem] overflow-hidden">
            <div class="px-6 py-4 bg-secondary text-white flex justify-between items-center rounded-t-[2rem]">
                <h2 class="text-sm font-bold uppercase tracking-widest">Cronograma de Pagos</h2>
                <span class="material-symbols-outlined">event_available</span>
            </div>
            <div class="p-6">
                <?php if (!empty($cuotas)): ?>
                <div class="space-y-4">
                    <?php foreach ($cuotas as $c):
                        $esPagada = ($c['estado'] === 'pagada');
                        $esPendiente = ($c['estado'] === 'pendiente');
                        $esParcial = ($c['estado'] === 'parcial');
                    ?>
                    <div class="flex items-center gap-4 p-4 rounded-xl <?= $esPagada ? 'bg-surface-container-low border border-transparent' : ($esPendiente ? 'bg-surface-container-lowest border border-outline-variant/30' : 'bg-surface-container-lowest border border-outline-variant/30') ?> hover:border-primary-container transition-all <?= (!$esPagada && !$esPendiente && !$esParcial) ? 'opacity-70' : '' ?>">
                        <div class="w-12 h-12 rounded-full <?= $esPagada ? 'bg-primary text-white' : ($esPendiente ? 'bg-error-container text-error' : 'bg-surface-container-highest text-outline') ?> flex items-center justify-center font-bold shrink-0">
                            <span class="material-symbols-outlined" <?= $esPagada ? 'style="font-variation-settings: \'FILL\' 1;"' : '' ?>><?= $esPagada ? 'check_circle' : ($esPendiente ? 'pending_actions' : 'calendar_today') ?></span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-on-surface"><?= htmlspecialchars($c['concepto'] ?? 'Cuota') ?></p>
                                    <p class="text-xs text-on-surface-variant">Vence: <?= !empty($c['fecha_vencimiento']) ? date('d/m/Y', strtotime($c['fecha_vencimiento'])) : '-' ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-black <?= $esPagada ? 'text-primary' : 'text-on-surface' ?>"><?= fmoney((float)($c['monto_esperado'] ?? 0), $moneda) ?></p>
                                    <?php
                                    $badgeColor = match($c['estado']) {
                                        'pagada' => 'bg-primary text-white',
                                        'pendiente' => 'bg-error text-white',
                                        'parcial' => 'bg-tertiary-container text-on-tertiary-container',
                                        default => 'bg-outline-variant text-on-surface-variant'
                                    };
                                    ?>
                                    <span class="text-[10px] <?= $badgeColor ?> px-2 py-0.5 rounded-full font-bold uppercase tracking-tighter"><?= ucfirst($c['estado']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="py-8 text-center">
                    <p class="text-sm text-outline">No hay cuotas programadas.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- MODAL REGISTRAR PAGO -->
<div id="modalPago" class="fixed inset-0 z-[70] bg-secondary/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-surface-container-low flex-shrink-0">
            <h3 class="font-black text-secondary flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">upload_file</span> Registrar Pago
            </h3>
            <button onclick="document.getElementById('modalPago').classList.add('hidden')" class="text-outline hover:text-error transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= Router::url('/client/payments/register') ?>" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? $_SESSION['csrf_token'] ?? '') ?>">

                <?php if (count($contratos) > 1): ?>
                <div>
                    <label class="block text-xs font-bold text-secondary mb-1">Contrato</label>
                    <select name="contrato_id" id="client-contrato" required class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 focus:border-primary outline-none bg-white font-mono text-sm">
                        <?php foreach ($contratos as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['codigo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php elseif (count($contratos) === 1): ?>
                <input type="hidden" name="contrato_id" id="client-contrato" value="<?= $contratos[0]['id'] ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-xs font-bold text-secondary mb-1">Monto Pagado (USD)</label>
                    <input type="number" step="0.01" name="monto" id="client-monto" required min="1" placeholder="Ej: 500.00" class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 font-bold focus:border-primary outline-none">
                </div>

                <!-- Cascade Preview -->
                <div id="client-cascade" class="hidden rounded-xl border border-primary/20 bg-primary/5 p-4">
                    <p class="text-xs font-bold text-primary mb-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">info</span> Tu pago cubrirá:
                    </p>
                    <div id="client-cascade-detail" class="space-y-1.5"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-secondary mb-1">Concepto (Opcional)</label>
                    <input type="text" name="concepto" id="client-concepto" placeholder="Ej: Abono Cuota 2" class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 focus:border-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-secondary mb-1">Comprobante de Pago</label>
                    <input type="file" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" required class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 focus:border-primary outline-none text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    <p class="text-[10px] text-outline mt-1">Formatos: JPG, PNG, PDF. Max: 5MB.</p>
                </div>
                <button type="submit" class="w-full py-4 mt-4 rounded-xl bg-gradient-to-r from-primary to-primary-container text-white font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Subir Comprobante
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function(){
    const cuotasData = <?= json_encode($cuotasPorContrato ?? [], JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    const montoInput = document.getElementById('client-monto');
    const contratoInput = document.getElementById('client-contrato');
    const cascadeEl = document.getElementById('client-cascade');
    const cascadeDetail = document.getElementById('client-cascade-detail');
    const conceptoInput = document.getElementById('client-concepto');

    function fmt(n) { return '$' + Number(n).toFixed(2); }

    function getContratoId() {
        if (!contratoInput) return null;
        return contratoInput.value || null;
    }

    function updatePreview() {
        const cid = getContratoId();
        const monto = parseFloat(montoInput?.value) || 0;
        if (!cid || monto <= 0 || !cuotasData[cid] || cuotasData[cid].length === 0) {
            cascadeEl?.classList.add('hidden');
            return;
        }

        const cuotas = cuotasData[cid];
        let remaining = monto;
        const lines = [];
        const cuotaPagadas = [];

        for (let i = 0; i < cuotas.length; i++) {
            if (remaining <= 0) break;
            const q = cuotas[i];
            const faltante = q.faltante;

            if (remaining >= faltante) {
                lines.push(
                    '<div class="flex items-center justify-between gap-2">' +
                        '<div class="flex items-center gap-2">' +
                            '<span class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[10px] font-bold">' + q.numero + '</span>' +
                            '<span class="text-xs text-on-surface">' + (q.concepto || 'Cuota ' + q.numero) + '</span>' +
                        '</div>' +
                        '<span class="text-xs font-bold text-primary">' + fmt(faltante) + ' ✓</span>' +
                    '</div>'
                );
                remaining -= faltante;
                cuotaPagadas.push('Cuota ' + q.numero);
            } else {
                lines.push(
                    '<div class="flex items-center justify-between gap-2">' +
                        '<div class="flex items-center gap-2">' +
                            '<span class="w-5 h-5 rounded-full bg-tertiary-container text-on-tertiary-container flex items-center justify-center text-[10px] font-bold">' + q.numero + '</span>' +
                            '<span class="text-xs text-on-surface">' + (q.concepto || 'Cuota ' + q.numero) + ' (parcial)</span>' +
                        '</div>' +
                        '<span class="text-xs font-bold text-tertiary">' + fmt(remaining) + ' de ' + fmt(faltante) + '</span>' +
                    '</div>'
                );
                cuotaPagadas.push('Cuota ' + q.numero + ' (parcial)');
                remaining = 0;
            }
        }

        if (remaining > 0) {
            lines.push(
                '<div class="flex items-center justify-between gap-2 pt-1 border-t border-primary/10 mt-1">' +
                    '<span class="text-xs text-outline">Excedente a favor</span>' +
                    '<span class="text-xs font-bold text-primary">' + fmt(remaining) + '</span>' +
                '</div>'
            );
        }

        cascadeDetail.innerHTML = lines.join('');
        cascadeEl?.classList.remove('hidden');

        // Auto-fill concepto
        if (cuotaPagadas.length > 0 && !conceptoInput._userEdited) {
            conceptoInput.value = 'Pago ' + cuotaPagadas.join(', ');
        }
    }

    montoInput?.addEventListener('input', updatePreview);
    contratoInput?.addEventListener('change', function() {
        updatePreview();
    });
    conceptoInput?.addEventListener('input', function() {
        this._userEdited = this.value.length > 0;
    });

    // Trigger initial preview if single contract
    if (contratoInput?.tagName === 'INPUT') {
        montoInput?.addEventListener('input', updatePreview);
    }
})();
</script>
