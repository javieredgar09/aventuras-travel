<!-- ADMIN PAGOS – Vista Organizada por Grupo -->
<?php
$awaiting = $awaiting ?? [];
$transactions = $transactions ?? [];
$monthlyVolume = $monthlyVolume ?? 0;
$totalApproved = $totalApproved ?? 0;
$gruposFamiliares = $gruposFamiliares ?? [];
$gruposEscolares = $gruposEscolares ?? [];
$pendFamiliar = $pendFamiliar ?? 0;
$pendEscolar = $pendEscolar ?? 0;
$activeTab = $_GET['tab'] ?? 'familiar';

// Collect all contracts and their pending cuotas for the admin register form
$allContracts = [];
$cuotasMap = [];
foreach (array_merge($gruposFamiliares, $gruposEscolares) as $g) {
    foreach ($g['contratos'] ?? [] as $c) {
        $allContracts[] = [
            'id' => $c['id'],
            'codigo' => $c['codigo'] ?? '',
            'grupo' => $g['nombre'] ?? '',
            'tipo' => $g['tipo'] ?? '',
            'titular' => $c['titular_nombre'] ?? '',
        ];
        $pendientes = [];
        foreach ($c['cuotas'] ?? [] as $q) {
            if ($q['estado'] !== 'pagada') {
                $pendientes[] = [
                    'id' => (int)$q['id'],
                    'numero' => (int)$q['numero_cuota'],
                    'concepto' => $q['concepto'] ?? ('Cuota ' . $q['numero_cuota']),
                    'esperado' => (float)$q['monto_esperado'],
                    'pagado' => (float)$q['monto_pagado'],
                    'faltante' => round((float)$q['monto_esperado'] - (float)$q['monto_pagado'], 2),
                    'fecha' => $q['fecha_vencimiento'] ?? '',
                    'estado' => $q['estado'],
                ];
            }
        }
        $cuotasMap[$c['id']] = $pendientes;
    }
}

// Group awaiting by contrato_id for badge counts
$awaitingByContrato = [];
foreach ($awaiting as $a) {
    $cid = $a['contrato_id'] ?? 0;
    if (!isset($awaitingByContrato[$cid])) $awaitingByContrato[$cid] = [];
    $awaitingByContrato[$cid][] = $a;
}
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start gap-3 sm:gap-4 mb-6 sm:mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-petroleo">Gestión de Pagos</h1>
        <p class="text-petroleo/60 text-sm mt-1">Organiza, valida y registra pagos por grupo.</p>
    </div>
    <button onclick="document.getElementById('modal-registrar').classList.remove('hidden')" class="flex items-center gap-2 px-5 py-2.5 bg-turquesa text-white font-bold text-sm rounded-xl hover:bg-turquesa-dark transition-colors shadow-lg shadow-turquesa/20">
        <span class="material-symbols-outlined text-lg">add_circle</span>
        Registrar Pago
    </button>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg shadow-indigo-500/20 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full"></div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-100 mb-1">Volumen del Mes</p>
        <p class="text-3xl font-black">$ <?= number_format($monthlyVolume, 2) ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-petroleo/5 shadow-sm">
        <p class="text-[10px] uppercase tracking-widest font-bold text-petroleo/50 mb-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-emerald-500 text-sm">account_balance_wallet</span> Total Recaudado
        </p>
        <p class="text-3xl font-black text-petroleo">$ <?= number_format($totalApproved, 2) ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-petroleo/5 shadow-sm">
        <p class="text-[10px] uppercase tracking-widest font-bold text-petroleo/50 mb-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-amber-500 text-sm">pending_actions</span> Por Validar
        </p>
        <p class="text-3xl font-black text-petroleo"><?= count($awaiting) ?></p>
        <p class="text-xs text-petroleo/40 mt-1">comprobantes pendientes</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-petroleo/5 shadow-sm">
        <p class="text-[10px] uppercase tracking-widest font-bold text-petroleo/50 mb-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-turquesa text-sm">groups</span> Grupos Activos
        </p>
        <p class="text-3xl font-black text-petroleo"><?= count($gruposFamiliares) + count($gruposEscolares) ?></p>
        <p class="text-xs text-petroleo/40 mt-1"><?= count($gruposFamiliares) ?> familiar · <?= count($gruposEscolares) ?> escolar</p>
    </div>
</div>

