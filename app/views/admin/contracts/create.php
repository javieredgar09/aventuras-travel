<!-- admin/contracts/create.php -->
<div class="mb-4 flex flex-col md:flex-row md:justify-between md:items-center bg-white p-3 rounded-xl border border-petroleo/5 shadow-sm">
    <div class="flex items-center gap-2">
        <a href="<?= Router::url('/admin/sales/' . $grupo['id']) ?>" class="w-8 h-8 rounded-full bg-superficie text-petroleo flex items-center justify-center hover:bg-turquesa hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl text-petroleo font-black leading-none">Generar Nuevo Contrato</h1>
            <p class="text-xs text-petroleo/60 mt-1">Grupo: <?= htmlspecialchars($grupo['nombre']) ?> (<?= ucfirst($grupo['tipo']) ?>)</p>
        </div>
    </div>
</div>

<form action="<?= Router::url('/admin/sales/' . $grupo['id'] . '/contract') ?>" method="POST" id="createContractForm" class="space-y-4 pb-20" onsubmit="return prepararEnvioContrato(event)">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <input type="hidden" name="cuotas_json" id="cuotas_json" value="[]">
    <input type="hidden" name="pasajeros_json" id="pasajeros_json" value="[]">

    <!-- 1. DATOS DEL TITULAR (RESPONSABLE DE PAGO) -->
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-turquesa">person</span>
            <h2 class="text-lg font-bold text-petroleo">1. Datos del Titular (Responsable)</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Código de Contrato (Opcional)</label>
                <input type="text" name="codigo" placeholder="Ej: CCPA-2026-003, dejar vacío para auto-generar" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none font-mono">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Nombre Completo *</label>
                <input type="text" name="titular_nombre" required placeholder="Ej: Samuel Alejandro Torres" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Fecha de Firma *</label>
                <input type="date" name="fecha_firma" required value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">DNI / Pasaporte *</label>
                <input type="text" name="titular_documento" required placeholder="Ej: 40522887" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Teléfono</label>
                <input type="text" name="titular_telefono" placeholder="Ej: 999888777" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Correo Electrónico</label>
                <input type="email" name="titular_correo" placeholder="correo@ejemplo.com" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Dirección</label>
                <input type="text" name="titular_direccion" placeholder="Dirección completa" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
            </div>
        </div>
    </div>

    <!-- 2. PASAJEROS -->
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-500">groups</span>
                <h2 class="text-lg font-bold text-petroleo">2. Pasajeros Incluidos</h2>
            </div>
            <button type="button" onclick="addPaxRow()" class="text-xs font-bold text-white bg-petroleo px-3 py-1.5 rounded-lg hover:bg-turquesa transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">person_add</span> Agregar Pasajero
            </button>
        </div>
        <div class="p-6">
            <div id="lista-pasajeros" class="space-y-3">
                <div class="pax-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-humo/30 p-3 rounded-lg border border-petroleo/10">
                    <div class="md:col-span-3">
                        <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Nombre</label>
                        <input type="text" class="pax-nombre w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none" required>
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
                        <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">DNI/Pasaporte</label>
                        <input type="text" class="pax-pasaporte w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none">
                    </div>
                    <div class="md:col-span-1 text-right">
                        <button type="button" class="invisible w-full px-2 py-1.5 text-red-500 hover:bg-red-50 rounded"><span class="material-symbols-outlined text-[16px]">delete</span></button>
                    </div>
                </div>
            </div>
            <button type="button" onclick="copiarTitularAPasajero()" class="mt-3 text-xs text-turquesa-dark font-bold hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">content_copy</span> El titular también es pasajero (Copiar Datos)
            </button>
        </div>
    </div>

    <!-- 3. CONFIGURACIÓN ECONÓMICA -->
    <div class="bg-white rounded-xl border border-petroleo/5 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-petroleo/5 bg-superficie/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-500">payments</span>
            <h2 class="text-lg font-bold text-petroleo">3. Resumen y Plan de Pagos</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Valor Total (USD) *</label>
                    <input type="number" step="0.01" id="valor_total" name="valor_total" value="0.00" oninput="calcularSaldo()" required class="w-full px-3 py-2 text-lg font-bold rounded-lg border border-petroleo/20 focus:border-turquesa outline-none text-petroleo bg-superficie">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Prepago / Ingreso (USD)</label>
                    <input type="number" step="0.01" id="deposito" name="deposito" value="0.00" oninput="calcularSaldo()" class="w-full px-3 py-2 text-lg font-bold rounded-lg border border-petroleo/20 focus:border-turquesa outline-none text-emerald-600 bg-emerald-50">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Saldo Pendiente (USD)</label>
                    <input type="text" id="saldo_pendiente" readonly value="$0.00" class="w-full px-3 py-2 text-lg font-bold rounded-lg border-none outline-none text-red-500 bg-red-50">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Tipo de Pago</label>
                    <select name="tipo_pago" id="tipo_pago" onchange="toggleCuotas()" class="w-full px-3 py-2 text-sm rounded-lg border border-petroleo/20 focus:border-turquesa outline-none">
                        <option value="contado">Contado</option>
                        <option value="cuotas" selected>En Cuotas (Plan)</option>
                    </select>
                </div>
            </div>

            <div id="seccion-cuotas" class="border-t border-petroleo/10 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-[10px] uppercase font-bold text-petroleo/70 tracking-wider">Plan de Cuotas</label>
                    <div class="flex gap-2">
                        <button type="button" onclick="distribuirCuotasAutom()" class="text-xs font-bold text-petroleo bg-humo px-3 py-1 rounded hover:bg-superficie transition-colors">
                            Dividir Automáticamente
                        </button>
                        <button type="button" onclick="addCuotaRow()" class="text-xs font-bold text-turquesa-dark border border-turquesa bg-turquesa/5 px-3 py-1 rounded hover:bg-turquesa hover:text-white transition-colors">
                            + Añadir Cuota Manual
                        </button>
                    </div>
                </div>
                <div id="lista-cuotas" class="space-y-2">
                    <!-- Cuota base insertada via JS -->
                </div>
                <div class="mt-3 flex justify-between text-xs">
                    <span class="text-petroleo/60">Suma de cuotas: <strong id="suma_cuotas" class="text-petroleo">0.00</strong></span>
                    <span id="alerta_cuotas" class="text-red-500 font-bold hidden">La suma de cuotas no coincide con el saldo.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón Fijo -->
    <div class="fixed bottom-0 left-0 lg:left-64 right-0 p-3 bg-white border-t border-petroleo/10 shadow-[0_-10px_30px_rgba(0,0,0,0.05)] flex justify-end gap-2 z-40">
        <a href="<?= Router::url('/admin/sales/' . $grupo['id']) ?>" class="px-4 py-2 text-sm rounded-lg font-bold text-petroleo bg-superficie hover:bg-humo transition-colors">Cancelar</a>
        <button type="submit" class="px-6 py-2 text-sm rounded-lg font-black text-white bg-turquesa hover:bg-turquesa-dark shadow-lg shadow-turquesa/30 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">save</span>
            Generar Contrato
        </button>
    </div>
