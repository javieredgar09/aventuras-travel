<?php
/**
 * Vista: Mis Servicios / Mi Viaje — Aventuras Travel Pucallpa
 * Variables: user, contrato, vuelos, pasajeros, servicios, cliente
 */
require_once __DIR__ . '/../../helpers/DestinationHelper.php';

$user      = $user ?? ($_SESSION['user'] ?? []);
$contrato  = $contrato ?? null;
$vuelos    = $vuelos ?? [];
$pasajeros = $pasajeros ?? [];
$servicios = $servicios ?? [];

if (!function_exists('fmoney')) {
    function fmoney(float $a, string $c = 'USD'): string {
        return '$' . number_format($a, 2);
    }
}

$codigo  = htmlspecialchars($contrato['codigo'] ?? '');
$destino = htmlspecialchars($contrato['destino'] ?? 'Mi Viaje');
$fechaSalida  = $contrato['fecha_salida'] ?? '';
$fechaRetorno = $contrato['fecha_retorno'] ?? '';
$estado  = ucfirst($contrato['estado'] ?? 'activo');

// Categorize services
$hoteles = [];
$itinerario = [];
$seguros = [];
$otros = [];
foreach ($servicios as $s) {
    $tipo = strtolower($s['tipo'] ?? '');
    if (strpos($tipo, 'hotel') !== false || strpos($tipo, 'aloj') !== false) {
        $hoteles[] = $s;
    } elseif (strpos($tipo, 'seguro') !== false || strpos($tipo, 'insurance') !== false) {
        $seguros[] = $s;
    } elseif (strpos($tipo, 'actividad') !== false || strpos($tipo, 'tour') !== false || strpos($tipo, 'excursion') !== false) {
        $itinerario[] = $s;
    } else {
        $otros[] = $s;
    }
}
if (empty($itinerario) && !empty($otros)) {
    $itinerario = $otros;
    $otros = [];
}

$vuelo = !empty($vuelos) ? $vuelos[0] : null;
$hotel = !empty($hoteles) ? $hoteles[0] : null;

// Imagen dinámica del destino via helper centralizado
$heroImg = DestinationHelper::getHeroImage($destino);
$destIcon = DestinationHelper::getIcon($destino);
$accentColor = DestinationHelper::getAccentColor($destino);

// Imagen del hotel: intentar obtener del servicio o usar la del destino
$hotelImg = '';
if ($hotel) {
    $hotelDet = json_decode($hotel['detalles_json'] ?? '{}', true);
    $hotelImg = $hotelDet['imagen'] ?? ($hotel['imagen'] ?? '');
}
if (empty($hotelImg)) $hotelImg = DestinationHelper::getCardImage($destino);

// ── Airline IATA mapping for logos ──
$airlineMap = [
    'latam'       => ['code' => 'LA', 'color' => '#1B0088'],
    'copa'        => ['code' => 'CM', 'color' => '#003876'],
    'avianca'     => ['code' => 'AV', 'color' => '#E31837'],
    'jetsmart'    => ['code' => 'JA', 'color' => '#FF6600'],
    'american'    => ['code' => 'AA', 'color' => '#0078D2'],
    'delta'       => ['code' => 'DL', 'color' => '#003366'],
    'united'      => ['code' => 'UA', 'color' => '#002244'],
    'spirit'      => ['code' => 'NK', 'color' => '#FFD700'],
    'viva'        => ['code' => 'VV', 'color' => '#E4002B'],
    'sky'         => ['code' => 'H2', 'color' => '#00A651'],
    'aeromexico'  => ['code' => 'AM', 'color' => '#002D5F'],
    'aéromexico'  => ['code' => 'AM', 'color' => '#002D5F'],
    'volaris'     => ['code' => 'Y4', 'color' => '#6D2077'],
    'gol'         => ['code' => 'G3', 'color' => '#FF6600'],
    'azul'        => ['code' => 'AD', 'color' => '#003DA5'],
    'iberia'      => ['code' => 'IB', 'color' => '#D4213D'],
    'air europa'  => ['code' => 'UX', 'color' => '#003399'],
    'aerolineas argentinas' => ['code' => 'AR', 'color' => '#006B95'],
    'wingo'       => ['code' => 'P5', 'color' => '#24B74F'],
    'star peru'   => ['code' => '2I', 'color' => '#B22234'],
    'peruvian'    => ['code' => 'P9', 'color' => '#D4213D'],
];

