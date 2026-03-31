<?php
/**
 * Vista: Dashboard Cliente Familiar — Design System "The Elevated Explorer"
 * Variables: user, contrato, contratos, vuelos, pasajeros, pagos, servicios, vouchers, pago_completo, cliente
 */
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

$valor_total  = (float)($contrato['valor_total'] ?? 0);
$total_pagado = (float)($contrato['total_pagado'] ?? 0);
$saldo        = $valor_total - $total_pagado;
$moneda       = $contrato['moneda'] ?? 'USD';

if (!function_exists('fmoney')) {
    function fmoney(float $a, string $c = 'USD'): string {
        $s = match(strtoupper($c)) { 'USD','$' => '$', 'EUR' => '€', 'PEN' => 'S/', default => strtoupper($c).' ' };
        return $s . number_format($a, 2);
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

// Imagen hero por destino (fallback si hero_image no está en contrato)
$heroImages = [
    'cancún'     => 'https://images.unsplash.com/photo-1510097467424-192d713fd8b2?w=1200&q=80',
    'cancun'     => 'https://images.unsplash.com/photo-1510097467424-192d713fd8b2?w=1200&q=80',
    'punta cana' => 'https://images.unsplash.com/photo-1580237072617-771c3ecc4a24?w=1200&q=80',
    'cusco'      => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1200&q=80',
    'lima'       => 'https://images.unsplash.com/photo-1531968455001-5c5272a67c71?w=1200&q=80',
    'miami'      => 'https://images.unsplash.com/photo-1535498730771-e735b998cd64?w=1200&q=80',
    'cartagena'  => 'https://images.unsplash.com/photo-1583997052301-0fc38714e428?w=1200&q=80',
];
$heroImg = $contrato['hero_image'] ?? null;
if (empty($heroImg)) {
    $destLower = strtolower($destino);
    foreach ($heroImages as $key => $url) {
        if (strpos($destLower, $key) !== false) { $heroImg = $url; break; }
    }
}
if (empty($heroImg)) $heroImg = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80';
?>

<?php if ($contrato): ?>

<!-- HERO -->
<section class="relative h-[420px] md:h-[450px] rounded-[2rem] overflow-hidden mb-8 shadow-2xl group">
    <img alt="<?= $destino ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?= htmlspecialchars($heroImg) ?>">
    <div class="absolute inset-0 bg-gradient-to-t from-secondary/90 via-secondary/20 to-transparent"></div>
    <div class="absolute bottom-0 left-0 p-8 md:p-16 w-full flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div class="max-w-2xl">
            <div class="bg-primary-container/40 backdrop-blur-md px-4 py-1.5 rounded-full w-fit mb-6 border border-white/20">
                <span class="text-white text-xs font-black tracking-widest uppercase">Contrato: <?= $codigo ?></span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter leading-tight">
                ¡Hola <?= $nombre ?>!<br>
                <?php if ($daysUntil !== null && $daysUntil > 0): ?>
                Tu aventura comienza en <span class="text-primary-container"><?= $daysUntil ?> días</span>
                <?php elseif ($daysUntil === 0): ?>
                <span class="text-primary-container">¡Tu aventura es hoy!</span>
                <?php else: ?>
                Tu aventura a <span class="text-primary-container"><?= $destino ?></span>
                <?php endif; ?>
            </h1>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 shrink-0">
            <?php if ($pago_completo && !empty($vouchers)): ?>
            <button class="px-8 py-4 bg-white text-secondary font-bold rounded-2xl shadow-lg hover:bg-surface-container-low transition-all active:scale-95 flex items-center justify-center gap-3">
                <span class="material-symbols-outlined">download</span> Descargar Vouchers
            </button>
            <?php endif; ?>
            <a href="<?= Router::url('/client/services') ?>" class="px-8 py-4 bg-primary text-white font-bold rounded-2xl shadow-lg hover:brightness-110 transition-all active:scale-95 flex items-center justify-center gap-3">
                <span class="material-symbols-outlined">map</span> Ver Itinerario
            </a>
        </div>
    </div>
</section>

<!-- BENTO GRID -->
<div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
    <!-- LEFT COL -->
    <div class="xl:col-span-1 flex flex-col gap-6">
        <div class="bg-gradient-to-br from-cyan-600 to-primary p-8 rounded-[2rem] shadow-xl text-white flex flex-col justify-between min-h-[300px] relative overflow-hidden group">
            <div class="relative z-10">
                <span class="material-symbols-outlined text-4xl text-cyan-200 mb-3">auto_stories</span>
                <?php
                $versiculos = [
                    ['texto' => 'Porque yo sé los planes que tengo para ustedes, planes de bienestar y no de calamidad, a fin de darles un futuro y una esperanza.', 'cita' => 'Jeremías 29:11'],
                    ['texto' => 'Mira que te mando que te esfuerces y seas valiente; no temas ni desmayes, porque Jehová tu Dios estará contigo en dondequiera que vayas.', 'cita' => 'Josué 1:9'],
                    ['texto' => 'Confía en Jehová con todo tu corazón, y no te apoyes en tu propia prudencia. Reconócelo en todos tus caminos, y Él enderezará tus veredas.', 'cita' => 'Proverbios 3:5-6'],
                    ['texto' => 'Todo lo puedo en Cristo que me fortalece.', 'cita' => 'Filipenses 4:13'],
                    ['texto' => 'No se amolden al mundo actual, sino sean transformados mediante la renovación de su mente.', 'cita' => 'Romanos 12:2'],
                ];
                $verso = $versiculos[array_rand($versiculos)];
                ?>
                <p class="text-cyan-50 font-medium text-sm leading-relaxed italic">"<?= $verso['texto'] ?>"</p>
            </div>
            <div class="relative z-10 mt-6">
                <p class="text-right text-white font-black text-sm">— <?= $verso['cita'] ?></p>
                <p class="text-cyan-200/70 text-xs mt-4 text-center font-semibold">Nuevos caminos, grandes propósitos. ¡Dios te acompaña! ✨</p>
            </div>
            <span class="material-symbols-outlined absolute -right-8 -bottom-8 text-[200px] opacity-10 rotate-12 transition-transform group-hover:rotate-0">church</span>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm border border-outline-variant/10">
            <p class="text-[11px] font-bold uppercase tracking-widest text-outline mb-4">Estado del Contrato</p>
            <div class="flex items-center gap-3">
                <?php $estado = strtolower($contrato['estado'] ?? 'activo'); ?>
                <div class="w-3 h-3 rounded-full <?= $estado === 'cancelado' ? 'bg-red-500' : ($estado === 'pendiente' ? 'bg-amber-500' : 'bg-green-500') ?>"></div>
                <span class="text-sm font-bold text-secondary"><?= ucfirst($estado) ?> & Confirmado</span>
            </div>
        </div>
    </div>

    <!-- RIGHT COL (3-wide) -->
    <div class="xl:col-span-3 space-y-8">
        <!-- 3 Cards Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Saldo -->
            <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm border border-outline-variant/10 flex flex-col justify-between group hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-outline mb-1">Saldo Pendiente</p>
                        <h2 class="text-3xl font-black text-secondary"><?= fmoney($saldo, $moneda) ?></h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary-container text-2xl">payments</span>
                    </div>
                </div>
                <div>
                    <?php if ($saldo > 0): ?>
                    <div class="bg-error-container/10 p-3 rounded-xl mb-4 border border-error/5">
                        <p class="text-xs font-bold text-error flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">event_busy</span> Total: <?= fmoney($valor_total, $moneda) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    <a href="<?= Router::url('/client/payments') ?>" class="block w-full py-3 bg-secondary text-white rounded-xl font-bold text-sm text-center hover:bg-primary transition-all active:scale-95 shadow-lg">Pagar Cuota</a>
                </div>
            </div>

            <!-- Vuelo -->
            <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm border border-outline-variant/10 flex flex-col justify-between">
                <p class="text-[11px] font-bold uppercase tracking-widest text-outline mb-4">Próximo Vuelo</p>
                <?php if ($vuelo): ?>
                <div class="flex items-center justify-between mb-8">
                    <div class="text-center">
                        <p class="text-3xl font-black text-secondary"><?= htmlspecialchars(strtoupper(substr($vuelo['origen'] ?? 'PCL', 0, 3))) ?></p>
                        <p class="text-[10px] font-bold text-outline"><?= htmlspecialchars($vuelo['origen'] ?? '') ?></p>
                    </div>
                    <div class="flex-1 flex flex-col items-center px-4">
                        <div class="w-full h-px bg-outline-variant relative">
                            <span class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-primary bg-surface-container-lowest px-2">flight_takeoff</span>
                        </div>
                        <p class="text-[10px] font-bold text-primary mt-3 tracking-widest"><?= htmlspecialchars($vuelo['numero_vuelo'] ?? $vuelo['aerolinea'] ?? '') ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-black text-secondary"><?= htmlspecialchars(strtoupper(substr($vuelo['destino'] ?? 'LIM', 0, 3))) ?></p>
                        <p class="text-[10px] font-bold text-outline"><?= htmlspecialchars($vuelo['destino'] ?? '') ?></p>
                    </div>
                </div>
                <div class="flex justify-between items-center py-3 px-4 bg-slate-50 rounded-2xl">
                    <div class="flex items-center gap-2 text-sm font-bold text-secondary">
                        <span class="material-symbols-outlined text-primary">calendar_today</span>
                        <?= !empty($vuelo['fecha_salida']) ? date('d M', strtotime($vuelo['fecha_salida'])) : '-' ?>
                    </div>
                    <div class="flex items-center gap-2 text-sm font-bold text-secondary">
                        <span class="material-symbols-outlined text-primary">schedule</span>
                        <?= htmlspecialchars($vuelo['hora_salida'] ?? '--:--') ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="flex-1 flex items-center justify-center"><p class="text-sm text-outline">Sin información de vuelo</p></div>
                <?php endif; ?>
            </div>

            <!-- Alojamiento -->
            <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm border border-outline-variant/10 relative overflow-hidden group">
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-outline mb-4">Alojamiento</p>
                        <?php if ($hotel): ?>
                        <h3 class="text-xl font-black text-secondary mb-1"><?= htmlspecialchars($hotel['nombre'] ?? $hotel['descripcion'] ?? 'Hotel') ?></h3>
                        <p class="text-sm font-semibold text-primary mb-3"><?= htmlspecialchars($hotel['tipo'] ?? 'Hospedaje') ?></p>
                        <?php else: ?>
                        <h3 class="text-xl font-black text-secondary mb-1">Por confirmar</h3>
                        <p class="text-sm font-semibold text-primary mb-3">Alojamiento</p>
                        <?php endif; ?>
                    </div>
                    <a href="<?= Router::url('/client/services') ?>" class="mt-6 text-secondary text-sm font-bold flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                        Ver Detalles <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
                <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-[120px] text-slate-100 group-hover:text-cyan-50 transition-colors">hotel</span>
            </div>
        </div>

        <!-- Passengers + Timeline -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <div class="lg:col-span-3">
                <h3 class="text-xl font-black text-secondary mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">group</span> Pasajeros del Contrato
                </h3>
                <?php if (!empty($pasajeros)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($pasajeros as $i => $p):
                        $ini = strtoupper(substr($p['nombre'] ?? '', 0, 1) . substr($p['apellido'] ?? '', 0, 1));
                    ?>
                    <div class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-3xl hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl <?= $i === 0 ? 'bg-primary-container/20 text-primary' : 'bg-secondary-container/20 text-secondary' ?> flex items-center justify-center font-black text-lg"><?= $ini ?></div>
                            <div>
                                <p class="font-bold text-secondary"><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></p>
                                <p class="text-xs text-outline font-medium"><?= $i === 0 ? 'Titular del Viaje' : 'Pasajero Acompañante' ?></p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-[9px] font-black uppercase tracking-widest rounded-lg">Confirmado</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="bg-white border border-slate-100 rounded-3xl p-8 text-center"><p class="text-sm text-outline">No hay pasajeros registrados.</p></div>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-2">
                <h3 class="text-xl font-black text-secondary mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">history_toggle_off</span> Últimas Actualizaciones
                </h3>
                <div class="bg-white border border-slate-100 rounded-3xl p-6 relative overflow-hidden">
                    <div class="relative pl-8 space-y-8 before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                        <?php if (!empty($pagos)): foreach (array_slice($pagos, 0, 3) as $pg): ?>
                        <div class="relative">
                            <span class="absolute -left-8 top-1 w-6 h-6 rounded-full bg-primary flex items-center justify-center border-4 border-white shadow-sm">
                                <span class="material-symbols-outlined text-[10px] text-white">payments</span>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-secondary">Pago: <?= htmlspecialchars($pg['concepto'] ?? 'Cuota') ?></p>
                                <p class="text-[11px] text-outline font-medium mt-0.5"><?= !empty($pg['created_at']) ? date('d M Y', strtotime($pg['created_at'])) : '' ?> · <?= ucfirst($pg['estado'] ?? '') ?></p>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                        <?php if ($vuelo): ?>
                        <div class="relative">
                            <span class="absolute -left-8 top-1 w-6 h-6 rounded-full bg-primary-container flex items-center justify-center border-4 border-white shadow-sm">
                                <span class="material-symbols-outlined text-[10px] text-white">flight</span>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-secondary">Vuelo confirmado</p>
                                <p class="text-[11px] text-outline font-medium mt-0.5"><?= htmlspecialchars($vuelo['aerolinea'] ?? '') ?> · <?= htmlspecialchars($vuelo['numero_vuelo'] ?? '') ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="relative">
                            <span class="absolute -left-8 top-1 w-6 h-6 rounded-full bg-secondary-container flex items-center justify-center border-4 border-white shadow-sm">
                                <span class="material-symbols-outlined text-[10px] text-secondary">description</span>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-secondary">Contrato creado</p>
                                <p class="text-[11px] text-outline font-medium mt-0.5"><?= $codigo ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHATBOT -->
<div class="fixed bottom-24 lg:bottom-8 right-8 z-[60] flex flex-col items-end gap-4">
    <div class="bg-white/95 backdrop-blur-md p-4 rounded-3xl shadow-2xl border border-primary-container/20 max-w-[280px] mb-2 hidden md:block" id="chatBubble">
        <p class="text-xs font-black text-primary flex items-center gap-2 mb-2">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> AventuraBot en línea
        </p>
        <p class="text-sm text-secondary font-medium leading-relaxed">¡Hola <?= $nombre ?>! Estoy listo para ayudarte con cualquier detalle de tu viaje.</p>
    </div>
    <button onclick="document.getElementById('chatBubble').classList.toggle('hidden')" class="w-16 h-16 bg-gradient-to-br from-primary to-primary-container text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all">
        <span class="material-symbols-outlined text-3xl">smart_toy</span>
    </button>
</div>

<?php else: ?>
<div class="max-w-lg mx-auto mt-20 bg-white rounded-[2rem] p-12 text-center shadow-sm border border-outline-variant/10">
    <span class="material-symbols-outlined text-6xl text-primary-container mb-4 block">flight_takeoff</span>
    <h2 class="text-2xl font-black text-secondary mb-2">Sin contratos activos</h2>
    <p class="text-outline">Contacta a tu asesor de viajes para activar tu próxima aventura.</p>
</div>
<?php endif; ?>
