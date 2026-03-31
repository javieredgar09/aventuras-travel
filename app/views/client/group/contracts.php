<!-- CONTRATOS GRUPALES – TAILWIND -->
<?php $contratos = $contratos ?? []; ?>

<h1 class="text-3xl font-black text-petroleo mb-8">Mis Contratos de Viaje</h1>

<?php if (empty($contratos)): ?>
<div class="bg-white rounded-2xl p-12 text-center border border-petroleo/5">
    <span class="material-symbols-outlined text-5xl text-turquesa/30 mb-3">description</span>
    <h2 class="text-xl font-black text-petroleo mb-2">Sin contratos activos</h2>
    <p class="text-petroleo/50 text-sm">Tus contratos aparecerán aquí cuando se registren.</p>
</div>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <?php foreach ($contratos as $c): ?>
    <div class="bg-white rounded-xl p-6 border border-petroleo/5 shadow-sm hover:shadow-md transition-all">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-black text-petroleo"><?= htmlspecialchars($c['destino'] ?? '') ?></h3>
            <span class="badge-<?= $c['estado'] ?? 'activo' ?> px-3 py-1 rounded-full text-[10px] font-bold uppercase"><?= strtoupper($c['estado'] ?? 'activo') ?></span>
        </div>
        <p class="text-sm text-petroleo/50 mb-3">Contrato: <span class="font-mono font-bold"><?= htmlspecialchars($c['codigo'] ?? '') ?></span></p>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-xs text-petroleo/40">Valor Total</p>
                <p class="text-lg font-black text-petroleo">$<?= number_format($c['valor_total'] ?? 0, 2) ?></p>
            </div>
            <div>
                <p class="text-xs text-petroleo/40">Saldo</p>
                <p class="text-lg font-black text-amber-600">$<?= number_format($c['saldo'] ?? 0, 2) ?></p>
            </div>
        </div>
        <a href="<?= Router::url('/client/contract/' . $c['id']) ?>" class="block text-center w-full py-3 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl hover:shadow-lg transition-all">
            Ver Detalle →
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