if (!function_exists('getAirlineData')) {
    function getAirlineData(string $airline, array $map): array {
        $lower = strtolower(trim($airline));
        foreach ($map as $key => $info) {
            if (strpos($lower, $key) !== false) return $info;
        }
        return ['code' => '', 'color' => '#1B3A4B'];
    }
    function airlineLogoUrl(string $code): string {
        return $code ? 'https://pics.avs.io/200/80/' . urlencode($code) . '.png' : '';
    }
}
?>

<?php if ($contrato): ?>

<!-- HERO — Imagen dinámica del destino -->
<header class="relative h-[220px] sm:h-[260px] md:h-[300px] rounded-2xl overflow-hidden mb-6 shadow-2xl group">
    <img class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?= htmlspecialchars($heroImg) ?>" alt="<?= $destino ?>" fetchpriority="high">
    <div class="absolute inset-0 bg-gradient-to-r from-petroleo-dark/95 via-petroleo/60 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark/90 via-transparent to-transparent"></div>
    <div class="absolute top-0 right-0 w-60 h-60 rounded-full blur-3xl -translate-y-1/3 translate-x-1/4 pointer-events-none" style="background:<?= $accentColor ?>33;"></div>
    <div class="absolute inset-0 flex flex-col justify-end p-5 sm:p-7">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-xl text-white text-[10px] font-black rounded-full mb-3 w-fit tracking-widest uppercase border border-white/20 shadow-lg">
            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
            Contrato <?= $codigo ?>
        </div>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-tight mb-2 drop-shadow-2xl">
            <span class="mr-1"><?= $destIcon ?></span> Viaje a <?= $destino ?>
        </h1>
        <div class="flex flex-wrap items-center gap-3 text-white/90 text-xs">
            <?php if ($fechaSalida): ?>
            <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur px-3 py-1 rounded-lg">
                <span class="material-symbols-outlined text-sm text-gold">calendar_month</span>
                <span class="font-bold"><?= date('d M', strtotime($fechaSalida)) ?><?= $fechaRetorno ? ' — ' . date('d M, Y', strtotime($fechaRetorno)) : '' ?></span>
            </div>
            <?php endif; ?>
            <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur px-3 py-1 rounded-lg">
                <span class="material-symbols-outlined text-sm text-turquesa-light">group</span>
                <span class="font-bold"><?= count($pasajeros) ?> Pasajeros</span>
            </div>
            <?php if ($vuelo):
                $heroAl = getAirlineData($vuelo['aerolinea'] ?? '', $airlineMap);
                $heroLogo = airlineLogoUrl($heroAl['code']);
            ?>
            <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur px-3 py-1 rounded-lg">
                <?php if ($heroLogo): ?>
                <img src="<?= $heroLogo ?>" alt="" class="h-4 object-contain brightness-0 invert opacity-90" onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                <span class="material-symbols-outlined text-sm text-turquesa-light" style="display:none">flight_takeoff</span>
                <?php else: ?>
                <span class="material-symbols-outlined text-sm text-turquesa-light">flight_takeoff</span>
                <?php endif; ?>
                <span class="font-bold"><?= htmlspecialchars($vuelo['aerolinea'] ?? '') ?> <?= htmlspecialchars($vuelo['numero_vuelo'] ?? '') ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- STICKY TABS -->
