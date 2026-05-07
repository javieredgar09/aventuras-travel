<?php
/**
 * Vista: Dashboard Cliente Grupal (Colegio) - Aventuras Travel Pucallpa
 * Redise&ntilde;o premium con im&aacute;genes din&aacute;micas por destino
 */
require_once __DIR__ . '/../../../helpers/DestinationHelper.php';

$user       = $data['user'] ?? ($_SESSION['user'] ?? []);
$contrato   = $data['contrato'] ?? null;
$grupo      = $contrato['grupo'] ?? ($data['grupo'] ?? null);
$vuelos     = $data['vuelos'] ?? [];
$pasajeros  = $data['pasajeros'] ?? [];
$pagos      = $data['pagos'] ?? [];
$servicios  = $data['servicios'] ?? [];
$vouchers   = $data['vouchers'] ?? [];
$pago_completo = $data['pago_completo'] ?? false;

$nombre     = htmlspecialchars(trim(($user['nombre'] ?? '')));
$codigo     = htmlspecialchars($contrato['codigo'] ?? '');
$destino    = htmlspecialchars($grupo['destino'] ?? $contrato['destino'] ?? 'Tu Destino');
$grupoNombre = htmlspecialchars($grupo['nombre'] ?? '');

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
$heroImg = DestinationHelper::getHeroImage($destino);
$destIcon = DestinationHelper::getIcon($destino);
$accentColor = DestinationHelper::getAccentColor($destino);
$progreso = $valor_total > 0 ? min(100, round(($total_pagado / $valor_total) * 100)) : 0;
?>

<?php if ($contrato): ?>

<!-- HERO -->
<section class="relative h-[220px] sm:h-[260px] md:h-[320px] rounded-2xl overflow-hidden mb-6 group shadow-2xl">
    <img alt="<?= $destino ?>" fetchpriority="high" loading="eager"
         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
         src="<?= htmlspecialchars($heroImg) ?>">
    <div class="absolute inset-0 bg-gradient-to-r from-petroleo-dark/95 via-petroleo/65 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark/90 via-transparent to-transparent"></div>
    <div class="absolute bottom-0 left-0 w-60 h-60 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4 pointer-events-none" style="background:<?= $accentColor ?>22;"></div>

    <div class="absolute top-4 left-5">
        <span class="bg-white/10 backdrop-blur-xl text-white text-[10px] font-black tracking-widest uppercase px-4 py-2 rounded-full border border-white/20 shadow-lg inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-sm text-turquesa-light">school</span>
            <?= $grupoNombre ?>
        </span>
    </div>

    <?php if ($daysUntil !== null && $daysUntil > 0): ?>
    <div class="absolute top-4 right-5">
        <div class="bg-gradient-to-br from-coral to-gold rounded-2xl px-5 py-3 text-center shadow-2xl shadow-coral/40 border border-white/20 hover:scale-110 transition-transform">
            <div class="text-3xl font-black text-white leading-none"><?= $daysUntil ?></div>
            <div class="text-[9px] uppercase tracking-widest text-white/80 font-black mt-0.5">D&iacute;as</div>
        </div>
    </div>
    <?php endif; ?>

    <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-7">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white leading-tight drop-shadow-2xl mb-2">
            <span class="text-2xl sm:text-3xl mr-1"><?= $destIcon ?></span> <?= $destino ?>
        </h1>
        <div class="flex flex-wrap gap-3 text-sm">
            <span class="flex items-center gap-1.5 text-white/90 font-semibold bg-white/10 backdrop-blur px-3 py-1 rounded-lg">
                <span class="material-symbols-outlined text-base text-coral">confirmation_number</span><?= $codigo ?>
            </span>
            <?php if (!empty($contrato['fecha_salida'])): ?>
            <span class="flex items-center gap-1.5 text-white/90 font-semibold bg-white/10 backdrop-blur px-3 py-1 rounded-lg">
                <span class="material-symbols-outlined text-base text-gold">calendar_month</span>
                <?= htmlspecialchars($contrato['fecha_salida']) ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-5 text-white shadow-lg shadow-amber-500/20">
        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-white">account_balance_wallet</span>
        </div>
        <div class="text-2xl font-black"><?= fmoney($saldo) ?></div>
        <div class="text-xs text-white/70 font-semibold mt-1">Saldo pendiente</div>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/20">
        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-white">paid</span>
        </div>
        <div class="text-2xl font-black"><?= fmoney($total_pagado) ?></div>
        <div class="text-xs text-white/70 font-semibold mt-1"><?= $progreso ?>% pagado</div>
    </div>
    <div class="bg-gradient-to-br from-turquesa-dark to-turquesa rounded-2xl p-5 text-white shadow-lg shadow-turquesa/20">
        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-white">group</span>
        </div>
        <div class="text-2xl font-black"><?= count($pasajeros) ?></div>
        <div class="text-xs text-white/70 font-semibold mt-1">Pasajeros</div>
    </div>
    <div class="bg-gradient-to-br from-petroleo to-petroleo-light rounded-2xl p-5 text-white shadow-lg shadow-petroleo/20">
        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-white">flight</span>
        </div>
        <?php if ($vuelo): ?>
        <div class="text-sm font-black truncate"><?= htmlspecialchars($vuelo['origen'] ?? '') ?> &rarr; <?= htmlspecialchars($vuelo['destino'] ?? '') ?></div>
        <?php else: ?>
        <div class="text-sm text-white/60">Sin vuelo</div>
        <?php endif; ?>
        <div class="text-xs text-white/70 font-semibold mt-1">Vuelo</div>
    </div>
