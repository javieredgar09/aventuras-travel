<!-- PANEL REPRESENTANTE – Vista de Contratos & Estado de Pagos -->
<?php
require_once __DIR__ . '/../../../helpers/DestinationHelper.php';

$grupo      = $grupo ?? null;
$grupos     = $grupos ?? [];
$contratos  = $contratos ?? [];
$stats      = $stats ?? [];
$user       = $_SESSION['user'] ?? [];
$nombre     = htmlspecialchars(trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')));

$totalContratos = (int)($stats['total'] ?? 0);
$paidCount      = (int)($stats['paid'] ?? 0);
$pendingCount   = (int)($stats['pending'] ?? 0);
$overdueCount   = (int)($stats['overdue'] ?? 0);
$totalRecaudado = (float)($stats['recaudado'] ?? 0);
$totalDeuda     = (float)($stats['deuda'] ?? 0);
$pctGlobal      = $totalDeuda > 0 ? round(($totalRecaudado / $totalDeuda) * 100) : 0;

if (!function_exists('fmoney')) {
    function fmoney(float $v): string { return '$' . number_format($v, 2); }
}

$grupoDestino = $grupo['destino'] ?? 'Viaje';
$heroImg = DestinationHelper::getHeroImage($grupoDestino);
$destIcon = DestinationHelper::getIcon($grupoDestino);
$accentColor = DestinationHelper::getAccentColor($grupoDestino);
?>

<!-- HERO BANNER -->
<section class="relative h-[200px] sm:h-[240px] md:h-[280px] rounded-2xl overflow-hidden mb-6 group shadow-2xl">
    <img src="<?= htmlspecialchars($heroImg) ?>" alt="<?= htmlspecialchars($grupoDestino) ?>"
         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="eager">
    <div class="absolute inset-0 bg-gradient-to-r from-petroleo-dark/95 via-petroleo/65 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark/90 via-transparent to-transparent"></div>
    <div class="absolute top-0 right-0 w-60 h-60 rounded-full blur-3xl -translate-y-1/3 translate-x-1/4 pointer-events-none" style="background:<?= $accentColor ?>33;"></div>
    <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-7">
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-xl text-white px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest mb-3 border border-white/20 shadow-lg">
            <span class="material-symbols-outlined text-sm text-turquesa-light">school</span>
            Panel Representante
        </div>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white drop-shadow-2xl">
            <span class="mr-1"><?= $destIcon ?></span> <?= htmlspecialchars($grupoDestino) ?>
        </h1>
        <p class="text-white/60 text-xs mt-2 max-w-lg">
            Gestiona los contratos de viaje del grupo, controla los pagos y asegura que todos estén listos para la salida.
        </p>
    </div>
    <div class="absolute top-5 right-5">
        <a href="<?= Router::url('/leader/payments') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-turquesa to-turquesa-dark text-white font-bold text-sm hover:shadow-lg hover:shadow-turquesa/40 transition-all active:scale-95 shadow-lg">
            <span class="material-symbols-outlined text-lg">payments</span>
            Registrar Pago
        </a>
    </div>
</section>

<!-- Grupo Info Bar -->
<?php if ($grupo): ?>
<div class="bg-white rounded-xl p-4 border border-petroleo/5 shadow-sm mb-6 flex flex-wrap items-center gap-6">
    <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-turquesa text-2xl">groups</span>
        <div>
            <p class="text-sm font-black text-petroleo"><?= htmlspecialchars($grupo['nombre'] ?? '') ?></p>
            <p class="text-xs text-petroleo/40"><?= htmlspecialchars($grupo['institucion'] ?? '') ?></p>
        </div>
    </div>
    <div class="h-8 w-px bg-petroleo/10 hidden md:block"></div>
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-sm text-petroleo/40">location_on</span>
        <span class="text-sm text-petroleo/60"><?= htmlspecialchars($grupo['destino'] ?? 'Destino por definir') ?></span>
    </div>
    <div class="h-8 w-px bg-petroleo/10 hidden md:block"></div>
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-sm text-petroleo/40">person</span>
        <span class="text-sm text-petroleo/60">Responsable: <strong class="text-petroleo"><?= $nombre ?></strong></span>
    </div>
    <?php if (count($grupos) > 1): ?>
    <div class="h-8 w-px bg-petroleo/10 hidden md:block"></div>
    <span class="text-xs bg-turquesa/10 text-turquesa-dark font-bold px-3 py-1 rounded-full"><?= count($grupos) ?> grupos</span>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl p-5 border border-petroleo/5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-petroleo/40 mb-3">Total Contratos</p>
        <div class="flex items-end justify-between">
            <span class="text-4xl font-black text-petroleo"><?= $totalContratos ?></span>
            <span class="material-symbols-outlined text-3xl text-turquesa/30">description</span>
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 border-l-4 border-l-emerald-400 border border-petroleo/5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-petroleo/40 mb-3">Pagados / Al Día</p>
        <div class="flex items-end justify-between">
            <span class="text-4xl font-black text-emerald-600"><?= $paidCount ?></span>
            <span class="material-symbols-outlined text-3xl text-emerald-300">check_circle</span>
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 border-l-4 border-l-amber-400 border border-petroleo/5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-petroleo/40 mb-3">Pendientes</p>
        <div class="flex items-end justify-between">
            <span class="text-4xl font-black text-amber-600"><?= $pendingCount ?></span>
            <span class="material-symbols-outlined text-3xl text-amber-300">schedule</span>
        </div>
    </div>
    <div class="bg-red-50 rounded-xl p-5 border-l-4 border-l-red-400 border border-red-100 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-red-500 mb-3">Pagos Atrasados</p>
        <div class="flex items-end justify-between">
            <span class="text-4xl font-black text-red-600"><?= $overdueCount ?></span>
            <span class="material-symbols-outlined text-3xl text-red-300">warning</span>
        </div>
    </div>
</div>

<!-- Recaudación Global -->
<div class="bg-gradient-to-r from-petroleo to-turquesa-dark rounded-xl p-6 text-white mb-8 shadow-lg">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-white/50 mb-1">Recaudación Global</p>
            <p class="text-3xl md:text-4xl font-black"><?= fmoney($totalRecaudado) ?></p>
        </div>
        <div class="text-right">
            <p class="text-xs text-white/50">Total Esperado</p>
            <p class="text-lg font-bold"><?= fmoney($totalDeuda) ?></p>
        </div>
    </div>
    <div class="mt-4">
        <div class="flex justify-between text-xs text-white/60 mb-1">
            <span>Progreso de recaudación</span>
            <span class="font-bold text-white"><?= $pctGlobal ?>%</span>
        </div>
        <div class="w-full bg-white/20 rounded-full h-2.5">
            <div class="h-2.5 rounded-full transition-all duration-500 <?= $pctGlobal >= 80 ? 'bg-emerald-400' : ($pctGlobal >= 50 ? 'bg-amber-400' : 'bg-white') ?>" style="width: <?= $pctGlobal ?>%"></div>
        </div>
        <p class="text-xs text-white/40 mt-2">Saldo pendiente: <strong class="text-white/80"><?= fmoney($totalDeuda - $totalRecaudado) ?></strong></p>
    </div>
</div>

<!-- Buscador / Filtro -->
<div class="bg-white rounded-xl p-4 border border-petroleo/5 shadow-sm mb-6">
    <div class="flex flex-col md:flex-row gap-3">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-petroleo/30">search</span>
            <input type="text" id="searchInput" placeholder="Buscar por nombre, contrato o destino..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-petroleo/10 text-sm focus:outline-none focus:ring-2 focus:ring-turquesa/30 focus:border-turquesa transition-all">
        </div>
        <select id="filterStatus" class="px-4 py-2.5 rounded-lg border border-petroleo/10 text-sm text-petroleo bg-white focus:outline-none focus:ring-2 focus:ring-turquesa/30 cursor-pointer">
            <option value="todos">Todos los estados</option>
            <option value="pagado">Pagado</option>
            <option value="parcial">Parcial / Al Día</option>
            <option value="pendiente">Pendiente</option>
            <option value="atrasado">Atrasado</option>
        </select>
        <select id="sortBy" class="px-4 py-2.5 rounded-lg border border-petroleo/10 text-sm text-petroleo bg-white focus:outline-none focus:ring-2 focus:ring-turquesa/30 cursor-pointer">
            <option value="nombre">Ordenar: Nombre</option>
            <option value="monto_desc">Monto: Mayor a Menor</option>
            <option value="monto_asc">Monto: Menor a Mayor</option>
            <option value="estado">Estado de Pago</option>
        </select>
    </div>
</div>

<!-- Tabla de Contratos -->
<div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-petroleo/5 flex justify-between items-center">
        <h2 class="text-lg font-black text-petroleo flex items-center gap-2">
            <span class="material-symbols-outlined text-turquesa">description</span>
            Contratos del Grupo
        </h2>
        <span id="contractCount" class="text-xs text-petroleo/40 font-bold"><?= $totalContratos ?> contratos</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full" id="contractsTable">
            <thead>
                <tr class="text-[10px] uppercase tracking-widest text-petroleo/40 border-b border-petroleo/5 bg-humo/30">
                    <th class="text-left px-6 py-3">Titular / Contrato</th>
                    <th class="text-left px-4 py-3">Destino</th>
                    <th class="text-left px-4 py-3 hidden lg:table-cell">Pasajeros</th>
                    <th class="text-right px-4 py-3">Monto Total</th>
                    <th class="text-right px-4 py-3">Pagado</th>
                    <th class="text-center px-4 py-3">Progreso</th>
                    <th class="text-center px-4 py-3">Estado</th>
                    <th class="text-center px-6 py-3">Detalle</th>
                </tr>
            </thead>
            <tbody id="contractsBody">
                <?php if (!empty($contratos)): ?>
                <?php foreach ($contratos as $c):
                    $titular = htmlspecialchars($c['titular_nombre'] ?? 'Sin titular');
                    $codigo  = htmlspecialchars($c['codigo'] ?? $c['numero_contrato'] ?? '');
                    $destino = htmlspecialchars($c['destino'] ?? $c['grupo_destino'] ?? '');
                    $nPax    = count($c['pasajeros'] ?? []);
                    $valor   = (float)($c['valor_total'] ?? 0);
                    $pagado  = (float)($c['cuota_pagada'] ?? 0);
                    $pct     = (int)($c['pct_pagado'] ?? 0);
                    $ep      = $c['estado_pago'] ?? 'pendiente';
                    $vencidas = (int)($c['cuotas_vencidas'] ?? 0);
                    $initials = '';
                    $parts = explode(' ', $titular);
                    foreach (array_slice($parts, 0, 2) as $p) { $initials .= strtoupper(mb_substr($p, 0, 1)); }

                    $badgeColors = [
                        'pagado'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'parcial'   => 'bg-blue-50 text-blue-700 border-blue-200',
                        'pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'atrasado'  => 'bg-red-50 text-red-700 border-red-200',
                    ];
                    $badgeClass = $badgeColors[$ep] ?? $badgeColors['pendiente'];
                    $badgeLabel = ['pagado' => 'Pagado', 'parcial' => 'Al Día', 'pendiente' => 'Pendiente', 'atrasado' => 'Atrasado'];
                    $avatarColors = [
                        'pagado'    => 'bg-emerald-100 text-emerald-700',
                        'parcial'   => 'bg-blue-100 text-blue-700',
                        'pendiente' => 'bg-amber-100 text-amber-700',
                        'atrasado'  => 'bg-red-100 text-red-700',
                    ];
                    $avatarClass = $avatarColors[$ep] ?? $avatarColors['pendiente'];
                    $progressColor = $pct >= 100 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-blue-500' : ($pct > 0 ? 'bg-amber-500' : 'bg-slate-300'));
                    $rowBg = $ep === 'atrasado' ? 'bg-red-50/30' : '';
                ?>
                <tr class="border-b border-petroleo/5 hover:bg-humo/50 transition-all contract-row <?= $rowBg ?>"
                    data-name="<?= strtolower($titular) ?>"
                    data-code="<?= strtolower($codigo) ?>"
                    data-destino="<?= strtolower($destino) ?>"
                    data-estado="<?= $ep ?>"
                    data-monto="<?= $valor ?>">
                    <!-- Titular -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full <?= $avatarClass ?> flex items-center justify-center text-xs font-black flex-shrink-0">
                                <?= $initials ?>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-petroleo truncate"><?= $titular ?></p>
                                <p class="text-xs text-petroleo/40 font-mono">#<?= $codigo ?></p>
                            </div>
                        </div>
                    </td>
                    <!-- Destino -->
                    <td class="px-4 py-4">
                        <p class="text-sm text-petroleo"><?= $destino ?></p>
                    </td>
                    <!-- Pasajeros -->
                    <td class="px-4 py-4 hidden lg:table-cell">
                        <span class="inline-flex items-center gap-1 text-sm text-petroleo/60">
                            <span class="material-symbols-outlined text-sm">person</span>
                            <?= $nPax ?>
                        </span>
                    </td>
                    <!-- Monto Total -->
                    <td class="px-4 py-4 text-right">
                        <p class="text-sm font-bold text-petroleo"><?= fmoney($valor) ?></p>
                    </td>
                    <!-- Pagado -->
                    <td class="px-4 py-4 text-right">
                        <p class="text-sm font-bold <?= $pagado >= $valor ? 'text-emerald-600' : 'text-petroleo/60' ?>"><?= fmoney($pagado) ?></p>
                        <?php if ($valor > 0 && $pagado < $valor): ?>
                        <p class="text-[10px] text-red-400">Debe: <?= fmoney($valor - $pagado) ?></p>
                        <?php endif; ?>
                    </td>
                    <!-- Progreso -->
                    <td class="px-4 py-4">
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-full max-w-[100px] bg-slate-100 rounded-full h-2">
                                <div class="<?= $progressColor ?> h-2 rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-petroleo/40"><?= $pct ?>%</span>
                        </div>
                    </td>
                    <!-- Estado -->
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold uppercase border <?= $badgeClass ?>">
                            <?php if ($ep === 'atrasado'): ?>
                            <span class="material-symbols-outlined text-xs">warning</span>
                            <?php endif; ?>
                            <?= $badgeLabel[$ep] ?? ucfirst($ep) ?>
                        </span>
                        <?php if ($vencidas > 0): ?>
                        <p class="text-[10px] text-red-500 mt-1"><?= $vencidas ?> cuota<?= $vencidas > 1 ? 's' : '' ?> vencida<?= $vencidas > 1 ? 's' : '' ?></p>
                        <?php endif; ?>
                    </td>
                    <!-- Acciones -->
                    <td class="px-6 py-4 text-center">
                        <button onclick="toggleDetail(<?= (int)$c['id'] ?>)" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-turquesa-dark bg-turquesa/10 rounded-lg hover:bg-turquesa/20 transition-all">
                            <span class="material-symbols-outlined text-sm detail-icon-<?= (int)$c['id'] ?>">expand_more</span>
                            Ver
                        </button>
                    </td>
                </tr>
                <!-- Fila expandible: detalle de cuotas -->
                <tr id="detail-<?= (int)$c['id'] ?>" class="hidden">
                    <td colspan="8" class="px-6 py-0">
                        <div class="bg-humo rounded-xl p-5 my-2 border border-petroleo/5">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-3">
                                <h3 class="text-sm font-black text-petroleo flex items-center gap-2">
                                    <span class="material-symbols-outlined text-turquesa text-lg">event_note</span>
                                    Cronograma de Cuotas — <?= $codigo ?>
                                </h3>
                                <div class="flex items-center gap-2 text-xs text-petroleo/50">
                                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Pagada</span>
                                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Parcial</span>
                                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Vencida</span>
                                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-300"></span> Pendiente</span>
                                </div>
                            </div>
                            <?php
                            $cuotaModel = new Cuota();
                            $cuotasContrato = $cuotaModel->getByEntidad('contrato', (int)$c['id']);
                            ?>
                            <?php if (!empty($cuotasContrato)): ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="text-[10px] uppercase tracking-widest text-petroleo/30 border-b border-petroleo/5">
                                            <th class="text-left pb-2 px-3">Cuota</th>
                                            <th class="text-left pb-2 px-3">Concepto</th>
                                            <th class="text-left pb-2 px-3">Vencimiento</th>
                                            <th class="text-right pb-2 px-3">Esperado</th>
                                            <th class="text-right pb-2 px-3">Pagado</th>
                                            <th class="text-center pb-2 px-3">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cuotasContrato as $ct):
                                            $estCuota = $ct['estado'] ?? 'pendiente';
                                            $vencida = ($estCuota !== 'pagada' && !empty($ct['fecha_vencimiento']) && $ct['fecha_vencimiento'] < date('Y-m-d'));
                                            $cuotaBadge = match(true) {
                                                $estCuota === 'pagada' => 'bg-emerald-50 text-emerald-700',
                                                $estCuota === 'parcial' => 'bg-amber-50 text-amber-700',
                                                $vencida => 'bg-red-50 text-red-700',
                                                default => 'bg-slate-50 text-slate-500',
                                            };
                                            $cuotaLabel = match(true) {
                                                $estCuota === 'pagada' => 'Pagada',
                                                $estCuota === 'parcial' => 'Parcial',
                                                $vencida => 'Vencida',
                                                default => 'Pendiente',
                                            };
                                        ?>
                                        <tr class="border-b border-petroleo/5 last:border-0 <?= $vencida ? 'bg-red-50/40' : '' ?>">
                                            <td class="px-3 py-2.5 text-sm font-bold text-petroleo">
                                                <?= str_pad($ct['numero_cuota'] ?? '?', 2, '0', STR_PAD_LEFT) ?>
                                            </td>
                                            <td class="px-3 py-2.5 text-sm text-petroleo/60">
                                                <?= htmlspecialchars($ct['concepto'] ?? 'Cuota ' . ($ct['numero_cuota'] ?? '')) ?>
                                            </td>
                                            <td class="px-3 py-2.5 text-sm text-petroleo/60">
                                                <?php if (!empty($ct['fecha_vencimiento'])): ?>
                                                    <?= date('d M Y', strtotime($ct['fecha_vencimiento'])) ?>
                                                    <?php if ($vencida): ?>
                                                    <span class="text-red-500 text-[10px] font-bold ml-1">VENCIDA</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-3 py-2.5 text-sm text-right font-medium text-petroleo">
                                                <?= fmoney((float)($ct['monto_esperado'] ?? 0)) ?>
                                            </td>
                                            <td class="px-3 py-2.5 text-sm text-right font-medium <?= $estCuota === 'pagada' ? 'text-emerald-600' : 'text-petroleo/50' ?>">
                                                <?= fmoney((float)($ct['monto_pagado'] ?? 0)) ?>
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $cuotaBadge ?>">
                                                    <?= $cuotaLabel ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <p class="text-sm text-petroleo/30 text-center py-4">No hay cuotas registradas para este contrato.</p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <span class="material-symbols-outlined text-5xl text-petroleo/10 mb-3 block">description</span>
                        <p class="text-lg font-bold text-petroleo/30">Sin contratos registrados</p>
                        <p class="text-sm text-petroleo/20 mt-1">Los contratos aparecerán aquí cuando se registren en el sistema.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalContratos > 0): ?>
    <div class="px-6 py-3 border-t border-petroleo/5 flex flex-wrap justify-between items-center text-xs text-petroleo/40 gap-2">
        <span id="showingText">Mostrando <strong class="text-petroleo"><?= $totalContratos ?></strong> contratos</span>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1 text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> <?= $paidCount ?> pagados</span>
            <span class="inline-flex items-center gap-1 text-amber-600"><span class="w-2 h-2 rounded-full bg-amber-500"></span> <?= $pendingCount ?> pendientes</span>
            <?php if ($overdueCount > 0): ?>
            <span class="inline-flex items-center gap-1 text-red-600"><span class="w-2 h-2 rounded-full bg-red-500"></span> <?= $overdueCount ?> atrasados</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Resumen por Estado -->