<div class="sticky top-16 sm:top-20 z-40 bg-surface-bright/80 backdrop-blur-lg mb-5 -mx-2 sm:-mx-4 px-2 sm:px-4 py-1.5">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between bg-white rounded-2xl shadow-sm p-1 border border-outline-variant/10 overflow-x-auto">
            <button onclick="showTab('accommodation')" class="srv-tab flex-1 flex flex-col items-center gap-0.5 sm:gap-1 py-2 sm:py-3 px-1.5 sm:px-2 rounded-xl text-primary bg-primary-fixed/30 border-b-2 border-primary transition-all min-w-[60px]" data-tab="accommodation">
                <span class="material-symbols-outlined text-xl sm:text-2xl">hotel</span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-tighter">Alojamiento</span>
            </button>
            <button onclick="showTab('flights')" class="srv-tab flex-1 flex flex-col items-center gap-0.5 sm:gap-1 py-2 sm:py-3 px-1.5 sm:px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all min-w-[60px]" data-tab="flights">
                <span class="material-symbols-outlined text-xl sm:text-2xl">flight</span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-tighter">Vuelos</span>
            </button>
            <button onclick="showTab('itinerary')" class="srv-tab flex-1 flex flex-col items-center gap-0.5 sm:gap-1 py-2 sm:py-3 px-1.5 sm:px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all min-w-[60px]" data-tab="itinerary">
                <span class="material-symbols-outlined text-xl sm:text-2xl">event_note</span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-tighter">Itinerario</span>
            </button>
            <button onclick="showTab('insurance')" class="srv-tab flex-1 flex flex-col items-center gap-0.5 sm:gap-1 py-2 sm:py-3 px-1.5 sm:px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all min-w-[60px]" data-tab="insurance">
                <span class="material-symbols-outlined text-xl sm:text-2xl">verified_user</span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-tighter">Seguro</span>
            </button>
            <button onclick="showTab('summary')" class="srv-tab flex-1 flex flex-col items-center gap-0.5 sm:gap-1 py-2 sm:py-3 px-1.5 sm:px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all min-w-[60px]" data-tab="summary">
                <span class="material-symbols-outlined text-xl sm:text-2xl">description</span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-tighter">Resumen</span>
            </button>
        </div>
    </div>
</div>

