<?php
/**
 * Vista: Dashboard Cliente Familiar — Aventuras Travel Pucallpa
 * Layout compacto 3 columnas: Versiculo+Estado | Saldo+Vuelo+Cuotas | Alojamiento+Pasajeros
 */
require_once __DIR__ . '/../../../helpers/DestinationHelper.php';

$user       = $user ?? ($_SESSION['user'] ?? []);
$contrato   = $contrato ?? null;
$cliente    = $cliente ?? null;
$vuelos     = $vuelos ?? [];
$pasajeros  = $pasajeros ?? [];
$pagos      = $pagos ?? [];
$servicios  = $servicios ?? [];
$vouchers   = $vouchers ?? [];
$pago_completo = $pago_completo ?? false;

$nombre  = htmlspecialchars(trim(($user['nombre'] ?? '')));
$codigo  = htmlspecialchars($contrato['codigo'] ?? '');
$destino = htmlspecialchars($contrato['destino'] ?? 'Tu Destino');
$grupoNombre = htmlspecialchars($contrato['grupo_nombre'] ?? ($contrato['grupo']['nombre'] ?? ''));

$valor_total  = (float)($contrato['valor_total'] ?? 0);
$total_pagado = (float)($contrato['total_pagado'] ?? 0);
$saldo        = $valor_total - $total_pagado;

if (!function_exists('fmoney')) {
    function fmoney(float $a, string $c = 'USD'): string {
        return '$' . number_format($a, 2);
    }
}

$daysUntil = null;
if (!empty($contrato['fecha_salida'])) {
    $dep = new DateTime($contrato['fecha_salida']);
    $now = new DateTime();
    $diff = $now->diff($dep);
    $daysUntil = $diff->invert ? 0 : $diff->days;
}

$vuelo = !empty($vuelos) ? $vuelos[0] : null;
$hotel = null;
foreach ($servicios as $s) {
    if (stripos($s['tipo'] ?? '', 'hotel') !== false || stripos($s['tipo'] ?? '', 'aloj') !== false) { $hotel = $s; break; }
}
if (!$hotel && !empty($servicios)) $hotel = $servicios[0];

// Imagen dinámica del destino vía helper centralizado
$heroImg = DestinationHelper::getHeroImage($destino);
$destIcon = DestinationHelper::getIcon($destino);
$accentColor = DestinationHelper::getAccentColor($destino);
$progreso = $valor_total > 0 ? min(100, round(($total_pagado / $valor_total) * 100)) : 0;
$estado = strtolower($contrato['estado'] ?? 'activo');
$cuotas = $contrato['cuotas'] ?? [];
?>

<?php if ($contrato): ?>

<!-- HERO compacto -->
<section class="relative h-[200px] sm:h-[240px] md:h-[280px] rounded-2xl overflow-hidden mb-5 group">
    <img alt="<?= $destino ?>" fetchpriority="high" loading="eager" decoding="async"
         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
         src="<?= htmlspecialchars($heroImg) ?>">
    <div class="absolute inset-0 bg-gradient-to-t from-[#0a2a2f]/85 via-[#0a2a2f]/30 to-transparent"></div>
    <div class="absolute top-3 left-4">
        <span class="bg-white/15 backdrop-blur-md text-white text-[10px] font-black tracking-widest uppercase px-3 py-1.5 rounded-full border border-white/10">
            <?= $grupoNombre ? htmlspecialchars($grupoNombre) . ' · ' : '' ?><?= $codigo ?>
        </span>
    </div>
    <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 flex flex-col md:flex-row md:items-end justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white tracking-tight leading-snug" style="text-shadow:0 2px 12px rgba(0,0,0,.5)">
                ¡Hola <?= $nombre ?>! Tu viaje a <span class="text-primary-container"><?= $destino ?></span>
                <?php if ($daysUntil !== null && $daysUntil > 0): ?>
                en <span class="text-primary-container"><?= $daysUntil ?> días</span>
                <?php elseif ($daysUntil === 0): ?>
                <span class="text-primary-container">¡es hoy!</span>
                <?php endif; ?>
            </h1>
        </div>
        <div class="flex gap-2 shrink-0">
            <?php if ($pago_completo && !empty($vouchers)): ?>
            <button class="px-4 py-2.5 bg-white text-secondary font-bold rounded-xl shadow-lg hover:bg-surface-container-low transition-all active:scale-95 flex items-center gap-2 text-xs">
                <span class="material-symbols-outlined text-base">download</span> Vouchers
            </button>
            <?php endif; ?>
            <a href="<?= Router::url('/client/services') ?>" class="px-4 py-2.5 bg-primary text-white font-bold rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95 flex items-center gap-2 text-xs">
                <span class="material-symbols-outlined text-base">map</span> Ver Itinerario
            </a>
        </div>
    </div>
