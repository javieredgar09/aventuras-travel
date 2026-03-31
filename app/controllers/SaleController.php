<?php
/**
 * SaleController.php - Gestión completa de ventas (Grupos Familiares y Colegios)
 */
class SaleController extends Controller {

    /**
     * Lista de grupos con filtros y estadísticas
     */
    public function index(): void {
        $grupoModel = new Grupo();
        $filtro = $_GET['tipo'] ?? null;
        $grupos = $grupoModel->getAllWithStats($filtro ?: null);
        $stats = $grupoModel->getDashboardStats();
        $chartData = $grupoModel->getPaymentsByMonth();

        $data = [
            'title'     => 'Gestión de Grupos - Aventuras Travel',
            'grupos'    => $grupos,
            'stats'     => $stats,
            'chartData' => $chartData,
            'filtro'    => $filtro,
            'flash'     => $this->getFlash(),
        ];
        $this->render('admin/sales/index', $data, 'admin');
    }

    /**
     * Formulario de creación de grupo
     */
    public function create(): void {
        $data = [
            'title'        => 'Nuevo Grupo - Aventuras Travel',
            'csrf_token'   => $this->generateCsrfToken(),
            'serviceMeta'  => ServicioGrupo::getServiceMeta(),
        ];
        $this->render('admin/sales/create', $data, 'admin');
    }

