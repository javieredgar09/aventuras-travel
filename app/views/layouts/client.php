<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Mi Viaje - Aventuras Travel') ?></title>
    <link rel="icon" href="<?= Router::url('/img/sin_fondo.png') ?>" type="image/png">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <?php
    if (!function_exists('asset_version')) {
        function asset_version(string $publicPath): string {
            $full = BASE_PATH . '/public' . $publicPath;
            $url = Router::url($publicPath);
            return file_exists($full) ? $url . '?v=' . filemtime($full) : $url . '?v=' . time();
        }
    }
    ?>
    <link rel="stylesheet" href="<?= asset_version('/assets/css/styles.css') ?>">
    <script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "error": "#ba1a1a",
                    "surface": "#f6fafc",
                    "inverse-primary": "#65d5f1",
                    "surface-dim": "#d6dbdd",
                    "outline": "#6d797d",
                    "on-tertiary-fixed-variant": "#6c3a00",
                    "outline-variant": "#bdc8cd",
                    "on-secondary-fixed-variant": "#124c63",
                    "primary-fixed-dim": "#65d5f1",
                    "secondary-container": "#aee1fd",
                    "on-primary-container": "#004a58",
                    "on-secondary": "#ffffff",
                    "error-container": "#ffdad6",
                    "secondary-fixed-dim": "#9bcde9",
                    "on-primary": "#ffffff",
                    "on-surface": "#171c1e",
                    "tertiary": "#8f4e00",
                    "on-tertiary-fixed": "#2e1500",
                    "on-secondary-container": "#31657d",
                    "on-error-container": "#93000a",
                    "background": "#f6fafc",
                    "tertiary-fixed-dim": "#ffb779",
                    "primary-container": "#4abed9",
                    "on-surface-variant": "#3d494c",
                    "on-primary-fixed-variant": "#004e5c",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low": "#f0f4f6",
                    "surface-container-high": "#e4e9eb",
                    "inverse-surface": "#2c3133",
                    "surface-container": "#eaeff0",
                    "primary": "#00687a",
                    "surface-tint": "#00687a",
                    "tertiary-container": "#f09d50",
                    "inverse-on-surface": "#edf1f3",
                    "surface-variant": "#dfe3e5",
                    "secondary": "#30647c",
                    "primary-fixed": "#acedff",
                    "secondary-fixed": "#c0e8ff",
                    "surface-container-highest": "#dfe3e5",
                    "tertiary-fixed": "#ffdcc1",
                    "on-error": "#ffffff",
                    "on-tertiary-container": "#673700",
                    "on-tertiary": "#ffffff",
                    "on-secondary-fixed": "#001e2b",
                    "on-primary-fixed": "#001f26",
                    "surface-bright": "#f6fafc",
                    "on-background": "#171c1e",
                    "petroleo": "#1B3A4B",
                    "turquesa": "#4ABED9",
                    "turquesa-dark": "#00687A",
                    "superficie": "#EAF0F2",
                    "humo": "#F6FAFC"
                },
                fontFamily: {
                    "headline": ["Inter","system-ui","sans-serif"],
                    "body": ["Inter","system-ui","sans-serif"],
                    "sans": ["Inter","system-ui","sans-serif"]
                },
                borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "2xl": "1rem", "3xl": "1.5rem", "4xl": "2rem", "full": "9999px"},
            },
        },
    }
    </script>
</head>
<body class="bg-surface font-body text-on-surface">

<?php
$uri = $_SERVER['REQUEST_URI'] ?? '';
$isRepresentante = ($_SESSION['user']['rol'] ?? '') === 'representante';
$navItems = $isRepresentante ? [
    ['/leader/dashboard', 'Panel', 'home'],
    ['/leader/contracts', 'Contratos', 'description'],
    ['/leader/payments',  'Pagos', 'payments'],
] : [
    ['/client/dashboard', 'Panel', 'home'],
    ['/client/services',  'Mis Viajes', 'explore'],
    ['/client/payments',  'Pagos', 'payments'],
];
$userName = htmlspecialchars(trim(($_SESSION['user']['nombre'] ?? '') . ' ' . ($_SESSION['user']['apellido'] ?? '')));
$userCode = htmlspecialchars($_SESSION['user']['codigo'] ?? '');
$userInitials = strtoupper(substr($_SESSION['user']['nombre'] ?? 'U', 0, 1) . substr($_SESSION['user']['apellido'] ?? '', 0, 1));
?>

