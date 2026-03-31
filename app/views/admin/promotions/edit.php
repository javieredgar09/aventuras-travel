<!-- ADMIN – EDITAR PROMOCIÓN – TAILWIND -->
<?php 
$csrf_token = $csrf_token ?? '';
$promo = $promo ?? [];
?>

<div class="mb-6">
    <a href="<?= Router::url('/admin/promotions') ?>" class="text-turquesa-dark font-semibold text-sm hover:underline flex items-center gap-1">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Volver a Promociones
    </a>
</div>
<h1 class="text-3xl font-black text-petroleo mb-8">Editar Promoción</h1>

<form action="<?= Router::url('/admin/promotions/update/' . ($promo['id'] ?? '')) ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <div class="bg-white rounded-xl p-8 border border-petroleo/5 shadow-sm max-w-2xl">
        <div class="space-y-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Título</label>
                <input type="text" name="titulo" required value="<?= htmlspecialchars($promo['titulo'] ?? '') ?>"
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Descripción</label>
                <textarea name="descripcion" rows="4" required
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium resize-none"><?= htmlspecialchars($promo['descripcion'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Destino</label>
                <input type="text" name="destino" value="<?= htmlspecialchars($promo['destino'] ?? '') ?>"
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Descuento</label>
                <input type="text" name="descuento" value="<?= htmlspecialchars($promo['descuento'] ?? '') ?>"
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" required value="<?= $promo['fecha_inicio'] ?? '' ?>"
                        class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Fecha Fin</label>
                    <input type="date" name="fecha_fin" required value="<?= $promo['fecha_fin'] ?? '' ?>"
                        class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-4 mt-8">
            <a href="<?= Router::url('/admin/promotions') ?>" class="px-6 py-3 border border-petroleo/20 text-petroleo font-bold rounded-xl hover:bg-superficie transition-all">Cancelar</a>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl hover:shadow-lg transition-all active:scale-95 flex items-center gap-2">
                <span class="material-symbols-outlined">save</span>
                Guardar Cambios
            </button>
        </div>
    </div>
</form>
