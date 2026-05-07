<!-- admin/sales/show.php -->
<?php
$grupo = $grupo ?? [];
$serviceMeta = $serviceMeta ?? [];
$esColegio = $grupo['tipo'] === 'colegio';
$iconoTipo = $esColegio ? 'school' : 'family_restroom';
$colorTipo = $esColegio ? 'text-indigo-600 bg-indigo-50 border-indigo-200' : 'text-orange-600 bg-orange-50 border-orange-200';
?>

<!-- Cabecera Corta -->
<div class="mb-4 flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-white p-4 rounded-xl border border-petroleo/5 shadow-sm">
    <div class="flex items-start gap-4">
        <a href="<?= Router::url('/admin/sales') ?>" class="w-8 h-8 shrink-0 rounded-full bg-superficie text-petroleo flex items-center justify-center hover:bg-turquesa hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-xl text-petroleo font-black border-r border-petroleo/10 pr-3"><?= htmlspecialchars($grupo['nombre']) ?></h1>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border <?= $colorTipo ?>">
                    <span class="material-symbols-outlined text-[12px]"><?= $iconoTipo ?></span>
                    <?= $esColegio ? 'Colegio' : 'Familiar' ?>
                </span>
            </div>
            <p class="text-xs text-petroleo/60 flex items-center gap-2">
                <span class="material-symbols-outlined text-[14px]">location_on</span> <?= htmlspecialchars($grupo['destino']) ?>
                <?php if($grupo['fecha_viaje']): ?>
                    <span class="px-1 text-petroleo/20">|</span>
                    <span class="material-symbols-outlined text-[14px]">calendar_month</span> <?= date('d M Y', strtotime($grupo['fecha_viaje'])) ?>
                <?php endif; ?>
                <?php if($grupo['operador']): ?>
                    <span class="px-1 text-petroleo/20">|</span>
                    <span class="material-symbols-outlined text-[14px]">corporate_fare</span> <?= htmlspecialchars($grupo['operador']) ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    
    <div class="flex shrink-0 gap-2">
        <a href="<?= Router::url('/admin/sales/' . $grupo['id'] . '/edit') ?>" class="px-3 py-1.5 rounded text-xs font-bold text-petroleo border border-petroleo/10 hover:bg-superficie transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">edit</span> Editar
        </a>
        
        <!-- Eliminar Grupo -->
        <form action="<?= Router::url('/admin/sales/delete/' . $grupo['id']) ?>" method="POST" data-confirm="¿Estás seguro de que deseas eliminar este grupo y TODOS sus datos relacionados (pagos, pasajeros, servicios)? Esta acción no se puede deshacer." class="inline">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <button type="submit" class="px-3 py-1.5 rounded text-xs font-bold text-red-600 border border-red-100 bg-red-50 hover:bg-red-100 transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">delete</span> Eliminar
            </button>
        </form>

        <?php if($esColegio): ?>
            <!-- Añadir Contrato -->
            <button onclick="document.getElementById('modalContrato').classList.remove('hidden')" class="px-3 py-1.5 rounded text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors flex items-center gap-1 shadow shadow-indigo-600/20">
                <span class="material-symbols-outlined text-[14px]">add_box</span> Nuevo Contrato
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-12">
    <!-- COLUMNA IZQUIERDA: Servicios y Contratos -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Tarjeta: Servicios Incluidos -->
        <?php
        // Separate services by type for better rendering
        $svcVuelos = null;
        $svcHotel = null;
        $svcOtros = [];
        if (!empty($grupo['servicios'])) {
            foreach ($grupo['servicios'] as $svc) {
                $tipo = strtolower($svc['servicio_tipo'] ?? '');
                if ($tipo === 'vuelos' || $tipo === 'vuelo') {
                    $svcVuelos = $svc;
                } elseif ($tipo === 'hotel' || $tipo === 'alojamiento') {
                    $svcHotel = $svc;
                } else {
                    $svcOtros[] = $svc;
                }
            }
        }

        // Airline IATA mapping for logos
        $airlineMap = [
            'latam' => ['code' => 'LA', 'color' => '#1B0088'],
            'copa' => ['code' => 'CM', 'color' => '#003876'],
            'avianca' => ['code' => 'AV', 'color' => '#E31837'],
            'jetsmart' => ['code' => 'JA', 'color' => '#FF6600'],
            'american' => ['code' => 'AA', 'color' => '#0078D2'],
            'delta' => ['code' => 'DL', 'color' => '#003366'],
            'united' => ['code' => 'UA', 'color' => '#002244'],
            'spirit' => ['code' => 'NK', 'color' => '#FFD700'],
            'arajet' => ['code' => 'DM', 'color' => '#E4002B'],
            'aeromexico' => ['code' => 'AM', 'color' => '#002D5F'],
            'viva' => ['code' => 'VV', 'color' => '#E4002B'],
            'sky' => ['code' => 'H2', 'color' => '#00A651'],
            'volaris' => ['code' => 'Y4', 'color' => '#6D2077'],
            'iberia' => ['code' => 'IB', 'color' => '#D4213D'],
            'air europa' => ['code' => 'UX', 'color' => '#003399'],
            'wingo' => ['code' => 'P5', 'color' => '#24B74F'],
            'star peru' => ['code' => '2I', 'color' => '#B22234'],
            'peruvian' => ['code' => 'P9', 'color' => '#D4213D'],
            'gol' => ['code' => 'G3', 'color' => '#FF6600'],
            'azul' => ['code' => 'AD', 'color' => '#003DA5'],
        ];
        if (!function_exists('getAlData')) {
            function getAlData(string $airline, array $map): array {
                $lower = strtolower(trim($airline));
                foreach ($map as $key => $info) {
                    if (strpos($lower, $key) !== false) return $info;
                }
                return ['code' => '', 'color' => '#1B3A4B'];
            }
        }
        ?>

        <!-- HOTEL -->
        <?php if ($svcHotel):
            $hotelDet = json_decode($svcHotel['detalle_json'] ?? '{}', true) ?: [];
        ?>
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-500 text-[18px]">hotel</span>
                <span class="text-sm font-bold text-petroleo tracking-wide uppercase">Hotel</span>
            </div>
            <div class="p-4">
                <div class="flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-blue-500 text-2xl">apartment</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-black text-petroleo mb-0.5"><?= htmlspecialchars($hotelDet['nombre'] ?? $hotelDet['hotel'] ?? '-') ?></h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3">
                            <?php if (!empty($hotelDet['tipo_habitacion'])): ?>
                            <div class="p-2.5 bg-humo/40 rounded-lg">
                                <p class="text-[9px] font-bold text-petroleo/40 uppercase tracking-wider">Habitación</p>
                                <p class="text-xs font-bold text-petroleo mt-0.5"><?= htmlspecialchars($hotelDet['tipo_habitacion']) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($hotelDet['noches'])): ?>
                            <div class="p-2.5 bg-humo/40 rounded-lg">
                                <p class="text-[9px] font-bold text-petroleo/40 uppercase tracking-wider">Noches</p>
                                <p class="text-xs font-bold text-petroleo mt-0.5"><?= htmlspecialchars($hotelDet['noches']) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($hotelDet['regimen'])): ?>
                            <div class="p-2.5 bg-humo/40 rounded-lg">
                                <p class="text-[9px] font-bold text-petroleo/40 uppercase tracking-wider">Régimen</p>
                                <p class="text-xs font-bold text-petroleo mt-0.5"><?= htmlspecialchars($hotelDet['regimen']) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php
                            // Show any other details not already displayed
                            $hotelShown = ['nombre','hotel','tipo_habitacion','noches','regimen'];
                            foreach ($hotelDet as $hk => $hv):
                                if (in_array($hk, $hotelShown) || is_array($hv)) continue;
                            ?>
                            <div class="p-2.5 bg-humo/40 rounded-lg">
                                <p class="text-[9px] font-bold text-petroleo/40 uppercase tracking-wider capitalize"><?= str_replace('_', ' ', $hk) ?></p>
                                <p class="text-xs font-bold text-petroleo mt-0.5"><?= htmlspecialchars($hv) ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- VUELOS -->
        <?php if ($svcVuelos):
            $vuelosDet = json_decode($svcVuelos['detalle_json'] ?? '{}', true) ?: [];
            $tramos = $vuelosDet['vuelos'] ?? [];
        ?>
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-500 text-[18px]">flight_takeoff</span>
                    <span class="text-sm font-bold text-petroleo tracking-wide uppercase">Vuelos</span>
                    <span class="text-[10px] font-bold text-petroleo/40 ml-1">(<?= count($tramos) ?> tramo<?= count($tramos) !== 1 ? 's' : '' ?>)</span>
                </div>
            </div>
            <div class="p-4 space-y-3">
                <?php if (empty($tramos)): ?>
                    <p class="text-xs text-petroleo/40">Sin vuelos registrados.</p>
                <?php else: ?>
                    <?php foreach ($tramos as $ti => $tramo):
                        $aerolinea = $tramo['aerolinea'] ?? $tramo['Aerolinea'] ?? '';
                        $numero = $tramo['numero'] ?? $tramo['numero_vuelo'] ?? $tramo['Numero'] ?? '';
                        $ruta = $tramo['ruta'] ?? $tramo['Ruta'] ?? '';
                        $salida = $tramo['salida'] ?? $tramo['fecha_salida'] ?? $tramo['Salida'] ?? '';
                        $llegada = $tramo['llegada'] ?? $tramo['fecha_llegada'] ?? $tramo['Llegada'] ?? '';
                        $origen = $tramo['origen'] ?? $tramo['Origen'] ?? '';
                        $destino_v = $tramo['destino'] ?? $tramo['Destino'] ?? '';
                        $origen_ciudad = $tramo['origen_ciudad'] ?? $tramo['Origen Ciudad'] ?? $tramo['origen ciudad'] ?? '';
                        $destino_ciudad = $tramo['destino_ciudad'] ?? $tramo['Destino Ciudad'] ?? $tramo['destino ciudad'] ?? '';

                        // Parse origin/destination from route if not set separately
                        if (empty($origen) && !empty($ruta) && strpos($ruta, '-') !== false) {
                            $parts = explode('-', $ruta);
                            $origen = trim($parts[0] ?? '');
                            $destino_v = trim($parts[1] ?? '');
                        }

                        // Parse dates
                        $salidaTime = !empty($salida) ? strtotime($salida) : null;
                        $llegadaTime = !empty($llegada) ? strtotime($llegada) : null;
                        $horaSalida = $salidaTime ? date('H:i', $salidaTime) : '--:--';
                        $horaLlegada = $llegadaTime ? date('H:i', $llegadaTime) : '--:--';
                        $fechaSalida = $salidaTime ? date('d M Y', $salidaTime) : '';
                        $durH = ($salidaTime && $llegadaTime && ($llegadaTime - $salidaTime) > 0) ? round(($llegadaTime - $salidaTime) / 3600, 1) : null;

                        $alData = getAlData($aerolinea, $airlineMap);
                        $alCode = strtoupper($alData['code'] ?: mb_substr($aerolinea, 0, 2));
                        $alColor = $alData['color'];
                        $logoUrl = $alData['code'] ? 'https://pics.avs.io/200/80/' . urlencode($alData['code']) . '.png' : '';
                    ?>
                    <div class="relative bg-white rounded-xl border border-petroleo/5 overflow-hidden hover:shadow-md transition-all group">
                        <!-- Accent bar -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-xl" style="background:<?= $alColor ?>"></div>

                        <div class="p-4 pl-5">
                            <!-- Header row: Airline + Flight number + Tramo badge -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <?php if ($logoUrl): ?>
                                    <img src="<?= $logoUrl ?>" alt="<?= htmlspecialchars($aerolinea) ?>" class="h-5 object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-[8px] font-black shrink-0" style="background:<?= $alColor ?>;display:none"><?= $alCode ?></div>
                                    <?php else: ?>
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-[8px] font-black shrink-0" style="background:<?= $alColor ?>"><?= $alCode ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="text-xs font-bold text-petroleo"><?= htmlspecialchars($aerolinea) ?></p>
                                        <p class="text-[10px] text-petroleo/40 font-mono"><?= htmlspecialchars($numero) ?></p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-600 border border-indigo-100">
                                    Tramo <?= $ti + 1 ?>
                                </span>
                            </div>

                            <!-- Route: ORG ----✈---- DST -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="text-center min-w-[50px]">
                                    <p class="text-xl font-black text-petroleo tracking-tight"><?= htmlspecialchars(strtoupper($origen)) ?></p>
                                    <?php if ($origen_ciudad): ?>
                                    <p class="text-[9px] text-petroleo/50 font-medium truncate max-w-[80px]"><?= htmlspecialchars($origen_ciudad) ?></p>
                                    <?php endif; ?>
                                    <p class="text-xs font-bold text-turquesa mt-0.5"><?= $horaSalida ?></p>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-0.5 px-2">
                                    <?php if ($durH): ?>
                                    <span class="text-[9px] text-petroleo/40 font-medium"><?= $durH ?>h</span>
                                    <?php endif; ?>
                                    <div class="w-full flex items-center gap-1">
                                        <div class="w-1.5 h-1.5 rounded-full border-2" style="border-color:<?= $alColor ?>"></div>
                                        <div class="h-px flex-1 border-t-2 border-dashed" style="border-color:<?= $alColor ?>30"></div>
                                        <span class="material-symbols-outlined text-sm shrink-0" style="color:<?= $alColor ?>;font-variation-settings:'FILL' 1">flight</span>
                                        <div class="h-px flex-1 border-t-2 border-dashed" style="border-color:<?= $alColor ?>30"></div>
                                        <div class="w-1.5 h-1.5 rounded-full shrink-0" style="background:<?= $alColor ?>"></div>
                                    </div>
                                    <?php if ($fechaSalida): ?>
                                    <span class="text-[9px] text-petroleo/40"><?= $fechaSalida ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-center min-w-[50px]">
                                    <p class="text-xl font-black text-petroleo tracking-tight"><?= htmlspecialchars(strtoupper($destino_v)) ?></p>
                                    <?php if ($destino_ciudad): ?>
                                    <p class="text-[9px] text-petroleo/50 font-medium truncate max-w-[80px]"><?= htmlspecialchars($destino_ciudad) ?></p>
                                    <?php endif; ?>
                                    <p class="text-xs font-bold text-turquesa mt-0.5"><?= $horaLlegada ?></p>
                                </div>
                            </div>

                            <!-- Footer: Date details -->
                            <div class="flex flex-wrap items-center gap-4 pt-2.5 border-t border-dashed border-petroleo/10 text-[10px] text-petroleo/50">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                                    <span class="font-bold"><?= $fechaSalida ?: '—' ?></span>
                                </span>
                                <?php if ($horaSalida !== '--:--'): ?>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">schedule</span>
                                    Sale: <span class="font-bold"><?= $horaSalida ?></span>
                                </span>
                                <?php endif; ?>
                                <?php if ($horaLlegada !== '--:--'): ?>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">flight_land</span>
                                    Llega: <span class="font-bold"><?= $horaLlegada ?></span>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- OTROS SERVICIOS -->
        <?php if (!empty($svcOtros)): ?>
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500 text-[18px]">luggage</span>
                <span class="text-sm font-bold text-petroleo tracking-wide uppercase">Otros Servicios</span>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php foreach ($svcOtros as $svc):
                        $tipo = $svc['servicio_tipo'];
                        $meta = $serviceMeta[$tipo] ?? ['icon'=>'info', 'label'=>ucfirst($tipo), 'emoji'=>'✔️'];
                        $detalles = json_decode($svc['detalle_json'], true) ?: [];
                    ?>
                    <div class="p-3 rounded-lg bg-humo/30 border border-petroleo/5 flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shrink-0 shadow-sm border border-petroleo/5 text-[16px]">
                            <?= $meta['emoji'] ?>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-petroleo uppercase text-[10px] tracking-wider mb-1.5"><?= $meta['label'] ?></h3>
                            <ul class="text-xs text-petroleo/70 space-y-1">
                                <?php foreach ($detalles as $k => $v):
                                    if (is_array($v)) continue;
                                ?>
                                    <li><strong class="capitalize opacity-60 font-medium"><?= str_replace('_', ' ', $k) ?>:</strong> <?= htmlspecialchars($v) ?></li>
                                <?php endforeach; ?>
                                <?php foreach ($detalles as $k => $v):
                                    if (!is_array($v)) continue;
                                ?>
                                    <li><strong class="capitalize opacity-60 font-medium"><?= str_replace('_', ' ', $k) ?>:</strong>
                                        <ul class="list-disc pl-3 mt-1 text-[11px] space-y-0.5">
                                            <?php foreach ($v as $item): ?>
                                                <li><?= is_array($item) ? implode(' - ', $item) : htmlspecialchars($item) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- No services message -->
        <?php if (empty($grupo['servicios'])): ?>
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-500 text-[18px]">luggage</span>
                <span class="text-sm font-bold text-petroleo tracking-wide uppercase">Servicios Incluidos</span>
            </div>
            <div class="p-6 text-center">
                <p class="text-xs text-petroleo/40">No hay servicios registrados para este grupo.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tarjeta: Vouchers -->
        <?php 
        $vouchers = $vouchers ?? [];
        $ppagado = $grupo['valor_total'] > 0 ? min(100, round(($grupo['total_pagado'] / $grupo['valor_total']) * 100)) : 0;
        ?>
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden mt-6">
            <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">description</span>
                    <h2 class="font-bold text-petroleo">Documentos de Viaje (Vouchers)</h2>
                </div>
                <?php if($ppagado >= 100): ?>
                    <button onclick="document.getElementById('modalVoucher').classList.remove('hidden')" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-sm">upload_file</span> Subir Voucher
                    </button>
                <?php else: ?>
                    <span class="text-[10px] text-petroleo/40 uppercase font-bold tracking-wider" title="Requiere pago 100%">Bloqueado (100% Pago Req.)</span>
                <?php endif; ?>
            </div>
            <div class="p-4">
                <?php if(empty($vouchers)): ?>
                    <p class="text-xs text-petroleo/40 text-center py-4">No hay vouchers subidos.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach($vouchers as $v): ?>
                            <div class="flex items-center gap-3 p-3 bg-humo/30 border border-petroleo/5 rounded-lg">
                                <div class="w-10 h-10 rounded bg-white border border-petroleo/10 flex items-center justify-center text-turquesa shrink-0">
                                    <span class="material-symbols-outlined">description</span>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="font-bold text-[11px] text-petroleo truncate"><?= htmlspecialchars($v['titulo']) ?></p>
                                    <p class="text-[10px] text-petroleo/50 uppercase tracing-wider"><?= htmlspecialchars($v['tipo_entidad'] . ' ' . $v['tipo_voucher']) ?></p>
                                </div>
                                <a href="<?= Router::url('/storage/vouchers/' . $v['archivo_url']) ?>" target="_blank" class="w-8 h-8 flex items-center justify-center bg-white rounded-md border border-petroleo/10 text-petroleo/60 hover:text-turquesa transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tarjeta: Contratos (Solo Colegio) -->
        <?php if($esColegio): ?>
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-petroleo/10">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-petroleo">request_quote</span>
                    <h2 class="text-lg font-black text-petroleo">Contratos del Colegio</h2>
                </div>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['rol'] === 'admin'): ?>
                <a href="<?= Router::url('/admin/sales/' . $grupo['id'] . '/contract/create') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-petroleo rounded-lg hover:bg-turquesa transition-colors">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Nuevo Contrato
                </a>
                <?php endif; ?>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-[10px] text-petroleo/40 uppercase tracking-widest bg-petroleo/5 border-b border-petroleo/10">
                            <th class="px-3 py-2 font-bold">Código</th>
                            <th class="px-3 py-2 font-bold text-center">Pasajeros</th>
                            <th class="px-3 py-2 font-bold text-right">Valor</th>
                            <th class="px-3 py-2 font-bold text-right">Pagado</th>
                            <th class="px-3 py-2 font-bold text-right">Saldo</th>
                            <th class="px-3 py-2 font-bold text-center">Estado</th>
                            <th class="px-3 py-2 font-bold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-petroleo/5">
                        <?php if(empty($grupo['contratos'])): ?>
                            <tr><td colspan="6" class="p-4 text-center text-petroleo/40">No hay contratos registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach($grupo['contratos'] as $c): 
                                $pagado = (float)$c['pagado'];
                                $total = (float)$c['valor_total'];
                                $saldo = $total - $pagado;
                                $porcentaje = $total > 0 ? min(100, round(($pagado / $total) * 100)) : 0;
                            ?>
                            <tr class="hover:bg-humo/50 transition-colors">
                                <td class="px-3 py-2 font-bold text-petroleo"><?= htmlspecialchars($c['codigo']) ?></td>
                                <td class="px-3 py-2 text-center">
                                    <span class="inline-flex items-center gap-1 bg-superficie px-1.5 py-0.5 rounded text-[10px] font-bold text-petroleo">
                                        <span class="material-symbols-outlined text-[12px]">groups</span> <?= $c['num_pasajeros'] ?>
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right font-bold">$<?= number_format($total, 2) ?></td>
                                <td class="px-3 py-2 text-right text-emerald-600 font-medium">$<?= number_format($pagado, 2) ?></td>
                                <td class="px-3 py-2 text-right text-amber-600 font-medium">$<?= number_format($saldo, 2) ?></td>
                                <td class="px-3 py-2 text-center">
                                    <div class="w-16 mx-auto bg-humo rounded-full h-1.5 overflow-hidden mb-1">
                                        <div class="bg-turquesa h-full" style="width: <?= $porcentaje ?>%"></div>
                                    </div>
                                    <span class="text-[9px] uppercase font-bold text-petroleo/60"><?= $porcentaje ?>%</span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <a href="<?= Router::url('/admin/contracts/' . $c['id']) ?>" class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-bold text-white bg-indigo-600 rounded hover:bg-indigo-700 transition-colors shadow-sm">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        Ver / Gestionar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tarjeta: Pasajeros (Si es familiar) -->
        <?php if(!$esColegio): ?>
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-orange-500">groups</span>
                    <h2 class="font-bold text-petroleo">Lista de Pasajeros</h2>
                </div>
                <button onclick="document.getElementById('modalPasajero').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-petroleo hover:bg-turquesa transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">person_add</span> Agregar
                </button>
            </div>
            
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="text-[10px] text-petroleo/40 uppercase tracking-widest bg-petroleo/5 border-b border-petroleo/10">
                        <th class="px-3 py-2 font-bold">Pasajero</th>
                        <th class="px-3 py-2 font-bold">Tipo</th>
                        <th class="px-3 py-2 font-bold">Pasaporte / DNI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-petroleo/5">
                    <?php if(empty($grupo['pasajeros'])): ?>
                        <tr><td colspan="3" class="p-4 text-center text-petroleo/40">No hay pasajeros registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach($grupo['pasajeros'] as $p): ?>
                        <tr class="hover:bg-humo/50">
                            <td class="px-3 py-2 font-bold text-petroleo">
                                <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>
                                <?php if($p['edad']): ?> <span class="text-[10px] text-petroleo/40 font-normal ml-1">(<?= $p['edad'] ?>a)</span><?php endif; ?>
                            </td>
                            <td class="px-3 py-2">
                                <span class="capitalize px-1.5 py-0.5 rounded bg-superficie text-[10px] font-bold text-petroleo"><?= htmlspecialchars($p['tipo']) ?></span>
                            </td>
                            <td class="px-3 py-2 font-mono text-[10px]"><?= htmlspecialchars($p['pasaporte'] ?: '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>

    <!-- COLUMNA DERECHA: Economía y Pagos -->
    <div class="space-y-6">
        
        <!-- Estado Económico General -->
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden sticky top-24">
            <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">account_balance_wallet</span>
                    <h2 class="font-bold text-petroleo">Resumen Económico</h2>
                </div>
            </div>
            
            <div class="p-4">
                <!-- Bar Chart -->
                <?php
                $ppagado = $grupo['valor_total'] > 0 ? min(100, round(($grupo['total_pagado'] / $grupo['valor_total']) * 100)) : 0;
                ?>
                <div class="flex justify-between items-end mb-2">
                    <span class="text-2xl font-black text-emerald-600"><?= $ppagado ?>%</span>
                    <span class="text-[10px] font-bold text-petroleo/40 uppercase tracking-widest">Abonado</span>
                </div>
                <div class="w-full bg-humo rounded-full h-2 overflow-hidden mb-4">
                    <div class="bg-emerald-500 h-full transition-all duration-1000" style="width: <?= $ppagado ?>%"></div>
                </div>

                <!-- Stats -->
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center p-2 rounded-lg bg-humo/30">
                        <span class="text-petroleo/60 font-medium">Valor Total</span>
                        <span class="font-black text-petroleo">$<?= number_format($grupo['valor_total'], 2) ?></span>
                    </div>
                    <div class="flex justify-between items-center p-2 rounded-lg bg-emerald-50 text-emerald-800">
                        <span class="font-medium">Total Pagado</span>
                        <span class="font-black">$<?= number_format($grupo['total_pagado'], 2) ?></span>
                    </div>
                    <div class="flex justify-between items-center p-2 rounded-lg bg-amber-50 text-amber-800 border border-amber-100">
                        <span class="font-medium">Saldo Pendiente</span>
                        <span class="font-black">$<?= number_format($grupo['saldo_pendiente'], 2) ?></span>
                    </div>
                </div>

                <hr class="my-4 border-petroleo/10">

                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-petroleo/60 text-[16px]">history</span>
                    <h3 class="font-bold text-petroleo text-xs uppercase tracking-wider">Historial</h3>
                </div>

                <div class="space-y-3 mb-6">
                    <?php if(empty($grupo['pagos'])): ?>
                        <p class="text-xs text-petroleo/40 italic">Ningún pago registrado.</p>
                    <?php else: ?>
                        <?php foreach($grupo['pagos'] as $p): 
                            $esAprobado = $p['estado'] === 'aprobado';
                            $iconoPago = $esAprobado ? 'check_circle' : 'pending_actions';
                            $colorPago = $esAprobado ? 'text-emerald-500' : 'text-amber-500';
                            $bgPago = $esAprobado ? 'bg-emerald-50' : 'bg-amber-50';
                        ?>
                        <div class="p-3 rounded-lg border border-petroleo/5 flex items-start gap-3 bg-white hover:bg-superficie transition-colors">
                            <span class="material-symbols-outlined mt-0.5 <?= $colorPago ?> text-[20px]"><?= $iconoPago ?></span>
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <p class="font-bold text-petroleo text-sm"><?= htmlspecialchars($p['concepto']) ?></p>
                                    <p class="font-bold <?= $colorPago ?>">$<?= number_format($p['monto'], 2) ?></p>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-[11px] text-petroleo/40 font-medium">
                                        Vence: <?= date('d M', strtotime($p['fecha_vencimiento'])) ?>
                                        <?php if($esColegio && isset($p['contrato_codigo'])): ?>
                                            &bull; Contrato: <?= $p['contrato_codigo'] ?>
                                        <?php endif; ?>
                                    </p>
                                    <?php if(!$esAprobado): ?>
                                        <form action="<?= Router::url('/admin/sales/payment/approve/' . $p['id']) ?>" method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <button type="submit" class="text-[10px] font-bold bg-turquesa text-white px-2 py-0.5 rounded hover:bg-turquesa-dark">Marcar Pagado</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button onclick="document.getElementById('modalPago').classList.remove('hidden')" class="w-full py-3 rounded-xl bg-petroleo text-white font-bold hover:bg-turquesa shadow-md transition-all flex justify-center items-center gap-2">
                    <span class="material-symbols-outlined">payments</span> Registrar Nuevo Pago
                </button>
            </div>
        </div>

    </div>
</div>

<!-- MODAL: Nuevo Pago -->
<div id="modalPago" class="fixed inset-0 bg-petroleo/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden anim-fade-in">
        <div class="p-4 border-b border-petroleo/5 flex justify-between items-center bg-superficie/30">
            <h3 class="font-black text-petroleo flex items-center gap-2"><span class="material-symbols-outlined text-emerald-500">payments</span> Registrar Pago</h3>
            <button onclick="document.getElementById('modalPago').classList.add('hidden')" class="text-petroleo/40 hover:text-red-500"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form action="<?= Router::url('/admin/sales/payment') ?>" method="POST" class="p-4 space-y-3">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">

            <?php if($esColegio && !empty($grupo['contratos'])): ?>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Asignar a Contrato</label>
                    <select name="contrato_id" required class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 focus:border-turquesa outline-none bg-white font-mono text-xs">
                        <option value="">Seleccione contrato...</option>
                        <?php foreach($grupo['contratos'] as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['codigo'] ?> (Saldo: $<?= $c['valor_total'] - $c['pagado'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Concepto</label>
                <input type="text" name="concepto" required placeholder="Ej: Pago Cuota 3" class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Monto (USD)</label>
                    <input type="number" step="0.01" name="monto" required min="1" class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 font-bold text-xs focus:border-turquesa outline-none">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Fecha Venc.</label>
                    <input type="date" name="fecha_vencimiento" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-1.5 text-xs rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
                </div>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Estado del Pago</label>
                <select name="estado_pago" class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none bg-white">
                    <option value="aprobado">Pagado y Aprobado</option>
                    <option value="pendiente">Pendiente (Generar Recibo)</option>
                </select>
            </div>

            <button type="submit" class="w-full py-2 mt-2 rounded-lg bg-emerald-500 text-white text-sm font-bold shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 transition-colors">Guardar Pago</button>
        </form>
    </div>
</div>

<!-- MODAL: Nuevo Contrato (Solo Colegio) -->
<?php if($esColegio): ?>
<!-- Modal previously here was removed as we now use a dedicated page for contracts -->
<?php endif; ?>

<!-- MODAL: Añadir Pasajero -->
<div id="modalPasajero" class="fixed inset-0 bg-petroleo/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden anim-fade-in">
        <div class="p-4 border-b border-petroleo/5 flex justify-between items-center bg-superficie/30">
            <h3 class="font-black text-petroleo flex items-center gap-2"><span class="material-symbols-outlined text-orange-500">person_add</span> Añadir Pasajero</h3>
            <button onclick="document.getElementById('modalPasajero').classList.add('hidden')" class="text-petroleo/40 hover:text-red-500"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form action="<?= Router::url('/admin/sales/' . $grupo['id'] . '/passenger') ?>" method="POST" class="p-4 space-y-3">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <?php if($esColegio && !empty($grupo['contratos'])): ?>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Asignar a Contrato (Obligatorio)</label>
                    <select name="contrato_id" required class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none bg-white font-mono">
                        <?php foreach($grupo['contratos'] as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['codigo'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Nombres</label>
                    <input type="text" name="nombre" required class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Apellidos</label>
                    <input type="text" name="apellido" required class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Tipo</label>
                    <select name="tipo" class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none bg-white">
                        <option value="adulto">Adulto</option>
                        <option value="menor">Menor/Niño</option>
                        <option value="infante">Infante</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">DNI/Pasaporte</label>
                    <input type="text" name="pasaporte" class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none">
                </div>
            </div>

            <button type="submit" class="w-full py-2 mt-2 rounded-lg bg-turquesa text-white text-sm font-bold shadow-lg shadow-turquesa/30 hover:bg-turquesa-dark transition-colors">Guardar Pasajero</button>
        </form>
    </div>
</div>

<!-- MODAL: Subir Voucher -->
<div id="modalVoucher" class="fixed inset-0 bg-petroleo/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden anim-fade-in">
        <div class="p-4 border-b border-petroleo/5 flex justify-between items-center bg-superficie/30">
            <h3 class="font-black text-petroleo flex items-center gap-2"><span class="material-symbols-outlined text-emerald-500">upload_file</span> Subir Voucher</h3>
            <button onclick="document.getElementById('modalVoucher').classList.add('hidden')" class="text-petroleo/40 hover:text-red-500"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form action="<?= Router::url('/admin/sales/voucher') ?>" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
            
            <?php if($esColegio && !empty($grupo['contratos'])): ?>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Asignar a</label>
                    <select name="contrato_id" class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none bg-white">
                        <option value="0">Emisión Grupal (Todo el colegio)</option>
                        <?php foreach($grupo['contratos'] as $c): ?>
                            <option value="<?= $c['id'] ?>">Contrato: <?= $c['codigo'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Categoría</label>
                <select name="tipo_voucher" required class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none bg-white">
                    <option value="vuelos">Tickets Aéreos</option>
                    <option value="hotel">Confirmación Hotel / Voucher</option>
                    <option value="traslado">Voucher Traslados</option>
                    <option value="seguro">Póliza de Seguro</option>
                    <option value="excursion">Voucher de Excursiones</option>
                    <option value="general">Voucher General</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Título del Documento</label>
                <input type="text" name="titulo" required placeholder="Ej: Tickets LATAM - Familia Pérez" class="w-full px-3 py-1.5 rounded-lg border border-petroleo/20 text-xs focus:border-turquesa outline-none">
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Archivo PDF/JPG</label>
                <input type="file" name="archivo" required accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs font-bold text-petroleo">
            </div>

            <button type="submit" class="w-full py-2 mt-2 rounded-lg bg-emerald-500 text-white text-sm font-bold shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 transition-colors">Subir Documento</button>
        </form>
    </div>
</div>
