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

<form action="<?= Router::url('/admin/promotions/update/' . ($promo['id'] ?? '')) ?>" method="POST" enctype="multipart/form-data">
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

            <!-- IMAGEN -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Imagen de la Promoción</label>
                <?php if (!empty($promo['imagen'])): ?>
                <div class="mb-3 relative inline-block" id="current-img-wrapper">
                    <img src="<?= Router::url('/storage/promociones/' . htmlspecialchars($promo['imagen'])) ?>" alt="Imagen actual" class="max-h-48 rounded-lg shadow-sm object-cover border border-petroleo/10">
                    <span class="absolute top-2 left-2 bg-emerald-500 text-white text-[9px] font-bold uppercase px-2 py-0.5 rounded">Actual</span>
                    <label class="absolute top-2 right-2 flex items-center gap-1 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded cursor-pointer hover:bg-red-600 transition-colors">
                        <input type="checkbox" name="eliminar_imagen" value="1" class="hidden" id="chk-del-img">
                        <span class="material-symbols-outlined text-xs">delete</span> Eliminar
                    </label>
                </div>
                <?php endif; ?>
                <div class="border-2 border-dashed border-petroleo/15 rounded-xl p-6 text-center hover:border-turquesa/40 transition-colors bg-superficie/50" id="dropzone-edit">
                    <div id="preview-edit" class="hidden mb-3">
                        <img id="preview-img-edit" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-sm object-cover">
                    </div>
                    <div id="placeholder-edit">
                        <span class="material-symbols-outlined text-4xl text-petroleo/20 mb-2 block"><?= empty($promo['imagen']) ? 'add_photo_alternate' : 'swap_horiz' ?></span>
                        <p class="text-sm text-petroleo/50 font-medium"><?= empty($promo['imagen']) ? 'Arrastra una imagen o haz clic para seleccionar' : 'Subir nueva imagen para reemplazar' ?></p>
                    </div>
                    <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp" class="hidden" id="input-img-edit">
                    <button type="button" onclick="document.getElementById('input-img-edit').click()" class="mt-3 px-4 py-2 bg-turquesa/10 text-turquesa-dark text-xs font-bold rounded-lg hover:bg-turquesa/20 transition-colors">
                        <?= empty($promo['imagen']) ? 'Seleccionar Imagen' : 'Cambiar Imagen' ?>
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
                <input type="text" name="destino" value="<?= htmlspecialchars($promo['destino'] ?? '') ?>"
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Descuento</label>
                <input type="text" name="descuento" value="<?= htmlspecialchars($promo['descuento'] ?? '') ?>"
                    class="w-full py-3 px-4 bg-superficie border border-petroleo/10 rounded-xl focus:ring-2 focus:ring-turquesa/30 font-medium">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

<script>
(function(){
    var input = document.getElementById('input-img-edit');
    var preview = document.getElementById('preview-edit');
    var previewImg = document.getElementById('preview-img-edit');
    var placeholder = document.getElementById('placeholder-edit');
    var currentWrapper = document.getElementById('current-img-wrapper');
    var chkDel = document.getElementById('chk-del-img');

    if (input) {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    if (currentWrapper) currentWrapper.style.opacity = '0.3';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Delete image checkbox
    if (chkDel) {
        chkDel.addEventListener('change', function() {
            if (currentWrapper) {
                currentWrapper.style.opacity = this.checked ? '0.3' : '1';
                if (this.checked) {
                    currentWrapper.querySelector('img').style.filter = 'grayscale(1)';
                } else {
                    currentWrapper.querySelector('img').style.filter = '';
                }
            }
        });
    }

    // Drag & drop
    var dz = document.getElementById('dropzone-edit');
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