<!-- Awaiting Review Banner -->
<?php if (!empty($awaiting)): ?>
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8 shadow-sm">
    <h2 class="text-lg font-black text-amber-800 mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined">notification_important</span>
        <?= count($awaiting) ?> Comprobante<?= count($awaiting) > 1 ? 's' : '' ?> por Validar
    </h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <?php foreach ($awaiting as $p): ?>
        <?php
            $tipoLabel  = match($p['grupo_tipo'] ?? '') { 'familiar' => 'Familiar', 'escolar' => 'Escolar', 'colegio' => 'Escolar', default => 'Contrato' };
            $tipoCss    = in_array($p['grupo_tipo'] ?? '', ['familiar']) ? 'blue-100 text-blue-700' : 'purple-100 text-purple-700';
            $metodo     = $p['metodo_pago'] ?? '';
            $banco      = $p['banco'] ?? '';
            $moneda     = $p['moneda_pago'] ?? 'USD';
            $clienteEmail = $p['cliente_email'] ?? $p['titular_correo'] ?? '';
        ?>
        <div class="bg-white rounded-xl p-4 border border-amber-100 shadow-sm">
            <!-- Header: tipo + nombre + monto -->
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1 min-w-0 mr-4">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded bg-<?= $tipoCss ?>">
                            <?= htmlspecialchars($tipoLabel) ?>
                        </span>
                        <?php if ($metodo): ?>
                        <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded bg-petroleo/5 text-petroleo/60">
                            <?= htmlspecialchars($metodo) ?><?= $banco ? ' · ' . htmlspecialchars($banco) : '' ?>
                        </span>
                        <?php endif; ?>
                        <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded bg-petroleo/5 text-petroleo/60">
                            <?= htmlspecialchars($moneda) ?>
                        </span>
                    </div>
                    <p class="font-bold text-petroleo text-sm truncate">
                        <?= htmlspecialchars($p['cliente_nombre'] ?? 'Cliente') ?>
                    </p>
                    <?php if ($clienteEmail): ?>
                    <p class="text-[11px] text-petroleo/40"><?= htmlspecialchars($clienteEmail) ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-petroleo/50 mt-0.5">
                        <?= htmlspecialchars($p['grupo_nombre'] ?? '—') ?> &nbsp;·&nbsp;
                        <strong><?= htmlspecialchars($p['contrato_codigo'] ?? '—') ?></strong>
                    </p>
                    <p class="text-xs text-petroleo/40 mt-0.5 truncate"><?= htmlspecialchars($p['concepto']) ?></p>
                    <p class="text-[10px] text-petroleo/30 mt-0.5"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-2xl font-black text-petroleo">$ <?= number_format($p['monto'], 2) ?></p>
                    <?php if (!empty($p['excedente']) && (float)$p['excedente'] > 0): ?>
                    <p class="text-[10px] text-amber-600">Excedente: $<?= number_format($p['excedente'], 2) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-3 border-t border-petroleo/5 space-y-2">
                <?php if (!empty($p['comprobante_url'])): ?>
                <a href="<?= Router::url('/storage/comprobantes/' . $p['comprobante_url']) ?>" target="_blank"
                   class="flex items-center justify-center gap-1.5 w-full py-2 text-center text-xs font-bold text-petroleo border border-petroleo/10 rounded-lg hover:bg-superficie transition-colors">
                    <span class="material-symbols-outlined text-sm">attach_file</span>
                    Ver Comprobante
                </a>
                <?php else: ?>
                <p class="text-[10px] text-center text-petroleo/30 py-1">Sin comprobante adjunto</p>
                <?php endif; ?>

                <div class="flex gap-2">
                    <form action="<?= Router::url('/admin/payments/approve/' . $p['id']) ?>" method="POST" class="flex-1"
                          data-confirm="¿Aprobar el pago de $ <?= number_format($p['monto'], 2) ?> de <?= htmlspecialchars($p['cliente_nombre'] ?? 'este cliente') ?>? Las cuotas se actualizarán en cascada.">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <button type="submit" class="w-full py-2 text-center text-xs font-bold text-white bg-emerald-500 rounded-lg hover:bg-emerald-600 transition-colors flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-sm">check_circle</span> Aprobar
                        </button>
                    </form>
                    <button type="button"
                            onclick="document.getElementById('reject-form-<?= $p['id'] ?>').classList.toggle('hidden')"
                            class="flex-1 py-2 text-center text-xs font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">cancel</span> Rechazar
                    </button>
                </div>

                <!-- Reject form (hidden by default) -->
                <form id="reject-form-<?= $p['id'] ?>" action="<?= Router::url('/admin/payments/reject/' . $p['id']) ?>" method="POST" class="hidden">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <textarea name="notas_rechazo" rows="2" placeholder="Motivo del rechazo (se notificará al cliente)..."
                              class="w-full bg-superficie border-none rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-red-400/30 resize-none mb-2"
                              required></textarea>
                    <button type="submit" class="w-full py-2 text-center text-xs font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Confirmar Rechazo y Notificar
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>


<!-- Tabs: Familiar / Escolar -->
<div class="mb-6">
    <div class="flex border-b border-petroleo/10">
        <button class="tab-btn px-6 py-3 text-sm font-bold transition-colors border-b-2 <?= $activeTab === 'familiar' ? 'text-turquesa border-turquesa' : 'text-petroleo/40 border-transparent hover:text-petroleo/70' ?>" data-tab="familiar">
            <span class="material-symbols-outlined text-sm align-middle mr-1">family_restroom</span>
            Familiar
            <?php if ($pendFamiliar > 0): ?><span class="ml-1 px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold"><?= $pendFamiliar ?></span><?php endif; ?>
        </button>
        <button class="tab-btn px-6 py-3 text-sm font-bold transition-colors border-b-2 <?= $activeTab === 'escolar' ? 'text-turquesa border-turquesa' : 'text-petroleo/40 border-transparent hover:text-petroleo/70' ?>" data-tab="escolar">
            <span class="material-symbols-outlined text-sm align-middle mr-1">school</span>
            Escolar
            <?php if ($pendEscolar > 0): ?><span class="ml-1 px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold"><?= $pendEscolar ?></span><?php endif; ?>
        </button>
    </div>
