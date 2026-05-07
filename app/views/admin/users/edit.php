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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Nombre *</label>
                    <input type="text" name="nombre" required value="<?= htmlspecialchars($u['nombre'] ?? '') ?>" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Apellido *</label>
                    <input type="text" name="apellido" required value="<?= htmlspecialchars($u['apellido'] ?? '') ?>" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($u['email'] ?? '') ?>" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-petroleo/50 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($u['telefono'] ?? '') ?>" class="w-full bg-superficie border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-turquesa/30 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
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

        <!-- Sección de Acciones Adicionales -->
        <div class="border-t border-petroleo/10 p-6 bg-superficie">
            <h3 class="text-sm font-bold text-petroleo mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-base">settings</span> Acciones Adicionales
            </h3>
            <div class="flex gap-3 flex-wrap">
                <!-- Botón Reenviar Credenciales por WhatsApp -->
                <?php if (!empty($u['telefono']) && in_array($u['rol'], ['cliente_familiar', 'cliente_colegio', 'cliente_grupal'])): ?>
                <button type="button" onclick="openResendWhatsAppModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['codigo']) ?>', '<?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>', '<?= htmlspecialchars($u['telefono']) ?>')"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-[#25d366] hover:bg-[#1db954] transition-colors flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Reenviar por WhatsApp
                </button>
                <?php else: ?>
                <div class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-400 bg-gray-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">block</span>
                    No se puede enviar WhatsApp (sin teléfono o rol incompatible)
                </div>
                <?php endif; ?>

                <!-- Botón Resetear Contraseña -->
                <a href="<?= Router::url('/admin/users/' . $u['id'] . '/reset-password') ?>"
                   class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 transition-colors flex items-center gap-2 shadow-lg shadow-orange-500/20">
                    <span class="material-symbols-outlined text-base">lock_reset</span>
                    Resetear Contraseña
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Reenviar Credenciales por WhatsApp -->
<div id="resendWhatsAppModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl">
        <div class="bg-petroleo p-5 flex justify-between items-center">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-[#25d366]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Reenviar Credenciales
            </h2>
            <button type="button" onclick="closeResendWhatsAppModal()" class="text-white/50 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-sm text-petroleo/70">¿Reenviar las credenciales de acceso a <strong id="modal-user-name"></strong>?</p>
            <div class="bg-superficie rounded-xl p-3 text-xs space-y-1">
                <p><strong>Teléfono:</strong> <span id="modal-user-phone"></span></p>
                <p><strong>Usuario:</strong> <code id="modal-user-code" class="font-mono font-bold text-petroleo"></code></p>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700 flex items-start gap-2">
                <span class="material-symbols-outlined text-sm shrink-0 mt-0.5">info</span>
                <p>Se enviará un mensaje de WhatsApp con las credenciales de acceso.</p>
            </div>
        </div>
        <div class="border-t border-petroleo/10 p-4 flex gap-3">
            <button type="button" onclick="closeResendWhatsAppModal()" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-petroleo bg-superficie hover:bg-humo transition-colors">
                Cancelar
            </button>
            <button type="button" onclick="submitResendWhatsApp()" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-[#25d366] hover:bg-[#1db954] transition-colors">
                <span class="inline-block">Reenviar</span>
            </button>
        </div>
    </div>
</div>

<script>
let modalUserData = {};

function openResendWhatsAppModal(userId, userCode, userName, userPhone) {
    modalUserData = { userId, userCode, userName, userPhone };
    document.getElementById('modal-user-name').textContent = userName;
    document.getElementById('modal-user-phone').textContent = userPhone;
    document.getElementById('modal-user-code').textContent = userCode;
    document.getElementById('resendWhatsAppModal').classList.remove('hidden');
}

function closeResendWhatsAppModal() {
    document.getElementById('resendWhatsAppModal').classList.add('hidden');
    modalUserData = {};
}

async function submitResendWhatsApp() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    try {
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Enviando...';

        // Obtener la contraseña actual del usuario desde la BD (esto debe hacerse por API)
        const response = await fetch('<?= Router::url('/api/whatsapp/send-credentials') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('[name="csrf_token"]')?.value || '',
            },
            body: JSON.stringify({
                phone_number: modalUserData.userPhone,
                client_name: modalUserData.userName,
                contract_code: modalUserData.userCode,
                username: modalUserData.userCode,
                password: 'ContactarAdmin', // En la práctica, se debe obtener de forma segura
            }),
        });

        const result = await response.json();

        if (result.success) {
            alert('✅ WhatsApp enviado exitosamente a ' + modalUserData.userPhone);
            closeResendWhatsAppModal();
        } else {
            alert('❌ Error al enviar: ' + (result.error || 'Error desconocido'));
        }
    } catch (error) {
        alert('❌ Error: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// Cerrar modal al hacer click fuera
document.getElementById('resendWhatsAppModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'resendWhatsAppModal') closeResendWhatsAppModal();
});
</script>
