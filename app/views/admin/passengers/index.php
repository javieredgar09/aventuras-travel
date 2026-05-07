<!-- ADMIN PASAJEROS – TAILWIND -->
<?php $pasajeros = $pasajeros ?? []; ?>

<h1 class="text-2xl sm:text-3xl font-black text-petroleo mb-6 sm:mb-8">Registro de Pasajeros</h1>

<div class="bg-white rounded-xl p-4 sm:p-6 md:p-8 border border-petroleo/5 shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <h2 class="text-lg sm:text-xl font-black text-petroleo"><?= count($pasajeros) ?> Pasajeros Registrados</h2>
        <input type="text" placeholder="Buscar pasajero..." class="bg-superficie border-none rounded-xl px-4 py-2 text-sm w-full sm:w-64 focus:ring-2 focus:ring-turquesa/30">
    </div>
    <div class="overflow-x-auto -mx-4 sm:mx-0">
    <table class="w-full min-w-[600px]">
        <thead>
            <tr class="text-xs uppercase tracking-widest text-petroleo/40 border-b border-petroleo/5">
                <th class="text-left pb-3">Pasajero</th>
                <th class="text-left pb-3">DNI/Documento</th>
                <th class="text-left pb-3">Contrato</th>
                <th class="text-left pb-3">Pasaporte</th>
                <th class="text-center pb-3">Tipo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pasajeros)): ?>
            <?php foreach ($pasajeros as $p): ?>
            <tr class="border-b border-petroleo/5 hover:bg-humo/50 transition-all">
                <td class="py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-turquesa/10 text-turquesa-dark rounded-full flex items-center justify-center font-bold text-xs">
                            <?= strtoupper(substr($p['nombre'] ?? '', 0, 1) . substr($p['apellido'] ?? '', 0, 1)) ?>
                        </div>
                        <span class="font-bold text-sm"><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></span>
                    </div>
                </td>
                <td class="py-4 text-sm text-petroleo/60"><?= htmlspecialchars($p['documento'] ?? '—') ?></td>
                <td class="py-4 text-sm font-mono"><?= htmlspecialchars($p['contrato_codigo'] ?? 'N/A') ?></td>
                <td class="py-4 text-sm text-petroleo/60"><?= htmlspecialchars($p['pasaporte'] ?? '—') ?></td>
                <td class="py-4 text-center">
                    <span class="bg-turquesa/10 text-turquesa-dark px-3 py-1 rounded-full text-[10px] font-bold uppercase"><?= htmlspecialchars($p['tipo'] ?? 'adulto') ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="5" class="py-8 text-center text-petroleo/30">Sin pasajeros registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