</form>

<script>
function copiarTitularAPasajero() {
    const nombreFull = document.querySelector('input[name="titular_nombre"]').value;
    const doc = document.querySelector('input[name="titular_documento"]').value;
    
    if(!nombreFull) {
        alert("Primero ingresa el nombre del titular.");
        return;
    }
    
    let parts = nombreFull.split(' ');
    let nombre = parts[0];
    let apellido = parts.slice(1).join(' ');

    const row = document.querySelector('.pax-row'); // target first row usually
    if(row) {
        row.querySelector('.pax-nombre').value = nombre;
        row.querySelector('.pax-apellido').value = apellido;
        row.querySelector('.pax-pasaporte').value = doc;
        row.querySelector('.pax-tipo').value = 'adulto';
        row.classList.add('bg-turquesa/10');
        setTimeout(() => row.classList.remove('bg-turquesa/10'), 1000);
    }
}

function addPaxRow() {
    const container = document.getElementById('lista-pasajeros');
    const html = `
        <div class="pax-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-humo/30 p-3 rounded-lg border border-petroleo/10 anim-fade-in mt-2">
            <div class="md:col-span-3">
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">Nombre</label>
                <input type="text" class="pax-nombre w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none" required>
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
                <label class="block text-[10px] uppercase font-bold text-petroleo/70 mb-1 tracking-wider">DNI/Pasaporte</label>
                <input type="text" class="pax-pasaporte w-full px-2 py-1.5 rounded border border-petroleo/20 text-xs outline-none">
            </div>
            <div class="md:col-span-1 text-right">
                <button type="button" onclick="this.closest('.pax-row').remove()" class="w-full px-2 py-1.5 text-red-500 hover:bg-red-50 rounded flex justify-center"><span class="material-symbols-outlined text-[16px]">delete</span></button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function calcularSaldo() {
    let valor = parseFloat(document.getElementById('valor_total').value) || 0;
    let dep = parseFloat(document.getElementById('deposito').value) || 0;
    let saldo = valor - dep;
    document.getElementById('saldo_pendiente').value = '$' + saldo.toFixed(2);
    validarCuotas();
}

function toggleCuotas() {
    const isCuotas = document.getElementById('tipo_pago').value === 'cuotas';
    const cuotasSec = document.getElementById('seccion-cuotas');
    if (isCuotas) {
        cuotasSec.classList.remove('hidden');
    } else {
        cuotasSec.classList.add('hidden');
    }
}

function addCuotaRow(monto = '', fecha = '', label = '') {
    const container = document.getElementById('lista-cuotas');
    const idx = container.children.length + 1;
    const labelTxt = label || ('Cuota ' + idx);
    
    // Default fecha a un mes despues que el ultimo, o hoy + 1 mes
    if(!fecha) {
        let d = new Date();
        d.setMonth(d.getMonth() + idx);
        fecha = d.toISOString().split('T')[0];
    }

    const html = `
        <div class="cuota-row flex gap-3 items-end p-2 border border-petroleo/10 rounded bg-white">
            <div class="w-10 text-center font-bold text-petroleo/40 shrink-0 self-center">#<span class="cuota-idx">${idx}</span></div>
            <div class="flex-1">
                <label class="block text-[9px] uppercase font-bold text-petroleo/50">Monto USD</label>
                <input type="number" step="0.01" class="cuota-monto w-full px-2 py-1 border rounded text-sm text-petroleo outline-none focus:border-turquesa" value="${monto}" oninput="validarCuotas()" required>
            </div>
            <div class="flex-1">
                <label class="block text-[9px] uppercase font-bold text-petroleo/50">Vencimiento</label>
                <input type="date" class="cuota-fecha w-full px-2 py-1 border rounded text-sm text-petroleo outline-none focus:border-turquesa" value="${fecha}" required>
            </div>
            <button type="button" onclick="this.closest('.cuota-row').remove(); reindexarCuotas(); validarCuotas();" class="p-1 px-2 text-red-500 hover:bg-red-50 rounded h-[30px]">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function reindexarCuotas() {
    const rows = document.querySelectorAll('.cuota-row');
    rows.forEach((r, i) => {
        r.querySelector('.cuota-idx').innerText = i + 1;
    });
}

function distribuirCuotasAutom() {
    let saldoStr = document.getElementById('saldo_pendiente').value;
    let saldo = parseFloat(saldoStr.replace('$', '')) || 0;
    if (saldo <= 0) return alert('No hay saldo pendiente a distribuir.');
    
    let qty = prompt('¿En cuántas cuotas deseas dividir el saldo ($' + saldo.toFixed(2) + ')?', 3);
    qty = parseInt(qty);
    if (!qty || qty <= 0) return;
    
    document.getElementById('lista-cuotas').innerHTML = ''; // clear
    let baseMonto = Math.floor((saldo / qty) * 100) / 100;
    
    let sum = 0;
    for(let i=0; i<qty; i++) {
        let m = baseMonto;
        if(i === qty - 1) m = saldo - sum; // ultimo adjust
        sum += m;
        
        // Fecha 1 por mes
        let d = new Date();
        d.setMonth(d.getMonth() + i + 1);
        let f = d.toISOString().split('T')[0];
        
        addCuotaRow(m.toFixed(2), f);
    }
    validarCuotas();
}

function validarCuotas() {
    const montos = document.querySelectorAll('.cuota-monto');
    let sum = 0;
    montos.forEach(m => sum += (parseFloat(m.value) || 0));
    
    document.getElementById('suma_cuotas').innerText = '$' + sum.toFixed(2);
    
    let saldo = parseFloat(document.getElementById('saldo_pendiente').value.replace('$', '')) || 0;
    const alerta = document.getElementById('alerta_cuotas');
    
    // Tolerancia de 0.05
    if (Math.abs(sum - saldo) > 0.05 && montos.length > 0) {
        alerta.classList.remove('hidden');
    } else {
        alerta.classList.add('hidden');
    }
}

function prepararEnvioContrato(e) {
    if (document.getElementById('tipo_pago').value === 'cuotas') {
        const alerta = document.getElementById('alerta_cuotas');
        if(!alerta.classList.contains('hidden') && document.querySelectorAll('.cuota-row').length > 0) {
            alert('Atención: La suma total de las cuotas no coincide con el saldo de la deuda. Ajusta los montos.');
            return false;
        }

        let cuotas = [];
        document.querySelectorAll('.cuota-row').forEach(row => {
            cuotas.push({
                numero: parseInt(row.querySelector('.cuota-idx').innerText),
                monto: parseFloat(row.querySelector('.cuota-monto').value),
                fecha: row.querySelector('.cuota-fecha').value
            });
        });
        document.getElementById('cuotas_json').value = JSON.stringify(cuotas);
        
        // Asignar campo oculto via frontend si necesitamos guardar total cuotas para el DB
        const cp = document.createElement("input");
        cp.type = "hidden";
        cp.name = "total_cuotas";
        cp.value = cuotas.length;
        e.target.appendChild(cp);
    }

    let pasajeros = [];
    document.querySelectorAll('.pax-row').forEach(row => {
        let nom = row.querySelector('.pax-nombre').value;
        if(nom.trim()) {
            pasajeros.push({
                nombre: nom,
                apellido: row.querySelector('.pax-apellido').value,
                tipo: row.querySelector('.pax-tipo').value,
                edad: row.querySelector('.pax-edad').value,
                pasaporte: row.querySelector('.pax-pasaporte').value
            });
        }
    });
    document.getElementById('pasajeros_json').value = JSON.stringify(pasajeros);
    
    return true;
}

// initialization
document.addEventListener('DOMContentLoaded', () => {
    // Si queremos empezar con 1 row
    if(document.querySelectorAll('.cuota-row').length === 0) {
        // vacio para q usuario manual lo haga
    }
});
</script>
