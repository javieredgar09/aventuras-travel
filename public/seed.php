<?php
/**
 * seed.php — http://localhost/aventuras/public/seed.php
 * Limpia la BD y crea datos realistas siguiendo el flujo real del sistema.
 * ELIMINAR EN PRODUCCIÓN.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');

echo "<pre style='font-family:Consolas,monospace;background:#0f172a;color:#22d3ee;padding:24px;font-size:13px;line-height:1.6;'>";
echo "╔═══════════════════════════════════════════════════════╗\n";
echo "║   AVENTURAS TRAVEL — SEED COMPLETO                   ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=aventuras;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("❌ Error de conexión: " . htmlspecialchars($e->getMessage()));
}

// ─── Helper: insertar fila, ignorar columnas que no existen ─────
function ins(PDO $pdo, string $table, array $data): int {
    static $tableColumns = [];
    if (!isset($tableColumns[$table])) {
        $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
        $tableColumns[$table] = array_map(fn($r) => $r['Field'], $stmt->fetchAll());
    }
    $filtered = array_intersect_key($data, array_flip($tableColumns[$table]));
    if (empty($filtered)) return 0;
    $cols = implode(',', array_map(fn($c) => "`$c`", array_keys($filtered)));
    $phs  = implode(',', array_fill(0, count($filtered), '?'));
    $stmt = $pdo->prepare("INSERT INTO `{$table}` ({$cols}) VALUES ({$phs})");
    $stmt->execute(array_values($filtered));
    return (int)$pdo->lastInsertId();
}

// ═══════════════════════════════════════════════════════════════
// 1. TRUNCAR TODAS LAS TABLAS
// ═══════════════════════════════════════════════════════════════
echo "◆ Limpiando base de datos...\n";
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
foreach (['notificaciones','archivos','comprobantes','plan_cuotas','vouchers',
          'pagos','servicios','vuelos','pasajeros','contratos','clientes',
          'servicios_grupo','grupos','promociones','usuarios'] as $t) {
    try { $pdo->exec("TRUNCATE TABLE `{$t}`"); } catch (Exception $e) {
        try { $pdo->exec("DELETE FROM `{$t}`"); } catch (Exception $e2) {}
    }
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "  ✅ Tablas limpias\n\n";

// ═══════════════════════════════════════════════════════════════
// 2. HASHES VÁLIDOS
// ═══════════════════════════════════════════════════════════════
$hAdmin  = password_hash('admin123', PASSWORD_DEFAULT);
$hClient = password_hash('cliente123', PASSWORD_DEFAULT);
echo "◆ Hashes generados:\n";
echo "  admin123   → " . strlen($hAdmin) . " chars | verify=" . (password_verify('admin123',$hAdmin)?'✅':'❌') . "\n";
echo "  cliente123 → " . strlen($hClient) . " chars | verify=" . (password_verify('cliente123',$hClient)?'✅':'❌') . "\n\n";

// ═══════════════════════════════════════════════════════════════
// 3. USUARIO ADMIN (id=1)
// ═══════════════════════════════════════════════════════════════
echo "◆ Creando admin...\n";
$adminId = ins($pdo, 'usuarios', [
    'codigo'=>'admin', 'password'=>$hAdmin, 'nombre'=>'Carlos', 'apellido'=>'Mendoza Ríos',
    'email'=>'admin@aventurastravel.pe', 'telefono'=>'+51-961-555-100', 'rol'=>'admin', 'activo'=>1
]);
echo "  ✅ Admin id={$adminId} codigo=admin\n\n";

// ═══════════════════════════════════════════════════════════════
// 4. GRUPO FAMILIAR 1: Rodríguez → Cancún
// ═══════════════════════════════════════════════════════════════
echo "◆ GRUPO FAMILIAR 1: Familia Rodríguez → Cancún\n";

// 4a. Crear usuario para el pasajero principal (titular)
$uid_rod = ins($pdo, 'usuarios', [
    'codigo'=>'AV-2026-001', 'password'=>$hClient, 'nombre'=>'Miguel', 'apellido'=>'Rodríguez Torres',
    'email'=>'miguel.rodriguez@email.com', 'telefono'=>'+51-961-200-001', 'rol'=>'cliente_familiar', 'activo'=>1
]);
$cid_rod = ins($pdo, 'clientes', ['usuario_id'=>$uid_rod, 'tipo'=>'familiar', 'direccion'=>'Jr. Ucayali 234', 'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'45678912']);
echo "  → Usuario id={$uid_rod} codigo=AV-2026-001 | Cliente id={$cid_rod}\n";

// 4b. Crear grupo
$gid_rod = ins($pdo, 'grupos', [
    'nombre'=>'Familia Rodríguez - Cancún 2026', 'tipo'=>'familiar', 'operador'=>'Avianca Tours',
    'destino'=>'Cancún, México', 'fecha_viaje'=>'2026-07-15', 'fecha_retorno'=>'2026-07-22',
    'valor_total'=>12500.00, 'deposito'=>3000.00, 'tipo_pago'=>'cuotas', 'total_cuotas'=>6,
    'meses_pago'=>'Enero,Febrero,Marzo,Abril,Mayo,Junio', 'estado'=>'activo',
    'representante_id'=>$uid_rod, 'cantidad_pasajeros'=>4
]);
echo "  → Grupo id={$gid_rod}\n";

// 4c. Servicios del grupo (itinerario)
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_rod,'servicio_tipo'=>'hotel','detalle_json'=>json_encode(['nombre'=>'Grand Oasis Cancún','tipo_habitacion'=>'Suite Familiar','noches'=>7,'regimen'=>'All Inclusive'])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_rod,'servicio_tipo'=>'vuelos','detalle_json'=>json_encode(['vuelos'=>[
    ['aerolinea'=>'LATAM','numero'=>'LA-2581','ruta'=>'PCL - LIM','salida'=>'2026-07-15T06:30','llegada'=>'2026-07-15T07:30','origen'=>'PCL','destino'=>'LIM','origen_ciudad'=>'Pucallpa','destino_ciudad'=>'Lima'],
    ['aerolinea'=>'Avianca','numero'=>'AV-452','ruta'=>'LIM - CUN','salida'=>'2026-07-15T11:00','llegada'=>'2026-07-15T18:30','origen'=>'LIM','destino'=>'CUN','origen_ciudad'=>'Lima','destino_ciudad'=>'Cancún'],
    ['aerolinea'=>'Avianca','numero'=>'AV-453','ruta'=>'CUN - LIM','salida'=>'2026-07-22T09:00','llegada'=>'2026-07-22T16:00','origen'=>'CUN','destino'=>'LIM','origen_ciudad'=>'Cancún','destino_ciudad'=>'Lima'],
    ['aerolinea'=>'LATAM','numero'=>'LA-2352','ruta'=>'LIM - PCL','salida'=>'2026-07-22T19:00','llegada'=>'2026-07-22T20:00','origen'=>'LIM','destino'=>'PCL','origen_ciudad'=>'Lima','destino_ciudad'=>'Pucallpa']
]])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_rod,'servicio_tipo'=>'traslados','detalle_json'=>json_encode(['tipo'=>'ambos','detalle'=>'Aeropuerto Cancún ↔ Grand Oasis — SUV Privado'])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_rod,'servicio_tipo'=>'excursiones','detalle_json'=>json_encode(['items'=>[['nombre'=>'Chichén Itzá + Cenote Ik-Kil','fecha'=>'2026-07-17','costo'=>350],['nombre'=>'Xcaret Park — Día Completo','fecha'=>'2026-07-19','costo'=>420]]])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_rod,'servicio_tipo'=>'seguro','detalle_json'=>json_encode(['nombre'=>'Assist Card Premium','poliza'=>'AC-2026-8891','cobertura'=>'USD 60,000'])]);
echo "  → 5 servicios_grupo (itinerario)\n";

// 4d. Contrato familiar vinculado al cliente
$ctid_rod = ins($pdo, 'contratos', [
    'codigo'=>'AV-2026-001', 'cliente_id'=>$cid_rod, 'grupo_id'=>$gid_rod, 'tipo'=>'familiar',
    'destino'=>'Cancún, México', 'hotel'=>'Grand Oasis Cancún - Suite Familiar',
    'descripcion'=>'Paquete familiar all-inclusive Cancún con excursiones a Chichén Itzá y Xcaret.',
    'fecha_salida'=>'2026-07-15', 'fecha_retorno'=>'2026-07-22',
    'valor_total'=>12500.00, 'deposito'=>3000.00, 'saldo'=>9500.00, 'estado'=>'activo',
    'fecha_firma'=>'2026-01-10',
    'titular_nombre'=>'Miguel Rodríguez Torres', 'titular_correo'=>'miguel.rodriguez@email.com', 'titular_telefono'=>'+51-961-200-001',
    'total_cuotas'=>6, 'meses_pago'=>'Enero,Febrero,Marzo,Abril,Mayo,Junio', 'tipo_pago'=>'cuotas'
]);
echo "  → Contrato id={$ctid_rod} codigo=AV-2026-001\n";

// 4e. Pasajeros (vinculados a contrato Y grupo)
ins($pdo,'pasajeros',['contrato_id'=>$ctid_rod,'grupo_id'=>$gid_rod,'nombre'=>'Miguel',   'apellido'=>'Rodríguez Torres',   'tipo'=>'adulto','edad'=>42,'pasaporte'=>'PE-45678912','documento_verificado'=>1]);
ins($pdo,'pasajeros',['contrato_id'=>$ctid_rod,'grupo_id'=>$gid_rod,'nombre'=>'María',    'apellido'=>'Torres de Rodríguez','tipo'=>'adulto','edad'=>39,'pasaporte'=>'PE-45678913','documento_verificado'=>1]);
ins($pdo,'pasajeros',['contrato_id'=>$ctid_rod,'grupo_id'=>$gid_rod,'nombre'=>'Sofía',    'apellido'=>'Rodríguez Torres',   'tipo'=>'nino',  'edad'=>12,'pasaporte'=>'PE-78901234','documento_verificado'=>0]);
ins($pdo,'pasajeros',['contrato_id'=>$ctid_rod,'grupo_id'=>$gid_rod,'nombre'=>'Diego',    'apellido'=>'Rodríguez Torres',   'tipo'=>'nino',  'edad'=>8, 'pasaporte'=>'PE-78901235','documento_verificado'=>0]);
echo "  → 4 pasajeros\n";

// 4f. Vuelos del contrato
ins($pdo,'vuelos',['contrato_id'=>$ctid_rod,'aerolinea'=>'LATAM',  'numero_vuelo'=>'LA-2581','origen'=>'PCL','origen_ciudad'=>'Pucallpa','destino'=>'LIM','destino_ciudad'=>'Lima',    'fecha_salida'=>'2026-07-15 06:30:00','fecha_llegada'=>'2026-07-15 07:30:00','tipo'=>'ida',     'clase'=>'economica','avion'=>'Airbus A320',   'estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_rod,'aerolinea'=>'Avianca','numero_vuelo'=>'AV-452', 'origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'CUN','destino_ciudad'=>'Cancún',  'fecha_salida'=>'2026-07-15 11:00:00','fecha_llegada'=>'2026-07-15 18:30:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Boeing 787',    'estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_rod,'aerolinea'=>'Avianca','numero_vuelo'=>'AV-453', 'origen'=>'CUN','origen_ciudad'=>'Cancún',  'destino'=>'LIM','destino_ciudad'=>'Lima',    'fecha_salida'=>'2026-07-22 09:00:00','fecha_llegada'=>'2026-07-22 16:00:00','tipo'=>'vuelta',  'clase'=>'economica','avion'=>'Boeing 787',    'estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_rod,'aerolinea'=>'LATAM',  'numero_vuelo'=>'LA-2352','origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'PCL','destino_ciudad'=>'Pucallpa','fecha_salida'=>'2026-07-22 19:00:00','fecha_llegada'=>'2026-07-22 20:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Airbus A320',   'estado'=>'confirmado']);
echo "  → 4 vuelos\n";

// 4g. Servicios del contrato
ins($pdo,'servicios',['contrato_id'=>$ctid_rod,'tipo'=>'hotel',   'nombre'=>'Grand Oasis Cancún',          'descripcion'=>'Suite Familiar All Inclusive — 7 noches con vista al mar, piscina privada y kids club.','fecha_inicio'=>'2026-07-15','fecha_fin'=>'2026-07-22','precio'=>5200.00,'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_rod,'tipo'=>'tour',    'nombre'=>'Chichén Itzá + Cenote Ik-Kil','descripcion'=>'Tour de día completo con guía en español, almuerzo incluido y nado en cenote sagrado.','fecha_inicio'=>'2026-07-17','fecha_fin'=>'2026-07-17','precio'=>350.00,'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_rod,'tipo'=>'tour',    'nombre'=>'Xcaret Park — Día Completo',  'descripcion'=>'Parque eco-arqueológico con ríos subterráneos, show nocturno y buffet.','fecha_inicio'=>'2026-07-19','fecha_fin'=>'2026-07-19','precio'=>420.00,'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_rod,'tipo'=>'transfer','nombre'=>'Traslado Aeropuerto ↔ Hotel', 'descripcion'=>'Transporte privado SUV ida y vuelta: Aeropuerto Cancún — Grand Oasis.','fecha_inicio'=>'2026-07-15','fecha_fin'=>'2026-07-22','precio'=>180.00,'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_rod,'tipo'=>'seguro',  'nombre'=>'Assist Card Premium',         'descripcion'=>'Cobertura médica USD 60,000 — Equipaje — Cancelación — Repatriación. Póliza #AC-2026-8891.','fecha_inicio'=>'2026-07-15','fecha_fin'=>'2026-07-22','precio'=>280.00,'estado'=>'pendiente']);
echo "  → 5 servicios\n";

// 4h. Pagos
ins($pdo,'pagos',['contrato_id'=>$ctid_rod,'concepto'=>'Depósito Inicial',  'monto'=>3000.00,'fecha_vencimiento'=>'2026-01-15','fecha_pago'=>'2026-01-15','estado'=>'aprobado','metodo_pago'=>'Transferencia bancaria']);
ins($pdo,'pagos',['contrato_id'=>$ctid_rod,'concepto'=>'Cuota 1 — Febrero', 'monto'=>1583.33,'fecha_vencimiento'=>'2026-02-28','fecha_pago'=>'2026-02-25','estado'=>'aprobado','metodo_pago'=>'Transferencia bancaria']);
ins($pdo,'pagos',['contrato_id'=>$ctid_rod,'concepto'=>'Cuota 2 — Marzo',   'monto'=>1583.33,'fecha_vencimiento'=>'2026-03-31','fecha_pago'=>'2026-03-28','estado'=>'aprobado','metodo_pago'=>'Yape']);
ins($pdo,'pagos',['contrato_id'=>$ctid_rod,'concepto'=>'Cuota 3 — Abril',   'monto'=>1583.33,'fecha_vencimiento'=>'2026-04-30','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_rod,'concepto'=>'Cuota 4 — Mayo',    'monto'=>1583.33,'fecha_vencimiento'=>'2026-05-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_rod,'concepto'=>'Cuota 5 — Junio',   'monto'=>1583.35,'fecha_vencimiento'=>'2026-06-30','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
echo "  → 6 pagos (3 aprobados + 3 pendientes)\n";
echo "  ✅ Familia Rodríguez completa\n\n";

// ═══════════════════════════════════════════════════════════════
// 5. GRUPO FAMILIAR 2: García → Punta Cana (pago contado)
// ═══════════════════════════════════════════════════════════════
echo "◆ GRUPO FAMILIAR 2: Familia García → Punta Cana\n";

$uid_gar = ins($pdo, 'usuarios', [
    'codigo'=>'AV-2026-002', 'password'=>$hClient, 'nombre'=>'Roberto', 'apellido'=>'García Sánchez',
    'email'=>'roberto.garcia@email.com', 'telefono'=>'+51-961-200-002', 'rol'=>'cliente_familiar', 'activo'=>1
]);
$cid_gar = ins($pdo, 'clientes', ['usuario_id'=>$uid_gar, 'tipo'=>'familiar', 'direccion'=>'Av. Centenario 890', 'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'32145678']);
echo "  → Usuario id={$uid_gar} codigo=AV-2026-002 | Cliente id={$cid_gar}\n";

$gid_gar = ins($pdo, 'grupos', [
    'nombre'=>'Familia García - Punta Cana 2026', 'tipo'=>'familiar', 'operador'=>'Copa Travel',
    'destino'=>'Punta Cana, República Dominicana', 'fecha_viaje'=>'2026-08-10', 'fecha_retorno'=>'2026-08-17',
    'valor_total'=>15000.00, 'deposito'=>15000.00, 'tipo_pago'=>'contado', 'total_cuotas'=>0,
    'estado'=>'activo', 'representante_id'=>$uid_gar, 'cantidad_pasajeros'=>2
]);

ins($pdo,'servicios_grupo',['grupo_id'=>$gid_gar,'servicio_tipo'=>'hotel','detalle_json'=>json_encode(['nombre'=>'Hard Rock Hotel & Casino Punta Cana','tipo_habitacion'=>'Swim-Up Suite','noches'=>7,'regimen'=>'All Inclusive Gold'])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_gar,'servicio_tipo'=>'vuelos','detalle_json'=>json_encode(['vuelos'=>[
    ['aerolinea'=>'LATAM','numero'=>'LA-2581','ruta'=>'PCL - LIM','salida'=>'2026-08-10T06:30','llegada'=>'2026-08-10T07:30','origen'=>'PCL','destino'=>'LIM','origen_ciudad'=>'Pucallpa','destino_ciudad'=>'Lima'],
    ['aerolinea'=>'Copa','numero'=>'CM-802','ruta'=>'LIM - PUJ','salida'=>'2026-08-10T10:00','llegada'=>'2026-08-10T19:00','origen'=>'LIM','destino'=>'PUJ','origen_ciudad'=>'Lima','destino_ciudad'=>'Punta Cana'],
    ['aerolinea'=>'Copa','numero'=>'CM-803','ruta'=>'PUJ - LIM','salida'=>'2026-08-17T08:00','llegada'=>'2026-08-17T17:00','origen'=>'PUJ','destino'=>'LIM','origen_ciudad'=>'Punta Cana','destino_ciudad'=>'Lima'],
    ['aerolinea'=>'LATAM','numero'=>'LA-2352','ruta'=>'LIM - PCL','salida'=>'2026-08-17T20:00','llegada'=>'2026-08-17T21:00','origen'=>'LIM','destino'=>'PCL','origen_ciudad'=>'Lima','destino_ciudad'=>'Pucallpa']
]])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_gar,'servicio_tipo'=>'excursiones','detalle_json'=>json_encode(['items'=>[['nombre'=>'Isla Saona — Catamarán VIP','fecha'=>'2026-08-12','costo'=>120],['nombre'=>'Santo Domingo Colonial Tour','fecha'=>'2026-08-14','costo'=>85]]])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_gar,'servicio_tipo'=>'seguro','detalle_json'=>json_encode(['nombre'=>'Travel Guard Plus','poliza'=>'TG-2026-4421','cobertura'=>'USD 50,000'])]);

$ctid_gar = ins($pdo, 'contratos', [
    'codigo'=>'AV-2026-002', 'cliente_id'=>$cid_gar, 'grupo_id'=>$gid_gar, 'tipo'=>'familiar',
    'destino'=>'Punta Cana, República Dominicana', 'hotel'=>'Hard Rock Hotel & Casino Punta Cana',
    'descripcion'=>'Paquete premium todo incluido Punta Cana con excursiones opcionales.',
    'fecha_salida'=>'2026-08-10', 'fecha_retorno'=>'2026-08-17',
    'valor_total'=>15000.00, 'deposito'=>15000.00, 'saldo'=>0.00, 'estado'=>'activo',
    'fecha_firma'=>'2026-02-05',
    'titular_nombre'=>'Roberto García Sánchez', 'titular_correo'=>'roberto.garcia@email.com', 'titular_telefono'=>'+51-961-200-002',
    'total_cuotas'=>0, 'tipo_pago'=>'contado'
]);

ins($pdo,'pasajeros',['contrato_id'=>$ctid_gar,'grupo_id'=>$gid_gar,'nombre'=>'Roberto','apellido'=>'García Sánchez', 'tipo'=>'adulto','edad'=>45,'pasaporte'=>'PE-32145678','documento_verificado'=>1]);
ins($pdo,'pasajeros',['contrato_id'=>$ctid_gar,'grupo_id'=>$gid_gar,'nombre'=>'Ana',    'apellido'=>'García de Pérez','tipo'=>'adulto','edad'=>43,'pasaporte'=>'PE-32145679','documento_verificado'=>1]);

ins($pdo,'vuelos',['contrato_id'=>$ctid_gar,'aerolinea'=>'LATAM','numero_vuelo'=>'LA-2581','origen'=>'PCL','origen_ciudad'=>'Pucallpa','destino'=>'LIM','destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-08-10 06:30:00','fecha_llegada'=>'2026-08-10 07:30:00','tipo'=>'ida',     'clase'=>'economica','avion'=>'Airbus A320',   'estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_gar,'aerolinea'=>'Copa',  'numero_vuelo'=>'CM-802', 'origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'PUJ','destino_ciudad'=>'Punta Cana','fecha_salida'=>'2026-08-10 10:00:00','fecha_llegada'=>'2026-08-10 19:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Boeing 737-800','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_gar,'aerolinea'=>'Copa',  'numero_vuelo'=>'CM-803', 'origen'=>'PUJ','origen_ciudad'=>'Punta Cana','destino'=>'LIM','destino_ciudad'=>'Lima',   'fecha_salida'=>'2026-08-17 08:00:00','fecha_llegada'=>'2026-08-17 17:00:00','tipo'=>'vuelta',  'clase'=>'economica','avion'=>'Boeing 737-800','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_gar,'aerolinea'=>'LATAM','numero_vuelo'=>'LA-2352','origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'PCL','destino_ciudad'=>'Pucallpa', 'fecha_salida'=>'2026-08-17 20:00:00','fecha_llegada'=>'2026-08-17 21:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Airbus A320',   'estado'=>'confirmado']);

ins($pdo,'servicios',['contrato_id'=>$ctid_gar,'tipo'=>'hotel',   'nombre'=>'Hard Rock Hotel & Casino Punta Cana','descripcion'=>'All Inclusive Gold — 7 noches, Swim-Up Suite, spa ilimitado.','fecha_inicio'=>'2026-08-10','fecha_fin'=>'2026-08-17','precio'=>6800.00,'estado'=>'pagado']);
ins($pdo,'servicios',['contrato_id'=>$ctid_gar,'tipo'=>'tour',    'nombre'=>'Isla Saona — Catamarán VIP',        'descripcion'=>'Excursión en catamarán, almuerzo en playa, snorkeling.',                           'fecha_inicio'=>'2026-08-12','fecha_fin'=>'2026-08-12','precio'=>120.00, 'estado'=>'pagado']);
ins($pdo,'servicios',['contrato_id'=>$ctid_gar,'tipo'=>'tour',    'nombre'=>'Santo Domingo Colonial Tour',       'descripcion'=>'Recorrido histórico Zona Colonial, Alcázar de Colón.',                             'fecha_inicio'=>'2026-08-14','fecha_fin'=>'2026-08-14','precio'=>85.00,  'estado'=>'pagado']);
ins($pdo,'servicios',['contrato_id'=>$ctid_gar,'tipo'=>'transfer','nombre'=>'Traslado Aeropuerto ↔ Hotel',       'descripcion'=>'Transporte VIP ida y vuelta: Aeropuerto PUJ — Hard Rock.',                          'fecha_inicio'=>'2026-08-10','fecha_fin'=>'2026-08-17','precio'=>150.00, 'estado'=>'pagado']);
ins($pdo,'servicios',['contrato_id'=>$ctid_gar,'tipo'=>'seguro',  'nombre'=>'Travel Guard Plus',                 'descripcion'=>'Cobertura médica USD 50,000. Póliza #TG-2026-4421.',                                'fecha_inicio'=>'2026-08-10','fecha_fin'=>'2026-08-17','precio'=>220.00, 'estado'=>'pagado']);

ins($pdo,'pagos',['contrato_id'=>$ctid_gar,'concepto'=>'Pago Total Contado','monto'=>15000.00,'fecha_vencimiento'=>'2026-02-05','fecha_pago'=>'2026-02-05','estado'=>'aprobado','metodo_pago'=>'Transferencia bancaria']);
echo "  ✅ Familia García completa (pagado al contado)\n\n";

// ═══════════════════════════════════════════════════════════════
// 6. GRUPO FAMILIAR 3: López → Cusco
// ═══════════════════════════════════════════════════════════════
echo "◆ GRUPO FAMILIAR 3: Familia López → Cusco\n";

$uid_lop = ins($pdo, 'usuarios', [
    'codigo'=>'AV-2026-003', 'password'=>$hClient, 'nombre'=>'Luisa', 'apellido'=>'López Ramírez',
    'email'=>'luisa.lopez@email.com', 'telefono'=>'+51-961-200-003', 'rol'=>'cliente_familiar', 'activo'=>1
]);
$cid_lop = ins($pdo, 'clientes', ['usuario_id'=>$uid_lop, 'tipo'=>'familiar', 'direccion'=>'Jr. Inmaculada 456', 'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'21436587']);

$gid_lop = ins($pdo, 'grupos', [
    'nombre'=>'Familia López - Cusco 2026', 'tipo'=>'familiar', 'operador'=>'LATAM Travel',
    'destino'=>'Cusco, Perú', 'fecha_viaje'=>'2026-06-20', 'fecha_retorno'=>'2026-06-25',
    'valor_total'=>4500.00, 'deposito'=>1500.00, 'tipo_pago'=>'cuotas', 'total_cuotas'=>3,
    'meses_pago'=>'Abril,Mayo,Junio', 'estado'=>'activo',
    'representante_id'=>$uid_lop, 'cantidad_pasajeros'=>3
]);

ins($pdo,'servicios_grupo',['grupo_id'=>$gid_lop,'servicio_tipo'=>'hotel','detalle_json'=>json_encode(['nombre'=>'Casa Andina Premium Cusco','tipo_habitacion'=>'Superior','noches'=>5,'regimen'=>'Desayuno Buffet'])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_lop,'servicio_tipo'=>'excursiones','detalle_json'=>json_encode(['items'=>[['nombre'=>'Valle Sagrado de los Incas','fecha'=>'2026-06-21','costo'=>180],['nombre'=>'Machu Picchu — Tren Expedition','fecha'=>'2026-06-22','costo'=>450],['nombre'=>'Montaña de 7 Colores (Vinicunca)','fecha'=>'2026-06-23','costo'=>120]]])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_lop,'servicio_tipo'=>'seguro','detalle_json'=>json_encode(['nombre'=>'Assist Card Básico','poliza'=>'AC-2026-CUSCO','cobertura'=>'USD 30,000'])]);

$ctid_lop = ins($pdo, 'contratos', [
    'codigo'=>'AV-2026-003', 'cliente_id'=>$cid_lop, 'grupo_id'=>$gid_lop, 'tipo'=>'familiar',
    'destino'=>'Cusco, Perú', 'hotel'=>'Casa Andina Premium Cusco',
    'descripcion'=>'Escapada familiar al corazón del imperio inca. Valle Sagrado y Machu Picchu.',
    'fecha_salida'=>'2026-06-20', 'fecha_retorno'=>'2026-06-25',
    'valor_total'=>4500.00, 'deposito'=>1500.00, 'saldo'=>3000.00, 'estado'=>'activo',
    'fecha_firma'=>'2026-03-01',
    'titular_nombre'=>'Luisa López Ramírez', 'titular_correo'=>'luisa.lopez@email.com', 'titular_telefono'=>'+51-961-200-003',
    'total_cuotas'=>3, 'meses_pago'=>'Abril,Mayo,Junio', 'tipo_pago'=>'cuotas'
]);

ins($pdo,'pasajeros',['contrato_id'=>$ctid_lop,'grupo_id'=>$gid_lop,'nombre'=>'Luisa',    'apellido'=>'López Ramírez','tipo'=>'adulto','edad'=>38,'pasaporte'=>'PE-21436587','documento_verificado'=>1]);
ins($pdo,'pasajeros',['contrato_id'=>$ctid_lop,'grupo_id'=>$gid_lop,'nombre'=>'Pedro',    'apellido'=>'López Vargas', 'tipo'=>'adulto','edad'=>40,'pasaporte'=>'PE-21436588','documento_verificado'=>1]);
ins($pdo,'pasajeros',['contrato_id'=>$ctid_lop,'grupo_id'=>$gid_lop,'nombre'=>'Valentina','apellido'=>'López',        'tipo'=>'nino',  'edad'=>10,'pasaporte'=>'PE-87654321','documento_verificado'=>0]);

ins($pdo,'vuelos',['contrato_id'=>$ctid_lop,'aerolinea'=>'LATAM','numero_vuelo'=>'LA-2581','origen'=>'PCL','origen_ciudad'=>'Pucallpa','destino'=>'LIM','destino_ciudad'=>'Lima',    'fecha_salida'=>'2026-06-20 06:30:00','fecha_llegada'=>'2026-06-20 07:30:00','tipo'=>'ida',     'clase'=>'economica','avion'=>'Airbus A320','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_lop,'aerolinea'=>'LATAM','numero_vuelo'=>'LA-2041','origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'CUZ','destino_ciudad'=>'Cusco',   'fecha_salida'=>'2026-06-20 09:00:00','fecha_llegada'=>'2026-06-20 10:20:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Airbus A319','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_lop,'aerolinea'=>'LATAM','numero_vuelo'=>'LA-2042','origen'=>'CUZ','origen_ciudad'=>'Cusco',   'destino'=>'LIM','destino_ciudad'=>'Lima',    'fecha_salida'=>'2026-06-25 15:00:00','fecha_llegada'=>'2026-06-25 16:20:00','tipo'=>'vuelta',  'clase'=>'economica','avion'=>'Airbus A319','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_lop,'aerolinea'=>'LATAM','numero_vuelo'=>'LA-2352','origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'PCL','destino_ciudad'=>'Pucallpa','fecha_salida'=>'2026-06-25 19:00:00','fecha_llegada'=>'2026-06-25 20:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Airbus A320','estado'=>'confirmado']);

ins($pdo,'servicios',['contrato_id'=>$ctid_lop,'tipo'=>'hotel','nombre'=>'Casa Andina Premium Cusco',       'descripcion'=>'Habitación Superior — 5 noches con desayuno buffet, Plaza de Armas.',          'fecha_inicio'=>'2026-06-20','fecha_fin'=>'2026-06-25','precio'=>1200.00,'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_lop,'tipo'=>'tour', 'nombre'=>'Valle Sagrado de los Incas',      'descripcion'=>'Día completo: Pisac, Ollantaytambo, mercado artesanal y almuerzo buffet.',      'fecha_inicio'=>'2026-06-21','fecha_fin'=>'2026-06-21','precio'=>180.00, 'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_lop,'tipo'=>'tour', 'nombre'=>'Machu Picchu — Tren Expedition',  'descripcion'=>'Tren Ollantaytambo – Aguas Calientes, entrada Machu Picchu con guía.',          'fecha_inicio'=>'2026-06-22','fecha_fin'=>'2026-06-22','precio'=>450.00, 'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_lop,'tipo'=>'tour', 'nombre'=>'Montaña de 7 Colores (Vinicunca)','descripcion'=>'Trekking guiado con desayuno y almuerzo. Altitud: 5,200 m.s.n.m.',              'fecha_inicio'=>'2026-06-23','fecha_fin'=>'2026-06-23','precio'=>120.00, 'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_lop,'tipo'=>'seguro','nombre'=>'Assist Card Básico',             'descripcion'=>'Cobertura médica USD 30,000 — Equipaje. Póliza #AC-2026-CUSCO.',                'fecha_inicio'=>'2026-06-20','fecha_fin'=>'2026-06-25','precio'=>150.00, 'estado'=>'pendiente']);

ins($pdo,'pagos',['contrato_id'=>$ctid_lop,'concepto'=>'Depósito Inicial', 'monto'=>1500.00,'fecha_vencimiento'=>'2026-03-15','fecha_pago'=>'2026-03-15','estado'=>'aprobado','metodo_pago'=>'Depósito bancario']);
ins($pdo,'pagos',['contrato_id'=>$ctid_lop,'concepto'=>'Cuota 1 — Abril',  'monto'=>1000.00,'fecha_vencimiento'=>'2026-04-30','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_lop,'concepto'=>'Cuota 2 — Mayo',   'monto'=>1000.00,'fecha_vencimiento'=>'2026-05-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_lop,'concepto'=>'Cuota 3 — Junio',  'monto'=>1000.00,'fecha_vencimiento'=>'2026-06-15','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
echo "  ✅ Familia López completa\n\n";

// ═══════════════════════════════════════════════════════════════
// 7. GRUPO ESCOLAR: Colegio CCPA → Punta Cana
// ═══════════════════════════════════════════════════════════════
echo "◆ GRUPO ESCOLAR: Colegio CCPA → Punta Cana\n";

// Representante del colegio
$uid_rep_ccpa = ins($pdo, 'usuarios', [
    'codigo'=>'REP-CCPA-001', 'password'=>$hClient, 'nombre'=>'Patricia', 'apellido'=>'Vargas Romero',
    'email'=>'patricia.vargas@ccpa.edu.pe', 'telefono'=>'+51-961-300-001', 'rol'=>'representante', 'activo'=>1
]);
echo "  → Representante id={$uid_rep_ccpa} codigo=REP-CCPA-001\n";

$gid_ccpa = ins($pdo, 'grupos', [
    'nombre'=>'Promoción 2026 - Colegio CCPA', 'tipo'=>'colegio', 'operador'=>'Aventuras Travel',
    'destino'=>'Punta Cana, República Dominicana', 'fecha_viaje'=>'2026-10-26', 'fecha_retorno'=>'2026-11-02',
    'valor_total'=>85000.00, 'deposito'=>25000.00, 'tipo_pago'=>'cuotas', 'total_cuotas'=>6,
    'meses_pago'=>'Abril,Mayo,Junio,Julio,Agosto,Septiembre', 'estado'=>'activo',
    'institucion'=>'Colegio CCPA Pucallpa', 'representante_id'=>$uid_rep_ccpa, 'cantidad_pasajeros'=>4
]);

ins($pdo,'servicios_grupo',['grupo_id'=>$gid_ccpa,'servicio_tipo'=>'hotel','detalle_json'=>json_encode(['nombre'=>'Catalonia Bávaro Beach 5★','tipo_habitacion'=>'Standard Triple','noches'=>6,'regimen'=>'All Inclusive'])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_ccpa,'servicio_tipo'=>'vuelos','detalle_json'=>json_encode(['vuelos'=>[
    ['aerolinea'=>'LATAM','numero'=>'LA-2581','ruta'=>'PCL - LIM','salida'=>'2026-10-26T08:30','llegada'=>'2026-10-26T09:30','origen'=>'PCL','destino'=>'LIM','origen_ciudad'=>'Pucallpa','destino_ciudad'=>'Lima'],
    ['aerolinea'=>'Arajet','numero'=>'DM-677','ruta'=>'LIM - PUJ','salida'=>'2026-10-27T10:00','llegada'=>'2026-10-27T18:00','origen'=>'LIM','destino'=>'PUJ','origen_ciudad'=>'Lima','destino_ciudad'=>'Punta Cana'],
    ['aerolinea'=>'Arajet','numero'=>'DM-6774','ruta'=>'PUJ - LIM','salida'=>'2026-11-01T14:20','llegada'=>'2026-11-01T22:20','origen'=>'PUJ','destino'=>'LIM','origen_ciudad'=>'Punta Cana','destino_ciudad'=>'Lima'],
    ['aerolinea'=>'LATAM','numero'=>'LA-2352','ruta'=>'LIM - PCL','salida'=>'2026-11-02T06:00','llegada'=>'2026-11-02T07:00','origen'=>'LIM','destino'=>'PCL','origen_ciudad'=>'Lima','destino_ciudad'=>'Pucallpa']
]])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_ccpa,'servicio_tipo'=>'traslados','detalle_json'=>json_encode(['tipo'=>'ambos','detalle'=>'Transporte grupal Aeropuerto ↔ Hotel'])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_ccpa,'servicio_tipo'=>'excursiones','detalle_json'=>json_encode(['items'=>[['nombre'=>'Isla Saona — Tour Escolar','fecha'=>'2026-10-29','costo'=>95],['nombre'=>'Zip Line Mega Splash','fecha'=>'2026-10-28','costo'=>85]]])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_ccpa,'servicio_tipo'=>'seguro','detalle_json'=>json_encode(['nombre'=>'Travel Guard Escolar','poliza'=>'TG-SCH-2026','cobertura'=>'USD 40,000'])]);
echo "  → Grupo id={$gid_ccpa} + 5 servicios_grupo\n";

// Contrato 1 CCPA: Jane Vargas — usuario generado al crear contrato (titular)
$uid_jane = ins($pdo, 'usuarios', [
    'codigo'=>'CCPA-2026-001', 'password'=>$hClient, 'nombre'=>'Jane', 'apellido'=>'Vargas Romero',
    'email'=>'jane.vargas@email.com', 'telefono'=>'+51-961-300-010', 'rol'=>'cliente_colegio', 'activo'=>1
]);
$cid_jane = ins($pdo, 'clientes', ['usuario_id'=>$uid_jane, 'tipo'=>'colegio', 'direccion'=>'Av. Yarinacocha 123', 'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'78901234']);

$ctid_jane = ins($pdo, 'contratos', [
    'codigo'=>'CCPA-2026-001', 'cliente_id'=>$cid_jane, 'grupo_id'=>$gid_ccpa, 'tipo'=>'colegio',
    'destino'=>'Punta Cana, República Dominicana', 'hotel'=>'Catalonia Bávaro Beach 5★ Resort & Spa',
    'descripcion'=>'Paquete de promoción escolar all-inclusive.',
    'fecha_salida'=>'2026-10-26', 'fecha_retorno'=>'2026-11-02',
    'valor_total'=>1549.00, 'deposito'=>350.00, 'saldo'=>1199.00, 'estado'=>'activo',
    'fecha_firma'=>'2026-01-15',
    'titular_nombre'=>'Jane Vargas Romero', 'titular_correo'=>'jane.vargas@email.com', 'titular_telefono'=>'+51-961-300-010',
    'total_cuotas'=>6, 'meses_pago'=>'Abril,Mayo,Junio,Julio,Agosto,Septiembre', 'tipo_pago'=>'cuotas'
]);

ins($pdo,'pasajeros',['contrato_id'=>$ctid_jane,'grupo_id'=>$gid_ccpa,'nombre'=>'Jane',   'apellido'=>'Vargas Romero','tipo'=>'adulto','edad'=>17,'pasaporte'=>'PE-78901234','documento_verificado'=>1]);
ins($pdo,'pasajeros',['contrato_id'=>$ctid_jane,'grupo_id'=>$gid_ccpa,'nombre'=>'Roberto','apellido'=>'Vargas',       'tipo'=>'adulto','edad'=>45,'pasaporte'=>'PE-12345679','documento_verificado'=>1]);

ins($pdo,'vuelos',['contrato_id'=>$ctid_jane,'aerolinea'=>'LATAM', 'numero_vuelo'=>'LA-2581','origen'=>'PCL','origen_ciudad'=>'Pucallpa','destino'=>'LIM','destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-10-26 08:30:00','fecha_llegada'=>'2026-10-26 09:30:00','tipo'=>'ida',     'clase'=>'economica','avion'=>'Airbus A320',   'estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_jane,'aerolinea'=>'Arajet','numero_vuelo'=>'DM-677', 'origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'PUJ','destino_ciudad'=>'Punta Cana','fecha_salida'=>'2026-10-27 10:00:00','fecha_llegada'=>'2026-10-27 18:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Boeing 737 MAX','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_jane,'aerolinea'=>'Arajet','numero_vuelo'=>'DM-6774','origen'=>'PUJ','origen_ciudad'=>'Punta Cana','destino'=>'LIM','destino_ciudad'=>'Lima',   'fecha_salida'=>'2026-11-01 14:20:00','fecha_llegada'=>'2026-11-01 22:20:00','tipo'=>'vuelta',  'clase'=>'economica','avion'=>'Boeing 737 MAX','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_jane,'aerolinea'=>'LATAM', 'numero_vuelo'=>'LA-2352','origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'PCL','destino_ciudad'=>'Pucallpa', 'fecha_salida'=>'2026-11-02 06:00:00','fecha_llegada'=>'2026-11-02 07:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Airbus A320',   'estado'=>'confirmado']);

ins($pdo,'servicios',['contrato_id'=>$ctid_jane,'tipo'=>'hotel',    'nombre'=>'Catalonia Bávaro Beach 5★','descripcion'=>'Resort & Spa All Inclusive — 6 noches frente al mar.','fecha_inicio'=>'2026-10-27','fecha_fin'=>'2026-11-01','precio'=>800.00,'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_jane,'tipo'=>'actividad','nombre'=>'Zip Line Mega Splash',     'descripcion'=>'Tirolesa sobre la laguna — 2 horas de adrenalina.',                    'fecha_inicio'=>'2026-10-28','fecha_fin'=>'2026-10-28','precio'=>85.00, 'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_jane,'tipo'=>'tour',     'nombre'=>'Isla Saona — Tour Escolar','descripcion'=>'Excursión grupal en catamarán, snorkeling y almuerzo en playa.',     'fecha_inicio'=>'2026-10-29','fecha_fin'=>'2026-10-29','precio'=>95.00, 'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_jane,'tipo'=>'seguro',   'nombre'=>'Travel Guard Escolar',     'descripcion'=>'Cobertura grupal médica USD 40,000. Póliza #TG-SCH-2026.',            'fecha_inicio'=>'2026-10-26','fecha_fin'=>'2026-11-02','precio'=>120.00,'estado'=>'pendiente']);

ins($pdo,'pagos',['contrato_id'=>$ctid_jane,'concepto'=>'Prepago Confirmado', 'monto'=>350.00, 'fecha_vencimiento'=>'2026-01-15','fecha_pago'=>'2026-01-15','estado'=>'aprobado','metodo_pago'=>'Transferencia']);
ins($pdo,'pagos',['contrato_id'=>$ctid_jane,'concepto'=>'Cuota 1 — Abril',   'monto'=>199.83, 'fecha_vencimiento'=>'2026-04-30','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_jane,'concepto'=>'Cuota 2 — Mayo',    'monto'=>199.83, 'fecha_vencimiento'=>'2026-05-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_jane,'concepto'=>'Cuota 3 — Junio',   'monto'=>199.83, 'fecha_vencimiento'=>'2026-06-30','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_jane,'concepto'=>'Cuota 4 — Julio',   'monto'=>199.83, 'fecha_vencimiento'=>'2026-07-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_jane,'concepto'=>'Cuota 5 — Agosto',  'monto'=>199.85, 'fecha_vencimiento'=>'2026-08-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
echo "  → Contrato CCPA-2026-001 (Jane) con usuario, vuelos, servicios, pagos\n";

// Contrato 2 CCPA: Marco Díaz
$uid_marco = ins($pdo, 'usuarios', [
    'codigo'=>'CCPA-2026-002', 'password'=>$hClient, 'nombre'=>'Marco', 'apellido'=>'Díaz Pérez',
    'email'=>'marco.diaz@email.com', 'telefono'=>'+51-961-300-011', 'rol'=>'cliente_colegio', 'activo'=>1
]);
$cid_marco = ins($pdo, 'clientes', ['usuario_id'=>$uid_marco, 'tipo'=>'colegio', 'direccion'=>'Jr. Húsares de Junín 567', 'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'65432198']);

$ctid_marco = ins($pdo, 'contratos', [
    'codigo'=>'CCPA-2026-002', 'cliente_id'=>$cid_marco, 'grupo_id'=>$gid_ccpa, 'tipo'=>'colegio',
    'destino'=>'Punta Cana, República Dominicana', 'hotel'=>'Catalonia Bávaro Beach 5★ Resort & Spa',
    'descripcion'=>'Paquete de promoción escolar all-inclusive.',
    'fecha_salida'=>'2026-10-26', 'fecha_retorno'=>'2026-11-02',
    'valor_total'=>1549.00, 'deposito'=>350.00, 'saldo'=>1199.00, 'estado'=>'activo',
    'fecha_firma'=>'2026-01-20',
    'titular_nombre'=>'Marco Díaz Pérez', 'titular_correo'=>'marco.diaz@email.com', 'titular_telefono'=>'+51-961-300-011',
    'total_cuotas'=>6, 'meses_pago'=>'Abril,Mayo,Junio,Julio,Agosto,Septiembre', 'tipo_pago'=>'cuotas'
]);

ins($pdo,'pasajeros',['contrato_id'=>$ctid_marco,'grupo_id'=>$gid_ccpa,'nombre'=>'Marco', 'apellido'=>'Díaz Pérez',   'tipo'=>'adulto','edad'=>17,'pasaporte'=>'PE-65432198','documento_verificado'=>1]);
ins($pdo,'pasajeros',['contrato_id'=>$ctid_marco,'grupo_id'=>$gid_ccpa,'nombre'=>'Carmen','apellido'=>'Pérez de Díaz','tipo'=>'adulto','edad'=>42,'pasaporte'=>'PE-65432199','documento_verificado'=>1]);

ins($pdo,'vuelos',['contrato_id'=>$ctid_marco,'aerolinea'=>'LATAM', 'numero_vuelo'=>'LA-2581','origen'=>'PCL','origen_ciudad'=>'Pucallpa','destino'=>'LIM','destino_ciudad'=>'Lima',      'fecha_salida'=>'2026-10-26 08:30:00','fecha_llegada'=>'2026-10-26 09:30:00','tipo'=>'ida',     'clase'=>'economica','avion'=>'Airbus A320',   'estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_marco,'aerolinea'=>'Arajet','numero_vuelo'=>'DM-677', 'origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'PUJ','destino_ciudad'=>'Punta Cana','fecha_salida'=>'2026-10-27 10:00:00','fecha_llegada'=>'2026-10-27 18:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Boeing 737 MAX','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_marco,'aerolinea'=>'Arajet','numero_vuelo'=>'DM-6774','origen'=>'PUJ','origen_ciudad'=>'Punta Cana','destino'=>'LIM','destino_ciudad'=>'Lima',   'fecha_salida'=>'2026-11-01 14:20:00','fecha_llegada'=>'2026-11-01 22:20:00','tipo'=>'vuelta',  'clase'=>'economica','avion'=>'Boeing 737 MAX','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_marco,'aerolinea'=>'LATAM', 'numero_vuelo'=>'LA-2352','origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'PCL','destino_ciudad'=>'Pucallpa', 'fecha_salida'=>'2026-11-02 06:00:00','fecha_llegada'=>'2026-11-02 07:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Airbus A320',   'estado'=>'confirmado']);

ins($pdo,'servicios',['contrato_id'=>$ctid_marco,'tipo'=>'hotel',    'nombre'=>'Catalonia Bávaro Beach 5★','descripcion'=>'Resort & Spa All Inclusive — 6 noches.','fecha_inicio'=>'2026-10-27','fecha_fin'=>'2026-11-01','precio'=>800.00,'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_marco,'tipo'=>'actividad','nombre'=>'Zip Line Mega Splash',     'descripcion'=>'Tirolesa sobre la laguna.',                   'fecha_inicio'=>'2026-10-28','fecha_fin'=>'2026-10-28','precio'=>85.00, 'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_marco,'tipo'=>'tour',     'nombre'=>'Isla Saona — Tour Escolar','descripcion'=>'Catamarán grupal, snorkeling y almuerzo.',     'fecha_inicio'=>'2026-10-29','fecha_fin'=>'2026-10-29','precio'=>95.00, 'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_marco,'tipo'=>'seguro',   'nombre'=>'Travel Guard Escolar',     'descripcion'=>'Cobertura grupal USD 40,000. Póliza #TG-SCH.','fecha_inicio'=>'2026-10-26','fecha_fin'=>'2026-11-02','precio'=>120.00,'estado'=>'pendiente']);

ins($pdo,'pagos',['contrato_id'=>$ctid_marco,'concepto'=>'Prepago Confirmado','monto'=>350.00, 'fecha_vencimiento'=>'2026-01-20','fecha_pago'=>'2026-01-20','estado'=>'aprobado','metodo_pago'=>'Yape']);
ins($pdo,'pagos',['contrato_id'=>$ctid_marco,'concepto'=>'Cuota 1 — Abril',  'monto'=>199.83, 'fecha_vencimiento'=>'2026-04-30','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_marco,'concepto'=>'Cuota 2 — Mayo',   'monto'=>199.83, 'fecha_vencimiento'=>'2026-05-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_marco,'concepto'=>'Cuota 3 — Junio',  'monto'=>199.83, 'fecha_vencimiento'=>'2026-06-30','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_marco,'concepto'=>'Cuota 4 — Julio',  'monto'=>199.83, 'fecha_vencimiento'=>'2026-07-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_marco,'concepto'=>'Cuota 5 — Agosto', 'monto'=>199.85, 'fecha_vencimiento'=>'2026-08-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
echo "  → Contrato CCPA-2026-002 (Marco) con usuario, vuelos, servicios, pagos\n";
echo "  ✅ Colegio CCPA completo\n\n";

// ═══════════════════════════════════════════════════════════════
// 8. GRUPO ESCOLAR: San Agustín → Cancún
// ═══════════════════════════════════════════════════════════════
echo "◆ GRUPO ESCOLAR: San Agustín → Cancún\n";

$uid_rep_sa = ins($pdo, 'usuarios', [
    'codigo'=>'REP-SA-001', 'password'=>$hClient, 'nombre'=>'Fernando', 'apellido'=>'Ruiz Castillo',
    'email'=>'fernando.ruiz@sanagustin.edu.pe', 'telefono'=>'+51-961-400-001', 'rol'=>'representante', 'activo'=>1
]);

$gid_sa = ins($pdo, 'grupos', [
    'nombre'=>'Promoción 2026 - San Agustín', 'tipo'=>'colegio', 'operador'=>'Aventuras Travel',
    'destino'=>'Cancún, México', 'fecha_viaje'=>'2026-11-15', 'fecha_retorno'=>'2026-11-22',
    'valor_total'=>72000.00, 'deposito'=>20000.00, 'tipo_pago'=>'cuotas', 'total_cuotas'=>6,
    'meses_pago'=>'Mayo,Junio,Julio,Agosto,Septiembre,Octubre', 'estado'=>'activo',
    'institucion'=>'Colegio San Agustín', 'representante_id'=>$uid_rep_sa, 'cantidad_pasajeros'=>2
]);

ins($pdo,'servicios_grupo',['grupo_id'=>$gid_sa,'servicio_tipo'=>'hotel','detalle_json'=>json_encode(['nombre'=>'Grand Oasis Cancún','tipo_habitacion'=>'Standard Doble','noches'=>7,'regimen'=>'All Inclusive'])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_sa,'servicio_tipo'=>'vuelos','detalle_json'=>json_encode(['vuelos'=>[
    ['aerolinea'=>'LATAM','numero'=>'LA-2581','ruta'=>'PCL - LIM','salida'=>'2026-11-15T06:30','llegada'=>'2026-11-15T07:30','origen'=>'PCL','destino'=>'LIM','origen_ciudad'=>'Pucallpa','destino_ciudad'=>'Lima'],
    ['aerolinea'=>'Avianca','numero'=>'AV-452','ruta'=>'LIM - CUN','salida'=>'2026-11-15T11:00','llegada'=>'2026-11-15T18:30','origen'=>'LIM','destino'=>'CUN','origen_ciudad'=>'Lima','destino_ciudad'=>'Cancún'],
    ['aerolinea'=>'Avianca','numero'=>'AV-453','ruta'=>'CUN - LIM','salida'=>'2026-11-22T09:00','llegada'=>'2026-11-22T16:00','origen'=>'CUN','destino'=>'LIM','origen_ciudad'=>'Cancún','destino_ciudad'=>'Lima'],
    ['aerolinea'=>'LATAM','numero'=>'LA-2352','ruta'=>'LIM - PCL','salida'=>'2026-11-22T19:00','llegada'=>'2026-11-22T20:00','origen'=>'LIM','destino'=>'PCL','origen_ciudad'=>'Lima','destino_ciudad'=>'Pucallpa']
]])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_sa,'servicio_tipo'=>'excursiones','detalle_json'=>json_encode(['items'=>[['nombre'=>'Chichén Itzá — Tour Escolar','fecha'=>'2026-11-17','costo'=>90]]])]);
ins($pdo,'servicios_grupo',['grupo_id'=>$gid_sa,'servicio_tipo'=>'seguro','detalle_json'=>json_encode(['nombre'=>'Assist Card Escolar','poliza'=>'AC-SCH-2026','cobertura'=>'USD 35,000'])]);

// Contrato San Agustín: Ana Torres
$uid_ana = ins($pdo, 'usuarios', [
    'codigo'=>'SA-2026-001', 'password'=>$hClient, 'nombre'=>'Ana', 'apellido'=>'Torres Medina',
    'email'=>'ana.torres@email.com', 'telefono'=>'+51-961-400-010', 'rol'=>'cliente_colegio', 'activo'=>1
]);
$cid_ana = ins($pdo, 'clientes', ['usuario_id'=>$uid_ana, 'tipo'=>'colegio', 'direccion'=>'Av. San Martín 321', 'ciudad'=>'Pucallpa', 'pais'=>'Perú', 'documento_identidad'=>'87654321']);

$ctid_ana = ins($pdo, 'contratos', [
    'codigo'=>'SA-2026-001', 'cliente_id'=>$cid_ana, 'grupo_id'=>$gid_sa, 'tipo'=>'colegio',
    'destino'=>'Cancún, México', 'hotel'=>'Grand Oasis Cancún',
    'descripcion'=>'Paquete de promoción escolar Cancún.',
    'fecha_salida'=>'2026-11-15', 'fecha_retorno'=>'2026-11-22',
    'valor_total'=>1450.00, 'deposito'=>300.00, 'saldo'=>1150.00, 'estado'=>'activo',
    'fecha_firma'=>'2026-04-01',
    'titular_nombre'=>'Ana Torres Medina', 'titular_correo'=>'ana.torres@email.com', 'titular_telefono'=>'+51-961-400-010',
    'total_cuotas'=>6, 'meses_pago'=>'Mayo,Junio,Julio,Agosto,Septiembre,Octubre', 'tipo_pago'=>'cuotas'
]);

ins($pdo,'pasajeros',['contrato_id'=>$ctid_ana,'grupo_id'=>$gid_sa,'nombre'=>'Ana', 'apellido'=>'Torres Medina','tipo'=>'adulto','edad'=>16,'pasaporte'=>'PE-87654321','documento_verificado'=>1]);
ins($pdo,'pasajeros',['contrato_id'=>$ctid_ana,'grupo_id'=>$gid_sa,'nombre'=>'Luis','apellido'=>'Torres Medina','tipo'=>'adulto','edad'=>44,'pasaporte'=>'PE-87654322','documento_verificado'=>0]);

ins($pdo,'vuelos',['contrato_id'=>$ctid_ana,'aerolinea'=>'LATAM',  'numero_vuelo'=>'LA-2581','origen'=>'PCL','origen_ciudad'=>'Pucallpa','destino'=>'LIM','destino_ciudad'=>'Lima',    'fecha_salida'=>'2026-11-15 06:30:00','fecha_llegada'=>'2026-11-15 07:30:00','tipo'=>'ida',     'clase'=>'economica','avion'=>'Airbus A320','estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_ana,'aerolinea'=>'Avianca','numero_vuelo'=>'AV-452', 'origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'CUN','destino_ciudad'=>'Cancún',  'fecha_salida'=>'2026-11-15 11:00:00','fecha_llegada'=>'2026-11-15 18:30:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Boeing 787',  'estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_ana,'aerolinea'=>'Avianca','numero_vuelo'=>'AV-453', 'origen'=>'CUN','origen_ciudad'=>'Cancún',  'destino'=>'LIM','destino_ciudad'=>'Lima',    'fecha_salida'=>'2026-11-22 09:00:00','fecha_llegada'=>'2026-11-22 16:00:00','tipo'=>'vuelta',  'clase'=>'economica','avion'=>'Boeing 787',  'estado'=>'confirmado']);
ins($pdo,'vuelos',['contrato_id'=>$ctid_ana,'aerolinea'=>'LATAM',  'numero_vuelo'=>'LA-2352','origen'=>'LIM','origen_ciudad'=>'Lima',    'destino'=>'PCL','destino_ciudad'=>'Pucallpa','fecha_salida'=>'2026-11-22 19:00:00','fecha_llegada'=>'2026-11-22 20:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Airbus A320','estado'=>'confirmado']);

ins($pdo,'servicios',['contrato_id'=>$ctid_ana,'tipo'=>'hotel', 'nombre'=>'Grand Oasis Cancún',          'descripcion'=>'All Inclusive — 7 noches, piscina y restaurantes.',                    'fecha_inicio'=>'2026-11-15','fecha_fin'=>'2026-11-22','precio'=>680.00,'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_ana,'tipo'=>'tour',  'nombre'=>'Chichén Itzá — Tour Escolar', 'descripcion'=>'Excursión educativa con guía bilingüe, cenote y almuerzo buffet.',    'fecha_inicio'=>'2026-11-17','fecha_fin'=>'2026-11-17','precio'=>90.00, 'estado'=>'pendiente']);
ins($pdo,'servicios',['contrato_id'=>$ctid_ana,'tipo'=>'seguro','nombre'=>'Assist Card Escolar',          'descripcion'=>'Cobertura grupal USD 35,000. Póliza #AC-SCH-2026.',                   'fecha_inicio'=>'2026-11-15','fecha_fin'=>'2026-11-22','precio'=>110.00,'estado'=>'pendiente']);

ins($pdo,'pagos',['contrato_id'=>$ctid_ana,'concepto'=>'Prepago Confirmado',   'monto'=>300.00, 'fecha_vencimiento'=>'2026-04-15','fecha_pago'=>'2026-04-15','estado'=>'aprobado','metodo_pago'=>'Depósito bancario']);
ins($pdo,'pagos',['contrato_id'=>$ctid_ana,'concepto'=>'Cuota 1 — Mayo',      'monto'=>191.67, 'fecha_vencimiento'=>'2026-05-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_ana,'concepto'=>'Cuota 2 — Junio',     'monto'=>191.67, 'fecha_vencimiento'=>'2026-06-30','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_ana,'concepto'=>'Cuota 3 — Julio',     'monto'=>191.67, 'fecha_vencimiento'=>'2026-07-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_ana,'concepto'=>'Cuota 4 — Agosto',    'monto'=>191.67, 'fecha_vencimiento'=>'2026-08-31','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
ins($pdo,'pagos',['contrato_id'=>$ctid_ana,'concepto'=>'Cuota 5 — Septiembre','monto'=>191.65, 'fecha_vencimiento'=>'2026-09-30','fecha_pago'=>null,'estado'=>'pendiente','metodo_pago'=>null]);
echo "  → Contrato SA-2026-001 (Ana) con usuario, vuelos, servicios, pagos\n";
echo "  ✅ Colegio San Agustín completo\n\n";

// ═══════════════════════════════════════════════════════════════
// 9. PROMOCIONES
// ═══════════════════════════════════════════════════════════════
echo "◆ Creando promociones...\n";
ins($pdo,'promociones',['titulo'=>'Cancún Familiar — 20% OFF',  'descripcion'=>'Descubre Cancún con paquetes familiares all-inclusive. Chichén Itzá y Xcaret incluidos.','destino'=>'Cancún, México',  'descuento'=>'20%','fecha_inicio'=>'2026-03-01','fecha_fin'=>'2026-07-01','activa'=>1]);
ins($pdo,'promociones',['titulo'=>'Punta Cana All Inclusive',    'descripcion'=>'Resort 5★ frente al mar caribeño. Hard Rock Hotel a precios inigualables.',              'destino'=>'Punta Cana',      'descuento'=>'15%','fecha_inicio'=>'2026-04-01','fecha_fin'=>'2026-08-01','activa'=>1]);
ins($pdo,'promociones',['titulo'=>'Cusco Imperial — Early Bird', 'descripcion'=>'Valle Sagrado, Machu Picchu y Montaña 7 Colores. Hotel premium incluido.',               'destino'=>'Cusco, Perú',     'descuento'=>'10%','fecha_inicio'=>'2026-02-01','fecha_fin'=>'2026-05-31','activa'=>1]);
ins($pdo,'promociones',['titulo'=>'Promo Escolar 2026',          'descripcion'=>'Paquetes para colegios: Punta Cana y Cancún. Cuotas sin interés, seguro incluido.',      'destino'=>'Internacional',   'descuento'=>'Cuotas sin interés','fecha_inicio'=>'2026-01-01','fecha_fin'=>'2026-09-30','activa'=>1]);
echo "  ✅ 4 promociones\n\n";

// ═══════════════════════════════════════════════════════════════
// 10. NOTIFICACIONES
// ═══════════════════════════════════════════════════════════════
echo "◆ Creando notificaciones...\n";
ins($pdo,'notificaciones',['usuario_id'=>$uid_rod, 'titulo'=>'Recordatorio de Pago',  'mensaje'=>'Tu Cuota 3 — Abril ($1,583.33) vence el 30 de abril.','tipo'=>'advertencia','leida'=>0]);
ins($pdo,'notificaciones',['usuario_id'=>$uid_rod, 'titulo'=>'Vuelos Confirmados',    'mensaje'=>'Tus vuelos a Cancún han sido confirmados.',            'tipo'=>'info',       'leida'=>0]);
ins($pdo,'notificaciones',['usuario_id'=>$uid_gar, 'titulo'=>'Pago Total Recibido',   'mensaje'=>'Tu pago de $15,000 fue recibido. Viaje 100% confirmado.','tipo'=>'exito',   'leida'=>0]);
ins($pdo,'notificaciones',['usuario_id'=>$uid_jane,'titulo'=>'Cuota Pendiente',        'mensaje'=>'Tu Cuota 1 de $199.83 vence el 30 de abril.',          'tipo'=>'advertencia','leida'=>0]);
echo "  ✅ 4 notificaciones\n\n";

// ═══════════════════════════════════════════════════════════════
// VERIFICACIÓN FINAL
// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════\n";
echo "◆ VERIFICACIÓN DE LOGIN\n";
echo "═══════════════════════════════════════════════════════\n";

$testCreds = [
    ['admin',         'admin123'],
    ['AV-2026-001',   'cliente123'],
    ['AV-2026-002',   'cliente123'],
    ['AV-2026-003',   'cliente123'],
    ['CCPA-2026-001', 'cliente123'],
    ['SA-2026-001',   'cliente123'],
    ['REP-CCPA-001',  'cliente123'],
];
foreach ($testCreds as [$code, $pass]) {
    $stmt = $pdo->prepare("SELECT password, rol FROM usuarios WHERE codigo = ?");
    $stmt->execute([$code]);
    $row = $stmt->fetch();
    $ok = $row && password_verify($pass, $row['password']);
    $pad = str_pad($code, 15);
    echo "  {$pad} / {$pass} → " . ($ok ? "✅ OK ({$row['rol']})" : "❌ FALLA") . "\n";
}

// Conteo
echo "\n═══════════════════════════════════════════════════════\n";
echo "◆ RESUMEN DE DATOS\n";
echo "═══════════════════════════════════════════════════════\n";
foreach (['usuarios','clientes','grupos','contratos','pasajeros','vuelos','servicios','pagos','servicios_grupo','promociones','notificaciones'] as $t) {
    $count = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
    echo "  " . str_pad($t, 18) . " → {$count} registros\n";
}

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║   ✅ SEED COMPLETADO — VE A /aventuras/login        ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n";
echo "\nCredenciales:\n";
echo "  admin         / admin123    → Panel Admin\n";
echo "  AV-2026-001   / cliente123  → Fam. Rodríguez (Cancún)\n";
echo "  AV-2026-002   / cliente123  → Fam. García (Punta Cana)\n";
echo "  AV-2026-003   / cliente123  → Fam. López (Cusco)\n";
echo "  CCPA-2026-001 / cliente123  → Jane Vargas (Colegio CCPA)\n";
echo "  CCPA-2026-002 / cliente123  → Marco Díaz (Colegio CCPA)\n";
echo "  SA-2026-001   / cliente123  → Ana Torres (San Agustín)\n";
echo "  REP-CCPA-001  / cliente123  → Representante CCPA\n";
echo "  REP-SA-001    / cliente123  → Representante San Agustín\n";
echo "</pre>";
