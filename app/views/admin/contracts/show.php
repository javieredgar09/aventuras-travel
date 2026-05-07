<!-- ADMIN – DETALLE CONTRATO – Elevated Explorer Design -->
<?php
$contrato = $contrato ?? [];
$csrf_token = $csrf_token ?? '';
$cuotas = $contrato['cuotas'] ?? [];
$resumen = $contrato['resumen_cuotas'] ?? ['total_cuotas' => 0, 'suma_esperada' => 0, 'suma_pagada' => 0, 'suma_pendiente' => 0];
$valorTotal = (float)($contrato['valor_total'] ?? 0);
$totalPagado = (float)($contrato['total_pagado_real'] ?? 0);
$saldoReal = (float)($contrato['saldo_real'] ?? 0);
$progreso = $valorTotal > 0 ? min(100, round(($totalPagado / $valorTotal) * 100)) : 0;

// Hero image
$defaultHero = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80';
$heroImg = $contrato['hero_image'] ?? $defaultHero;

// Cuotas pendientes para el modal de pago
$cuotasPendientes = [];
foreach ($cuotas as $q) {
    if ($q['estado'] !== 'pagada') {
        $cuotasPendientes[] = [
            'id'       => (int)$q['id'],
            'numero'   => (int)$q['numero_cuota'],
            'concepto' => $q['concepto'] ?? ('Cuota ' . $q['numero_cuota']),
            'esperado' => (float)$q['monto_esperado'],
            'pagado'   => (float)$q['monto_pagado'],
            'faltante' => round((float)$q['monto_esperado'] - (float)$q['monto_pagado'], 2),
            'fecha'    => $q['fecha_vencimiento'] ?? '',
            'estado'   => $q['estado'],
        ];
    }
}
?>

<!-- Breadcrumb -->
<div class="mb-6">
    <a href="<?= Router::url('/admin/contracts') ?>" class="text-turquesa font-semibold text-sm hover:underline flex items-center gap-1">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Contratos
    </a>
</div>

