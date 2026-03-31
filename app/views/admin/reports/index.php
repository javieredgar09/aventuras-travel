<!-- ADMIN REPORTS – TAILWIND -->
<?php
$stats = $stats ?? ['total_recaudado' => 0, 'saldo_pendiente' => 0, 'total_contratos' => 0];
$monthlyRevenue = $monthlyRevenue ?? [];
$salesByDest = $salesByDest ?? [];

// Preparar data para Chart.js
$labelsMeses = [];
$dataIngresos = [];
foreach ($monthlyRevenue as $row) {
    if (!isset($row['mes'])) continue;
    $dateObj = DateTime::createFromFormat('Y-m', $row['mes']);
    if ($dateObj) {
        $labelsMeses[] = htmlspecialchars($dateObj->format('M Y'));
        $dataIngresos[] = (float)$row['total'];
    }
}
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="flex justify-between items-start mb-8">
    <div>
        <h1 class="text-3xl font-black text-petroleo">Reportes y Estadísticas</h1>
        <p class="text-sm text-petroleo/60 mt-1">Análisis financiero y métricas de ventas</p>
    </div>
    <div class="flex gap-3 relative" x-data="{ open: false }">
        <button onclick="document.getElementById('exportMenu').classList.toggle('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">download</span> Exportar CSV
        </button>
        <!-- Menú simple (toggle manual en onclick) -->
        <div id="exportMenu" class="absolute right-0 top-12 bg-white rounded-xl shadow-xl border border-petroleo/10 w-48 hidden overflow-hidden z-50">
            <a href="<?= Router::url('/admin/reports/export?tipo=ventas') ?>" class="block px-4 py-3 text-sm font-bold text-petroleo hover:bg-superficie transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500 text-[18px]">luggage</span> Ventas / Grupos
            </a>
            <a href="<?= Router::url('/admin/reports/export?tipo=pagos') ?>" class="block px-4 py-3 text-sm font-bold text-petroleo hover:bg-superficie transition-colors border-t border-petroleo/5 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-500 text-[18px]">payments</span> Pagos Realizados
            </a>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-turquesa-dark to-turquesa rounded-2xl p-6 text-white shadow-lg overflow-hidden relative">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full"></div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-white/70 mb-1">Ingresos Totales (Aprobados)</p>
        <p class="text-4xl font-black">$<?= number_format($stats['total_recaudado'], 2) ?></p>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-amber-500 mb-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">schedule</span> Por Cobrar Global
        </p>
        <p class="text-3xl font-black text-petroleo">$<?= number_format($stats['saldo_pendiente'], 2) ?></p>
        <p class="text-xs text-petroleo/40 mt-1">Saldo pendiente de todas las ventas activas</p>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-500 mb-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">groups</span> Volumen de Grupos
        </p>
        <p class="text-3xl font-black text-petroleo"><?= htmlspecialchars($stats['total_contratos'] ?? 0) ?></p>
        <p class="text-xs text-petroleo/40 mt-1">Total de expedientes activos en el sistema</p>
    </div>
</div>

<!-- Contenedor Inferior: Gráfico y Tabla -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-12">
    <!-- Chart Section -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
        <h2 class="text-lg font-black text-petroleo mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-turquesa">trending_up</span> Ingresos Históricos (Últimos 12 meses)
        </h2>
        <div class="relative h-72 w-full">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Destinos Populares -->
    <div class="bg-white rounded-2xl p-6 border border-petroleo/5 shadow-sm">
        <h2 class="text-lg font-black text-petroleo mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-500">public</span> Top Destinos (Volumen)
        </h2>
        
        <?php if (empty($salesByDest)): ?>
            <p class="text-sm text-petroleo/40">Sin datos registrados.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach($salesByDest as $idx => $d): ?>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 font-bold flex justify-center items-center text-xs">
                            #<?= $idx + 1 ?>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-petroleo text-sm"><?= htmlspecialchars($d['destino']) ?></h3>
                            <p class="text-[10px] uppercase text-petroleo/50 tracking-wider"><?= $d['cantidad'] ?> Grupos</p>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-emerald-600 text-sm">$<?= number_format($d['ingresos'], 2) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Init Chart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labelsMeses) ?>,
                datasets: [{
                    label: 'Ingresos Aprobados (USD)',
                    data: <?= json_encode($dataIngresos) ?>,
                    backgroundColor: 'rgba(2, 169, 163, 0.8)', // Turquesa
                    hoverBackgroundColor: 'rgba(5, 126, 137, 1)', // Petroleo Light
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.03)',
                        },
                        ticks: {
                            font: { family: "'Outfit', sans-serif", size: 10 },
                            callback: function(value) { return '$' + value; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Outfit', sans-serif", size: 10 } }
                    }
                }
            }
        });
    }
});
</script>
