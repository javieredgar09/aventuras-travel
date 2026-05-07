<!-- admin/sales/create.php -->
<div class="mb-4 flex flex-col md:flex-row md:justify-between md:items-center bg-white p-3 rounded-xl border border-petroleo/5 shadow-sm">
    <div class="flex items-center gap-2">
        <a href="<?= Router::url('/admin/sales') ?>" class="w-8 h-8 rounded-full bg-superficie text-petroleo flex items-center justify-center hover:bg-turquesa hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl text-petroleo font-black leading-none">Nuevo Grupo de Viaje</h1>
            <p class="text-xs text-petroleo/60 mt-1">Registra un nuevo grupo familiar o viaje de promoción</p>
        </div>
    </div>
</div>

<form action="<?= Router::url('/admin/sales/store') ?>" method="POST" id="createGroupForm" class="space-y-4 pb-20" onsubmit="return prepararEnvio(event)">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <input type="hidden" name="servicios_json" id="servicios_json" value="[]">
    <input type="hidden" name="pasajeros_json" id="pasajeros_json" value="[]">

    <!-- Tarjeta Principal -->
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-turquesa">info</span>
            <h2 class="text-lg font-bold text-petroleo">Información General</h2>
        </div>
        
        <div class="p-6">
            <div class="mb-4 p-3 border rounded bg-white/50 flex items-center gap-3">
                <input type="checkbox" name="crear_usuario" id="crear_usuario" value="1" checked class="w-4 h-4">
                <div class="text-xs">
                    <label for="crear_usuario" class="font-bold text-petroleo">Crear usuario para el titular y enviar credenciales por correo</label>
                    <div class="text-petroleo/60">Si está activado, el sistema generará un código/usuario y contraseña para el primer pasajero (titular) y enviará las credenciales al correo indicado.</div>
                </div>
            </div>
            <!-- Tipo de Grupo -->
            <div class="mb-4">
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-2 tracking-wider">Tipo de Grupo</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex flex-row items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all border-orange-200 bg-orange-50 hover:bg-orange-100 peer-checked:border-orange-500" id="label-familiar">
                        <input type="radio" name="tipo" value="familiar" class="sr-only peer" checked onchange="toggleFormType()">
                        <span class="material-symbols-outlined text-2xl text-orange-600">family_restroom</span>
                        <div>
                            <span class="block font-bold text-sm text-orange-900">Familiar</span>
                            <span class="block text-[10px] text-orange-700 leading-tight mt-0.5">Representante único</span>
                        </div>
                        <div class="absolute inset-0 border-2 border-orange-500 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                    </label>

                    <label class="relative flex flex-row items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all border-indigo-200 bg-indigo-50 hover:bg-indigo-100 peer-checked:border-indigo-500" id="label-colegio">
                        <input type="radio" name="tipo" value="colegio" class="sr-only peer" onchange="toggleFormType()">
                        <span class="material-symbols-outlined text-2xl text-indigo-600">school</span>
                        <div>
                            <span class="block font-bold text-sm text-indigo-900">Colegio</span>
                            <span class="block text-[10px] text-indigo-700 leading-tight mt-0.5">Múltiples contratos</span>
                        </div>
                        <div class="absolute inset-0 border-2 border-indigo-500 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                    </label>
                </div>
            </div>

            <!-- Datos Básicos -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Nombre del Grupo *</label>
                    <input type="text" name="nombre" required placeholder="Ej: Familia García..." class="w-full px-3 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none">
                </div>
                
                <div id="container-institucion" class="hidden md:col-span-2 lg:col-span-1">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Institución Educativa</label>
                    <input type="text" name="institucion" placeholder="Ej: San Agustín" class="w-full px-3 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none">
                </div>

                <div class="md:col-span-1 lg:col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Destino Principal *</label>
                    <div class="relative">
                        <input type="text" name="destino" required placeholder="Ej: Punta Cana" class="w-full px-3 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none" id="destino-input" autocomplete="off">
                        <input type="hidden" name="destino_code" id="destino-code" value="">
                        <div id="destino-dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-petroleo/10 shadow-lg rounded-lg max-h-56 overflow-y-auto z-50"></div>
                    </div>
                </div>

                <div class="md:col-span-1 lg:col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Operador (Opcional)</label>
                    <input type="text" name="operador" placeholder="Ej: Euroandinos" class="w-full px-3 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none">
                </div>

                <div class="md:col-span-1 lg:col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Fecha Salida</label>
                    <input type="date" name="fecha_viaje" class="w-full px-3 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none">
                </div>
                <div class="md:col-span-1 lg:col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Fecha Retorno</label>
                    <input type="date" name="fecha_retorno" class="w-full px-3 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none">
                </div>
            </div>
        </div>
    </div> <!-- /Tarjeta Principal -->

    <!-- Configuración Económica -->
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden" id="seccion-economia">
        <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-500">payments</span>
            <h2 class="text-lg font-bold text-petroleo">Detalles Económicos del Grupo</h2>
        </div>
        
        <div class="p-6">
            <div id="mensaje-colegio" class="hidden mb-4 p-4 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-800 text-sm flex gap-2 items-start">
                <span class="material-symbols-outlined shrink-0">info</span>
                <p><strong>Nota para Grupos de Colegio:</strong> El "Valor Total" aquí representa el estimado global proyectado. Los pagos reales se gestionarán de forma independiente en cada <strong>Contrato Individual</strong> que crees después de guardar este grupo.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Moneda del Grupo *</label>
                    <select name="moneda_grupo" required class="w-full px-2 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none bg-white">
                        <option value="USD" selected>USD - Dólares</option>
                        <option value="PEN">PEN - Soles Peruanos</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Valor</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 font-bold text-petroleo/40 text-xs moneda-symbol">$</span>
                        <input type="number" step="0.01" name="valor_total" value="0.00" class="w-full pl-6 pr-2 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none">
                    </div>
                </div>
                
                <div id="container-deposito">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Abono Inicial</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 font-bold text-petroleo/40 text-xs moneda-symbol">$</span>
                        <input type="number" step="0.01" name="deposito" value="0.00" class="w-full pl-6 pr-2 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none">
                    </div>
                </div>

                <div id="container-tipopago">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Tipo Pago</label>
                    <select name="tipo_pago" id="tipo_pago" onchange="toggleCuotas()" class="w-full px-2 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none bg-white">
                        <option value="contado">Contado</option>
                        <option value="cuotas">Cuotas</option>
                    </select>
                </div>
                
                <div id="container-cuotas" class="hidden">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Nº Cuotas</label>
                    <input type="number" name="total_cuotas" min="0" max="24" value="0" class="w-full px-2 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none">
                </div>
            </div>
            
            <div id="container-meses" class="hidden mt-3 pt-3 border-t border-petroleo/10">
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Meses correspondientes</label>
                <input type="text" name="meses_pago" placeholder="Ej: Enero, Febrero, Marzo..." class="w-full px-3 py-1.5 text-xs rounded border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
        </div>
    </div> <!-- /Configuración Económica -->

    <!-- Servicios Incluidos -->
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500">luggage</span>
            <h2 class="text-lg font-bold text-petroleo">Servicios Incluidos en el Paquete</h2>
        </div>
        
        <div class="p-6">
            <p class="text-sm text-petroleo/60 mb-2">Selecciona los servicios que incluye el paquete.</p>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2 mb-4">
                <!-- Selectores de Servicio -->
                <?php foreach ($serviceMeta ?? [] as $key => $meta): ?>
                <label class="flex flex-row items-center gap-2 p-2 border-2 border-petroleo/10 bg-humo/50 rounded-xl cursor-pointer hover:bg-superficie transition-colors svc-toggle relative" data-svc="<?= $key ?>">
                    <input type="checkbox" class="sr-only peer svc-checkbox" value="<?= $key ?>">
                    <span class="text-lg"><?= $meta['emoji'] ?></span>
                    <span class="font-bold text-xs text-petroleo/80 peer-checked:text-turquesa-dark"><?= $meta['label'] ?></span>
                    <div class="absolute inset-0 border-2 border-turquesa rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                    <div class="absolute top-1 right-1 w-3 h-3 rounded-full bg-turquesa text-white items-center justify-center hidden peer-checked:flex">
                        <span class="material-symbols-outlined text-[8px] font-bold">check</span>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>

            <!-- Formularios de Servicios Dinámicos -->
            <div id="servicios-forms-container" class="space-y-4">
                <!-- Se llenarán vía JS -->
            </div>
        </div>
    </div> <!-- /Servicios Incluidos -->

    <!-- Pasajeros (Solo Familiar) -->
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden" id="seccion-pasajeros">
        <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-petroleo">groups</span>
                <h2 class="text-lg font-bold text-petroleo">Pasajeros del Grupo Familiar</h2>
            </div>
            <button type="button" onclick="addPasajero()" class="text-xs font-bold text-white bg-petroleo px-3 py-1.5 rounded-lg hover:bg-turquesa transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">person_add</span> Agregar Pasajero
            </button>
        </div>
        
        <div class="p-6">
            <div id="lista-pasajeros" class="space-y-3">
                <!-- Form inicial de pasajero 1 -->
                <div class="pax-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-humo/30 p-3 rounded-lg border border-petroleo/10">
                    <div class="md:col-span-3">
                        <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Nombre</label>
                        <input type="text" class="pax-nombre w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Apellidos</label>
                        <input type="text" class="pax-apellido w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Correo electrónico</label>
                        <input type="email" class="pax-email w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none" placeholder="ejemplo@correo.com">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Teléfono / Celular</label>
                        <input type="tel" class="pax-telefono w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none" placeholder="+51-9XXXXXXXX">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Tipo</label>
                        <select class="pax-tipo w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none bg-white">
                            <option value="adulto">Adulto</option>
                            <option value="menor">Menor</option>
                            <option value="infante">Inf</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Edad</label>
                        <input type="number" class="pax-edad w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none" min="0">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Pasaporte</label>
                        <input type="text" class="pax-pasaporte w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none">
                    </div>
                    <div class="md:col-span-1 text-right">
                        <button type="button" class="invisible w-full px-2 py-1.5 text-red-500 hover:bg-red-50 rounded flex justify-center">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </div>
                </div>
            </div>
            <p class="text-xs text-petroleo/40 mt-3">* Puedes agregar más pasajeros después desde la vista detallada del grupo.</p>
        </div>
    </div> <!-- /Pasajeros -->

    <!-- Botón de Envío Fijo -->
    <div class="fixed bottom-0 left-0 lg:left-64 right-0 p-3 bg-white border-t border-petroleo/10 shadow-[0_-10px_30px_rgba(0,0,0,0.05)] flex justify-end gap-2 z-40">
        <a href="<?= Router::url('/admin/sales') ?>" class="px-4 py-2 text-sm rounded-lg font-bold text-petroleo bg-superficie hover:bg-humo transition-colors">Cancelar</a>
        <button type="submit" class="px-6 py-2 text-sm rounded-lg font-black text-white bg-turquesa hover:bg-turquesa-dark shadow-lg shadow-turquesa/30 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">save</span>
            Guardar
        </button>
    </div>
