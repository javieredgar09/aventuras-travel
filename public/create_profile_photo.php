<?php
/**
 * Crear foto de perfil de Javier Edgar Sandy Da Cruz
 * Ejecutar una sola vez: php public/create_profile_photo.php
 */

// Dimensiones
$width = 400;
$height = 400;

// Crear imagen
$image = imagecreatetruecolor($width, $height);

// Definir colores
$petroleo = imagecolorallocate($image, 39, 107, 130);
$petroleo_oscuro = imagecolorallocate($image, 20, 60, 80);
$turquesa = imagecolorallocate($image, 100, 150, 180);
$blanco = imagecolorallocate($image, 255, 255, 255);

// Fondo gradiente simulado con rectángulos
imagefilledrectangle($image, 0, 0, $width, $height/2, $petroleo);
imagefilledrectangle($image, 0, $height/2, $width, $height, $petroleo_oscuro);

// Marco
imagerectangle($image, 15, 15, $width-15, $height-15, $turquesa);
imagerectangle($image, 20, 20, $width-20, $height-20, $turquesa);

// Círculo para cabeza (avatar)
imagefilledellipse($image, $width/2, 120, 120, 120, $turquesa);

// Cuerpo (rectángulo redondeado simulado)
imagefilledrectangle($image, 60, 200, $width-60, $height-40, $petroleo_oscuro);

// Guardar
$photoPath = __DIR__ . '/img/javier.jpg';
imagejpeg($image, $photoPath, 85);
imagedestroy($image);

echo "✓ Foto de perfil creada: $photoPath\n";
?>
