<?php
// scripts/test_create_group_school.php
// Prueba: crea un grupo tipo escolar con dos contratos (uno con 2 pasajeros, otro con 1)

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/services/EmailService.php';

$db = Database::getInstance();
try {
    echo "--- Iniciando prueba: grupo escolar (colegio) ---\n";

    // Datos del grupo escolar
    $nombre = 'COLEGIO TEST ' . date('YmdHis');
    $tipo = 'colegio';
    $destino = 'Cusco';
    $destino_code = 'CUS-TEST-'.rand(100,999);
    $fecha_viaje = date('Y-m-d', strtotime('+45 days'));
    $fecha_retorno = date('Y-m-d', strtotime('+49 days'));
    $valor_total = 3000.00;
    $deposito = 500.00;

    // Crear grupo
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
    echo "Grupo escolar creado ID: {$grupoId}\n";

    // Insertar servicios a nivel de grupo (servicios_grupo) para que aparezcan en la vista del grupo
    $svcDetail1 = json_encode(['hoteles' => [['nombre' => 'Hostal Escolar Demo', 'noches' => 4]], 'precio_por_alumno' => 100.00], JSON_UNESCAPED_UNICODE);
    $svcDetail2 = json_encode(['vuelos' => [['aerolinea' => 'SchoolAir', 'numero' => 'SC-200', 'origen' => 'LIM', 'destino' => 'CUZ', 'salida' => $fecha_viaje]]], JSON_UNESCAPED_UNICODE);
    $db->insert('servicios_grupo', ['grupo_id' => $grupoId, 'servicio_tipo' => 'hotel', 'detalle_json' => $svcDetail1, 'activo' => 1]);
    $db->insert('servicios_grupo', ['grupo_id' => $grupoId, 'servicio_tipo' => 'vuelos', 'detalle_json' => $svcDetail2, 'activo' => 1]);
    echo "Servicios de grupo insertados (servicios_grupo).\n";

    // Helper para generar codigo de contrato
    $generateCodigo = function($baseName) use ($db) {
        $year = date('Y');
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $baseName), 0, 4));
        $last = $db->fetchOne('SELECT MAX(id) as max_id FROM contratos');
        $next = ($last['max_id'] ?? 0) + 1;
        return "{$prefix}-{$year}-" . str_pad($next, 3, '0', STR_PAD_LEFT);
    };

    // CONTRATO A: 2 pasajeros (ej. representante + acompañante)
    $codigoA = $generateCodigo($nombre . 'A');
    $contratoA = [
        'codigo' => $codigoA,
        'grupo_id' => $grupoId,
        'tipo' => 'colegio',
        'destino' => $destino,
        'destino_code' => $destino_code,
        'fecha_salida' => $fecha_viaje,
        'fecha_retorno' => $fecha_retorno,
        'valor_total' => 1800.00,
        'deposito' => 300.00,
        'saldo' => 1500.00,
        'estado' => 'activo'
    ];
    $contratoAId = $db->insert('contratos', $contratoA);
    echo "Contrato A creado ID: {$contratoAId} (codigo: {$codigoA})\n";

    // CONTRATO B: 1 pasajero
    $codigoB = $generateCodigo($nombre . 'B');
    $contratoB = [
        'codigo' => $codigoB,
        'grupo_id' => $grupoId,
        'tipo' => 'colegio',
        'destino' => $destino,
        'destino_code' => $destino_code,
        'fecha_salida' => $fecha_viaje,
        'fecha_retorno' => $fecha_retorno,
        'valor_total' => 1200.00,
        'deposito' => 200.00,
        'saldo' => 1000.00,
        'estado' => 'activo'
    ];
    $contratoBId = $db->insert('contratos', $contratoB);
    echo "Contrato B creado ID: {$contratoBId} (codigo: {$codigoB})\n";

    // Pasajeros para contrato A (titular + acompañante)
    $pasA1 = ['nombre' => 'María', 'apellido' => 'Gonzales', 'tipo' => 'lider', 'edad' => 40, 'pasaporte' => 'P-'.rand(1000000,9999999), 'email' => 'maria.gonzales+'.time().'@example.com', 'telefono' => '+51-999-300-'.rand(100,999)];
    $pasA2 = ['nombre' => 'Carlos', 'apellido' => 'Lopez', 'tipo' => 'adulto', 'edad' => 42, 'pasaporte' => 'P-'.rand(1000000,9999999), 'email' => 'carlos.lopez+'.time().'@example.com', 'telefono' => '+51-999-301-'.rand(100,999)];

    $pA1Id = $db->insert('pasajeros', array_merge($pasA1, ['grupo_id' => $grupoId, 'contrato_id' => $contratoAId]));
    $pA2Id = $db->insert('pasajeros', array_merge($pasA2, ['grupo_id' => $grupoId, 'contrato_id' => $contratoAId]));
    echo "Pasajeros para Contrato A creados: {$pA1Id}, {$pA2Id}\n";

    // Pasajero para contrato B (titular individual)
    $pasB1 = ['nombre' => 'Lucía', 'apellido' => 'Martínez', 'tipo' => 'lider', 'edad' => 38, 'pasaporte' => 'P-'.rand(1000000,9999999), 'email' => 'lucia.martinez+'.time().'@example.com', 'telefono' => '+51-999-400-'.rand(100,999)];
    $pB1Id = $db->insert('pasajeros', array_merge($pasB1, ['grupo_id' => $grupoId, 'contrato_id' => $contratoBId]));
    echo "Pasajero para Contrato B creado: {$pB1Id}\n";

    // Insertar servicios y vuelo simples para ambos contratos
    $hotelJson = json_encode(['nombre' => 'Hotel Escolar Demo', 'noches' => 4]);
    $db->insert('servicios', ['contrato_id' => $contratoAId, 'tipo' => 'hotel', 'nombre' => 'Hotel Escolar Demo', 'descripcion' => 'Alojamiento grupo', 'fecha_inicio' => $fecha_viaje, 'fecha_fin' => $fecha_retorno, 'precio' => 900.00, 'estado' => 'confirmado', 'detalles_json' => $hotelJson]);
    $db->insert('servicios', ['contrato_id' => $contratoBId, 'tipo' => 'hotel', 'nombre' => 'Hotel Individual Demo', 'descripcion' => 'Alojamiento titular', 'fecha_inicio' => $fecha_viaje, 'fecha_fin' => $fecha_retorno, 'precio' => 400.00, 'estado' => 'confirmado', 'detalles_json' => $hotelJson]);

    // Vuelos demo
    $db->insert('vuelos', ['contrato_id' => $contratoAId, 'aerolinea' => 'SchoolAir', 'numero_vuelo' => 'SC-200', 'origen' => 'LIM', 'origen_ciudad' => 'Lima', 'destino' => 'CUZ', 'destino_ciudad' => 'Cusco', 'fecha_salida' => $fecha_viaje.' 07:00:00', 'fecha_llegada' => $fecha_viaje.' 08:30:00', 'tipo' => 'ida', 'clase' => 'turista', 'estado' => 'confirmado']);
    $db->insert('vuelos', ['contrato_id' => $contratoBId, 'aerolinea' => 'SchoolAir', 'numero_vuelo' => 'SC-201', 'origen' => 'LIM', 'origen_ciudad' => 'Lima', 'destino' => 'CUZ', 'destino_ciudad' => 'Cusco', 'fecha_salida' => $fecha_viaje.' 09:00:00', 'fecha_llegada' => $fecha_viaje.' 10:30:00', 'tipo' => 'ida', 'clase' => 'turista', 'estado' => 'confirmado']);

    echo "Servicios y vuelos agregados a ambos contratos.\n";

    // Crear usuarios/clientes titulares para cada contrato (si no existe)
    $createUserFor = function($person) use ($db) {
            $email = $person['email'] ?? null;
            // Verificar columnas existentes en la tabla usuarios para evitar columnas faltantes
            // Obtener columnas de la tabla usuarios (no usar placeholder para mayor compatibilidad)
            $colsRows = $db->fetchAll("SELECT `COLUMN_NAME` FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios'");
            $cols = is_array($colsRows) ? array_column($colsRows, 'COLUMN_NAME') : [];

            $exists = null;
            if ($email && in_array('email', $cols)) {
                $exists = $db->fetchOne('SELECT id FROM usuarios WHERE email = ?', [$email]);
            }
            if ($exists) return $exists['id'];

            $last = $db->fetchOne('SELECT MAX(id) as max_id FROM usuarios');
            $next = ($last['max_id'] ?? 0) + 1;
            $year = date('Y');
            $codigo = "AV-{$year}-" . str_pad($next, 3, '0', STR_PAD_LEFT);
            $pass = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
            $parts = explode(' ', trim($person['nombre'] . ' ' . $person['apellido']), 2);

            // Datos mínimos garantizados
            $userData = [
                'nombre' => $parts[0] ?? $person['nombre'],
                'apellido' => $parts[1] ?? ($person['apellido'] ?? ''),
                'password' => password_hash($pass, PASSWORD_DEFAULT),
                'codigo' => $codigo,
                'rol' => 'cliente_colegio',
                'activo' => 1
            ];
            // Agregar opcionales solo si existen en la tabla
            if (!empty($person['telefono']) && in_array('telefono', $cols)) $userData['telefono'] = $person['telefono'];
            if (!empty($email) && in_array('email', $cols)) $userData['email'] = $email;

            // Filtrar solo columnas válidas por si faltan otras
            $validUserData = $userData;
            try {
                $userId = $db->insert('usuarios', $validUserData);
            } catch (Exception $ex) {
                // Intento de emergencia: insertar sólo columnas mínimas para evitar fallos de esquema
                error_log('test_create_group_school.php: usuarios insert failed: ' . $ex->getMessage());
                $minimal = ['nombre' => $userData['nombre'], 'apellido' => $userData['apellido'], 'password' => $userData['password'], 'codigo' => $userData['codigo']];
                $userId = $db->insert('usuarios', array_intersect_key($minimal, array_flip($cols)));
            }

            // Insertar cliente sólo si la tabla clientes existe y tiene usuario_id
            $clienteColsRows = $db->fetchAll('SELECT `COLUMN_NAME` FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', ['clientes']);
            $clienteCols = array_column($clienteColsRows, 'COLUMN_NAME');
            $clienteId = null;
            if (in_array('usuario_id', $clienteCols)) {
                $clienteData = ['usuario_id' => $userId, 'tipo' => 'colegio'];
                $clienteId = $db->insert('clientes', array_intersect_key($clienteData, array_flip($clienteCols)));
            }

            // Intento de envío de credenciales
            if ($email) {
                $es = new EmailService();
                $es->sendCredentials($email, trim($person['nombre'] . ' ' . $person['apellido']), $codigo, $pass);
            }

            echo "Usuario creado ID: {$userId} - codigo: {$codigo} (email: {$email})\n";
            return ['user_id' => $userId, 'cliente_id' => $clienteId];
    };

    $userA = $createUserFor($pasA1);
    $userB = $createUserFor($pasB1);

    // Asociar representante del grupo al primer usuario creado
    if (!empty($userA['user_id'])) $db->update('grupos', ['representante_id' => $userA['user_id']], 'id = ?', [$grupoId]);

    // Asociar contratos al cliente (titular responsable)
    if (!empty($userA['cliente_id'])) {
        $db->update('contratos', ['cliente_id' => $userA['cliente_id']], 'id = ?', [$contratoAId]);
        echo "Contrato A asociado a cliente ID: {$userA['cliente_id']}\n";
    }
    if (!empty($userB['cliente_id'])) {
        $db->update('contratos', ['cliente_id' => $userB['cliente_id']], 'id = ?', [$contratoBId]);
        echo "Contrato B asociado a cliente ID: {$userB['cliente_id']}\n";
    }

    // --- CONTRATO C: crear contrato adicional con titular y pasajero ---
    $codigoC = $generateCodigo($nombre . 'C');
    $contratoC = [
        'codigo' => $codigoC,
        'grupo_id' => $grupoId,
        'tipo' => 'colegio',
        'destino' => $destino,
        'destino_code' => $destino_code,
        'fecha_salida' => $fecha_viaje,
        'fecha_retorno' => $fecha_retorno,
        'valor_total' => 1600.00,
        'deposito' => 300.00,
        'saldo' => 1300.00,
        'estado' => 'activo'
    ];
    $contratoCId = $db->insert('contratos', $contratoC);
    echo "Contrato C creado ID: {$contratoCId} (codigo: {$codigoC})\n";

    // Titular del contrato C (responsable) — crear usuario y cliente
    $titularC = ['nombre' => 'Fernando', 'apellido' => 'Santos', 'telefono' => '+51-999-500-'.rand(100,999), 'email' => 'fernando.santos+'.time().'@example.com'];
    $userC = $createUserFor($titularC);
    if (!empty($userC['cliente_id'])) {
        $db->update('contratos', ['cliente_id' => $userC['cliente_id']], 'id = ?', [$contratoCId]);
        echo "Contrato C asociado a cliente ID: {$userC['cliente_id']}\n";
    }

    // Actualizar campos de titular en contratos si la columna existe (intento seguro)
    $colsContrato = $db->fetchAll("SHOW COLUMNS FROM contratos");
    $colNamesContrato = array_map(fn($c) => $c['Field'], $colsContrato ?: []);
    $updateFields = [];
    if (in_array('titular_nombre', $colNamesContrato)) $updateFields['titular_nombre'] = $titularC['nombre'] . ' ' . $titularC['apellido'];
    if (in_array('titular_correo', $colNamesContrato)) $updateFields['titular_correo'] = $titularC['email'];
    if (in_array('titular_telefono', $colNamesContrato)) $updateFields['titular_telefono'] = $titularC['telefono'];
    if (!empty($updateFields)) $db->update('contratos', $updateFields, 'id = ?', [$contratoCId]);

    // Crear pasajero para contrato C (datos del pasajero)
    $pasC1 = ['nombre' => 'Andrés', 'apellido' => 'Santos', 'tipo' => 'lider', 'edad' => 45, 'pasaporte' => 'P-'.rand(1000000,9999999)];
    $pC1Id = $db->insert('pasajeros', array_merge($pasC1, ['grupo_id' => $grupoId, 'contrato_id' => $contratoCId]));
    echo "Pasajero para Contrato C creado: {$pC1Id}\n";

    // Función para crear cronograma de pagos: prepago + cuotas mensuales
    $createPaymentSchedule = function($contratoId, $valorTotal, $prepago, $numCuotas = 6, $startDate = null) use ($db) {
        if (!$startDate) $startDate = date('Y-m-d');
        // Prepago
        $db->insert('pagos', ['contrato_id' => $contratoId, 'concepto' => 'Prepago (al firmar)', 'monto' => $prepago, 'fecha_vencimiento' => $startDate, 'estado' => 'pendiente']);
        $resto = round($valorTotal - $prepago, 2);
        if ($numCuotas <= 0) return;
        $cuotaBase = floor(($resto / $numCuotas) * 100) / 100; // floor to cents
        $suma = $cuotaBase * $numCuotas;
        $ajuste = round($resto - $suma, 2);
        for ($i = 1; $i <= $numCuotas; $i++) {
            $monto = $cuotaBase;
            if ($i === $numCuotas) $monto = round($monto + $ajuste, 2);
            $fecha = date('Y-m-d', strtotime("+{$i} month", strtotime($startDate)));
            $db->insert('pagos', ['contrato_id' => $contratoId, 'concepto' => "Cuota {$i}", 'monto' => $monto, 'fecha_vencimiento' => $fecha, 'estado' => 'pendiente']);
        }
    };

    // Crear cronograma para ambos contratos
    $createPaymentSchedule($contratoAId, $contratoA['valor_total'], $contratoA['deposito'], 6, date('Y-m-d'));
    $createPaymentSchedule($contratoBId, $contratoB['valor_total'], $contratoB['deposito'], 6, date('Y-m-d'));

    echo "Cronogramas de pagos creados para Contrato A y B.\n";

    echo "--- Prueba escolar completada. Revisa tablas: grupos, contratos, pasajeros, servicios, vuelos, usuarios, clientes, pagos. ---\n";

} catch (Exception $e) {
    echo "Error en prueba escolar: " . $e->getMessage() . "\n";
}