</form>

<!-- Template Javascript -->
<script>
// Actualizar símbolo de moneda
function updateCurrencySymbol() {
    const monedaSelect = document.querySelector('select[name="moneda_grupo"]');
    const monedaSymbols = document.querySelectorAll('.moneda-symbol');
    const symbol = monedaSelect.value === 'PEN' ? 'S/ ' : '$ ';
    monedaSymbols.forEach(el => el.textContent = symbol);
}

// Configurar listener para cambios de moneda
document.addEventListener('DOMContentLoaded', () => {
    const monedaSelect = document.querySelector('select[name="moneda_grupo"]');
    if (monedaSelect) {
        monedaSelect.addEventListener('change', updateCurrencySymbol);
        updateCurrencySymbol(); // Inicializar
    }
});

// Manejar Tipos de Grupo
function toggleFormType() {
    const isColegio = document.querySelector('input[name="tipo"]:checked').value === 'colegio';
    
    // UI Elements
    const labelFam = document.getElementById('label-familiar');
    const labelCol = document.getElementById('label-colegio');
    const institucionContainer = document.getElementById('container-institucion');
    const seccionPasajeros = document.getElementById('seccion-pasajeros');
    const mensajeColegio = document.getElementById('mensaje-colegio');
    const depositoContainer = document.getElementById('container-deposito');
    const tipoPagoContainer = document.getElementById('container-tipopago');
    const cuotasContainer = document.getElementById('container-cuotas');
    const mesesContainer = document.getElementById('container-meses');

    if (isColegio) {
        // Modo Colegio
        institucionContainer.classList.remove('hidden');
        seccionPasajeros.classList.add('hidden'); // Colegios tienen pasajeros por contrato
        mensajeColegio.classList.remove('hidden');
        
        // Ocultar detalles economicos finos (se heredan a contratos)
        depositoContainer.classList.add('hidden');
        tipoPagoContainer.classList.add('hidden');
        cuotasContainer.classList.add('hidden');
        mesesContainer.classList.add('hidden');
    } else {
        // Modo Familiar
        institucionContainer.classList.add('hidden');
        seccionPasajeros.classList.remove('hidden');
        mensajeColegio.classList.add('hidden');
        
        // Mostrar detalles economicos
        depositoContainer.classList.remove('hidden');
        tipoPagoContainer.classList.remove('hidden');
        toggleCuotas(); // Restaurar estado de cuotas
    }
}

