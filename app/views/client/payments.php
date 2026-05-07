<?php
/**
 * Vista: Mis Pagos — Contratos, Vouchers, Historial de Pagos con detalle
 * Variables: contratos, cuotas, pagos, resumen, user, csrf_token, archivos, vouchers
 */
require_once __DIR__ . '/../../helpers/DestinationHelper.php';

$contratos = $contratos ?? [];
$cuotas    = $cuotas ?? [];
$pagos     = $pagos ?? [];
$resumen   = $resumen ?? ['esperado' => 0, 'pagado' => 0, 'pendiente' => 0];
$archivos  = $archivos ?? [];
$vouchers  = $vouchers ?? [];

if (!function_exists('fmoney')) {
    function fmoney(float $a, string $c = 'USD'): string {
        return '$' . number_format($a, 2);
    }
}

$moneda = 'USD';
$primerContrato = !empty($contratos) ? $contratos[0] : null;
$codigoContrato = htmlspecialchars($primerContrato['codigo'] ?? '');
$destino = htmlspecialchars($primerContrato['destino'] ?? 'Mi Viaje');

// Bank brand colors
$bankColors = [
    'BCP' => ['bg' => 'bg-blue-600', 'text' => 'text-blue-600', 'light' => 'bg-blue-50', 'border' => 'border-blue-200'],
    'BBVA Continental' => ['bg' => 'bg-sky-700', 'text' => 'text-sky-700', 'light' => 'bg-sky-50', 'border' => 'border-sky-200'],
    'Interbank' => ['bg' => 'bg-green-600', 'text' => 'text-green-600', 'light' => 'bg-green-50', 'border' => 'border-green-200'],
    'Scotiabank' => ['bg' => 'bg-red-600', 'text' => 'text-red-600', 'light' => 'bg-red-50', 'border' => 'border-red-200'],
];

$metodoPagoIcons = [
    'Efectivo' => 'payments',
    'Transferencia bancaria' => 'swap_horiz',
    'Depósito bancario' => 'account_balance',
    'Yape' => 'phone_android',
    'Plin' => 'phone_android',
];

// Imagen dinámica del destino via helper centralizado
$heroImg = DestinationHelper::getHeroImage($destino);
$destIcon = DestinationHelper::getIcon($destino);
$accentColor = DestinationHelper::getAccentColor($destino);
?>

<!-- HERO — Imagen dinámica del destino -->
<section class="relative h-[220px] sm:h-[260px] md:h-[300px] rounded-2xl overflow-hidden mb-6 group shadow-2xl">
    <img class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?= htmlspecialchars($heroImg) ?>" alt="<?= $destino ?>" fetchpriority="high">
    <div class="absolute inset-0 bg-gradient-to-r from-petroleo-dark/95 via-petroleo/60 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark/90 via-transparent to-transparent"></div>
    <div class="absolute top-0 right-0 w-60 h-60 rounded-full blur-3xl -translate-y-1/3 translate-x-1/4 pointer-events-none" style="background:<?= $accentColor ?>33;"></div>
    <div class="absolute bottom-0 left-0 p-5 sm:p-7">
        <?php if ($codigoContrato): ?>
        <span class="bg-white/10 backdrop-blur-xl text-white text-[10px] font-black tracking-widest uppercase px-4 py-2 rounded-full border border-white/20 mb-3 inline-flex items-center gap-2 shadow-lg">
            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
            Contrato <?= $codigoContrato ?>
        </span>
        <?php endif; ?>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-tight drop-shadow-2xl">
            <span class="mr-1"><?= $destIcon ?></span> Viaje a <?= $destino ?>
        </h1>
        <p class="text-white/70 mt-2 text-xs font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-sm text-turquesa-light">account_balance_wallet</span>
            Estado de Cuenta &middot; Documentos &middot; Comprobantes
        </p>
    </div>
</section>

<!-- TABS NAVIGATION -->
<div class="sticky top-16 sm:top-20 z-40 bg-surface-bright/80 backdrop-blur-lg mb-5 -mx-2 sm:-mx-4 px-2 sm:px-4 py-1.5">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center bg-white rounded-2xl shadow-sm p-1 border border-outline-variant/10 overflow-x-auto">
            <button onclick="showPayTab('overview')" class="pay-tab flex-1 flex flex-col items-center gap-0.5 py-2 sm:py-3 px-2 rounded-xl text-primary bg-primary-fixed/30 border-b-2 border-primary transition-all min-w-[65px]" data-tab="overview">
                <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-tighter">Resumen</span>
            </button>
            <button onclick="showPayTab('payments')" class="pay-tab flex-1 flex flex-col items-center gap-0.5 py-2 sm:py-3 px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all min-w-[65px]" data-tab="payments">
                <span class="material-symbols-outlined text-xl">receipt_long</span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-tighter">Pagos</span>
            </button>
            <button onclick="showPayTab('documents')" class="pay-tab flex-1 flex flex-col items-center gap-0.5 py-2 sm:py-3 px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all min-w-[65px]" data-tab="documents">
                <span class="material-symbols-outlined text-xl">folder_open</span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-tighter">Documentos</span>
            </button>
            <button onclick="showPayTab('schedule')" class="pay-tab flex-1 flex flex-col items-center gap-0.5 py-2 sm:py-3 px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all min-w-[65px]" data-tab="schedule">
                <span class="material-symbols-outlined text-xl">event_available</span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-tighter">Cronograma</span>
            </button>
        </div>
    </div>
