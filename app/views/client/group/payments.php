<!-- PAGOS REPRESENTANTE – Registro de pago por contrato con buscador -->
<?php
$contratos  = $contratos ?? [];
$pagos      = $pagos ?? [];
$resumen    = $resumen ?? ['esperado' => 0, 'pagado' => 0, 'pendiente' => 0];
$grupo      = $grupo ?? null;
$user       = $_SESSION['user'] ?? [];
$cuotasPorContrato = $cuotasPorContrato ?? [];

if (!function_exists('fmoney')) {
    function fmoney(float $v, string $c = 'USD'): string {
        return '$' . number_format($v, 2);
    }
}
$moneda = 'USD';

// Build search data for JS
$searchData = [];
foreach ($contratos as $c) {
    $paxNames = [];
    foreach ($c['pasajeros'] ?? [] as $p) {
        $paxNames[] = trim($p['nombre'] . ' ' . $p['apellido']);
    }
    $searchData[] = [
        'id'          => $c['id'],
        'codigo'      => $c['codigo'] ?? '',
        'titular'     => $c['titular_nombre'] ?? '',
        'destino'     => $c['destino'] ?? '',
        'valor_total' => (float)($c['valor_total'] ?? 0),
        'pagado'      => (float)($c['cuota_pagada'] ?? 0),
        'pendiente'   => (float)($c['cuota_pendiente'] ?? 0),
        'pasajeros'   => $paxNames,
        'moneda'      => $c['moneda'] ?? 'USD',
    ];
}
?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-1">Panel Representante</p>
        <h1 class="text-3xl md:text-4xl font-black text-petroleo leading-tight">Pagos del Grupo</h1>
        <p class="text-sm text-petroleo/50 mt-1">
            Registra pagos para cualquier contrato del grupo. Busca por código de contrato o nombre de pasajero.
        </p>
    </div>
    <a href="<?= Router::url('/leader/dashboard') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-petroleo/5 text-petroleo font-bold text-sm hover:bg-petroleo/10 transition-all">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Volver al Panel
    </a>
</div>

<!-- Resumen financiero -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl p-5 border border-petroleo/5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-petroleo/40 mb-2">Total Esperado</p>
        <p class="text-3xl font-black text-petroleo"><?= fmoney($resumen['esperado'], $moneda) ?></p>
    </div>
    <div class="bg-white rounded-xl p-5 border-l-4 border-l-emerald-400 border border-petroleo/5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-petroleo/40 mb-2">Total Pagado</p>
        <p class="text-3xl font-black text-emerald-600"><?= fmoney($resumen['pagado'], $moneda) ?></p>
    </div>
    <div class="bg-white rounded-xl p-5 border-l-4 border-l-red-400 border border-petroleo/5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-petroleo/40 mb-2">Saldo Pendiente</p>
        <p class="text-3xl font-black text-red-600"><?= fmoney($resumen['pendiente'], $moneda) ?></p>
    </div>
</div>