function toggleCuotas() {
    const isCuotas = document.getElementById('tipo_pago').value === 'cuotas';
    const containerCuotas = document.getElementById('container-cuotas');
    const containerMeses = document.getElementById('container-meses');
    
    if (isCuotas) {
        containerCuotas.classList.remove('hidden');
        containerMeses.classList.remove('hidden');
    } else {
        containerCuotas.classList.add('hidden');
        containerMeses.classList.add('hidden');
    }
}

// Pasajeros Dinámicos
function addPasajero() {
    const container = document.getElementById('lista-pasajeros');
    const row = document.createElement('div');
    row.className = 'pax-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-humo/30 p-3 rounded-lg border border-petroleo/10 mt-3';
    row.innerHTML = `
        <div class="md:col-span-3">
            <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Nombre</label>
            <input type="text" class="pax-nombre w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none">
        </div>
        <div class="md:col-span-3">
            <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Apellidos</label>
            <input type="text" class="pax-apellido w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none">
        </div>
        <div class="md:col-span-2">
            <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Tipo</label>
            <select class="pax-tipo w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none bg-white">
                <option value="adulto">Adulto</option>
                <option value="menor">Menor</option>
                <option value="infante">Inf</option>
            </select>
        </div>
        <div class="md:col-span-1">
            <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Edad</label>
            <input type="number" class="pax-edad w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none" min="0">
        </div>
        <div class="md:col-span-2">
            <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Pasaporte</label>
            <input type="text" class="pax-pasaporte w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none">
        </div>
        <div class="md:col-span-1 text-right">
            <button type="button" onclick="this.closest('.pax-row').remove()" class="w-full px-2 py-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 rounded transition-colors flex justify-center">
                <span class="material-symbols-outlined text-[16px]">delete</span>
            </button>
        </div>
    `;
    container.appendChild(row);
}

