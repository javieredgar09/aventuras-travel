<!-- DASHBOARD GRUPO LÍDER – TAILWIND – Vinculación Grupo-Contratos -->
<?php
$grupo = $grupo ?? null;
$contratos = $contratos ?? [];
$pagos = $pagos ?? [];
$user = $_SESSION['user'] ?? [];
$nombre = htmlspecialchars(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
$totalPasajeros = 0;
$totalRecaudado = 0;
$totalPendiente = 0;
foreach ($contratos as $c) {
    $totalPasajeros += count($c['pasajeros'] ?? []);
}
foreach ($pagos as $p) {
    if ($p['estado'] === 'aprobado') $totalRecaudado += $p['monto'];
    else $totalPendiente += $p['monto'];
}
?>

<!-- Header -->
<div class="flex justify-between items-start mb-8">
    <div>
        <div class="text-xs text-petroleo/40 mb-1">Grupos › <?= htmlspecialchars($grupo['nombre'] ?? 'Mi Grupo') ?></div>
        <h1 class="text-3xl font-black text-petroleo">Gestión de Grupo</h1>
    </div>
</div>

<!-- Grupo Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Grupo Info -->
    <div class="bg-white rounded-xl p-6 border border-petroleo/5 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <span class="material-symbols-outlined text-turquesa text-3xl">groups</span>
            <span class="badge-active px-3 py-1 rounded-full text-[10px] font-bold uppercase">ACTIVO</span>
        </div>
        <h2 class="text-xl font-black text-petroleo mb-1"><?= htmlspecialchars($grupo['nombre'] ?? 'Promo 2026') ?></h2>
        <p class="text-sm text-petroleo/50 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">location_on</span>
            <?= htmlspecialchars($grupo['destino'] ?? 'Destino por definir') ?>
        </p>
        <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-petroleo/5">
            <div>
                <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Pasajeros</p>
                <p class="text-2xl font-black text-petroleo"><?= $totalPasajeros ?></p>
            </div>
            <div>
                <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Responsable</p>
                <p class="text-sm font-semibold text-petroleo"><?= $nombre ?></p>
            </div>
        </div>
    </div>

    <!-- Recaudación -->
    <div class="bg-gradient-to-br from-turquesa-dark to-turquesa rounded-xl p-6 text-white">
        <p class="text-xs font-bold uppercase tracking-widest text-white/60 mb-2">Recaudación Total</p>
        <p class="text-4xl font-black">$<?= number_format($totalRecaudado, 2) ?></p>
        <div class="mt-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-white/70">Saldo Pendiente</span>
                <span class="font-bold">$<?= number_format($totalPendiente, 2) ?></span>
            </div>
            <?php $total = $totalRecaudado + $totalPendiente; $pct = $total > 0 ? round(($totalRecaudado / $total) * 100) : 0; ?>
            <div class="w-full bg-white/20 rounded-full h-2 mt-2">
                <div class="bg-white rounded-full h-2 transition-all" style="width: <?= $pct ?>%"></div>
            </div>
            <p class="text-right text-xs text-white/70 mt-1"><?= $pct ?>%</p>
        </div>
    </div>

    <!-- Acción Rápida -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 flex flex-col items-center justify-center text-center">
        <span class="material-symbols-outlined text-4xl text-turquesa mb-3">payments</span>
        <h3 class="text-lg font-black text-petroleo mb-2">Acción Rápida</h3>
        <p class="text-xs text-petroleo/50 mb-4">Registra pagos de cuotas individuales o grupales.</p>
        <a href="<?= Router::url('/client/payments/upload') ?>" class="bg-petroleo text-white font-bold px-6 py-3 rounded-xl hover:bg-petroleo-light transition-all flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-lg">receipt</span>
            Registrar Pago
        </a>
    </div>
</div>

<!-- Contratos del Grupo (Vinculación) -->
<div class="bg-white rounded-xl p-8 border border-petroleo/5 shadow-sm mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-black text-petroleo flex items-center gap-2">
            <span class="material-symbols-outlined text-turquesa">description</span>
            Contratos Individuales del Grupo
        </h2>
        <span class="text-sm text-petroleo/40"><?= count($contratos) ?> contratos firmados</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-xs uppercase tracking-widest text-petroleo/40 border-b border-petroleo/5">
                    <th class="text-left pb-3">Contrato</th>
                    <th class="text-left pb-3">Titular (Padre/Madre)</th>
                    <th class="text-left pb-3">Pasajeros</th>
                    <th class="text-right pb-3">Monto Total</th>
                    <th class="text-center pb-3">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contratos)): ?>
                <?php foreach ($contratos as $c): ?>
                <tr class="border-b border-petroleo/5 hover:bg-humo/50 transition-all">
                    <td class="py-4">
                        <span class="font-bold text-sm text-petroleo"><?= htmlspecialchars($c['numero_contrato'] ?? '') ?></span>
                    </td>
                    <td class="py-4">
                        <p class="text-sm font-medium"><?= htmlspecialchars($c['titular_nombre'] ?? $c['cliente_nombre'] ?? '') ?></p>
                        <p class="text-xs text-petroleo/40">DNI: <?= htmlspecialchars($c['titular_documento'] ?? '') ?></p>
                    </td>
                    <td class="py-4">
                        <?php $nPax = count($c['pasajeros'] ?? []); ?>
                        <span class="text-sm"><?= $nPax ?> pasajero<?= $nPax > 1 ? 's' : '' ?></span>
                    </td>
                    <td class="py-4 text-right font-bold text-sm">$<?= number_format($c['valor_total'] ?? 0, 2) ?></td>
                    <td class="py-4 text-center">
                        <span class="badge-<?= $c['estado'] ?? 'activo' ?> px-3 py-1 rounded-full text-[10px] font-bold uppercase"><?= strtoupper($c['estado'] ?? 'activo') ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="5" class="py-8 text-center text-petroleo/30">No hay contratos registrados</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Cronograma de Pagos -->