</div>

<!-- ==================== TAB: OVERVIEW ==================== -->
<section class="pay-panel" id="panel-overview">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Financial Summary -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-outline-variant/10">
                <h2 class="text-[10px] font-bold text-outline uppercase tracking-widest mb-4">Resumen Financiero</h2>
                <div class="space-y-4">
                    <div class="p-3 bg-surface-container-low rounded-xl">
                        <p class="text-[10px] text-outline mb-0.5 font-semibold uppercase">Valor Total del Contrato</p>
                        <p class="text-2xl font-black text-secondary"><?= fmoney($resumen['esperado'], $moneda) ?></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-primary/10 rounded-xl border-l-4 border-primary">
                            <p class="text-[10px] text-primary mb-0.5 font-bold uppercase">Pagado</p>
                            <p class="text-lg font-bold text-primary"><?= fmoney($resumen['pagado'], $moneda) ?></p>
                        </div>
                        <div class="p-3 bg-error-container/30 rounded-xl border-l-4 border-error">
                            <p class="text-[10px] text-error mb-0.5 font-bold uppercase">Pendiente</p>
                            <p class="text-lg font-bold text-error"><?= fmoney($resumen['pendiente'], $moneda) ?></p>
                        </div>
                    </div>
                    <!-- Progress bar -->
                    <?php $pctPaid = $resumen['esperado'] > 0 ? round(($resumen['pagado'] / $resumen['esperado']) * 100) : 0; ?>
                    <div>
                        <div class="flex justify-between text-[10px] mb-1">
                            <span class="font-bold text-secondary"><?= $pctPaid ?>% completado</span>
                            <span class="text-outline"><?= fmoney($resumen['pagado'], $moneda) ?> / <?= fmoney($resumen['esperado'], $moneda) ?></span>
                        </div>
                        <div class="w-full bg-surface-container-high rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all <?= $pctPaid >= 100 ? 'bg-green-500' : 'bg-gradient-to-r from-primary-container to-primary' ?>" style="width: <?= $pctPaid ?>%"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    <button onclick="document.getElementById('modalPago').classList.remove('hidden')" class="w-full py-3 bg-secondary text-white font-bold rounded-xl shadow-md hover:bg-primary active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-base">payments</span> Registrar Pago
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent payments + Quick docs -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm text-center">
                    <span class="material-symbols-outlined text-primary text-2xl mb-1 block">receipt_long</span>
                    <p class="text-2xl font-black text-on-surface"><?= count($pagos) ?></p>
                    <p class="text-[10px] text-outline uppercase font-bold tracking-widest">Pagos</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm text-center">
                    <span class="material-symbols-outlined text-primary text-2xl mb-1 block">description</span>
                    <?php $totalDocs = 0; foreach ($archivos as $docs) $totalDocs += count($docs); ?>
                    <p class="text-2xl font-black text-on-surface"><?= $totalDocs ?></p>
                    <p class="text-[10px] text-outline uppercase font-bold tracking-widest">Contratos</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm text-center">
                    <span class="material-symbols-outlined text-primary text-2xl mb-1 block">confirmation_number</span>
                    <?php $totalVch = 0; foreach ($vouchers as $vcs) $totalVch += count($vcs); ?>
                    <p class="text-2xl font-black text-on-surface"><?= $totalVch ?></p>
                    <p class="text-[10px] text-outline uppercase font-bold tracking-widest">Vouchers</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm text-center">
                    <span class="material-symbols-outlined text-primary text-2xl mb-1 block">event_available</span>
                    <p class="text-2xl font-black text-on-surface"><?= count($cuotas) ?></p>
                    <p class="text-[10px] text-outline uppercase font-bold tracking-widest">Cuotas</p>
                </div>
            </div>

            <!-- Last 5 payments -->
            <?php if (!empty($pagos)): ?>
            <div class="bg-white rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm">
                <div class="px-6 py-4 bg-surface-container-high flex justify-between items-center">
                    <h2 class="text-sm font-bold text-secondary uppercase tracking-widest">Últimos Pagos</h2>
                    <button onclick="showPayTab('payments')" class="text-xs text-primary font-bold hover:underline">Ver todos →</button>
                </div>
                <div class="divide-y divide-surface-container">
                    <?php foreach (array_slice($pagos, 0, 5) as $p):
                        $metodo = $p['metodo_pago'] ?? 'Efectivo';
                        $mIcon = $metodoPagoIcons[$metodo] ?? 'payments';
                        $bancoName = $p['banco'] ?? '';
                        $monedaPg = strtoupper($p['moneda_pago'] ?? 'PEN');
                        $bankStyle = $bankColors[$bancoName] ?? ['bg' => 'bg-slate-600', 'text' => 'text-slate-600', 'light' => 'bg-slate-50', 'border' => 'border-slate-200'];
                    ?>
                    <div class="p-5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl <?= $bankStyle['light'] ?> <?= $bankStyle['border'] ?> border flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-xl <?= $bankStyle['text'] ?>"><?= $mIcon ?></span>
                            </div>
                            <div>
                                <p class="font-bold text-on-surface text-sm"><?= htmlspecialchars($p['concepto'] ?? 'Pago') ?></p>
                                <div class="flex items-center gap-2 text-[10px] text-on-surface-variant">
                                    <span><?= htmlspecialchars($metodo) ?></span>
                                    <?php if ($bancoName): ?>
                                    <span class="w-1 h-1 rounded-full bg-outline/30"></span>
                                    <span class="font-bold"><?= htmlspecialchars($bancoName) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($p['moneda_pago']) && strtoupper($p['moneda_pago']) === 'PEN'): ?>
                                    <span class="w-1 h-1 rounded-full bg-outline/30"></span>
                                    <span>Pagó en Soles</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-black text-on-surface">$<?= number_format((float)($p['monto'] ?? 0), 2) ?></p>
                            <?php
                            $estadoColor = match($p['estado'] ?? 'pendiente') {
                                'aprobado' => 'bg-primary text-white',
                                'rechazado' => 'bg-error text-white',
                                default => 'bg-error-container text-error'
                            };
                            $estadoTexto = match($p['estado'] ?? 'pendiente') {
                                'aprobado' => 'Aprobado',
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
        </div>
    </div>
</section>

<!-- ==================== TAB: PAYMENTS (All transactions with full details) ==================== -->
<section class="pay-panel hidden" id="panel-payments">
    <div class="flex items-end justify-between px-2 mb-6">
        <div>
            <h2 class="text-3xl font-black text-secondary tracking-tighter">Historial de Pagos</h2>
            <p class="text-sm text-outline mt-1"><?= count($pagos) ?> transacción<?= count($pagos) !== 1 ? 'es' : '' ?> registrada<?= count($pagos) !== 1 ? 's' : '' ?></p>
        </div>
        <button onclick="document.getElementById('modalPago').classList.remove('hidden')" class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary-container transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined text-lg">add</span> Nuevo Pago
        </button>
    </div>

    <?php if (!empty($pagos)): ?>
    <div class="space-y-4">
        <?php foreach ($pagos as $pi => $p):
            $metodo = $p['metodo_pago'] ?? 'Efectivo';
            $mIcon = $metodoPagoIcons[$metodo] ?? 'payments';
            $bancoName = $p['banco'] ?? '';
            $monedaPg = strtoupper($p['moneda_pago'] ?? 'PEN');
            $bankStyle = $bankColors[$bancoName] ?? ['bg' => 'bg-slate-600', 'text' => 'text-slate-600', 'light' => 'bg-slate-50', 'border' => 'border-slate-200'];
            $estadoColor = match($p['estado'] ?? 'pendiente') {
                'aprobado' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'rechazado' => 'bg-red-50 text-red-700 border-red-200',
                default => 'bg-amber-50 text-amber-700 border-amber-200'
            };
            $estadoTexto = match($p['estado'] ?? 'pendiente') {
                'aprobado' => 'Aprobado',
                'rechazado' => 'Rechazado',
                default => 'En Revisión'
            };
            $hasFile = !empty($p['comprobante_url']);
            $hasComprobantes = !empty($p['comprobantes']);
        ?>
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-md transition-all">
            <!-- Header -->
            <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 cursor-pointer" onclick="togglePago(<?= $pi ?>)">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl <?= $bankStyle['light'] ?> <?= $bankStyle['border'] ?> border flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-2xl <?= $bankStyle['text'] ?>"><?= $mIcon ?></span>
                    </div>
                    <div>
                        <p class="font-bold text-on-surface"><?= htmlspecialchars($p['concepto'] ?? 'Pago') ?></p>
                        <div class="flex flex-wrap items-center gap-1.5 mt-1">
                            <!-- Method badge -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md <?= $bankStyle['light'] ?> <?= $bankStyle['border'] ?> border text-[10px] font-bold <?= $bankStyle['text'] ?>">
                                <span class="material-symbols-outlined text-[11px]"><?= $mIcon ?></span>
                                <?= htmlspecialchars($metodo) ?>
                            </span>
                            <!-- Bank badge -->
                            <?php if ($bancoName): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md <?= $bankStyle['light'] ?> <?= $bankStyle['border'] ?> border text-[10px] font-bold <?= $bankStyle['text'] ?>">
                                <span class="material-symbols-outlined text-[11px]">account_balance</span>
                                <?= htmlspecialchars($bancoName) ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($p['moneda_pago']) && strtoupper($p['moneda_pago']) === 'PEN'): ?>
                            <!-- Paid in soles indicator -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 border border-amber-200 text-[10px] font-bold text-amber-700">
                                Pagó en S/ Soles
                            </span>
                            <?php endif; ?>
                            <!-- File indicator -->
                            <?php if ($hasFile || $hasComprobantes): ?>
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md bg-primary/5 border border-primary/20 text-[10px] font-bold text-primary">
                                <span class="material-symbols-outlined text-[11px]">attach_file</span>
                                Archivo
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="text-right">
                        <p class="text-xl font-black text-on-surface">$<?= number_format((float)($p['monto'] ?? 0), 2) ?></p>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border <?= $estadoColor ?>"><?= $estadoTexto ?></span>
                    </div>
                    <span class="material-symbols-outlined text-outline/30 transition-transform pago-chevron" id="chevron-<?= $pi ?>">expand_more</span>
                </div>
            </div>

            <!-- Expandable Details -->
            <div class="hidden border-t border-slate-100" id="pago-detail-<?= $pi ?>">
                <div class="p-5 bg-surface/50 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-[10px] text-outline font-bold uppercase tracking-widest mb-1">Fecha de Pago</p>
                        <p class="text-sm font-bold text-on-surface"><?= !empty($p['fecha_pago']) ? date('d M Y', strtotime($p['fecha_pago'])) : (!empty($p['created_at']) ? date('d M Y', strtotime($p['created_at'])) : '—') ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-outline font-bold uppercase tracking-widest mb-1">Contrato</p>
                        <p class="text-sm font-bold text-on-surface font-mono"><?= htmlspecialchars($p['contrato_codigo'] ?? '—') ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-outline font-bold uppercase tracking-widest mb-1">Referencia</p>
                        <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars($p['referencia'] ?? '—') ?></p>
                    </div>
                </div>

                <!-- Attached files -->
                <?php if ($hasFile || $hasComprobantes): ?>
                <div class="px-5 pb-5 pt-2">
                    <p class="text-[10px] text-outline font-bold uppercase tracking-widest mb-3">Archivos Adjuntos</p>
                    <div class="flex flex-wrap gap-3">
                        <?php if ($hasFile):
                            $ext = strtolower(pathinfo($p['comprobante_url'], PATHINFO_EXTENSION));
                            $isPdf = $ext === 'pdf';
                            $fileUrl = Router::url('/storage/comprobantes/' . htmlspecialchars($p['comprobante_url']));
                        ?>
                        <a href="<?= $fileUrl ?>" target="_blank" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white hover:border-primary/40 hover:shadow-md transition-all group max-w-xs">
                            <?php if ($isPdf): ?>
                            <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-red-600 text-2xl">picture_as_pdf</span>
                            </div>
                            <?php else: ?>
                            <div class="w-12 h-12 rounded-lg overflow-hidden border border-slate-200 shrink-0">
                                <img src="<?= $fileUrl ?>" alt="Comprobante" class="w-full h-full object-cover">
                            </div>
                            <?php endif; ?>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-on-surface group-hover:text-primary truncate">Comprobante</p>
                                <p class="text-[10px] text-outline uppercase"><?= $isPdf ? 'PDF' : strtoupper($ext) ?></p>
                            </div>
                            <span class="material-symbols-outlined text-outline/30 text-sm group-hover:text-primary shrink-0">open_in_new</span>
                        </a>
                        <?php endif; ?>

                        <?php if ($hasComprobantes):
                            foreach ($p['comprobantes'] as $comp):
                                $cExt = strtolower(pathinfo($comp['archivo_hash'], PATHINFO_EXTENSION));
                                $cIsPdf = $cExt === 'pdf';
                                $cUrl = Router::url('/storage/comprobantes/' . htmlspecialchars($comp['archivo_hash']));
                        ?>
                        <a href="<?= $cUrl ?>" target="_blank" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white hover:border-primary/40 hover:shadow-md transition-all group max-w-xs">
                            <?php if ($cIsPdf): ?>
                            <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-red-600 text-2xl">picture_as_pdf</span>
                            </div>
                            <?php else: ?>
                            <div class="w-12 h-12 rounded-lg overflow-hidden border border-slate-200 shrink-0">
                                <img src="<?= $cUrl ?>" alt="Comprobante" class="w-full h-full object-cover">
                            </div>
                            <?php endif; ?>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-on-surface group-hover:text-primary truncate"><?= htmlspecialchars($comp['archivo_nombre'] ?? 'Comprobante') ?></p>
                                <p class="text-[10px] text-outline uppercase"><?= $cIsPdf ? 'PDF' : strtoupper($cExt) ?></p>
                            </div>
                            <span class="material-symbols-outlined text-outline/30 text-sm group-hover:text-primary shrink-0">open_in_new</span>
                        </a>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Receipt Download (only for approved payments with a receipt) -->
                <?php if (($p['estado'] ?? '') === 'aprobado' && !empty($p['recibo_url'])): ?>
                <div class="px-5 pb-5 pt-2">
                    <p class="text-[10px] text-outline font-bold uppercase tracking-widest mb-3">Recibo de Pago</p>
                    <div class="flex items-center gap-3">
                        <a href="<?= Router::url('/descargar-recibo.php?id=' . $p['id'] . '&mode=inline') ?>" target="_blank"
                           class="inline-flex items-center gap-3 p-4 rounded-xl border-2 border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 hover:border-emerald-400 hover:shadow-lg hover:shadow-emerald-100 transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0 group-hover:bg-emerald-200 transition-colors">
                                <span class="material-symbols-outlined text-emerald-600 text-2xl">picture_as_pdf</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-emerald-800 group-hover:text-emerald-900">Ver Recibo de Pago</p>
                                <p class="text-[10px] text-emerald-600 font-medium">PDF · REC-<?= str_pad($p['id'], 6, '0', STR_PAD_LEFT) ?> · $<?= number_format((float)($p['monto'] ?? 0), 2) ?></p>
                            </div>
                            <span class="material-symbols-outlined text-emerald-400 group-hover:text-emerald-600 transition-colors shrink-0">open_in_new</span>
                        </a>
                        <a href="<?= Router::url('/descargar-recibo.php?id=' . $p['id'] . '&mode=download') ?>"
                           class="inline-flex items-center gap-2 px-4 py-3 rounded-xl border-2 border-blue-200 bg-blue-50 hover:border-blue-400 hover:shadow-lg transition-all text-blue-700 hover:text-blue-900 font-bold text-sm">
                            <span class="material-symbols-outlined text-lg">download</span>
                            Descargar
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-[2rem] p-12 text-center border border-slate-100">
        <span class="material-symbols-outlined text-6xl text-outline/20 mb-4 block">receipt_long</span>
        <h3 class="text-xl font-black text-secondary mb-2">Sin pagos registrados</h3>
        <p class="text-outline">Aún no tienes pagos. Registra tu primer pago.</p>
    </div>
    <?php endif; ?>