// Servicios Dinámicos Forms
const svcTemplates = {
    'hotel': `
        <div id="hoteles-list" class="space-y-3">
            <div class="hotel-item flex flex-col gap-3 items-start svc-multi-row pb-4">
                <div class="w-full relative">
                    <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo flex justify-between">Buscar Hotel (SerpAPI)</label>
                    <div class="flex gap-1 mt-1">
                        <input type="text" class="svc-input w-full p-2 border rounded text-sm bg-white hotel-search-input" placeholder="Ej: Barcelo Bavaro Punta Cana">
                        <button type="button" onclick="searchHotelAPI(this)" title="Buscar Hotel" class="px-3 py-2 text-white bg-turquesa hover:bg-turquesa-dark rounded flex items-center justify-center transition-colors"><span class="material-symbols-outlined text-[16px]">search</span></button>
                    </div>
                    <div class="hotel-results-dropdown hidden absolute top-full left-0 right-0 bg-white border border-petroleo/10 shadow-lg mt-1 z-50 max-h-48 overflow-y-auto rounded-lg divide-y divide-petroleo/5"></div>
                </div>
                <div class="w-full grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-6">
                        <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Nombre Confirmado</label>
                        <input type="text" data-field="nombre" data-array="hoteles" class="svc-input w-full p-2 border border-turquesa rounded mt-1 text-sm font-bold text-petroleo bg-superficie h-name-input">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Noches</label>
                        <input type="number" data-field="noches" data-array="hoteles" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white" value="1">
                    </div>
                    <div class="md:col-span-4 mt-6 text-right">
                        <button type="button" class="invisible py-2 text-petroleo/40"><span class="material-symbols-outlined text-sm">delete</span></button>
                    </div>
                    <div class="md:col-span-12">
                         <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Régimen Alimenticio / Detalles</label>
                         <input type="text" data-field="regimen" data-array="hoteles" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white" placeholder="Ej: All Inclusive, Habitación Doble">
                    </div>
                </div>
            </div>
        </div>
        <button type="button" onclick="addHotelItem()" class="mt-3 text-xs text-turquesa-dark font-bold hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">add</span> Añadir otro hotel</button>
    `,
    'vuelos': `
        <div id="vuelos-list" class="space-y-3">
            <div class="vuelo-item flex flex-col gap-3 items-start svc-multi-row pb-4 border-b border-petroleo/10 last:border-0 relative">
                
                <div class="w-full bg-turquesa/5 p-3 rounded-lg border border-turquesa/20 relative">
                    <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo mb-2 block border-b border-turquesa/10 pb-1">1. Buscar Vuelo (Google Flights)</label>
                    <div class="mb-2">
                        <select class="svc-input p-1.5 px-3 border rounded border-petroleo/10 text-xs bg-white font-bold text-petroleo w-auto inline-block focus:border-turquesa outline-none vuelo-tipo" onchange="toggleRetorno(this)">
                            <option value="2">Ida (One-way)</option>
                            <option value="1">Ida y Vuelta (Round-trip)</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <div class="relative">
                            <label class="text-[9px] text-petroleo/60 uppercase font-bold">Origen (IATA)</label>
                            <input type="text" class="svc-input w-full p-2 border rounded border-petroleo/10 text-sm bg-white vuelo-origen" placeholder="Ej: LIM" autocomplete="off" oninput="searchAirportsAPI(this)" onblur="setTimeout(()=>closeAirportDropdown(this), 250)">
                            <div class="airport-dropdown hidden absolute top-full left-0 right-0 bg-white border border-petroleo/10 shadow-xl mt-1 z-[60] max-h-48 overflow-y-auto rounded-lg divide-y divide-petroleo/5"></div>
                        </div>
                        <div class="relative">
                            <label class="text-[9px] text-petroleo/60 uppercase font-bold">Destino (IATA)</label>
                            <input type="text" class="svc-input w-full p-2 border rounded border-petroleo/10 text-sm bg-white vuelo-destino" placeholder="Ej: PUJ" autocomplete="off" oninput="searchAirportsAPI(this)" onblur="setTimeout(()=>closeAirportDropdown(this), 250)">
                            <div class="airport-dropdown hidden absolute top-full left-0 right-0 bg-white border border-petroleo/10 shadow-xl mt-1 z-[60] max-h-48 overflow-y-auto rounded-lg divide-y divide-petroleo/5"></div>
                        </div>
                        <div>
                            <label class="text-[9px] text-petroleo/60 uppercase font-bold">Fecha Salida</label>
                            <input type="date" class="svc-input w-full p-2 border border-petroleo/10 rounded text-sm bg-white vuelo-fecha">
                        </div>
                        <div>
                            <label class="text-[9px] text-petroleo/60 uppercase font-bold text-retorno-lbl transition-opacity opacity-50">Fecha Retorno</label>
                            <div class="flex gap-1">
                                <input type="date" class="svc-input w-full p-2 border border-petroleo/10 rounded text-sm bg-superficie opacity-50 cursor-not-allowed vuelo-retorno transition-all" disabled>
                                <button type="button" onclick="searchFlightAPI(this)" title="Buscar Vuelo SerpAPI" class="px-3 text-white bg-turquesa hover:bg-turquesa-dark rounded flex items-center justify-center transition-colors shadow-sm"><span class="material-symbols-outlined text-[16px]">search</span></button>
                            </div>
                        </div>
                    </div>
                    <div class="vuelo-results-dropdown hidden absolute top-full left-0 right-0 bg-white border border-petroleo/10 shadow-xl mt-1 z-50 max-h-64 overflow-y-auto rounded-lg divide-y divide-petroleo/5"></div>
                </div>
                
                <div class="w-full grid grid-cols-1 md:grid-cols-12 gap-3 mt-1">
                    <div class="md:col-span-12 mb-0"><label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">2. Detalles Confirmados</label></div>
                    <div class="md:col-span-5">
                        <label class="text-[9px] uppercase font-bold text-petroleo/60">Aerolínea / Número Vuelo</label>
                        <div class="flex gap-1 mt-1">
                            <input type="text" data-field="aerolinea" data-array="vuelos" class="svc-input w-2/3 p-2 border border-petroleo/20 rounded text-sm bg-superficie font-bold text-petroleo" placeholder="Ej: LATAM">
                            <input type="text" data-field="numero" data-array="vuelos" class="svc-input w-1/3 p-2 border border-petroleo/20 rounded text-sm bg-superficie font-mono font-bold text-petroleo" placeholder="2361">
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label class="text-[9px] uppercase font-bold text-petroleo/60">Ruta Confirmada</label>
                        <input type="text" data-field="ruta" data-array="vuelos" class="svc-input w-full p-2 border border-petroleo/20 rounded mt-1 text-sm bg-superficie font-bold text-petroleo h-ruta-input" placeholder="Ej: LIM - PUJ">
                    </div>
                    <div class="md:col-span-4 mt-5 text-right flex items-center justify-end">
                        <button type="button" class="invisible py-2 text-petroleo/40"><span class="material-symbols-outlined text-sm">delete</span></button>
                    </div>
                    <div class="md:col-span-6">
                        <label class="text-[9px] uppercase font-bold text-petroleo/60">Salida (Local)</label>
                        <input type="datetime-local" data-field="salida" data-array="vuelos" class="svc-input w-full p-2 border border-petroleo/20 rounded mt-1 text-sm bg-white">
                    </div>
                    <div class="md:col-span-6">
                        <label class="text-[9px] uppercase font-bold text-petroleo/60">Llegada (Local)</label>
                        <input type="datetime-local" data-field="llegada" data-array="vuelos" class="svc-input w-full p-2 border border-petroleo/20 rounded mt-1 text-sm bg-white">
                    </div>
                </div>
            </div>
        </div>
        <button type="button" onclick="addVueloItem()" class="mt-3 text-xs text-turquesa-dark font-bold hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">add</span> Añadir otro vuelo/tramo</button>
    `,
    'traslados': `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Ruta de Traslado</label>
                <select data-field="ruta" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white">
                    <option value="in" selected>IN (Aeropuerto - Hotel)</option>
                    <option value="out">OUT (Hotel - Aeropuerto)</option>
                    <option value="ambos">IN / OUT (Ambos)</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Tipo de Servicio</label>
                <select data-field="tipo_servicio" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white">
                    <option value="compartido" selected>Compartido (Shuttle)</option>
                    <option value="privado">Privado</option>
                </select>
            </div>
            <div class="md:col-span-2"><label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Detalles / Vehículo</label><input type="text" data-field="detalle" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white" placeholder="Ej: Van 15 pax"></div>
        </div>
    `,
    'excursiones': `
        <div>
            <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo block mb-1">Tours Incluidos (separados por renglón)</label>
            <textarea data-field="lista_tours" class="svc-input w-full p-3 border rounded text-xs bg-white" rows="3" placeholder="Ej:
1. Tour a Isla Saona
2. City Tour Santo Domingo"></textarea>
        </div>
    `,
    'seguro': `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div><label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Aseguradora</label><input type="text" data-field="nombre" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white" placeholder="Ej: Assist Card"></div>
            <div><label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Tipo de Plan</label><input type="text" data-field="plan" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white" placeholder="Ej: Premium 60k"></div>
            <div><label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Cobertura Total</label><input type="text" data-field="cobertura" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white" placeholder="Ej: $60,000 USD"></div>
        </div>
    `
};