</div>

<!-- CONTENT GRID -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- LEFT: Pasajeros -->
    <div class="lg:col-span-2 space-y-5">
        <?php if (!empty($pasajeros)): ?>
        <div class="bg-white rounded-2xl border border-turquesa/10 shadow-lg overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-turquesa-dark/5 to-transparent border-b border-turquesa/10 flex items-center justify-between">
                <h2 class="font-black text-petroleo flex items-center gap-2 text-lg">
                    <span class="material-symbols-outlined text-turquesa text-xl" style="font-variation-settings:'FILL' 1">group</span>
                    Pasajeros del Grupo
                </h2>
                <span class="bg-turquesa/15 text-turquesa-dark text-xs font-black px-3 py-1 rounded-full"><?= count($pasajeros) ?></span>
            </div>
            <div class="divide-y divide-petroleo/5">
                <?php 
                $colors = ['bg-turquesa/15 text-turquesa-dark','bg-coral/15 text-coral-dark','bg-gold/15 text-gold-dark','bg-emerald-100 text-emerald-700','bg-violet-100 text-violet-700','bg-blue-100 text-blue-700','bg-pink-100 text-pink-700'];
                foreach ($pasajeros as $i => $p): ?>
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-superficie transition-colors">
                    <div class="w-10 h-10 rounded-full <?= $colors[$i % count($colors)] ?> flex items-center justify-center font-black text-xs shrink-0 ring-2 ring-white shadow">
                        <?= strtoupper(substr($p['nombre'] ?? 'P', 0, 1) . substr($p['apellido'] ?? '', 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-petroleo text-sm"><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></p>
                        <p class="text-[11px] text-petroleo/40"><?= htmlspecialchars($p['tipo'] ?? 'Pasajero') ?><?= !empty($p['dni']) ? ' &middot; DNI: ' . htmlspecialchars($p['dni']) : '' ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Servicios -->
        <?php if (!empty($servicios)): ?>
        <div class="bg-white rounded-2xl border border-turquesa/10 shadow-lg overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-turquesa-dark/5 to-transparent border-b border-turquesa/10">
                <h2 class="font-black text-petroleo flex items-center gap-2 text-lg">
                    <span class="material-symbols-outlined text-turquesa text-xl" style="font-variation-settings:'FILL' 1">travel_explore</span>
                    Servicios del Viaje
                </h2>
            </div>
            <div class="divide-y divide-petroleo/5">
                <?php foreach ($servicios as $s): 
                    $det = json_decode($s['detalle_json'] ?? $s['detalles_json'] ?? '{}', true);
                    $titulo = $det['titulo'] ?? ($s['nombre'] ?? $s['servicio_tipo'] ?? 'Servicio');
                    $tipo = $s['tipo'] ?? $s['servicio_tipo'] ?? '';
                    $icon = stripos($tipo,'vuelo') !== false ? 'flight' : (stripos($tipo,'hotel') !== false ? 'hotel' : 'luggage');
                    $iconBg = stripos($tipo,'vuelo') !== false ? 'bg-blue-100 text-blue-600' : (stripos($tipo,'hotel') !== false ? 'bg-purple-100 text-purple-600' : 'bg-turquesa/10 text-turquesa-dark');
                ?>
                <div class="flex items-center gap-4 px-5 py-4 hover:bg-superficie transition-colors">
                    <div class="w-10 h-10 rounded-xl <?= $iconBg ?> flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1"><?= $icon ?></span>
                    </div>
                    <div><p class="font-bold text-petroleo text-sm"><?= htmlspecialchars($titulo) ?></p></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT: Sidebar -->
    <aside class="space-y-5">
        <!-- Pago -->
        <div class="bg-gradient-to-br from-petroleo-dark to-petroleo rounded-2xl p-6 text-white shadow-xl shadow-petroleo/30">
            <h3 class="font-black mb-1 flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa-light" style="font-variation-settings:'FILL' 1">payments</span>Estado de Pago
            </h3>
            <div class="text-3xl font-black mb-1 mt-4 text-transparent bg-clip-text bg-gradient-to-r from-turquesa-light to-coral"><?= fmoney($saldo) ?></div>
            <div class="text-xs text-white/40 mb-4">de <?= fmoney($valor_total) ?></div>
            <div class="h-3 bg-white/10 rounded-full overflow-hidden mb-4">
                <div class="h-full bg-gradient-to-r from-turquesa to-turquesa-light rounded-full" style="width:<?= $progreso ?>%"></div>
            </div>
            <a href="<?= Router::url('/client/payments') ?>" class="flex items-center justify-center gap-2 w-full py-3 bg-gradient-to-r from-turquesa to-turquesa-dark text-white font-bold rounded-xl hover:shadow-lg hover:shadow-turquesa/40 transition-all active:scale-95">
                <span class="material-symbols-outlined">receipt_long</span>Ver Pagos
            </a>
        </div>

        <!-- WhatsApp -->
        <a href="https://wa.me/51976324716?text=Hola%20soy%20<?= urlencode($nombre) ?>%20del%20grupo%20<?= urlencode($grupoNombre) ?>" target="_blank"
           class="flex items-center gap-3 w-full py-4 px-5 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-green-500/40 transition-all active:scale-95">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            WhatsApp
        </a>

        <!-- Soporte -->
        <a href="<?= Router::url('/client/soporte') ?>" class="flex items-center gap-3 w-full py-3.5 px-5 bg-white text-petroleo font-bold rounded-xl border border-petroleo/10 shadow-sm hover:shadow-md transition-all">
            <span class="material-symbols-outlined text-turquesa">support_agent</span>Centro de Soporte
        </a>
    </aside>
</div>

<?php else: ?>
<div class="bg-gradient-to-br from-superficie to-white rounded-2xl p-12 text-center border border-turquesa/10 shadow-lg">
    <span class="material-symbols-outlined text-6xl text-turquesa/20 mb-4 block">school</span>
    <h3 class="font-black text-petroleo text-xl mb-2">Sin grupo asignado</h3>
    <p class="text-sm text-petroleo/40 max-w-md mx-auto mb-6">No se encontr&oacute; un grupo vinculado a tu cuenta. Contacta a tu representante.</p>
    <a href="https://wa.me/51976324716" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-xl transition-all hover:shadow-lg">
        <span class="material-symbols-outlined">chat</span>Contactar
    </a>
</div>
<?php endif; ?>