<!-- ADMIN PROMOCIONES – TAILWIND -->
<?php 
$promociones = $promociones ?? [];
$csrf_token = $csrf_token ?? '';
?>

<div class="flex justify-between items-start mb-8">
    <h1 class="text-3xl font-black text-petroleo">Gestión de Promociones</h1>
    <a href="<?= Router::url('/admin/promotions/create') ?>" class="px-6 py-3 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl hover:shadow-lg transition-all active:scale-95 flex items-center gap-2">
        <span class="material-symbols-outlined">add_circle</span>
        Nueva Promoción
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (!empty($promociones)): ?>
    <?php foreach ($promociones as $p): ?>
    <div class="bg-white rounded-xl p-6 border border-petroleo/5 shadow-sm hover:shadow-md transition-all">
        <div class="flex justify-between items-center mb-4">
            <span class="<?= ($p['status_label'] ?? '') === 'active' ? 'badge-aprobado' : 'badge-rechazado' ?> px-3 py-1 rounded-full text-[10px] font-bold uppercase">
                <?= ($p['status_label'] ?? '') === 'active' ? 'ACTIVO' : 'INACTIVO' ?>
            </span>
            <div class="flex gap-2">
                <a href="<?= Router::url('/admin/promotions/edit/' . $p['id']) ?>" class="text-petroleo/40 hover:text-turquesa transition-all">
                    <span class="material-symbols-outlined text-xl">edit</span>
                </a>
                <form action="<?= Router::url('/admin/promotions/delete/' . $p['id']) ?>" method="POST" style="display:inline;" data-confirm="¿Eliminar esta promoción?">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <button type="submit" class="text-petroleo/40 hover:text-red-500 transition-all">
                        <span class="material-symbols-outlined text-xl">delete</span>
                    </button>
                </form>
            </div>
        </div>
        <h3 class="text-lg font-black text-petroleo mb-1"><?= htmlspecialchars($p['titulo'] ?? '') ?></h3>
        <p class="text-sm text-petroleo/50 mb-3"><?= htmlspecialchars($p['descripcion'] ?? '') ?></p>
        <div class="flex items-center gap-2 text-xs text-petroleo/40">
            <span class="material-symbols-outlined text-sm">location_on</span>
            <?= htmlspecialchars($p['destino'] ?? 'General') ?>
        </div>
        <div class="text-xs text-petroleo/40 mt-1">
            <?= date('d/m/Y', strtotime($p['fecha_inicio'] ?? 'now')) ?> → <?= date('d/m/Y', strtotime($p['fecha_fin'] ?? 'now')) ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="col-span-3 bg-white rounded-xl p-12 text-center border border-petroleo/5">
        <span class="material-symbols-outlined text-5xl text-turquesa/30 mb-3">campaign</span>
        <p class="text-petroleo/40">Sin promociones registradas</p>
    </div>
    <?php endif; ?>
</div>