function addHotelItem() {
    const list = document.getElementById('hoteles-list');
    const html = `
        <div class="hotel-item flex flex-col gap-3 items-start svc-multi-row pt-4 border-t border-petroleo/10 mt-3 anim-fade-in pb-4">
            <div class="w-full relative">
                <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo flex justify-between">Buscar Hotel (SerpAPI)</label>
                <div class="flex gap-1 mt-1">
                    <input type="text" class="svc-input w-full p-2 border rounded text-sm bg-white hotel-search-input" placeholder="Ej: Barcelo Bavaro Punta Cana">
                    <button type="button" onclick="searchHotelAPI(this)" title="Buscar Hotel" class="px-3 py-2 text-white bg-turquesa hover:bg-turquesa-dark rounded flex items-center justify-center transition-colors"><span class="material-symbols-outlined text-[16px]">search</span></button>
                </div>
                <div class="hotel-results-dropdown hidden absolute top-full left-0 right-0 bg-white border border-petroleo/10 shadow-lg mt-1 z-50 max-h-48 overflow-y-auto rounded-lg divide-y divide-petroleo/5"></div>
            </div>
            <div class="w-full grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-6">
                    <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Nombre Confirmado</label>
                    <input type="text" data-field="nombre" data-array="hoteles" class="svc-input w-full p-2 border border-turquesa rounded mt-1 text-sm font-bold text-petroleo bg-superficie h-name-input">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Noches</label>
                    <input type="number" data-field="noches" data-array="hoteles" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white" value="1">
                </div>
                <div class="md:col-span-4 mt-6 text-right">
                    <button type="button" onclick="this.closest('.hotel-item').remove()" class="w-full py-2 text-red-500 hover:bg-red-50 shadow-sm border border-red-100 rounded bg-white transition-colors"><span class="material-symbols-outlined text-[16px]">delete</span></button>
                </div>
                <div class="md:col-span-12">
                     <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">Régimen Alimenticio / Detalles</label>
                     <input type="text" data-field="regimen" data-array="hoteles" class="svc-input w-full p-2 border rounded mt-1 text-sm bg-white" placeholder="Ej: All Inclusive, Habitación Doble">
                </div>
            </div>
        </div>
    `;
    list.insertAdjacentHTML('beforeend', html);
}

function addVueloItem() {
    const list = document.getElementById('vuelos-list');
    const html = `
        <div class="vuelo-item flex flex-col gap-3 items-start svc-multi-row pt-4 border-t border-petroleo/10 mt-3 anim-fade-in pb-4">
            
            <div class="w-full bg-turquesa/5 p-3 rounded-lg border border-turquesa/20 relative">
                <label class="text-[10px] uppercase tracking-wider font-bold text-petroleo mb-2 block border-b border-turquesa/10 pb-1">1. Buscar Vuelo (Google Flights)</label>
                <div class="mb-2">
                    <select class="svc-input p-1.5 px-3 border rounded border-petroleo/10 text-xs bg-white font-bold text-petroleo w-auto inline-block focus:border-turquesa outline-none vuelo-tipo" onchange="toggleRetorno(this)">
                        <option value="2">Ida (One-way)</option>
                        <option value="1">Ida y Vuelta (Round-trip)</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                    <div class="relative">
                        <label class="text-[9px] text-petroleo/60 uppercase font-bold">Origen (IATA)</label>
                        <input type="text" class="svc-input w-full p-2 border rounded border-petroleo/10 text-sm bg-white vuelo-origen" placeholder="Ej: LIM" autocomplete="off" oninput="searchAirportsAPI(this)" onblur="setTimeout(()=>closeAirportDropdown(this), 250)">
                        <div class="airport-dropdown hidden absolute top-full left-0 right-0 bg-white border border-petroleo/10 shadow-xl mt-1 z-[60] max-h-48 overflow-y-auto rounded-lg divide-y divide-petroleo/5"></div>
                    </div>
                    <div class="relative">
                        <label class="text-[9px] text-petroleo/60 uppercase font-bold">Destino (IATA)</label>
                        <input type="text" class="svc-input w-full p-2 border rounded border-petroleo/10 text-sm bg-white vuelo-destino" placeholder="Ej: PUJ" autocomplete="off" oninput="searchAirportsAPI(this)" onblur="setTimeout(()=>closeAirportDropdown(this), 250)">
                        <div class="airport-dropdown hidden absolute top-full left-0 right-0 bg-white border border-petroleo/10 shadow-xl mt-1 z-[60] max-h-48 overflow-y-auto rounded-lg divide-y divide-petroleo/5"></div>
                    </div>
                    <div>
                        <label class="text-[9px] text-petroleo/60 uppercase font-bold">Fecha Salida</label>
                        <input type="date" class="svc-input w-full p-2 border border-petroleo/10 rounded text-sm bg-white vuelo-fecha">
                    </div>
                    <div>
                        <label class="text-[9px] text-petroleo/60 uppercase font-bold text-retorno-lbl transition-opacity opacity-50">Fecha Retorno</label>
                        <div class="flex gap-1">
                            <input type="date" class="svc-input w-full p-2 border border-petroleo/10 rounded text-sm bg-superficie opacity-50 cursor-not-allowed vuelo-retorno transition-all" disabled>
                            <button type="button" onclick="searchFlightAPI(this)" title="Buscar Vuelo SerpAPI" class="px-3 text-white bg-turquesa hover:bg-turquesa-dark rounded flex items-center justify-center transition-colors shadow-sm"><span class="material-symbols-outlined text-[16px]">search</span></button>
                        </div>
                    </div>
                </div>
                <div class="vuelo-results-dropdown hidden absolute top-full left-0 right-0 bg-white border border-petroleo/10 shadow-xl mt-1 z-50 max-h-64 overflow-y-auto rounded-lg divide-y divide-petroleo/5"></div>
            </div>
            
            <div class="w-full grid grid-cols-1 md:grid-cols-12 gap-3 mt-1">
                <div class="md:col-span-12 mb-0"><label class="text-[10px] uppercase tracking-wider font-bold text-petroleo">2. Detalles Confirmados</label></div>
                <div class="md:col-span-5">
                    <label class="text-[9px] uppercase font-bold text-petroleo/60">Aerolínea / Número Vuelo</label>
                    <div class="flex gap-1 mt-1">
                        <input type="text" data-field="aerolinea" data-array="vuelos" class="svc-input w-2/3 p-2 border border-petroleo/20 rounded text-sm bg-superficie font-bold text-petroleo" placeholder="Ej: LATAM">
                        <input type="text" data-field="numero" data-array="vuelos" class="svc-input w-1/3 p-2 border border-petroleo/20 rounded text-sm bg-superficie font-mono font-bold text-petroleo" placeholder="2361">
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="text-[9px] uppercase font-bold text-petroleo/60">Ruta Confirmada</label>
                    <input type="text" data-field="ruta" data-array="vuelos" class="svc-input w-full p-2 border border-petroleo/20 rounded mt-1 text-sm bg-superficie font-bold text-petroleo h-ruta-input" placeholder="Ej: LIM - PUJ">
                </div>
                <div class="md:col-span-4 mt-5 text-right flex items-center justify-end">
                    <button type="button" onclick="this.closest('.vuelo-item').remove()" class="w-full py-2 text-red-500 hover:bg-red-50 shadow-sm border border-red-100 rounded bg-white transition-colors"><span class="material-symbols-outlined text-[16px]">delete</span></button>
                </div>
                <div class="md:col-span-6">
                    <label class="text-[9px] uppercase font-bold text-petroleo/60">Salida (Local)</label>
                    <input type="datetime-local" data-field="salida" data-array="vuelos" class="svc-input w-full p-2 border border-petroleo/20 rounded mt-1 text-sm bg-white">
                </div>
                <div class="md:col-span-6">
                    <label class="text-[9px] uppercase font-bold text-petroleo/60">Llegada (Local)</label>
                    <input type="datetime-local" data-field="llegada" data-array="vuelos" class="svc-input w-full p-2 border border-petroleo/20 rounded mt-1 text-sm bg-white">
                </div>
            </div>
        </div>
    `;
    list.insertAdjacentHTML('beforeend', html);
}

