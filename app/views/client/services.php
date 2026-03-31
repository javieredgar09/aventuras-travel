<?php
/**
 * Vista: Mis Servicios / Mi Viaje — Design System "The Elevated Explorer"
 * Variables: user, contrato, vuelos, pasajeros, servicios, cliente
 */
$user      = $user ?? ($_SESSION['user'] ?? []);
$contrato  = $contrato ?? null;
$vuelos    = $vuelos ?? [];
$pasajeros = $pasajeros ?? [];
$servicios = $servicios ?? [];

if (!function_exists('fmoney')) {
    function fmoney(float $a, string $c = 'USD'): string {
        $s = match(strtoupper($c)) { 'USD','$' => '$', 'EUR' => '€', 'PEN' => 'S/', default => strtoupper($c).' ' };
        return $s . number_format($a, 2);
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
// If no itinerario, put other services there
if (empty($itinerario) && !empty($otros)) {
    $itinerario = $otros;
    $otros = [];
}

$vuelo = !empty($vuelos) ? $vuelos[0] : null;
$hotel = !empty($hoteles) ? $hoteles[0] : null;

// Imagen hero por destino
$heroImages = [
    'cancún'     => 'https://images.unsplash.com/photo-1510097467424-192d713fd8b2?w=1200&q=80',
    'cancun'     => 'https://images.unsplash.com/photo-1510097467424-192d713fd8b2?w=1200&q=80',
    'punta cana' => 'https://images.unsplash.com/photo-1580237072617-771c3ecc4a24?w=1200&q=80',
    'cusco'      => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1200&q=80',
    'lima'       => 'https://images.unsplash.com/photo-1531968455001-5c5272a67c71?w=1200&q=80',
    'miami'      => 'https://images.unsplash.com/photo-1535498730771-e735b998cd64?w=1200&q=80',
    'cartagena'  => 'https://images.unsplash.com/photo-1583997052301-0fc38714e428?w=1200&q=80',
    'río de janeiro' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=1200&q=80',
    'rio de janeiro' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=1200&q=80',
    'buenos aires'   => 'https://images.unsplash.com/photo-1589909202802-8f4aadce1849?w=1200&q=80',
    'bogotá'     => 'https://images.unsplash.com/photo-1568034304837-671dd3e0e251?w=1200&q=80',
    'bogota'     => 'https://images.unsplash.com/photo-1568034304837-671dd3e0e251?w=1200&q=80',
];
$heroImg = $contrato['hero_image'] ?? null;
if (empty($heroImg)) {
    $destLower = strtolower($contrato['destino'] ?? '');
    foreach ($heroImages as $key => $url) {
        if (strpos($destLower, $key) !== false) { $heroImg = $url; break; }
    }
}
if (empty($heroImg)) $heroImg = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80';

// Imagen del hotel: intentar obtener del servicio o usar la del destino
$hotelImg = '';
if ($hotel) {
    $hotelDet = json_decode($hotel['detalles_json'] ?? '{}', true);
    $hotelImg = $hotelDet['imagen'] ?? ($hotel['imagen'] ?? '');
}
if (empty($hotelImg)) $hotelImg = $heroImg;
?>

<?php if ($contrato): ?>

<!-- HERO -->
<header class="relative h-64 md:h-96 rounded-[2.5rem] overflow-hidden mb-8 shadow-2xl shadow-primary/10">
    <img class="absolute inset-0 w-full h-full object-cover" src="<?= htmlspecialchars($heroImg) ?>" alt="<?= $destino ?>">
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-8 md:p-16">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-md text-white text-xs font-black rounded-full mb-6 w-fit tracking-widest uppercase border border-white/30">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            Contrato <?= $codigo ?>
        </div>
        <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter mb-4">Viaje a <?= $destino ?></h1>
        <div class="flex flex-wrap items-center gap-6 text-white/90">
            <?php if ($fechaSalida): ?>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary-fixed">calendar_month</span>
                <span class="font-bold"><?= date('d M', strtotime($fechaSalida)) ?><?= $fechaRetorno ? ' — ' . date('d M, Y', strtotime($fechaRetorno)) : '' ?></span>
            </div>
            <?php endif; ?>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary-fixed">group</span>
                <span class="font-bold"><?= count($pasajeros) ?> Pasajeros</span>
            </div>
            <?php if ($vuelo): ?>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary-fixed">flight_takeoff</span>
                <span class="font-bold"><?= htmlspecialchars($vuelo['aerolinea'] ?? '') ?> <?= htmlspecialchars($vuelo['numero_vuelo'] ?? '') ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- STICKY TABS -->
<div class="sticky top-20 z-40 bg-surface-bright/80 backdrop-blur-lg mb-10 -mx-4 px-4 py-2">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between bg-white rounded-2xl shadow-lg shadow-slate-200/50 p-2 border border-slate-100">
            <button onclick="showTab('accommodation')" class="srv-tab flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-xl text-primary bg-primary-fixed/30 border-b-2 border-primary transition-all" data-tab="accommodation">
                <span class="material-symbols-outlined">hotel</span>
                <span class="text-xs font-black uppercase tracking-tighter">Alojamiento</span>
            </button>
            <button onclick="showTab('itinerary')" class="srv-tab flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all" data-tab="itinerary">
                <span class="material-symbols-outlined">event_note</span>
                <span class="text-xs font-black uppercase tracking-tighter">Itinerario</span>
            </button>
            <button onclick="showTab('insurance')" class="srv-tab flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all" data-tab="insurance">
                <span class="material-symbols-outlined">verified_user</span>
                <span class="text-xs font-black uppercase tracking-tighter">Seguro</span>
            </button>
            <button onclick="showTab('summary')" class="srv-tab flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-xl text-slate-500 hover:text-primary hover:bg-slate-50 transition-all" data-tab="summary">
                <span class="material-symbols-outlined">description</span>
                <span class="text-xs font-black uppercase tracking-tighter">Resumen</span>
            </button>
        </div>
    </div>
</div>

<!-- CONTENT -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- LEFT (8 cols) -->
    <div class="lg:col-span-8 space-y-10">

        <!-- ACCOMMODATION TAB -->
        <section class="srv-panel" id="panel-accommodation">
            <div class="bg-white rounded-[2.5rem] p-4 shadow-sm border border-slate-100">
                <?php if ($hotel): ?>
                <div class="flex flex-col md:flex-row gap-8 items-start p-4">
                    <div class="w-full md:w-5/12 h-64 md:h-80 rounded-[2rem] overflow-hidden shadow-lg shrink-0 bg-surface-container-high">
                        <img src="<?= htmlspecialchars($hotelImg) ?>" alt="<?= htmlspecialchars($hotel['nombre'] ?? 'Hotel') ?>" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-outline\'><span class=\'material-symbols-outlined text-8xl\'>hotel</span></div>'">
                    </div>
                    <div class="flex-grow flex flex-col justify-between py-2 space-y-6">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h2 class="text-3xl font-black text-secondary tracking-tight"><?= htmlspecialchars($hotel['nombre'] ?? $hotel['descripcion'] ?? 'Hotel') ?></h2>
                                <span class="px-4 py-1.5 bg-secondary-container text-on-secondary-container rounded-full text-[10px] font-black uppercase tracking-widest"><?= htmlspecialchars($hotel['tipo'] ?? 'Hospedaje') ?></span>
                            </div>
                            <?php if ($fechaSalida): ?>
                            <div class="grid grid-cols-2 gap-4 mt-6 mb-8">
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
                <div class="mb-8">
                    <h3 class="text-sm font-black text-outline uppercase tracking-widest mb-4">Vuelos</h3>
                    <div class="space-y-4">
                        <?php foreach ($vuelos as $v): ?>
                        <div class="flex items-center gap-6 p-4 bg-surface rounded-2xl">
                            <span class="material-symbols-outlined text-primary text-3xl">flight_takeoff</span>
                            <div class="flex-1">
                                <p class="font-bold text-on-surface"><?= htmlspecialchars(($v['origen'] ?? '') . ' → ' . ($v['destino'] ?? '')) ?></p>
                                <p class="text-xs text-outline"><?= htmlspecialchars($v['aerolinea'] ?? '') ?> <?= htmlspecialchars($v['numero_vuelo'] ?? '') ?> · <?= !empty($v['fecha_salida']) ? date('d M Y', strtotime($v['fecha_salida'])) : '' ?></p>
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
        <div class="bg-secondary rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl" id="panel-insurance-desktop">
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
        <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
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
