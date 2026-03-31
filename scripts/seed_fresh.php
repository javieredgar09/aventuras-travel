<?php
/**
 * seed_fresh.php — Limpia toda la BD y crea datos completos de prueba
 * Uso: php scripts/seed_fresh.php
 *
 * Genera hashes bcrypt reales para que el login funcione.
 * Contraseñas: admin123 (admin), cliente123 (clientes)
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', BASE_PATH . '/storage');
}

if (!class_exists('Database')) {
    require BASE_PATH . '/core/Database.php';
}

$db = Database::getInstance();

echo "=== AVENTURAS TRAVEL — SEED FRESH ===\n\n";

// ─── 1. TRUNCAR TODAS LAS TABLAS ────────────────────────────────
echo "[1] Limpiando base de datos...\n";
$pdo = $db->getConnection();
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['notificaciones','archivos','comprobantes','plan_cuotas','vouchers',
           'pagos','servicios','vuelos','pasajeros','contratos','clientes',
           'servicios_grupo','grupos','promociones','usuarios'];
foreach ($tables as $t) {
    try { $pdo->exec("TRUNCATE TABLE `{$t}`"); } catch (Exception $e) {
        try { $pdo->exec("DELETE FROM `{$t}`"); $pdo->exec("ALTER TABLE `{$t}` AUTO_INCREMENT = 1"); } catch (Exception $e2) {}
    }
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "    OK — Todas las tablas limpias\n\n";

// ─── Hashes reales ──────────────────────────────────────────────
$hashAdmin   = password_hash('admin123', PASSWORD_DEFAULT);
$hashClient  = password_hash('cliente123', PASSWORD_DEFAULT);

// ─── 2. USUARIOS ────────────────────────────────────────────────
echo "[2] Creando usuarios...\n";
$usuarios = [
    // id=1: Admin
    ['codigo'=>'admin',         'password'=>$hashAdmin,  'nombre'=>'Carlos',     'apellido'=>'Mendoza',        'email'=>'admin@aventurastravel.pe',     'telefono'=>'+51-961-555-100', 'rol'=>'admin'],
    // id=2: Familia Rodríguez (Cancún)
    ['codigo'=>'AV-2026-001',   'password'=>$hashClient, 'nombre'=>'Miguel',     'apellido'=>'Rodríguez Torres','email'=>'miguel.rodriguez@email.com',  'telefono'=>'+51-961-200-001', 'rol'=>'cliente_familiar'],
    // id=3: Familia García (Punta Cana)
    ['codigo'=>'AV-2026-002',   'password'=>$hashClient, 'nombre'=>'Roberto',    'apellido'=>'García Sánchez', 'email'=>'roberto.garcia@email.com',     'telefono'=>'+51-961-200-002', 'rol'=>'cliente_familiar'],
    // id=4: Familia López (Cusco)
    ['codigo'=>'AV-2026-003',   'password'=>$hashClient, 'nombre'=>'Luisa',      'apellido'=>'López Ramírez',  'email'=>'luisa.lopez@email.com',        'telefono'=>'+51-961-200-003', 'rol'=>'cliente_familiar'],
    // id=5: Representante Colegio CCPA
    ['codigo'=>'REP-CCPA-001',  'password'=>$hashClient, 'nombre'=>'Patricia',   'apellido'=>'Vargas Romero',  'email'=>'patricia.vargas@ccpa.edu.pe',  'telefono'=>'+51-961-300-001', 'rol'=>'representante'],
    // id=6: Jane Vargas (contrato colegio CCPA)
    ['codigo'=>'CCPA-2026-001', 'password'=>$hashClient, 'nombre'=>'Jane',       'apellido'=>'Vargas Romero',  'email'=>'jane.vargas@email.com',        'telefono'=>'+51-961-300-010', 'rol'=>'cliente_familiar'],
    // id=7: Marco Díaz (contrato colegio CCPA)
    ['codigo'=>'CCPA-2026-002', 'password'=>$hashClient, 'nombre'=>'Marco',      'apellido'=>'Díaz Pérez',     'email'=>'marco.diaz@email.com',         'telefono'=>'+51-961-300-011', 'rol'=>'cliente_familiar'],
    // id=8: Representante Colegio San Agustín
    ['codigo'=>'REP-SA-001',    'password'=>$hashClient, 'nombre'=>'Fernando',   'apellido'=>'Ruiz Castillo',  'email'=>'fernando.ruiz@sanagustin.edu.pe','telefono'=>'+51-961-400-001','rol'=>'representante'],
    // id=9: Ana Torres (contrato colegio San Agustín)
    ['codigo'=>'SA-2026-001',   'password'=>$hashClient, 'nombre'=>'Ana',        'apellido'=>'Torres Medina',  'email'=>'ana.torres@email.com',         'telefono'=>'+51-961-400-010', 'rol'=>'cliente_familiar'],
];
foreach ($usuarios as $u) {
    $db->insert('usuarios', $u);
}
echo "    OK — " . count($usuarios) . " usuarios creados\n";

// ─── 3. CLIENTES ────────────────────────────────────────────────
echo "[3] Creando clientes...\n";
$clientes = [
    ['usuario_id'=>2, 'tipo'=>'familiar', 'direccion'=>'Jr. Ucayali 234',                  'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'45678912'],
    ['usuario_id'=>3, 'tipo'=>'familiar', 'direccion'=>'Av. Centenario 890',               'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'32145678'],
    ['usuario_id'=>4, 'tipo'=>'familiar', 'direccion'=>'Jr. Inmaculada 456',               'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'21436587'],
    ['usuario_id'=>6, 'tipo'=>'familiar', 'direccion'=>'Av. Yarinacocha 123',              'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'78901234'],
    ['usuario_id'=>7, 'tipo'=>'familiar', 'direccion'=>'Jr. Húsares de Junín 567',         'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'65432198'],
    ['usuario_id'=>9, 'tipo'=>'familiar', 'direccion'=>'Av. San Martín 321',               'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'87654321'],
];
foreach ($clientes as $c) {
    $db->insert('clientes', $c);
}
echo "    OK — " . count($clientes) . " clientes creados\n";

// ─── 4. GRUPOS ──────────────────────────────────────────────────
echo "[4] Creando grupos...\n";
$grupos = [
    // id=1: Familia Rodríguez — Cancún
    ['nombre'=>'Familia Rodríguez - Cancún 2026',   'tipo'=>'familiar', 'operador'=>'Avianca Tours',     'destino'=>'Cancún, México',                 'fecha_viaje'=>'2026-07-15', 'fecha_retorno'=>'2026-07-22', 'valor_total'=>12500.00, 'deposito'=>3000.00, 'tipo_pago'=>'cuotas', 'total_cuotas'=>6, 'meses_pago'=>'Enero,Febrero,Marzo,Abril,Mayo,Junio', 'estado'=>'activo', 'representante_id'=>2],
    // id=2: Familia García — Punta Cana
    ['nombre'=>'Familia García - Punta Cana 2026',  'tipo'=>'familiar', 'operador'=>'Copa Travel',       'destino'=>'Punta Cana, República Dominicana','fecha_viaje'=>'2026-08-10', 'fecha_retorno'=>'2026-08-17', 'valor_total'=>15000.00, 'deposito'=>15000.00,'tipo_pago'=>'contado','total_cuotas'=>0,  'meses_pago'=>null, 'estado'=>'activo', 'representante_id'=>3],
    // id=3: Familia López — Cusco
    ['nombre'=>'Familia López - Cusco 2026',        'tipo'=>'familiar', 'operador'=>null,                'destino'=>'Cusco, Perú',                    'fecha_viaje'=>'2026-06-20', 'fecha_retorno'=>'2026-06-25', 'valor_total'=>4500.00,  'deposito'=>1500.00, 'tipo_pago'=>'cuotas', 'total_cuotas'=>3,  'meses_pago'=>'Abril,Mayo,Junio', 'estado'=>'activo', 'representante_id'=>4],
    // id=4: Colegio CCPA — Punta Cana
    ['nombre'=>'Promoción 2026 - Colegio CCPA',     'tipo'=>'colegio',  'operador'=>'Aventuras Travel',  'destino'=>'Punta Cana, República Dominicana','fecha_viaje'=>'2026-10-26', 'fecha_retorno'=>'2026-11-02', 'valor_total'=>85000.00, 'deposito'=>25000.00,'tipo_pago'=>'cuotas', 'total_cuotas'=>6,  'meses_pago'=>'Abril,Mayo,Junio,Julio,Agosto,Septiembre', 'estado'=>'activo', 'institucion'=>'Colegio CCPA Pucallpa', 'representante_id'=>5],
    // id=5: Colegio San Agustín — Cancún
    ['nombre'=>'Promoción 2026 - San Agustín',      'tipo'=>'colegio',  'operador'=>'Aventuras Travel',  'destino'=>'Cancún, México',                 'fecha_viaje'=>'2026-11-15', 'fecha_retorno'=>'2026-11-22', 'valor_total'=>72000.00, 'deposito'=>20000.00,'tipo_pago'=>'cuotas', 'total_cuotas'=>6,  'meses_pago'=>'Mayo,Junio,Julio,Agosto,Septiembre,Octubre', 'estado'=>'activo', 'institucion'=>'Colegio San Agustín',   'representante_id'=>8],
];
foreach ($grupos as $g) {
    $db->insert('grupos', $g);
}
echo "    OK — " . count($grupos) . " grupos creados\n";

// ─── 5. CONTRATOS ───────────────────────────────────────────────
echo "[5] Creando contratos...\n";
$contratos = [
    // id=1: Fam Rodríguez → cliente_id=1
    ['codigo'=>'AV-2026-001', 'cliente_id'=>1, 'grupo_id'=>1, 'tipo'=>'familiar', 'destino'=>'Cancún, México', 'hotel'=>'Grand Oasis Cancún - Suite Familiar', 'descripcion'=>'Paquete familiar all-inclusive con excursiones incluidas.', 'fecha_salida'=>'2026-07-15', 'fecha_retorno'=>'2026-07-22', 'valor_total'=>12500.00, 'deposito'=>3000.00, 'saldo'=>9500.00, 'estado'=>'activo', 'moneda'=>'USD'],
    // id=2: Fam García → cliente_id=2
    ['codigo'=>'AV-2026-002', 'cliente_id'=>2, 'grupo_id'=>2, 'tipo'=>'familiar', 'destino'=>'Punta Cana, República Dominicana', 'hotel'=>'Hard Rock Hotel & Casino Punta Cana', 'descripcion'=>'Paquete premium todo incluido con excursiones opcionales.', 'fecha_salida'=>'2026-08-10', 'fecha_retorno'=>'2026-08-17', 'valor_total'=>15000.00, 'deposito'=>15000.00, 'saldo'=>0.00, 'estado'=>'activo', 'moneda'=>'USD'],
    // id=3: Fam López → cliente_id=3
    ['codigo'=>'AV-2026-003', 'cliente_id'=>3, 'grupo_id'=>3, 'tipo'=>'familiar', 'destino'=>'Cusco, Perú', 'hotel'=>'Casa Andina Premium Cusco', 'descripcion'=>'Escapada familiar al corazón del imperio inca.', 'fecha_salida'=>'2026-06-20', 'fecha_retorno'=>'2026-06-25', 'valor_total'=>4500.00, 'deposito'=>1500.00, 'saldo'=>3000.00, 'estado'=>'activo', 'moneda'=>'PEN'],
    // id=4: Jane Vargas (CCPA) → cliente_id=4
    ['codigo'=>'CCPA-2026-001', 'cliente_id'=>4, 'grupo_id'=>4, 'tipo'=>'colegio', 'destino'=>'Punta Cana, República Dominicana', 'hotel'=>'Catalonia Bávaro Beach 5★ Resort & Spa', 'descripcion'=>'Paquete de promoción escolar all-inclusive.', 'fecha_salida'=>'2026-10-26', 'fecha_retorno'=>'2026-11-02', 'valor_total'=>1549.00, 'deposito'=>350.00, 'saldo'=>1199.00, 'estado'=>'activo', 'moneda'=>'USD'],
    // id=5: Marco Díaz (CCPA) → cliente_id=5
    ['codigo'=>'CCPA-2026-002', 'cliente_id'=>5, 'grupo_id'=>4, 'tipo'=>'colegio', 'destino'=>'Punta Cana, República Dominicana', 'hotel'=>'Catalonia Bávaro Beach 5★ Resort & Spa', 'descripcion'=>'Paquete de promoción escolar all-inclusive.', 'fecha_salida'=>'2026-10-26', 'fecha_retorno'=>'2026-11-02', 'valor_total'=>1549.00, 'deposito'=>350.00, 'saldo'=>1199.00, 'estado'=>'activo', 'moneda'=>'USD'],
    // id=6: Ana Torres (San Agustín) → cliente_id=6
    ['codigo'=>'SA-2026-001', 'cliente_id'=>6, 'grupo_id'=>5, 'tipo'=>'colegio', 'destino'=>'Cancún, México', 'hotel'=>'Grand Oasis Cancún', 'descripcion'=>'Paquete de promoción escolar Cancún.', 'fecha_salida'=>'2026-11-15', 'fecha_retorno'=>'2026-11-22', 'valor_total'=>1450.00, 'deposito'=>300.00, 'saldo'=>1150.00, 'estado'=>'activo', 'moneda'=>'USD'],
];
foreach ($contratos as $c) {
    $db->insert('contratos', $c);
}
echo "    OK — " . count($contratos) . " contratos creados\n";

// ─── 6. PASAJEROS ───────────────────────────────────────────────
echo "[6] Creando pasajeros...\n";
$pasajeros = [
    // Contrato 1 — Familia Rodríguez
    ['contrato_id'=>1, 'grupo_id'=>1, 'nombre'=>'Miguel',    'apellido'=>'Rodríguez Torres', 'tipo'=>'lider',  'edad'=>42, 'pasaporte'=>'PE-45678912', 'preferencia_comida'=>'Standard', 'documento_verificado'=>1],
    ['contrato_id'=>1, 'grupo_id'=>1, 'nombre'=>'María',     'apellido'=>'Torres de Rodríguez','tipo'=>'adulto','edad'=>39, 'pasaporte'=>'PE-45678913', 'preferencia_comida'=>'Vegetariano', 'documento_verificado'=>1],
    ['contrato_id'=>1, 'grupo_id'=>1, 'nombre'=>'Sofía',     'apellido'=>'Rodríguez Torres', 'tipo'=>'nino',   'edad'=>12, 'pasaporte'=>'PE-78901234', 'preferencia_comida'=>'Menú Infantil', 'documento_verificado'=>0],
    ['contrato_id'=>1, 'grupo_id'=>1, 'nombre'=>'Diego',     'apellido'=>'Rodríguez Torres', 'tipo'=>'nino',   'edad'=>8,  'pasaporte'=>'PE-78901235', 'preferencia_comida'=>'Menú Infantil', 'documento_verificado'=>0],
    // Contrato 2 — Familia García
    ['contrato_id'=>2, 'grupo_id'=>2, 'nombre'=>'Roberto',   'apellido'=>'García Sánchez',   'tipo'=>'lider',  'edad'=>45, 'pasaporte'=>'PE-32145678', 'preferencia_comida'=>'Standard', 'documento_verificado'=>1],
    ['contrato_id'=>2, 'grupo_id'=>2, 'nombre'=>'Ana',       'apellido'=>'García de Pérez',  'tipo'=>'adulto', 'edad'=>43, 'pasaporte'=>'PE-32145679', 'preferencia_comida'=>'Standard', 'documento_verificado'=>1],
    // Contrato 3 — Familia López
    ['contrato_id'=>3, 'grupo_id'=>3, 'nombre'=>'Luisa',     'apellido'=>'López Ramírez',    'tipo'=>'lider',  'edad'=>38, 'pasaporte'=>'PE-21436587', 'preferencia_comida'=>'Standard', 'documento_verificado'=>1],
    ['contrato_id'=>3, 'grupo_id'=>3, 'nombre'=>'Pedro',     'apellido'=>'López Vargas',     'tipo'=>'adulto', 'edad'=>40, 'pasaporte'=>'PE-21436588', 'preferencia_comida'=>'Standard', 'documento_verificado'=>1],
    ['contrato_id'=>3, 'grupo_id'=>3, 'nombre'=>'Valentina', 'apellido'=>'López',            'tipo'=>'nino',   'edad'=>10, 'pasaporte'=>'PE-87654321', 'preferencia_comida'=>'Menú Infantil', 'documento_verificado'=>0],
    // Contrato 4 — Jane Vargas (CCPA)
    ['contrato_id'=>4, 'grupo_id'=>4, 'nombre'=>'Jane',      'apellido'=>'Vargas Romero',    'tipo'=>'lider',  'edad'=>17, 'pasaporte'=>'PE-78901234', 'preferencia_comida'=>'Standard', 'documento_verificado'=>1],
    ['contrato_id'=>4, 'grupo_id'=>4, 'nombre'=>'Roberto',   'apellido'=>'Vargas',           'tipo'=>'adulto', 'edad'=>45, 'pasaporte'=>'PE-12345679', 'preferencia_comida'=>'Standard', 'documento_verificado'=>1],
    // Contrato 5 — Marco Díaz (CCPA)
    ['contrato_id'=>5, 'grupo_id'=>4, 'nombre'=>'Marco',     'apellido'=>'Díaz Pérez',       'tipo'=>'lider',  'edad'=>17, 'pasaporte'=>'PE-65432198', 'preferencia_comida'=>'Standard', 'documento_verificado'=>1],
    ['contrato_id'=>5, 'grupo_id'=>4, 'nombre'=>'Carmen',    'apellido'=>'Pérez de Díaz',    'tipo'=>'adulto', 'edad'=>42, 'pasaporte'=>'PE-65432199', 'preferencia_comida'=>'Vegetariano', 'documento_verificado'=>1],
    // Contrato 6 — Ana Torres (San Agustín)
    ['contrato_id'=>6, 'grupo_id'=>5, 'nombre'=>'Ana',       'apellido'=>'Torres Medina',    'tipo'=>'lider',  'edad'=>16, 'pasaporte'=>'PE-87654321', 'preferencia_comida'=>'Standard', 'documento_verificado'=>1],
    ['contrato_id'=>6, 'grupo_id'=>5, 'nombre'=>'Luis',      'apellido'=>'Torres Medina',    'tipo'=>'adulto', 'edad'=>44, 'pasaporte'=>'PE-87654322', 'preferencia_comida'=>'Standard', 'documento_verificado'=>0],
];
foreach ($pasajeros as $p) {
    $db->insert('pasajeros', $p);
}
echo "    OK — " . count($pasajeros) . " pasajeros creados\n";

// ─── 7. VUELOS ──────────────────────────────────────────────────
echo "[7] Creando vuelos...\n";
$vuelos = [
    // Contrato 1 — Rodríguez (Pucallpa → Lima → Cancún)
    ['contrato_id'=>1, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2581',  'origen'=>'PCL', 'origen_ciudad'=>'Pucallpa',   'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-07-15 06:30:00', 'fecha_llegada'=>'2026-07-15 07:30:00', 'tipo'=>'ida',      'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    ['contrato_id'=>1, 'aerolinea'=>'Avianca',   'numero_vuelo'=>'AV-452',   'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'CUN', 'destino_ciudad'=>'Cancún',    'fecha_salida'=>'2026-07-15 11:00:00', 'fecha_llegada'=>'2026-07-15 18:30:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Boeing 787',     'estado'=>'confirmado'],
    ['contrato_id'=>1, 'aerolinea'=>'Avianca',   'numero_vuelo'=>'AV-453',   'origen'=>'CUN', 'origen_ciudad'=>'Cancún',     'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-07-22 09:00:00', 'fecha_llegada'=>'2026-07-22 16:00:00', 'tipo'=>'vuelta',   'clase'=>'economica', 'avion'=>'Boeing 787',     'estado'=>'confirmado'],
    ['contrato_id'=>1, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2352',  'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'PCL', 'destino_ciudad'=>'Pucallpa',  'fecha_salida'=>'2026-07-22 19:00:00', 'fecha_llegada'=>'2026-07-22 20:00:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    // Contrato 2 — García (Pucallpa → Lima → Punta Cana)
    ['contrato_id'=>2, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2581',  'origen'=>'PCL', 'origen_ciudad'=>'Pucallpa',   'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-08-10 06:30:00', 'fecha_llegada'=>'2026-08-10 07:30:00', 'tipo'=>'ida',      'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    ['contrato_id'=>2, 'aerolinea'=>'Copa',      'numero_vuelo'=>'CM-802',   'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'PUJ', 'destino_ciudad'=>'Punta Cana','fecha_salida'=>'2026-08-10 10:00:00', 'fecha_llegada'=>'2026-08-10 19:00:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Boeing 737-800', 'estado'=>'confirmado'],
    ['contrato_id'=>2, 'aerolinea'=>'Copa',      'numero_vuelo'=>'CM-803',   'origen'=>'PUJ', 'origen_ciudad'=>'Punta Cana', 'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-08-17 08:00:00', 'fecha_llegada'=>'2026-08-17 17:00:00', 'tipo'=>'vuelta',   'clase'=>'economica', 'avion'=>'Boeing 737-800', 'estado'=>'confirmado'],
    ['contrato_id'=>2, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2352',  'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'PCL', 'destino_ciudad'=>'Pucallpa',  'fecha_salida'=>'2026-08-17 20:00:00', 'fecha_llegada'=>'2026-08-17 21:00:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    // Contrato 3 — López (Pucallpa → Lima → Cusco)
    ['contrato_id'=>3, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2581',  'origen'=>'PCL', 'origen_ciudad'=>'Pucallpa',   'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-06-20 06:30:00', 'fecha_llegada'=>'2026-06-20 07:30:00', 'tipo'=>'ida',      'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    ['contrato_id'=>3, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2041',  'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'CUZ', 'destino_ciudad'=>'Cusco',     'fecha_salida'=>'2026-06-20 09:00:00', 'fecha_llegada'=>'2026-06-20 10:20:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Airbus A319',    'estado'=>'confirmado'],
    ['contrato_id'=>3, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2042',  'origen'=>'CUZ', 'origen_ciudad'=>'Cusco',      'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-06-25 15:00:00', 'fecha_llegada'=>'2026-06-25 16:20:00', 'tipo'=>'vuelta',   'clase'=>'economica', 'avion'=>'Airbus A319',    'estado'=>'confirmado'],
    ['contrato_id'=>3, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2352',  'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'PCL', 'destino_ciudad'=>'Pucallpa',  'fecha_salida'=>'2026-06-25 19:00:00', 'fecha_llegada'=>'2026-06-25 20:00:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    // Contrato 4 — Jane Vargas CCPA (Pucallpa → Lima → Punta Cana)
    ['contrato_id'=>4, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2581',  'origen'=>'PCL', 'origen_ciudad'=>'Pucallpa',   'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-10-26 08:30:00', 'fecha_llegada'=>'2026-10-26 09:30:00', 'tipo'=>'ida',      'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    ['contrato_id'=>4, 'aerolinea'=>'Arajet',    'numero_vuelo'=>'DM-677',   'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'PUJ', 'destino_ciudad'=>'Punta Cana','fecha_salida'=>'2026-10-27 10:00:00', 'fecha_llegada'=>'2026-10-27 18:00:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Boeing 737 MAX', 'estado'=>'confirmado'],
    ['contrato_id'=>4, 'aerolinea'=>'Arajet',    'numero_vuelo'=>'DM-6774',  'origen'=>'PUJ', 'origen_ciudad'=>'Punta Cana', 'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-11-01 14:20:00', 'fecha_llegada'=>'2026-11-01 22:20:00', 'tipo'=>'vuelta',   'clase'=>'economica', 'avion'=>'Boeing 737 MAX', 'estado'=>'confirmado'],
    ['contrato_id'=>4, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2352',  'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'PCL', 'destino_ciudad'=>'Pucallpa',  'fecha_salida'=>'2026-11-02 06:00:00', 'fecha_llegada'=>'2026-11-02 07:00:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    // Contrato 5 — Marco Díaz CCPA (misma ruta)
    ['contrato_id'=>5, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2581',  'origen'=>'PCL', 'origen_ciudad'=>'Pucallpa',   'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-10-26 08:30:00', 'fecha_llegada'=>'2026-10-26 09:30:00', 'tipo'=>'ida',      'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    ['contrato_id'=>5, 'aerolinea'=>'Arajet',    'numero_vuelo'=>'DM-677',   'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'PUJ', 'destino_ciudad'=>'Punta Cana','fecha_salida'=>'2026-10-27 10:00:00', 'fecha_llegada'=>'2026-10-27 18:00:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Boeing 737 MAX', 'estado'=>'confirmado'],
    ['contrato_id'=>5, 'aerolinea'=>'Arajet',    'numero_vuelo'=>'DM-6774',  'origen'=>'PUJ', 'origen_ciudad'=>'Punta Cana', 'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-11-01 14:20:00', 'fecha_llegada'=>'2026-11-01 22:20:00', 'tipo'=>'vuelta',   'clase'=>'economica', 'avion'=>'Boeing 737 MAX', 'estado'=>'confirmado'],
    ['contrato_id'=>5, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2352',  'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'PCL', 'destino_ciudad'=>'Pucallpa',  'fecha_salida'=>'2026-11-02 06:00:00', 'fecha_llegada'=>'2026-11-02 07:00:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    // Contrato 6 — Ana Torres San Agustín (Pucallpa → Lima → Cancún)
    ['contrato_id'=>6, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2581',  'origen'=>'PCL', 'origen_ciudad'=>'Pucallpa',   'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-11-15 06:30:00', 'fecha_llegada'=>'2026-11-15 07:30:00', 'tipo'=>'ida',      'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
    ['contrato_id'=>6, 'aerolinea'=>'Avianca',   'numero_vuelo'=>'AV-452',   'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'CUN', 'destino_ciudad'=>'Cancún',    'fecha_salida'=>'2026-11-15 11:00:00', 'fecha_llegada'=>'2026-11-15 18:30:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Boeing 787',     'estado'=>'confirmado'],
    ['contrato_id'=>6, 'aerolinea'=>'Avianca',   'numero_vuelo'=>'AV-453',   'origen'=>'CUN', 'origen_ciudad'=>'Cancún',     'destino'=>'LIM', 'destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-11-22 09:00:00', 'fecha_llegada'=>'2026-11-22 16:00:00', 'tipo'=>'vuelta',   'clase'=>'economica', 'avion'=>'Boeing 787',     'estado'=>'confirmado'],
    ['contrato_id'=>6, 'aerolinea'=>'LATAM',    'numero_vuelo'=>'LA-2352',  'origen'=>'LIM', 'origen_ciudad'=>'Lima',       'destino'=>'PCL', 'destino_ciudad'=>'Pucallpa',  'fecha_salida'=>'2026-11-22 19:00:00', 'fecha_llegada'=>'2026-11-22 20:00:00', 'tipo'=>'conexion', 'clase'=>'economica', 'avion'=>'Airbus A320',    'estado'=>'confirmado'],
];
foreach ($vuelos as $v) {
    $db->insert('vuelos', $v);
}
echo "    OK — " . count($vuelos) . " vuelos creados\n";

// ─── 8. SERVICIOS ───────────────────────────────────────────────
echo "[8] Creando servicios...\n";
$servicios = [
    // Contrato 1 — Rodríguez (Cancún)
    ['contrato_id'=>1, 'tipo'=>'hotel',      'nombre'=>'Grand Oasis Cancún',               'descripcion'=>'Suite Familiar All Inclusive — 7 noches con vista al mar, piscina privada y kids club.',                     'fecha_inicio'=>'2026-07-15', 'fecha_fin'=>'2026-07-22', 'precio'=>5200.00,  'estado'=>'pendiente'],
    ['contrato_id'=>1, 'tipo'=>'tour',       'nombre'=>'Chichén Itzá + Cenote Ik-Kil',     'descripcion'=>'Tour de día completo con guía en español, almuerzo incluido y nado en cenote sagrado.',                      'fecha_inicio'=>'2026-07-17', 'fecha_fin'=>'2026-07-17', 'precio'=>350.00,   'estado'=>'pendiente'],
    ['contrato_id'=>1, 'tipo'=>'tour',       'nombre'=>'Xcaret Park — Día Completo',        'descripcion'=>'Parque eco-arqueológico con ríos subterráneos, show nocturno México Espectacular y buffet.',                 'fecha_inicio'=>'2026-07-19', 'fecha_fin'=>'2026-07-19', 'precio'=>420.00,   'estado'=>'pendiente'],
    ['contrato_id'=>1, 'tipo'=>'transfer',   'nombre'=>'Traslado Aeropuerto ↔ Hotel',       'descripcion'=>'Transporte privado SUV ida y vuelta: Aeropuerto Cancún — Grand Oasis.',                                     'fecha_inicio'=>'2026-07-15', 'fecha_fin'=>'2026-07-22', 'precio'=>180.00,   'estado'=>'pendiente'],
    ['contrato_id'=>1, 'tipo'=>'seguro',     'nombre'=>'Assist Card Premium',               'descripcion'=>'Cobertura médica USD 60,000 — Equipaje — Cancelación — Repatriación. Póliza #AC-2026-8891.',                'fecha_inicio'=>'2026-07-15', 'fecha_fin'=>'2026-07-22', 'precio'=>280.00,   'estado'=>'pendiente'],
    // Contrato 2 — García (Punta Cana)
    ['contrato_id'=>2, 'tipo'=>'hotel',      'nombre'=>'Hard Rock Hotel & Casino Punta Cana','descripcion'=>'All Inclusive Gold — 7 noches, habitación Swim-Up Suite, spa ilimitado y mini-bar premium.',                  'fecha_inicio'=>'2026-08-10', 'fecha_fin'=>'2026-08-17', 'precio'=>6800.00,  'estado'=>'pagado'],
    ['contrato_id'=>2, 'tipo'=>'tour',       'nombre'=>'Isla Saona — Catamarán VIP',        'descripcion'=>'Excursión en catamarán a la isla paradisíaca, almuerzo en la playa, barra libre y snorkeling.',               'fecha_inicio'=>'2026-08-12', 'fecha_fin'=>'2026-08-12', 'precio'=>120.00,   'estado'=>'pagado'],
    ['contrato_id'=>2, 'tipo'=>'tour',       'nombre'=>'Santo Domingo Colonial Tour',       'descripcion'=>'Recorrido histórico por la Zona Colonial, Alcázar de Colón y Catedral Primada de América.',                  'fecha_inicio'=>'2026-08-14', 'fecha_fin'=>'2026-08-14', 'precio'=>85.00,    'estado'=>'pagado'],
    ['contrato_id'=>2, 'tipo'=>'transfer',   'nombre'=>'Traslado Aeropuerto ↔ Hotel',       'descripcion'=>'Transporte VIP ida y vuelta: Aeropuerto Punta Cana — Hard Rock.',                                            'fecha_inicio'=>'2026-08-10', 'fecha_fin'=>'2026-08-17', 'precio'=>150.00,   'estado'=>'pagado'],
    ['contrato_id'=>2, 'tipo'=>'seguro',     'nombre'=>'Travel Guard Plus',                 'descripcion'=>'Cobertura médica USD 50,000 — Equipaje y cancelación. Póliza #TG-2026-4421.',                                'fecha_inicio'=>'2026-08-10', 'fecha_fin'=>'2026-08-17', 'precio'=>220.00,   'estado'=>'pagado'],
    // Contrato 3 — López (Cusco)
    ['contrato_id'=>3, 'tipo'=>'hotel',      'nombre'=>'Casa Andina Premium Cusco',         'descripcion'=>'Habitación Superior — 5 noches con desayuno buffet, ubicación en Plaza de Armas.',                           'fecha_inicio'=>'2026-06-20', 'fecha_fin'=>'2026-06-25', 'precio'=>1200.00,  'estado'=>'pendiente'],
    ['contrato_id'=>3, 'tipo'=>'tour',       'nombre'=>'Valle Sagrado de los Incas',        'descripcion'=>'Día completo: Pisac, Ollantaytambo, mercado artesanal y almuerzo buffet.',                                    'fecha_inicio'=>'2026-06-21', 'fecha_fin'=>'2026-06-21', 'precio'=>180.00,   'estado'=>'pendiente'],
    ['contrato_id'=>3, 'tipo'=>'tour',       'nombre'=>'Machu Picchu — Tren Expedition',    'descripcion'=>'Viaje en tren Ollantaytambo – Aguas Calientes, entrada a Machu Picchu con guía.',                             'fecha_inicio'=>'2026-06-22', 'fecha_fin'=>'2026-06-22', 'precio'=>450.00,   'estado'=>'pendiente'],
    ['contrato_id'=>3, 'tipo'=>'tour',       'nombre'=>'Montaña de 7 Colores (Vinicunca)',  'descripcion'=>'Trekking guiado con desayuno y almuerzo incluido. Altitud: 5,200 m.s.n.m.',                                   'fecha_inicio'=>'2026-06-23', 'fecha_fin'=>'2026-06-23', 'precio'=>120.00,   'estado'=>'pendiente'],
    ['contrato_id'=>3, 'tipo'=>'seguro',     'nombre'=>'Assist Card Básico',                'descripcion'=>'Cobertura médica USD 30,000 — Equipaje. Póliza #AC-2026-CUSCO.',                                             'fecha_inicio'=>'2026-06-20', 'fecha_fin'=>'2026-06-25', 'precio'=>150.00,   'estado'=>'pendiente'],
    // Contrato 4 — Jane Vargas CCPA
    ['contrato_id'=>4, 'tipo'=>'hotel',      'nombre'=>'Catalonia Bávaro Beach 5★',         'descripcion'=>'Resort & Spa All Inclusive — 6 noches frente al mar, animación y actividades acuáticas.',                     'fecha_inicio'=>'2026-10-27', 'fecha_fin'=>'2026-11-01', 'precio'=>800.00,   'estado'=>'pendiente'],
    ['contrato_id'=>4, 'tipo'=>'actividad',  'nombre'=>'Zip Line Mega Splash',              'descripcion'=>'Tirolesa sobre la laguna — Splash of Emotions, 2 horas de adrenalina pura.',                                 'fecha_inicio'=>'2026-10-28', 'fecha_fin'=>'2026-10-28', 'precio'=>85.00,    'estado'=>'pendiente'],
    ['contrato_id'=>4, 'tipo'=>'tour',       'nombre'=>'Isla Saona — Tour Escolar',         'descripcion'=>'Excursión grupal en catamarán, snorkeling y almuerzo en playa, incluye Colonial Santo Domingo opcional.',     'fecha_inicio'=>'2026-10-29', 'fecha_fin'=>'2026-10-29', 'precio'=>95.00,    'estado'=>'pendiente'],
    ['contrato_id'=>4, 'tipo'=>'seguro',     'nombre'=>'Travel Guard Escolar',              'descripcion'=>'Cobertura grupal médica USD 40,000 — Equipaje — Cancelación. Póliza #TG-SCH-2026.',                           'fecha_inicio'=>'2026-10-26', 'fecha_fin'=>'2026-11-02', 'precio'=>120.00,   'estado'=>'pendiente'],
    // Contrato 5 — Marco Díaz CCPA (mismos servicios)
    ['contrato_id'=>5, 'tipo'=>'hotel',      'nombre'=>'Catalonia Bávaro Beach 5★',         'descripcion'=>'Resort & Spa All Inclusive — 6 noches frente al mar.',                                                        'fecha_inicio'=>'2026-10-27', 'fecha_fin'=>'2026-11-01', 'precio'=>800.00,   'estado'=>'pendiente'],
    ['contrato_id'=>5, 'tipo'=>'actividad',  'nombre'=>'Zip Line Mega Splash',              'descripcion'=>'Tirolesa sobre la laguna — Splash of Emotions.',                                                              'fecha_inicio'=>'2026-10-28', 'fecha_fin'=>'2026-10-28', 'precio'=>85.00,    'estado'=>'pendiente'],
    ['contrato_id'=>5, 'tipo'=>'tour',       'nombre'=>'Isla Saona — Tour Escolar',         'descripcion'=>'Excursión grupal en catamarán, snorkeling y almuerzo.',                                                        'fecha_inicio'=>'2026-10-29', 'fecha_fin'=>'2026-10-29', 'precio'=>95.00,    'estado'=>'pendiente'],
    ['contrato_id'=>5, 'tipo'=>'seguro',     'nombre'=>'Travel Guard Escolar',              'descripcion'=>'Cobertura grupal médica USD 40,000. Póliza #TG-SCH-2026.',                                                    'fecha_inicio'=>'2026-10-26', 'fecha_fin'=>'2026-11-02', 'precio'=>120.00,   'estado'=>'pendiente'],
    // Contrato 6 — Ana Torres San Agustín
    ['contrato_id'=>6, 'tipo'=>'hotel',      'nombre'=>'Grand Oasis Cancún',                'descripcion'=>'All Inclusive — 7 noches habitación standard, piscina y restaurantes.',                                        'fecha_inicio'=>'2026-11-15', 'fecha_fin'=>'2026-11-22', 'precio'=>680.00,   'estado'=>'pendiente'],
    ['contrato_id'=>6, 'tipo'=>'tour',       'nombre'=>'Chichén Itzá — Tour Escolar',       'descripcion'=>'Excursión educativa con guía bilingüe, cenote y almuerzo buffet.',                                             'fecha_inicio'=>'2026-11-17', 'fecha_fin'=>'2026-11-17', 'precio'=>90.00,    'estado'=>'pendiente'],
    ['contrato_id'=>6, 'tipo'=>'seguro',     'nombre'=>'Assist Card Escolar',               'descripcion'=>'Cobertura grupal médica USD 35,000. Póliza #AC-SCH-2026.',                                                    'fecha_inicio'=>'2026-11-15', 'fecha_fin'=>'2026-11-22', 'precio'=>110.00,   'estado'=>'pendiente'],
];
foreach ($servicios as $s) {
    $db->insert('servicios', $s);
}
echo "    OK — " . count($servicios) . " servicios creados\n";

// ─── 9. PAGOS ───────────────────────────────────────────────────
echo "[9] Creando pagos...\n";
$pagos = [
    // Contrato 1 — Rodríguez (cuotas)
    ['contrato_id'=>1, 'concepto'=>'Depósito Inicial',   'monto'=>3000.00,  'fecha_vencimiento'=>'2026-01-15', 'fecha_pago'=>'2026-01-15', 'estado'=>'aprobado',  'metodo_pago'=>'Transferencia bancaria'],
    ['contrato_id'=>1, 'concepto'=>'Cuota 1 — Febrero',  'monto'=>1583.33,  'fecha_vencimiento'=>'2026-02-28', 'fecha_pago'=>'2026-02-25', 'estado'=>'aprobado',  'metodo_pago'=>'Transferencia bancaria'],
    ['contrato_id'=>1, 'concepto'=>'Cuota 2 — Marzo',    'monto'=>1583.33,  'fecha_vencimiento'=>'2026-03-31', 'fecha_pago'=>'2026-03-28', 'estado'=>'aprobado',  'metodo_pago'=>'Yape'],
    ['contrato_id'=>1, 'concepto'=>'Cuota 3 — Abril',    'monto'=>1583.33,  'fecha_vencimiento'=>'2026-04-30', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>1, 'concepto'=>'Cuota 4 — Mayo',     'monto'=>1583.33,  'fecha_vencimiento'=>'2026-05-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>1, 'concepto'=>'Cuota 5 — Junio',    'monto'=>1583.35,  'fecha_vencimiento'=>'2026-06-30', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    // Contrato 2 — García (contado, todo pagado)
    ['contrato_id'=>2, 'concepto'=>'Pago Total Contado',  'monto'=>15000.00, 'fecha_vencimiento'=>'2026-07-01', 'fecha_pago'=>'2026-07-01', 'estado'=>'aprobado',  'metodo_pago'=>'Transferencia bancaria'],
    // Contrato 3 — López (cuotas)
    ['contrato_id'=>3, 'concepto'=>'Depósito Inicial',   'monto'=>1500.00,  'fecha_vencimiento'=>'2026-03-15', 'fecha_pago'=>'2026-03-15', 'estado'=>'aprobado',  'metodo_pago'=>'Depósito bancario'],
    ['contrato_id'=>3, 'concepto'=>'Cuota 1 — Abril',    'monto'=>1000.00,  'fecha_vencimiento'=>'2026-04-30', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>3, 'concepto'=>'Cuota 2 — Mayo',     'monto'=>1000.00,  'fecha_vencimiento'=>'2026-05-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>3, 'concepto'=>'Cuota 3 — Junio',    'monto'=>1000.00,  'fecha_vencimiento'=>'2026-06-15', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    // Contrato 4 — Jane Vargas CCPA (cuotas)
    ['contrato_id'=>4, 'concepto'=>'Prepago Confirmado',  'monto'=>350.00,   'fecha_vencimiento'=>'2026-01-15', 'fecha_pago'=>'2026-01-15', 'estado'=>'aprobado',  'metodo_pago'=>'Transferencia'],
    ['contrato_id'=>4, 'concepto'=>'Cuota 1 — Abril',    'monto'=>199.83,   'fecha_vencimiento'=>'2026-04-30', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>4, 'concepto'=>'Cuota 2 — Mayo',     'monto'=>199.83,   'fecha_vencimiento'=>'2026-05-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>4, 'concepto'=>'Cuota 3 — Junio',    'monto'=>199.83,   'fecha_vencimiento'=>'2026-06-30', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>4, 'concepto'=>'Cuota 4 — Julio',    'monto'=>199.83,   'fecha_vencimiento'=>'2026-07-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>4, 'concepto'=>'Cuota 5 — Agosto',   'monto'=>199.85,   'fecha_vencimiento'=>'2026-08-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    // Contrato 5 — Marco Díaz CCPA (cuotas)
    ['contrato_id'=>5, 'concepto'=>'Prepago Confirmado',  'monto'=>350.00,   'fecha_vencimiento'=>'2026-01-20', 'fecha_pago'=>'2026-01-20', 'estado'=>'aprobado',  'metodo_pago'=>'Yape'],
    ['contrato_id'=>5, 'concepto'=>'Cuota 1 — Abril',    'monto'=>199.83,   'fecha_vencimiento'=>'2026-04-30', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>5, 'concepto'=>'Cuota 2 — Mayo',     'monto'=>199.83,   'fecha_vencimiento'=>'2026-05-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>5, 'concepto'=>'Cuota 3 — Junio',    'monto'=>199.83,   'fecha_vencimiento'=>'2026-06-30', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>5, 'concepto'=>'Cuota 4 — Julio',    'monto'=>199.83,   'fecha_vencimiento'=>'2026-07-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>5, 'concepto'=>'Cuota 5 — Agosto',   'monto'=>199.85,   'fecha_vencimiento'=>'2026-08-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    // Contrato 6 — Ana Torres San Agustín (cuotas)
    ['contrato_id'=>6, 'concepto'=>'Prepago Confirmado',  'monto'=>300.00,   'fecha_vencimiento'=>'2026-04-15', 'fecha_pago'=>'2026-04-15', 'estado'=>'aprobado',  'metodo_pago'=>'Depósito bancario'],
    ['contrato_id'=>6, 'concepto'=>'Cuota 1 — Mayo',     'monto'=>191.67,   'fecha_vencimiento'=>'2026-05-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>6, 'concepto'=>'Cuota 2 — Junio',    'monto'=>191.67,   'fecha_vencimiento'=>'2026-06-30', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>6, 'concepto'=>'Cuota 3 — Julio',    'monto'=>191.67,   'fecha_vencimiento'=>'2026-07-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>6, 'concepto'=>'Cuota 4 — Agosto',   'monto'=>191.67,   'fecha_vencimiento'=>'2026-08-31', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
    ['contrato_id'=>6, 'concepto'=>'Cuota 5 — Septiembre','monto'=>191.65,   'fecha_vencimiento'=>'2026-09-30', 'fecha_pago'=>null,          'estado'=>'pendiente', 'metodo_pago'=>null],
];
foreach ($pagos as $p) {
    $db->insert('pagos', $p);
}
echo "    OK — " . count($pagos) . " pagos creados\n";

// ─── 10. SERVICIOS GRUPO ────────────────────────────────────────
echo "[10] Creando servicios de grupo...\n";
$sgList = [
    // Grupo 1 — Rodríguez Cancún
    ['grupo_id'=>1, 'servicio_tipo'=>'hotel',       'detalle_json'=>json_encode(['nombre'=>'Grand Oasis Cancún','tipo_habitacion'=>'Suite Familiar','noches'=>7,'regimen'=>'All Inclusive'])],
    ['grupo_id'=>1, 'servicio_tipo'=>'vuelos',      'detalle_json'=>json_encode(['vuelos'=>[['aerolinea'=>'LATAM','numero'=>'LA-2581','ruta'=>'PCL - LIM','salida'=>'2026-07-15T06:30'],['aerolinea'=>'Avianca','numero'=>'AV-452','ruta'=>'LIM - CUN','salida'=>'2026-07-15T11:00']]])],
    ['grupo_id'=>1, 'servicio_tipo'=>'traslados',   'detalle_json'=>json_encode(['tipo'=>'ambos','detalle'=>'Aeropuerto Cancún ↔ Grand Oasis — SUV Privado'])],
    ['grupo_id'=>1, 'servicio_tipo'=>'excursiones', 'detalle_json'=>json_encode(['items'=>[['nombre'=>'Chichén Itzá + Cenote','fecha'=>'2026-07-17','costo'=>350],['nombre'=>'Xcaret Park','fecha'=>'2026-07-19','costo'=>420]]])],
    ['grupo_id'=>1, 'servicio_tipo'=>'seguro',      'detalle_json'=>json_encode(['nombre'=>'Assist Card Premium','poliza'=>'AC-2026-8891','cobertura'=>'USD 60,000'])],
    // Grupo 2 — García Punta Cana
    ['grupo_id'=>2, 'servicio_tipo'=>'hotel',       'detalle_json'=>json_encode(['nombre'=>'Hard Rock Hotel Punta Cana','tipo_habitacion'=>'Swim-Up Suite','noches'=>7,'regimen'=>'All Inclusive Gold'])],
    ['grupo_id'=>2, 'servicio_tipo'=>'vuelos',      'detalle_json'=>json_encode(['vuelos'=>[['aerolinea'=>'LATAM','numero'=>'LA-2581','ruta'=>'PCL - LIM'],['aerolinea'=>'Copa','numero'=>'CM-802','ruta'=>'LIM - PUJ']]])],
    ['grupo_id'=>2, 'servicio_tipo'=>'excursiones', 'detalle_json'=>json_encode(['items'=>[['nombre'=>'Isla Saona VIP','fecha'=>'2026-08-12','costo'=>120],['nombre'=>'Santo Domingo Colonial','fecha'=>'2026-08-14','costo'=>85]]])],
    ['grupo_id'=>2, 'servicio_tipo'=>'seguro',      'detalle_json'=>json_encode(['nombre'=>'Travel Guard Plus','poliza'=>'TG-2026-4421','cobertura'=>'USD 50,000'])],
    // Grupo 3 — López Cusco
    ['grupo_id'=>3, 'servicio_tipo'=>'hotel',       'detalle_json'=>json_encode(['nombre'=>'Casa Andina Premium Cusco','tipo_habitacion'=>'Superior','noches'=>5,'regimen'=>'Desayuno Buffet'])],
    ['grupo_id'=>3, 'servicio_tipo'=>'excursiones', 'detalle_json'=>json_encode(['items'=>[['nombre'=>'Valle Sagrado','fecha'=>'2026-06-21','costo'=>180],['nombre'=>'Machu Picchu','fecha'=>'2026-06-22','costo'=>450],['nombre'=>'Montaña 7 Colores','fecha'=>'2026-06-23','costo'=>120]]])],
    ['grupo_id'=>3, 'servicio_tipo'=>'seguro',      'detalle_json'=>json_encode(['nombre'=>'Assist Card Básico','poliza'=>'AC-2026-CUSCO','cobertura'=>'USD 30,000'])],
    // Grupo 4 — CCPA Punta Cana
    ['grupo_id'=>4, 'servicio_tipo'=>'hotel',       'detalle_json'=>json_encode(['nombre'=>'Catalonia Bávaro Beach 5★','tipo_habitacion'=>'Standard Triple','noches'=>6,'regimen'=>'All Inclusive'])],
    ['grupo_id'=>4, 'servicio_tipo'=>'vuelos',      'detalle_json'=>json_encode(['vuelos'=>[['aerolinea'=>'LATAM','numero'=>'LA-2581','ruta'=>'PCL - LIM'],['aerolinea'=>'Arajet','numero'=>'DM-677','ruta'=>'LIM - PUJ']]])],
    ['grupo_id'=>4, 'servicio_tipo'=>'traslados',   'detalle_json'=>json_encode(['tipo'=>'ambos','detalle'=>'Transporte grupal Aeropuerto ↔ Hotel'])],
    ['grupo_id'=>4, 'servicio_tipo'=>'excursiones', 'detalle_json'=>json_encode(['items'=>[['nombre'=>'Isla Saona Grupal','fecha'=>'2026-10-29','costo'=>75],['nombre'=>'Zip Line Mega Splash','fecha'=>'2026-10-28','costo'=>85]]])],
    ['grupo_id'=>4, 'servicio_tipo'=>'seguro',      'detalle_json'=>json_encode(['nombre'=>'Travel Guard Escolar','poliza'=>'TG-SCH-2026','cobertura'=>'USD 40,000'])],
    // Grupo 5 — San Agustín Cancún
    ['grupo_id'=>5, 'servicio_tipo'=>'hotel',       'detalle_json'=>json_encode(['nombre'=>'Grand Oasis Cancún','tipo_habitacion'=>'Standard Doble','noches'=>7,'regimen'=>'All Inclusive'])],
    ['grupo_id'=>5, 'servicio_tipo'=>'vuelos',      'detalle_json'=>json_encode(['vuelos'=>[['aerolinea'=>'LATAM','numero'=>'LA-2581','ruta'=>'PCL - LIM'],['aerolinea'=>'Avianca','numero'=>'AV-452','ruta'=>'LIM - CUN']]])],
    ['grupo_id'=>5, 'servicio_tipo'=>'excursiones', 'detalle_json'=>json_encode(['items'=>[['nombre'=>'Chichén Itzá Tour Escolar','fecha'=>'2026-11-17','costo'=>90]]])],
    ['grupo_id'=>5, 'servicio_tipo'=>'seguro',      'detalle_json'=>json_encode(['nombre'=>'Assist Card Escolar','poliza'=>'AC-SCH-2026','cobertura'=>'USD 35,000'])],
];
foreach ($sgList as $sg) {
    $db->insert('servicios_grupo', $sg);
}
echo "    OK — " . count($sgList) . " servicios de grupo creados\n";

// ─── 11. PROMOCIONES ────────────────────────────────────────────
echo "[11] Creando promociones...\n";
$promos = [
    ['titulo'=>'Cancún Familiar — 20% OFF', 'descripcion'=>'Descubre la magia de Cancún con paquetes familiares all-inclusive. Incluye excursiones a Chichén Itzá y Xcaret. Oferta válida para reservas hasta julio 2026.', 'destino'=>'Cancún, México', 'descuento'=>'20%', 'fecha_inicio'=>'2026-03-01', 'fecha_fin'=>'2026-07-01', 'activa'=>1],
    ['titulo'=>'Punta Cana All Inclusive',   'descripcion'=>'Resort 5 estrellas frente al mar caribeño. Spa, deportes acuáticos y excursiones incluidas. Hard Rock Hotel & Casino a precios inigualables.', 'destino'=>'Punta Cana', 'descuento'=>'15%', 'fecha_inicio'=>'2026-04-01', 'fecha_fin'=>'2026-08-01', 'activa'=>1],
    ['titulo'=>'Cusco Imperial — Early Bird','descripcion'=>'Reserva anticipada: Valle Sagrado, Machu Picchu y Montaña de 7 Colores. Hotel premium en Plaza de Armas incluido.', 'destino'=>'Cusco, Perú', 'descuento'=>'10%', 'fecha_inicio'=>'2026-02-01', 'fecha_fin'=>'2026-05-31', 'activa'=>1],
    ['titulo'=>'Promo Escolar 2026',         'descripcion'=>'Paquetes exclusivos para colegios: Punta Cana y Cancún. Financiamiento en cuotas mensuales, seguro grupal incluido.', 'destino'=>'Internacional', 'descuento'=>'Cuotas sin interés', 'fecha_inicio'=>'2026-01-01', 'fecha_fin'=>'2026-09-30', 'activa'=>1],
];
foreach ($promos as $p) {
    $db->insert('promociones', $p);
}
echo "    OK — " . count($promos) . " promociones creadas\n";

// ─── 12. NOTIFICACIONES ─────────────────────────────────────────
echo "[12] Creando notificaciones...\n";
$notifs = [
    ['usuario_id'=>2, 'titulo'=>'Recordatorio de Pago',        'mensaje'=>'Tu Cuota 3 — Abril de $1,583.33 vence el 30 de abril. No olvides realizar tu pago a tiempo.', 'tipo'=>'advertencia', 'leida'=>0],
    ['usuario_id'=>2, 'titulo'=>'Vuelos Confirmados',          'mensaje'=>'Tus vuelos a Cancún han sido confirmados. Revisa los detalles en tu panel.', 'tipo'=>'info', 'leida'=>0],
    ['usuario_id'=>3, 'titulo'=>'Pago Total Recibido',         'mensaje'=>'Hemos recibido tu pago total de $15,000.00. Tu viaje a Punta Cana está 100% confirmado.', 'tipo'=>'exito', 'leida'=>0],
    ['usuario_id'=>6, 'titulo'=>'Cuota Pendiente',             'mensaje'=>'Tu Cuota 1 de $199.83 vence el 30 de abril. Realiza tu pago para mantener tu reservación.', 'tipo'=>'advertencia', 'leida'=>0],
];
foreach ($notifs as $n) {
    $db->insert('notificaciones', $n);
}
echo "    OK — " . count($notifs) . " notificaciones creadas\n";

// ─── RESUMEN ────────────────────────────────────────────────────
echo "\n=== SEED COMPLETADO ===\n\n";
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│  CREDENCIALES DE ACCESO                                        │\n";
echo "├─────────────────────┬──────────────────┬───────────────────────┤\n";
echo "│  Código             │  Contraseña      │  Rol                  │\n";
echo "├─────────────────────┼──────────────────┼───────────────────────┤\n";
echo "│  admin              │  admin123        │  Administrador        │\n";
echo "│  AV-2026-001        │  cliente123      │  Fam. Rodríguez       │\n";
echo "│  AV-2026-002        │  cliente123      │  Fam. García          │\n";
echo "│  AV-2026-003        │  cliente123      │  Fam. López           │\n";
echo "│  CCPA-2026-001      │  cliente123      │  Jane Vargas (CCPA)   │\n";
echo "│  CCPA-2026-002      │  cliente123      │  Marco Díaz (CCPA)    │\n";
echo "│  SA-2026-001        │  cliente123      │  Ana Torres (SA)      │\n";
echo "│  REP-CCPA-001       │  cliente123      │  Representante CCPA   │\n";
echo "│  REP-SA-001         │  cliente123      │  Representante SA     │\n";
echo "└─────────────────────┴──────────────────┴───────────────────────┘\n\n";

echo "Totales: " . count($usuarios) . " usuarios, " . count($clientes) . " clientes, " . count($grupos) . " grupos, ";
echo count($contratos) . " contratos, " . count($pasajeros) . " pasajeros, " . count($vuelos) . " vuelos, ";
echo count($servicios) . " servicios, " . count($pagos) . " pagos\n";
