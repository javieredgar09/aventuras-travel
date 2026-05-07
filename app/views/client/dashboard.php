<?php
/**
 * Vista: Cliente - Dashboard Principal — Aventuras Travel Pucallpa
 * Rediseño premium con imágenes dinámicas por destino
 */
require_once __DIR__ . '/../../helpers/DestinationHelper.php';

$user       = $data['user'] ?? ($_SESSION['user'] ?? []);
$contrato   = $contrato ?? $data['contrato'] ?? null;
$grupo      = $grupo ?? $data['grupo'] ?? null;

$nombre      = htmlspecialchars(trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')) ?: 'Cliente');
$codigo      = htmlspecialchars($contrato['codigo'] ?? '');
$destino     = htmlspecialchars($contrato['destino'] ?? $grupo['destino'] ?? 'Tu Destino');
$grupoNombre = htmlspecialchars($contrato['grupo_nombre'] ?? $grupo['nombre'] ?? '');

$valor_total  = (float)($contrato['valor_total'] ?? 0);
$total_pagado = (float)($contrato['total_pagado'] ?? 0);
$saldo        = isset($contrato['saldo']) ? (float)$contrato['saldo'] : ($valor_total - $total_pagado);
$pct          = $valor_total > 0 ? min(100, round($total_pagado / $valor_total * 100)) : 0;

if (!function_exists('fmt_money')) {
    function fmt_money(float $a): string { return '$' . number_format($a, 2); }
}

$daysUntil = null;
if (!empty($contrato['fecha_salida'])) {
    $dep = new DateTime($contrato['fecha_salida']);
    $now = new DateTime();
    $diff  = $now->diff($dep);
    $daysUntil = $diff->invert ? 0 : $diff->days;
}

// Imagen dinámica del destino vía helper centralizado
$heroImg = DestinationHelper::getHeroImage($destino);
$destIcon = DestinationHelper::getIcon($destino);
$destMaterialIcon = DestinationHelper::getMaterialIcon($destino);
$accentColor = DestinationHelper::getAccentColor($destino);

$services = $grupo['servicios'] ?? ($contrato['servicios'] ?? []);
?>

<!-- HERO BANNER — Imagen dinámica del destino -->
<div class="relative h-72 sm:h-80 md:h-[360px] overflow-hidden rounded-2xl shadow-2xl group mb-6">
    <img src="<?= htmlspecialchars($heroImg) ?>" alt="<?= $destino ?>"
         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="eager">
    <!-- Gradientes fuertes -->
    <div class="absolute inset-0 bg-gradient-to-r from-petroleo-dark/95 via-petroleo/70 to-turquesa-dark/40"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-transparent to-transparent"></div>
    <!-- Glow decorativo -->
    <div class="absolute top-0 right-0 w-80 h-80 rounded-full blur-3xl -translate-y-1/3 translate-x-1/4 pointer-events-none" style="background:<?= $accentColor ?>33;"></div>
    <div class="absolute bottom-0 left-0 w-60 h-60 bg-coral/15 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4 pointer-events-none"></div>

    <!-- Contenido del hero -->
    <div class="absolute bottom-0 left-0 right-0 px-6 sm:px-8 pb-8 sm:pb-10">
        <div class="max-w-7xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-xl text-white px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest mb-4 border border-white/20 shadow-lg">
                <span class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse shadow-lg shadow-emerald-400/50"></span>
                Viaje Activo
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-white leading-tight drop-shadow-2xl mb-2">
                <span class="text-3xl sm:text-4xl mr-2"><?= $destIcon ?></span> <?= $destino ?>
            </h1>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-white/90 text-sm sm:text-base drop-shadow-lg">
                <?php if ($grupoNombre): ?>
                    <span class="flex items-center gap-1.5 font-semibold bg-white/10 backdrop-blur px-3 py-1 rounded-lg">
                        <span class="material-symbols-outlined text-lg text-turquesa-light">groups</span><?= $grupoNombre ?>
                    </span>
                <?php endif; ?>
                <?php if ($codigo): ?>
                    <span class="flex items-center gap-1.5 font-semibold bg-white/10 backdrop-blur px-3 py-1 rounded-lg">
                        <span class="material-symbols-outlined text-lg text-coral">confirmation_number</span><?= $codigo ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($contrato['fecha_salida'])): ?>
                    <span class="flex items-center gap-1.5 font-semibold bg-white/10 backdrop-blur px-3 py-1 rounded-lg">
                        <span class="material-symbols-outlined text-lg text-gold">flight_takeoff</span><?= htmlspecialchars($contrato['fecha_salida']) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Countdown -->
    <?php if ($daysUntil !== null && $daysUntil > 0): ?>
    <div class="absolute top-5 right-5 sm:top-6 sm:right-6">
        <div class="bg-gradient-to-br from-coral to-gold backdrop-blur-xl rounded-2xl px-5 py-4 text-center shadow-2xl shadow-coral/50 transform hover:scale-110 transition-transform duration-300 border border-white/20">
            <div class="text-3xl sm:text-4xl font-black text-white drop-shadow-lg leading-none"><?= $daysUntil ?></div>
            <div class="text-[10px] uppercase tracking-widest text-white/90 font-black mt-1">Días</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- QUICK STATS -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <!-- Saldo -->
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-5 text-white shadow-lg shadow-amber-500/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur">
                <span class="material-symbols-outlined text-white text-xl">account_balance_wallet</span>
            </div>
        </div>
        <div class="text-2xl font-black"><?= fmt_money($saldo) ?></div>
        <div class="text-xs text-white/70 font-semibold mt-1">Saldo pendiente</div>
    </div>
    <!-- Pagado -->
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur">
                <span class="material-symbols-outlined text-white text-xl">paid</span>
            </div>
        </div>
        <div class="text-2xl font-black"><?= fmt_money($total_pagado) ?></div>
        <div class="text-xs text-white/70 font-semibold mt-1"><?= $pct ?>% completado</div>
        <div class="h-1.5 bg-white/20 rounded-full overflow-hidden mt-2">
            <div class="h-full bg-white rounded-full transition-all" style="width:<?= $pct ?>%"></div>
        </div>
    </div>
    <!-- Estado -->
    <div class="bg-gradient-to-br from-turquesa-dark to-turquesa rounded-2xl p-5 text-white shadow-lg shadow-turquesa/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur">
                <span class="material-symbols-outlined text-white text-xl">fact_check</span>
            </div>
        </div>
        <div class="text-lg font-black"><?= htmlspecialchars(ucfirst($contrato['estado'] ?? 'Activo')) ?></div>
        <div class="text-xs text-white/70 font-semibold mt-1">Estado contrato</div>
    </div>
    <!-- Vuelo -->
    <div class="bg-gradient-to-br from-petroleo to-petroleo-light rounded-2xl p-5 text-white shadow-lg shadow-petroleo/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur">
                <span class="material-symbols-outlined text-white text-xl">flight</span>
            </div>
        </div>
        <?php $vuelo = !empty($contrato['vuelos']) ? $contrato['vuelos'][0] : null; ?>
        <?php if ($vuelo): ?>
        <div class="text-sm font-black truncate"><?= htmlspecialchars($vuelo['origen'] ?? '-') ?> → <?= htmlspecialchars($vuelo['destino'] ?? '-') ?></div>
        <?php else: ?>
        <div class="text-sm text-white/60">Sin vuelo aún</div>
        <?php endif; ?>
        <div class="text-xs text-white/70 font-semibold mt-1">Vuelo principal</div>
    </div>