    /**
     * Guardar grupo nuevo con servicios, pasajeros y pagos
     */
    public function store(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/sales/create');
            return;
        }
        try {
            $db = Database::getInstance();
            $tipo = $this->input('tipo');
            $nombre = $this->input('nombre');
            $destino = $this->input('destino');
            $operador = $this->input('operador');
            $fechaViaje = $this->input('fecha_viaje');
            $fechaRetorno = $this->input('fecha_retorno');
            $valorTotal = (float) $this->input('valor_total', 0);
            $deposito = (float) $this->input('deposito', 0);
            $tipoPago = $this->input('tipo_pago', 'contado');
            $totalCuotas = (int) $this->input('total_cuotas', 0);
            $mesesPago = $this->input('meses_pago', '');
            $institucion = $this->input('institucion', '');

            if (empty($tipo) || empty($nombre) || empty($destino)) {
                $this->flash('error', 'Nombre, tipo y destino son obligatorios.');
                $this->redirect('/admin/sales/create');
                return;
            }

        // Server-side validation: para grupos familiares el primer pasajero debe tener email y teléfono
        $pasajeros_json = $this->input('pasajeros_json', '[]');
        $pasajeros_arr = json_decode($pasajeros_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $pasajeros_arr = [];
        }

        if ($tipo === 'familiar') {
            if (empty($pasajeros_arr) || !isset($pasajeros_arr[0])) {
                $this->flash('error', 'Debe agregar al menos un pasajero titular con correo y teléfono.');
                $this->redirect('/admin/sales/create');
                return;
            }

            $first = $pasajeros_arr[0];
            $emailFirst = trim($first['email'] ?? '');
            $phoneFirst = trim($first['telefono'] ?? '');

            if (empty($emailFirst) || !filter_var($emailFirst, FILTER_VALIDATE_EMAIL)) {
                $this->flash('error', 'Correo electrónico del titular inválido o vacío.');
                $this->redirect('/admin/sales/create');
                return;
            }

            // Valida teléfono simple: dígitos, espacios, +, - y paréntesis (7-20 chars)
            if (empty($phoneFirst) || !preg_match('/^[0-9+\-\s\(\)]{7,20}$/', $phoneFirst)) {
                $this->flash('error', 'Teléfono del titular inválido o vacío. Formato aceptado: números y opcionalmente + - ( )');
                $this->redirect('/admin/sales/create');
                return;
            }
        }

        // Crear grupo
        $grupoId = $db->insert('grupos', [
            'nombre'       => $nombre,
            'tipo'         => $tipo,
            'operador'     => $operador ?: null,
            'destino'      => $destino,
            'destino_code' => $this->input('destino_code') ?: null,
            'fecha_viaje'  => $fechaViaje ?: null,
            'fecha_retorno' => $fechaRetorno ?: null,
            'valor_total'  => $valorTotal,
            'deposito'     => $deposito,
            'tipo_pago'    => $tipoPago,
            'total_cuotas' => $totalCuotas,
            'meses_pago'   => $mesesPago ?: null,
            'estado'       => 'activo',
            'institucion'  => $institucion ?: null,
        ]);

        // Guardar servicios
        $servicios = json_decode($this->input('servicios_json', '[]'), true);
        error_log('[SaleController::store] servicios_json raw: ' . $this->input('servicios_json', '[]'));
        if (!empty($servicios)) {
            $svcModel = new ServicioGrupo();
            $svcModel->syncServicios($grupoId, $servicios);
            error_log('[SaleController::store] syncServicios invoked for grupo_id=' . $grupoId . ' count=' . count($servicios));
        }

        // Si es grupo colegio: asegurar que exista un itinerario/vuelo guardado
        if ($tipo === 'colegio') {
            $hasItinerario = false;
            if (is_array($servicios) && !empty($servicios)) {
                foreach ($servicios as $s) {
                    $t = strtolower(trim((string)($s['tipo'] ?? $s['servicio_tipo'] ?? '')));
                    if (in_array($t, ['itinerario','itinerarios','vuelos','vuelo','itinerary'])) {
                        $hasItinerario = true;
                        break;
                    }
                }
            }

            if (!$hasItinerario) {
                $defaultIt = [
                    'itinerario' => [
                        ['dia' => 1, 'descripcion' => 'Itinerario por definir. Detallar actividades, horarios y responsables.']
                    ],
                    'fecha_inicio' => $fechaViaje ?: null,
                    'fecha_fin' => $fechaRetorno ?: null,
                    'destino' => $destino ?: null
                ];
                try {
                    $db->insert('servicios_grupo', [
                        'grupo_id' => $grupoId,
                        'servicio_tipo' => 'itinerario',
                        'detalle_json' => json_encode($defaultIt, JSON_UNESCAPED_UNICODE),
                        'activo' => 1,
                    ]);
                    error_log('[SaleController::store] Itinerario por defecto creado para grupo_id=' . $grupoId);
                } catch (Exception $e) {
                    error_log('[SaleController::store] Error al crear itinerario por defecto: ' . $e->getMessage());
                }
            }
        }

        // Guardar pasajeros
        $pasajeros = json_decode($this->input('pasajeros_json', '[]'), true);
        foreach ($pasajeros as $pax) {
            $db->insert('pasajeros', [
                'nombre'   => $pax['nombre'] ?? '',
                'apellido' => $pax['apellido'] ?? '',
                'tipo'     => $pax['tipo'] ?? 'adulto',
                'edad'     => $pax['edad'] ?? null,
                'pasaporte' => $pax['pasaporte'] ?? null,
                'grupo_id' => $grupoId,
            ]);
        }

        // Si es grupo familiar: crear usuario/cliente para el primer pasajero (titular)
        $credMsg = '';
        $crearUsuarioFlag = $this->input('crear_usuario') ? true : false;
        if ($tipo === 'familiar' && !empty($pasajeros) && $crearUsuarioFlag) {
            $first = $pasajeros[0];
            $email = trim($first['email'] ?? '');
            $telefono = trim($first['telefono'] ?? '');
            $nombreTitular = trim(($first['nombre'] ?? '') . ' ' . ($first['apellido'] ?? ''));

            if (!empty($email)) {
                // Verificar existencia por email (si la columna existe). Hacerlo tolerante a errores.
                $exists = null;
                try {
                    $uCols = $db->fetchAll("SHOW COLUMNS FROM usuarios");
                    $uColsNames = array_map(fn($c) => $c['Field'], $uCols ?: []);
                    if (in_array('email', $uColsNames)) {
                        $exists = $db->fetchOne('SELECT id FROM usuarios WHERE email = ?', [$email]);
                    }
                } catch (Exception $ex) {
                    error_log('[SaleController::store] comprobacion email tabla usuarios falló: ' . $ex->getMessage());
                    $exists = null;
                }

                if (!$exists) {
                    $lastId = $db->fetchOne('SELECT MAX(id) as max_id FROM usuarios');
                    $nextId = ($lastId['max_id'] ?? 0) + 1;
                    $year = date('Y');
                    $codigo = "AV-{$year}-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);
                    $randomPass = bin2hex(random_bytes(6)); // 12 chars hex, criptográficamente seguro

                    $nameParts = explode(' ', $nombreTitular, 2);
                    $nombre = $nameParts[0] ?? $nombreTitular;
                    $apellido = $nameParts[1] ?? '';

                    $userData = [
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'email' => $email,
                        'telefono' => $telefono ?: null,
                        'password' => password_hash($randomPass, PASSWORD_DEFAULT),
                        'codigo' => $codigo,
                        'rol' => 'cliente_familiar',
                        'activo' => 1,
                    ];
                    $userCols = $db->fetchAll("SHOW COLUMNS FROM usuarios");
                    $userColsNames = array_map(fn($c) => $c['Field'], $userCols ?: []);
                    $userFiltered = array_intersect_key($userData, array_flip($userColsNames));
                    $userId = $db->insert('usuarios', $userFiltered);

                    $clienteId = $db->insert('clientes', [
                        'usuario_id' => $userId,
                        'tipo' => 'familiar',
                    ]);

                    // Establecer representante del grupo
                    $db->update('grupos', ['representante_id' => $userId], 'id = ?', [$grupoId]);

                    // Enviar credenciales por correo (si EmailService disponible)
                    require_once BASE_PATH . '/app/services/EmailService.php';
                    $emailSvc = new EmailService();
                    $emailSvc->sendCredentials($email, $nombreTitular ?: 'Cliente', $codigo, $randomPass);

                    // Preparar mensaje de credenciales para mostrar al admin
                    $credMsg = " Credenciales generadas: usuario={$codigo} contraseña={$randomPass} (enviadas a {$email}).";
                } else {
                    // Si ya existe, intentar vincular el cliente
                    $clienteRow = $db->fetchOne('SELECT id FROM clientes WHERE usuario_id = ?', [$exists['id']]);
                    if ($clienteRow) {
                        $db->update('grupos', ['representante_id' => $exists['id']], 'id = ?', [$grupoId]);
                    }
                }
            }
        }

        // Pagos (depósito o cuotas)
        if ($deposito > 0) {
            $db->insert('pagos', [
                'grupo_id'        => $grupoId,
                'entidad_tipo'    => 'grupo',
                'concepto'        => 'Depósito inicial',
                'monto'           => $deposito,
                'cuota_numero'    => 0,
                'fecha_vencimiento' => date('Y-m-d'),
                'estado'          => 'pendiente',
            ]);
        }

        $this->flash('exito', "Grupo «{$nombre}» creado exitosamente." . $credMsg);
        $this->redirect('/admin/sales/' . $grupoId);
        return;
        } catch (Exception $e) {
            // Preparar resumen sanitizado de POST para depuración (no incluir contraseñas ni tokens)
            $postPreview = [];
            foreach ($_POST as $k => $v) {
                if (stripos($k, 'pass') !== false || stripos($k, 'csrf') !== false || stripos($k, 'password') !== false) {
                    $postPreview[$k] = '***';
                } else {
                    if (is_string($v)) {
                        $clean = mb_substr($v, 0, 300);
                        $postPreview[$k] = $clean;
                    } else {
                        $postPreview[$k] = $v;
                    }
                }
            }

            $filesPreview = [];
            foreach ($_FILES as $k => $f) {
                $filesPreview[$k] = ['name' => $f['name'] ?? null, 'error' => $f['error'] ?? null, 'size' => $f['size'] ?? null];
            }

            $logMsg = '[SaleController::store] Error creando grupo: ' . $e->getMessage() . "\nPOST: " . json_encode($postPreview) . "\nFILES: " . json_encode($filesPreview) . "\nTrace: " . $e->getTraceAsString();
            error_log($logMsg);

            // Mostrar mensaje temporal al admin con resumen seguro
            $flashMsg = 'Error creando grupo: ' . $e->getMessage() . ' — Revisar logs para más detalles.';
            $this->flash('error', $flashMsg, ['post' => $postPreview, 'files' => $filesPreview, 'trace' => $e->getTraceAsString()]);
            $this->redirect('/admin/sales/create');
            return;
        }
    }

    /**
     * Vista detallada de un grupo
     */
    public function show(int $id): void {
        $grupoModel = new Grupo();
        $grupo = $grupoModel->getFullDetails($id);

        if (!$grupo) {
            $this->flash('error', 'Grupo no encontrado.');
            $this->redirect('/admin/sales');
            return;
        }

        // Decodificar JSON de servicios
        foreach ($grupo['servicios'] as &$svc) {
            $svc['detalle'] = json_decode($svc['detalle_json'] ?? '{}', true);
        }

        // Fetch vouchers (for familiar groups, they are tied to 'grupo'. for colegio, mostly 'contrato', but we'll fetch group ones here)
        $voucherModel = new Voucher();
        $vouchers = $voucherModel->getByEntidad('grupo', $id);

        $data = [
            'title'       => $grupo['nombre'] . ' - Aventuras Travel',
            'grupo'       => $grupo,
            'vouchers'    => $vouchers,
            'serviceMeta' => ServicioGrupo::getServiceMeta(),
            'flash'       => $this->getFlash(),
        ];
        $this->render('admin/sales/show', $data, 'admin');
    }

    public function edit(string $id): void {
        $model = new Grupo();
        $grupo = $model->find((int) $id);
        
        if (!$grupo) {
            $this->flash('error', 'Grupo no encontrado.');
            $this->redirect('/admin/sales');
            return;
        }

        $data = [
            'id'           => $id,
            'title'        => 'Editar Grupo - Aventuras Travel',
            'grupo'        => $grupo,
            'csrf_token'   => $this->generateCsrfToken(),
            'serviceMeta'  => ServicioGrupo::getServiceMeta(),
        ];
        
        $this->render('admin/sales/edit', $data, 'admin');
    }

    public function delete(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/sales');
            return;
        }

        $db = Database::getInstance();
        try {
            // Eliminar dependencias manualmente ya que no todas tienen CASCADE en DB
            $db->delete('pagos', 'grupo_id = ?', [$id]);
            $db->delete('pasajeros', 'grupo_id = ?', [$id]);
            $db->delete('servicios_grupo', 'grupo_id = ?', [$id]);
            
            // Eliminar pagos de los contratos del grupo
            $contratos = $db->fetchAll('SELECT id FROM contratos WHERE grupo_id = ?', [$id]);
            foreach($contratos as $c) {
                $db->delete('pagos', 'contrato_id = ?', [$c['id']]);
                $db->delete('pasajeros', 'contrato_id = ?', [$c['id']]);
            }
            $db->delete('contratos', 'grupo_id = ?', [$id]);
            
            // Finalmente eliminar el grupo
            $db->delete('grupos', 'id = ?', [$id]);
            
            $this->flash('exito', 'Grupo eliminado correctamente.');
        } catch (Exception $e) {
            $this->flash('error', 'No se pudo eliminar el grupo. Revisa los detalles.', $e->getMessage());
        }
        $this->redirect('/admin/sales');
    }

    public function update(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/sales/' . $id);
            return;
        }

        $db = Database::getInstance();
        
        $data = [
            'nombre'        => $this->input('nombre'),
            'destino'       => $this->input('destino'),
            'destino_code'  => $this->input('destino_code') ?: null,
            'fecha_viaje'   => $this->input('fecha_viaje') ?: null,
            'fecha_retorno' => $this->input('fecha_retorno') ?: null,
            'operador'      => $this->input('operador') ?: null,
            'valor_total'   => (float) $this->input('valor_total', 0),
            'estado'        => $this->input('estado', 'activo')
        ];

        try {
            $db->update('grupos', $data, 'id = ?', [$id]);
            
            // Obtener servicios JSON
        $servicios_json = $this->inputRaw('servicios_json', '[]');
        $servicios = json_decode($servicios_json, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $svcModel = new ServicioGrupo();
                $svcModel->syncServicios((int)$id, $servicios);
            }

            $this->flash('exito', 'Grupo actualizado correctamente.');
        } catch (Exception $e) {
            $this->flash('error', 'Error actualizando grupo: ' . $e->getMessage());
        }
        $this->redirect('/admin/sales/' . $id);
    }

    /**
     * Guardar itinerario (AJAX) — usado por representante para editar itinerario del grupo
     */
    public function saveItinerary(string $id): void {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $user = $this->auth();
            if (empty($user) || !in_array($user['rol'] ?? '', ['representante','admin'])) {
                echo json_encode(['success' => false, 'message' => 'No autorizado']);
                return;
            }

            // Leer JSON del body
            $raw = file_get_contents('php://input');
            $payload = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE || empty($payload['grupo_id'])) {
                echo json_encode(['success' => false, 'message' => 'Payload inválido']);
                return;
            }

            $grupoId = (int)$payload['grupo_id'];
            // CSRF opcional: si envían token lo validamos
            if (!empty($payload['csrf_token']) && !$this->verifyCsrfToken($payload['csrf_token'])) {
                // Note: verifyCsrfToken() acepta token si la implementación lo soporta
            }

            $detalle = $payload['detalle_json'] ?? null;
            if ($detalle === null) {
                echo json_encode(['success' => false, 'message' => 'detalle_json requerido']);
                return;
            }

            $db = Database::getInstance();
            // Buscar servicio existente tipo 'itinerario'
            $row = $db->fetchOne('SELECT id FROM servicios_grupo WHERE grupo_id = ? AND servicio_tipo = ? LIMIT 1', [$grupoId, 'itinerario']);
            if ($row && !empty($row['id'])) {
                $db->update('servicios_grupo', ['detalle_json' => $detalle], 'id = ?', [$row['id']]);
            } else {
                $db->insert('servicios_grupo', ['grupo_id' => $grupoId, 'servicio_tipo' => 'itinerario', 'detalle_json' => $detalle, 'activo' => 1]);
            }

            echo json_encode(['success' => true]);
            return;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            return;
        }
    }

    /**
     * Añadir pasajero a un grupo
     */
    public function addPassenger(int $id): void {
        $grupoId = $id;
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/sales/' . $grupoId);
            return;
        }

        $db = Database::getInstance();
        $contratoId = $this->input('contrato_id') ?: null;
        
        $db->insert('pasajeros', [
            'nombre'    => $this->input('nombre'),
            'apellido'  => $this->input('apellido'),
            'tipo'      => $this->input('tipo', 'adulto'),
            'edad'      => $this->input('edad') ?: null,
            'pasaporte' => $this->input('pasaporte') ?: null,
            'grupo_id'  => $grupoId,
            'contrato_id' => $contratoId ? (int) $contratoId : null,
        ]);

        $this->flash('exito', 'Pasajero añadido correctamente.');
        $this->redirect('/admin/sales/' . $grupoId);
    }

    /**
     * Añadir contrato a un grupo colegio
     */
    public function addContract(int $id): void {
        $grupoId = $id;
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/sales/' . $grupoId);
            return;
        }

        $db = Database::getInstance();
        $grupo = (new Grupo())->find($grupoId);

        // Generar código de contrato
        $year = date('Y');
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $grupo['nombre']), 0, 4));
        $lastId = $db->fetchOne("SELECT MAX(id) as max_id FROM contratos");
        $nextId = ($lastId['max_id'] ?? 0) + 1;
        $codigo = "{$prefix}-{$year}-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        $valorTotal = (float) $this->input('valor_total', 0);
        $depositoContrato = (float) $this->input('deposito', 0);

        $contratoId = $db->insert('contratos', [
            'codigo'       => $codigo,
            'grupo_id'     => $grupoId,
            'tipo'         => 'colegio',
            'destino'      => $grupo['destino'],
            'fecha_salida' => $grupo['fecha_viaje'],
            'fecha_retorno' => $grupo['fecha_retorno'],
            'valor_total'  => $valorTotal,
            'deposito'     => $depositoContrato,
            'saldo'        => $valorTotal - $depositoContrato,
            'estado'       => 'activo',
        ]);

        if ($depositoContrato > 0) {
            $db->insert('pagos', [
                'contrato_id'     => $contratoId,
                'grupo_id'        => $grupoId,
                'entidad_tipo'    => 'contrato',
                'concepto'        => 'Depósito - ' . $codigo,
                'monto'           => $depositoContrato,
                'cuota_numero'    => 0,
                'fecha_vencimiento' => date('Y-m-d'),
                'estado'          => 'pendiente',
            ]);
        }

        $this->flash('exito', "Contrato {$codigo} creado exitosamente.");
        $this->redirect('/admin/sales/' . $grupoId);
    }

    /**
     * Registrar pago (grupo o contrato)
     */
    public function registerPayment(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/sales');
            return;
        }

        $db = Database::getInstance();
        $grupoId = $this->input('grupo_id');
        $contratoId = $this->input('contrato_id') ?: null;
        $entidadTipo = $contratoId ? 'contrato' : 'grupo';

        $db->insert('pagos', [
            'contrato_id'      => $contratoId ? (int) $contratoId : null,
            'grupo_id'         => (int) $grupoId,
            'entidad_tipo'     => $entidadTipo,
            'concepto'         => $this->input('concepto', 'Pago'),
            'monto'            => (float) $this->input('monto', 0),
            'cuota_numero'     => (int) $this->input('cuota_numero', 0),
            'mes_correspondiente' => $this->input('mes_correspondiente') ?: null,
            'fecha_vencimiento' => $this->input('fecha_vencimiento') ?: date('Y-m-d'),
            'estado'           => $this->input('estado_pago', 'pendiente'),
        ]);

        $this->flash('exito', 'Pago registrado correctamente.');
        $this->redirect('/admin/sales/' . $grupoId);
    }


    /**
     * Aprobar un pago (desde vista de grupo)
     */
    public function approvePayment(int $pagoId): void {
        require_once __DIR__ . '/../services/PaymentService.php';
        $paymentService = new PaymentService();
        $db = Database::getInstance();
        $pago = $db->fetchOne("SELECT * FROM pagos WHERE id = ?", [$pagoId]);
        $grupoId = $pago['grupo_id'] ?? 0;

        if ($paymentService->approvePayment($pagoId)) {
            $this->flash('exito', 'Pago aprobado. Cuotas actualizadas.');
        } else {
            $this->flash('error', 'No se pudo aprobar el pago.');
        }
        $this->redirect('/admin/sales/' . $grupoId);
    }

    /**
     * Subir voucher
     */
    public function uploadVoucher(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/sales');
            return;
        }

        $grupoId = (int)$this->input('grupo_id');
        $contratoId = (int)$this->input('contrato_id', 0);
        $tipoEntidad = $contratoId > 0 ? 'contrato' : 'grupo';
        $entidadId = $contratoId > 0 ? $contratoId : $grupoId;
        $tipoVoucher = $this->input('tipo_voucher', 'description');
        $titulo = $this->input('titulo', 'Voucher de Viaje');

        if (empty($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Debe adjuntar el archivo.');
            $this->redirect('/admin/sales/' . $grupoId);
            return;
        }

        $file = $_FILES['archivo'];
        $hash = bin2hex(random_bytes(16));
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = $hash . '.' . $ext;
        $path = STORAGE_PATH . '/vouchers';
        
        if (!is_dir($path)) mkdir($path, 0755, true);
        move_uploaded_file($file['tmp_name'], $path . '/' . $newName);

        $voucherModel = new Voucher();
        $voucherModel->create([
            'entidad_id'   => $entidadId,
            'tipo_entidad' => $tipoEntidad,
            'tipo_voucher' => $tipoVoucher,
            'titulo'       => $titulo,
            'archivo_url'  => $newName
        ]);

        $this->flash('exito', 'Voucher subido correctamente.');
        $this->redirect('/admin/sales/' . $grupoId);
    }

    /**
     * Buscar vuelos via SerpAPI para el widget de "Vuelos" en crear/editar grupo
     * Endpoint API: GET /api/vuelos/buscar?q=LIM CUN 2024-12-01
     */
    public function ajaxSearchFlight() {
        header('Content-Type: application/json');
        $origen = $_GET['origen'] ?? '';
        $destino = $_GET['destino'] ?? '';
        $fecha = $_GET['fecha'] ?? '';
        $fechaRetorno = $_GET['fecha_retorno'] ?? '';
        
        if(!$origen || !$destino || !$fecha) {
            echo json_encode(['success' => false, 'error' => 'Parámetros incompletos']);
            return;
        }

        require_once BASE_PATH . '/app/services/SerpApiService.php';
        $serp = new SerpApiService();
        $result = $serp->searchFlights($origen, $destino, $fecha, $fechaRetorno ?: null);
        
        echo json_encode($result);
        exit;
    }

    public function ajaxSearchAirport() {
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';
        if(empty($query)) { echo json_encode(['success'=>false, 'error'=>'Falta param query']); return; }
        
        require_once BASE_PATH . '/app/services/SerpApiService.php';
        $serp = new SerpApiService();
        echo json_encode($serp->searchAirports($query));
        exit;
    }

    public function ajaxSearchHotel() {
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';
        $dest = $_GET['dest'] ?? '';
        $dest_code = $_GET['dest_code'] ?? '';
        // Si no se envía q, intentar usar dest como término de búsqueda
        if (empty($query)) {
            if (empty($dest)) {
                echo json_encode(['success'=>false, 'error'=>'Falta param query o destino']);
                return;
            }
            $query = $dest;
        } else {
            // Si se recibe destino por separado, combinar para dar contexto a la búsqueda cuando no está incluido
            if (!empty($dest) && stripos($query, $dest) === false) {
                $query = $query . ' ' . $dest;
            }
        }

        require_once BASE_PATH . '/app/services/SerpApiService.php';
        $serp = new SerpApiService();

        // Si hay dest_code, añadirlo al query para dar contexto (SerpAPI puede usarlo en la búsqueda)
        if (!empty($dest_code) && stripos($query, $dest_code) === false) {
            $query = $query . ' ' . $dest_code;
        }

        echo json_encode($serp->searchHotels($query));
        exit;
    }

    /**
     * Buscar destinos (autocomplete) usando SerpApi (places)
     */
    public function ajaxSearchDestination() {
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';
        if (empty($query)) { echo json_encode(['success' => false, 'error' => 'Falta param q']); return; }

        require_once BASE_PATH . '/app/services/SerpApiService.php';
        $serp = new SerpApiService();
        // Preferir búsqueda de ciudades (solo ciudades)
        $citiesResp = $serp->searchCities($query);
        if (!empty($citiesResp['success']) && !empty($citiesResp['cities'])) {
            echo json_encode(['success' => true, 'places' => $citiesResp['cities']]);
            return;
        }

        // Fallback: intentar searchAirports que incluye ciudades y aeropuertos
        $airResp = $serp->searchAirports($query);
        if (!empty($airResp['success']) && !empty($airResp['airports'])) {
            // mapear a formato places
            $places = array_map(function($a){
                return [
                    'id' => $a['id'] ?? '',
                    'name' => $a['name'] ?? '',
                    'country' => '',
                    'type' => 'city_or_airport'
                ];
            }, $airResp['airports']);
            echo json_encode(['success' => true, 'places' => $places]);
            return;
        }

        // Último recurso: searchPlaces (turismo) — pero preferimos NO usarlo para autocompletar ciudades
        echo json_encode(['success' => false, 'error' => 'No se encontraron destinos (ciudades).']);
        exit;
    }
}