<!-- CONTENT -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

    <!-- LEFT (8 cols) -->
    <div class="lg:col-span-8 space-y-6">

        <!-- ACCOMMODATION TAB -->
        <section class="srv-panel" id="panel-accommodation">
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-outline-variant/10">
                <?php if ($hotel): ?>
                <div class="flex flex-col md:flex-row gap-4 items-start p-2">
                    <div class="w-full md:w-5/12 h-48 sm:h-56 rounded-2xl overflow-hidden shadow-sm shrink-0 bg-surface-container-high">
                        <img src="<?= htmlspecialchars($hotelImg) ?>" alt="<?= htmlspecialchars($hotel['nombre'] ?? 'Hotel') ?>" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-outline\'><span class=\'material-symbols-outlined text-8xl\'>hotel</span></div>'">
                    </div>
                    <div class="flex-grow flex flex-col justify-between py-2 space-y-6">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h2 class="text-3xl font-black text-secondary tracking-tight"><?= htmlspecialchars($hotel['nombre'] ?? $hotel['descripcion'] ?? 'Hotel') ?></h2>
                                <span class="px-4 py-1.5 bg-secondary-container text-on-secondary-container rounded-full text-[10px] font-black uppercase tracking-widest"><?= htmlspecialchars($hotel['tipo'] ?? 'Hospedaje') ?></span>
                            </div>
                            <?php if ($fechaSalida): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-4 sm:mt-6 mb-6 sm:mb-8">
                                <div class="p-5 bg-surface rounded-2xl border border-slate-50">
                                    <div class="text-[10px] text-outline uppercase font-black tracking-widest mb-1">Check-in</div>
                                    <div class="text-on-surface font-black text-xl"><?= date('d M Y', strtotime($fechaSalida)) ?></div>
                                    <div class="text-[10px] text-secondary font-bold">15:00 PM</div>
                                </div>
                                <div class="p-5 bg-surface rounded-2xl border border-slate-50">
                                    <div class="text-[10px] text-outline uppercase font-black tracking-widest mb-1">Check-out</div>
                                    <div class="text-on-surface font-black text-xl"><?= $fechaRetorno ? date('d M Y', strtotime($fechaRetorno)) : '-' ?></div>
                                    <div class="text-[10px] text-secondary font-bold">12:00 PM</div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3 text-sm font-semibold text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary">person_add</span>
                                    Capacidad: <?= count($pasajeros) ?> Huéspedes
                                </div>
                            </div>
                        </div>
                        <div class="pt-6 border-t border-slate-100">
                            <h3 class="text-[10px] font-black text-outline uppercase tracking-[0.2em] mb-4">Servicios Incluidos</h3>
                            <div class="flex flex-wrap gap-5">
                                <?php foreach (['wifi' => 'WiFi', 'pool' => 'Piscina', 'restaurant' => 'Buffet', 'beach_access' => 'Club Mar'] as $icon => $label): ?>
                                <div class="flex flex-col items-center gap-1">
                                    <div class="w-12 h-12 rounded-2xl bg-surface flex items-center justify-center text-secondary shadow-sm">
                                        <span class="material-symbols-outlined"><?= $icon ?></span>
                                    </div>
                                    <span class="text-[10px] font-bold text-outline"><?= $label ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="p-12 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline/30 mb-4 block">hotel</span>
                    <h3 class="text-xl font-black text-secondary mb-2">Alojamiento por confirmar</h3>
                    <p class="text-outline">Tu asesor confirmará los detalles de tu hospedaje pronto.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- FLIGHTS TAB – Boarding Pass Style -->
        <section class="srv-panel hidden" id="panel-flights">
            <div class="flex items-end justify-between px-2 mb-6">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-black text-secondary tracking-tighter">Mis Vuelos</h2>
                    <p class="text-sm text-outline mt-1"><?= count($vuelos) ?> vuelo<?= count($vuelos) !== 1 ? 's' : '' ?> programado<?= count($vuelos) !== 1 ? 's' : '' ?></p>
                </div>
            </div>
            <?php if (!empty($vuelos)): ?>
            <div class="space-y-5">
                <?php foreach ($vuelos as $vi => $v):
                    $alData  = getAirlineData($v['aerolinea'] ?? '', $airlineMap);
                    $alCode  = strtoupper($alData['code'] ?: mb_substr($v['aerolinea'] ?? 'XX', 0, 2));
                    $alColor = $alData['color'];
                    $logoUrl = airlineLogoUrl($alData['code']);
                    $orgCode = htmlspecialchars($v['origen'] ?? '');
                    $orgCity = htmlspecialchars($v['origen_ciudad'] ?? '');
                    $dstCode = htmlspecialchars($v['destino'] ?? '');
                    $dstCity = htmlspecialchars($v['destino_ciudad'] ?? '');
                    $tSal    = !empty($v['fecha_salida'])  ? strtotime($v['fecha_salida'])  : null;
                    $tLleg   = !empty($v['fecha_llegada']) ? strtotime($v['fecha_llegada']) : null;
                    $durH    = ($tSal && $tLleg && ($tLleg-$tSal) > 0 && ($tLleg-$tSal) < 172800) ? round(($tLleg-$tSal)/3600,1) : null;
                ?>
                <div class="relative bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-xl transition-all group">
                    <!-- Accent bar -->
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 rounded-l-2xl" style="background:<?= $alColor ?>"></div>

                    <div class="p-5 sm:p-6 pl-6 sm:pl-8">
                        <!-- Header: Airline Logo + Flight + Status -->
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <?php if ($logoUrl): ?>
                                <img src="<?= $logoUrl ?>" alt="<?= htmlspecialchars($v['aerolinea'] ?? '') ?>"
                                     class="h-7 sm:h-8 object-contain"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-[10px] font-black shrink-0" style="background:<?= $alColor ?>;display:none"><?= $alCode ?></div>
                                <?php else: ?>
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-[10px] font-black shrink-0" style="background:<?= $alColor ?>"><?= $alCode ?></div>
                                <?php endif; ?>
                                <div>
                                    <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars($v['aerolinea'] ?? '') ?></p>
                                    <p class="text-[11px] text-outline font-mono"><?= htmlspecialchars($v['numero_vuelo'] ?? '') ?></p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider border
                                <?= ($v['estado'] ?? '') === 'confirmado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' ?>">
                                <?= ucfirst(htmlspecialchars($v['estado'] ?? 'pendiente')) ?>
                            </span>
                        </div>

                        <!-- Route: ORG ----✈---- DST -->
                        <div class="flex items-center justify-between gap-2 mb-5">
                            <div class="text-center min-w-[60px]">
                                <p class="text-2xl sm:text-3xl font-black text-on-surface tracking-tight"><?= $orgCode ?></p>
                                <?php if ($orgCity): ?><p class="text-[10px] text-outline font-medium truncate max-w-[90px]"><?= $orgCity ?></p><?php endif; ?>
                                <?php if ($tSal): ?><p class="text-xs font-bold text-primary mt-1"><?= date('H:i', $tSal) ?></p><?php endif; ?>
                            </div>
                            <div class="flex-1 flex flex-col items-center gap-1 px-2">
                                <?php if ($durH): ?>
                                <span class="text-[9px] text-outline font-medium"><?= $durH ?>h</span>
                                <?php endif; ?>
                                <div class="w-full flex items-center gap-1">
                                    <div class="w-2 h-2 rounded-full border-2 border-primary shrink-0"></div>
                                    <div class="h-px flex-1 border-t-2 border-dashed border-primary/30"></div>
                                    <span class="material-symbols-outlined text-primary text-base shrink-0" style="font-variation-settings:'FILL' 1">flight</span>
                                    <div class="h-px flex-1 border-t-2 border-dashed border-primary/30"></div>
                                    <div class="w-2 h-2 rounded-full bg-primary shrink-0"></div>
                                </div>
                                <span class="text-[9px] text-outline capitalize"><?= htmlspecialchars($v['tipo'] ?? 'directo') ?></span>
                            </div>
                            <div class="text-center min-w-[60px]">
                                <p class="text-2xl sm:text-3xl font-black text-on-surface tracking-tight"><?= $dstCode ?></p>
                                <?php if ($dstCity): ?><p class="text-[10px] text-outline font-medium truncate max-w-[90px]"><?= $dstCity ?></p><?php endif; ?>
                                <?php if ($tLleg): ?><p class="text-xs font-bold text-primary mt-1"><?= date('H:i', $tLleg) ?></p><?php endif; ?>
                            </div>
                        </div>

                        <!-- Footer: Date, Class, Plane -->
                        <div class="flex flex-wrap items-center gap-4 sm:gap-6 pt-3 border-t border-dashed border-slate-200">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-outline text-sm">calendar_today</span>
                                <div>
                                    <span class="text-[9px] text-outline font-bold uppercase tracking-widest block leading-tight">Fecha</span>
                                    <span class="text-xs sm:text-sm font-bold text-on-surface"><?= $tSal ? date('d M Y', $tSal) : '—' ?></span>
                                </div>
                            </div>
                            <?php if (!empty($v['clase'])): ?>
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-outline text-sm">airline_seat_recline_normal</span>
                                <div>
                                    <span class="text-[9px] text-outline font-bold uppercase tracking-widest block leading-tight">Clase</span>
                                    <span class="text-xs sm:text-sm font-bold text-on-surface capitalize"><?= htmlspecialchars($v['clase']) ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($v['avion'])): ?>
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-outline text-sm">airplanemode_active</span>
                                <div>
                                    <span class="text-[9px] text-outline font-bold uppercase tracking-widest block leading-tight">Avión</span>
                                    <span class="text-xs sm:text-sm font-bold text-on-surface"><?= htmlspecialchars($v['avion']) ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-[2rem] p-12 text-center shadow-sm border border-slate-100">
                <span class="material-symbols-outlined text-6xl text-outline/30 mb-4 block">flight</span>
                <h3 class="text-xl font-black text-secondary mb-2">Vuelos por confirmar</h3>
                <p class="text-outline">Tu asesor confirmará los detalles de tus vuelos pronto.</p>
            </div>
            <?php endif; ?>
        </section>

        <!-- ITINERARY TAB -->
        <section class="srv-panel hidden" id="panel-itinerary">
            <div class="flex items-end justify-between px-2 mb-8">
                <h2 class="text-4xl font-black text-secondary tracking-tighter">Itinerario de Actividades</h2>
            </div>
            <?php if (!empty($itinerario)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($itinerario as $i => $act): ?>
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 flex gap-6 hover:shadow-xl transition-all group">
                    <div class="shrink-0 w-16 h-16 rounded-[1.25rem] bg-primary text-white flex flex-col items-center justify-center">
                        <span class="text-[10px] font-black uppercase">DÍA</span>
                        <span class="text-2xl font-black"><?= $i + 1 ?></span>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-primary uppercase tracking-widest mb-1"><?= htmlspecialchars($act['tipo'] ?? 'ACTIVIDAD') ?></div>
                        <h4 class="font-extrabold text-lg text-on-surface mb-2 group-hover:text-primary transition-colors"><?= htmlspecialchars($act['nombre'] ?? $act['descripcion'] ?? '') ?></h4>
                        <?php if (!empty($act['descripcion'])): ?>
                        <p class="text-sm text-on-surface-variant leading-relaxed"><?= htmlspecialchars($act['descripcion']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-[2rem] p-12 text-center shadow-sm border border-slate-100">
                <span class="material-symbols-outlined text-6xl text-outline/30 mb-4 block">event_note</span>
                <h3 class="text-xl font-black text-secondary mb-2">Itinerario en preparación</h3>
                <p class="text-outline">Tu asesor está preparando las actividades de tu viaje.</p>
            </div>
            <?php endif; ?>
        </section>

        <!-- INSURANCE TAB (shown inline for mobile, sidebar for desktop) -->
        <section class="srv-panel hidden lg:hidden" id="panel-insurance-mobile">
            <?php include __DIR__ . '/partials/services_insurance.php'; ?>
        </section>

        <!-- SUMMARY TAB -->
        <section class="srv-panel hidden" id="panel-summary">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <h2 class="text-3xl font-black text-secondary tracking-tighter mb-8">Resumen del Viaje</h2>

                <!-- Flights -->
                <?php if (!empty($vuelos)): ?>
                <div class="mb-10">
                    <h3 class="text-sm font-black text-outline uppercase tracking-widest mb-5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">flight</span>
                        Vuelos
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($vuelos as $v):
                            $salData  = getAirlineData($v['aerolinea'] ?? '', $airlineMap);
                            $salColor = $salData['color'];
                            $salLogo  = airlineLogoUrl($salData['code']);
                            $salCode  = strtoupper($salData['code'] ?: mb_substr($v['aerolinea'] ?? 'XX', 0, 2));
                        ?>
                        <div class="flex items-center gap-4 p-4 bg-surface rounded-2xl border border-slate-50 hover:shadow-md transition-all">
                            <div class="shrink-0 w-14 flex flex-col items-center justify-center">
                                <?php if ($salLogo): ?>
                                <img src="<?= $salLogo ?>" alt="<?= htmlspecialchars($v['aerolinea'] ?? '') ?>"
                                     class="h-5 object-contain"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-[9px] font-black" style="background:<?= $salColor ?>;display:none"><?= $salCode ?></div>
                                <?php else: ?>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-[9px] font-black" style="background:<?= $salColor ?>"><?= $salCode ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <span class="font-black text-on-surface text-lg"><?= htmlspecialchars($v['origen'] ?? '') ?></span>
                                <div class="flex items-center gap-1 flex-1">
                                    <div class="h-px flex-1 border-t border-dashed border-primary/30"></div>
                                    <span class="material-symbols-outlined text-primary text-sm" style="font-variation-settings:'FILL' 1">flight</span>
                                    <div class="h-px flex-1 border-t border-dashed border-primary/30"></div>
                                </div>
                                <span class="font-black text-on-surface text-lg"><?= htmlspecialchars($v['destino'] ?? '') ?></span>
                            </div>
                            <div class="text-right shrink-0 hidden sm:block">
                                <p class="text-xs font-bold text-on-surface"><?= htmlspecialchars($v['aerolinea'] ?? '') ?> <?= htmlspecialchars($v['numero_vuelo'] ?? '') ?></p>
                                <p class="text-[10px] text-outline"><?= !empty($v['fecha_salida']) ? date('d M Y', strtotime($v['fecha_salida'])) : '' ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Passengers -->
                <div class="mb-8">
                    <h3 class="text-sm font-black text-outline uppercase tracking-widest mb-4">Pasajeros</h3>
                    <div class="space-y-3">
                        <?php foreach ($pasajeros as $i => $p): ?>
                        <div class="flex items-center gap-4 p-4 bg-surface rounded-2xl">
                            <div class="w-12 h-12 rounded-2xl <?= $i === 0 ? 'bg-primary-container/20 text-primary' : 'bg-secondary-container/20 text-secondary' ?> flex items-center justify-center font-black">
                                <?= strtoupper(substr($p['nombre'] ?? '', 0, 1) . substr($p['apellido'] ?? '', 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-bold text-on-surface"><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></p>
                                <p class="text-xs text-outline"><?= htmlspecialchars($p['tipo'] ?? 'Adulto') ?> · Doc: <?= htmlspecialchars($p['documento'] ?? '-') ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- All Services -->
                <?php if (!empty($servicios)): ?>
                <div>
                    <h3 class="text-sm font-black text-outline uppercase tracking-widest mb-4">Todos los Servicios</h3>
                    <div class="space-y-3">
                        <?php foreach ($servicios as $s): ?>
                        <div class="flex items-center gap-4 p-4 bg-surface rounded-2xl">
                            <span class="material-symbols-outlined text-primary">check_circle</span>
                            <div>
                                <p class="font-bold text-on-surface"><?= htmlspecialchars($s['nombre'] ?? $s['descripcion'] ?? '') ?></p>
                                <p class="text-xs text-outline"><?= htmlspecialchars($s['tipo'] ?? '') ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- RIGHT SIDEBAR (4 cols) -->
    <aside class="lg:col-span-4 space-y-8">

        <!-- Insurance Card -->
        <div class="bg-secondary rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-8 md:p-10 text-white relative overflow-hidden shadow-2xl" id="panel-insurance-desktop">
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-primary-container/20 rounded-full blur-[80px]"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl flex items-center justify-center mb-8">
                    <span class="material-symbols-outlined text-4xl text-primary-fixed" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                </div>
                <h3 class="text-3xl font-black tracking-tight mb-4 leading-tight">Seguro de Viaje<br>Asistencia Global</h3>
                <p class="text-white/60 text-sm mb-10 leading-relaxed font-medium">Tu seguridad es nuestra prioridad. Cuentas con cobertura integral 24/7 y asistencia médica inmediata durante toda tu estancia.</p>
                <div class="space-y-6 mb-12">
                    <?php foreach (['Asistencia médica premium (USD 60k)', 'Cobertura equipaje y cancelaciones', 'Repatriación sanitaria incluida'] as $item): ?>
                    <div class="flex items-center gap-4">
                        <div class="w-6 h-6 rounded-full bg-primary-container/20 flex items-center justify-center border border-primary-container/30">
                            <span class="material-symbols-outlined text-sm text-primary-container">check</span>
                        </div>
                        <span class="text-sm font-bold"><?= $item ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="w-full py-5 bg-primary text-white font-black rounded-2xl hover:bg-primary-container transition-all shadow-xl shadow-black/20 flex items-center justify-center gap-2">
                    Ver Certificado de Póliza
                    <span class="material-symbols-outlined">download</span>
                </button>
            </div>
        </div>

        <!-- Trip Status -->
        <div class="bg-white rounded-[2rem] sm:rounded-[2.5rem] p-5 sm:p-6 md:p-8 border border-slate-100 shadow-sm">
                <h3 class="text-xl font-black text-on-surface tracking-tight">Estado del Viaje</h3>
                <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full uppercase tracking-widest border border-green-200"><?= $estado ?></span>
            </div>
            <div class="space-y-5">
                <div class="flex justify-between items-center p-4 bg-surface rounded-2xl border border-slate-50">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-outline font-black uppercase tracking-widest">Contrato</span>
                        <span class="text-sm font-bold text-on-surface"><?= $codigo ?></span>
                    </div>
                    <span class="material-symbols-outlined text-primary">verified</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-surface rounded-2xl border border-slate-50">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-outline font-black uppercase tracking-widest">Operador</span>
                        <span class="text-sm font-bold text-on-surface">Aventuras Travel Pucallpa</span>
                    </div>
                    <span class="material-symbols-outlined text-primary">apartment</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-surface rounded-2xl border border-slate-50">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-outline font-black uppercase tracking-widest">Pasajeros</span>
                        <span class="text-sm font-bold text-on-surface"><?= count($pasajeros) ?> personas</span>
                    </div>
                    <span class="material-symbols-outlined text-primary">group</span>
                </div>
            </div>
            <div class="mt-10 pt-8 border-t border-slate-100">
                <button class="w-full py-4 bg-surface-container-high text-secondary font-black rounded-2xl hover:bg-secondary hover:text-white transition-all flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined">chat_bubble</span> Chat con Asesor
                </button>
            </div>
        </div>
    </aside>
</div>

<script>
function showTab(tab) {
    // Hide all panels
    document.querySelectorAll('.srv-panel').forEach(function(el) { el.classList.add('hidden'); });
    // Reset all tabs
    document.querySelectorAll('.srv-tab').forEach(function(el) {
        el.classList.remove('text-primary', 'bg-primary-fixed/30', 'border-b-2', 'border-primary');
        el.classList.add('text-slate-500');
    });
    // Show selected panel
    var panel = document.getElementById('panel-' + tab);
    if (panel) panel.classList.remove('hidden');
    // Also handle insurance special case (mobile vs desktop)
    if (tab === 'insurance') {
        var mob = document.getElementById('panel-insurance-mobile');
        if (mob) mob.classList.remove('hidden');
    }
    // Activate tab button
    var btn = document.querySelector('.srv-tab[data-tab="' + tab + '"]');
    if (btn) {
        btn.classList.remove('text-slate-500');
        btn.classList.add('text-primary', 'bg-primary-fixed/30', 'border-b-2', 'border-primary');
    }
}
</script>

<?php else: ?>
<div class="max-w-lg mx-auto mt-20 bg-white rounded-[2rem] p-12 text-center shadow-sm border border-outline-variant/10">
    <span class="material-symbols-outlined text-6xl text-primary-container mb-4 block">explore</span>
    <h2 class="text-2xl font-black text-secondary mb-2">Sin viajes activos</h2>
    <p class="text-outline">Contacta a tu asesor de viajes para planificar tu próxima aventura.</p>
</div>
<?php endif; ?>
