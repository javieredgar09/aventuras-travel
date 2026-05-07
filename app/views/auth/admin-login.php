<?php /* Admin Login — Aventuras Travel — Design System Unificado */ ?>

<div class="min-h-screen bg-gradient-to-br from-petroleo via-petroleo-light to-[#0D2535] flex items-center justify-center px-4 py-10 relative overflow-hidden">

    <!-- Decorative blobs -->
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-turquesa/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-turquesa/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">

        <!-- Logo card -->
        <div class="text-center mb-8 animate-fadeInUp">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/15 shadow-2xl mb-5">
                <img src="<?= Router::url('/img/sin_fondo.png') ?>" alt="Aventuras Travel" class="h-12 brightness-0 invert">
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Portal Administrativo</h1>
            <p class="text-white/50 text-sm mt-1">Aventuras Travel Pucallpa</p>
        </div>

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl p-8 animate-fadeInUp" style="animation-delay:.1s">

            <?php if (!empty($flash)): ?>
            <div class="mb-6 p-4 rounded-xl text-sm font-semibold flex items-center gap-3 
                <?= $flash['type'] === 'error' ? 'bg-red-500/20 text-red-200 border border-red-500/30' : 'bg-emerald-500/20 text-emerald-200 border border-emerald-500/30' ?>">
                <span class="material-symbols-outlined text-lg">
                    <?= $flash['type'] === 'error' ? 'error' : 'check_circle' ?>
                </span>
                <?= htmlspecialchars($flash['message']) ?>
            </div>
            <?php endif; ?>

            <form action="<?= Router::url('/admin/login') ?>" method="POST" id="adminLoginForm">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- Usuario -->
                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-widest text-turquesa mb-2">Usuario</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-xl">person</span>
                        <input type="text" name="codigo" required autofocus
                            placeholder="nombre.usuario"
                            class="w-full pl-12 pr-4 py-4 bg-white/10 border border-white/15 rounded-xl text-white font-medium placeholder:text-white/30 focus:ring-2 focus:ring-turquesa/40 focus:border-turquesa/50 focus:outline-none transition-all"
                            value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>">
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="mb-6">
                    <label class="block text-xs font-bold uppercase tracking-widest text-turquesa mb-2">Contraseña</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-xl">lock</span>
                        <input type="password" name="password" required id="adminPwd"
                            placeholder="••••••••••"
                            class="w-full pl-12 pr-12 py-4 bg-white/10 border border-white/15 rounded-xl text-white font-medium placeholder:text-white/30 focus:ring-2 focus:ring-turquesa/40 focus:border-turquesa/50 focus:outline-none transition-all">
                        <button type="button" onclick="toggleAdminPwd()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 hover:text-white/70 transition-colors">
                            <span class="material-symbols-outlined text-xl" id="adminEyeIcon">visibility_off</span>
                        </button>
                    </div>
                </div>

                <!-- Recordarme -->
                <div class="flex items-center gap-3 mb-6">
                    <input type="checkbox" name="remember" id="remember"
                        class="w-4 h-4 rounded border-white/30 bg-white/10 accent-turquesa">
                    <label for="remember" class="text-sm text-white/60 cursor-pointer">Mantener sesión iniciada</label>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-4 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl shadow-lg hover:shadow-turquesa/30 hover:shadow-xl transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-base">
                    <span class="material-symbols-outlined">admin_panel_settings</span>
                    Acceder al Sistema
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-8 pt-6 border-t border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white/30 text-xs">
                    <span class="material-symbols-outlined text-sm">shield</span>
                    Conexión segura AES-256
                </div>
                <a href="<?= Router::url('/login') ?>" class="text-turquesa/70 hover:text-turquesa text-xs font-medium transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Portal Clientes
                </a>
            </div>
        </div>

        <!-- Footer note -->
        <p class="text-center text-white/25 text-xs mt-6">
            Aventuras Travel Pucallpa &copy; <?= date('Y') ?> · Sistema de Gestión v2.5
        </p>
    </div>
</div>

<script>
function toggleAdminPwd() {
    const i = document.getElementById('adminPwd');
    const e = document.getElementById('adminEyeIcon');
    i.type = i.type === 'password' ? 'text' : 'password';
    e.textContent = i.type === 'password' ? 'visibility_off' : 'visibility';
}
</script>