</section>

<!-- ==================== TAB: DOCUMENTS (Contratos + Vouchers) ==================== -->
<section class="pay-panel hidden" id="panel-documents">
    <div class="flex items-end justify-between px-2 mb-6">
        <div>
            <h2 class="text-3xl font-black text-secondary tracking-tighter">Mis Documentos</h2>
            <p class="text-sm text-outline mt-1">Contratos adjuntos, vouchers y boletas de servicio</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Contract Documents -->
        <div>
            <h3 class="text-sm font-black text-outline uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-lg">description</span>
                Contratos
            </h3>
            <?php
            $anyDocs = false;
            foreach ($contratos as $c):
                $cDocs = $archivos[$c['id']] ?? [];
                $contractDocs = array_filter($cDocs, fn($d) => $d['tipo'] === 'contrato');
                if (empty($contractDocs)) continue;
                $anyDocs = true;
            ?>
            <div class="mb-4">
                <p class="text-xs text-outline mb-2 font-mono"><?= htmlspecialchars($c['codigo'] ?? '') ?> — <?= htmlspecialchars($c['destino'] ?? '') ?></p>
                <div class="space-y-2">
                    <?php foreach ($contractDocs as $doc):
                        $ext = strtolower(pathinfo($doc['nombre_hash'], PATHINFO_EXTENSION));
                        $isPdf = $ext === 'pdf';
                        $docUrl = Router::url('/storage/' . htmlspecialchars($doc['ruta']));
                    ?>
                    <a href="<?= $docUrl ?>" target="_blank" class="flex items-center gap-4 p-4 bg-white rounded-xl border border-slate-100 hover:border-primary/40 hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 rounded-xl <?= $isPdf ? 'bg-red-50' : 'bg-blue-50' ?> flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-3xl <?= $isPdf ? 'text-red-500' : 'text-blue-500' ?>"><?= $isPdf ? 'picture_as_pdf' : 'image' ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-on-surface group-hover:text-primary truncate"><?= htmlspecialchars($doc['nombre_original'] ?? 'Contrato') ?></p>
                            <div class="flex items-center gap-2 text-[10px] text-outline mt-0.5">
                                <span class="uppercase font-bold"><?= strtoupper($ext) ?></span>
                                <span class="w-1 h-1 bg-outline/30 rounded-full"></span>
                                <span><?= number_format(($doc['tamano'] ?? 0) / 1024, 0) ?> KB</span>
                                <span class="w-1 h-1 bg-outline/30 rounded-full"></span>
                                <span><?= !empty($doc['created_at']) ? date('d M Y', strtotime($doc['created_at'])) : '' ?></span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-outline/30 group-hover:text-primary transition-colors">download</span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (!$anyDocs): ?>
            <div class="bg-white rounded-xl p-8 text-center border border-slate-100">
                <span class="material-symbols-outlined text-4xl text-outline/20 mb-2 block">description</span>
                <p class="text-sm text-outline">No hay contratos adjuntos aún.</p>
                <p class="text-[10px] text-outline/60 mt-1">Tu asesor subirá el contrato firmado.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Vouchers & Service Documents -->
        <div>
            <h3 class="text-sm font-black text-outline uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-lg">confirmation_number</span>
                Vouchers de Servicio
            </h3>
            <?php
            $voucherIcons = [
                'hotel' => ['icon' => 'hotel', 'color' => 'bg-indigo-50 text-indigo-600'],
                'excursion' => ['icon' => 'hiking', 'color' => 'bg-emerald-50 text-emerald-600'],
                'seguro' => ['icon' => 'verified_user', 'color' => 'bg-cyan-50 text-cyan-700'],
                'vuelos' => ['icon' => 'flight', 'color' => 'bg-blue-50 text-blue-600'],
                'general' => ['icon' => 'receipt', 'color' => 'bg-slate-50 text-slate-600'],
            ];
            $anyVch = false;
            foreach ($contratos as $c):
                $cVouchers = $vouchers[$c['id']] ?? [];
                if (empty($cVouchers)) continue;
                $anyVch = true;
            ?>
            <div class="mb-4">
                <p class="text-xs text-outline mb-2 font-mono"><?= htmlspecialchars($c['codigo'] ?? '') ?></p>
                <div class="space-y-2">
                    <?php foreach ($cVouchers as $v):
                        $vType = $v['tipo_voucher'] ?? 'general';
                        $vStyle = $voucherIcons[$vType] ?? $voucherIcons['general'];
                        $vExt = strtolower(pathinfo($v['archivo_url'], PATHINFO_EXTENSION));
                        $vUrl = Router::url('/storage/vouchers/' . htmlspecialchars($v['archivo_url']));
                    ?>
                    <a href="<?= $vUrl ?>" target="_blank" class="flex items-center gap-4 p-4 bg-white rounded-xl border border-slate-100 hover:border-primary/40 hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 rounded-xl <?= $vStyle['color'] ?> flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-3xl"><?= $vStyle['icon'] ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-on-surface group-hover:text-primary truncate"><?= htmlspecialchars($v['titulo'] ?? 'Voucher') ?></p>
                            <div class="flex items-center gap-2 text-[10px] text-outline mt-0.5">
                                <span class="uppercase font-bold px-1.5 py-0.5 rounded bg-primary/5 text-primary"><?= ucfirst($vType) ?></span>
                                <span class="w-1 h-1 bg-outline/30 rounded-full"></span>
                                <span class="uppercase font-bold"><?= strtoupper($vExt) ?></span>
                                <span class="w-1 h-1 bg-outline/30 rounded-full"></span>
                                <span><?= !empty($v['fecha_subida']) ? date('d M Y', strtotime($v['fecha_subida'])) : '' ?></span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-outline/30 group-hover:text-primary transition-colors">download</span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (!$anyVch): ?>
            <div class="bg-white rounded-xl p-8 text-center border border-slate-100">
                <span class="material-symbols-outlined text-4xl text-outline/20 mb-2 block">confirmation_number</span>
                <p class="text-sm text-outline">No hay vouchers disponibles aún.</p>
                <p class="text-[10px] text-outline/60 mt-1">Los vouchers se subirán cuando tus servicios estén confirmados.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ==================== TAB: SCHEDULE (Cronograma de Cuotas) ==================== -->
