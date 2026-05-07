<!-- LOGIN UNIFICADO – admin y clientes en uno solo -->
<div class="w-full max-w-md px-4 sm:px-0">
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl sm:rounded-3xl shadow-2xl p-6 sm:p-8 md:p-10 animate-fadeInUp">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-petroleo rounded-2xl flex items-center justify-center shadow-lg">
                <img src="<?= Router::url('/img/sin_fondo.png') ?>" alt="Logo" class="h-10 brightness-0 invert">
            </div>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-center text-petroleo mb-1">Aventuras Travel</h1>
        <p class="text-center text-petroleo/50 text-sm mb-6 sm:mb-8">Gestiona tu próxima gran aventura</p>

        <?php if (isset($flash) && $flash): ?>
        <div class="p-3 rounded-xl text-sm font-medium mb-6 <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= Router::url('/login') ?>">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <!-- Código / Usuario -->
            <div class="mb-5">
                <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-2">Número de FILE o Usuario</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-petroleo/30">badge</span>
                    <input type="text" name="codigo" required placeholder="Ej: CCPA-2026-001 o admin"
                        class="w-full pl-12 pr-4 py-4 bg-superficie border border-petroleo/10 rounded-xl text-petroleo font-medium focus:ring-2 focus:ring-turquesa/30 focus:border-turquesa transition-all"
                        value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>">
                </div>
            </div>

            <!-- Contraseña -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-turquesa-dark">Contraseña</label>
                    <a href="#" class="text-xs text-turquesa-dark font-semibold hover:underline">¿Olvidaste tu clave?</a>
                </div>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-petroleo/30">lock</span>
                    <input type="password" name="password" required placeholder="••••••••" id="loginPassword"
                        class="w-full pl-12 pr-12 py-4 bg-superficie border border-petroleo/10 rounded-xl text-petroleo font-medium focus:ring-2 focus:ring-turquesa/30 focus:border-turquesa transition-all">
                    <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-petroleo/30 hover:text-petroleo transition-all">
                        <span class="material-symbols-outlined" id="eyeIcon">visibility_off</span>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full py-4 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-lg">
                Ingresar a mi Viaje
                <span class="material-symbols-outlined">arrow_forward</span>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-petroleo/10">
            <p class="text-center text-xs text-petroleo/40 leading-relaxed">
                ¿No tienes cuenta? Contacta a tu asesor de viajes o visita nuestra oficina central para activar tu acceso.
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('loginPassword');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility_off';
    }
}
</script>
