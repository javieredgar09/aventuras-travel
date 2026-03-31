<!-- ADMIN CONTRATOS – TAILWIND -->
<?php $contratos = $contratos ?? []; ?>

<h1 class="text-3xl font-black text-petroleo mb-8">Gestión de Contratos</h1>

<div class="bg-white rounded-xl p-8 border border-petroleo/5 shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-black text-petroleo">Listado de Contratos</h2>
        <input type="text" placeholder="Buscar contrato..." class="bg-superficie border-none rounded-xl px-4 py-2 text-sm w-64 focus:ring-2 focus:ring-turquesa/30">
    </div>
    <table class="w-full">
        <thead>
            <tr class="text-xs uppercase tracking-widest text-petroleo/40 border-b border-petroleo/5">
                <th class="text-left pb-3">Código</th>
                <th class="text-left pb-3">Cliente</th>
                <th class="text-left pb-3">Destino</th>
                <th class="text-right pb-3">Valor Total</th>
                <th class="text-right pb-3">Saldo</th>
                <th class="text-center pb-3">Estado</th>
                <th class="text-right pb-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($contratos)): ?>
            <?php foreach ($contratos as $c): ?>
            <tr class="border-b border-petroleo/5 hover:bg-humo/50 transition-all">
                <td class="py-4 font-bold text-sm"><?= htmlspecialchars($c['codigo'] ?? '') ?></td>
                <td class="py-4 text-sm"><?= htmlspecialchars($c['cliente_nombre'] ?? '') ?></td>
                <td class="py-4 text-sm text-petroleo/60"><?= htmlspecialchars($c['destino'] ?? '') ?></td>
                <td class="py-4 text-right font-bold text-sm">$<?= number_format($c['valor_total'] ?? 0, 2) ?></td>
                <td class="py-4 text-right font-bold text-sm <?= ($c['saldo'] ?? 0) > 0 ? 'text-amber-600' : 'text-emerald-600' ?>">$<?= number_format($c['saldo'] ?? 0, 2) ?></td>
                <td class="py-4 text-center">
                    <span class="badge-<?= $c['estado'] ?? 'activo' ?> px-3 py-1 rounded-full text-[10px] font-bold uppercase"><?= strtoupper($c['estado'] ?? 'activo') ?></span>
                </td>
                <td class="py-4 text-right">
                    <a href="<?= Router::url('/admin/contracts/' . $c['id']) ?>" class="text-turquesa-dark font-semibold text-sm hover:underline">Detalle →</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="7" class="py-8 text-center text-petroleo/30">Sin contratos registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