<section class="pay-panel hidden" id="panel-schedule">
    <div class="flex items-end justify-between px-2 mb-6">
        <div>
            <h2 class="text-3xl font-black text-secondary tracking-tighter">Cronograma de Pagos</h2>
            <p class="text-sm text-outline mt-1">Plan de cuotas y fechas de vencimiento</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] overflow-hidden border border-slate-100">
        <div class="px-6 py-4 bg-secondary text-white flex justify-between items-center rounded-t-[2rem]">
            <h2 class="text-sm font-bold uppercase tracking-widest flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">event_available</span>
                Plan de Cuotas
            </h2>
            <span class="text-xs opacity-70"><?= count($cuotas) ?> cuota<?= count($cuotas) !== 1 ? 's' : '' ?></span>
        </div>
        <div class="p-6">
            <?php if (!empty($cuotas)): ?>
            <div class="space-y-4">
                <?php foreach ($cuotas as $c):
                    $esPagada = ($c['estado'] === 'pagada');
                    $esPendiente = ($c['estado'] === 'pendiente');
                    $esParcial = ($c['estado'] === 'parcial');
                ?>
                <div class="flex items-center gap-4 p-4 rounded-xl <?= $esPagada ? 'bg-surface-container-low border border-transparent' : 'bg-surface-container-lowest border border-outline-variant/30' ?> hover:border-primary-container transition-all">
                    <div class="w-12 h-12 rounded-full <?= $esPagada ? 'bg-primary text-white' : ($esPendiente ? 'bg-error-container text-error' : 'bg-tertiary-container text-on-tertiary-container') ?> flex items-center justify-center font-bold shrink-0">
                        <span class="material-symbols-outlined" <?= $esPagada ? 'style="font-variation-settings: \'FILL\' 1;"' : '' ?>><?= $esPagada ? 'check_circle' : ($esPendiente ? 'pending_actions' : 'hourglass_top') ?></span>
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
            <div class="py-12 text-center">
                <span class="material-symbols-outlined text-5xl text-outline/20 mb-2 block">event_available</span>
                <p class="text-sm text-outline">No hay cuotas programadas.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Penalty Policy -->
    <div class="mt-6 bg-tertiary-fixed rounded-[2rem] p-6 border border-tertiary-container/50">
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
</section>