</section>

<!-- GRID PRINCIPAL 3 columnas -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

    <!-- COL IZQUIERDA (3/12) -->
    <div class="lg:col-span-3 space-y-4">
        <!-- Versículo -->
        <div class="bg-gradient-to-br from-secondary to-primary p-4 rounded-2xl text-white relative overflow-hidden">
            <?php
            $versiculos = [
                ['texto' => 'No tengas miedo ni te detengas porque el Señor tu Dios te acompañará dondequiera que vayas.', 'cita' => 'Josué 1:9'],
                ['texto' => 'Porque yo sé los planes que tengo para ustedes, planes de bienestar y no de calamidad.', 'cita' => 'Jeremías 29:11'],
                ['texto' => 'Confía en Jehová con todo tu corazón, y no te apoyes en tu propia prudencia.', 'cita' => 'Proverbios 3:5-6'],
                ['texto' => 'Todo lo puedo en Cristo que me fortalece.', 'cita' => 'Filipenses 4:13'],
            ];
            $verso = $versiculos[array_rand($versiculos)];
            ?>
            <span class="material-symbols-outlined text-2xl text-cyan-200/60 mb-1.5 block">auto_stories</span>
            <p class="text-cyan-50 text-xs leading-relaxed italic">"<?= $verso['texto'] ?>"</p>
            <p class="text-right text-white/70 font-bold text-[10px] mt-2">— <?= $verso['cita'] ?></p>
            <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-[60px] opacity-[0.06]">church</span>
        </div>

        <!-- Estado del Contrato -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-outline-variant/10">
            <p class="text-[10px] font-bold uppercase tracking-widest text-outline mb-2">Estado del Contrato</p>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-lg <?= $estado === 'cancelado' ? 'text-red-500' : ($estado === 'pendiente' ? 'text-amber-500' : 'text-green-500') ?>" style="font-variation-settings:'FILL' 1">check_circle</span>
                <span class="text-sm font-bold text-secondary"><?= ucfirst($estado) ?> y Confirmado</span>
            </div>
        </div>

        <!-- Mini imagen destino -->
        <div class="relative h-32 rounded-2xl overflow-hidden shadow-sm">
            <img alt="<?= $destino ?>" class="w-full h-full object-cover" src="<?= htmlspecialchars($heroImg) ?>" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-[#0a2a2f]/50 to-transparent"></div>
            <p class="absolute bottom-2 left-3 text-white text-xs font-bold"><?= $destino ?></p>
        </div>

        <!-- Progreso de pago -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-outline-variant/10">
            <div class="flex justify-between text-[10px] text-outline mb-1">
                <span class="font-bold">Progreso de pago</span>
                <span class="font-black text-primary"><?= $progreso ?>%</span>
            </div>
            <div class="w-full h-1.5 bg-surface-container-high rounded-full overflow-hidden">
                <div class="h-full rounded-full <?= $progreso >= 100 ? 'bg-green-500' : 'bg-gradient-to-r from-primary-container to-primary' ?>" style="width:<?= $progreso ?>%"></div>
            </div>
            <div class="flex justify-between mt-1 text-[9px] text-outline">
                <span>Pagado: <span class="font-bold text-green-600"><?= fmoney($total_pagado) ?></span></span>
                <span>Total: <span class="font-bold text-secondary"><?= fmoney($valor_total) ?></span></span>
            </div>
        </div>
    </div>

    <!-- COL CENTRO (5/12) -->
    <div class="lg:col-span-5 space-y-4">
        <!-- Fila: Saldo + Vuelo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Saldo Pendiente -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-outline-variant/10">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-outline">Saldo Pendiente</p>
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-base">payments</span>
                    </div>
                </div>
                <h2 class="text-2xl font-black text-secondary mb-3"><?= fmoney($saldo) ?></h2>
                <a href="<?= Router::url('/client/payments') ?>" class="block w-full py-2 bg-secondary text-white rounded-xl font-bold text-xs text-center hover:bg-primary transition-all active:scale-95 shadow-md">
                    Pagar Cuota
                </a>
            </div>

            <!-- Próximo Vuelo -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-outline-variant/10">
                <p class="text-[10px] font-bold uppercase tracking-widest text-outline mb-2">Próximo Vuelo</p>
                <?php if ($vuelo): ?>
                <div class="flex items-center justify-between mb-3">
                    <div class="text-center">
                        <p class="text-xl font-black text-secondary"><?= htmlspecialchars(strtoupper(substr($vuelo['origen'] ?? 'PCL', 0, 3))) ?></p>
                        <p class="text-[8px] font-semibold text-outline"><?= htmlspecialchars($vuelo['origen'] ?? '') ?></p>
                    </div>
                    <div class="flex-1 flex flex-col items-center px-2">
                        <div class="w-full h-px bg-outline-variant relative">
                            <span class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-primary bg-white px-1 text-sm">flight_takeoff</span>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-black text-secondary"><?= htmlspecialchars(strtoupper(substr($vuelo['destino'] ?? 'LIM', 0, 3))) ?></p>
                        <p class="text-[8px] font-semibold text-outline"><?= htmlspecialchars($vuelo['destino'] ?? '') ?></p>
                    </div>
                </div>
                <div class="flex justify-between items-center py-1.5 px-2.5 bg-surface-container-low rounded-lg text-[11px]">
                    <span class="flex items-center gap-1 font-bold text-secondary">
                        <span class="material-symbols-outlined text-primary text-xs">calendar_today</span>
                        <?= !empty($vuelo['fecha_salida']) ? date('d M', strtotime($vuelo['fecha_salida'])) : '-' ?>
                    </span>
                    <span class="flex items-center gap-1 font-bold text-secondary">
                        <span class="material-symbols-outlined text-primary text-xs">schedule</span>
                        <?= htmlspecialchars($vuelo['hora_salida'] ?? '--:--') ?>
                    </span>
                </div>
                <?php else: ?>
                <div class="flex flex-col items-center justify-center py-4 text-center">
                    <span class="material-symbols-outlined text-2xl text-outline-variant mb-1">flight</span>
                    <p class="text-xs text-outline">Sin información</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cronograma de Cuotas -->
        <div>
            <h3 class="text-sm font-black text-secondary mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-lg">receipt_long</span> Cronograma de Cuotas
            </h3>
            <?php
            $items = !empty($cuotas) ? $cuotas : $pagos;
            if (!empty($items)): ?>
            <div class="space-y-2">
                <?php foreach ($items as $i => $c):
                    $ec = strtolower($c['estado'] ?? 'pendiente');
                    $ok = in_array($ec, ['aprobado', 'pagado']);
                ?>
                <div class="flex items-center justify-between p-3 bg-white border border-outline-variant/10 rounded-xl hover:shadow-sm transition-all">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg <?= $ok ? 'bg-green-50 text-green-600' : ($ec === 'atrasado' ? 'bg-red-50 text-red-500' : 'bg-amber-50 text-amber-600') ?> flex items-center justify-center">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1"><?= $ok ? 'check_circle' : ($ec === 'atrasado' ? 'error' : 'schedule') ?></span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-secondary"><?= htmlspecialchars($c['concepto'] ?? 'Cuota ' . str_pad($i+1, 2, '0', STR_PAD_LEFT)) ?></p>
                            <p class="text-[10px] text-outline"><?= !empty($c['fecha_vencimiento']) ? date('d M, Y', strtotime($c['fecha_vencimiento'])) : (!empty($c['created_at']) ? date('d M, Y', strtotime($c['created_at'])) : '') ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-secondary"><?= fmoney((float)($c['monto'] ?? 0)) ?></p>
                        <span class="text-[8px] font-black uppercase tracking-wider <?= $ok ? 'text-green-600' : ($ec === 'atrasado' ? 'text-red-500' : 'text-amber-600') ?>"><?= strtoupper($ec) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="bg-white border border-outline-variant/10 rounded-xl p-5 text-center">
                <p class="text-xs text-outline">Sin cuotas programadas.</p>
            </div>
            <?php endif; ?>

            <a href="<?= Router::url('/client/payments') ?>" class="mt-2 flex items-center justify-center gap-1.5 text-primary text-xs font-bold hover:underline">
                <span class="material-symbols-outlined text-sm">receipt_long</span> Ver Comprobante
            </a>
        </div>
    </div>

    <!-- COL DERECHA (4/12) -->
    <div class="lg:col-span-4 space-y-4">
        <!-- Alojamiento con pasajeros -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-outline-variant/10">
            <p class="text-[10px] font-bold uppercase tracking-widest text-outline mb-2">Alojamiento</p>
            <?php if ($hotel): ?>
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-base">hotel</span>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-black text-secondary leading-tight truncate"><?= htmlspecialchars($hotel['nombre'] ?? $hotel['descripcion'] ?? 'Hotel') ?></h4>
                    <p class="text-[10px] text-outline"><?= htmlspecialchars($hotel['tipo'] ?? 'Hospedaje') ?></p>
                </div>
            </div>
            <?php else: ?>
            <p class="text-xs text-outline mb-3">Por confirmar</p>
            <?php endif; ?>

            <?php if (!empty($pasajeros)): ?>
            <div class="space-y-1.5 mt-2">
                <?php foreach (array_slice($pasajeros, 0, 4) as $i => $p):
                    $ini = strtoupper(substr($p['nombre'] ?? '', 0, 1) . substr($p['apellido'] ?? '', 0, 1));
                ?>
                <div class="flex items-center gap-2.5 p-2 bg-surface-container-low rounded-lg">
                    <div class="w-7 h-7 rounded-md <?= $i===0 ? 'bg-primary-container/20 text-primary' : 'bg-secondary-container/20 text-secondary' ?> flex items-center justify-center font-black text-[10px]"><?= $ini ?></div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-bold text-secondary truncate"><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></p>
                        <p class="text-[9px] text-outline"><?= htmlspecialchars($hotel['nombre'] ?? 'hotel') ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Subir Comprobante -->
        <a href="<?= Router::url('/client/payments') ?>" class="flex items-center justify-center gap-2 p-3 bg-primary text-white rounded-2xl font-bold text-xs hover:brightness-110 transition-all active:scale-95 shadow-lg">
            <span class="material-symbols-outlined text-base">upload_file</span> Subir Comprobante
        </a>

        <!-- Pasajeros del Contrato -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-outline-variant/10">
            <p class="text-[10px] font-bold uppercase tracking-widest text-outline mb-2 flex items-center gap-1">
                <span class="material-symbols-outlined text-primary text-sm">group</span> Pasajeros (<?= count($pasajeros) ?>)
            </p>
            <?php if (!empty($pasajeros)): ?>
            <div class="space-y-1.5">
                <?php foreach ($pasajeros as $i => $p):
                    $ini = strtoupper(substr($p['nombre'] ?? '', 0, 1) . substr($p['apellido'] ?? '', 0, 1));
                ?>
                <a href="<?= Router::url('/client/services') ?>" class="flex items-center justify-between p-2.5 bg-surface-container-low rounded-lg hover:bg-surface-container-high transition-colors group">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-md <?= $i===0 ? 'bg-primary-container/20 text-primary' : 'bg-secondary-container/20 text-secondary' ?> flex items-center justify-center font-black text-[10px] shrink-0"><?= $ini ?></div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold text-secondary truncate"><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></p>
                            <p class="text-[9px] text-outline"><?= $i === 0 ? 'Titular' : 'Acompañante' ?></p>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-outline-variant text-sm group-hover:text-primary transition-colors">chevron_right</span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-xs text-outline text-center py-3">Sin pasajeros registrados.</p>
            <?php endif; ?>

            <a href="<?= Router::url('/client/services') ?>" class="mt-2 flex items-center justify-center gap-1 text-primary text-[11px] font-bold hover:underline pt-1">
                <span class="material-symbols-outlined text-sm">explore</span> Ver Servicios
            </a>
        </div>
    </div>
</div>

<?php else: ?>
<div class="max-w-md mx-auto mt-16 bg-white rounded-2xl p-10 text-center shadow-sm border border-outline-variant/10">
    <span class="material-symbols-outlined text-5xl text-primary-container mb-3 block">flight_takeoff</span>
    <h2 class="text-xl font-black text-secondary mb-2">Sin contratos activos</h2>
    <p class="text-outline text-sm">Contacta a tu asesor de viajes para activar tu próxima aventura.</p>
</div>
<?php endif; ?>
