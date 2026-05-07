<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Aventuras Travel Pucallpa') ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= Router::url('/assets/css/styles.css') ?>">
    <link rel="icon" href="<?= Router::url('/img/sin_fondo.png') ?>" type="image/png">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    /* Primarios */
                    'petroleo':         '#1B3A4B',
                    'petroleo-dark':    '#0D2432',
                    'petroleo-mid':     '#24516B',
                    'petroleo-light':   '#2D5468',
                    'petroleo-lighter': '#3E6E88',
                    /* Turquesa */
                    'turquesa':         '#4ABED9',
                    'turquesa-dark':    '#00687A',
                    'turquesa-darker':  '#004D5E',
                    'turquesa-light':   '#7DD3E8',
                    /* Acentos */
                    'coral':            '#FF6B6B',
                    'coral-dark':       '#E84040',
                    'gold':             '#F4A633',
                    'gold-dark':        '#D4860F',
                    /* Neutros */
                    'humo':             '#F8FBFD',
                    'superficie':       '#EDF4F7',
                    'surface-alt':      '#D9ECF2',
                },
                fontFamily: {
                    'sans': ['Inter', 'system-ui', 'sans-serif'],
                },
                boxShadow: {
                    'turquesa': '0 8px 32px rgba(74,190,217,0.3)',
                    'petroleo': '0 8px 32px rgba(13,36,50,0.25)',
                },
                keyframes: {
                    fadeInUp: {
                        '0%': { opacity: '0', transform: 'translateY(1rem)' },
                        '100%': { opacity: '1', transform: 'translateY(0)' }
                    },
                    float: {
                        '0%,100%': { transform: 'translateY(0)' },
                        '50%': { transform: 'translateY(-8px)' }
                    }
                },
                animation: {
                    fadeInUp: 'fadeInUp 0.5s ease forwards',
                    float:    'float 3.5s ease-in-out infinite',
                }
            },
        },
    }
    </script>
</head>
<body class="bg-humo text-petroleo font-sans selection:bg-turquesa/30">

<?php
// Detectar página activa del menú
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$isHome = ($currentUri === '/aventuras/' || $currentUri === '/aventuras' || $currentUri === '/aventuras/public/' || $currentUri === '/aventuras/public');
$isDestinos = (strpos($currentUri, '/search') !== false);
$isPromociones = (strpos($currentUri, '/promotions') !== false);
$isAsesoria = (strpos($currentUri, '/asesoria') !== false);

?>

