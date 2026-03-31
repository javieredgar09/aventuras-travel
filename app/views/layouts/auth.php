<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Iniciar Sesión - Aventuras Travel') ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= Router::url('/assets/css/styles.css') ?>">
    <link rel="icon" href="/aventuras/img/sin_fondo.png" type="image/png">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'petroleo': '#1B3A4B',
                    'turquesa': '#4ABED9',
                    'turquesa-dark': '#00687A',
                    'humo': '#F6FAFC',
                },
                fontFamily: { 'sans': ['Inter', 'system-ui', 'sans-serif'] },
            },
        },
    }
    </script>
</head>
<body class="min-h-screen font-sans relative overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-petroleo/90 via-turquesa-dark/80 to-petroleo/90 z-0"></div>
    <img src="/aventuras/img/machu.jpg" alt="" class="absolute inset-0 w-full h-full object-cover opacity-30 z-0" onerror="this.style.display='none'">

    <!-- Help button -->
    <div class="absolute top-6 right-6 z-20">
        <a href="<?= Router::url('/') ?>" class="flex items-center gap-2 bg-white/20 backdrop-blur-md text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-white/30 transition-all">
            <span class="material-symbols-outlined text-lg">help</span>
            Ayuda
        </a>
    </div>

    <!-- Content -->
    <div class="relative z-10 min-h-screen flex items-center justify-center p-6">
        <?= $viewContent ?>
    </div>

    <!-- Bottom bar -->
    <div class="absolute bottom-0 w-full z-20 px-8 py-4 flex justify-between items-center">
        <div class="text-white/60 text-xs">
            <p class="uppercase tracking-widest text-[10px] text-white/40">Destino Destacado</p>
            <p class="font-semibold">Cusco, Perú</p>
        </div>
        <div class="flex gap-3 text-white/50">
            <span class="material-symbols-outlined text-xl">travel_explore</span>
            <span class="material-symbols-outlined text-xl">public</span>
            <span class="material-symbols-outlined text-xl">share</span>
        </div>
    </div>
</body>
</html>
