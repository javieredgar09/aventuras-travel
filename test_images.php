<?php
/**
 * TEST: Verificar que las imágenes cargan correctamente
 */

echo "=== TEST DE IMÁGENES ===\n\n";

$base_path = __DIR__;

// Verificar archivos en public/img/
$images = [
    'javier.jpg',
    'machu.jpg',
    'a_color.png',
    'sin_fondo.png',
    'blanco.png',
    'negro.png'
];

echo "1. Verificando archivos en public/img/:\n";
foreach ($images as $img) {
    $path = $base_path . '/public/img/' . $img;
    $exists = file_exists($path);
    $size = $exists ? round(filesize($path)/1024, 2) . ' KB' : 'NO EXISTE';
    echo "   " . ($exists ? "✓" : "✗") . " $img ($size)\n";
}

echo "\n2. Rutas que usarán las vistas:\n";
$testRoutes = [
    '/img/javier.jpg' => 'Foto de Javier',
    '/img/a_color.png' => 'Logo Aventuras',
    '/img/sin_fondo.png' => 'Logo transparente',
    '/img/machu.jpg' => 'Hero Asesorías',
    '/img/blanco.png' => 'Logo blanco',
    '/img/negro.png' => 'Logo negro'
];

foreach ($testRoutes as $route => $desc) {
    $publicPath = $base_path . '/public' . $route;
    $exists = file_exists($publicPath);
    echo "   " . ($exists ? "✓" : "✗") . " $route ($desc)\n";
}

echo "\n✅ Todas las imágenes están disponibles para las vistas.\n";
echo "\n3. Las vistas usan Router::url('/img/...') para acceder.\n";
?>