function searchFlightAPI(btnElement) {
    const container = btnElement.closest('.vuelo-item');
    const origen = container.querySelector('.vuelo-origen').value.trim();
    const destino = container.querySelector('.vuelo-destino').value.trim();
    const fecha = container.querySelector('.vuelo-fecha').value.trim();
    const tipo = container.querySelector('.vuelo-tipo') ? container.querySelector('.vuelo-tipo').value : '2';
    const retorno = container.querySelector('.vuelo-retorno') ? container.querySelector('.vuelo-retorno').value.trim() : '';
    const dropdown = container.querySelector('.vuelo-results-dropdown');
    
    // Validar
    if(!origen || !destino || !fecha) {
        alert("Por favor completa Origen, Destino y Fecha Salida para poder buscar en Google Flights API.");
        return;
    }
    
    if(tipo === '1' && !retorno) {
        alert("Seleccionaste Ida y Vuelta, debes poner Fecha de Retorno.");
        return;
    }
    
    let url = `<?= Router::url("/admin/sales/search-flight") ?>?origen=${encodeURIComponent(origen)}&destino=${encodeURIComponent(destino)}&fecha=${encodeURIComponent(fecha)}`;
    if(tipo === '1') {
        url += `&fecha_retorno=${encodeURIComponent(retorno)}`;
    }
    
    btnElement.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span>';
    dropdown.innerHTML = '<div class="p-4 text-xs text-center text-petroleo/60 flex flex-col items-center"><span class="material-symbols-outlined animate-spin mb-1 text-turquesa text-[24px]">sync</span> Buscando Vuelos en Google Flights...</div>';
    dropdown.classList.remove('hidden');
    
    fetch(url)
    .then(res => res.json())
    .then(data => {
        if(data.success && data.vuelos && data.vuelos.length > 0) {
            dropdown.innerHTML = '';
            
            // Add helper text if round trip
            if(tipo === '1') {
                let note = document.createElement('div');
                note.className = "text-[9px] p-2 bg-indigo-50 text-indigo-800 font-bold border-b border-indigo-100 uppercase tracking-wider";
                note.innerText = "⭐ Mostrando combinaciones Outbound (Ida). La fecha de retorno fue pasada a la búsqueda.";
                dropdown.appendChild(note);
            }

            data.vuelos.forEach(v => {
                let div = document.createElement('div');
                div.className = 'p-3 hover:bg-humo cursor-pointer transition-colors';
                
                // Formatear display de fecha y horas
                const tSalida = v.salida_iso ? v.salida_iso.replace('T', ' ') : 'N/A';
                const tLlegada = v.llegada_iso ? v.llegada_iso.replace('T', ' ') : 'N/A';
                
                div.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div class="font-bold text-sm text-petroleo">${v.aerolinea}</div>
                        <div class="text-xs font-mono text-petroleo/70 bg-humo px-1.5 rounded">${v.ruta}</div>
                    </div>
                    <div class="text-[10px] text-turquesa-dark mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[12px]">flight_takeoff</span> ${tSalida}  
                        <span class="material-symbols-outlined text-[12px] ml-2">flight_land</span> ${tLlegada}
                    </div>
                    <div class="text-[9px] text-petroleo/50 mt-0.5">Vuelo(s): ${v.numero}</div>
                `;
                
                div.onclick = function() {
                    container.querySelector('[data-field="ruta"]').value = v.ruta;
                    container.querySelector('[data-field="aerolinea"]').value = v.aerolinea;
                    container.querySelector('[data-field="numero"]').value = v.numero;
                    
                    if(v.salida_iso) container.querySelector('[data-field="salida"]').value = v.salida_iso.slice(0, 16);
                    if(v.llegada_iso) container.querySelector('[data-field="llegada"]').value = v.llegada_iso.slice(0, 16);
                    
                    dropdown.classList.add('hidden');
                };
                dropdown.appendChild(div);
            });
        } else {
            dropdown.innerHTML = `<div class="p-3 text-xs text-center text-red-500">${data.error || "No se encontraron vuelos para esta ruta y fecha."}</div>`;
            setTimeout(() => dropdown.classList.add('hidden'), 4000);
        }
    })
    .catch(err => {
        console.error(err);
        dropdown.innerHTML = `<div class="p-3 text-xs text-center text-red-500">Error de conexión a la API.</div>`;
        setTimeout(() => dropdown.classList.add('hidden'), 4000);
    })
    .finally(() => {
        btnElement.innerHTML = '<span class="material-symbols-outlined text-[16px]">search</span>';
    });
}

function toggleRetorno(selectEl) {
    const container = selectEl.closest('.w-full');
    const inputRetorno = container.querySelector('.vuelo-retorno');
    const labelRetorno = container.querySelector('.text-retorno-lbl');
    
    if (selectEl.value === '1') {
        inputRetorno.disabled = false;
        inputRetorno.classList.remove('opacity-50', 'bg-superficie', 'cursor-not-allowed');
        inputRetorno.classList.add('bg-white');
        labelRetorno.classList.remove('opacity-50');
    } else {
        inputRetorno.disabled = true;
        inputRetorno.value = '';
        inputRetorno.classList.add('opacity-50', 'bg-superficie', 'cursor-not-allowed');
        inputRetorno.classList.remove('bg-white');
        labelRetorno.classList.add('opacity-50');
    }
}

let airportTimeout = null;

function searchAirportsAPI(inputEl) {
    clearTimeout(airportTimeout);
    const query = inputEl.value.trim();
    const dropdown = inputEl.nextElementSibling;
    
    if(query.length < 2) {
        dropdown.classList.add('hidden');
        return;
    }
    
    dropdown.innerHTML = '<div class="p-3 text-xs text-center text-petroleo/50"><span class="material-symbols-outlined text-[14px] animate-spin align-middle inline-block">sync</span> Buscando en SerpAPI...</div>';
    dropdown.classList.remove('hidden');
    
    airportTimeout = setTimeout(() => {
        fetch('<?= Router::url("/admin/sales/search-airport") ?>?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if(data.success && data.airports && data.airports.length > 0) {
                dropdown.innerHTML = '';
                data.airports.forEach(a => {
                    const code = a.flight_id || a.airport_code || a.id || '';
                    const title = `(${code}) ${a.name || ''}`;
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-humo cursor-pointer transition-colors text-[11px] leading-tight flex items-start gap-2 border-b border-petroleo/5 last:border-0';
                    div.innerHTML = `<span class="material-symbols-outlined opacity-40 text-[14px] mt-0.5">flight</span> <div><span class="font-bold text-petroleo">${code}</span> - ${a.name || ''}</div>`;
                    
                    div.onclick = function() {
                        inputEl.value = code;
                        dropdown.classList.add('hidden');
                    };
                    dropdown.appendChild(div);
                });
            } else {
                dropdown.innerHTML = '<div class="p-3 text-xs text-center text-red-500 font-bold">Sin resultados</div>';
            }
        })
        .catch(err => {
            console.error(err);
            dropdown.innerHTML = '<div class="p-3 text-xs text-center text-red-500">Error conectando a SerpAPI</div>';
        });
    }, 400); // 400ms debounce
}

function closeAirportDropdown(inputEl) {
    const dropdown = inputEl.nextElementSibling;
    if(dropdown) {
        dropdown.classList.add('hidden');
    }
}

function searchHotelAPI(btnElement) {
    const container = btnElement.closest('.hotel-item');
    const inputEl = container.querySelector('.hotel-search-input');
    const query = inputEl.value.trim();
    const dropdown = container.querySelector('.hotel-results-dropdown');
    
    // Añadir contexto de destino principal si está presente
    const destinoInput = document.querySelector('input[name="destino"]');
    const destinoVal = destinoInput ? destinoInput.value.trim() : '';
    let qParam = query;
    
    // Si no hay consulta del usuario, usar el destino seleccionado como término de búsqueda
    if ((!query || query.length < 3) && destinoVal) {
        qParam = destinoVal;
    }

    // Si aún no hay término válido, pedir mínimo
    if (!qParam || qParam.length < 2) {
        // mostrar mensaje no intrusivo en el dropdown
        dropdown.innerHTML = '<div class="p-3 text-xs text-center text-petroleo/60">Escribe el nombre del hotel (mín. 2 caracteres) o selecciona un Destino Principal primero.</div>';
        dropdown.classList.remove('hidden');
        setTimeout(() => dropdown.classList.add('hidden'), 2500);
        return;
    }
    
    btnElement.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span>';
    dropdown.innerHTML = '<div class="p-4 text-xs text-center text-petroleo/60 flex flex-col items-center"><span class="material-symbols-outlined animate-spin mb-1 text-turquesa text-[24px]">sync</span> Buscando hoteles...</div>';
    dropdown.classList.remove('hidden');
    
    const destCode = document.getElementById('destino-code') ? document.getElementById('destino-code').value : '';
    const urlParams = new URLSearchParams();
    urlParams.append('q', qParam);
    if (destinoVal) urlParams.append('dest', destinoVal);
    if (destCode) urlParams.append('dest_code', destCode);
    
    fetch('<?= Router::url("/admin/sales/search-hotel") ?>?' + urlParams.toString())
    .then(res => res.json())
    .then(data => {
        btnElement.innerHTML = '<span class="material-symbols-outlined text-[16px]">search</span>';
        
        if(data.success && data.hoteles && data.hoteles.length > 0) {
            dropdown.innerHTML = '';
            data.hoteles.forEach((h, idx) => {
                let div = document.createElement('div');
                div.className = 'p-3 hover:bg-humo cursor-pointer transition-colors border-b border-petroleo/5 last:border-0';
                
                const ratingHtml = h.rating ? `<div class="text-[10px] text-turquesa-dark flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[12px]">star</span> ${h.rating}</div>` : '';
                div.innerHTML = `<div class="font-bold text-sm text-petroleo leading-tight">${h.nombre}</div>${ratingHtml}`;
                
                div.onclick = function() {
                    container.querySelector('.h-name-input').value = h.nombre;
                    inputEl.value = h.nombre;
                    dropdown.classList.add('hidden');
                    // Trigger event para sincronizar con array serializado
                    const inputField = container.querySelector('[data-field="nombre"]');
                    if (inputField) inputField.value = h.nombre;
                };
                dropdown.appendChild(div);
            });
            dropdown.classList.remove('hidden');
        } else {
            let errMsg = data.error || "No se encontraron hoteles. Intenta con otro término de búsqueda.";
            dropdown.innerHTML = `<div class="p-3 text-xs text-center text-red-500 font-bold">${errMsg}</div>`;
            dropdown.classList.remove('hidden');
            setTimeout(() => dropdown.classList.add('hidden'), 4000);
        }
    })
    .catch(err => {
        btnElement.innerHTML = '<span class="material-symbols-outlined text-[16px]">search</span>';
        console.error('Hotel search error:', err);
        dropdown.innerHTML = `<div class="p-3 text-xs text-center text-red-500">Error conectando con la API. Intenta nuevamente.</div>`;
        dropdown.classList.remove('hidden');
        setTimeout(() => dropdown.classList.add('hidden'), 3000);
    });
}

// Close Dropdowns on click outside
document.addEventListener('click', function(e) {
    if(!e.target.closest('.hotel-item') && !e.target.classList.contains('hotel-results-dropdown')) {
        document.querySelectorAll('.hotel-results-dropdown').forEach(d => d.classList.add('hidden'));
    }
    if(!e.target.closest('.vuelo-item') && !e.target.classList.contains('vuelo-results-dropdown')) {
        document.querySelectorAll('.vuelo-results-dropdown').forEach(d => d.classList.add('hidden'));
    }
});

// DESTINO autocomplete
let destinoTimer = null;
let selectedDestino = '';
const destinoInput = document.getElementById('destino-input');
const destinoDropdown = document.getElementById('destino-dropdown');
if (destinoInput) {
    destinoInput.addEventListener('input', function(){
        const q = this.value.trim();
        clearTimeout(destinoTimer);
        if (q.length < 2) { destinoDropdown.classList.add('hidden'); return; }
        destinoTimer = setTimeout(() => {
            fetch('<?= Router::url("/admin/sales/search-destination") ?>?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                destinoDropdown.innerHTML = '';
                if (data.success && data.places && data.places.length) {
                    data.places.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-humo cursor-pointer transition-colors text-sm';
                        // Mostrar nombre y ciudad/país si están disponibles
                        const subtitle = (p.country || p.region || p.address) ? `<div class="text-xs text-petroleo/60 mt-1">${p.country || p.region || p.address}</div>` : '';
                        div.innerHTML = `<div class="font-bold">${p.name || p.title || q}</div>${subtitle}`;
                        div.setAttribute('data-id', p.id || p.place_id || '');
                        div.onclick = function(){
                            const display = p.name || p.title || q;
                            destinoInput.value = display;
                            selectedDestino = display;
                            // guardar código/identificador si viene
                            const codeVal = p.id || p.place_id || '';
                            if (codeVal && document.getElementById('destino-code')) {
                                document.getElementById('destino-code').value = codeVal;
                            }
                            destinoDropdown.classList.add('hidden');
                        };
                        destinoDropdown.appendChild(div);
                    });
                    destinoDropdown.classList.remove('hidden');
                } else {
                    destinoDropdown.innerHTML = '<div class="p-3 text-xs text-center text-petroleo/60">No se encontraron destinos</div>';
                    destinoDropdown.classList.remove('hidden');
                    setTimeout(()=>destinoDropdown.classList.add('hidden'), 2000);
                }
            }).catch(err => {
                console.error(err);
            });
        }, 300);
    });

    // Close dropdown on outside click handled above; also clear when blurred
    destinoInput.addEventListener('blur', function(){ setTimeout(()=>destinoDropdown.classList.add('hidden'), 200); });
}

// Update hotel search to prefer selectedDestino
function getSelectedDestino() {
    return selectedDestino || (document.querySelector('input[name="destino"]') ? document.querySelector('input[name="destino"]').value.trim() : '');
}

// Override previous searchHotelAPI to use getSelectedDestino() when available (keeps existing signature)
// The existing function uses destino input directly; no further change required because earlier code reads input[name="destino"].

document.querySelectorAll('.svc-checkbox').forEach(chk => {
    chk.addEventListener('change', function() {
        const type = this.value;
        const containerId = 'svc-form-' + type;
        const container = document.getElementById('servicios-forms-container');
        
        // Estilos visuales del label padre
        const labelEl = this.parentElement;
        if(this.checked) {
            labelEl.classList.remove('border-petroleo/10');
            labelEl.classList.add('border-turquesa');
        } else {
            labelEl.classList.add('border-petroleo/10');
            labelEl.classList.remove('border-turquesa');
        }

        if (this.checked) {
            // Añadir form
            if (!document.getElementById(containerId)) {
                let html = `
                    <div id="${containerId}" class="svc-form-item p-4 border border-petroleo/10 rounded-lg bg-superficie/30 relative mt-4 anim-fade-in" data-type="${type}">
                        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-petroleo/5">
                            <span class="font-bold text-petroleo uppercase text-[10px] tracking-wider">${labelEl.querySelector('span.font-bold').innerText}</span>
                        </div>
                        ${svcTemplates[type] || '<p>Detalles predeterminados requeridos.</p>'}
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
            }
        } else {
            // Remover form (y limpiar valor)
            const el = document.getElementById(containerId);
            if (el) el.remove();
        }
    });
});

