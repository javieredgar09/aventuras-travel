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
                    'petroleo': '#1B3A4B',
                    'petroleo-light': '#2D5468',
                    'turquesa': '#4ABED9',
                    'turquesa-dark': '#00687A',
                    'humo': '#F6FAFC',
                    'superficie': '#EAF0F2',
                },
                fontFamily: {
                    'sans': ['Inter', 'system-ui', 'sans-serif'],
                },
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

$activeClass = 'text-turquesa-dark font-semibold border-b-2 border-turquesa px-1 py-1 transition-all';
$inactiveClass = 'text-slate-600 hover:text-turquesa-dark transition-all px-1 py-1';
?>

<!-- NAVBAR -->
<header class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md shadow-sm h-20 flex justify-between items-center px-8">
    <div class="flex items-center gap-8">
        <a href="<?= Router::url('/') ?>" class="flex items-center gap-3">
            <img src="/aventuras/img/a_color.png" alt="Logo" class="h-10">
            <span class="text-2xl font-bold text-petroleo tracking-tight">Aventuras Travel</span>
        </a>
        <nav class="hidden md:flex gap-6 items-center">
            <a href="<?= Router::url('/') ?>" class="<?= $isHome ? $activeClass : $inactiveClass ?>">Inicio</a>
            <a href="<?= Router::url('/search') ?>" class="<?= $isDestinos ? $activeClass : $inactiveClass ?>">Destinos</a>
            <a href="<?= Router::url('/promotions') ?>" class="<?= $isPromociones ? $activeClass : $inactiveClass ?>">Promociones</a>
            <a href="<?= Router::url('/asesoria') ?>" class="<?= $isAsesoria ? $activeClass : $inactiveClass ?>">Asesorías</a>
        </nav>
    </div>
    <div class="flex items-center gap-4">
        <a href="<?= Router::url('/login') ?>" class="flex items-center gap-2 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-semibold px-5 py-2.5 rounded-xl hover:shadow-lg transition-all active:scale-95">
            <span>Ingreso de Clientes</span>
            <span class="material-symbols-outlined text-xl">account_circle</span>
        </a>
    </div>
</header>

<!-- CONTENIDO -->
<main class="pt-20">
    <?php if (isset($flash) && $flash): ?>
    <div class="max-w-7xl mx-auto px-8 pt-4">
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
<footer class="w-full bg-petroleo text-white mt-auto py-16 px-12">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <img src="/aventuras/img/sin_fondo.png" alt="Logo" class="h-8 brightness-0 invert">
                <h2 class="text-xl font-bold">Aventuras Travel</h2>
            </div>
            <p class="text-xs text-white/50 leading-relaxed uppercase tracking-widest">
                El horizonte de la posibilidad. Viajes nacionales e internacionales diseñados con pasión.
            </p>
            <div class="mt-4 text-xs text-white/60 space-y-1">
                <p><strong>RUC:</strong> 10475951587</p>
                <p><strong>Dirección:</strong> Jr. Zavala 568A, Pucallpa</p>
                <p><strong>Teléfono:</strong> 976324716</p>
                <p><strong>Email:</strong> reservas.aventurastravelpcl@gmail.com</p>
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

<script src="<?= Router::url('/assets/js/app_shared.js') ?>"></script>
<script src="<?= Router::url('/assets/js/app_client.js') ?>"></script>
</body>
</html>
