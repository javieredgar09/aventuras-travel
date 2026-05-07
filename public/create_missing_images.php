<?php
/**
 * Crear imágenes faltantes
 * Ejecutar: php public/create_missing_images.php
 */

// Crear machu.jpg si no existe
if (!file_exists(__DIR__ . '/img/machu.jpg')) {
    $width = 1600;
    $height = 900;
    
    // Crear imagen con gradiente verde (naturaleza)
    $image = imagecreatetruecolor($width, $height);
    
    // Colores: gradiente de verde a azul (montaña)
    $verde_oscuro = imagecolorallocate($image, 20, 100, 50);
    $verde = imagecolorallocate($image, 40, 140, 80);
    $cielo = imagecolorallocate($image, 100, 180, 200);
    $blanco = imagecolorallocate($image, 255, 255, 255);
    
    // Cielo
    imagefilledrectangle($image, 0, 0, $width, $height/3, $cielo);
    
    // Gradiente de montaña
    for ($y = 0; $y < $height*2/3; $y += 5) {
        $shade = 40 + ($y / ($height*2/3)) * 40;
        $color = imagecolorallocate($image, 20, $shade, 60);
        imagefilledrectangle($image, 0, $height/3 + $y, $width, $height/3 + $y + 5, $color);
    }
    
    // Picos nevados
    for ($x = 0; $x < $width; $x += 200) {
        $points = [
            $x, $height*0.6,
            $x + 100, $height*0.3,
            $x + 200, $height*0.6
        ];
        imagefilledpolygon($image, $points, 3, $blanco);
    }
    
    // Guardar
    imagejpeg($image, __DIR__ . '/img/machu.jpg', 85);
    imagedestroy($image);
    echo "✓ machu.jpg creado\n";
} else {
    echo "✓ machu.jpg ya existe\n";
}

// Verificar todas las imágenes
$images = glob(__DIR__ . '/img/*.{jpg,png,jpeg}', GLOB_BRACE);
echo "\n✓ Imágenes disponibles en public/img/:\n";
foreach ($images as $img) {
    echo "  - " . basename($img) . " (" . round(filesize($img)/1024, 2) . " KB)\n";
}
?>
