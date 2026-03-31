<?php
/**
 * Script de prueba: simula login de cliente y renderiza dashboard
 * Uso: php scripts/test_client_flow.php
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Autoload
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/core/',
        BASE_PATH . '/app/controllers/',
        BASE_PATH . '/app/controllers/api/',
        BASE_PATH . '/app/models/',
        BASE_PATH . '/app/middlewares/',
        BASE_PATH . '/app/services/',
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) { require_once $file; return; }
    }
});

echo "=== TEST CLIENTE AVENTURAS TRAVEL ===\n\n";

// 1. Conexión a BD
echo "[1] Conexión a BD... ";
try {
    $db = Database::getInstance();
    echo "OK\n";
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Buscar usuario cliente_familiar
echo "[2] Buscando usuario cliente_familiar... ";
$userRow = $db->fetchOne("SELECT * FROM usuarios WHERE rol = 'cliente_familiar' LIMIT 1");
if (!$userRow) {
    echo "FAIL: No hay usuarios cliente_familiar en la BD\n";
    // Intentar con cliente_colegio
    $userRow = $db->fetchOne("SELECT * FROM usuarios WHERE rol = 'cliente_colegio' LIMIT 1");
    if (!$userRow) {
        echo "FAIL: Tampoco hay cliente_colegio\n";
        exit(1);
    }
    echo "Usando cliente_colegio en su lugar\n";
} else {
    echo "OK: {$userRow['nombre']} {$userRow['apellido']} (ID:{$userRow['id']}, rol:{$userRow['rol']})\n";
}

// 3. Simular sesión
echo "[3] Simulando sesión... ";
if (!headers_sent() && session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user'] = $userRow;
$_SERVER['REQUEST_URI'] = '/client/dashboard';
$_SERVER['REQUEST_METHOD'] = 'GET';
echo "OK (SESSION user ID: {$userRow['id']})\n";

// 4. Probar modelo Cliente + insertar seed data completo si falta
echo "[4] Probando modelo Cliente... ";
try {
    $clienteModel = new Cliente();
    $cliente = $clienteModel->findByUsuarioId($userRow['id']);
    if (!$cliente) {
        echo "No existe, insertando seed clientes... ";
        $seedClientes = [
            ['usuario_id' => 2, 'tipo' => 'familiar', 'direccion' => '123 Oak Street', 'ciudad' => 'New York', 'pais' => 'USA', 'documento_identidad' => 'US-PP-882193441'],
            ['usuario_id' => 3, 'tipo' => 'familiar', 'direccion' => 'Calle Mayor 45', 'ciudad' => 'Madrid', 'pais' => 'España', 'documento_identidad' => 'ES-DNI-12345678'],
            ['usuario_id' => 4, 'tipo' => 'familiar', 'direccion' => '456 Elm Avenue', 'ciudad' => 'Boston', 'pais' => 'USA', 'documento_identidad' => 'US-PP-882193444'],
            ['usuario_id' => 8, 'tipo' => 'familiar', 'direccion' => 'Av. Larco 234', 'ciudad' => 'Lima', 'pais' => 'Perú', 'documento_identidad' => 'PE-DNI-87654321'],
        ];
        foreach ($seedClientes as $sc) {
            $exists = $db->fetchOne("SELECT id FROM clientes WHERE usuario_id = ?", [$sc['usuario_id']]);
            if (!$exists) {
                $db->insert('clientes', $sc);
            }
        }
        $cliente = $clienteModel->findByUsuarioId($userRow['id']);
    }
    if ($cliente) {
        echo "OK: cliente_id={$cliente['id']}, tipo={$cliente['tipo']}\n";
    } else {
        echo "FAIL: No se pudo crear registro de cliente\n";
    }

    // [4b] Insertar contratos familiares + dependencias si no existen
    echo "[4b] Verificando contratos familiares... ";
    // Mapeo: codigo_contrato => usuario_id del dueño
    $contratosSeed = [
        ['codigo' => 'AV-2024-8812', 'usuario_id' => 2, 'destino' => 'Swiss Alps & Northern Italy', 'hotel' => 'Grand Hotel Zermatt - Suite Superior', 'descripcion' => 'The Miller Family Expedition.', 'fecha_salida' => '2024-06-14', 'fecha_retorno' => '2024-06-22', 'valor_total' => 14850.00, 'deposito' => 4455.00, 'saldo' => 10395.00],
        ['codigo' => 'AV-2024-8902', 'usuario_id' => 3, 'destino' => 'Bali, Indonesia', 'hotel' => 'Bali Deluxe Resort & Spa', 'descripcion' => 'Bali Deluxe Package.', 'fecha_salida' => '2024-07-10', 'fecha_retorno' => '2024-07-20', 'valor_total' => 3240.00, 'deposito' => 1000.00, 'saldo' => 2240.00],
        ['codigo' => 'AV-2024-8901', 'usuario_id' => 4, 'destino' => 'Swiss Alps', 'hotel' => 'Alpine Lodge Zermatt', 'descripcion' => 'Swiss Alps Express.', 'fecha_salida' => '2024-08-01', 'fecha_retorno' => '2024-08-08', 'valor_total' => 1850.00, 'deposito' => 500.00, 'saldo' => 1350.00],
        ['codigo' => 'CCPA-2026-001', 'usuario_id' => 8, 'destino' => 'Punta Cana, Dominican Republic', 'hotel' => 'Catalonia Bávaro Beach 5★ Resort & Spa', 'descripcion' => 'Elite Accommodation package.', 'fecha_salida' => '2026-10-26', 'fecha_retorno' => '2026-11-02', 'valor_total' => 1549.00, 'deposito' => 350.00, 'saldo' => 1199.00],
    ];
    $insertados = 0;
    $contratoIdMap = []; // codigo => new contrato_id
    foreach ($contratosSeed as $cs) {
        $existente = $db->fetchOne("SELECT id FROM contratos WHERE codigo = ?", [$cs['codigo']]);
        if ($existente) {
            $contratoIdMap[$cs['codigo']] = (int)$existente['id'];
            continue;
        }
        $realCliente = $db->fetchOne("SELECT id FROM clientes WHERE usuario_id = ?", [$cs['usuario_id']]);
        if (!$realCliente) continue;
        $newId = $db->insert('contratos', [
            'codigo' => $cs['codigo'], 'cliente_id' => $realCliente['id'], 'tipo' => 'familiar',
            'destino' => $cs['destino'], 'hotel' => $cs['hotel'], 'descripcion' => $cs['descripcion'],
            'fecha_salida' => $cs['fecha_salida'], 'fecha_retorno' => $cs['fecha_retorno'],
            'valor_total' => $cs['valor_total'], 'deposito' => $cs['deposito'], 'saldo' => $cs['saldo'], 'estado' => 'activo',
        ]);
        $contratoIdMap[$cs['codigo']] = $newId;
        $insertados++;
    }
    echo "OK ($insertados contratos insertados)\n";

    // [4c] Insertar pasajeros, vuelos, servicios, pagos para contratos nuevos
    if ($insertados > 0) {
        echo "[4c] Insertando pasajeros/vuelos/servicios/pagos... ";
        $deps = 0;
        // Miller (AV-2024-8812)
        if ($cid = ($contratoIdMap['AV-2024-8812'] ?? null)) {
            if (!$db->fetchOne("SELECT id FROM pasajeros WHERE contrato_id = ? LIMIT 1", [$cid])) {
                $db->insert('pasajeros', ['contrato_id'=>$cid,'nombre'=>'David','apellido'=>'Miller','tipo'=>'lider','pasaporte'=>'PP-882193441','preferencia_comida'=>'Standard Meal']); $deps++;
                $db->insert('pasajeros', ['contrato_id'=>$cid,'nombre'=>'Elena','apellido'=>'Miller','tipo'=>'adulto','pasaporte'=>'PP-882193442','preferencia_comida'=>'Vegetarian']); $deps++;
                $db->insert('pasajeros', ['contrato_id'=>$cid,'nombre'=>'Leo','apellido'=>'Miller','tipo'=>'nino','pasaporte'=>'PP-882193443','preferencia_comida'=>'Kids Menu']); $deps++;
            }
            if (!$db->fetchOne("SELECT id FROM vuelos WHERE contrato_id = ? LIMIT 1", [$cid])) {
                $db->insert('vuelos', ['contrato_id'=>$cid,'aerolinea'=>'Lufthansa','numero_vuelo'=>'LH-401','origen'=>'JFK','origen_ciudad'=>'New York','destino'=>'ZRH','destino_ciudad'=>'Zurich','fecha_salida'=>'2024-06-14 10:45:00','fecha_llegada'=>'2024-06-15 01:05:00','tipo'=>'ida','clase'=>'economica','avion'=>'Boeing 747-8','estado'=>'confirmado']); $deps++;
            }
            if (!$db->fetchOne("SELECT id FROM servicios WHERE contrato_id = ? LIMIT 1", [$cid])) {
                $db->insert('servicios', ['contrato_id'=>$cid,'tipo'=>'hotel','nombre'=>'Grand Hotel Zermatt','descripcion'=>'4 Nights - Suite Superior','fecha_inicio'=>'2024-06-14','fecha_fin'=>'2024-06-18','precio'=>4800.00,'estado'=>'pagado']); $deps++;
                $db->insert('servicios', ['contrato_id'=>$cid,'tipo'=>'tour','nombre'=>'Matterhorn Sunrise Trek','descripcion'=>'June 16 - Private Guide','fecha_inicio'=>'2024-06-16','fecha_fin'=>'2024-06-16','precio'=>350.00,'estado'=>'pagado']); $deps++;
                $db->insert('servicios', ['contrato_id'=>$cid,'tipo'=>'transfer','nombre'=>'Private Airport Transfer','descripcion'=>'ZRH to Zermatt - Mercedes V-Class','fecha_inicio'=>'2024-06-14','fecha_fin'=>'2024-06-14','precio'=>280.00,'estado'=>'pendiente']); $deps++;
                $db->insert('servicios', ['contrato_id'=>$cid,'tipo'=>'seguro','nombre'=>'Global Travel Platinum','descripcion'=>'Policy #GT-88219','fecha_inicio'=>'2024-06-14','fecha_fin'=>'2024-06-22','precio'=>450.00,'estado'=>'pagado']); $deps++;
            }
            if (!$db->fetchOne("SELECT id FROM pagos WHERE contrato_id = ? LIMIT 1", [$cid])) {
                $db->insert('pagos', ['contrato_id'=>$cid,'concepto'=>'Depósito inicial','monto'=>4455.00,'fecha_vencimiento'=>'2024-03-01','fecha_pago'=>'2024-03-01','estado'=>'aprobado','metodo_pago'=>'Transferencia bancaria']); $deps++;
                $db->insert('pagos', ['contrato_id'=>$cid,'concepto'=>'Saldo final','monto'=>10395.00,'fecha_vencimiento'=>'2024-06-10','estado'=>'pendiente']); $deps++;
            }
        }
        // Jane Vargas (CCPA-2026-001)
        if ($cid = ($contratoIdMap['CCPA-2026-001'] ?? null)) {
            if (!$db->fetchOne("SELECT id FROM pasajeros WHERE contrato_id = ? LIMIT 1", [$cid])) {
                $db->insert('pasajeros', ['contrato_id'=>$cid,'nombre'=>'Jane','apellido'=>'Vargas Romero','tipo'=>'lider','pasaporte'=>'PE-PP-12345678','preferencia_comida'=>'Standard']); $deps++;
                $db->insert('pasajeros', ['contrato_id'=>$cid,'nombre'=>'Roberto','apellido'=>'Vargas Romero','tipo'=>'adulto','pasaporte'=>'PE-PP-12345679','preferencia_comida'=>'Standard']); $deps++;
                $db->insert('pasajeros', ['contrato_id'=>$cid,'nombre'=>'Elena','apellido'=>'Vargas Romero','tipo'=>'nino','pasaporte'=>'PE-PP-12345680','preferencia_comida'=>'Kids Menu']); $deps++;
            }
            if (!$db->fetchOne("SELECT id FROM vuelos WHERE contrato_id = ? LIMIT 1", [$cid])) {
                $db->insert('vuelos', ['contrato_id'=>$cid,'aerolinea'=>'LATAM','numero_vuelo'=>'LATAM 2581','origen'=>'PCL','origen_ciudad'=>'Pucallpa','destino'=>'LIM','destino_ciudad'=>'Lima','fecha_salida'=>'2026-10-26 08:30:00','fecha_llegada'=>'2026-10-26 09:30:00','tipo'=>'ida','clase'=>'economica','avion'=>'Airbus A320','estado'=>'confirmado']); $deps++;
                $db->insert('vuelos', ['contrato_id'=>$cid,'aerolinea'=>'Arajet','numero_vuelo'=>'DM 677','origen'=>'LIM','origen_ciudad'=>'Lima','destino'=>'PUJ','destino_ciudad'=>'Punta Cana','fecha_salida'=>'2026-10-27 10:00:00','fecha_llegada'=>'2026-10-27 18:00:00','tipo'=>'conexion','clase'=>'economica','avion'=>'Boeing 737 MAX','estado'=>'confirmado']); $deps++;
            }
            if (!$db->fetchOne("SELECT id FROM servicios WHERE contrato_id = ? LIMIT 1", [$cid])) {
                $db->insert('servicios', ['contrato_id'=>$cid,'tipo'=>'hotel','nombre'=>'Catalonia Bávaro Beach 5★','descripcion'=>'Resort & Spa - All Inclusive','fecha_inicio'=>'2026-10-27','fecha_fin'=>'2026-11-01','precio'=>800.00,'estado'=>'pendiente']); $deps++;
                $db->insert('servicios', ['contrato_id'=>$cid,'tipo'=>'actividad','nombre'=>'Zip Line Mega Splash','descripcion'=>'Splash of Emotions Included','fecha_inicio'=>'2026-10-28','fecha_fin'=>'2026-10-28','precio'=>85.00,'estado'=>'pendiente']); $deps++;
            }
            if (!$db->fetchOne("SELECT id FROM pagos WHERE contrato_id = ? LIMIT 1", [$cid])) {
                $db->insert('pagos', ['contrato_id'=>$cid,'concepto'=>'Prepago Confirmado','monto'=>350.00,'fecha_vencimiento'=>'2026-01-15','fecha_pago'=>'2026-01-15','estado'=>'aprobado','metodo_pago'=>'Transferencia']); $deps++;
                $db->insert('pagos', ['contrato_id'=>$cid,'concepto'=>'Cuota 1','monto'=>199.83,'fecha_vencimiento'=>'2026-04-30','estado'=>'pendiente']); $deps++;
                $db->insert('pagos', ['contrato_id'=>$cid,'concepto'=>'Cuota 2','monto'=>199.83,'fecha_vencimiento'=>'2026-05-31','estado'=>'pendiente']); $deps++;
                $db->insert('pagos', ['contrato_id'=>$cid,'concepto'=>'Cuota 3','monto'=>199.83,'fecha_vencimiento'=>'2026-06-30','estado'=>'pendiente']); $deps++;
            }
        }
        echo "OK ($deps registros)\n";
    }
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}

// 5. Probar modelo Contrato
echo "[5] Probando modelo Contrato... ";
try {
    $contratoModel = new Contrato();
    if ($cliente) {
        $contratos = $contratoModel->getByClienteId($cliente['id']);
        echo "Contratos encontrados: " . count($contratos) . "\n";
        if (!empty($contratos)) {
            echo "    Primer contrato: ID={$contratos[0]['id']}, codigo={$contratos[0]['codigo']}\n";
            
            // 5b. getFullDetails
            echo "[5b] getFullDetails del contrato... ";
            $full = $contratoModel->getFullDetails($contratos[0]['id']);
            if ($full) {
                echo "OK\n";
                echo "    - destino: " . ($full['destino'] ?? 'N/A') . "\n";
                echo "    - valor_total: " . ($full['valor_total'] ?? 0) . "\n";
                echo "    - total_pagado: " . ($full['total_pagado'] ?? 0) . "\n";
                echo "    - moneda: " . ($full['moneda'] ?? 'N/A') . "\n";
                echo "    - vuelos: " . count($full['vuelos'] ?? []) . "\n";
                echo "    - pasajeros: " . count($full['pasajeros'] ?? []) . "\n";
                echo "    - pagos: " . count($full['pagos'] ?? []) . "\n";
                echo "    - servicios: " . count($full['servicios'] ?? []) . "\n";
                echo "    - estado: " . ($full['estado'] ?? 'N/A') . "\n";
                echo "    - fecha_salida: " . ($full['fecha_salida'] ?? 'N/A') . "\n";
            } else {
                echo "WARN: getFullDetails devolvió null\n";
            }
        }
    } else {
        echo "SKIP (no hay cliente)\n";
    }
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}

// 6. Probar renderizado de la vista family/dashboard.php
echo "\n[6] Probando renderizado de vista family/dashboard.php...\n";
try {
    // Simular las variables que pasa el controlador
    $contrato = $full ?? null;
    $vuelos = $contrato['vuelos'] ?? [];
    $pasajeros = $contrato['pasajeros'] ?? [];
    $pagos = $contrato['pagos'] ?? [];
    $servicios = $contrato['servicios'] ?? [];
    $vouchers = [];
    $pago_completo = false;
    
    $data = [
        'title'           => 'Mi Viaje - Aventuras Travel',
        'user'            => $userRow,
        'cliente'         => $cliente,
        'contrato'        => $contrato,
        'contratos'       => $contratos ?? [],
        'vuelos'          => $vuelos,
        'pasajeros'       => $pasajeros,
        'pagos'           => $pagos,
        'servicios'       => $servicios,
        'vouchers'        => $vouchers,
        'pago_completo'   => $pago_completo,
        'notificaciones'  => [],
        'csrf_token'      => bin2hex(random_bytes(16)),
        'flash'           => null,
    ];
    
    // Mock Router::url
    if (!class_exists('Router')) {
        // Si Router no se cargó, crear mock
        echo "    WARN: Router no disponible, no se puede renderizar vista\n";
    } else {
        // Capturar output con ob
        ob_start();
        $viewFile = BASE_PATH . '/app/views/client/family/dashboard.php';
        if (!file_exists($viewFile)) {
            echo "    FAIL: No existe $viewFile\n";
        } else {
            extract($data);
            // Wrap en try para capturar errores fatales
            try {
                include $viewFile;
                $output = ob_get_clean();
                $len = strlen($output);
                echo "    OK: Vista renderizada ($len bytes)\n";
                
                // Verificar que hay contenido HTML esperado
                $checks = [
                    'Bienvenido' => strpos($output, 'Bienvenido') !== false,
                    'font-black' => strpos($output, 'font-black') !== false,
                    'fmoney()' => strpos($output, 'S/') !== false || strpos($output, '$') !== false,
                    'progress-bar' => strpos($output, 'progress-bar') !== false,
                    'Router::url' => strpos($output, '/client/') !== false,
                ];
                foreach ($checks as $label => $pass) {
                    echo "    " . ($pass ? '✓' : '✗') . " $label\n";
                }
            } catch (Throwable $e) {
                ob_end_clean();
                echo "    FAIL al renderizar: " . $e->getMessage() . "\n";
                echo "    Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
            }
        }
    }
} catch (Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    echo "    " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// 7. Probar vista group/dashboard.php con usuario colegio
echo "\n[7] Probando vista group/dashboard.php...\n";
try {
    $userColegio = $db->fetchOne("SELECT * FROM usuarios WHERE rol = 'cliente_colegio' LIMIT 1");
    if ($userColegio) {
        echo "    Usuario colegio: {$userColegio['nombre']} (ID:{$userColegio['id']})\n";
        
        // Buscar su grupo
        $grupoRows = $db->fetchAll(
            "SELECT g.* FROM grupos g 
             JOIN contratos co ON co.grupo_id = g.id
             JOIN pasajeros p ON p.contrato_id = co.id
             WHERE p.nombre = ? AND p.apellido LIKE ?",
            [$userColegio['nombre'], '%' . $userColegio['apellido'] . '%']
        );
        echo "    Grupos encontrados: " . count($grupoRows) . "\n";
        
        $contratoGrupo = null;
        if (!empty($grupoRows)) {
            $contratosGrupo = $contratoModel->getByGrupoId($grupoRows[0]['id']);
            if (!empty($contratosGrupo)) {
                $contratoGrupo = $contratoModel->getFullDetails($contratosGrupo[0]['id']);
                echo "    Contrato grupo: ID={$contratosGrupo[0]['id']}\n";
            }
        }
        
        $data = [
            'user'       => $userColegio,
            'contrato'   => $contratoGrupo,
            'contratos'  => $contratosGrupo ?? [],
            'vuelos'     => $contratoGrupo['vuelos'] ?? [],
            'pasajeros'  => $contratoGrupo['pasajeros'] ?? [],
            'pagos'      => $contratoGrupo['pagos'] ?? [],
            'servicios'  => $contratoGrupo['servicios'] ?? [],
            'vouchers'   => [],
            'pago_completo' => false,
            'grupo'      => $grupoRows[0] ?? null,
            'flash'      => null,
            'csrf_token' => bin2hex(random_bytes(16)),
        ];
        
        ob_start();
        $viewFile2 = BASE_PATH . '/app/views/client/group/dashboard.php';
        extract($data);
        include $viewFile2;
        $output2 = ob_get_clean();
        echo "    OK: Vista grupo renderizada (" . strlen($output2) . " bytes)\n";
        
        $checks2 = [
            'Mi Contrato' => strpos($output2, 'Mi Contrato') !== false,
            'fmoney()'    => strpos($output2, 'S/') !== false || strpos($output2, '$') !== false,
            'progress-bar' => strpos($output2, 'progress-bar') !== false,
            '/client/payments' => strpos($output2, '/client/payments') !== false,
        ];
        foreach ($checks2 as $label => $pass) {
            echo "    " . ($pass ? '✓' : '✗') . " $label\n";
        }
    } else {
        echo "    SKIP: No hay usuario cliente_colegio\n";
    }
} catch (Throwable $e) {
    echo "    FAIL: " . $e->getMessage() . "\n";
    echo "    " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// 8. Probar vista contract.php
echo "\n[8] Probando vista contract.php...\n";
try {
    if (!empty($full)) {
        $data = [
            'user'       => $userRow,
            'contrato'   => $full,
            'csrf_token' => bin2hex(random_bytes(16)),
        ];
        ob_start();
        extract($data);
        include BASE_PATH . '/app/views/client/contract.php';
        $output3 = ob_get_clean();
        echo "    OK: Vista contrato renderizada (" . strlen($output3) . " bytes)\n";
    } else {
        echo "    SKIP: No hay contrato para probar\n";
    }
} catch (Throwable $e) {
    echo "    FAIL: " . $e->getMessage() . "\n";
    echo "    " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// 9. Probar layout completo
echo "\n[9] Probando layout client.php con vista family...\n";
try {
    $data = [
        'title'           => 'Test - Aventuras Travel',
        'user'            => $userRow,
        'cliente'         => $cliente,
        'contrato'        => $full ?? null,
        'contratos'       => $contratos ?? [],
        'vuelos'          => $full['vuelos'] ?? [],
        'pasajeros'       => $full['pasajeros'] ?? [],
        'pagos'           => $full['pagos'] ?? [],
        'servicios'       => $full['servicios'] ?? [],
        'vouchers'        => [],
        'pago_completo'   => false,
        'notificaciones'  => [],
        'csrf_token'      => bin2hex(random_bytes(16)),
        'flash'           => null,
    ];
    extract($data);
    
    // Renderizar vista dentro del layout como lo haría Controller::render
    ob_start();
    include BASE_PATH . '/app/views/client/family/dashboard.php';
    $viewContent = ob_get_clean();
    
    ob_start();
    include BASE_PATH . '/app/views/layouts/client.php';
    $fullPage = ob_get_clean();
    
    echo "    OK: Layout completo renderizado (" . strlen($fullPage) . " bytes)\n";
    
    // Verificar estructura del layout
    $layoutChecks = [
        'DOCTYPE'      => strpos($fullPage, '<!DOCTYPE html>') !== false,
        'sidebar'      => strpos($fullPage, 'Portal del Cliente') !== false,
        'Resumen link' => strpos($fullPage, '/client/dashboard') !== false,
        'Pagos link'   => strpos($fullPage, '/client/payments') !== false,
        'NO /client/flights' => strpos($fullPage, '/client/flights') === false,
        'NO /client/hotels'  => strpos($fullPage, '/client/hotels') === false,
        'logout'       => strpos($fullPage, '/logout') !== false,
        'client_dashboard.css' => strpos($fullPage, 'client_dashboard.css') !== false,
        'NO admin.css' => strpos($fullPage, 'admin.css') === false,
        'app_client.js' => strpos($fullPage, 'app_client.js') !== false,
    ];
    foreach ($layoutChecks as $label => $pass) {
        echo "    " . ($pass ? '✓' : '✗') . " $label\n";
    }
    
} catch (Throwable $e) {
    echo "    FAIL: " . $e->getMessage() . "\n";
    echo "    " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";
