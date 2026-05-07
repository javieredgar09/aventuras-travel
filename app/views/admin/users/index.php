<!-- ADMIN – GESTIÓN DE USUARIOS -->
<?php
$usuarios  = $usuarios ?? [];
$stats     = $stats ?? [];
$csrf      = $csrf_token ?? '';
$filtroRol = $filtro_rol ?? '';
$buscar    = $buscar ?? '';
$flash     = $flash ?? null;

$rolLabels = [
    'cliente_familiar' => ['Familiar',       'bg-blue-100 text-blue-700',    'family_restroom'],
    'cliente_colegio'  => ['Escolar',        'bg-violet-100 text-violet-700','school'],
    'representante'    => ['Representante',  'bg-amber-100 text-amber-700',  'shield_person'],
    'cliente_grupal'   => ['Grupal',         'bg-cyan-100 text-cyan-700',    'groups'],
];
?>

<!-- Flash -->
<?php if ($flash): ?>
<div class="mb-6 p-4 rounded-xl text-sm font-medium flex items-center gap-2 <?= $flash['type'] === 'exito' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
    <span class="material-symbols-outlined text-base"><?= $flash['type'] === 'exito' ? 'check_circle' : 'error' ?></span>
    <?= $flash['message'] ?>
</div>
<?php endif; ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-black text-petroleo tracking-tight">Gestión de Usuarios</h1>
        <p class="text-sm text-petroleo/50">Administra cuentas de clientes familiares y escolares</p>
    </div>
    <a href="<?= Router::url('/admin/users/create') ?>" class="bg-turquesa text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-turquesa-dark transition-colors shadow-lg shadow-turquesa/20">
        <span class="material-symbols-outlined text-base">person_add</span> Nuevo Usuario
    </a>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
    <div class="bg-white rounded-xl p-4 border border-petroleo/5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/30">Total</p>
        <p class="text-2xl font-black text-petroleo"><?= $stats['total'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-petroleo/5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-500">Activos</p>
        <p class="text-2xl font-black text-emerald-600"><?= $stats['activos'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-petroleo/5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-red-400">Inactivos</p>
        <p class="text-2xl font-black text-red-500"><?= $stats['inactivos'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-petroleo/5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-blue-500">Familiares</p>
        <p class="text-2xl font-black text-blue-600"><?= $stats['familiares'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-petroleo/5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-violet-500">Escolares</p>
        <p class="text-2xl font-black text-violet-600"><?= $stats['escolares'] ?? 0 ?></p>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm mb-6 p-4">
    <form method="GET" action="<?= Router::url('/admin/users') ?>" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/40 mb-1">Buscar</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-petroleo/30 text-base">search</span>
                <input type="text" name="q" value="<?= htmlspecialchars($buscar) ?>" placeholder="Nombre, código, email..." class="w-full pl-10 pr-4 py-2.5 border border-petroleo/10 rounded-xl text-sm focus:border-turquesa focus:ring-2 focus:ring-turquesa/20 outline-none">
            </div>
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/40 mb-1">Rol</label>
            <select name="rol" class="px-4 py-2.5 border border-petroleo/10 rounded-xl text-sm bg-white focus:border-turquesa outline-none">
                <option value="">Todos</option>
                <option value="cliente_familiar" <?= $filtroRol === 'cliente_familiar' ? 'selected' : '' ?>>Familiar</option>
                <option value="cliente_colegio" <?= $filtroRol === 'cliente_colegio' ? 'selected' : '' ?>>Escolar</option>
                <option value="representante" <?= $filtroRol === 'representante' ? 'selected' : '' ?>>Representante</option>
                <option value="cliente_grupal" <?= $filtroRol === 'cliente_grupal' ? 'selected' : '' ?>>Grupal</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2.5 bg-petroleo text-white rounded-xl text-sm font-bold hover:bg-petroleo/80 transition-colors">Filtrar</button>
        <?php if ($filtroRol || $buscar): ?>
            <a href="<?= Router::url('/admin/users') ?>" class="px-4 py-2.5 bg-superficie text-petroleo rounded-xl text-sm font-bold hover:bg-humo transition-colors">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabla de Usuarios -->
<div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm overflow-hidden">
    <?php if (!empty($usuarios)): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-petroleo text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-widest">Usuario</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-widest">Código</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-widest">Contacto</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest">Rol</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest">Estado</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest">Último Acceso</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest">WhatsApp</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-petroleo/5">
                <?php foreach ($usuarios as $u):
                    $rl = $rolLabels[$u['rol']] ?? ['Desconocido', 'bg-gray-100 text-gray-600', 'help'];
                ?>
                <tr class="hover:bg-humo/30 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold <?= $u['activo'] ? 'bg-turquesa/10 text-turquesa-dark' : 'bg-red-100 text-red-500' ?>">
                                <?= strtoupper(substr($u['nombre'] ?? '', 0, 1) . substr($u['apellido'] ?? '', 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-bold text-petroleo"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></p>
                                <p class="text-[10px] text-petroleo/40">ID: <?= $u['id'] ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-mono font-bold text-petroleo bg-superficie px-2 py-1 rounded text-xs"><?= htmlspecialchars($u['codigo']) ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-petroleo text-xs"><?= htmlspecialchars($u['email'] ?? '—') ?></p>
                        <p class="text-petroleo/40 text-[10px]"><?= htmlspecialchars($u['telefono'] ?? '') ?></p>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold <?= $rl[1] ?>">
                            <span class="material-symbols-outlined text-xs"><?= $rl[2] ?></span> <?= $rl[0] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($u['activo']): ?>
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1"></span><span class="text-emerald-600 text-xs font-bold">Activo</span>
                        <?php else: ?>
                            <span class="inline-block w-2 h-2 rounded-full bg-red-400 mr-1"></span><span class="text-red-500 text-xs font-bold">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-petroleo/40">
                        <?= $u['ultimo_acceso'] ? date('d M Y H:i', strtotime($u['ultimo_acceso'])) : 'Nunca' ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php
                        $telWa = preg_replace('/[^0-9]/', '', $u['telefono'] ?? '');
                        ?>
                        <?php if ($telWa): ?>
                        <div class="flex items-center justify-center gap-1">
                            <!-- Enviar credenciales via API de Meta (genera nueva contraseña) -->
                            <form action="<?= Router::url('/admin/users/' . $u['id'] . '/send-whatsapp') ?>" method="POST" data-confirm="¿Enviar credenciales por WhatsApp a <?= htmlspecialchars($u['telefono']) ?>? Se generará una nueva contraseña.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                <button type="submit" class="w-8 h-8 rounded-lg bg-[#25d366]/10 text-[#25d366] flex items-center justify-center hover:bg-[#25d366]/20 transition-colors" title="Enviar credenciales por WhatsApp API">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </button>
                            </form>
                        </div>
                        <?php else: ?>
                        <span class="inline-flex w-8 h-8 rounded-lg bg-gray-50 text-gray-300 items-center justify-center" title="Sin teléfono registrado">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="<?= Router::url('/admin/users/' . $u['id'] . '/edit') ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors" title="Editar">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
                            <form action="<?= Router::url('/admin/users/' . $u['id'] . '/reset-password') ?>" method="POST" data-confirm="¿Generar nueva contraseña para <?= htmlspecialchars($u['codigo']) ?>?">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                <button type="submit" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-100 transition-colors" title="Reset Password">
                                    <span class="material-symbols-outlined text-base">lock_reset</span>
                                </button>
                            </form>
                            <form action="<?= Router::url('/admin/users/' . $u['id'] . '/toggle') ?>" method="POST" data-confirm="¿<?= $u['activo'] ? 'Desactivar' : 'Activar' ?> usuario <?= htmlspecialchars($u['codigo']) ?>?">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                <button type="submit" class="w-8 h-8 rounded-lg <?= $u['activo'] ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' ?> flex items-center justify-center transition-colors" title="<?= $u['activo'] ? 'Desactivar' : 'Activar' ?>">
                                    <span class="material-symbols-outlined text-base"><?= $u['activo'] ? 'person_off' : 'person' ?></span>
                                </button>
                            </form>
                            <form action="<?= Router::url('/admin/users/' . $u['id'] . '/delete') ?>" method="POST" data-confirm="¿Eliminar usuario <?= htmlspecialchars($u['codigo']) ?>? Si tiene datos asociados, será desactivado.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors" title="Eliminar">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="p-12 text-center text-petroleo/30">
        <span class="material-symbols-outlined text-5xl mb-3">person_off</span>
        <p class="text-sm">No se encontraron usuarios<?= ($filtroRol || $buscar) ? ' con los filtros aplicados' : '' ?>.</p>
    </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('[data-confirm]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
});
</script>
