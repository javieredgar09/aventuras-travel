<!-- admin/sales/edit.php -->
<div class="mb-4 flex flex-col md:flex-row md:justify-between md:items-center bg-white p-3 rounded-xl border border-petroleo/5 shadow-sm">
    <div class="flex items-center gap-2">
        <a href="<?= Router::url('/admin/sales/' . $grupo['id']) ?>" class="w-8 h-8 rounded-full bg-superficie text-petroleo flex items-center justify-center hover:bg-turquesa hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl text-petroleo font-black leading-none">Editar Grupo / Servicios</h1>
            <p class="text-xs text-petroleo/60 mt-1"><?= htmlspecialchars($grupo['nombre']) ?> (<?= ucfirst($grupo['tipo']) ?>)</p>
        </div>
    </div>
</div>

<form action="<?= Router::url('/admin/sales/' . $grupo['id'] . '/update') ?>" method="POST" id="editGroupForm" class="space-y-4 pb-20" onsubmit="return prepararEnvio(event)">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <input type="hidden" name="servicios_json" id="servicios_json" value="[]">

    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-turquesa">tune</span>
            <h2 class="text-lg font-bold text-petroleo">Configuración General</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Nombre del Grupo *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($grupo['nombre'] ?? '') ?>" required class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Destino Principal *</label>
                <input type="text" name="destino" value="<?= htmlspecialchars($grupo['destino'] ?? '') ?>" required class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Fecha Ida</label>
                <input type="date" name="fecha_viaje" value="<?= htmlspecialchars($grupo['fecha_viaje'] ?? '') ?>" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Fecha Retorno</label>
                <input type="date" name="fecha_retorno" value="<?= htmlspecialchars($grupo['fecha_retorno'] ?? '') ?>" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Operador Mayorista (Opcional)</label>
                <input type="text" name="operador" value="<?= htmlspecialchars($grupo['operador'] ?? '') ?>" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Valor Total Grupal (USD)</label>
                <input type="number" step="0.01" name="valor_total" value="<?= htmlspecialchars($grupo['valor_total'] ?? '0.00') ?>" required class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Estado</label>
                <select name="estado" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none bg-white">
                    <option value="activo" <?= ($grupo['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="cancelado" <?= ($grupo['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    <option value="completado" <?= ($grupo['estado'] ?? '') === 'completado' ? 'selected' : '' ?>>Completado</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Servicios Incluidos -->
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500">luggage</span>
            <h2 class="text-lg font-bold text-petroleo">Servicios Incluidos</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2 mb-4">
                <?php foreach ($serviceMeta ?? [] as $key => $meta): ?>
                <label class="flex flex-row items-center gap-2 p-2 border-2 border-petroleo/10 bg-humo/50 rounded-xl cursor-pointer hover:bg-superficie transition-colors svc-toggle relative" data-svc="<?= $key ?>">
                    <input type="checkbox" class="sr-only peer svc-checkbox" value="<?= $key ?>">
                    <span class="text-lg"><?= $meta['emoji'] ?></span>
                    <span class="font-bold text-xs text-petroleo/80 peer-checked:text-turquesa-dark"><?= $meta['label'] ?></span>
                    <div class="absolute inset-0 border-2 border-turquesa rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                </label>
                <?php endforeach; ?>
            </div>
            <div id="servicios-forms-container" class="space-y-4"></div>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 lg:left-64 right-0 p-3 bg-white border-t border-petroleo/10 shadow-[0_-10px_30px_rgba(0,0,0,0.05)] flex justify-end gap-2 z-40">
        <a href="<?= Router::url('/admin/sales/' . $grupo['id']) ?>" class="px-6 py-2 rounded-lg text-sm font-bold text-petroleo bg-superficie hover:bg-humo transition-colors">Cancelar</a>
        <button type="submit" class="px-6 py-2 rounded-lg text-sm font-black text-white bg-turquesa hover:bg-turquesa-dark shadow-lg shadow-turquesa/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">save</span> Guardar Cambios
        </button>
    </div>
</form>

<script>
const existingServices = <?= json_encode($grupo['servicios'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

const svcTemplates = {
    'hotel': `
        <div id="hoteles-list" class="space-y-3">
            <div class="hotel-item flex flex-col gap-3 items-start svc-multi-row pb-4">
                <div class="w-full relative">
                    <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Buscar Hotel (SerpAPI)</label>
                    <div class="flex gap-1 mt-1">
                        <input type="text" class="svc-input w-full p-2 border rounded text-sm hotel-search-input" placeholder="Ej: Barcelo Bavaro">
                        <button type="button" onclick="searchHotelAPI(this)" class="px-3 py-2 text-white bg-turquesa hover:bg-turquesa-dark rounded"><span class="material-symbols-outlined text-sm">search</span></button>
                    </div>
                </div>
                <div class="w-full grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-12">
                        <label class="text-[10px] uppercase font-bold text-petroleo">Nombre Confirmado</label>
                        <input type="text" data-field="nombre" data-array="hoteles" class="svc-input w-full p-2 border rounded text-sm font-bold h-name-input">
                    </div>
                </div>
            </div>
        </div>
        <button type="button" onclick="addHotelItem()" class="mt-3 text-xs text-turquesa-dark font-bold">+ Añadir otro hotel</button>
    `,
    'vuelos': `
        <div id="vuelos-list" class="space-y-3">
            <div class="vuelo-item flex flex-col gap-3 svc-multi-row pb-4 border-b border-petroleo/10 last:border-0">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <input type="text" data-field="aerolinea" data-array="vuelos" class="svc-input p-2 border rounded text-sm" placeholder="Aerolínea">
                    <input type="text" data-field="numero" data-array="vuelos" class="svc-input p-2 border rounded text-sm" placeholder="Nº Vuelo">
                    <input type="text" data-field="ruta" data-array="vuelos" class="svc-input p-2 border rounded text-sm" placeholder="Ruta (Ej: LIM-PUJ)">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div><label class="text-[9px] uppercase font-bold">Salida</label><input type="datetime-local" data-field="salida" data-array="vuelos" class="svc-input w-full p-2 border rounded text-sm"></div>
                    <div><label class="text-[9px] uppercase font-bold">Llegada</label><input type="datetime-local" data-field="llegada" data-array="vuelos" class="svc-input w-full p-2 border rounded text-sm"></div>
                </div>
            </div>
        </div>
        <button type="button" onclick="addVueloItem()" class="mt-3 text-xs text-turquesa-dark font-bold">+ Añadir tramo</button>
    `,
    'traslados': `<div class="grid grid-cols-1 gap-3">
        <input type="text" data-field="ruta" class="svc-input p-2 border rounded text-sm" placeholder="Ruta">
        <input type="text" data-field="detalle" class="svc-input p-2 border rounded text-sm" placeholder="Detalles">
    </div>`,
    'excursiones': `<textarea data-field="lista_tours" class="svc-input w-full p-2 border rounded text-sm" rows="3" placeholder="Lista de tours"></textarea>`,
    'seguro': `<input type="text" data-field="nombre" class="svc-input p-2 border rounded text-sm" placeholder="Aseguradora">`
};

function addHotelItem() {
    const list = document.getElementById('hoteles-list');
    list.insertAdjacentHTML('beforeend', `<div class="hotel-item svc-multi-row border-t pt-3 mt-3">
        <input type="text" data-field="nombre" data-array="hoteles" class="svc-input w-full p-2 border rounded text-sm mb-2" placeholder="Nombre Hotel">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-xs">Eliminar hotel</button>
    </div>`);
}

function addVueloItem() {
    const list = document.getElementById('vuelos-list');
    list.insertAdjacentHTML('beforeend', `<div class="vuelo-item flex flex-col gap-3 svc-multi-row pb-4 border-b border-petroleo/10 last:border-0 pt-3 mt-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
            <input type="text" data-field="aerolinea" data-array="vuelos" class="svc-input p-2 border rounded text-sm" placeholder="Aerolínea">
            <input type="text" data-field="numero" data-array="vuelos" class="svc-input p-2 border rounded text-sm" placeholder="Nº Vuelo">
            <input type="text" data-field="ruta" data-array="vuelos" class="svc-input p-2 border rounded text-sm" placeholder="Ruta (Ej: LIM-PUJ)">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div><label class="text-[9px] uppercase font-bold">Salida</label><input type="datetime-local" data-field="salida" data-array="vuelos" class="svc-input w-full p-2 border rounded text-sm"></div>
            <div><label class="text-[9px] uppercase font-bold">Llegada</label><input type="datetime-local" data-field="llegada" data-array="vuelos" class="svc-input w-full p-2 border rounded text-sm"></div>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-xs self-start font-bold">Eliminar tramo</button>
    </div>`);
}

document.querySelectorAll('.svc-checkbox').forEach(chk => {
    chk.addEventListener('change', function() {
        const type = this.value;
        const containerId = 'svc-form-' + type;
        const container = document.getElementById('servicios-forms-container');
        
        if (this.checked) {
            if (!document.getElementById(containerId)) {
                let html = `<div id="${containerId}" class="svc-form-item p-4 border border-petroleo/10 rounded-lg bg-superficie/30 relative" data-type="${type}">
                    <h3 class="font-bold text-petroleo uppercase text-[10px] mb-2">${type}</h3>
                    ${svcTemplates[type] || ''}
                </div>`;
                container.insertAdjacentHTML('beforeend', html);
            }
            this.parentElement.classList.add('border-turquesa');
        } else {
            const el = document.getElementById(containerId);
            if (el) el.remove();
            this.parentElement.classList.remove('border-turquesa');
        }
    });
});

function prepararEnvio(e) {
    let servicios = [];
    document.querySelectorAll('.svc-form-item').forEach(item => {
        let type = item.getAttribute('data-type');
        let details = {};
        const multiRows = item.querySelectorAll('.svc-multi-row');
        
        if(multiRows.length > 0) {
            let arraysMap = {};
            multiRows.forEach(row => {
                let rowData = {};
                let arrayName = '';
                row.querySelectorAll('[data-field]').forEach(inp => {
                    let field = inp.getAttribute('data-field');
                    arrayName = inp.getAttribute('data-array') || arrayName;
                    if(field) rowData[field] = inp.value;
                });
                if(arrayName) {
                    if(!arraysMap[arrayName]) arraysMap[arrayName] = [];
                    arraysMap[arrayName].push(rowData);
                }
            });
            details = arraysMap;
        } else {
            item.querySelectorAll('[data-field]').forEach(inp => {
                let field = inp.getAttribute('data-field');
                if (field) details[field] = inp.value;
            });
        }
        servicios.push({ tipo: type, detalle: details });
    });
    document.getElementById('servicios_json').value = JSON.stringify(servicios);
    return true;
}

function renderExistingServices(svcs) {
    svcs.forEach(s => {
        const type = s.servicio_tipo;
        let details = {};
        try { details = JSON.parse(s.detalle_json); } catch(e) {}
        
        const chk = document.querySelector(`.svc-checkbox[value="${type}"]`);
        if(chk) {
            chk.checked = true;
            chk.dispatchEvent(new Event('change'));
            
            const container = document.getElementById('svc-form-' + type);
            if(type === 'vuelos' || type === 'hoteles') {
                const arrayName = type;
                const items = details[arrayName] || [];
                if(items.length > 0) {
                    container.querySelectorAll(`.${type === 'vuelos' ? 'vuelo' : 'hotel'}-item`).forEach(el => el.remove());
                    items.forEach(itemData => {
                        if(type === 'vuelos') addVueloItem(); else addHotelItem();
                        const lastItem = container.querySelector(`.${type === 'vuelos' ? 'vuelo' : 'hotel'}-item:last-child`);
                        Object.keys(itemData).forEach(key => {
                            const inp = lastItem.querySelector(`[data-field="${key}"]`);
                            if(inp) inp.value = itemData[key];
                        });
                    });
                }
            } else {
                Object.keys(details).forEach(key => {
                    const inp = container.querySelector(`[data-field="${key}"]`);
                    if(inp) inp.value = details[key];
                });
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    if(existingServices && existingServices.length > 0) {
        renderExistingServices(existingServices);
    }
});
</script>
