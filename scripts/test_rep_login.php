<?php
/**
 * test_rep_login.php — Verificar que el login del representante funciona
 */
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
require BASE_PATH . '/core/Database.php';

$db = Database::getInstance();

$user = $db->fetchOne("SELECT id, codigo, password, nombre, apellido, rol FROM usuarios WHERE codigo = 'REP-CCPA-001'");

if (!$user) {
    echo "ERROR: Usuario REP-CCPA-001 no encontrado.\n";
    exit(1);
}

echo "Usuario encontrado:\n";
echo "  ID: {$user['id']}\n";
echo "  Código: {$user['codigo']}\n";
echo "  Nombre: {$user['nombre']} {$user['apellido']}\n";
echo "  Rol: {$user['rol']}\n";
echo "  Hash length: " . strlen($user['password']) . "\n";

// Verificar contraseña
$testPass = 'cliente123';
$valid = password_verify($testPass, $user['password']);
echo "\n  password_verify('cliente123'): " . ($valid ? 'OK ✓' : 'FALLO ✗') . "\n";

if (!$valid) {
    echo "\nRegenerando hash para 'cliente123'...\n";
    $newHash = password_hash('cliente123', PASSWORD_DEFAULT);
    $db->query("UPDATE usuarios SET password = ? WHERE id = ?", [$newHash, $user['id']]);
    echo "Hash actualizado.\n";
    
    // Verificar de nuevo
    $user2 = $db->fetchOne("SELECT password FROM usuarios WHERE id = ?", [$user['id']]);
    $valid2 = password_verify('cliente123', $user2['password']);
    echo "Verificación post-update: " . ($valid2 ? 'OK ✓' : 'FALLO ✗') . "\n";
}

// Verificar segundo representante
$user3 = $db->fetchOne("SELECT id, codigo, password FROM usuarios WHERE codigo = 'REP-SA-001'");
if ($user3) {
    $v3 = password_verify('cliente123', $user3['password']);
    echo "\nREP-SA-001 password_verify: " . ($v3 ? 'OK ✓' : 'FALLO ✗') . "\n";
    if (!$v3) {
        $newHash3 = password_hash('cliente123', PASSWORD_DEFAULT);
        $db->query("UPDATE usuarios SET password = ? WHERE id = ?", [$newHash3, $user3['id']]);
        echo "Hash actualizado para REP-SA-001.\n";
    }
}

echo "\n=== CREDENCIALES PARA PROBAR ===\n";
echo "┌─────────────────┬──────────────┬────────────────────────────┐\n";
echo "│ Código           │ Contraseña   │ Descripción                │\n";
echo "├─────────────────┼──────────────┼────────────────────────────┤\n";
echo "│ REP-CCPA-001     │ cliente123   │ Patricia Vargas (CCPA)     │\n";
echo "│ REP-SA-001       │ cliente123   │ Fernando Ruiz (San Agustín)│\n";
echo "└─────────────────┴──────────────┴────────────────────────────┘\n";
echo "\nURL: http://localhost/aventuras/login\n";
