<?php
define('BASE_PATH', 'c:/xampp/htdocs/aventuras');
define('STORAGE_PATH', BASE_PATH . '/storage');

$files = glob(STORAGE_PATH . '/recibos/*.pdf');
foreach ($files as $f) {
    $name = basename($f);
    $size = filesize($f);
    $header = file_get_contents($f, false, null, 0, 5);
    $isPdf = ($header === '%PDF-');
    echo "$name | $size bytes | Valid PDF: " . ($isPdf ? 'YES' : 'NO') . "\n";
}
