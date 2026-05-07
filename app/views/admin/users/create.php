<!-- ADMIN – CREAR USUARIO (CREDENCIALES AUTO-GENERADAS) -->
<?php
$csrf           = $csrf_token ?? '';
$roles          = $roles ?? [];
$grupos         = $grupos ?? [];
$gruposColegios = $gruposColegios ?? [];
$contratos      = $contratos ?? [];

$rolLabels = [
    'cliente_familiar' => 'Cliente Familiar',
    'cliente_colegio'  => 'Cliente Escolar (Colegio)',
    'representante'    => 'Representante de Grupo',
    'cliente_grupal'   => 'Cliente Grupal',
];
$rolIcons = [
    'cliente_familiar' => 'family_restroom',
    'cliente_colegio'  => 'school',
    'representante'    => 'shield_person',
    'cliente_grupal'   => 'groups',
];
$rolColors = [
    'cliente_familiar' => 'bg-blue-50 border-blue-200 text-blue-700',
    'cliente_colegio'  => 'bg-violet-50 border-violet-200 text-violet-700',
    'representante'    => 'bg-amber-50 border-amber-200 text-amber-700',
    'cliente_grupal'   => 'bg-cyan-50 border-cyan-200 text-cyan-700',
];
?>

<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <a href="<?= Router::url('/admin/users') ?>" class="text-turquesa font-semibold text-sm hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-lg">arrow_back</span> Usuarios
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="bg-petroleo p-5 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-turquesa/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-turquesa">person_add</span>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">Nuevo Usuario</h1>
                <p class="text-xs text-white/50 mt-0.5">El código y contraseña se generan automáticamente</p>
            </div>
        </div>

        <form action="<?= Router::url('/admin/users/store') ?>" method="POST" class="p-6 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <!-- Nombre y Apellido -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none" placeholder="Juan">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Apellido *</label>
                    <input type="text" name="apellido" required class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none" placeholder="Pérez">
                </div>
            </div>

            <!-- Email y Teléfono -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Email</label>
                    <input type="email" name="email" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none" placeholder="correo@ejemplo.com">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">
                        WhatsApp / Teléfono
                        <span class="text-emerald-500 normal-case font-normal ml-1">(para envío de credenciales)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </span>
                        <input type="text" name="telefono" class="w-full bg-superficie border-none rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none" placeholder="+51 999 999 999">
                    </div>
                </div>
            </div>

            <!-- Rol -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-2">Tipo de Usuario *</label>
                <div class="grid grid-cols-2 gap-2" id="rol-options">
                    <?php foreach ($roles as $r): ?>
                    <label class="rol-card flex items-center gap-3 p-3 rounded-xl border-2 border-transparent bg-superficie cursor-pointer hover:border-turquesa/30 transition-all <?= $rolColors[$r] ?? '' ?>"
                           data-rol="<?= $r ?>">
                        <input type="radio" name="rol" value="<?= $r ?>" class="hidden" <?= $r === 'cliente_familiar' ? 'checked' : '' ?>>
                        <span class="material-symbols-outlined text-xl"><?= $rolIcons[$r] ?? 'person' ?></span>
                        <span class="font-bold text-sm"><?= $rolLabels[$r] ?? $r ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sección Representante: solo grupo de colegio -->
            <div id="section-representante" class="hidden">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Grupo de Colegio *</label>
                <select name="grupo_id_rep" id="sel-grupo-rep" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                    <option value="">— Selecciona el colegio/grupo —</option>
                    <?php foreach ($gruposColegios as $g): ?>
                    <option value="<?= $g['id'] ?>">
                        <?= htmlspecialchars($g['nombre']) ?>
                        <?php if (!empty($g['institucion'])): ?> · <?= htmlspecialchars($g['institucion']) ?><?php endif; ?>
                        <?php if (!empty($g['destino'])): ?> → <?= htmlspecialchars($g['destino']) ?><?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($gruposColegios)): ?>
                <p class="text-xs text-amber-600 mt-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">warning</span>
                    No hay grupos de colegios registrados aún.
                </p>
                <?php endif; ?>
                <p class="text-[10px] text-petroleo/40 mt-1">El representante verá TODOS los contratos de este grupo.</p>
            </div>

            <!-- Sección otros roles: grupo + contrato -->
            <div id="section-otros" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Grupo / Venta</label>
                    <select name="grupo_id" id="sel-grupo" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                        <option value="">— Sin asignar —</option>
                        <?php foreach ($grupos as $g): ?>
                        <option value="<?= $g['id'] ?>" data-tipo="<?= htmlspecialchars($g['tipo']) ?>">
                            <?= htmlspecialchars($g['nombre']) ?> — <?= htmlspecialchars($g['destino'] ?? '') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Contrato (FILE)</label>
                    <select name="contrato_id" id="sel-contrato" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                        <option value="">— Selecciona grupo primero —</option>
                    </select>
                </div>
            </div>

            <!-- Aviso credenciales automáticas -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-xs text-emerald-700 flex items-start gap-2">
                <span class="material-symbols-outlined text-sm mt-0.5 shrink-0">auto_awesome</span>
                <div>
                    <p class="font-bold">Credenciales automáticas</p>
                    <p>El <strong>código de acceso</strong> y la <strong>contraseña</strong> se generarán automáticamente al crear el usuario. Se mostrarán una sola vez con opción de envío por WhatsApp.</p>
                </div>
            </div>

            <!-- Campos ocultos para sincronizar grupo según rol -->
            <input type="hidden" name="grupo_id_sync" id="grupo-id-sync" value="">

            <div class="flex justify-end gap-3 pt-2">
                <a href="<?= Router::url('/admin/users') ?>" class="px-6 py-2.5 rounded-xl text-sm font-bold text-petroleo bg-superficie hover:bg-humo transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-turquesa hover:bg-turquesa-dark transition-colors shadow-lg shadow-turquesa/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">person_add</span> Crear y Ver Credenciales
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const contratosData = <?= json_encode(array_map(function($c) {
    return [
        'id'            => (int)$c['id'],
        'codigo'        => $c['codigo'],
        'grupo_id'      => (int)$c['grupo_id'],
        'tipo'          => $c['tipo'],
        'destino'       => $c['destino'],
        'titular'       => $c['titular_nombre'],
        'tiene_cliente' => !empty($c['cliente_id']),
        'estado'        => $c['estado'],
    ];
}, $contratos), JSON_HEX_TAG | JSON_HEX_AMP) ?>;

