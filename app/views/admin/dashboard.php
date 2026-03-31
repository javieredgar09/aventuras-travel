<!-- ADMIN DASHBOARD – TAILWIND -->
<?php
$stats = $stats ?? ['total_contratos' => 0, 'contratos_activos' => 0, 'total_pasajeros' => 0, 'total_recaudado' => 0, 'saldo_pendiente' => 0];
$recent = $recent ?? [];
$promociones = $promociones ?? [];
?>

<!-- Breadcrumb -->
<div class="mb-8">
    <h1 class="text-3xl font-black text-petroleo">Dashboard Ejecutivo</h1>
</div>
<!-- Chart.js CDN + init -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const createSpark = (canvasId, color) => {
        const el = document.getElementById(canvasId);
        if (!el) return;
        const values = JSON.parse(el.getAttribute('data-values') || '[]');
        const labels = JSON.parse(el.getAttribute('data-labels') || '[]');
        const ctx = el.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    borderColor: color,
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    tension: 0.35,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                elements: { point: { radius: 0 } },
                plugins: { legend: { display: false } },
                scales: { x: { display: false }, y: { display: false } },
                interaction: { mode: 'index', intersect: false }
            }
        });
    };

    createSpark('spark-recaudado', '#4ABED9');
    createSpark('spark-pasajeros', '#4ABED9');
    createSpark('spark-recaudado-2', '#10B981');
    createSpark('spark-pendiente', '#F59E0B');
});
</script>

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm hover:shadow-md transition-all stat-card">
        <div class="stat-top">
            <p class="stat-title text-turquesa-dark">Contratos Activos</p>
            <span class="material-symbols-outlined text-turquesa">description</span>
        </div>
        <div>
            <p class="stat-number"><?= $stats['contratos_activos'] ?></p>
            <div style="height:28px">
                <canvas id="spark-recaudado" class="stat-spark-canvas" data-values='<?= json_encode($spark_recaudado ?? []) ?>' data-labels='<?= json_encode($spark_labels ?? []) ?>'></canvas>
            </div>
            <p class="stat-sub">de <?= $stats['total_contratos'] ?> totales</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm hover:shadow-md transition-all stat-card">
        <div class="stat-top">
            <p class="stat-title text-turquesa-dark">Total Pasajeros</p>
            <span class="material-symbols-outlined text-turquesa">groups</span>
        </div>
        <div>
            <p class="stat-number"><?= $stats['total_pasajeros'] ?></p>
            <div style="height:28px">
                <canvas id="spark-pasajeros" class="stat-spark-canvas" data-values='<?= json_encode($spark_pasajeros ?? []) ?>' data-labels='<?= json_encode($spark_labels ?? []) ?>'></canvas>
            </div>
            <p class="stat-sub">En todos los contratos</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm hover:shadow-md transition-all stat-card">
        <div class="stat-top">
            <p class="stat-title text-emerald-600">Recaudado</p>
            <span class="material-symbols-outlined text-emerald-500">savings</span>
        </div>
        <div>
            <p class="stat-number text-emerald-600">$<?= number_format($stats['total_recaudado'], 2) ?></p>
            <div style="height:28px">
                <canvas id="spark-recaudado-2" class="stat-spark-canvas" data-values='<?= json_encode($spark_recaudado ?? []) ?>' data-labels='<?= json_encode($spark_labels ?? []) ?>'></canvas>
            </div>
            <p class="stat-sub">Total acumulado</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm hover:shadow-md transition-all stat-card border-l-4 border-l-amber-400">
        <div class="stat-top">
            <p class="stat-title text-amber-600">Saldo Pendiente</p>
            <span class="material-symbols-outlined text-amber-500">warning</span>
        </div>
        <div>
            <p class="stat-number text-amber-600">$<?= number_format($stats['saldo_pendiente'], 2) ?></p>
            <div style="height:28px">
                <canvas id="spark-pendiente" class="stat-spark-canvas" data-values='<?= json_encode($spark_recaudado ?? []) ?>' data-labels='<?= json_encode($spark_labels ?? []) ?>'></canvas>
            </div>
            <p class="stat-sub">Por cobrar</p>
        </div>
    </div>
</div>

<!-- Main Grid: Transacciones + Gestión Rápida -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Transacciones Recientes -->
    <div class="lg:col-span-2 bg-white rounded-xl p-8 border border-petroleo/5 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-black text-petroleo flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa">receipt_long</span>
                Transacciones Recientes
            </h2>
            <a href="<?= Router::url('/admin/payments') ?>" class="text-sm text-turquesa-dark font-semibold hover:underline">Ver todas →</a>
        </div>
        <table class="w-full">
            <thead>
                <tr class="text-xs uppercase tracking-widest text-petroleo/40 border-b border-petroleo/5">
                    <th class="text-left pb-3">Contrato</th>
                    <th class="text-left pb-3">Concepto</th>
                    <th class="text-right pb-3">Monto</th>
                    <th class="text-center pb-3">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recent)): ?>
                <?php foreach (array_slice($recent, 0, 6) as $tx): ?>
                <tr class="border-b border-petroleo/5 hover:bg-humo/50 transition-all">
                    <td class="py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-turquesa/10 text-turquesa-dark rounded-lg flex items-center justify-center text-xs font-bold">AV</div>
                            <span class="font-bold text-sm"><?= htmlspecialchars($tx['numero_contrato'] ?? $tx['codigo'] ?? '') ?></span>
                        </div>
                    </td>
                    <td class="py-4 text-sm text-petroleo/60"><?= htmlspecialchars($tx['concepto'] ?? $tx['cliente_nombre'] ?? '') ?></td>
                    <td class="py-4 text-right font-bold text-sm">$<?= number_format($tx['monto'] ?? $tx['valor_total'] ?? 0, 2) ?></td>
                    <td class="py-4 text-center">
                        <?php 
                        $estado = $tx['estado'] ?? 'pendiente';
                        $badgeClass = 'badge-' . $estado;
                        ?>
                        <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?= $badgeClass ?>"><?= strtoupper($estado) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="4" class="py-8 text-center text-petroleo/30">Sin transacciones recientes</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Gestión Rápida -->
    <div class="bg-white rounded-xl p-8 border border-petroleo/5 shadow-sm">
        <h2 class="text-xl font-black text-petroleo flex items-center gap-2 mb-6">
            <span class="material-symbols-outlined text-turquesa">hub</span>
            Gestión Rápida
        </h2>
        <div class="space-y-3">
            <?php
            $quickLinks = [
                ['/admin/sales', 'group_add', 'Ventas', 'Grupos y familiares'],
                ['/admin/payments', 'payments', 'Pagos', 'Validar comprobantes'],
                ['/admin/contracts', 'description', 'Contratos', 'Gestionar contratos'],
                ['/admin/passengers', 'person', 'Pasajeros', 'Lista de pasajeros'],
                ['/admin/promotions', 'campaign', 'Promociones', 'Ofertas activas'],
            ];
            foreach ($quickLinks as $link):
            ?>
            <a href="<?= Router::url($link[0]) ?>" class="flex items-center gap-3 p-3 rounded-xl hover:bg-superficie transition-all group quick-link-compact">
                <div class="w-10 h-10 bg-turquesa/10 text-turquesa-dark rounded-xl flex items-center justify-center group-hover:bg-turquesa group-hover:text-white transition-all">
                    <span class="material-symbols-outlined text-xl"><?= $link[1] ?></span>
                </div>
                <div>
                    <p class="font-bold text-sm text-petroleo"><?= $link[2] ?></p>
                    <p class="text-xs text-petroleo/40"><?= $link[3] ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
