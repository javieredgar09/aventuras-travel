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
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex justify-between items-center">
                <div class="flex items-center gap-2 text-sm font-bold text-petroleo tracking-wide uppercase">
                    <span class="material-symbols-outlined text-blue-500 text-[18px]">luggage</span>
                    Servicios Incluidos
                </div>
            </div>
            <div class="p-4">
                <?php if(empty($grupo['servicios'])): ?>
                    <p class="text-xs text-petroleo/40">No hay servicios registrados para este grupo.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach($grupo['servicios'] as $svc): 
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
                                        <?php if(isset($detalles['hoteles']) || isset($detalles['vuelos'])): ?>
                                            <!-- Múltiples tramos -->
                                            <?php 
                                            $arrayName = isset($detalles['hoteles']) ? 'hoteles' : 'vuelos';
                                            foreach($detalles[$arrayName] as $i => $item): ?>
                                                <li class="mb-1.5 pb-1.5 border-b border-petroleo/5 last:border-0 last:mb-0 last:pb-0">
                                                    <div class="font-bold text-[10px] text-turquesa-dark uppercase">Tramo <?= $i+1 ?></div>
                                                    <?php foreach($item as $k => $v): ?>
                                                        <span class="block"><span class="font-medium opacity-60 capitalize"><?= str_replace('_', ' ', $k) ?>:</span> <?= htmlspecialchars($v) ?></span>
                                                    <?php endforeach; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <!-- Servicio Single -->
                                            <?php foreach($detalles as $k => $v): if(is_array($v)) continue; ?>
                                                <li><strong class="capitalize opacity-60 font-medium"><?= str_replace('_', ' ', $k) ?>:</strong> <?= htmlspecialchars($v) ?></li>
                                            <?php endforeach; ?>
                                            <!-- Arrays Dinámicos Simples (Ej: Tours) -->
                                            <?php foreach($detalles as $k => $v): if(!is_array($v)) continue; ?>
                                                <li><strong class="capitalize opacity-60 font-medium"><?= str_replace('_', ' ', $k) ?>:</strong>
                                                    <ul class="list-disc pl-3 mt-1 text-[11px] space-y-0.5">
                                                        <?php foreach($v as $item): ?>
                                                            <li><?= is_array($item) ? implode(' - ', $item) : $item ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

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