const rolCards      = document.querySelectorAll('.rol-card');
const secRep        = document.getElementById('section-representante');
const secOtros      = document.getElementById('section-otros');
const selGrupo      = document.getElementById('sel-grupo');
const selContrato   = document.getElementById('sel-contrato');
const selGrupoRep   = document.getElementById('sel-grupo-rep');
const selGrupoSync  = document.getElementById('grupo-id-sync');

// Selección visual de rol
rolCards.forEach(card => {
    card.addEventListener('click', () => {
        rolCards.forEach(c => c.classList.remove('ring-2', 'ring-turquesa', '!bg-turquesa/5'));
        card.classList.add('ring-2', 'ring-turquesa', '!bg-turquesa/5');
        card.querySelector('input[type=radio]').checked = true;
        updateRolUI();
    });
});

// Inicializar primer rol seleccionado
const checkedCard = document.querySelector('.rol-card input[type=radio]:checked');
if (checkedCard) {
    checkedCard.closest('.rol-card').classList.add('ring-2', 'ring-turquesa', '!bg-turquesa/5');
}

function getSelectedRol() {
    const checked = document.querySelector('.rol-card input[type=radio]:checked');
    return checked ? checked.value : 'cliente_familiar';
}

function updateRolUI() {
    const rol = getSelectedRol();
    const isRep = rol === 'representante';

    secRep.classList.toggle('hidden', !isRep);
    secOtros.classList.toggle('hidden', isRep);

    if (!isRep) {
        const esFamiliar = rol === 'cliente_familiar';
        Array.from(selGrupo.options).forEach(opt => {
            if (!opt.value) return;
            opt.hidden = esFamiliar ? opt.dataset.tipo === 'colegio' : opt.dataset.tipo === 'familiar';
        });
        const sel = selGrupo.options[selGrupo.selectedIndex];
        if (sel && sel.value) {
            const tipoActual = sel.dataset.tipo;
            const deberia = esFamiliar ? 'familiar' : 'colegio';
            if (tipoActual !== deberia) selGrupo.value = '';
        }
        updateContratos();
    }

    // Sincronizar nombre del campo
    syncGrupoId();
}

function updateContratos() {
    const grupoId = parseInt(selGrupo.value) || 0;
    selContrato.innerHTML = '';
    if (!grupoId) {
        selContrato.innerHTML = '<option value="">— Selecciona grupo primero —</option>';
        return;
    }
    const filtered = contratosData.filter(c => c.grupo_id === grupoId);
    if (!filtered.length) {
        selContrato.innerHTML = '<option value="">— Sin contratos disponibles —</option>';
        return;
    }
    selContrato.innerHTML = '<option value="">— Seleccionar contrato —</option>';
    filtered.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        let label = c.codigo + ' — ' + (c.destino || '');
        if (c.titular) label += ' (' + c.titular + ')';
        if (c.tiene_cliente) label += ' [ya asignado]';
        opt.textContent = label;
        selContrato.appendChild(opt);
    });
    if (filtered.length === 1) selContrato.value = filtered[0].id;
}

function syncGrupoId() {
    const rol = getSelectedRol();
    if (rol === 'representante') {
        selGrupoSync.name = ''; // se usará grupo_id_rep
    }
}

selGrupo.addEventListener('change', () => { updateContratos(); });
if (selGrupoRep) {
    selGrupoRep.addEventListener('change', () => {
        // El name=grupo_id_rep ya se usa directamente en el form
    });
}

// Init
updateRolUI();
</script>