<!-- NAVBAR -->
<nav class="fixed top-0 w-full z-50 bg-gradient-to-r from-white/80 to-white/90 backdrop-blur-2xl shadow-lg shadow-petroleo/5 border-b border-turquesa/10">
    <div class="flex justify-between items-center px-6 py-3 max-w-[1600px] mx-auto">
        <div class="flex items-center gap-8">
            <a href="<?= Router::url('/') ?>" class="flex items-center gap-2">
                <img src="<?= Router::url('/img/sin_fondo.png') ?>" alt="Aventuras Travel" class="h-10 w-10 object-contain">
                <span class="text-xl font-bold text-petroleo tracking-tight hidden sm:inline">Aventuras Travel <span class="text-turquesa-dark font-medium">Pucallpa</span></span>
            </a>
            <div class="hidden md:flex items-center gap-1 border-l border-petroleo/10 pl-8">
                <a href="<?= Router::url('/') ?>" class="px-4 py-2 font-medium transition-colors tracking-tight <?= $isHome ? 'text-turquesa-dark font-bold border-b-2 border-turquesa' : 'text-petroleo/70 hover:text-turquesa-dark' ?>">Inicio</a>
                <a href="<?= Router::url('/search') ?>" class="px-4 py-2 font-medium transition-colors tracking-tight <?= $isDestinos ? 'text-turquesa-dark font-bold border-b-2 border-turquesa' : 'text-petroleo/70 hover:text-turquesa-dark' ?>">Destinos</a>
                <a href="<?= Router::url('/promotions') ?>" class="relative inline-flex items-center gap-1.5 px-4 py-2 rounded-xl font-bold text-sm tracking-tight transition-all duration-300 group
                    <?= $isPromociones 
                        ? 'bg-gradient-to-r from-[#FF6B35] to-[#F4A633] text-white shadow-lg shadow-orange-400/30' 
                        : 'bg-gradient-to-r from-[#FF6B35] to-[#F4A633] text-white hover:shadow-lg hover:shadow-orange-400/30 hover:-translate-y-0.5' ?>"
                >
                    <span class="material-symbols-outlined text-[16px] text-white" style="font-variation-settings:'FILL' 1">local_fire_department</span>
                    Promociones
                    <!-- HOT badge parpadeante -->
                    <span class="absolute -top-2 -right-2 bg-[#FF4444] text-white text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded-full leading-none shadow-md animate-pulse border border-white/30">HOT</span>
                    <!-- Glow highlight on hover -->
                    <span class="absolute inset-0 rounded-xl bg-white/0 group-hover:bg-white/10 transition-all duration-300"></span>
                </a>
                <a href="<?= Router::url('/asesoria') ?>" class="px-4 py-2 font-medium transition-colors tracking-tight <?= $isAsesoria ? 'text-turquesa-dark font-bold border-b-2 border-turquesa' : 'text-petroleo/70 hover:text-turquesa-dark' ?>">Asesorías</a>
            </div>
        </div>
        <div class="flex items-center gap-2 md:gap-4">
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-petroleo/5" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')">
                <span class="material-symbols-outlined text-2xl text-petroleo">menu</span>
            </button>
            <?php if (!empty($_SESSION['user'])): ?>
                <?php
                $loggedName = htmlspecialchars(trim(($_SESSION['user']['nombre'] ?? '') . ' ' . ($_SESSION['user']['apellido'] ?? '')));
                $loggedInitials = strtoupper(substr($_SESSION['user']['nombre'] ?? 'U', 0, 1) . substr($_SESSION['user']['apellido'] ?? '', 0, 1));
                $loggedRol = $_SESSION['user']['rol'] ?? '';
                $loggedCode = htmlspecialchars($_SESSION['user']['codigo'] ?? '');
                $dashUrl = ($loggedRol === 'admin') ? '/admin/dashboard' : (($loggedRol === 'representante') ? '/leader/dashboard' : '/client/dashboard');
                ?>
                <button class="p-2 hover:bg-turquesa/5 rounded-lg transition-all relative">
                    <span class="material-symbols-outlined text-petroleo">notifications</span>
                </button>
                <div class="relative" id="user-menu-wrapper">
                    <button onclick="document.getElementById('user-dropdown').classList.toggle('hidden')" class="flex items-center gap-2 px-3 py-1.5 hover:bg-turquesa/5 rounded-full transition-colors">
                        <div class="w-9 h-9 rounded-full bg-turquesa/15 flex items-center justify-center text-turquesa-dark font-black text-sm">
                            <?= $loggedInitials ?>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-bold text-petroleo leading-none"><?= $loggedCode ?></p>
                            <p class="text-[10px] text-slate-500 leading-none mt-0.5"><?= $loggedRol === 'admin' ? 'Administrador' : ($loggedRol === 'representante' ? 'Representante' : 'Cliente') ?></p>
                        </div>
                        <span class="material-symbols-outlined text-sm text-slate-400 hidden sm:inline">keyboard_arrow_down</span>
                    </button>
                    <div id="user-dropdown" class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 p-2 z-50">
                        <div class="px-3 py-2 border-b border-slate-100 mb-1">
                            <p class="text-sm font-bold text-petroleo"><?= $loggedName ?></p>
                            <p class="text-xs text-slate-400"><?= $loggedCode ?></p>
                        </div>
                        <?php if ($loggedRol === 'admin'): ?>
                            <a href="<?= Router::url('/admin/dashboard') ?>" class="flex items-center gap-3 px-3 py-2 hover:bg-turquesa/5 rounded-lg transition-colors text-sm text-slate-700">
                                <span class="material-symbols-outlined text-lg text-turquesa-dark">admin_panel_settings</span> Panel Admin
                            </a>
                        <?php elseif ($loggedRol === 'representante'): ?>
                            <a href="<?= Router::url('/leader/dashboard') ?>" class="flex items-center gap-3 px-3 py-2 hover:bg-turquesa/5 rounded-lg transition-colors text-sm text-slate-700">
                                <span class="material-symbols-outlined text-lg text-turquesa-dark">school</span> Mi Panel
                            </a>
                            <a href="<?= Router::url('/leader/payments') ?>" class="flex items-center gap-3 px-3 py-2 hover:bg-turquesa/5 rounded-lg transition-colors text-sm text-slate-700">
                                <span class="material-symbols-outlined text-lg text-turquesa-dark">payments</span> Pagos
                            </a>
                        <?php else: ?>
                            <a href="<?= Router::url('/client/dashboard') ?>" class="flex items-center gap-3 px-3 py-2 hover:bg-turquesa/5 rounded-lg transition-colors text-sm text-slate-700">
                                <span class="material-symbols-outlined text-lg text-turquesa-dark">travel_explore</span> Mis Viajes
                            </a>
                            <a href="<?= Router::url('/client/payments') ?>" class="flex items-center gap-3 px-3 py-2 hover:bg-turquesa/5 rounded-lg transition-colors text-sm text-slate-700">
                                <span class="material-symbols-outlined text-lg text-turquesa-dark">payments</span> Mis Pagos
                            </a>
                        <?php endif; ?>
                        <div class="border-t border-slate-100 mt-1 pt-1">
                            <a href="<?= Router::url('/logout') ?>" class="flex items-center gap-3 px-3 py-2 hover:bg-red-50 rounded-lg transition-colors text-sm text-red-600 font-medium">
                                <span class="material-symbols-outlined text-lg">logout</span> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= Router::url('/login') ?>" class="flex items-center gap-2 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-semibold px-5 py-2.5 rounded-xl hover:shadow-lg transition-all active:scale-95">
                    <span>Ingreso de Clientes</span>
                    <span class="material-symbols-outlined text-xl">account_circle</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- MOBILE NAV DROPDOWN -->
