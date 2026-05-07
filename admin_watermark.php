<?php
require_once __DIR__ . '/core/Database.php';
$db = Database::getInstance();

$message = '';
$uploaded = [];

// Lista de las promociones insertadas
$targetPromos = [
    'cyber_punta_cana.jpg' => 'Cyber Days Punta Cana - Catalonia (US$ 1025)',
    'cyber_panama_ciudad.jpg' => 'Cyber Days Panamá Ciudad y Playa (US$ 939)',
    'cyber_panama.jpg' => 'Cyber Days Panamá (US$ 1225)',
    'cyber_curacao_panama.jpg' => 'Cyber Days Curacao y Panamá desde Chiclayo (US$ 1385)',
    'cyber_rio_panama.jpg' => 'Cyber Days Rio de Janeiro y Panamá (US$ 1489)'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logoPath = __DIR__ . '/public/assets/img/logo.png';
    // Fallback if logo is missing 
    if (!file_exists($logoPath)) {
        // Try other common path
        $logoPath = __DIR__ . '/public/assets/images/logo.png';
    }

    $logoImg = file_exists($logoPath) ? imagecreatefrompng($logoPath) : null;
    
    foreach ($_FILES['promos']['tmp_name'] as $targetFile => $tmpName) {
        if (empty($tmpName) || !isset($targetPromos[$targetFile])) {
            continue; // Skipped input
        }

        $info = getimagesize($tmpName);
        if ($info === false) {
            $message .= "<br>Error: Archivo no es imagen ($targetFile)";
            continue;
        }

        // Cargar imagen subida
        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg': $source = imagecreatefromjpeg($tmpName); break;
            case 'image/png':  $source = imagecreatefrompng($tmpName); break;
            case 'image/webp': $source = imagecreatefromwebp($tmpName); break;
            default: continue 2;
        }

        $srcW = imagesx($source);
        $srcH = imagesy($source);

        // Agregando marca de agua (Logo)
        if ($logoImg) {
            $logoW = imagesx($logoImg);
            $logoH = imagesy($logoImg);

            // Escalar logo al 20% del ancho de la imagen base si es muy grande
            $targetLogoW = min($logoW, $srcW * 0.25);
            $targetLogoH = ($logoH / $logoW) * $targetLogoW;

            // Posición: Arriba a la derecha con un margen de 30px
            $margen = 30;
            $dstX = $srcW - $targetLogoW - $margen;
            $dstY = $margen;

            // Re-muestrear logo sobre la imagen
            imagecopyresampled(
                $source, $logoImg, 
                $dstX, $dstY, 
                0, 0, 
                $targetLogoW, $targetLogoH, 
                $logoW, $logoH
            );
        }

        // Guardar
        $savePath = __DIR__ . '/storage/promociones/' . $targetFile;
        if (!is_dir(dirname($savePath))) mkdir(dirname($savePath), 0777, true);
        
        imagejpeg($source, $savePath, 90);
        
        imagedestroy($source);
        $uploaded[] = $targetFile;
    }

    if ($logoImg) imagedestroy($logoImg);

    if (count($uploaded) > 0) {
        $message = "¡Éxito! Se subieron y sellaron con tu logo " . count($uploaded) . " imágenes.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sellar Cyber Promos | Aventuras Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

<div class="max-w-2xl w-full bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-black text-[#1B3A4B] mb-2">Editor de Promociones Cyber</h1>
        <p class="text-gray-500">He actualizado los precios en la base de datos (sumando $90 a cada uno). <br>Sube aquí las imágenes que me compartiste y el sistema les colocará tu logo automáticamente.</p>
    </div>

    <?php if ($message): ?>
    <div class="bg-green-100 text-green-800 p-4 rounded-xl font-bold text-center mb-6 border border-green-200">
        <?= $message ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php foreach ($targetPromos as $file => $name): ?>
        <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 bg-gray-50 hover:bg-gray-100 transition-colors">
            <label class="block cursor-pointer">
                <span class="block text-sm font-bold text-[#00687A] mb-2"><?= $name ?></span>
                <input type="file" name="promos[<?= $file ?>]" accept="image/*" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-[#4ABED9] file:text-white
                    hover:file:bg-[#00687A] transition-colors
                ">
            </label>
            <?php if(in_array($file, $uploaded)): ?>
                <div class="mt-2 text-xs font-bold text-emerald-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Subida y sellada con éxito
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <button type="submit" class="w-full bg-gradient-to-r from-[#00687A] to-[#4ABED9] text-white font-black py-4 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all">
            Procesar Imágenes y Agregar Logo
        </button>
    </form>
    
    <div class="mt-6 text-center">
        <a href="/aventuras/promociones" class="text-sm font-bold text-[#4ABED9] hover:underline">← Ir a ver las promociones online</a>
    </div>
</div>

</body>
</html>