<?php if ($totalContratos > 0): ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl p-5 border border-emerald-100 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600">thumb_up</span>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-petroleo/40">Al Día / Pagados</p>
                <p class="text-2xl font-black text-emerald-600"><?= $paidCount ?></p>
            </div>
        </div>
        <p class="text-xs text-petroleo/40">Contratos con todos sus pagos completos.</p>
    </div>
    <div class="bg-white rounded-xl p-5 border border-amber-100 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600">hourglass_top</span>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-petroleo/40">Pendientes</p>
                <p class="text-2xl font-black text-amber-600"><?= $pendingCount ?></p>
            </div>
        </div>
        <p class="text-xs text-petroleo/40">Contratos con pagos parciales o sin pagos registrados.</p>
    </div>
    <div class="bg-white rounded-xl p-5 border border-red-100 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-600">report</span>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-petroleo/40">Atrasados</p>
                <p class="text-2xl font-black text-red-600"><?= $overdueCount ?></p>
            </div>
        </div>
        <p class="text-xs text-petroleo/40">Contratos con cuotas vencidas que requieren atención.</p>
    </div>
</div>
<?php endif; ?>

<!-- JS: Búsqueda, Filtros y Expandir -->
<script>
function toggleDetail(id) {
    var row = document.getElementById('detail-' + id);
    if (!row) return;
    var icons = document.querySelectorAll('.detail-icon-' + id);
    if (row.classList.contains('hidden')) {
        row.classList.remove('hidden');
        icons.forEach(function(i) { i.textContent = 'expand_less'; });
    } else {
        row.classList.add('hidden');
        icons.forEach(function(i) { i.textContent = 'expand_more'; });
    }
}