<!-- ==================== MODAL: REGISTRAR PAGO ==================== -->
<div id="modalPago" class="fixed inset-0 z-[70] bg-secondary/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-surface-container-low flex-shrink-0">
            <h3 class="font-black text-secondary flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">upload_file</span> Registrar Pago
            </h3>
            <button onclick="document.getElementById('modalPago').classList.add('hidden')" class="text-outline hover:text-error transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= Router::url('/client/payments/register') ?>" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-5">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? $_SESSION['csrf_token'] ?? '') ?>">

                <?php if (count($contratos) > 1): ?>
                <div>
                    <label class="block text-xs font-bold text-secondary mb-1 uppercase tracking-widest">Contrato</label>
                    <select name="contrato_id" id="client-contrato" required class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 focus:border-primary outline-none bg-white font-mono text-sm">
                        <?php foreach ($contratos as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['codigo']) ?> — <?= htmlspecialchars($c['destino'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php elseif (count($contratos) === 1): ?>
                <input type="hidden" name="contrato_id" id="client-contrato" value="<?= $contratos[0]['id'] ?>">
                <?php endif; ?>

                <!-- Monto -->
                <div>
                    <label class="block text-xs font-bold text-secondary mb-1 uppercase tracking-widest">Monto Pagado (USD $)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-bold text-outline">$</span>
                        <input type="number" step="0.01" name="monto" id="client-monto" required min="1" placeholder="0.00" class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant/30 font-bold text-lg focus:border-primary outline-none">
                    </div>
                    <p class="text-[10px] text-outline mt-1">Todos los pagos se registran en dólares americanos (USD). Si pagó en soles, ingrese el equivalente en dólares.</p>
                </div>

                <!-- Cascade Preview -->
                <div id="client-cascade" class="hidden rounded-xl border border-primary/20 bg-primary/5 p-4">
                    <p class="text-xs font-bold text-primary mb-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">info</span> Tu pago cubrirá:
                    </p>
                    <div id="client-cascade-detail" class="space-y-1.5"></div>
                </div>

                <!-- Método de pago -->
                <div>
                    <label class="block text-xs font-bold text-secondary mb-1 uppercase tracking-widest">Método de Pago</label>
                    <select name="metodo_pago" id="client-metodo" required class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 focus:border-primary outline-none bg-white text-sm">
                        <option value="Efectivo">💵 Efectivo</option>
                        <option value="Transferencia bancaria">🔄 Transferencia Bancaria</option>
                        <option value="Depósito bancario">🏦 Depósito Bancario</option>
                        <option value="Yape">📱 Yape</option>
                        <option value="Plin">📱 Plin</option>
                    </select>
                </div>

                <!-- Banco (condicional) -->
                <div id="bancoField" class="hidden">
                    <label class="block text-xs font-bold text-secondary mb-1 uppercase tracking-widest">Banco</label>
                    <select name="banco" id="client-banco" class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 focus:border-primary outline-none bg-white text-sm">
                        <option value="">— Seleccionar banco —</option>
                        <option value="BCP">BCP (Banco de Crédito del Perú)</option>
                        <option value="BBVA Continental">BBVA Continental</option>
                        <option value="Interbank">Interbank</option>
                        <option value="Scotiabank">Scotiabank</option>
                    </select>
                </div>

                <!-- Moneda con la que pagó (informativo) -->
                <div>
                    <label class="block text-xs font-bold text-secondary mb-1 uppercase tracking-widest">¿Con qué moneda realizó el pago?</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/30 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                            <input type="radio" name="moneda_pago" value="USD" checked class="accent-primary">
                            <span class="text-sm font-bold text-on-surface">$ Dólares</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/30 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                            <input type="radio" name="moneda_pago" value="PEN" class="accent-primary">
                            <span class="text-sm font-bold text-on-surface">S/ Soles</span>
                        </label>
                    </div>
                    <p class="text-[10px] text-outline mt-1">Indique la moneda con la que realizó la transacción. El monto siempre se registra en USD.</p>
                </div>

                <!-- Concepto -->
                <div>
                    <label class="block text-xs font-bold text-secondary mb-1 uppercase tracking-widest">Concepto (Opcional)</label>
                    <input type="text" name="concepto" id="client-concepto" placeholder="Ej: Abono Cuota 2" class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 focus:border-primary outline-none text-sm">
                </div>

                <!-- Comprobante -->
                <div>
                    <label class="block text-xs font-bold text-secondary mb-1 uppercase tracking-widest">Comprobante de Pago</label>
                    <input type="file" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" required class="w-full px-4 py-3 rounded-xl border border-outline-variant/30 focus:border-primary outline-none text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    <p class="text-[10px] text-outline mt-1">Formatos: JPG, PNG, PDF — Máx: 5MB</p>
                </div>

                <button type="submit" class="w-full py-4 mt-2 rounded-xl bg-gradient-to-r from-primary to-primary-container text-white font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">cloud_upload</span>
                    Registrar Pago
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== JAVASCRIPT ==================== -->
<script>
// Tab Navigation
function showPayTab(tab) {
    document.querySelectorAll('.pay-panel').forEach(function(el) { el.classList.add('hidden'); });
    document.querySelectorAll('.pay-tab').forEach(function(el) {
        el.classList.remove('text-primary', 'bg-primary-fixed/30', 'border-b-2', 'border-primary');
        el.classList.add('text-slate-500');
    });
    var panel = document.getElementById('panel-' + tab);
    if (panel) panel.classList.remove('hidden');
    var btn = document.querySelector('.pay-tab[data-tab="' + tab + '"]');
    if (btn) {
        btn.classList.remove('text-slate-500');
        btn.classList.add('text-primary', 'bg-primary-fixed/30', 'border-b-2', 'border-primary');
    }
}

// Toggle payment detail
function togglePago(i) {
    var el = document.getElementById('pago-detail-' + i);
    var ch = document.getElementById('chevron-' + i);
    if (el) {
        el.classList.toggle('hidden');
        if (ch) ch.style.transform = el.classList.contains('hidden') ? '' : 'rotate(180deg)';
    }
}

// Show/hide bank field based on payment method
(function() {
    var metodoSel = document.getElementById('client-metodo');
    var bancoField = document.getElementById('bancoField');
    var bancoSel = document.getElementById('client-banco');

    function toggleBanco() {
        var val = metodoSel ? metodoSel.value : '';
        var needsBank = (val === 'Transferencia bancaria' || val === 'Depósito bancario');
        if (bancoField) bancoField.classList.toggle('hidden', !needsBank);
        if (bancoSel && !needsBank) bancoSel.value = '';
    }

    if (metodoSel) {
        metodoSel.addEventListener('change', toggleBanco);
        toggleBanco();
    }
})();

// Cascade preview
(function(){
    var cuotasData = <?= json_encode($cuotasPorContrato ?? [], JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    var montoInput = document.getElementById('client-monto');
    var contratoInput = document.getElementById('client-contrato');
    var cascadeEl = document.getElementById('client-cascade');
    var cascadeDetail = document.getElementById('client-cascade-detail');
    var conceptoInput = document.getElementById('client-concepto');

    function fmt(n) { return '$' + Number(n).toFixed(2); }

    function getContratoId() {
        if (!contratoInput) return null;
        return contratoInput.value || null;
    }

    function updatePreview() {
        var cid = getContratoId();
        var monto = parseFloat(montoInput ? montoInput.value : 0) || 0;
        if (!cid || monto <= 0 || !cuotasData[cid] || cuotasData[cid].length === 0) {
            if (cascadeEl) cascadeEl.classList.add('hidden');
            return;
        }

        var cuotas = cuotasData[cid];
        var remaining = monto;
        var lines = [];
        var cuotaPagadas = [];

        for (var i = 0; i < cuotas.length; i++) {
            if (remaining <= 0) break;
            var q = cuotas[i];
            var faltante = q.faltante;

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
        if (cascadeEl) cascadeEl.classList.remove('hidden');

        if (cuotaPagadas.length > 0 && conceptoInput && !conceptoInput._userEdited) {
            conceptoInput.value = 'Pago ' + cuotaPagadas.join(', ');
        }
    }

    if (montoInput) montoInput.addEventListener('input', updatePreview);
    if (contratoInput) contratoInput.addEventListener('change', updatePreview);
    if (conceptoInput) conceptoInput.addEventListener('input', function() {
        this._userEdited = this.value.length > 0;
    });
})();
</script>
