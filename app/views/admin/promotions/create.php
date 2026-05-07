<!-- ADMIN – CREAR PROMOCIÓN – TAILWIND -->
<?php $csrf_token = $csrf_token ?? ''; ?>

<div class="mb-6">
    <a href="<?= Router::url('/admin/promotions') ?>" class="text-turquesa-dark font-semibold text-sm hover:underline flex items-center gap-1">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Volver a Promociones
    </a>
</div>
<h1 class="text-3xl font-black text-petroleo mb-8">Nueva Promoción</h1>

<form action="<?= Router::url('/admin/promotions/store') ?>" method="POST" enctype="multipart/form-data">
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

            <!-- IMAGEN -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Imagen de la Promoción</label>
                <div class="border-2 border-dashed border-petroleo/15 rounded-xl p-6 text-center hover:border-turquesa/40 transition-colors bg-superficie/50" id="dropzone-create">
                    <div id="preview-create" class="hidden mb-3">
                        <img id="preview-img-create" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-sm object-cover">
                    </div>
                    <div id="placeholder-create">
                        <span class="material-symbols-outlined text-4xl text-petroleo/20 mb-2 block">add_photo_alternate</span>
                        <p class="text-sm text-petroleo/50 font-medium">Arrastra una imagen o haz clic para seleccionar</p>
                    </div>
                    <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp" class="hidden" id="input-img-create">
                    <button type="button" onclick="document.getElementById('input-img-create').click()" class="mt-3 px-4 py-2 bg-turquesa/10 text-turquesa-dark text-xs font-bold rounded-lg hover:bg-turquesa/20 transition-colors">
                        Seleccionar Imagen
                    </button>
                </div>
                <div class="mt-2 bg-blue-50 border border-blue-100 rounded-lg p-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-500 mb-1">Especificaciones de imagen</p>
                    <ul class="text-xs text-blue-700 space-y-0.5">
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-xs">aspect_ratio</span> Tamaño recomendado: <strong>800 × 450 px</strong> (proporción 16:9)</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-xs">image</span> Formatos: <strong>JPG, PNG, WebP</strong></li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-xs">scale</span> Peso máximo: <strong>2 MB</strong></li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-xs">info</span> Mínimo sugerido: 600px de ancho para buena calidad</li>
                    </ul>
                </div>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

<script>
(function(){
    var input = document.getElementById('input-img-create');
    var preview = document.getElementById('preview-create');
    var previewImg = document.getElementById('preview-img-create');
    var placeholder = document.getElementById('placeholder-create');

    if (input) {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Drag & drop
    var dz = document.getElementById('dropzone-create');
    if (dz) {
        ['dragenter','dragover'].forEach(function(ev) {
            dz.addEventListener(ev, function(e) { e.preventDefault(); dz.classList.add('border-turquesa'); });
        });
        ['dragleave','drop'].forEach(function(ev) {
            dz.addEventListener(ev, function(e) { e.preventDefault(); dz.classList.remove('border-turquesa'); });
        });
        dz.addEventListener('drop', function(e) {
            if (e.dataTransfer.files.length > 0) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    }
})();
</script>