(function() {
    var searchInput = document.getElementById('searchInput');
    var filterStatus = document.getElementById('filterStatus');
    var sortBy = document.getElementById('sortBy');
    var rows = document.querySelectorAll('.contract-row');
    var countEl = document.getElementById('contractCount');
    var showingEl = document.getElementById('showingText');

    function applyFilters() {
        var query = (searchInput.value || '').toLowerCase().trim();
        var status = filterStatus.value;
        var visible = 0;

        rows.forEach(function(row) {
            var name = row.getAttribute('data-name') || '';
            var code = row.getAttribute('data-code') || '';
            var dest = row.getAttribute('data-destino') || '';
            var est  = row.getAttribute('data-estado') || '';

            var matchSearch = !query || name.indexOf(query) !== -1 || code.indexOf(query) !== -1 || dest.indexOf(query) !== -1;
            var matchStatus = status === 'todos' || est === status;

            var detailRow = row.nextElementSibling;
            if (matchSearch && matchStatus) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
                if (detailRow && detailRow.id && detailRow.id.startsWith('detail-')) {
                    detailRow.classList.add('hidden');
                }
            }
        });

        if (countEl) countEl.textContent = visible + ' contratos';
        if (showingEl) showingEl.innerHTML = 'Mostrando <strong class="text-petroleo">' + visible + '</strong> contratos';
    }

    function applySort() {
        var val = sortBy.value;
        var tbody = document.getElementById('contractsBody');
        if (!tbody) return;

        var pairs = [];
        for (var i = 0; i < rows.length; i++) {
            var detail = rows[i].nextElementSibling;
            pairs.push({ row: rows[i], detail: (detail && detail.id && detail.id.startsWith('detail-')) ? detail : null });
        }

        pairs.sort(function(a, b) {
            if (val === 'nombre') {
                return (a.row.getAttribute('data-name') || '').localeCompare(b.row.getAttribute('data-name') || '');
            } else if (val === 'monto_desc') {
                return parseFloat(b.row.getAttribute('data-monto') || 0) - parseFloat(a.row.getAttribute('data-monto') || 0);
            } else if (val === 'monto_asc') {
                return parseFloat(a.row.getAttribute('data-monto') || 0) - parseFloat(b.row.getAttribute('data-monto') || 0);
            } else if (val === 'estado') {
                var order = { atrasado: 0, pendiente: 1, parcial: 2, pagado: 3 };
                return (order[a.row.getAttribute('data-estado')] || 9) - (order[b.row.getAttribute('data-estado')] || 9);
            }
            return 0;
        });

        pairs.forEach(function(p) {
            tbody.appendChild(p.row);
            if (p.detail) tbody.appendChild(p.detail);
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);
    if (sortBy) sortBy.addEventListener('change', function() { applySort(); applyFilters(); });
})();
</script>