<div class="bg-white rounded-xl p-8 border border-petroleo/5 shadow-sm">
    <h2 class="text-xl font-black text-petroleo flex items-center gap-2 mb-6">
        <span class="material-symbols-outlined text-turquesa">event_note</span>
        Cronograma de Cuotas Global
    </h2>
    <table class="w-full">
        <thead>
            <tr class="text-xs uppercase tracking-widest text-petroleo/40 border-b border-petroleo/5">
                <th class="text-left pb-3">Cuota</th>
                <th class="text-left pb-3">Vencimiento</th>
                <th class="text-right pb-3">Monto</th>
                <th class="text-center pb-3">Estado</th>
                <th class="text-right pb-3">Progreso</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pagos)): ?>
            <?php $i = 1; foreach ($pagos as $pago): ?>
            <tr class="border-b border-petroleo/5">
                <td class="py-4 font-bold text-lg text-petroleo">Cuota <?= str_pad($i++, 2, '0', STR_PAD_LEFT) ?></td>
                <td class="py-4 text-sm text-petroleo/60"><?= date('d M, Y', strtotime($pago['fecha_vencimiento'] ?? 'now')) ?></td>
                <td class="py-4 text-right font-bold text-sm">$<?= number_format($pago['monto'] ?? 0, 2) ?></td>
                <td class="py-4 text-center">
                    <span class="badge-<?= $pago['estado'] ?? 'pendiente' ?> px-3 py-1 rounded-full text-[10px] font-bold uppercase"><?= ucfirst($pago['estado'] ?? 'pendiente') ?></span>
                </td>
                <td class="py-4">
                    <div class="progress-bar w-full max-w-[120px] ml-auto">
                        <div class="fill" style="width: <?= $pago['estado'] === 'aprobado' ? '100' : ($pago['estado'] === 'pendiente' ? '50' : '0') ?>%"></div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="5" class="py-8 text-center text-petroleo/30">Sin pagos programados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