</div>

<!-- TAB: FAMILIAR -->
<div id="tab-familiar" class="tab-content <?= $activeTab !== 'familiar' ? 'hidden' : '' ?>">
    <?php if (empty($gruposFamiliares)): ?>
        <div class="text-center py-12 text-petroleo/40">No hay grupos familiares registrados.</div>
    <?php endif; ?>

    <?php foreach ($gruposFamiliares as $gi => $grupo): ?>
    <?php
        $valorTotal = (float) ($grupo['valor_total'] ?? 0);
        $totalPagado = (float) ($grupo['total_pagado_real'] ?? 0);
        $saldo = (float) ($grupo['saldo_real'] ?? 0);
        $progreso = $valorTotal > 0 ? min(100, round(($totalPagado / $valorTotal) * 100)) : 0;
        $pendientesGrupo = [];
        foreach ($grupo['contratos'] as $c) {
            if (isset($awaitingByContrato[$c['id']])) {
                $pendientesGrupo = array_merge($pendientesGrupo, $awaitingByContrato[$c['id']]);
            }
        }
        $contrato = $grupo['contratos'][0] ?? null;
    ?>
    <div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm mb-4 overflow-hidden grupo-card">
        <!-- Group Header -->
        <div class="p-5 cursor-pointer grupo-toggle" data-target="fam-<?= $gi ?>">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">family_restroom</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-petroleo"><?= htmlspecialchars($grupo['nombre']) ?></h3>
                        <p class="text-xs text-petroleo/50"><?= htmlspecialchars($grupo['destino'] ?? '') ?> · <?= $contrato ? htmlspecialchars($contrato['codigo']) : '' ?></p>
                    </div>
                    <?php if (!empty($pendientesGrupo)): ?>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold"><?= count($pendientesGrupo) ?> pendiente<?= count($pendientesGrupo) > 1 ? 's' : '' ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs text-petroleo/40">Pagado / Total</p>
                        <p class="font-bold text-petroleo">$ <?= number_format($totalPagado, 2) ?> <span class="text-petroleo/30">/ <?= number_format($valorTotal, 2) ?></span></p>
                    </div>
                    <div class="w-24 hidden sm:block">
                        <div class="h-2 bg-petroleo/5 rounded-full overflow-hidden">
                            <div class="h-full rounded-full <?= $progreso >= 100 ? 'bg-emerald-500' : ($progreso >= 50 ? 'bg-turquesa' : 'bg-amber-400') ?>" style="width: <?= $progreso ?>%"></div>
                        </div>
                        <p class="text-[10px] text-petroleo/40 text-right mt-0.5"><?= $progreso ?>%</p>
                    </div>
                    <span class="material-symbols-outlined text-petroleo/30 grupo-chevron transition-transform duration-200">expand_more</span>
                </div>
            </div>
        </div>

        <!-- Group Body (Cuotas) -->
        <div id="fam-<?= $gi ?>" class="grupo-body hidden border-t border-petroleo/5">
            <?php if ($contrato && !empty($contrato['cuotas'])): ?>
            <div class="p-5">
                <h4 class="text-xs font-bold uppercase tracking-widest text-petroleo/40 mb-3">Plan de Cuotas</h4>
                <div class="space-y-2">
                    <?php foreach ($contrato['cuotas'] as $cuota): ?>
                    <?php
                        $esperado = (float) $cuota['monto_esperado'];
                        $pagado = (float) $cuota['monto_pagado'];
                        $cuotaProg = $esperado > 0 ? min(100, round(($pagado / $esperado) * 100)) : 0;
                        $cuotaEstado = $cuota['estado'];
                    ?>
                    <div class="flex items-center gap-4 p-3 rounded-xl <?= $cuotaEstado === 'pagada' ? 'bg-emerald-50' : ($cuotaEstado === 'parcial' ? 'bg-amber-50' : 'bg-petroleo/[0.02]') ?>">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black <?= $cuotaEstado === 'pagada' ? 'bg-emerald-100 text-emerald-600' : ($cuotaEstado === 'parcial' ? 'bg-amber-100 text-amber-600' : 'bg-petroleo/5 text-petroleo/40') ?>">
                            <?= $cuota['numero_cuota'] ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-petroleo truncate"><?= htmlspecialchars($cuota['concepto']) ?></p>
                            <p class="text-xs text-petroleo/40"><?= $cuota['fecha_vencimiento'] ? date('d M Y', strtotime($cuota['fecha_vencimiento'])) : '—' ?></p>
                        </div>
                        <div class="w-20 hidden sm:block">
                            <div class="h-1.5 bg-petroleo/5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full <?= $cuotaEstado === 'pagada' ? 'bg-emerald-500' : ($cuotaEstado === 'parcial' ? 'bg-amber-400' : 'bg-petroleo/10') ?>" style="width: <?= $cuotaProg ?>%"></div>
                            </div>
                        </div>
                        <div class="text-right w-28">
                            <p class="text-sm font-bold text-petroleo">$ <?= number_format($pagado, 2) ?></p>
                            <p class="text-[10px] text-petroleo/40">de $ <?= number_format($esperado, 2) ?></p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $cuotaEstado === 'pagada' ? 'bg-emerald-100 text-emerald-700' : ($cuotaEstado === 'parcial' ? 'bg-amber-100 text-amber-700' : 'bg-petroleo/5 text-petroleo/40') ?>">
                            <?= $cuotaEstado ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Transactions for this group -->
            <?php
                $grupoContratoIds = array_map(fn($c) => $c['id'], $grupo['contratos']);
                $grupoTransactions = array_filter($transactions, fn($t) => in_array($t['contrato_id'] ?? 0, $grupoContratoIds));
            ?>
            <?php if (!empty($grupoTransactions)): ?>
            <div class="p-5 pt-0">
                <h4 class="text-xs font-bold uppercase tracking-widest text-petroleo/40 mb-3">Historial de Pagos</h4>
                <div class="space-y-1">
                    <?php foreach ($grupoTransactions as $t): ?>
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-humo/50 text-sm">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-sm <?= $t['estado'] === 'aprobado' ? 'text-emerald-500' : ($t['estado'] === 'rechazado' ? 'text-red-500' : 'text-amber-500') ?>">
                                <?= $t['estado'] === 'aprobado' ? 'check_circle' : ($t['estado'] === 'rechazado' ? 'cancel' : 'schedule') ?>
                            </span>
                            <div>
                                <span class="font-medium text-petroleo"><?= htmlspecialchars($t['concepto']) ?></span>
                                <span class="text-xs text-petroleo/40 ml-2"><?= date('d M Y', strtotime($t['created_at'])) ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-petroleo">$ <?= number_format($t['monto'], 2) ?></span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $t['estado'] === 'aprobado' ? 'bg-emerald-100 text-emerald-700' : ($t['estado'] === 'rechazado' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') ?>">
                                <?= $t['estado'] ?>
                            </span>
                            <?php if ($t['estado'] === 'aprobado' && !empty($t['recibo_url'])): ?>
                            <a href="<?= Router::url('/descargar-recibo.php?id=' . $t['id'] . '&mode=inline') ?>" title="Ver Recibo" target="_blank"
                               class="w-7 h-7 rounded-lg bg-emerald-100 hover:bg-emerald-200 flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-emerald-600 text-sm">picture_as_pdf</span>
                            </a>
                            <form method="POST" action="<?= Router::url('/admin/payments/' . $t['id'] . '/regenerate') ?>" style="display:inline;" 
                                  onsubmit="return confirm('¿Regenerar recibo de $<?= number_format($t['monto'], 2) ?>?');">
                                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                                <button type="submit" title="Regenerar Recibo" 
                                        class="w-7 h-7 rounded-lg bg-blue-100 hover:bg-blue-200 flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-blue-600 text-sm">refresh</span>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- TAB: ESCOLAR -->
