<?php
define('BASE_PATH', 'c:/xampp/htdocs/aventuras');
$f = BASE_PATH . '/public/img/logo_recibo.png';
echo 'Exists: ' . (file_exists($f) ? 'YES' : 'NO') . "\n";
echo 'Size: ' . filesize($f) . "\n";
$info = getimagesize($f);
echo 'W: ' . $info[0] . ' H: ' . $info[1] . "\n";
echo 'Type: ' . $info['mime'] . "\n";

// Also check the sin_fondo.png
$f2 = BASE_PATH . '/public/img/sin_fondo.png';
echo "\nsin_fondo.png:\n";
echo 'Exists: ' . (file_exists($f2) ? 'YES' : 'NO') . "\n";
$info2 = getimagesize($f2);
echo 'W: ' . $info2[0] . ' H: ' . $info2[1] . "\n";
