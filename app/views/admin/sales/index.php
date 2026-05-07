<!-- admin/sales/index.php -->
<?php
$grupos = $grupos ?? [];
$stats = $stats ?? ['total_grupos' => 0, 'familiares' => 0, 'colegios' => 0, 'total_pasajeros' => 0, 'total_recaudado' => 0, 'saldo_pendiente' => 0];
$chartData = $chartData ?? [];
$filtro = $filtro ?? null;

// Preparar datos para el gráfico
$labels = array_column($chartData, 'mes');
$pagado = array_column($chartData, 'pagado');
$pendiente = array_column($chartData, 'pendiente');
?>

<!-- Cabecera -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-petroleo">Gestión de Ventas</h1>
        <p class="text-xs text-petroleo/60 mt-1">Control de grupos familiares y colegios</p>
    </div>
    <a href="<?= Router::url('/admin/sales/create') ?>" class="flex items-center gap-2 bg-turquesa hover:bg-turquesa-dark text-white px-4 py-2 text-sm rounded-lg font-bold transition-all shadow border border-petroleo/10">
        <span class="material-symbols-outlined text-[18px]">add_circle</span>
        Nuevo Grupo
    </a>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 mb-6">
    <div class="bg-white rounded-lg p-3 border border-petroleo/5 shadow-sm">
        <div class="flex justify-between items-start mb-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-turquesa-dark">Total Grupos</p>
            <span class="material-symbols-outlined text-turquesa text-[16px]">groups</span>
        </div>
        <p class="text-xl font-black text-petroleo leading-none"><?= $stats['total_grupos'] ?></p>
        <p class="text-[10px] text-petroleo/50 mt-1.5 flex items-center gap-2">
            <span class="font-bold flex items-center gap-0.5"><span class="material-symbols-outlined text-[10px]">family_restroom</span> <?= $stats['familiares'] ?></span>
            <span class="font-bold flex items-center gap-0.5"><span class="material-symbols-outlined text-[10px]">school</span> <?= $stats['colegios'] ?></span>
        </p>
    </div>
    <div class="bg-white rounded-lg p-3 border border-petroleo/5 shadow-sm">
        <div class="flex justify-between items-start mb-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-turquesa-dark">Total Pasajeros</p>
            <span class="material-symbols-outlined text-turquesa text-[16px]">person</span>
        </div>
        <p class="text-xl font-black text-petroleo leading-none"><?= $stats['total_pasajeros'] ?></p>
        <p class="text-[10px] text-petroleo/50 mt-1.5">En todos los grupos</p>
    </div>
    <div class="bg-white rounded-lg p-3 border border-petroleo/5 shadow-sm">
        <div class="flex justify-between items-start mb-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Recaudado</p>
            <span class="material-symbols-outlined text-emerald-500 text-[16px]">savings</span>
        </div>
        <p class="text-xl font-black text-emerald-600 leading-none">$<?= number_format($stats['total_recaudado'], 2) ?></p>
        <p class="text-[10px] text-petroleo/50 mt-1.5">Pagos aprobados</p>
    </div>
    <div class="bg-white rounded-lg p-3 border border-petroleo/5 shadow-sm border-l-4 border-l-amber-400">
        <div class="flex justify-between items-start mb-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-amber-600">Por Cobrar</p>
            <span class="material-symbols-outlined text-amber-500 text-[16px]">warning</span>
        </div>
        <p class="text-xl font-black text-amber-600 leading-none">$<?= number_format($stats['saldo_pendiente'], 2) ?></p>
        <p class="text-[10px] text-petroleo/50 mt-1.5">Saldos pendientes o en cuotas</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
    <!-- Lista de Grupos -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
        
        <!-- Filtros y Título -->
        <div class="p-3 sm:p-4 border-b border-petroleo/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-superficie/30">
            <h2 class="text-base sm:text-lg font-black text-petroleo flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-turquesa">list_alt</span>
                Directorio
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="<?= Router::url('/admin/sales') ?>" class="px-3 py-1.5 rounded text-xs font-bold transition-all <?= !$filtro ? 'bg-petroleo text-white shadow' : 'bg-white text-petroleo border border-petroleo/10 hover:bg-superficie' ?>">
                    Todos
                </a>
                <a href="<?= Router::url('/admin/sales?tipo=familiar') ?>" class="px-3 py-1.5 rounded text-xs font-bold transition-all flex items-center gap-1 <?= $filtro === 'familiar' ? 'bg-petroleo text-white shadow' : 'bg-white text-petroleo border border-petroleo/10 hover:bg-superficie' ?>">
                    <span class="material-symbols-outlined text-[14px]">family_restroom</span>Familiar
                </a>
                <a href="<?= Router::url('/admin/sales?tipo=colegio') ?>" class="px-3 py-1.5 rounded text-xs font-bold transition-all flex items-center gap-1 <?= $filtro === 'colegio' ? 'bg-petroleo text-white shadow' : 'bg-white text-petroleo border border-petroleo/10 hover:bg-superficie' ?>">
                    <span class="material-symbols-outlined text-[14px]">school</span>Colegio
                </a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] text-petroleo/40 uppercase tracking-widest bg-petroleo/5 border-b border-petroleo/10">
                        <th class="px-3 py-2 font-bold">Grupo / Destino</th>
                        <th class="px-3 py-2 font-bold text-center">Tipo</th>
                        <th class="px-3 py-2 font-bold text-center">Pasajeros</th>
                        <th class="px-3 py-2 font-bold text-right">Valor Total</th>
                        <th class="px-3 py-2 font-bold text-center">Estado Pago</th>
                        <th class="px-3 py-2 font-bold text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-petroleo/5 text-xs">
                    <?php if (empty($grupos)): ?>
                        <tr><td colspan="6" class="p-6 text-center text-petroleo/40">No se encontraron grupos.</td></tr>
                    <?php else: ?>
                        <?php foreach ($grupos as $grupo): 
                            $esColegio = $grupo['tipo'] === 'colegio';
                            $iconoTipo = $esColegio ? 'school' : 'family_restroom';
                            $colorTipo = $esColegio ? 'text-indigo-600 bg-indigo-50' : 'text-orange-600 bg-orange-50';
                            
                            $pagado = (float)($grupo['total_pagado'] ?? 0);
                            $total = (float)($grupo['valor_total'] ?? 0);
                            $porcentaje = $total > 0 ? min(100, round(($pagado / $total) * 100)) : 0;
                            
                            $estadoPagoStr = "Pendiente";
                            $estadoPagoColor = "text-amber-600 bg-amber-50";
                            $estadoPagoIcon = "⏳";
                            
                            if ($porcentaje >= 100) {
                                $estadoPagoStr = "Pagado";
                                $estadoPagoColor = "text-emerald-600 bg-emerald-50";
                                $estadoPagoIcon = "✔️";
                            } elseif ($porcentaje > 0) {
                                $estadoPagoStr = "En Cuotas ({$porcentaje}%)";
                                $estadoPagoColor = "text-blue-600 bg-blue-50";
                                $estadoPagoIcon = "📆";
                            }
                        ?>
                        <tr class="hover:bg-humo/50 transition-colors group cursor-pointer" onclick="window.location='<?= Router::url('/admin/sales/' . $grupo['id']) ?>'">
                            <td class="px-3 py-2">
                                <p class="font-bold text-petroleo group-hover:text-turquesa-dark transition-colors"><?= htmlspecialchars($grupo['nombre']) ?></p>
                                <p class="text-[10px] text-petroleo/60 flex items-center gap-1 mt-0.5">
                                    <span class="material-symbols-outlined text-[12px]">location_on</span>
                                    <?= htmlspecialchars($grupo['destino']) ?>
                                    <?php if($grupo['fecha_viaje']): ?>
                                        &bull; <span class="material-symbols-outlined text-[12px] ml-1">calendar_month</span> <?= date('d M Y', strtotime($grupo['fecha_viaje'])) ?>
                                    <?php endif; ?>
                                </p>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?= $colorTipo ?>">
                                    <span class="material-symbols-outlined text-[12px]"><?= $iconoTipo ?></span>
                                    <?= $esColegio ? 'Colegio' : 'Familiar' ?>
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-petroleo/40 text-[16px]">groups</span>
                                    <span class="font-bold"><?= $grupo['total_pasajeros'] ?? 0 ?></span>
                                    <?php if($esColegio && ($grupo['total_contratos'] ?? 0) > 0): ?>
                                        <span class="text-[9px] text-petroleo/40 bg-petroleo/5 px-1.5 rounded-full ml-1" title="Contratos"><?= $grupo['total_contratos'] ?>📄</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-right font-bold text-petroleo">
                                $<?= number_format($total, 2) ?>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold <?= $estadoPagoColor ?>">
                                    <?= $estadoPagoIcon ?> <?= $estadoPagoStr ?>
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <a href="<?= Router::url('/admin/sales/' . $grupo['id']) ?>" class="w-7 h-7 rounded-full bg-superficie text-petroleo flex items-center justify-center hover:bg-turquesa hover:text-white transition-colors ml-auto">
                                    <span class="material-symbols-outlined text-xs">arrow_forward_ios</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="bg-white rounded-xl p-6 border border-petroleo/5 shadow-sm self-start sticky top-24">
        <h3 class="text-lg font-black text-petroleo mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-turquesa">bar_chart</span>
            Cobranzas x Mes
        </h3>
        
        <?php if(empty($chartData)): ?>
            <div class="h-[250px] flex items-center justify-center bg-superficie/30 rounded-lg border border-dashed border-petroleo/10">
                <p class="text-sm text-petroleo/40 font-medium">No hay datos suficientes</p>
            </div>
        <?php else: ?>
            <canvas id="cobranzasChart" class="w-full h-[250px]"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('cobranzasChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($labels) ?>,
                        datasets: [
                            {
                                label: 'Pagado',
                                data: <?= json_encode($pagado) ?>,
                                backgroundColor: '#10B981', // emerald-500
                                borderRadius: 4,
                            },
                            {
                                label: 'Pendiente',
                                data: <?= json_encode($pendiente) ?>,
                                backgroundColor: '#F59E0B', // amber-500
                                borderRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 6 } }
                        },
                        scales: {
                            x: { stacked: true, grid: { display: false } },
                            y: { stacked: true, border: { display: false } }
                        }
                    }
                });
            });
            </script>
        <?php endif; ?>
    </div>
</div>