</div>

<!-- MAIN GRID -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- LEFT: Pasajeros + Servicios -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Pasajeros -->
        <?php $pasajeros = $contrato['pasajeros'] ?? []; ?>
        <?php if (!empty($pasajeros)): ?>
        <div class="bg-white rounded-2xl border border-turquesa/10 shadow-lg shadow-turquesa/5 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-turquesa-dark/5 to-transparent border-b border-turquesa/10">
                <h2 class="font-black text-petroleo flex items-center gap-2 text-lg">
                    <span class="material-symbols-outlined text-turquesa text-2xl" style="font-variation-settings:'FILL' 1">group</span>
                    Pasajeros
                </h2>
                <span class="bg-turquesa/15 text-turquesa-dark text-xs font-black px-3 py-1.5 rounded-full"><?= count($pasajeros) ?> personas</span>
            </div>
            <div class="divide-y divide-petroleo/5">
                <?php foreach ($pasajeros as $i => $p): 
                    $colors = ['bg-turquesa/15 text-turquesa-dark','bg-coral/15 text-coral-dark','bg-gold/15 text-gold-dark','bg-emerald-100 text-emerald-700','bg-violet-100 text-violet-700'];
                    $colorClass = $colors[$i % count($colors)];
                ?>
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-superficie transition-colors">
                    <div class="w-11 h-11 rounded-full <?= $colorClass ?> flex items-center justify-center font-black text-sm shrink-0 ring-2 ring-white shadow">
                        <?= strtoupper(substr($p['nombre'] ?? 'P', 0, 1) . substr($p['apellido'] ?? '', 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-petroleo"><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></p>
                        <p class="text-xs text-petroleo/50"><?= htmlspecialchars($p['tipo'] ?? 'Pasajero') ?><?= !empty($p['dni']) ? ' · DNI: ' . htmlspecialchars($p['dni']) : '' ?></p>
                    </div>
                    <span class="material-symbols-outlined text-petroleo/15 text-xl">person</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Servicios -->
        <?php if (!empty($services)): ?>
        <div class="bg-white rounded-2xl border border-turquesa/10 shadow-lg shadow-turquesa/5 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-turquesa-dark/5 to-transparent border-b border-turquesa/10">
                <h2 class="font-black text-petroleo flex items-center gap-2 text-lg">
                    <span class="material-symbols-outlined text-turquesa text-2xl" style="font-variation-settings:'FILL' 1">travel_explore</span>
                    Itinerario de Servicios
                </h2>
            </div>
            <div class="divide-y divide-petroleo/5">
                <?php foreach ($services as $s): 
                    $det = json_decode($s['detalle_json'] ?? $s['detalles_json'] ?? '{}', true);
                    $titulo = $det['titulo'] ?? ($s['nombre'] ?? $s['servicio_tipo'] ?? 'Servicio');
                    $desc = $det['descripcion'] ?? ($det['itinerario'][0]['descripcion'] ?? '');
                    $tipo = $s['tipo'] ?? $s['servicio_tipo'] ?? '';
                    $icon = stripos($tipo,'vuelo') !== false ? 'flight' : (stripos($tipo,'hotel') !== false ? 'hotel' : 'luggage');
                    $iconBg = stripos($tipo,'vuelo') !== false ? 'bg-blue-100 text-blue-600' : (stripos($tipo,'hotel') !== false ? 'bg-purple-100 text-purple-600' : 'bg-turquesa/10 text-turquesa-dark');
                ?>
                <div class="flex items-start gap-4 px-6 py-4 hover:bg-superficie transition-colors">
                    <div class="w-11 h-11 rounded-xl <?= $iconBg ?> flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1"><?= $icon ?></span>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-petroleo"><?= htmlspecialchars($titulo) ?></p>
                        <?php if ($desc): ?>
                        <p class="text-xs text-petroleo/50 mt-0.5 line-clamp-2"><?= htmlspecialchars(mb_strimwidth($desc, 0, 150, '...')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($contrato) && empty($grupo)): ?>
        <div class="bg-gradient-to-br from-superficie to-white rounded-2xl p-12 text-center border border-turquesa/10">
            <span class="material-symbols-outlined text-6xl text-turquesa/25 mb-4 block">luggage</span>
            <h3 class="font-black text-petroleo text-xl mb-2">Sin reservas activas</h3>
            <p class="text-sm text-petroleo/40 mb-6">Contacta a tu asesor para activar tu reserva.</p>
            <a href="<?= Router::url('/asesoria') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl hover:shadow-lg hover:shadow-turquesa/30 transition-all">
                <span class="material-symbols-outlined">support_agent</span>
                Contactar Asesor
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT: Sidebar -->
    <aside class="space-y-5">
        <!-- Pago rápido -->
        <div class="bg-gradient-to-br from-petroleo-dark to-petroleo rounded-2xl p-6 text-white shadow-xl shadow-petroleo/30 border border-white/5">
            <h3 class="font-black mb-1 flex items-center gap-2 text-lg">
                <span class="material-symbols-outlined text-turquesa-light text-2xl" style="font-variation-settings:'FILL' 1">payments</span>
                Pagos
            </h3>
            <p class="text-white/40 text-xs mb-5">Saldo pendiente a liquidar</p>
            <div class="text-4xl font-black mb-1 text-transparent bg-clip-text bg-gradient-to-r from-turquesa-light to-coral"><?= fmt_money($saldo) ?></div>
            <div class="text-xs text-white/30 mb-5">de <?= fmt_money($valor_total) ?> total</div>
            <div class="space-y-3">
                <a href="<?= Router::url('/client/payments') ?>"
                   class="flex items-center justify-center gap-2 w-full py-3.5 bg-gradient-to-r from-turquesa to-turquesa-dark text-white font-bold rounded-xl transition-all text-sm hover:shadow-lg hover:shadow-turquesa/40 active:scale-95">
                    <span class="material-symbols-outlined text-lg">receipt_long</span>
                    Ver mis Pagos
                </a>
                <a href="<?= Router::url('/client/services') ?>"
                   class="flex items-center justify-center gap-2 w-full py-3.5 bg-white/8 hover:bg-white/15 text-white font-bold rounded-xl transition-all text-sm border border-white/10">
                    <span class="material-symbols-outlined text-lg">luggage</span>
                    Mis Servicios
                </a>
            </div>
        </div>

        <!-- Contrato info -->
        <?php if (!empty($contrato)): ?>
        <div class="bg-white rounded-2xl border border-turquesa/10 shadow-lg shadow-turquesa/5 p-6">
            <h3 class="font-black text-petroleo mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa" style="font-variation-settings:'FILL' 1">description</span>
                Mi Contrato
            </h3>
            <div class="space-y-3 text-sm">
                <?php if (!empty($contrato['fecha_salida'])): ?>
                <div class="flex justify-between items-center py-2 border-b border-petroleo/5">
                    <span class="text-petroleo/50 flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-turquesa">flight_takeoff</span>Salida</span>
                    <span class="font-bold text-petroleo"><?= htmlspecialchars($contrato['fecha_salida']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($contrato['fecha_retorno'])): ?>
                <div class="flex justify-between items-center py-2 border-b border-petroleo/5">
                    <span class="text-petroleo/50 flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-turquesa">flight_land</span>Retorno</span>
                    <span class="font-bold text-petroleo"><?= htmlspecialchars($contrato['fecha_retorno']) ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between items-center py-2 border-b border-petroleo/5">
                    <span class="text-petroleo/50">Total</span>
                    <span class="font-black text-petroleo text-base"><?= fmt_money($valor_total) ?></span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-petroleo/50">Pagado</span>
                    <span class="font-bold text-emerald-600"><?= fmt_money($total_pagado) ?></span>
                </div>
                <div class="pt-3 border-t border-petroleo/5">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-petroleo/40">Progreso</span>
                        <span class="text-xs font-black text-turquesa-dark"><?= $pct ?>%</span>
                    </div>
                    <div class="h-2.5 bg-petroleo/8 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-turquesa-dark to-turquesa rounded-full transition-all shadow-sm shadow-turquesa/30" style="width:<?= $pct ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- WhatsApp CTA -->
        <a href="https://wa.me/51976324716?text=Hola%20Aventuras%20Travel%2C%20soy%20<?= urlencode($nombre) ?>%20y%20tengo%20una%20consulta%20sobre%20mi%20viaje." target="_blank"
           class="flex items-center gap-3 w-full py-4 px-5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-green-500/40 hover:shadow-xl active:scale-95">
            <svg class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            <span>Consultar por WhatsApp</span>
        </a>

        <!-- Soporte -->
        <a href="<?= Router::url('/client/soporte') ?>"
           class="flex items-center gap-3 w-full py-3.5 px-5 bg-white hover:bg-superficie text-petroleo font-bold rounded-2xl transition-all border border-petroleo/10 shadow-sm hover:shadow-md">
            <span class="material-symbols-outlined text-turquesa text-xl">support_agent</span>
            <span>Centro de Soporte</span>
        </a>
    </aside>
</div>