<!-- TOP NAV -->
<nav class="fixed top-0 w-full z-50 bg-gradient-to-r from-white/80 to-white/90 backdrop-blur-2xl shadow-lg shadow-turquesa-dark/10 border-b border-turquesa/15">
    <div class="flex justify-between items-center px-6 py-3 max-w-[1600px] mx-auto">
        <div class="flex items-center gap-8">
            <a href="<?= Router::url('/') ?>" class="flex items-center gap-2">
                <img src="<?= Router::url('/img/sin_fondo.png') ?>" alt="Aventuras Travel" class="h-10 w-10 object-contain">
                <span class="text-xl font-bold text-petroleo tracking-tight hidden sm:inline">Aventuras Travel <span class="text-turquesa-dark font-medium">Pucallpa</span></span>
            </a>
            <div class="hidden lg:flex items-center gap-1 border-l border-petroleo/10 pl-8">
                <?php foreach ($navItems as $item):
                    $isActive = strpos($uri, $item[0]) !== false;
                ?>
                <a href="<?= Router::url($item[0]) ?>"
                   class="px-4 py-2 font-medium transition-colors tracking-tight <?= $isActive ? 'text-turquesa-dark font-bold border-b-2 border-turquesa' : 'text-petroleo/70 hover:text-turquesa-dark' ?>">
                    <?= $item[1] ?>
                </a>
                <?php endforeach; ?>
                <a href="<?= Router::url('/client/soporte') ?>"
                   class="px-4 py-2 font-medium transition-colors tracking-tight <?= strpos($uri, '/client/soporte') !== false ? 'text-turquesa-dark font-bold border-b-2 border-turquesa' : 'text-petroleo/70 hover:text-turquesa-dark' ?>">
                   Soporte
                </a>
            </div>
        </div>
        <div class="flex items-center gap-2 md:gap-4">
            <button class="p-2 hover:bg-turquesa/5 rounded-lg transition-all relative">
                <span class="material-symbols-outlined text-petroleo">notifications</span>
                <?php if (!empty($notificaciones)): ?>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                <?php endif; ?>
            </button>
            <div class="relative" id="userMenuWrapper">
                <button onclick="document.getElementById('userDropdown').classList.toggle('hidden')" class="flex items-center gap-2 px-3 py-1.5 hover:bg-turquesa/5 rounded-full transition-colors">
                    <div class="w-9 h-9 rounded-full bg-turquesa/15 flex items-center justify-center text-turquesa-dark font-black text-sm">
                        <?= $userInitials ?>
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-bold text-petroleo leading-none"><?= $userCode ?></p>
                        <?php $loggedRol = $_SESSION['user']['rol'] ?? ''; ?>
                        <p class="text-[10px] text-slate-500 leading-none mt-0.5"><?= $loggedRol === 'admin' ? 'Administrador' : ($loggedRol === 'representante' ? 'Representante' : 'Cliente') ?></p>
                    </div>
                    <span class="material-symbols-outlined text-sm text-slate-400 hidden sm:inline">keyboard_arrow_down</span>
                </button>
                <div id="userDropdown" class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 p-2 z-50">
                    <div class="px-3 py-2 border-b border-slate-100 mb-1">
                        <p class="text-sm font-bold text-petroleo"><?= $userName ?></p>
                        <p class="text-xs text-slate-400"><?= $userCode ?></p>
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
                            <span class="material-symbols-outlined text-lg text-turquesa-dark">dashboard</span> Mi Panel
                        </a>
                        <a href="<?= Router::url('/client/payments') ?>" class="flex items-center gap-3 px-3 py-2 hover:bg-turquesa/5 rounded-lg transition-colors text-sm text-slate-700">
                            <span class="material-symbols-outlined text-lg text-turquesa-dark">receipt_long</span> Mis Pagos
                        </a>
                    <?php endif; ?>
                    <div class="border-t border-slate-100 mt-1 pt-1">
                        <a href="<?= Router::url('/logout') ?>" class="flex items-center gap-3 px-3 py-2 hover:bg-red-50 rounded-lg transition-colors text-sm text-red-600 font-medium">
                            <span class="material-symbols-outlined text-lg">logout</span> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="pt-20 pb-24 px-4 md:px-8 max-w-[1600px] mx-auto">
    <?php if (isset($flash) && $flash): ?>
    <div class="p-4 rounded-xl text-sm font-medium mb-6 <?= $flash['type'] === 'exito' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <?= $viewContent ?>
</main>

<!-- MOBILE BOTTOM NAV -->
<nav class="lg:hidden fixed bottom-0 w-full bg-white/95 backdrop-blur-lg shadow-[0_-4px_20px_rgba(0,0,0,0.05)] flex justify-around items-center py-3 px-4 z-50 border-t border-slate-100 rounded-t-3xl">
    <?php foreach ($navItems as $item):
        $isActive = strpos($uri, $item[0]) !== false;
    ?>
    <a href="<?= Router::url($item[0]) ?>" class="flex flex-col items-center gap-1 <?= $isActive ? 'text-primary' : 'text-slate-400 hover:text-primary' ?> transition-colors">
        <span class="material-symbols-outlined"><?= $item[2] ?></span>
        <span class="text-[9px] font-black uppercase tracking-widest"><?= $item[1] ?></span>
    </a>
    <?php endforeach; ?>
    <?php $soporteActive = strpos($uri, '/client/soporte') !== false; ?>
    <a href="<?= Router::url('/client/soporte') ?>" class="flex flex-col items-center gap-1 <?= $soporteActive ? 'text-primary' : 'text-slate-400 hover:text-primary' ?> transition-colors">
        <span class="material-symbols-outlined">headset_mic</span>
        <span class="text-[9px] font-black uppercase tracking-widest">Soporte</span>
    </a>
    <a href="<?= Router::url('/logout') ?>" class="flex flex-col items-center gap-1 text-slate-400 hover:text-red-500 transition-colors">
        <span class="material-symbols-outlined">logout</span>
        <span class="text-[9px] font-black uppercase tracking-widest">Salir</span>
    </a>
</nav>

<script src="<?= asset_version('/assets/js/app.js') ?>"></script>
<script>
// Close user dropdown on outside click
document.addEventListener('click', function(e) {
    var w = document.getElementById('userMenuWrapper');
    var d = document.getElementById('userDropdown');
    if (w && d && !w.contains(e.target)) d.classList.add('hidden');
});
</script>
</body>
</html>