<div id="tab-escolar" class="tab-content <?= $activeTab !== 'escolar' ? 'hidden' : '' ?>">
    <?php if (empty($gruposEscolares)): ?>
        <div class="text-center py-12 text-petroleo/40">No hay grupos escolares registrados.</div>
    <?php endif; ?>

    <?php foreach ($gruposEscolares as $gi => $grupo): ?>
    <?php
        $valorTotal = (float) ($grupo['valor_total'] ?? 0);
        $totalPagado = (float) ($grupo['total_pagado_real'] ?? 0);
        $saldo = (float) ($grupo['saldo_real'] ?? 0);
        $progreso = $valorTotal > 0 ? min(100, round(($totalPagado / $valorTotal) * 100)) : 0;
        $totalContratos = count($grupo['contratos'] ?? []);
        $pendientesGrupo = 0;
        foreach ($grupo['contratos'] as $c) {
            if (isset($awaitingByContrato[$c['id']])) $pendientesGrupo += count($awaitingByContrato[$c['id']]);
        }
    ?>
    <div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm mb-4 overflow-hidden grupo-card">
        <!-- Group Header -->
        <div class="p-5 cursor-pointer grupo-toggle" data-target="esc-<?= $gi ?>">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-petroleo"><?= htmlspecialchars($grupo['nombre']) ?></h3>
                        <p class="text-xs text-petroleo/50"><?= htmlspecialchars($grupo['institucion'] ?? '') ?> · <?= htmlspecialchars($grupo['destino'] ?? '') ?> · <?= $totalContratos ?> contrato<?= $totalContratos !== 1 ? 's' : '' ?></p>
                    </div>
                    <?php if ($pendientesGrupo > 0): ?>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold"><?= $pendientesGrupo ?> pendiente<?= $pendientesGrupo > 1 ? 's' : '' ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs text-petroleo/40">Pagado / Total</p>
                        <p class="font-bold text-petroleo">$ <?= number_format($totalPagado, 2) ?> <span class="text-petroleo/30">/ <?= number_format($valorTotal, 2) ?></span></p>
                    </div>
                    <div class="w-24 hidden sm:block">
                        <div class="h-2 bg-petroleo/5 rounded-full overflow-hidden">
                            <div class="h-full rounded-full <?= $progreso >= 100 ? 'bg-emerald-500' : ($progreso >= 50 ? 'bg-turquesa' : 'bg-amber-400') ?>" style="width: <?= $progreso ?>%"></div>
                        </div>
                        <p class="text-[10px] text-petroleo/40 text-right mt-0.5"><?= $progreso ?>%</p>
                    </div>
                    <span class="material-symbols-outlined text-petroleo/30 grupo-chevron transition-transform duration-200">expand_more</span>
                </div>
            </div>
        </div>

        <!-- Group Body: Contracts -->
        <div id="esc-<?= $gi ?>" class="grupo-body hidden border-t border-petroleo/5">
            <?php foreach ($grupo['contratos'] as $ci => $contrato): ?>
            <?php
                $cValor = (float) ($contrato['valor_total'] ?? 0);
                $cPagado = (float) ($contrato['total_pagado_real'] ?? 0);
                $cSaldo = max(0, $cValor - $cPagado);
                $cProg = $cValor > 0 ? min(100, round(($cPagado / $cValor) * 100)) : 0;
                $cPendientes = $awaitingByContrato[$contrato['id']] ?? [];
                $titular = $contrato['titular_nombre'] ?? 'Sin titular';
            ?>
            <div class="border-b border-petroleo/5 last:border-0">
                <!-- Contract Header -->
                <div class="px-5 py-4 cursor-pointer contrato-toggle" data-target="esc-<?= $gi ?>-c-<?= $ci ?>">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-petroleo/5 text-petroleo/60 flex items-center justify-center">
                                <span class="material-symbols-outlined text-sm">description</span>
                            </div>
                            <div>
                                <p class="font-bold text-sm text-petroleo"><?= htmlspecialchars($contrato['codigo'] ?? '') ?></p>
                                <p class="text-xs text-petroleo/50"><?= htmlspecialchars($titular) ?></p>
                            </div>
                            <?php if (!empty($cPendientes)): ?>
                            <span class="px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold"><?= count($cPendientes) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="font-bold text-sm text-petroleo">$ <?= number_format($cPagado, 2) ?> <span class="text-petroleo/30 font-normal">/ <?= number_format($cValor, 2) ?></span></p>
                                <p class="text-[10px] text-petroleo/40">Saldo: $ <?= number_format($cSaldo, 2) ?></p>
                            </div>
                            <div class="w-16 hidden sm:block">
                                <div class="h-1.5 bg-petroleo/5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full <?= $cProg >= 100 ? 'bg-emerald-500' : ($cProg >= 50 ? 'bg-turquesa' : 'bg-amber-400') ?>" style="width: <?= $cProg ?>%"></div>
                                </div>
                                <p class="text-[10px] text-petroleo/40 text-right mt-0.5"><?= $cProg ?>%</p>
                            </div>
                            <span class="material-symbols-outlined text-petroleo/20 text-sm contrato-chevron transition-transform duration-200">expand_more</span>
                        </div>
                    </div>
                </div>

                <!-- Contract Body: Cuotas -->
                <div id="esc-<?= $gi ?>-c-<?= $ci ?>" class="contrato-body hidden px-5 pb-4">
                    <?php if (!empty($contrato['cuotas'])): ?>
                    <div class="space-y-1.5 mb-3">
                        <?php foreach ($contrato['cuotas'] as $cuota): ?>
                        <?php
                            $esperado = (float) $cuota['monto_esperado'];
                            $pagado = (float) $cuota['monto_pagado'];
                            $cuotaProg = $esperado > 0 ? min(100, round(($pagado / $esperado) * 100)) : 0;
                            $cuotaEstado = $cuota['estado'];
                        ?>
                        <div class="flex items-center gap-3 p-2.5 rounded-lg <?= $cuotaEstado === 'pagada' ? 'bg-emerald-50' : ($cuotaEstado === 'parcial' ? 'bg-amber-50' : 'bg-petroleo/[0.02]') ?>">
                            <div class="w-7 h-7 rounded flex items-center justify-center text-[10px] font-black <?= $cuotaEstado === 'pagada' ? 'bg-emerald-100 text-emerald-600' : ($cuotaEstado === 'parcial' ? 'bg-amber-100 text-amber-600' : 'bg-petroleo/5 text-petroleo/40') ?>">
                                <?= $cuota['numero_cuota'] ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-petroleo truncate"><?= htmlspecialchars($cuota['concepto']) ?></p>
                                <p class="text-[10px] text-petroleo/40"><?= $cuota['fecha_vencimiento'] ? date('d M Y', strtotime($cuota['fecha_vencimiento'])) : '—' ?></p>
                            </div>
                            <div class="w-16 hidden sm:block">
                                <div class="h-1 bg-petroleo/5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full <?= $cuotaEstado === 'pagada' ? 'bg-emerald-500' : ($cuotaEstado === 'parcial' ? 'bg-amber-400' : 'bg-petroleo/10') ?>" style="width: <?= $cuotaProg ?>%"></div>
                                </div>
                            </div>
                            <div class="text-right w-24">
                                <p class="text-xs font-bold text-petroleo">$ <?= number_format($pagado, 2) ?></p>
                                <p class="text-[10px] text-petroleo/40">de $ <?= number_format($esperado, 2) ?></p>
                            </div>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase <?= $cuotaEstado === 'pagada' ? 'bg-emerald-100 text-emerald-700' : ($cuotaEstado === 'parcial' ? 'bg-amber-100 text-amber-700' : 'bg-petroleo/5 text-petroleo/40') ?>">
                                <?= $cuotaEstado ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Contract Transactions -->
                    <?php
                        $contratoTxns = array_filter($transactions, fn($t) => ($t['contrato_id'] ?? 0) == $contrato['id']);
                    ?>
                    <?php if (!empty($contratoTxns)): ?>
                    <h5 class="text-[10px] font-bold uppercase tracking-widest text-petroleo/30 mb-2 mt-2">Historial</h5>
                    <div class="space-y-1">
                        <?php foreach ($contratoTxns as $t): ?>
                        <div class="flex items-center justify-between p-2 rounded hover:bg-humo/50 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-xs <?= $t['estado'] === 'aprobado' ? 'text-emerald-500' : 'text-red-500' ?>">
                                    <?= $t['estado'] === 'aprobado' ? 'check_circle' : 'cancel' ?>
                                </span>
                                <span class="text-petroleo"><?= htmlspecialchars($t['concepto']) ?></span>
                                <span class="text-petroleo/30"><?= date('d M Y', strtotime($t['created_at'])) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-petroleo">$ <?= number_format($t['monto'], 2) ?></span>
                                <?php if ($t['estado'] === 'aprobado' && !empty($t['recibo_url'])): ?>
                                <a href="<?= Router::url('/descargar-recibo.php?id=' . $t['id'] . '&mode=inline') ?>" title="Ver Recibo" target="_blank"
                                   class="w-6 h-6 rounded bg-emerald-100 hover:bg-emerald-200 flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-emerald-600 text-xs">picture_as_pdf</span>
                                </a>
                                <form method="POST" action="<?= Router::url('/admin/payments/' . $t['id'] . '/regenerate') ?>" style="display:inline;"
                                      onsubmit="return confirm('¿Regenerar recibo?');">
                                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                                    <button type="submit" title="Regenerar Recibo" 
                                            class="w-6 h-6 rounded bg-blue-100 hover:bg-blue-200 flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-blue-600 text-xs">refresh</span>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- MODAL: Registrar Pago -->
