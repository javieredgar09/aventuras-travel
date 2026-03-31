<!-- ADMIN – CREAR USUARIO -->
<?php
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
        <div class="bg-petroleo p-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-turquesa">person_add</span> Nuevo Usuario
            </h1>
            <p class="text-xs text-white/50 mt-1">Los datos marcados con * son obligatorios</p>
        </div>

        <form action="<?= Router::url('/admin/users/store') ?>" method="POST" class="p-6 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none" placeholder="Juan">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Apellido *</label>
                    <input type="text" name="apellido" required class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none" placeholder="Pérez">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Email</label>
                    <input type="email" name="email" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none" placeholder="correo@ejemplo.com">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Teléfono</label>
                    <input type="text" name="telefono" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none" placeholder="+51 999 999 999">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Rol *</label>
                    <select name="rol" required class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                        <?php foreach ($roles as $r): ?>
                        <option value="<?= $r ?>"><?= $rolLabels[$r] ?? $r ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Contraseña *</label>
                    <div class="relative">
                        <input type="text" name="password" id="pwd" required minlength="6" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-turquesa/30 outline-none pr-10" placeholder="Mínimo 6 caracteres">
                        <button type="button" onclick="generatePwd()" class="absolute right-2 top-1/2 -translate-y-1/2 text-turquesa hover:text-turquesa-dark" title="Generar contraseña">
                            <span class="material-symbols-outlined text-base">autorenew</span>
                        </button>
                    </div>
                    <p class="text-[10px] text-petroleo/40 mt-1">Haz clic en el ícono para generar una contraseña segura.</p>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700 flex items-start gap-2">
                <span class="material-symbols-outlined text-sm mt-0.5">info</span>
                <div>
                    <p class="font-bold">Nota:</p>
                    <p>El código de usuario (FILE) se generará automáticamente. Las credenciales se mostrarán una sola vez al guardar.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="<?= Router::url('/admin/users') ?>" class="px-6 py-2.5 rounded-xl text-sm font-bold text-petroleo bg-superficie hover:bg-humo transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-turquesa hover:bg-turquesa-dark transition-colors shadow-lg shadow-turquesa/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">save</span> Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function generatePwd() {
    const chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let pwd = '';
    const arr = new Uint32Array(12);
    crypto.getRandomValues(arr);
    arr.forEach(v => pwd += chars[v % chars.length]);
    document.getElementById('pwd').value = pwd;
}
</script>
