<!-- ADMIN – CREAR PROMOCIÓN – TAILWIND -->
<?php $csrf_token = $csrf_token ?? ''; ?>

<div class="mb-6">
    <a href="<?= Router::url('/admin/promotions') ?>" class="text-turquesa-dark font-semibold text-sm hover:underline flex items-center gap-1">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Volver a Promociones
    </a>
</div>
<h1 class="text-3xl font-black text-petroleo mb-8">Nueva Promoción</h1>

<form action="<?= Router::url('/admin/promotions/store') ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <div class="bg-white rounded-xl p-8 border border-petroleo/5 shadow-sm max-w-2xl">
        <div class="space-y-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Título</label>
                <input type="text" name="titulo" required placeholder="Nombre de la promoción"
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Descripción</label>
                <textarea name="descripcion" rows="4" required placeholder="Detalle de la promoción"
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Destino</label>
                <input type="text" name="destino" placeholder="Ej: Cusco, Perú"
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Descuento</label>
                <input type="text" name="descuento" placeholder="Ej: 20% OFF"
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" required
                        class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Fecha Fin</label>
                    <input type="date" name="fecha_fin" required
                        class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-4 mt-8">
            <a href="<?= Router::url('/admin/promotions') ?>" class="px-6 py-3 border border-petroleo/20 text-petroleo font-bold rounded-xl hover:bg-superficie transition-all">Cancelar</a>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl hover:shadow-lg transition-all active:scale-95 flex items-center gap-2">
                <span class="material-symbols-outlined">save</span>
                Crear Promoción
            </button>
        </div>
    </div>
</form>