<div id="modal-registrar" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="bg-petroleo p-5 text-white flex-shrink-0">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Registrar Pago</h3>
                    <p class="text-xs text-white/50">Selecciona las cuotas a pagar. El excedente se aplica a la siguiente.</p>
                </div>
                <button onclick="document.getElementById('modal-registrar').classList.add('hidden')" class="text-white/50 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        <form action="<?= Router::url('/admin/payments/register') ?>" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Contrato</label>
                    <select name="contrato_id" id="reg-contrato" required class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30">
                        <option value="">Seleccionar contrato...</option>
                        <?php foreach ($allContracts as $ac): ?>
                        <option value="<?= $ac['id'] ?>">[<?= htmlspecialchars(strtoupper($ac['tipo'])) ?>] <?= htmlspecialchars($ac['codigo']) ?> — <?= htmlspecialchars($ac['grupo']) ?><?= $ac['titular'] ? ' · ' . htmlspecialchars($ac['titular']) : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Dynamic cuotas selector -->
                <div id="cuotas-selector" class="hidden">
                    <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-2">Seleccionar Cuotas a Pagar</label>
                    <div id="cuotas-list" class="space-y-1.5 max-h-48 overflow-y-auto rounded-xl border border-petroleo/10 p-3 bg-superficie/50">
                        <!-- Populated by JS -->
                    </div>
                    <div class="flex justify-between items-center mt-2 px-1">
                        <button type="button" id="btn-select-all" class="text-xs text-turquesa font-bold hover:underline">Seleccionar todas</button>
                        <p class="text-xs text-petroleo/50">Total seleccionado: <span id="cuotas-total" class="font-bold text-petroleo">$ 0.00</span></p>
                    </div>
                </div>

                <!-- No cuotas message -->
                <div id="cuotas-empty" class="hidden">
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center">
                        <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                        <p class="text-xs text-emerald-700 font-medium mt-1">Todas las cuotas de este contrato están pagadas.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Monto (USD $)</label>
                        <input type="number" name="monto" id="reg-monto" step="0.01" min="0.01" required class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30" placeholder="0.00">
                        <p id="monto-hint" class="text-[10px] text-petroleo/40 mt-1 hidden">Si supera la cuota seleccionada, el excedente se aplica a la siguiente.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Concepto</label>
                        <input type="text" name="concepto" id="reg-concepto" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30" placeholder="Ej: Pago cuota marzo">
                    </div>
                </div>

                <!-- Cascade preview -->
                <div id="cascade-preview" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3">
                    <p class="text-xs font-bold text-blue-700 mb-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">info</span> Vista previa de distribución
                    </p>
                    <div id="cascade-detail" class="text-xs text-blue-600 space-y-0.5"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-petroleo/50 mb-1">Comprobante (opcional)</label>
                    <input type="file" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" class="w-full bg-superficie border-none rounded-xl px-4 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-turquesa file:text-white file:px-3 file:py-1 file:text-xs file:font-bold">
                    <p class="text-[10px] text-petroleo/40 mt-1">PDF, JPG o PNG · Máx. 5 MB</p>
                </div>

                <!-- Hidden: selected cuota IDs -->
                <div id="cuota-ids-container"></div>

                <button type="submit" class="w-full py-3 bg-turquesa text-white font-bold rounded-xl hover:bg-turquesa-dark transition-colors text-sm">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">payments</span>
                    Registrar y Aprobar Pago
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Cuotas data embedded from PHP
const cuotasMap = <?= json_encode($cuotasMap, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('text-turquesa', 'border-turquesa');
                b.classList.add('text-petroleo/40', 'border-transparent');
            });
            document.getElementById('tab-' + tab)?.classList.remove('hidden');
            this.classList.remove('text-petroleo/40', 'border-transparent');
            this.classList.add('text-turquesa', 'border-turquesa');
        });
    });

    // Group accordion
    document.querySelectorAll('.grupo-toggle').forEach(el => {
        el.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            const chevron = this.querySelector('.grupo-chevron');
            if (target) {
                target.classList.toggle('hidden');
                chevron?.classList.toggle('rotate-180');
            }
        });
    });

    // Contract accordion (nested)
    document.querySelectorAll('.contrato-toggle').forEach(el => {
        el.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            const chevron = this.querySelector('.contrato-chevron');
            if (target) {
                target.classList.toggle('hidden');
                chevron?.classList.toggle('rotate-180');
            }
        });
    });

    // Confirm dialogs
    document.querySelectorAll('[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });

    // Close modal on backdrop click
    document.getElementById('modal-registrar')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });

    // ===== CUOTA SELECTION LOGIC =====
    const contratoSelect = document.getElementById('reg-contrato');
    const cuotasSelector = document.getElementById('cuotas-selector');
    const cuotasEmpty = document.getElementById('cuotas-empty');
    const cuotasList = document.getElementById('cuotas-list');
    const cuotaIdsContainer = document.getElementById('cuota-ids-container');
    const montoInput = document.getElementById('reg-monto');
    const conceptoInput = document.getElementById('reg-concepto');
    const cuotasTotal = document.getElementById('cuotas-total');
    const montoHint = document.getElementById('monto-hint');
    const cascadePreview = document.getElementById('cascade-preview');
    const cascadeDetail = document.getElementById('cascade-detail');
    const btnSelectAll = document.getElementById('btn-select-all');

    function formatMoney(n) {
        return '$ ' + Number(n).toFixed(2);
    }

    function renderCuotas(cuotas) {
        cuotasList.innerHTML = '';
        cuotaIdsContainer.innerHTML = '';

        if (!cuotas || cuotas.length === 0) {
            cuotasSelector.classList.add('hidden');
            cuotasEmpty.classList.remove('hidden');
            montoInput.value = '';
            conceptoInput.value = '';
            return;
        }

        cuotasEmpty.classList.add('hidden');
        cuotasSelector.classList.remove('hidden');

        cuotas.forEach(function(q) {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 p-2.5 rounded-lg cursor-pointer transition-colors ' +
                (q.estado === 'parcial' ? 'bg-amber-50 hover:bg-amber-100/70' : 'bg-white hover:bg-turquesa/5');

            const checked = q.estado === 'parcial' ? 'checked' : '';
            div.innerHTML =
                '<input type="checkbox" class="cuota-check w-4 h-4 rounded border-petroleo/20 text-turquesa focus:ring-turquesa/30" ' +
                    'data-id="' + q.id + '" data-faltante="' + q.faltante + '" data-numero="' + q.numero + '" data-concepto="' + (q.concepto || '') + '" ' + checked + '>' +
                '<div class="w-7 h-7 rounded flex items-center justify-center text-[10px] font-black ' +
                    (q.estado === 'parcial' ? 'bg-amber-100 text-amber-600' : 'bg-petroleo/5 text-petroleo/40') + '">' + q.numero + '</div>' +
                '<div class="flex-1 min-w-0">' +
                    '<p class="text-xs font-medium text-petroleo truncate">' + (q.concepto || 'Cuota ' + q.numero) + '</p>' +
                    '<p class="text-[10px] text-petroleo/40">' + (q.fecha ? new Date(q.fecha + 'T00:00:00').toLocaleDateString('es-PE', {day:'2-digit',month:'short',year:'numeric'}) : '—') + '</p>' +
                '</div>' +
                '<div class="text-right">' +
                    '<p class="text-xs font-bold text-petroleo">' + formatMoney(q.faltante) + '</p>' +
                    (q.pagado > 0 ? '<p class="text-[10px] text-amber-600">Abonado: ' + formatMoney(q.pagado) + '</p>' : '') +
                '</div>';

            // Click anywhere on the row to toggle checkbox
            div.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const cb = div.querySelector('.cuota-check');
                    cb.checked = !cb.checked;
                }
                recalculate();
            });

            cuotasList.appendChild(div);
        });

        // Auto-select first pending cuota
        const firstPending = cuotasList.querySelector('.cuota-check:not(:checked)');
        if (firstPending && !cuotasList.querySelector('.cuota-check:checked')) {
            firstPending.checked = true;
        }
        recalculate();
    }

    function recalculate() {
        const checks = cuotasList.querySelectorAll('.cuota-check:checked');
        let total = 0;
        const conceptos = [];
        cuotaIdsContainer.innerHTML = '';

        checks.forEach(function(cb) {
            total += parseFloat(cb.dataset.faltante);
            conceptos.push('Cuota ' + cb.dataset.numero);
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'cuota_ids[]';
            hidden.value = cb.dataset.id;
            cuotaIdsContainer.appendChild(hidden);
        });

        cuotasTotal.textContent = formatMoney(total);
        montoInput.value = total > 0 ? total.toFixed(2) : '';
        conceptoInput.value = conceptos.length > 0 ? 'Pago ' + conceptos.join(', ') : '';

        if (checks.length > 0) {
            montoHint.classList.remove('hidden');
        } else {
            montoHint.classList.add('hidden');
        }

        updateCascadePreview();
    }

    function updateCascadePreview() {
        const monto = parseFloat(montoInput.value) || 0;
        const checks = cuotasList.querySelectorAll('.cuota-check:checked');
        if (monto <= 0 || checks.length === 0) {
            cascadePreview.classList.add('hidden');
            return;
        }

        let remaining = monto;
        const lines = [];

        // First, apply to selected cuotas in order
        const selectedIds = new Set();
        checks.forEach(function(cb) { selectedIds.add(cb.dataset.id); });

        // Get ALL cuotas for this contract (selected first, then remaining)
        const allChecks = cuotasList.querySelectorAll('.cuota-check');
        const ordered = [];
        // Selected first
        allChecks.forEach(function(cb) {
            if (selectedIds.has(cb.dataset.id)) ordered.push(cb);
        });
        // Then unselected (for cascade overflow)
        allChecks.forEach(function(cb) {
            if (!selectedIds.has(cb.dataset.id)) ordered.push(cb);
        });

        ordered.forEach(function(cb) {
            if (remaining <= 0) return;
            const faltante = parseFloat(cb.dataset.faltante);
            const num = cb.dataset.numero;
            const isSelected = selectedIds.has(cb.dataset.id);

            if (remaining >= faltante) {
                lines.push('<div class="flex justify-between"><span>Cuota ' + num + (isSelected ? '' : ' (cascada)') + '</span><span class="font-bold">' + formatMoney(faltante) + ' ✓ completa</span></div>');
                remaining -= faltante;
            } else {
                lines.push('<div class="flex justify-between"><span>Cuota ' + num + (isSelected ? '' : ' (cascada)') + '</span><span class="font-bold">' + formatMoney(remaining) + ' parcial</span></div>');
                remaining = 0;
            }
        });

        if (remaining > 0) {
            lines.push('<div class="flex justify-between text-amber-600"><span>Excedente</span><span class="font-bold">' + formatMoney(remaining) + '</span></div>');
        }

        cascadeDetail.innerHTML = lines.join('');
        cascadePreview.classList.remove('hidden');
    }

    // Contract changed → load cuotas
    contratoSelect?.addEventListener('change', function() {
        const cid = this.value;
        if (!cid) {
            cuotasSelector.classList.add('hidden');
            cuotasEmpty.classList.add('hidden');
            montoInput.value = '';
            conceptoInput.value = '';
            cascadePreview.classList.add('hidden');
            return;
        }
        renderCuotas(cuotasMap[cid] || []);
    });

    // Monto manual change → update preview
    montoInput?.addEventListener('input', updateCascadePreview);

    // Select all button
    btnSelectAll?.addEventListener('click', function() {
        const checks = cuotasList.querySelectorAll('.cuota-check');
        const allChecked = Array.from(checks).every(c => c.checked);
        checks.forEach(function(cb) { cb.checked = !allChecked; });
        recalculate();
    });
});
</script>
