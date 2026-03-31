<!-- ADMIN – EDITAR USUARIO -->
<?php
$u     = $usuario ?? [];
$csrf  = $csrf_token ?? '';
$roles = $roles ?? [];

$rolLabels = [
    'cliente_familiar' => 'Cliente Familiar',
    'cliente_colegio'  => 'Cliente Escolar (Colegio)',
    'representante'    => 'Representante de Grupo',
    'cliente_grupal'   => 'Cliente Grupal',
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
        <div class="bg-petroleo p-5 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-turquesa">edit</span> Editar Usuario
                </h1>
                <p class="text-xs text-white/50 mt-1">Código: <?= htmlspecialchars($u['codigo'] ?? '') ?></p>
            </div>
            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?= ($u['activo'] ?? 1) ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' ?>">
                <?= ($u['activo'] ?? 1) ? 'Activo' : 'Inactivo' ?>
            </span>
        </div>

        <form action="<?= Router::url('/admin/users/' . ($u['id'] ?? 0) . '/update') ?>" method="POST" class="p-6 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required value="<?= htmlspecialchars($u['nombre'] ?? '') ?>" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Apellido *</label>
                    <input type="text" name="apellido" required value="<?= htmlspecialchars($u['apellido'] ?? '') ?>" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($u['email'] ?? '') ?>" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($u['telefono'] ?? '') ?>" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Rol *</label>
                    <select name="rol" required class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                        <?php foreach ($roles as $r): ?>
                        <option value="<?= $r ?>" <?= ($u['rol'] ?? '') === $r ? 'selected' : '' ?>><?= $rolLabels[$r] ?? $r ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Estado</label>
                    <select name="activo" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                        <option value="1" <?= ($u['activo'] ?? 1) == 1 ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= ($u['activo'] ?? 1) == 0 ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Nueva Contraseña</label>
                    <input type="text" name="password" minlength="6" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-turquesa/30 outline-none" placeholder="Dejar vacío para no cambiar">
                </div>
            </div>

            <!-- Info adicional -->
            <div class="bg-superficie rounded-xl p-4 text-xs text-petroleo/50 space-y-1">
                <p><strong>Código:</strong> <?= htmlspecialchars($u['codigo'] ?? '') ?></p>
                <p><strong>Creado:</strong> <?= isset($u['created_at']) ? date('d M Y H:i', strtotime($u['created_at'])) : '—' ?></p>
                <p><strong>Último acceso:</strong> <?= !empty($u['ultimo_acceso']) ? date('d M Y H:i', strtotime($u['ultimo_acceso'])) : 'Nunca' ?></p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="<?= Router::url('/admin/users') ?>" class="px-6 py-2.5 rounded-xl text-sm font-bold text-petroleo bg-superficie hover:bg-humo transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-turquesa hover:bg-turquesa-dark transition-colors shadow-lg shadow-turquesa/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">save</span> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