// Preparar y Serializar Datos JSON antes de Enviar
function prepararEnvio(e) {
    try {
        // 1. Recopilar Servicios
        let servicios = [];
        document.querySelectorAll('.svc-form-item').forEach(item => {
            let type = item.getAttribute('data-type');
            let details = {};
            
            // Comprobar si es un servicio con multiples arrays (hoteles, vuelos)
            const multiRows = item.querySelectorAll('.svc-multi-row');
            const hasArrays = multiRows.length > 0 || item.querySelectorAll('[data-array]').length > 0;
            
            if(hasArrays && multiRows.length > 0) {
                let arraysMap = {};
                multiRows.forEach(row => {
                    let rowData = {};
                    let arrayName = '';
                    row.querySelectorAll('.svc-input').forEach(inp => {
                        let field = inp.getAttribute('data-field');
                        let aName = inp.getAttribute('data-array');
                        if (aName) arrayName = aName;
                        if(field) rowData[field] = inp.value;
                    });
                    
                    if(arrayName) {
                        if(!arraysMap[arrayName]) arraysMap[arrayName] = [];
                        arraysMap[arrayName].push(rowData);
                    }
                });
                details = arraysMap;
            } else {
                // Servicio normal (traslados, excursiones...)
                item.querySelectorAll('.svc-input').forEach(inp => {
                    let field = inp.getAttribute('data-field');
                    if (field) details[field] = inp.value;
                });
            }
            
            servicios.push({ tipo: type, detalle: details });
        });

        const jsonStr = JSON.stringify(servicios);
        document.getElementById('servicios_json').value = jsonStr;
        console.log("Servicios serializados:", jsonStr);

        // 2. Recopilar Pasajeros (solo si es Familiar)
        const tipoChecked = document.querySelector('input[name="tipo"]:checked');
        const isColegio = tipoChecked && tipoChecked.value === 'colegio';
        
        if (!isColegio) {
            let pasajeros = [];
            document.querySelectorAll('.pax-row').forEach(row => {
                const nombreInp = row.querySelector('.pax-nombre');
                const apellidoInp = row.querySelector('.pax-apellido');
                const emailInp = row.querySelector('.pax-email');
                const telInp = row.querySelector('.pax-telefono');
                const tipoInp = row.querySelector('.pax-tipo');
                const edadInp = row.querySelector('.pax-edad');
                const pasaporteInp = row.querySelector('.pax-pasaporte');
                
                if (nombreInp && nombreInp.value.trim() !== '') {
                    pasajeros.push({
                        nombre: nombreInp.value,
                        apellido: apellidoInp ? apellidoInp.value : '',
                            email: emailInp ? emailInp.value : '',
                            telefono: telInp ? telInp.value : '',
                        tipo: tipoInp ? tipoInp.value : 'adulto',
                        edad: edadInp ? edadInp.value : '',
                        pasaporte: pasaporteInp ? pasaporteInp.value : ''
                    });
                }
            });
            document.getElementById('pasajeros_json').value = JSON.stringify(pasajeros);
        } else {
            document.getElementById('pasajeros_json').value = '[]';
        }

        return true;
    } catch (err) {
        console.error("Error en prepararEnvio:", err);
        alert("Ocurrió un error al procesar el formulario: " + err.message);
        return false; // Bloquear envío si hay error
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    toggleFormType();
});
</script>