<!-- Sección: Registrar Pago -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

    <!-- Buscador de Contrato (izquierda) -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden sticky top-24">
            <div class="px-5 py-4 border-b border-petroleo/5 bg-humo/30">
                <h2 class="text-sm font-black text-petroleo flex items-center gap-2">
                    <span class="material-symbols-outlined text-turquesa">search</span>
                    Seleccionar Contrato
                </h2>
            </div>
            <div class="p-4">
                <div class="relative mb-4">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-petroleo/30 text-lg">search</span>
                    <input type="text" id="contractSearch"
                           placeholder="Buscar por contrato, titular o pasajero..."
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-petroleo/10 text-sm focus:outline-none focus:ring-2 focus:ring-turquesa/30 focus:border-turquesa transition-all"
                           autocomplete="off">
                </div>

                <!-- Lista de contratos -->
                <div id="contractList" class="space-y-2 max-h-[400px] overflow-y-auto pr-1">
                    <?php foreach ($contratos as $c):
                        $titular = htmlspecialchars($c['titular_nombre'] ?? 'Sin titular');
                        $codigo  = htmlspecialchars($c['codigo'] ?? '');
                        $pendiente = (float)($c['cuota_pendiente'] ?? 0);
                        $pagado = (float)($c['cuota_pagada'] ?? 0);
                        $total = (float)($c['valor_total'] ?? 0);
                        $pct = $total > 0 ? round(($pagado / $total) * 100) : 0;
                        $paxNames = [];
                        foreach ($c['pasajeros'] ?? [] as $p) {
                            $paxNames[] = trim($p['nombre'] . ' ' . $p['apellido']);
                        }
                        $initials = '';
                        $parts = explode(' ', $titular);
                        foreach (array_slice($parts, 0, 2) as $pt) { $initials .= strtoupper(mb_substr($pt, 0, 1)); }
                    ?>
                    <button type="button"
                            class="contract-card w-full text-left p-4 rounded-xl border-2 border-petroleo/5 hover:border-turquesa/40 transition-all group"
                            data-id="<?= $c['id'] ?>"
                            data-code="<?= strtolower($codigo) ?>"
                            data-titular="<?= strtolower($titular) ?>"
                            data-pasajeros="<?= strtolower(implode(',', $paxNames)) ?>"
                            onclick="selectContract(<?= $c['id'] ?>, '<?= addslashes($codigo) ?>', '<?= addslashes($titular) ?>')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-turquesa/10 flex items-center justify-center text-turquesa-dark text-xs font-black flex-shrink-0 group-hover:bg-turquesa/20 transition-all">
                                <?= $initials ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-petroleo truncate"><?= $titular ?></p>
                                <p class="text-xs text-petroleo/40 font-mono">#<?= $codigo ?></p>
                                <?php if (!empty($paxNames)): ?>
                                <p class="text-[10px] text-petroleo/30 truncate mt-0.5">
                                    <span class="material-symbols-outlined text-[10px] align-middle">person</span>
                                    <?= htmlspecialchars(implode(', ', array_slice($paxNames, 0, 3))) ?>
                                    <?= count($paxNames) > 3 ? '...(+' . (count($paxNames) - 3) . ')' : '' ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <?php if ($pendiente > 0): ?>
                                <p class="text-xs font-bold text-red-500"><?= fmoney($pendiente, $moneda) ?></p>
                                <p class="text-[10px] text-petroleo/30">pendiente</p>
                                <?php else: ?>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">PAGADO</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Progress bar -->
                        <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5">
                            <div class="<?= $pct >= 100 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-blue-500' : 'bg-amber-500') ?> h-1.5 rounded-full transition-all" style="width: <?= $pct ?>%"></div>
                        </div>
                    </button>
                    <?php endforeach; ?>

                    <!-- Sin resultados -->
                    <div id="noResults" class="hidden text-center py-6">
                        <span class="material-symbols-outlined text-3xl text-petroleo/15 block mb-2">search_off</span>
                        <p class="text-sm text-petroleo/30">No se encontraron contratos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Pago (derecha) -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-petroleo/5 bg-humo/30">
                <h2 class="text-sm font-black text-petroleo flex items-center gap-2">
                    <span class="material-symbols-outlined text-turquesa">payments</span>
                    Registrar Pago
                </h2>
            </div>

            <!-- Contrato seleccionado (preview) -->
            <div id="selectedContractPreview" class="px-5 py-4 bg-turquesa/5 border-b border-turquesa/10 hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-turquesa/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-turquesa-dark text-sm">description</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-petroleo" id="previewTitular">—</p>
                            <p class="text-xs text-petroleo/40 font-mono" id="previewCodigo">—</p>
                        </div>
                    </div>
                    <button type="button" onclick="clearSelection()" class="text-xs text-red-500 hover:text-red-700 font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">close</span> Cambiar
                    </button>
                </div>
            </div>

            <!-- Mensaje: seleccionar contrato -->
            <div id="selectContractMsg" class="px-5 py-12 text-center">
                <span class="material-symbols-outlined text-5xl text-petroleo/10 block mb-3">touch_app</span>
                <p class="text-lg font-bold text-petroleo/30">Selecciona un contrato</p>
                <p class="text-sm text-petroleo/20 mt-1">Busca y elige el contrato para registrar el pago</p>
            </div>

            <!-- Formulario -->
            <form id="paymentForm" action="<?= Router::url('/leader/payments/register') ?>" method="POST" enctype="multipart/form-data" class="hidden">
                <div class="p-5 space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? $_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="contrato_id" id="selectedContratoId" value="">

                    <!-- Monto -->
                    <div>
                        <label class="block text-xs font-bold text-petroleo uppercase tracking-widest mb-2">Monto a Pagar</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-petroleo/40 font-bold">$</span>
                            <input type="number" step="0.01" name="monto" id="payMonto" required min="1"
                                   placeholder="0.00"
                                   class="w-full pl-8 pr-4 py-3.5 rounded-xl border border-petroleo/10 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-turquesa/30 focus:border-turquesa transition-all">
                        </div>
                    </div>

                    <!-- Preview de cuotas que cubre -->
                    <div id="cascadePreview" class="hidden rounded-xl border border-turquesa/20 bg-turquesa/5 p-4">
                        <p class="text-xs font-bold text-turquesa-dark mb-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">info</span> Este pago cubrirá:
                        </p>
                        <div id="cascadeDetail" class="space-y-1.5"></div>
                    </div>

                    <!-- Concepto -->
                    <div>
                        <label class="block text-xs font-bold text-petroleo uppercase tracking-widest mb-2">Concepto (Opcional)</label>
                        <input type="text" name="concepto" id="payConcepto"
                               placeholder="Ej: Pago Cuota 2 — Jane Vargas"
                               class="w-full px-4 py-3 rounded-xl border border-petroleo/10 text-sm focus:outline-none focus:ring-2 focus:ring-turquesa/30 focus:border-turquesa transition-all">
                    </div>

                    <!-- Comprobante -->
                    <div>
                        <label class="block text-xs font-bold text-petroleo uppercase tracking-widest mb-2">Comprobante de Pago</label>
                        <input type="file" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" required
                               class="w-full px-4 py-3 rounded-xl border border-petroleo/10 text-sm focus:outline-none focus:ring-2 focus:ring-turquesa/30 transition-all
                                      file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-turquesa/10 file:text-turquesa-dark hover:file:bg-turquesa/20">
                        <p class="text-[10px] text-petroleo/30 mt-1">Formatos: JPG, PNG, PDF — Máximo: 5MB</p>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-petroleo to-turquesa-dark text-white font-bold shadow-lg shadow-petroleo/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 text-sm">
                        <span class="material-symbols-outlined">upload_file</span>
                        Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Historial de Pagos Recientes -->
