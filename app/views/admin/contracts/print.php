<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Contrato') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        body { font-family: 'Arial', sans-serif; font-size: 11px; line-height: 1.4; color: #1f2937; }
        .header-title { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 2px; }
        .section-title { font-size: 12px; font-weight: bold; background-color: #f3f4f6; padding: 4px 8px; margin-top: 15px; margin-bottom: 8px; border: 1px solid #e5e7eb; border-left: 4px solid #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 5px 8px; text-align: left; }
        th { background-color: #f9fafb; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        ul.bullet-list { list-style-type: disc; padding-left: 20px; margin-bottom: 10px; }
    </style>
</head>
<body class="bg-gray-100 py-8">

<div class="max-w-[800px] mx-auto bg-white p-10 shadow-lg no-print:mb-8" style="min-height: 1056px;">
    
    <!-- HEADER -->
    <div class="flex justify-between items-start mb-6 border-b-2 border-gray-800 pb-4">
        <div>
            <h1 class="header-title">📜 CONTRATO DE VIAJE N°: <?= htmlspecialchars($contrato['codigo'] ?? '') ?></h1>
            <p class="font-bold">AVENTURAS TRAVEL PUCALLPA</p>
            <p>RUC: 10475951587 | Javier Edgar Sandy Da Cruz | DNI: 47228319</p>
            <p>Dirección: Jirón Zavala 568A, Pucallpa</p>
            <p>Teléfono: 976324716 | Email: reservas.aventurastravelpcl@gmail.com</p>
        </div>
        <div class="text-right">
            <img src="/aventuras/img/a_color.png" alt="Aventuras Travel" class="h-16 inline-block" onerror="this.style.display='none'">
            <div class="mt-2 text-xs font-bold text-gray-500">Fecha de Firma: <?= $contrato['fecha_firma'] ? date('d/m/Y', strtotime($contrato['fecha_firma'])) : date('d/m/Y') ?></div>
        </div>
    </div>

    <!-- 1. TITULAR -->
    <div class="section-title">1. DATOS DEL TITULAR (RESPONSABLE DE PAGO)</div>
    <table>
        <tr>
            <th class="w-1/3">Nombre Completo</th>
            <td><?= htmlspecialchars($contrato['titular_nombre'] ?? '') ?></td>
        </tr>
        <tr>
            <th>DNI / Pasaporte</th>
            <td><?= htmlspecialchars($contrato['titular_documento'] ?? '') ?></td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td><?= htmlspecialchars($contrato['titular_telefono'] ?? '_________________') ?></td>
        </tr>
        <tr>
            <th>Correo Electrónico</th>
            <td><?= htmlspecialchars($contrato['titular_correo'] ?? '_________________') ?></td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td><?= htmlspecialchars($contrato['titular_direccion'] ?? '_________________') ?></td>
        </tr>
    </table>

    <!-- 2. DETALLES DEL VIAJE -->
    <div class="section-title">2. DETALLES DEL VIAJE</div>
    <div class="mb-3 px-2">
        <p><strong>Destino:</strong> <?= htmlspecialchars($contrato['destino'] ?? '') ?></p>
        <?php 
        $dStart = new DateTime($contrato['fecha_salida'] ?? 'now');
        $dEnd = new DateTime($contrato['fecha_retorno'] ?? 'now');
        $diff = $dStart->diff($dEnd);
        $dias = $diff->days + 1;
        $noches = $diff->days;
        ?>
        <p><strong>Duración:</strong> <?= $dias ?> días / <?= $noches ?> noches</p>
        <p><strong>Fechas:</strong> Salida <?= date('d de F de Y', strtotime($contrato['fecha_salida'] ?? 'now')) ?> – Retorno <?= date('d de F de Y', strtotime($contrato['fecha_retorno'] ?? 'now')) ?></p>
    </div>

    <?php 
    // PARSEAR SERVICIOS DEL GRUPO (Si es colegio, hereda del grupo)
    $svcs = [];
    if (!empty($contrato['servicios_grupo'])) {
        foreach($contrato['servicios_grupo'] as $s) {
            $svcs[$s['servicio_tipo']] = json_decode($s['detalle_json'], true) ?: [];
        }
    }
    ?>

    <?php if(isset($svcs['vuelos']) && !empty($svcs['vuelos'])): ?>
    <h3 class="font-bold mb-1 px-2 text-gray-800">✈️ VUELOS INCLUIDOS</h3>
    <table>
        <tr>
            <th>Aerolínea</th>
            <th>Vuelo</th>
            <th>Ruta</th>
            <th>Salida</th>
            <th>Llegada</th>
        </tr>
        <?php foreach($svcs['vuelos'] as $k => $item):
            // Check if format is raw object or nested array from serpapi ui
            $vuelos = isset($item['aerolinea']) ? [$item] : (isset($item[0]) && is_array($item[0]) ? $item : $svcs['vuelos']);
            if($k > 0 && isset($item['aerolinea'])) continue; // handle different JSON structures
            foreach($vuelos as $v):
                if(!is_array($v)) continue;
        ?>
        <tr>
            <td><?= htmlspecialchars($v['aerolinea'] ?? '') ?></td>
            <td><?= htmlspecialchars($v['numero'] ?? '') ?></td>
            <td><?= htmlspecialchars($v['ruta'] ?? '') ?></td>
            <td><?= htmlspecialchars($v['salida'] ?? '') ?></td>
            <td><?= htmlspecialchars($v['llegada'] ?? '') ?></td>
        </tr>
        <?php endforeach; break; endforeach; ?>
    </table>
    <p class="text-gray-600 px-2 italic mb-3">Equipaje incluido: Artículo personal + equipaje de bodega. Asignación de asientos sujeta a disponibilidad.</p>
    <?php endif; ?>

    <?php if(isset($svcs['hotel']) && !empty($svcs['hotel'])): ?>
    <h3 class="font-bold mb-1 px-2 text-gray-800">🏨 ALOJAMIENTO</h3>
    <table>
        <tr>
            <th>Hotel</th>
            <th>Tipo de Habitación / Régimen</th>
            <th>Noches</th>
        </tr>
        <?php foreach($svcs['hotel'] as $k => $item):
            $hoteles = isset($item['nombre']) ? [$item] : (isset($item[0]) && is_array($item[0]) ? $item : $svcs['hotel']);
            if($k > 0 && isset($item['nombre'])) continue;
            foreach($hoteles as $h):
                if(!is_array($h)) continue;
        ?>
        <tr>
            <td><?= htmlspecialchars($h['nombre'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['regimen'] ?? $h['tipo_habitacion'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['noches'] ?? '') ?></td>
        </tr>
        <?php endforeach; break; endforeach; ?>
    </table>
    <?php endif; ?>

    <!-- 3. FINANZAS Y PAGOS -->
    <div class="section-title">3. FINANZAS Y PAGOS</div>
    
    <h3 class="font-bold mb-1 px-2 text-gray-800">💰 DESGLOSE DEL CONTRATO Y PASAJEROS</h3>
    <table>
        <tr>
            <th>Pasajero</th>
            <th>Documento</th>
            <th>Tipo</th>
        </tr>
        <?php if(!empty($contrato['pasajeros'])): ?>
            <?php foreach($contrato['pasajeros'] as $p): ?>
            <tr>
                <td><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></td>
                <td><?= htmlspecialchars($p['pasaporte'] ?? '-') ?></td>
                <td class="capitalize"><?= htmlspecialchars($p['tipo'] ?? 'adulto') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" class="text-center text-gray-400">Sin pasajeros registrados en este contrato</td></tr>
        <?php endif; ?>
    </table>
    
    <div class="flex justify-end pr-4 mb-4">
        <div class="w-64 border border-gray-300 rounded p-2 bg-gray-50">
            <div class="flex justify-between font-bold text-gray-700"><span>Prepago Global:</span> <span>$<?= number_format($contrato['deposito'] ?? 0, 2) ?></span></div>
            <div class="flex justify-between font-bold text-gray-700 mt-1"><span>Saldo a Financiar:</span> <span>$<?= number_format($contrato['saldo'] ?? 0, 2) ?></span></div>
            <div class="flex justify-between font-black text-gray-900 mt-2 pt-2 border-t border-gray-300 text-sm"><span>TOTAL USD:</span> <span>$<?= number_format($contrato['valor_total'] ?? 0, 2) ?></span></div>
        </div>
    </div>

    <h3 class="font-bold mb-1 px-2 text-gray-800">📅 CRONOGRAMA DE PAGOS</h3>
    <table>
        <tr>
            <th>Cuota</th>
            <th>Fecha Límite</th>
            <th>Monto (USD)</th>
            <th>Penalidad por Retraso</th>
        </tr>
        <tr>
            <td>Prepago / Abono</td>
            <td>Al firmar</td>
            <td class="font-bold">$<?= number_format($contrato['deposito'] ?? 0, 2) ?></td>
            <td>N/A</td>
        </tr>
        <?php if(!empty($contrato['pagos'])): ?>
            <?php 
            // Mostramos los pagos programados que representan plan_cuotas (numero_cuota > 0) o todos los pagos
            foreach($contrato['pagos'] as $p): 
                if (($p['monto'] ?? 0) > 0 && strpos(strtolower($p['concepto']), 'depósito') === false):
            ?>
            <tr>
                <td><?= htmlspecialchars($p['concepto'] ?? 'Cuota') ?></td>
                <td><?= date('d/m/Y', strtotime($p['fecha_vencimiento'] ?? 'now')) ?></td>
                <td class="font-bold">$<?= number_format($p['monto'] ?? 0, 2) ?></td>
                <td>+$10/2 días</td>
            </tr>
            <?php endif; endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center text-gray-400">Sin cronograma detallado registrado</td></tr>
        <?php endif; ?>
    </table>
    <div class="px-2 text-xs text-gray-600 border border-amber-200 bg-amber-50 p-2 rounded mb-4">
        <strong>⚠️ Aclaración sobre penalidades:</strong><br>
        • Tolerancia: 2 días naturales después de la fecha límite.<br>
        • Multa: USD 10 por cada 2 días adicionales. (Ej: Si paga 4 días tarde, la penalidad es de $20).
    </div>

    <!-- PAGE BREAK FOR LONG CONTENT -->
    <div class="page-break"></div>

    <!-- 4. ITINERARIO -->
    <div class="section-title">4. ITINERARIO Y ACTIVIDADES</div>
    <div class="px-2 mb-4">
        <p class="mb-2 text-gray-600">Este contrato incluye los servicios y actividades contratados en el paquete principal del grupo. Las excursiones y traslados específicos se detallan a continuación:</p>
        
        <?php if(isset($svcs['traslados']) && !empty($svcs['traslados'])): ?>
        <p><strong>Traslados:</strong> 
            Servicio de ruta: <span class="uppercase font-bold"><?= htmlspecialchars($svcs['traslados']['ruta'] ?? 'IN/OUT') ?></span> 
            en modalidad <span class="capitalize font-bold"><?= htmlspecialchars($svcs['traslados']['tipo_servicio'] ?? 'compartido') ?></span> 
            (Vehículo/Detalles: <?= htmlspecialchars($svcs['traslados']['detalle'] ?? 'No especificado') ?>)
        </p>
        <?php endif; ?>

        <?php if(isset($svcs['excursiones']) && !empty($svcs['excursiones'])): ?>
        <p class="font-bold mt-2">Actividades/Tours:</p>
        <ul class="bullet-list text-gray-700">
            <?php 
            $toursTxt = $svcs['excursiones']['lista_tours'] ?? ''; 
            if($toursTxt) {
                $lineas = explode("\n", $toursTxt);
                foreach($lineas as $l) {
                    if(trim($l)) echo "<li>".htmlspecialchars(trim($l))."</li>";
                }
            } else { echo "<li>Excursiones según programa general.</li>"; }
            ?>
        </ul>
        <?php endif; ?>
    </div>

    <!-- 5. SEGURO -->
    <div class="section-title">5. SEGURO DE VIAJES</div>
    <div class="px-2 mb-4 text-gray-800">
        <p><strong>5.1. COBERTURA INCLUIDA</strong></p>
        <p class="mb-2">El presente paquete turístico incluye un Seguro de Asistencia en Viaje para cada pasajero durante la duración del viaje.</p>
        <table>
            <tr>
                <th>Aseguradora / Plan</th>
                <td><?= htmlspecialchars($svcs['seguro']['nombre'] ?? 'Universal Assistance') ?> (Plan: <?= htmlspecialchars($svcs['seguro']['plan'] ?? 'Estándar') ?>)</td>
            </tr>
            <tr>
                <th>Cobertura Principal</th>
                <td>La póliza cuenta con un tope máximo de <?= htmlspecialchars($svcs['seguro']['cobertura'] ?? '$60,000 USD') ?> para gastos médicos, emergencias y hospitalización según términos de la póliza emitida.</td>
            </tr>
        </table>
        <p class="text-xs text-gray-500 mt-1">El certificado de asistencia final será entregado al Titular vía correo electrónico o impreso previo a la fecha de salida. Las exclusiones aplican a enfermedades preexistentes no declaradas.</p>
    </div>

    <!-- 6. CLAUSULAS -->
    <div class="section-title">6. CLÁUSULAS LEGALES</div>
    <div class="px-2 mb-8 text-gray-700">
        <p><strong>📜 POLÍTICAS CLAVE</strong></p>
        <ul class="list-decimal pl-5 mb-2">
            <li><strong>Cambios de Nombre:</strong> Permitidos hasta 96 horas antes del vuelo sujeto a pago de penalidad impuesta por las aerolíneas y operadores. Prohibidos dentro de las 96 horas previas.</li>
            <li><strong>Cancelaciones:</strong> El prepago y todas las cuotas depositadas son NO REEMBOLSABLES.</li>
            <li><strong>Modificaciones:</strong> Sujetas a disponibilidad y posibles recargos tarifarios actualizados.</li>
            <li><strong>Documentación:</strong> Adultos requieren Pasaporte/DNI vigente válido mínimo 6 meses post-viaje.</li>
        </ul>
    </div>

    <!-- 7. FIRMAS -->
    <div class="section-title">7. FIRMAS Y RESPONSABILIDADES</div>
    <div class="px-2 mb-12">
        <ul class="bullet-list text-gray-700">
            <li>El titular acepta las condiciones de pago, penalidades y las coberturas descritas en este documento.</li>
            <li>Aventuras Travel Pucallpa se compromete a brindar los servicios descritos operando bajo sus registros turísticos formales.</li>
        </ul>
    </div>

    <div class="flex justify-between mt-16 px-10 text-center pb-8 border-b-2 border-dashed border-gray-300">
        <div class="w-[250px]">
            <div class="border-b border-gray-800 h-8 mb-2"></div>
            <p class="font-bold text-gray-800 uppercase"><?= htmlspecialchars($contrato['titular_nombre'] ?? 'TITULAR DEL CONTRATO') ?></p>
            <p class="text-gray-500 text-xs">Doc: <?= htmlspecialchars($contrato['titular_documento'] ?? '___________') ?></p>
            <p class="text-gray-500 text-xs">Fecha: <?= $contrato['fecha_firma'] ? date('d/m/Y', strtotime($contrato['fecha_firma'])) : '___/___/20__' ?></p>
        </div>
        <div class="w-[250px]">
            <div class="border-b border-gray-800 h-8 mb-2"></div>
            <p class="font-bold text-gray-800 uppercase">Javier Edgar Sandy Da Cruz</p>
            <p class="text-gray-500 text-xs">Aventuras Travel Pucallpa</p>
            <p class="text-gray-500 text-xs">RUC: 10475951587</p>
        </div>
    </div>

    <!-- CONTROLS -->
    <div class="text-center mt-6 no-print">
        <button onclick="window.print()" class="bg-indigo-600 text-white font-bold py-2 px-6 rounded-lg shadow-lg hover:bg-indigo-700 transition">🖨️ Imprimir o Guardar PDF</button>
    </div>

</div>

</body>
</html>