<div id="mobile-nav" class="hidden md:hidden fixed top-16 left-0 w-full bg-white shadow-lg z-40 border-t border-slate-100">
    <nav class="flex flex-col p-4 gap-1">
        <a href="<?= Router::url('/') ?>" class="px-4 py-3 rounded-xl text-sm font-medium <?= $isHome ? 'bg-turquesa/10 text-turquesa-dark' : 'text-petroleo hover:bg-humo' ?>">Inicio</a>
        <a href="<?= Router::url('/search') ?>" class="px-4 py-3 rounded-xl text-sm font-medium <?= $isDestinos ? 'bg-turquesa/10 text-turquesa-dark' : 'text-petroleo hover:bg-humo' ?>">Destinos</a>
        <a href="<?= Router::url('/promotions') ?>" class="relative px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2
            <?= $isPromociones ? 'bg-gradient-to-r from-[#FF6B35] to-[#F4A633] text-white' : 'bg-gradient-to-r from-[#FF6B35]/10 to-[#F4A633]/10 text-[#D4500A]' ?>">
            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1">local_fire_department</span>
            Promociones
            <span class="ml-auto bg-[#FF4444] text-white text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded-full animate-pulse">HOT</span>
        </a>
        <a href="<?= Router::url('/asesoria') ?>" class="px-4 py-3 rounded-xl text-sm font-medium <?= $isAsesoria ? 'bg-turquesa/10 text-turquesa-dark' : 'text-petroleo hover:bg-humo' ?>">Asesorías</a>
    </nav>
</div>

<!-- CONTENIDO -->
<main class="pt-20">
    <?php if (isset($flash) && $flash): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 pt-4">
        <div class="p-4 rounded-xl text-sm font-medium animate-fadeInUp <?= $flash['type'] === 'exito' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
            <?= htmlspecialchars($flash['message']) ?>
            <?php if (!empty($flash['details'])): ?>
                <details class="mt-2 text-xs text-petroleo/60">
                    <summary class="font-medium">Detalles (debug)</summary>
                    <pre class="mt-2 text-[11px] p-2 bg-white rounded border text-petroleo/70" style="white-space:pre-wrap;word-break:break-word"><?= htmlspecialchars(is_array($flash['details']) ? print_r($flash['details'], true) : (string)$flash['details']) ?></pre>
                </details>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?= $viewContent ?>
</main>

<!-- FOOTER -->
<footer class="w-full bg-gradient-to-br from-petroleo-dark via-petroleo to-petroleo-light text-white mt-auto py-10 sm:py-12 md:py-16 px-4 sm:px-8 md:px-12 border-t-4 border-turquesa">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <img src="<?= Router::url('/img/sin_fondo.png') ?>" alt="Logo" class="h-8 brightness-0 invert">
                <h2 class="text-xl font-bold">Aventuras Travel</h2>
            </div>
            <p class="text-xs text-white/50 leading-relaxed uppercase tracking-widest">
                El horizonte de la posibilidad. Viajes nacionales e internacionales diseñados con pasión.
            </p>
            <div class="mt-4 text-xs text-white/60 space-y-1">
                <p><strong>RUC:</strong> 10475951587</p>
                <p><strong>Dirección:</strong> Jr. Zavala 568A, Pucallpa</p>
                <p><strong>Teléfono:</strong> 976324716</p>
                <p><strong>Email:</strong> aventurastravelpucallpa@gmail.com</p>
            </div>
        </div>
        <div>
            <h3 class="text-xs font-bold text-turquesa uppercase tracking-widest mb-4">Destinos</h3>
            <ul class="space-y-2 text-xs text-white/50">
                <li>Cusco, Perú</li>
                <li>Cancún, México</li>
                <li>Punta Cana</li>
                <li>París, Francia</li>
            </ul>
        </div>
        <div>
            <h3 class="text-xs font-bold text-turquesa uppercase tracking-widest mb-4">Servicios</h3>
            <ul class="space-y-2 text-xs text-white/50">
                <li>Viajes Familiares</li>
                <li>Viajes Escolares</li>
                <li>Paquetes Corporativos</li>
                <li>Asesoría Personalizada</li>
            </ul>
        </div>
        <div>
            <h3 class="text-xs font-bold text-turquesa uppercase tracking-widest mb-4">Legal</h3>
            <ul class="space-y-2 text-xs text-white/50">
                <li>Términos y Condiciones</li>
                <li>Política de Privacidad</li>
                <li>Política de Cancelaciones</li>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-white/10 flex justify-between items-center">
        <p class="text-xs text-white/30">© 2026 Aventuras Travel Pucallpa. Todos los derechos reservados.</p>
    </div>
</footer>

<script src="<?= Router::url('/assets/js/app.js') ?>"></script>
<script>
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('user-menu-wrapper');
    const dropdown = document.getElementById('user-dropdown');
    if (wrapper && dropdown && !wrapper.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>
</body>
</html>