<!-- Hero Banner -->
<div class="relative h-48 md:h-56 rounded-2xl overflow-hidden mb-8 group shadow-lg">
    <img class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?= htmlspecialchars($heroImg) ?>" alt="<?= htmlspecialchars($contrato['destino'] ?? '') ?>">
    <div class="absolute inset-0 bg-gradient-to-r from-petroleo/90 via-petroleo/60 to-transparent"></div>
    <div class="absolute bottom-0 left-0 p-6 z-10 flex items-end justify-between w-full">
        <div>
            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?php
                $estado = $contrato['estado'] ?? 'activo';
                echo match($estado) {
                    'activo' => 'bg-emerald-500 text-white',
                    'completado' => 'bg-blue-500 text-white',
                    'cancelado' => 'bg-red-500 text-white',
                    default => 'bg-white/20 text-white'
                };
            ?>"><?= strtoupper($estado) ?></span>
            <h1 class="text-3xl md:text-4xl font-black text-white mt-2 tracking-tight"><?= htmlspecialchars($contrato['codigo'] ?? '') ?></h1>
            <p class="text-white/70 text-sm mt-1"><?= htmlspecialchars($contrato['destino'] ?? '') ?> · <?= htmlspecialchars($contrato['cliente_nombre'] ?? $contrato['titular_nombre'] ?? 'Sin titular') ?></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= Router::url('/admin/contracts/' . $contrato['id'] . '/print') ?>" target="_blank" class="bg-white/10 backdrop-blur-sm text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 hover:bg-white/20 transition border border-white/10">
                <span class="material-symbols-outlined text-base">print</span> Imprimir
            </a>
            <button onclick="document.getElementById('modal-pago-contrato').classList.remove('hidden')" class="bg-turquesa text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 hover:bg-turquesa-dark transition shadow-lg shadow-turquesa/30">
                <span class="material-symbols-outlined text-base">payments</span> Registrar Pago
            </button>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-petroleo/5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/40 mb-1">Valor Total</p>
        <p class="text-2xl font-black text-petroleo">$<?= number_format($valorTotal, 2) ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-petroleo/5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/40 mb-1">Total Pagado</p>
        <p class="text-2xl font-black text-emerald-600">$<?= number_format($totalPagado, 2) ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-petroleo/5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/40 mb-1">Saldo Pendiente</p>
        <p class="text-2xl font-black <?= $saldoReal > 0 ? 'text-amber-600' : 'text-emerald-600' ?>">$<?= number_format($saldoReal, 2) ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-petroleo/5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/40 mb-1">Progreso</p>
        <div class="flex items-center gap-3">
            <p class="text-2xl font-black text-petroleo"><?= $progreso ?>%</p>
            <div class="flex-1 h-2.5 bg-petroleo/5 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 <?= $progreso >= 100 ? 'bg-emerald-500' : ($progreso >= 50 ? 'bg-turquesa' : 'bg-amber-400') ?>" style="width: <?= $progreso ?>%"></div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- LEFT: Info + Pasajeros -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Info General -->
        <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-widest text-petroleo/40 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa text-base">person</span> Información del Cliente
            </h2>
            <div class="space-y-4">
                <?php
                $infoRows = [
                    ['person', 'Titular', $contrato['cliente_nombre'] ?? ($contrato['titular_nombre'] ?? '—')],
                    ['mail', 'Correo', $contrato['cliente_email'] ?? ($contrato['titular_correo'] ?? '—')],
                    ['phone', 'Teléfono', $contrato['cliente_telefono'] ?? ($contrato['titular_telefono'] ?? '—')],
                    ['flight_takeoff', 'Destino', $contrato['destino'] ?? '—'],
                    ['calendar_today', 'Fecha Salida', !empty($contrato['fecha_salida']) ? date('d M Y', strtotime($contrato['fecha_salida'])) : '—'],
                    ['calendar_month', 'Fecha Retorno', !empty($contrato['fecha_retorno']) ? date('d M Y', strtotime($contrato['fecha_retorno'])) : '—'],
                ];
                foreach ($infoRows as $r):
                ?>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-petroleo/20 text-base mt-0.5"><?= $r[0] ?></span>
                    <div class="flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/30"><?= $r[1] ?></p>
                        <p class="text-sm font-medium text-petroleo"><?= htmlspecialchars($r[2]) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pasajeros -->
        <?php if (!empty($contrato['pasajeros'])): ?>
        <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-widest text-petroleo/40 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa text-base">groups</span> Pasajeros (<?= count($contrato['pasajeros']) ?>)
            </h2>
            <div class="space-y-2">
                <?php foreach ($contrato['pasajeros'] as $p): ?>
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-superficie transition-all">
                    <div class="w-9 h-9 bg-gradient-to-br from-turquesa/20 to-turquesa/5 text-turquesa-dark rounded-full flex items-center justify-center font-bold text-xs">
                        <?= strtoupper(substr($p['nombre'] ?? '', 0, 1) . substr($p['apellido'] ?? '', 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-petroleo truncate"><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></p>
                        <?php $doc = $p['documento'] ?? ($p['pasaporte'] ?? ($p['dni'] ?? '—')); ?>
                        <p class="text-[10px] text-petroleo/40"><?= htmlspecialchars(ucfirst($p['tipo'] ?? 'adulto')) ?> · <?= htmlspecialchars($doc) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Acciones -->
        <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-widest text-petroleo/40 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa text-base">settings</span> Acciones
            </h2>
            <div class="space-y-2">
                <a href="<?= Router::url('/admin/contracts/' . $contrato['id'] . '/print') ?>" target="_blank" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-superficie transition-all text-sm font-medium text-petroleo">
                    <span class="material-symbols-outlined text-petroleo/40 text-base">print</span> Imprimir Contrato
                </a>
                <a href="<?= Router::url('/admin/payments') ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-superficie transition-all text-sm font-medium text-petroleo">
                    <span class="material-symbols-outlined text-petroleo/40 text-base">account_balance_wallet</span> Gestión de Pagos
                </a>
                <button onclick="document.getElementById('modal-upload-contrato').classList.remove('hidden')" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition-all text-sm font-medium text-blue-600">
                    <span class="material-symbols-outlined text-base">upload_file</span> Subir Contrato PDF
                </button>
                <button onclick="document.getElementById('modal-upload-voucher').classList.remove('hidden')" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-purple-50 transition-all text-sm font-medium text-purple-600">
                    <span class="material-symbols-outlined text-base">receipt</span> Subir Voucher de Servicio
                </button>
                <form action="<?= Router::url('/admin/contracts/delete/' . $contrato['id']) ?>" method="POST" data-confirm="¿Estás seguro de anular este contrato? Esta acción no se puede deshacer.">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 transition-all text-sm font-medium text-red-500">
                        <span class="material-symbols-outlined text-base">delete</span> Anular Contrato
                    </button>
                </form>
            </div>
        </div>

        <!-- Documentos Subidos (Contratos PDF) -->
        <?php $archivos = $archivos ?? []; ?>
        <?php if (!empty($archivos)): ?>
        <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-widest text-petroleo/40 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa text-base">description</span> Documentos (<?= count($archivos) ?>)
            </h2>
            <div class="space-y-2">
                <?php foreach ($archivos as $arch): ?>
                <a href="<?= Router::url('/storage/contratos/' . htmlspecialchars($arch['nombre_hash'])) ?>" target="_blank" class="flex items-center gap-3 p-3 rounded-xl hover:bg-superficie transition-all">
                    <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-base"><?= str_contains($arch['mime_type'] ?? '', 'pdf') ? 'picture_as_pdf' : 'image' ?></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-petroleo truncate"><?= htmlspecialchars($arch['nombre_original'] ?? $arch['nombre_hash']) ?></p>
                        <p class="text-[10px] text-petroleo/40"><?= htmlspecialchars(ucfirst($arch['tipo'] ?? 'documento')) ?> · <?= !empty($arch['created_at']) ? date('d M Y', strtotime($arch['created_at'])) : '' ?></p>
                    </div>
                    <span class="material-symbols-outlined text-petroleo/20 text-base">open_in_new</span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT: Cuotas + Pagos -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Plan de Cuotas -->
        <div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-petroleo flex justify-between items-center">
                <h2 class="text-sm font-bold uppercase tracking-widest text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-turquesa text-base">event_available</span> Plan de Cuotas
                </h2>
                <span class="text-white/50 text-xs"><?= count($cuotas) ?> cuotas</span>
            </div>
            <?php if (!empty($cuotas)): ?>
            <div class="p-5 space-y-2">
                <?php foreach ($cuotas as $cu):
                    $esperado = (float)($cu['monto_esperado'] ?? 0);
                    $pagado = (float)($cu['monto_pagado'] ?? 0);
                    $faltante = max(0, $esperado - $pagado);
                    $cuotaProg = $esperado > 0 ? min(100, round(($pagado / $esperado) * 100)) : 0;
                    $cuotaEstado = $cu['estado'] ?? 'pendiente';
                    $vencida = $cuotaEstado !== 'pagada' && !empty($cu['fecha_vencimiento']) && $cu['fecha_vencimiento'] !== '0000-00-00' && strtotime($cu['fecha_vencimiento']) < time();
                ?>
                <div class="flex items-center gap-4 p-4 rounded-xl transition-all <?= $cuotaEstado === 'pagada' ? 'bg-emerald-50' : ($cuotaEstado === 'parcial' ? 'bg-amber-50' : ($vencida ? 'bg-red-50' : 'bg-petroleo/[0.02]')) ?>">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black <?= $cuotaEstado === 'pagada' ? 'bg-emerald-100 text-emerald-600' : ($cuotaEstado === 'parcial' ? 'bg-amber-100 text-amber-600' : ($vencida ? 'bg-red-100 text-red-600' : 'bg-petroleo/5 text-petroleo/40')) ?>">
                        <?php if ($cuotaEstado === 'pagada'): ?>
                            <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <?php else: ?>
                            <?= $cu['numero_cuota'] ?? '?' ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-petroleo"><?= htmlspecialchars($cu['concepto'] ?? ('Cuota ' . ($cu['numero_cuota'] ?? ''))) ?></p>
                        <p class="text-xs text-petroleo/40 mt-0.5">
                            <?php if (!empty($cu['fecha_vencimiento']) && $cu['fecha_vencimiento'] !== '0000-00-00'): ?>
                                Vence: <?= date('d M Y', strtotime($cu['fecha_vencimiento'])) ?>
                                <?php if ($vencida): ?><span class="text-red-500 font-bold ml-1">· Vencida</span><?php endif; ?>
                            <?php else: ?>
                                Sin fecha
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="w-24 hidden sm:block">
                        <div class="h-2 bg-petroleo/5 rounded-full overflow-hidden">
                            <div class="h-full rounded-full <?= $cuotaEstado === 'pagada' ? 'bg-emerald-500' : ($cuotaEstado === 'parcial' ? 'bg-amber-400' : 'bg-petroleo/10') ?>" style="width: <?= $cuotaProg ?>%"></div>
                        </div>
                        <p class="text-[9px] text-petroleo/30 text-right mt-0.5"><?= $cuotaProg ?>%</p>
                    </div>
                    <div class="text-right w-28">
                        <p class="text-sm font-black text-petroleo">$<?= number_format($pagado, 2) ?></p>
                        <p class="text-[10px] text-petroleo/40">de $<?= number_format($esperado, 2) ?></p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider <?php
                        echo match($cuotaEstado) {
                            'pagada'  => 'bg-emerald-100 text-emerald-700',
                            'parcial' => 'bg-amber-100 text-amber-700',
                            default   => $vencida ? 'bg-red-100 text-red-700' : 'bg-petroleo/5 text-petroleo/40',
                        };
                    ?>">
                        <?= $vencida && $cuotaEstado === 'pendiente' ? 'vencida' : $cuotaEstado ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Cuotas Summary Footer -->
            <div class="px-6 py-4 bg-superficie/50 border-t border-petroleo/5 flex flex-wrap items-center justify-between gap-4">
                <div class="flex gap-6">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/30">Esperado</p>
                        <p class="text-sm font-black text-petroleo">$<?= number_format($resumen['suma_esperada'], 2) ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600/60">Pagado</p>
                        <p class="text-sm font-black text-emerald-600">$<?= number_format($resumen['suma_pagada'], 2) ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-amber-600/60">Pendiente</p>
                        <p class="text-sm font-black text-amber-600">$<?= number_format($resumen['suma_pendiente'], 2) ?></p>
                    </div>
                </div>
                <button onclick="document.getElementById('modal-pago-contrato').classList.remove('hidden')" class="px-4 py-2 bg-turquesa text-white text-xs font-bold rounded-xl hover:bg-turquesa-dark transition-colors flex items-center gap-1.5 shadow-lg shadow-turquesa/20">
                    <span class="material-symbols-outlined text-sm">add_circle</span> Registrar Pago
                </button>
            </div>
            <?php else: ?>
            <div class="p-8 text-center text-petroleo/30">
                <span class="material-symbols-outlined text-4xl mb-2">event_busy</span>
                <p class="text-sm">No hay cuotas programadas para este contrato.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Historial de Transacciones -->
        <div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="px-6 py-4 flex justify-between items-center border-b border-petroleo/5">
                <h2 class="text-sm font-bold uppercase tracking-widest text-petroleo/40 flex items-center gap-2">
                    <span class="material-symbols-outlined text-turquesa text-base">receipt_long</span> Transacciones
                </h2>
                <span class="text-petroleo/30 text-xs"><?= count($contrato['pagos'] ?? []) ?> registros</span>
            </div>
            <?php if (!empty($contrato['pagos'])): ?>
            <div class="divide-y divide-petroleo/5">
                <?php foreach ($contrato['pagos'] as $p): ?>
                <div class="p-5 flex items-center justify-between gap-4 hover:bg-humo/30 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center <?php
                            echo match($p['estado'] ?? 'pendiente') {
                                'aprobado'  => 'bg-emerald-100 text-emerald-600',
                                'rechazado' => 'bg-red-100 text-red-600',
                                default     => 'bg-amber-100 text-amber-600',
                            };
                        ?>">
                            <span class="material-symbols-outlined text-lg"><?php
                                echo match($p['estado'] ?? 'pendiente') {
                                    'aprobado'  => 'check_circle',
                                    'rechazado' => 'cancel',
                                    default     => 'schedule',
                                };
                            ?></span>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-petroleo"><?= htmlspecialchars($p['concepto'] ?? 'Pago') ?></p>
                            <p class="text-xs text-petroleo/40"><?= date('d M Y', strtotime($p['created_at'] ?? $p['fecha_vencimiento'] ?? 'now')) ?>
                                <?php if (!empty($p['metodo_pago'])): ?> · <?= htmlspecialchars($p['metodo_pago']) ?><?php endif; ?>
                                <?php if (!empty($p['comprobante_url'])): ?>
                                    · <a href="<?= Router::url('/storage/comprobantes/' . $p['comprobante_url']) ?>" target="_blank" class="text-turquesa hover:underline">Ver comprobante</a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="font-black text-petroleo">$<?= number_format($p['monto'] ?? 0, 2) ?></p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase <?php
                                echo match($p['estado'] ?? 'pendiente') {
                                    'aprobado'  => 'bg-emerald-100 text-emerald-700',
                                    'rechazado' => 'bg-red-100 text-red-700',
                                    default     => 'bg-amber-100 text-amber-700',
                                };
                            ?>"><?= ucfirst($p['estado'] ?? 'pendiente') ?></span>
                        </div>
                        <?php if (($p['estado'] ?? '') === 'pendiente'): ?>
                        <div class="flex gap-1.5">
                            <form action="<?= Router::url('/admin/payments/approve/' . $p['id']) ?>" method="POST" data-confirm="¿Aprobar pago de $<?= number_format($p['monto'], 2) ?>? Las cuotas se actualizarán.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center hover:bg-emerald-600 transition-colors" title="Aprobar">
                                    <span class="material-symbols-outlined text-base">check</span>
                                </button>
                            </form>
                            <form action="<?= Router::url('/admin/payments/reject/' . $p['id']) ?>" method="POST" data-confirm="¿Rechazar este pago?">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition-colors" title="Rechazar">
                                    <span class="material-symbols-outlined text-base">close</span>
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="p-8 text-center text-petroleo/30">
                <span class="material-symbols-outlined text-4xl mb-2">receipt_long</span>
                <p class="text-sm">No hay transacciones registradas.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Vuelos -->
        <?php if (!empty($contrato['vuelos'])): ?>
        <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-widest text-petroleo/40 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa text-base">flight</span> Vuelos
            </h2>
            <div class="space-y-3">
                <?php foreach ($contrato['vuelos'] as $v): ?>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-petroleo/[0.02]">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">flight_takeoff</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-petroleo"><?= htmlspecialchars(($v['origen'] ?? '') . ' → ' . ($v['destino_vuelo'] ?? $v['destino'] ?? '')) ?></p>
                        <p class="text-xs text-petroleo/40"><?= htmlspecialchars($v['aerolinea'] ?? '') ?> · <?= !empty($v['fecha_salida']) ? date('d M Y H:i', strtotime($v['fecha_salida'])) : '' ?></p>
                    </div>
                    <?php if (!empty($v['numero_vuelo'])): ?>
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-mono font-bold"><?= htmlspecialchars($v['numero_vuelo']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Servicios -->
        <?php if (!empty($contrato['servicios'])): ?>
        <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-widest text-petroleo/40 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa text-base">room_service</span> Servicios
            </h2>
            <div class="space-y-2">
                <?php foreach ($contrato['servicios'] as $s): ?>
                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-superficie transition-all">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-petroleo/30 text-base">check_box</span>
                        <span class="text-sm text-petroleo"><?= htmlspecialchars($s['nombre'] ?? $s['descripcion'] ?? '') ?></span>
                    </div>
                    <?php if (!empty($s['precio'])): ?>
                    <span class="text-sm font-bold text-petroleo">$<?= number_format($s['precio'], 2) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Vouchers de Servicio -->
        <?php $vouchers = $vouchers ?? []; ?>
        <?php if (!empty($vouchers)): ?>
        <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-widest text-petroleo/40 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa text-base">receipt</span> Vouchers de Servicio (<?= count($vouchers) ?>)
            </h2>
            <div class="space-y-2">
                <?php foreach ($vouchers as $vc): ?>
                <a href="<?= Router::url('/storage/vouchers/' . htmlspecialchars($vc['archivo_url'])) ?>" target="_blank" class="flex items-center gap-4 p-4 rounded-xl bg-petroleo/[0.02] hover:bg-superficie transition-all">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-petroleo truncate"><?= htmlspecialchars($vc['titulo'] ?? 'Voucher') ?></p>
                        <p class="text-xs text-petroleo/40"><?= htmlspecialchars(ucfirst($vc['tipo_voucher'] ?? 'general')) ?> · <?= !empty($vc['fecha_subida']) ? date('d M Y', strtotime($vc['fecha_subida'])) : '' ?></p>
                    </div>
                    <span class="material-symbols-outlined text-petroleo/20 text-base">open_in_new</span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: Subir Contrato PDF -->
<div id="modal-upload-contrato" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-petroleo p-5 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Subir Contrato PDF</h3>
                    <p class="text-xs text-white/50"><?= htmlspecialchars($contrato['codigo'] ?? '') ?></p>
                </div>
                <button onclick="document.getElementById('modal-upload-contrato').classList.add('hidden')" class="text-white/50 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        <form action="<?= Router::url('/admin/contracts/' . $contrato['id'] . '/upload-contract') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Archivo del Contrato</label>
                <input type="file" name="contrato_pdf" accept=".pdf,.jpg,.jpeg,.png" required class="w-full bg-superficie border-none rounded-xl px-4 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white file:px-3 file:py-1 file:text-xs file:font-bold">
                <p class="text-[10px] text-petroleo/40 mt-1">PDF, JPG o PNG · Máx. 10 MB</p>
            </div>
            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors text-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">upload_file</span> Subir Contrato
            </button>
        </form>
    </div>
</div>

<!-- MODAL: Subir Voucher de Servicio -->
<div id="modal-upload-voucher" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-petroleo p-5 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Subir Voucher de Servicio</h3>
                    <p class="text-xs text-white/50"><?= htmlspecialchars($contrato['codigo'] ?? '') ?></p>
                </div>
                <button onclick="document.getElementById('modal-upload-voucher').classList.add('hidden')" class="text-white/50 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        <form action="<?= Router::url('/admin/contracts/' . $contrato['id'] . '/upload-voucher') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Tipo de Voucher</label>
                <select name="tipo_voucher" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300">
                    <option value="hotel">Hotel</option>
                    <option value="vuelo">Vuelo</option>
                    <option value="tour">Tour / Excursión</option>
                    <option value="seguro">Seguro de Viaje</option>
                    <option value="transporte">Transporte</option>
                    <option value="general">General</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Título / Descripción</label>
                <input type="text" name="titulo" placeholder="Ej: Voucher Hotel Marriott Lima" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Archivo</label>
                <input type="file" name="voucher_file" accept=".pdf,.jpg,.jpeg,.png" required class="w-full bg-superficie border-none rounded-xl px-4 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white file:px-3 file:py-1 file:text-xs file:font-bold">
                <p class="text-[10px] text-petroleo/40 mt-1">PDF, JPG o PNG · Máx. 10 MB</p>
            </div>
            <button type="submit" class="w-full py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-colors text-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">receipt</span> Subir Voucher
            </button>
        </form>
    </div>
</div>

<!-- MODAL: Registrar Pago para este contrato -->
<div id="modal-pago-contrato" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="bg-petroleo p-5 text-white flex-shrink-0">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Registrar Pago</h3>
                    <p class="text-xs text-white/50"><?= htmlspecialchars($contrato['codigo'] ?? '') ?> · <?= htmlspecialchars($contrato['cliente_nombre'] ?? '') ?></p>
                </div>
                <button onclick="document.getElementById('modal-pago-contrato').classList.add('hidden')" class="text-white/50 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        <form action="<?= Router::url('/admin/payments/register') ?>" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="contrato_id" value="<?= $contrato['id'] ?>">

                <!-- Cuotas selector -->
                <?php if (!empty($cuotasPendientes)): ?>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-2">Seleccionar Cuotas a Pagar</label>
                    <div id="ct-cuotas-list" class="space-y-1.5 max-h-48 overflow-y-auto rounded-xl border border-petroleo/10 p-3 bg-superficie/50">
                        <?php foreach ($cuotasPendientes as $q): ?>
                        <div class="flex items-center gap-3 p-2.5 rounded-lg cursor-pointer transition-colors <?= $q['estado'] === 'parcial' ? 'bg-amber-50 hover:bg-amber-100/70' : 'bg-white hover:bg-turquesa/5' ?>" onclick="toggleCuota(this, event)">
                            <input type="checkbox" class="ct-cuota-check w-4 h-4 rounded border-petroleo/20 text-turquesa focus:ring-turquesa/30"
                                data-faltante="<?= $q['faltante'] ?>" data-numero="<?= $q['numero'] ?>"
                                name="cuota_ids[]" value="<?= $q['id'] ?>"
                                <?= $q['estado'] === 'parcial' ? 'checked' : '' ?>>
                            <div class="w-7 h-7 rounded flex items-center justify-center text-[10px] font-black <?= $q['estado'] === 'parcial' ? 'bg-amber-100 text-amber-600' : 'bg-petroleo/5 text-petroleo/40' ?>"><?= $q['numero'] ?></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-petroleo truncate"><?= htmlspecialchars($q['concepto']) ?></p>
                                <p class="text-[10px] text-petroleo/40"><?= !empty($q['fecha']) ? date('d M Y', strtotime($q['fecha'])) : '—' ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-petroleo">$<?= number_format($q['faltante'], 2) ?></p>
                                <?php if ($q['pagado'] > 0): ?><p class="text-[10px] text-amber-600">Abonado: $<?= number_format($q['pagado'], 2) ?></p><?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex justify-between items-center mt-2 px-1">
                        <button type="button" onclick="selectAllCuotas()" class="text-xs text-turquesa font-bold hover:underline">Seleccionar todas</button>
                        <p class="text-xs text-petroleo/50">Total seleccionado: <span id="ct-cuotas-total" class="font-bold text-petroleo">$0.00</span></p>
                    </div>
                </div>
                <?php else: ?>
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center">
                    <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                    <p class="text-xs text-emerald-700 font-medium mt-1">Todas las cuotas están pagadas.</p>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Monto ($)</label>
                        <input type="number" name="monto" id="ct-monto" step="0.01" min="0.01" required class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 font-bold" placeholder="0.00">
                        <p class="text-[10px] text-petroleo/40 mt-1">Si supera la cuota, el excedente se aplica a la siguiente.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Concepto</label>
                        <input type="text" name="concepto" id="ct-concepto" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30" placeholder="Ej: Pago cuota marzo">
                    </div>
                </div>

                <!-- Cascade preview -->
                <div id="ct-cascade" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3">
                    <p class="text-xs font-bold text-blue-700 mb-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">info</span> Distribución del pago
                    </p>
                    <div id="ct-cascade-detail" class="text-xs text-blue-600 space-y-0.5"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Comprobante (opcional)</label>
                    <input type="file" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" class="w-full bg-superficie border-none rounded-xl px-4 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-turquesa file:text-white file:px-3 file:py-1 file:text-xs file:font-bold">
                    <p class="text-[10px] text-petroleo/40 mt-1">PDF, JPG o PNG · Máx. 5 MB</p>
                </div>

                <button type="submit" class="w-full py-3 bg-turquesa text-white font-bold rounded-xl hover:bg-turquesa-dark transition-colors text-sm">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">payments</span>
                    Registrar y Aprobar Pago
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const cuotasData = <?= json_encode($cuotasPendientes, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

function fmt(n) { return '$' + Number(n).toFixed(2); }

function toggleCuota(row, ev) {
    const cb = row.querySelector('.ct-cuota-check');
    if (ev.target.tagName !== 'INPUT') cb.checked = !cb.checked;
    recalcContrato();
}

function selectAllCuotas() {
    const checks = document.querySelectorAll('.ct-cuota-check');
    const allChecked = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !allChecked);
    recalcContrato();
}

function recalcContrato() {
    const checks = document.querySelectorAll('.ct-cuota-check:checked');
    let total = 0;
    const conceptos = [];
    checks.forEach(cb => {
        total += parseFloat(cb.dataset.faltante);
        conceptos.push('Cuota ' + cb.dataset.numero);
    });
    document.getElementById('ct-cuotas-total').textContent = fmt(total);
    document.getElementById('ct-monto').value = total > 0 ? total.toFixed(2) : '';
    document.getElementById('ct-concepto').value = conceptos.length > 0 ? 'Pago ' + conceptos.join(', ') : '';
    updateCascadeContrato();
}

function updateCascadeContrato() {
    const monto = parseFloat(document.getElementById('ct-monto').value) || 0;
    const cascade = document.getElementById('ct-cascade');
    const detail = document.getElementById('ct-cascade-detail');
    if (monto <= 0 || cuotasData.length === 0) { cascade.classList.add('hidden'); return; }

    const selectedIds = new Set();
    document.querySelectorAll('.ct-cuota-check:checked').forEach(cb => selectedIds.add(parseInt(cb.value)));

    const ordered = [];
    cuotasData.forEach(q => { if (selectedIds.has(q.id)) ordered.push(q); });
    cuotasData.forEach(q => { if (!selectedIds.has(q.id)) ordered.push(q); });

    let remaining = monto;
    const lines = [];
    ordered.forEach(q => {
        if (remaining <= 0) return;
        const sel = selectedIds.has(q.id);
        if (remaining >= q.faltante) {
            lines.push('<div class="flex justify-between"><span>Cuota ' + q.numero + (sel ? '' : ' (cascada)') + '</span><span class="font-bold">' + fmt(q.faltante) + ' ✓</span></div>');
            remaining -= q.faltante;
        } else {
            lines.push('<div class="flex justify-between"><span>Cuota ' + q.numero + (sel ? '' : ' (cascada)') + '</span><span class="font-bold">' + fmt(remaining) + ' parcial</span></div>');
            remaining = 0;
        }
    });
    if (remaining > 0) {
        lines.push('<div class="flex justify-between text-amber-600"><span>Excedente</span><span class="font-bold">' + fmt(remaining) + '</span></div>');
    }
    detail.innerHTML = lines.join('');
    cascade.classList.remove('hidden');
}

document.getElementById('ct-monto')?.addEventListener('input', updateCascadeContrato);

document.querySelectorAll('[data-confirm]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
});

document.getElementById('modal-pago-contrato')?.addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

document.getElementById('modal-upload-contrato')?.addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

document.getElementById('modal-upload-voucher')?.addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

document.addEventListener('DOMContentLoaded', function() {
    const first = document.querySelector('.ct-cuota-check:not(:checked)');
    const anyChecked = document.querySelector('.ct-cuota-check:checked');
    if (first && !anyChecked) first.checked = true;
    if (typeof recalcContrato === 'function') recalcContrato();
});
</script>
