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
    <div class="bg-white rounded-xl overflow-hidden border border-petroleo/5 shadow-sm hover:shadow-md transition-all">
        <!-- Imagen -->
        <div class="h-40 relative overflow-hidden bg-gradient-to-br from-turquesa/10 to-petroleo/5">
            <?php if (!empty($p['imagen'])): ?>
            <img src="<?= Router::url('/storage/promociones/' . htmlspecialchars($p['imagen'])) ?>" alt="<?= htmlspecialchars($p['titulo'] ?? '') ?>" class="w-full h-full object-cover">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <span class="material-symbols-outlined text-5xl text-petroleo/10">image</span>
            </div>
            <?php endif; ?>
            <div class="absolute top-3 left-3">
                <span class="<?= ($p['status_label'] ?? '') === 'active' ? 'bg-emerald-500' : 'bg-petroleo/50' ?> text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase shadow-sm">
                    <?= ($p['status_label'] ?? '') === 'active' ? 'ACTIVO' : 'INACTIVO' ?>
                </span>
            </div>
            <div class="absolute top-3 right-3 flex gap-1">
                <a href="<?= Router::url('/admin/promotions/edit/' . $p['id']) ?>" class="w-8 h-8 bg-white/90 backdrop-blur rounded-lg flex items-center justify-center text-petroleo hover:text-turquesa transition-all shadow-sm">
                    <span class="material-symbols-outlined text-lg">edit</span>
                </a>
                <form action="<?= Router::url('/admin/promotions/delete/' . $p['id']) ?>" method="POST" style="display:inline;" data-confirm="¿Eliminar esta promoción?">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <button type="submit" class="w-8 h-8 bg-white/90 backdrop-blur rounded-lg flex items-center justify-center text-petroleo hover:text-red-500 transition-all shadow-sm">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </form>
            </div>
        </div>
        <!-- Info -->
        <div class="p-5">
            <h3 class="text-lg font-black text-petroleo mb-1"><?= htmlspecialchars($p['titulo'] ?? '') ?></h3>
            <p class="text-sm text-petroleo/50 mb-3 line-clamp-2"><?= htmlspecialchars($p['descripcion'] ?? '') ?></p>
            <?php if (!empty($p['descuento'])): ?>
            <span class="inline-block bg-turquesa/10 text-turquesa-dark text-xs font-bold px-2.5 py-1 rounded-lg mb-2"><?= htmlspecialchars($p['descuento']) ?></span>
            <?php endif; ?>
            <div class="flex items-center gap-2 text-xs text-petroleo/40">
                <span class="material-symbols-outlined text-sm">location_on</span>
                <?= htmlspecialchars($p['destino'] ?? 'General') ?>
            </div>
            <div class="text-xs text-petroleo/40 mt-1">
                <?= date('d/m/Y', strtotime($p['fecha_inicio'] ?? 'now')) ?> → <?= date('d/m/Y', strtotime($p['fecha_fin'] ?? 'now')) ?>
            </div>
            <?php if (empty($p['imagen'])): ?>
            <div class="mt-3 flex items-center gap-1 text-[10px] text-amber-600 bg-amber-50 px-2 py-1 rounded font-medium">
                <span class="material-symbols-outlined text-xs">warning</span> Sin imagen — no se mostrará en la web pública
            </div>
            <?php endif; ?>
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