<?php if (!empty($pagos)): ?>
<div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden mb-8">
    <div class="px-5 py-4 border-b border-petroleo/5 flex justify-between items-center">
        <h2 class="text-sm font-black text-petroleo flex items-center gap-2">
            <span class="material-symbols-outlined text-turquesa">receipt_long</span>
            Historial de Pagos del Grupo
        </h2>
        <span class="text-xs text-petroleo/40"><?= count($pagos) ?> transacciones</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-[10px] uppercase tracking-widest text-petroleo/40 border-b border-petroleo/5 bg-humo/30">
                    <th class="text-left px-5 py-3">Contrato / Titular</th>
                    <th class="text-left px-4 py-3">Concepto</th>
                    <th class="text-left px-4 py-3 hidden md:table-cell">Fecha</th>
                    <th class="text-right px-4 py-3">Monto</th>
                    <th class="text-center px-5 py-3">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $p):
                    $estadoBadge = match($p['estado'] ?? 'pendiente') {
                        'aprobado'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'rechazado' => 'bg-red-50 text-red-700 border-red-200',
                        default     => 'bg-amber-50 text-amber-700 border-amber-200',
                    };
                    $estadoLabel = match($p['estado'] ?? 'pendiente') {
                        'aprobado'  => 'Aprobado',
                        'rechazado' => 'Rechazado',
                        default     => 'En Revisión',
                    };
                ?>
                <tr class="border-b border-petroleo/5 hover:bg-humo/30 transition-all">
                    <td class="px-5 py-3">
                        <p class="text-sm font-bold text-petroleo"><?= htmlspecialchars($p['titular_nombre'] ?? '') ?></p>
                        <p class="text-xs text-petroleo/40 font-mono">#<?= htmlspecialchars($p['contrato_codigo'] ?? '') ?></p>
                    </td>
                    <td class="px-4 py-3 text-sm text-petroleo/60"><?= htmlspecialchars($p['concepto'] ?? 'Pago') ?></td>
                    <td class="px-4 py-3 text-sm text-petroleo/50 hidden md:table-cell">
                        <?= !empty($p['created_at']) ? date('d M Y', strtotime($p['created_at'])) : '—' ?>
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-sm text-petroleo"><?= fmoney((float)($p['monto'] ?? 0), $moneda) ?></td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase border <?= $estadoBadge ?>"><?= $estadoLabel ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- JS: Buscador y selección de contrato -->
<script>
(function() {
    var searchInput = document.getElementById('contractSearch');
    var cards = document.querySelectorAll('.contract-card');
    var noResults = document.getElementById('noResults');

    // Búsqueda en tiempo real
    searchInput.addEventListener('input', function() {
        var query = this.value.toLowerCase().trim();
        var visible = 0;

        cards.forEach(function(card) {
            var code = card.getAttribute('data-code') || '';
            var titular = card.getAttribute('data-titular') || '';
            var pax = card.getAttribute('data-pasajeros') || '';

            var match = !query || code.indexOf(query) !== -1 || titular.indexOf(query) !== -1 || pax.indexOf(query) !== -1;
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        noResults.classList.toggle('hidden', visible > 0);
    });
})();

var selectedId = null;
var cuotasData = <?= json_encode($cuotasPorContrato, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

function selectContract(id, codigo, titular) {
    selectedId = id;
    document.getElementById('selectedContratoId').value = id;
    document.getElementById('previewTitular').textContent = titular;
    document.getElementById('previewCodigo').textContent = '#' + codigo;
    document.getElementById('selectedContractPreview').classList.remove('hidden');
    document.getElementById('selectContractMsg').classList.add('hidden');
    document.getElementById('paymentForm').classList.remove('hidden');

    // Highlight selected card
    document.querySelectorAll('.contract-card').forEach(function(c) {
        c.classList.toggle('border-turquesa', parseInt(c.getAttribute('data-id')) === id);
        c.classList.toggle('bg-turquesa/5', parseInt(c.getAttribute('data-id')) === id);
    });

    // Reset monto
    document.getElementById('payMonto').value = '';
    document.getElementById('payConcepto').value = '';
    document.getElementById('cascadePreview').classList.add('hidden');
    document.getElementById('payMonto').focus();
}

function clearSelection() {
    selectedId = null;
    document.getElementById('selectedContratoId').value = '';
    document.getElementById('selectedContractPreview').classList.add('hidden');
    document.getElementById('selectContractMsg').classList.remove('hidden');
    document.getElementById('paymentForm').classList.add('hidden');

    document.querySelectorAll('.contract-card').forEach(function(c) {
        c.classList.remove('border-turquesa', 'bg-turquesa/5');
    });
}

// Cascade preview
(function() {
    var montoInput = document.getElementById('payMonto');
    var cascadeEl = document.getElementById('cascadePreview');
    var cascadeDetail = document.getElementById('cascadeDetail');
    var conceptoInput = document.getElementById('payConcepto');

    function fmt(n) { return '$' + Number(n).toFixed(2); }

    function updatePreview() {
        if (!selectedId || !cuotasData[selectedId] || cuotasData[selectedId].length === 0) {
            cascadeEl.classList.add('hidden');
            return;
        }

        var monto = parseFloat(montoInput.value) || 0;
        if (monto <= 0) {
            cascadeEl.classList.add('hidden');
            return;
        }

        var cuotas = cuotasData[selectedId];
        var remaining = monto;
        var lines = [];
        var cuotaPagadas = [];

        for (var i = 0; i < cuotas.length; i++) {
            if (remaining <= 0) break;
            var q = cuotas[i];
            var faltante = q.faltante;

            if (remaining >= faltante) {
                lines.push(
                    '<div class="flex items-center justify-between gap-2">' +
                        '<div class="flex items-center gap-2">' +
                            '<span class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold">' + q.numero + '</span>' +
                            '<span class="text-xs text-petroleo">' + (q.concepto || 'Cuota ' + q.numero) + '</span>' +
                        '</div>' +
                        '<span class="text-xs font-bold text-emerald-600">' + fmt(faltante) + ' ✓</span>' +
                    '</div>'
                );
                remaining -= faltante;
                cuotaPagadas.push('Cuota ' + q.numero);
            } else {
                lines.push(
                    '<div class="flex items-center justify-between gap-2">' +
                        '<div class="flex items-center gap-2">' +
                            '<span class="w-5 h-5 rounded-full bg-amber-400 text-white flex items-center justify-center text-[10px] font-bold">' + q.numero + '</span>' +
                            '<span class="text-xs text-petroleo">' + (q.concepto || 'Cuota ' + q.numero) + ' (parcial)</span>' +
                        '</div>' +
                        '<span class="text-xs font-bold text-amber-600">' + fmt(remaining) + ' de ' + fmt(faltante) + '</span>' +
                    '</div>'
                );
                cuotaPagadas.push('Cuota ' + q.numero + ' (parcial)');
                remaining = 0;
            }
        }

        if (remaining > 0) {
            lines.push(
                '<div class="flex items-center justify-between gap-2 pt-1.5 border-t border-turquesa/10 mt-1">' +
                    '<span class="text-xs text-petroleo/40">Excedente a favor</span>' +
                    '<span class="text-xs font-bold text-turquesa-dark">' + fmt(remaining) + '</span>' +
                '</div>'
            );
        }

        cascadeDetail.innerHTML = lines.join('');
        cascadeEl.classList.remove('hidden');

        // Auto-fill concepto
        if (cuotaPagadas.length > 0 && !conceptoInput._userEdited) {
            conceptoInput.value = 'Pago ' + cuotaPagadas.join(', ');
        }
    }

    montoInput.addEventListener('input', updatePreview);
    conceptoInput.addEventListener('input', function() {
        this._userEdited = this.value.length > 0;
    });
})();
</script>
