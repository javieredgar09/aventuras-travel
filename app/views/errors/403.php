<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado 403 - Aventuras Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .float-anim { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#0D2432] via-[#1B3A4B] to-[#2D5468] flex items-center justify-center px-4">
    <div class="text-center max-w-lg mx-auto">
        <!-- Icono animado -->
        <div class="float-anim inline-block mb-8">
            <div class="w-28 h-28 rounded-3xl bg-white/10 backdrop-blur flex items-center justify-center mx-auto border border-white/20">
                <span class="material-symbols-outlined text-6xl text-amber-400">lock</span>
            </div>
        </div>

        <!-- Código de error -->
        <h1 class="text-8xl font-black text-white mb-2 leading-none">403</h1>
        <p class="text-2xl font-bold text-[#4ABED9] mb-3">Acceso Denegado</p>
        <p class="text-white/60 mb-10 text-base leading-relaxed">
            No tienes permiso para acceder a esta sección.<br>
            Si crees que es un error, contacta al administrador.
        </p>

        <!-- Acciones -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?= defined('APP_BASE') ? rtrim(APP_BASE, '/') . '/login' : '/login' ?>"
               class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all border border-white/20">
                <span class="material-symbols-outlined text-xl">login</span>
                Iniciar Sesión
            </a>
            <a href="<?= defined('APP_BASE') ? rtrim(APP_BASE, '/') . '/' : '/' ?>"
               class="inline-flex items-center gap-2 px-6 py-3 bg-[#4ABED9] hover:bg-[#00687A] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#4ABED9]/30">
                <span class="material-symbols-outlined text-xl">home</span>
                Ir al Inicio
            </a>
        </div>

        <!-- Footer -->
        <p class="mt-12 text-white/30 text-xs">Aventuras Travel &copy; <?= date('Y') ?></p>
    </div>
</body>
</html>
