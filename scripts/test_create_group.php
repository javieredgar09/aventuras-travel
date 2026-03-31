<?php
// scripts/test_create_group.php
// Script de prueba: inserta un grupo familiar de ejemplo + pasajero + crea usuario/cliente si aplica

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/services/EmailService.php';

$db = Database::getInstance();
try {
    echo "--- Iniciando prueba de creación de grupo (mock) ---\n";

    // Datos de ejemplo
    $nombre = 'TEST Grupo AI ' . date('YmdHis');
    $tipo = 'familiar';
    $destino = 'Punta Cana';
    $destino_code = 'PDC-TEST-001';
    $fecha_viaje = date('Y-m-d', strtotime('+30 days'));
    $fecha_retorno = date('Y-m-d', strtotime('+37 days'));
    $valor_total = 1200.00;
    $deposito = 200.00;

    // Pasajero titular con email y telefono
    $pasajeros = [
        [
            'nombre' => 'Test',
            'apellido' => 'Usuario',
            'email' => 'test.user+' . time() . '@example.com',
            'telefono' => '+51-999-000-'.rand(100,999),
            'tipo' => 'adulto',
            'edad' => 35,
            'pasaporte' => 'P-'.rand(1000000,9999999)
        ],
        [
            'nombre' => 'Ana',
            'apellido' => 'Pérez',
            'email' => 'ana.perez+' . time() . '@example.com',
            'telefono' => '+51-999-111-'.rand(100,999),
            'tipo' => 'adulto',
            'edad' => 32,
            'pasaporte' => 'P-'.rand(1000000,9999999)
        ],
        [
            'nombre' => 'Lucas',
            'apellido' => 'Gómez',
            'email' => 'lucas.gomez+' . time() . '@example.com',
            'telefono' => '+51-999-222-'.rand(100,999),
            'tipo' => 'menor',
            'edad' => 12,
            'pasaporte' => 'P-'.rand(1000000,9999999)
        ]
    ];

    // Insertar grupo
    $grupoId = $db->insert('grupos', [
        'nombre' => $nombre,
        'tipo' => $tipo,
        'destino' => $destino,
        'destino_code' => $destino_code,
        'fecha_viaje' => $fecha_viaje,
        'fecha_retorno' => $fecha_retorno,
        'valor_total' => $valor_total,
        'deposito' => $deposito,
        'estado' => 'activo'
    ]);

    echo "Grupo creado con ID: {$grupoId}\n";

    // Crear un contrato asociado al grupo para que el cliente vea vuelos/servicios
    $year = date('Y');
    $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nombre), 0, 4));
    $lastIdC = $db->fetchOne('SELECT MAX(id) as max_id FROM contratos');
    $nextIdC = ($lastIdC['max_id'] ?? 0) + 1;
    $codigoContrato = "{$prefix}-{$year}-" . str_pad($nextIdC, 3, '0', STR_PAD_LEFT);

    $contratoId = $db->insert('contratos', [
        'codigo' => $codigoContrato,
        'grupo_id' => $grupoId,
        'tipo' => 'familiar',
        'destino' => $destino,
        'destino_code' => $destino_code,
        'fecha_salida' => $fecha_viaje,
        'fecha_retorno' => $fecha_retorno,
        'valor_total' => $valor_total,
        'deposito' => $deposito,
        'saldo' => $valor_total - $deposito,
        'estado' => 'activo'
    ]);
    echo "Contrato creado ID: {$contratoId} - codigo: {$codigoContrato}\n";

    // Insertar pasajeros
    foreach ($pasajeros as $p) {
        $pId = $db->insert('pasajeros', [
            'nombre' => $p['nombre'],
            'apellido' => $p['apellido'],
            'tipo' => $p['tipo'],
            'edad' => $p['edad'],
            'pasaporte' => $p['pasaporte'],
            'grupo_id' => $grupoId,
            'contrato_id' => $contratoId
        ]);
        echo "Pasajero creado ID: {$pId} ({$p['nombre']} {$p['apellido']})\n";
    }

    // Agregar servicios al grupo en servicios_grupo (hotel, vuelos, excursiones)
    $hotelDetail = json_encode(['nombre' => 'Hotel Ficticio Playa Azul', 'tipo_habitacion' => 'Suite Familiar', 'noches' => 7]);
    $vuelosDetail = json_encode(['aerolinea' => 'DemoAir', 'vuelo_ida' => 'DA-100', 'vuelo_vuelta' => 'DA-101', 'origen' => 'LIM', 'destino' => 'PUJ', 'fecha_ida' => $fecha_viaje, 'fecha_vuelta' => $fecha_retorno]);
    $excursionesDetail = json_encode(['items' => [
        ['nombre' => 'Isla Ficticia Tour', 'fecha' => date('Y-m-d', strtotime($fecha_viaje . ' +2 days')), 'costo' => 50],
        ['nombre' => 'City Tour Demo', 'fecha' => date('Y-m-d', strtotime($fecha_viaje . ' +4 days')), 'costo' => 30]
    ]]);

    $db->insert('servicios_grupo', ['grupo_id' => $grupoId, 'servicio_tipo' => 'hotel', 'detalle_json' => $hotelDetail]);
    $db->insert('servicios_grupo', ['grupo_id' => $grupoId, 'servicio_tipo' => 'vuelos', 'detalle_json' => $vuelosDetail]);
    $db->insert('servicios_grupo', ['grupo_id' => $grupoId, 'servicio_tipo' => 'excursiones', 'detalle_json' => $excursionesDetail]);
    echo "Servicios (hotel, vuelos, excursiones) añadidos al grupo.\n";

    // Insertar vuelos en tabla contratos->vuelos para visibilidad del cliente
    $vueloIdA = $db->insert('vuelos', [
        'contrato_id' => $contratoId,
        'aerolinea' => 'DemoAir',
        'numero_vuelo' => 'DA-100',
        'origen' => 'LIM',
        'origen_ciudad' => 'Lima',
        'destino' => 'PUJ',
        'destino_ciudad' => 'Punta Cana',
        'fecha_salida' => $fecha_viaje . ' 08:00:00',
        'fecha_llegada' => $fecha_viaje . ' 12:00:00',
        'tipo' => 'ida',
        'clase' => 'economica',
        'avion' => 'Boeing 737',
        'estado' => 'confirmado'
    ]);
    $vueloIdB = $db->insert('vuelos', [
        'contrato_id' => $contratoId,
        'aerolinea' => 'DemoAir',
        'numero_vuelo' => 'DA-101',
        'origen' => 'PUJ',
        'origen_ciudad' => 'Punta Cana',
        'destino' => 'LIM',
        'destino_ciudad' => 'Lima',
        'fecha_salida' => $fecha_retorno . ' 14:00:00',
        'fecha_llegada' => $fecha_retorno . ' 18:00:00',
        'tipo' => 'vuelta',
        'clase' => 'economica',
        'avion' => 'Boeing 737',
        'estado' => 'confirmado'
    ]);
    echo "Vuelos creados IDs: {$vueloIdA}, {$vueloIdB}\n";

    // Insertar servicios (hotel) en tabla servicios vinculados al contrato
    $servId = $db->insert('servicios', [
        'contrato_id' => $contratoId,
        'tipo' => 'hotel',
        'nombre' => 'Hotel Ficticio Playa Azul',
        'descripcion' => 'Suite familiar + desayuno',
        'fecha_inicio' => $fecha_viaje,
        'fecha_fin' => $fecha_retorno,
        'precio' => 600.00,
        'estado' => 'pendiente',
        'detalles_json' => $hotelDetail
    ]);
    echo "Servicio (hotel) creado ID: {$servId}\n";

    // Intentar crear usuario/cliente para el primer pasajero
    $first = $pasajeros[0];
    $email = $first['email'];
    $telefono = $first['telefono'];
    $nombreTitular = trim($first['nombre'] . ' ' . $first['apellido']);

    // Comprobar si la columna email existe
    $exists = null;
    try {
        $uCols = $db->fetchAll("SHOW COLUMNS FROM usuarios");
        $uColsNames = array_map(fn($c) => $c['Field'], $uCols ?: []);
        if (in_array('email', $uColsNames)) {
            $exists = $db->fetchOne('SELECT id FROM usuarios WHERE email = ?', [$email]);
        }
    } catch (Exception $ex) {
        error_log('[scripts/test_create_group.php] comprobacion email tabla usuarios falló: ' . $ex->getMessage());
        $exists = null;
    }
    if ($exists) {
        echo "Usuario ya existe con ID: {$exists['id']}\n";
        $db->update('grupos', ['representante_id' => $exists['id']], 'id = ?', [$grupoId]);
    } else {
        $lastId = $db->fetchOne('SELECT MAX(id) as max_id FROM usuarios');
        $nextId = ($lastId['max_id'] ?? 0) + 1;
        $year = date('Y');
        $codigo = "AV-{$year}-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $randomPass = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        $parts = explode(' ', $nombreTitular, 2);
        $nombreUsr = $parts[0] ?? $nombreTitular;
        $apellidoUsr = $parts[1] ?? '';

        $userData = [
            'nombre' => $nombreUsr,
            'apellido' => $apellidoUsr,
            'email' => $email,
            'telefono' => $telefono,
            'password' => password_hash($randomPass, PASSWORD_DEFAULT),
            'codigo' => $codigo,
            'rol' => 'cliente_familiar',
            'activo' => 1
        ];
        $userCols = $db->fetchAll("SHOW COLUMNS FROM usuarios");
        $userColsNames = array_map(fn($c) => $c['Field'], $userCols ?: []);
        $userFiltered = array_intersect_key($userData, array_flip($userColsNames));
        $userId = $db->insert('usuarios', $userFiltered);

        echo "Usuario creado ID: {$userId} - codigo: {$codigo} - pass: {$randomPass}\n";

        $clienteId = $db->insert('clientes', [
            'usuario_id' => $userId,
            'tipo' => 'familiar'
        ]);
        echo "Cliente creado ID: {$clienteId}\n";

        $db->update('grupos', ['representante_id' => $userId], 'id = ?', [$grupoId]);

        // Enviar email (EmailService puede hacer fallback a storage)
        $emailSvc = new EmailService();
        $emailSvc->sendCredentials($email, $nombreTitular, $codigo, $randomPass);
        echo "Intento de envío de credenciales a {$email} (ver storage si no hay SMTP)\n";
    }

    // Insertar pago si hay deposito
    if ($deposito > 0) {
        $pagoId = $db->insert('pagos', [
            'grupo_id' => $grupoId,
            'entidad_tipo' => 'grupo',
            'concepto' => 'Depósito inicial',
            'monto' => $deposito,
            'cuota_numero' => 0,
            'fecha_vencimiento' => date('Y-m-d'),
            'estado' => 'pendiente'
        ]);
        echo "Pago (depósito) creado ID: {$pagoId}\n";
    }

    echo "--- Prueba completada. Verifica en la BD: grupos, pasajeros, usuarios, clientes, pagos. ---\n";

} catch (Exception $e) {
    echo "Error en prueba: " . $e->getMessage() . "\n";
}
