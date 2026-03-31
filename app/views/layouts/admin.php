<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Panel Admin - Aventuras Travel') ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= Router::url('/assets/css/styles.css') ?>">
    <link rel="stylesheet" href="<?= Router::url('/assets/css/admin.css') ?>">
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
                fontFamily: { 'sans': ['Inter', 'system-ui', 'sans-serif'] },
            },
        },
    }
    </script>
</head>
<body class="bg-humo font-sans text-petroleo">

<div class="flex min-h-screen">
    <!-- SIDEBAR -->
    <aside class="w-56 bg-petroleo text-white flex flex-col fixed h-full z-40">
        <div class="p-6">
            <h1 class="text-lg font-bold">Gestión Travel</h1>
            <p class="text-xs text-turquesa/60">Azure Horizon</p>
        </div>
            <?php
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            $items = [
                ['/admin/dashboard', 'grid_view', 'Dashboard'],
                ['/admin/sales', 'groups', 'Gestión de Ventas'],
                ['/admin/payments', 'payments', 'Pagos'],
                ['/admin/contracts', 'description', 'Contratos'],
                ['/admin/passengers', 'group', 'Pasajeros'],
                ['/admin/users', 'manage_accounts', 'Usuarios'],
                ['/admin/reports', 'bar_chart', 'Reportes'],
                ['/admin/promotions', 'campaign', 'Promociones']
            ];
            foreach ($items as $item):
                $isActive = strpos($uri, $item[0]) !== false || ($item[0] === '/admin/dashboard' && preg_match('#/admin/?$#', $uri));
                $activeClass = $isActive ? 'sidebar-active bg-white/5' : 'text-petroleo/60 text-white hover:bg-white/10';
            ?>
            <a href="<?= Router::url($item[0]) ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?= $activeClass ?>">
                <span class="material-symbols-outlined text-[20px]"><?= $item[1] ?></span>
                <?= $item[2] ?>
            </a>
            <?php endforeach; ?>
        </nav>
        <div class="p-4 mt-auto border-t border-white/10">
            <a href="<?= Router::url('/logout') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-300 hover:bg-red-500/20 transition-all">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                Cerrar Sesión
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 ml-56">
        <!-- TOP BAR -->
        <header class="sticky top-0 z-30 bg-humo/80 backdrop-blur-md border-b border-petroleo/5 h-16 flex items-center justify-between px-8">
            <div></div>
            <div class="flex items-center gap-4">
                <button class="relative">
                    <span class="material-symbols-outlined text-2xl text-petroleo/60 hover:text-petroleo transition-all">notifications</span>
                </button>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold">Admin Aventuras</p>
                        <p class="text-[11px] text-petroleo/50">Superusuario</p>
                    </div>
                    <div class="w-9 h-9 bg-petroleo text-white rounded-full flex items-center justify-center font-bold text-sm">
                        <?= strtoupper(substr($_SESSION['user']['nombre'] ?? 'A', 0, 1)) ?>
                    </div>
                </div>
                <a href="<?= Router::url('/logout') ?>" class="text-sm text-petroleo/50 hover:text-red-500 transition-all ml-2">
                    <span class="material-symbols-outlined text-xl">logout</span>
                </a>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <div class="p-8">
            <?php if (isset($flash) && $flash): ?>
            <div class="p-4 rounded-xl text-sm font-medium animate-fadeInUp mb-6 <?= $flash['type'] === 'exito' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
                <?= htmlspecialchars($flash['message']) ?>
                <?php if (!empty($flash['details'])): ?>
                    <details class="mt-2 text-xs text-petroleo/60">
                        <summary class="font-medium">Detalles (debug)</summary>
                        <pre class="mt-2 text-[11px] p-2 bg-white rounded border text-petroleo/70" style="white-space:pre-wrap;word-break:break-word"><?= htmlspecialchars(is_array($flash['details']) ? print_r($flash['details'], true) : (string)$flash['details']) ?></pre>
                    </details>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?= $viewContent ?>
        </div>
    </div>
</div>

    <script src="<?= Router::url('/assets/js/app_shared.js') ?>"></script>
    <script src="<?= Router::url('/assets/js/app_admin.js') ?>"></script>
</body>
</html>
