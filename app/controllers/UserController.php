<?php
/**
 * UserController.php - CRUD de usuarios del sistema
 * Aventuras Travel - Admin
 */
class UserController extends Controller {

    private const ROLES_ALLOWED = [
        'cliente_familiar',
        'cliente_grupal',
        'representante',
        'cliente_colegio',
    ];

    /**
     * Listar usuarios con filtros
     */
    public function index(): void {
        $db = Database::getInstance();
        $rol = $_GET['rol'] ?? null;
        $buscar = $_GET['q'] ?? null;

        $sql = "SELECT u.*, c.tipo as tipo_cliente, c.id as cliente_id
                FROM usuarios u
                LEFT JOIN clientes c ON c.usuario_id = u.id
                WHERE u.rol != 'admin'";
        $params = [];

        if ($rol && in_array($rol, self::ROLES_ALLOWED)) {
            $sql .= " AND u.rol = ?";
            $params[] = $rol;
        }
        if ($buscar) {
            $sql .= " AND (u.nombre LIKE ? OR u.apellido LIKE ? OR u.codigo LIKE ? OR u.email LIKE ?)";
            $like = '%' . $buscar . '%';
            $params = array_merge($params, [$like, $like, $like, $like]);
        }
        $sql .= " ORDER BY u.created_at DESC";
        $usuarios = $db->fetchAll($sql, $params);

        // Estadísticas
        $stats = $db->fetchOne("SELECT 
            COUNT(*) as total,
            SUM(activo = 1) as activos,
            SUM(activo = 0) as inactivos,
            SUM(rol = 'cliente_familiar') as familiares,
            SUM(rol IN ('cliente_colegio','representante','cliente_grupal')) as escolares
            FROM usuarios WHERE rol != 'admin'");

        $data = [
            'title'      => 'Gestión de Usuarios - Aventuras Travel',
            'usuarios'   => $usuarios,
            'stats'      => $stats,
            'filtro_rol' => $rol,
            'buscar'     => $buscar,
            'csrf_token' => $this->generateCsrfToken(),
            'flash'      => $this->getFlash(),
        ];
        $this->render('admin/users/index', $data, 'admin');
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): void {
        $db = Database::getInstance();

        // Grupos de colegios (para representante)
        $gruposColegios = $db->fetchAll(
            "SELECT g.id, g.nombre, g.destino, g.institucion
             FROM grupos g
             WHERE g.institucion IS NOT NULL AND g.institucion != ''
             ORDER BY g.nombre ASC"
        );

        // Todos los grupos (para otros roles)
        $grupos = $db->fetchAll(
            "SELECT g.id, g.nombre, g.destino, g.institucion,
                    CASE WHEN g.institucion IS NOT NULL AND g.institucion != '' THEN 'colegio' ELSE 'familiar' END as tipo
             FROM grupos g ORDER BY g.nombre ASC"
        );

        // Contratos disponibles (no cancelados y sin usuario asignado o ya asignados, para referencia)
        $contratos = $db->fetchAll(
            "SELECT c.id, c.codigo, c.grupo_id, c.tipo, c.destino, c.titular_nombre, c.cliente_id, c.estado
             FROM contratos c
             WHERE c.estado != 'cancelado'
             ORDER BY c.codigo ASC"
        );

        $data = [
            'title'          => 'Nuevo Usuario - Aventuras Travel',
            'csrf_token'     => $this->generateCsrfToken(),
            'roles'          => self::ROLES_ALLOWED,
            'grupos'         => $grupos,
            'gruposColegios' => $gruposColegios,
            'contratos'      => $contratos,
        ];
        $this->render('admin/users/create', $data, 'admin');
    }

    /**
     * Guardar nuevo usuario (contraseña auto-generada)
     */
    public function store(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/users/create');
            return;
        }

        $nombre     = trim($this->input('nombre', ''));
        $apellido   = trim($this->input('apellido', ''));
        $email      = trim($this->input('email', ''));
        $telefono   = trim($this->input('telefono', ''));
        $rol        = $this->input('rol', 'cliente_familiar');
        $contratoId = (int) $this->input('contrato_id', 0);

        // Para representante usamos grupo_id_rep; para otros roles, grupo_id
        if ($rol === 'representante') {
            $grupoId = (int) $this->input('grupo_id_rep', 0);
        } else {
            $grupoId = (int) $this->input('grupo_id', 0);
        }

        // Validaciones
        $errors = [];
        if (empty($nombre))   $errors[] = 'El nombre es obligatorio.';
        if (empty($apellido)) $errors[] = 'El apellido es obligatorio.';
        if (!in_array($rol, self::ROLES_ALLOWED)) $errors[] = 'Rol no válido.';
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email no válido.';

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/admin/users/create');
            return;
        }

        $db = Database::getInstance();

        // Auto-generar contraseña segura
        $password = $this->generateSecurePassword();

        // ── Generar código según el ROL ──────────────────────────────────────
        $codigo = '';

        if (in_array($rol, ['cliente_colegio', 'cliente_grupal'])) {
            // El código de usuario ES el código del contrato (ej: CCPA-2026-001)
            if ($contratoId > 0) {
                $contratoRow = $db->fetchOne('SELECT codigo FROM contratos WHERE id = ?', [$contratoId]);
                $codigo = $contratoRow['codigo'] ?? '';
            }
            if (empty($codigo)) {
                // Sin contrato: código escolar secuencial
                $lastId = $db->fetchOne('SELECT MAX(id) as max_id FROM usuarios');
                $nextId = ($lastId['max_id'] ?? 0) + 1;
                $codigo = 'ESC-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
            // Garantizar unicidad
            $dup = $db->fetchOne('SELECT id FROM usuarios WHERE codigo = ?', [$codigo]);
            if ($dup) {
                $codigo .= '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 3));
            }

        } elseif ($rol === 'representante') {
            // Código: R-[ABREV_COLEGIO]-[YY]
            $abrev = '';
            if ($grupoId > 0) {
                $grupoRow = $db->fetchOne('SELECT nombre, institucion FROM grupos WHERE id = ?', [$grupoId]);
                if ($grupoRow) {
                    $src   = !empty($grupoRow['institucion']) ? $grupoRow['institucion'] : $grupoRow['nombre'];
                    $words = preg_split('/\s+/', trim($src));
                    foreach ($words as $w) {
                        $letra = preg_replace('/[^A-Za-z0-9]/', '', $w);
                        if ($letra) $abrev .= strtoupper(substr($letra, 0, 1));
                        if (strlen($abrev) >= 5) break;
                    }
                }
            }
            $abrev  = $abrev ?: 'GRP';
            $year2  = date('y'); // 2 dígitos
            $base   = 'R-' . $abrev . '-' . $year2;
            // Garantizar unicidad
            $dup = $db->fetchOne('SELECT id FROM usuarios WHERE codigo = ?', [$base]);
            if ($dup) {
                $cnt = 2;
                while ($db->fetchOne('SELECT id FROM usuarios WHERE codigo = ?', [$base . '-' . $cnt])) {
                    $cnt++;
                }
                $base .= '-' . $cnt;
            }
            $codigo = $base;

        } else {
            // cliente_familiar → AV-YYYY-XXX
            $lastId = $db->fetchOne('SELECT MAX(id) as max_id FROM usuarios');
            $nextId = ($lastId['max_id'] ?? 0) + 1;
            $codigo = 'AV-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }
        // ────────────────────────────────────────────────────────────────────

        // Insertar usuario
        $userData = [
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $email ?: null,
            'telefono' => $telefono ?: null,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'codigo'   => $codigo,
            'rol'      => $rol,
            'activo'   => 1,
        ];
        $userId = $db->insert('usuarios', $userData);

        // Crear registro en clientes
        $tipoCliente = in_array($rol, ['cliente_colegio', 'representante', 'cliente_grupal']) ? 'colegio' : 'familiar';
        $clienteId = $db->insert('clientes', [
            'usuario_id' => $userId,
            'tipo'       => $tipoCliente,
        ]);

        // Para representante: asignar como representante del grupo de colegio seleccionado
        if ($rol === 'representante' && $grupoId > 0) {
            $db->update('grupos', ['representante_id' => $userId], 'id = ?', [$grupoId]);
        }

        // Para otros roles: vincular al contrato seleccionado
        if ($rol !== 'representante' && $contratoId > 0) {
            $contrato = $db->fetchOne('SELECT id, grupo_id FROM contratos WHERE id = ?', [$contratoId]);
            if ($contrato) {
                $db->update('contratos', ['cliente_id' => $clienteId], 'id = ?', [$contratoId]);
                // Si no se seleccionó grupo pero el contrato tiene uno, asignarlo
                if (!$grupoId && !empty($contrato['grupo_id'])) {
                    $grupoId = (int) $contrato['grupo_id'];
                }
            }
        }

        // Guardar credenciales en sesión para mostrar una sola vez
        $_SESSION['creds_once_' . $userId] = [
            'codigo'   => $codigo,
            'password' => $password,
            'nombre'   => $nombre . ' ' . $apellido,
            'telefono' => $telefono,
            'email'    => $email,
            'rol'      => $rol,
        ];

        // ── Enviar credenciales por WhatsApp ───────────────────────────────
        $whatsappResult = null;
        if (!empty($telefono)) {
            $whatsAppService = new WhatsAppService();

            // Normalizar número de teléfono
            $phoneNormalized = WhatsAppService::normalizePhoneNumber($telefono);

            // Obtener el código del contrato si existe
            $contractCode = '';
            if ($contratoId > 0) {
                $contrato = $db->fetchOne('SELECT codigo FROM contratos WHERE id = ?', [$contratoId]);
                $contractCode = $contrato['codigo'] ?? '';
            }

            // Si no hay código de contrato, usar el código del usuario
            if (empty($contractCode)) {
                $contractCode = $codigo;
            }

            // Enviar credenciales por WhatsApp (plantilla envio_acceso)
            $whatsappResult = $whatsAppService->sendCredentials(
                phoneNumber: $phoneNormalized,
                clientName: $nombre . ' ' . $apellido,
                contractCode: $contractCode,
                password: $password
            );

            // Guardar resultado en sesión para mostrar en la vista
            $_SESSION['creds_once_' . $userId]['whatsapp_result'] = $whatsappResult;

            // Registrar en logs
            if ($whatsappResult['success']) {
                error_log("[UserController::store] ✅ WhatsApp enviado a: $phoneNormalized | Message ID: " . $whatsappResult['message_id']);
            } else {
                error_log("[UserController::store] ❌ Error enviando WhatsApp a: $phoneNormalized | Error: " . $whatsappResult['error']);
            }
        } else {
            error_log("[UserController::store] ⚠️ Sin teléfono: no se puede enviar WhatsApp para usuario $codigo");
        }

        $this->redirect('/admin/users/' . $userId . '/credentials');
    }

    /**
     * Página de credenciales (se muestra una sola vez tras crear o resetear)
     */
    public function credentials(string $id): void {
        $key   = 'creds_once_' . (int)$id;
        $creds = $_SESSION[$key] ?? null;

        if (!$creds) {
            $this->flash('error', 'Las credenciales ya fueron mostradas o el usuario no existe.');
            $this->redirect('/admin/users');
            return;
        }

        // Consumir de sesión (mostrar solo una vez)
        unset($_SESSION[$key]);

        $db   = Database::getInstance();
        $user = $db->fetchOne('SELECT * FROM usuarios WHERE id = ?', [(int)$id]);

        // Extraer resultado de WhatsApp si existe
        $whatsappResult = $creds['whatsapp_result'] ?? null;
        unset($creds['whatsapp_result']); // No pasar en $creds para evitar duplicar info

        $data = [
            'title'           => 'Credenciales Generadas - Aventuras Travel',
            'creds'           => $creds,
            'user'            => $user,
            'id'              => (int)$id,
            'whatsapp_result' => $whatsappResult,
        ];
        $this->render('admin/users/credentials', $data, 'admin');
    }

    /**
     * Formulario de edición
     */
    public function edit(string $id): void {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT u.*, c.tipo as tipo_cliente, c.id as cliente_id FROM usuarios u LEFT JOIN clientes c ON c.usuario_id = u.id WHERE u.id = ? AND u.rol != 'admin'", [(int)$id]);

        if (!$user) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/users');
            return;
        }

        $data = [
            'title'      => 'Editar: ' . $user['codigo'] . ' - Aventuras Travel',
            'usuario'    => $user,
            'roles'      => self::ROLES_ALLOWED,
            'csrf_token' => $this->generateCsrfToken(),
        ];
        $this->render('admin/users/edit', $data, 'admin');
    }

    /**
     * Actualizar usuario
     */
    public function update(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/users/' . $id . '/edit');
            return;
        }

        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM usuarios WHERE id = ? AND rol != 'admin'", [(int)$id]);
        if (!$user) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/users');
            return;
        }

        $nombre   = trim($this->input('nombre', ''));
        $apellido = trim($this->input('apellido', ''));
        $email    = trim($this->input('email', ''));
        $telefono = trim($this->input('telefono', ''));
        $rol      = $this->input('rol', $user['rol']);
        $activo   = (int) $this->input('activo', 1);
        $password = $this->inputRaw('password', '');

        // Validaciones
        $errors = [];
        if (empty($nombre))  $errors[] = 'El nombre es obligatorio.';
        if (empty($apellido)) $errors[] = 'El apellido es obligatorio.';
        if (!in_array($rol, self::ROLES_ALLOWED)) $errors[] = 'Rol no válido.';
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email no válido.';
        if ($password && strlen($password) < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres.';

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/admin/users/' . $id . '/edit');
            return;
        }

        $data = [
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $email ?: null,
            'telefono' => $telefono ?: null,
            'rol'      => $rol,
            'activo'   => $activo,
        ];
        if ($password) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $db->update('usuarios', $data, 'id = ?', [(int)$id]);

        $this->flash('exito', 'Usuario actualizado correctamente.');
        $this->redirect('/admin/users');
    }

    /**
     * Toggle activar/desactivar
     */
    public function toggle(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/users');
            return;
        }

        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM usuarios WHERE id = ? AND rol != 'admin'", [(int)$id]);
        if (!$user) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/users');
            return;
        }

        $newState = $user['activo'] ? 0 : 1;
        $db->update('usuarios', ['activo' => $newState], 'id = ?', [(int)$id]);

        $label = $newState ? 'activado' : 'desactivado';
        $this->flash('exito', "Usuario {$user['codigo']} {$label}.");
        $this->redirect('/admin/users');
    }

    /**
     * Eliminar usuario (soft si tiene datos, hard si no)
     */
    public function delete(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/users');
            return;
        }

        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM usuarios WHERE id = ? AND rol != 'admin'", [(int)$id]);
        if (!$user) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/users');
            return;
        }

        $hasPagos = $db->fetchOne("SELECT COUNT(*) as cnt FROM pagos p INNER JOIN contratos c ON p.contrato_id = c.id INNER JOIN clientes cl ON c.cliente_id = cl.id WHERE cl.usuario_id = ?", [(int)$id]);
        
        if (($hasPagos['cnt'] ?? 0) > 0) {
            $db->update('usuarios', ['activo' => 0], 'id = ?', [(int)$id]);
            $this->flash('exito', "Usuario {$user['codigo']} desactivado (tiene datos asociados).");
        } else {
            $db->delete('clientes', 'usuario_id = ?', [(int)$id]);
            $db->delete('usuarios', 'id = ?', [(int)$id]);
            $this->flash('exito', "Usuario {$user['codigo']} eliminado permanentemente.");
        }
        $this->redirect('/admin/users');
    }

    /**
     * Reset de contraseña (auto-generada) + redirige a página de credenciales
     */
    public function resetPassword(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/users');
            return;
        }

        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM usuarios WHERE id = ? AND rol != 'admin'", [(int)$id]);
        if (!$user) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/users');
            return;
        }

        $newPassword = $this->generateSecurePassword();
        $db->update('usuarios', [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ], 'id = ?', [(int)$id]);

        // Intentar enviar por email si tiene
        if (!empty($user['email'])) {
            try {
                $emailSvc = new EmailService();
                $emailSvc->sendCredentials(
                    $user['email'],
                    $user['nombre'] . ' ' . $user['apellido'],
                    $user['codigo'],
                    $newPassword
                );
            } catch (\Exception $e) {
                error_log('[UserController::resetPassword] Email error: ' . $e->getMessage());
            }
        }

        // ── Enviar nuevas credenciales por WhatsApp ────────────────────────
        $whatsappResult = null;
        $telefono = $user['telefono'] ?? '';
        if (!empty($telefono)) {
            try {
                require_once BASE_PATH . '/app/services/WhatsAppService.php';
                $whatsAppService = new WhatsAppService();
                $phoneNormalized = WhatsAppService::normalizePhoneNumber($telefono);

                // Buscar código de contrato asociado al usuario
                $contractCode = $user['codigo'];
                $clienteRow = $db->fetchOne('SELECT id FROM clientes WHERE usuario_id = ?', [(int)$id]);
                if ($clienteRow) {
                    $contratoRow = $db->fetchOne('SELECT codigo FROM contratos WHERE cliente_id = ? ORDER BY id DESC LIMIT 1', [$clienteRow['id']]);
                    if ($contratoRow && !empty($contratoRow['codigo'])) {
                        $contractCode = $contratoRow['codigo'];
                    }
                }

                $whatsappResult = $whatsAppService->sendCredentials(
                    phoneNumber: $phoneNormalized,
                    clientName: $user['nombre'] . ' ' . $user['apellido'],
                    contractCode: $contractCode,
                    password: $newPassword
                );

                if ($whatsappResult['success']) {
                    error_log("[UserController::resetPassword] ✅ WhatsApp enviado a: $phoneNormalized");
                } else {
                    error_log("[UserController::resetPassword] ❌ WhatsApp error: " . ($whatsappResult['error'] ?? 'desconocido'));
                }
            } catch (\Exception $e) {
                error_log('[UserController::resetPassword] WhatsApp exception: ' . $e->getMessage());
            }
        }

        // Guardar en sesión para mostrar en página de credenciales
        $_SESSION['creds_once_' . (int)$id] = [
            'codigo'   => $user['codigo'],
            'password' => $newPassword,
            'nombre'   => $user['nombre'] . ' ' . $user['apellido'],
            'telefono' => $telefono,
            'email'    => $user['email'] ?? '',
            'rol'      => $user['rol'],
            'reset'    => true,
            'whatsapp_result' => $whatsappResult,
        ];

        $this->redirect('/admin/users/' . (int)$id . '/credentials');
    }

    /**
     * Reenviar credenciales por WhatsApp (resetea contraseña + envía)
     * POST /admin/users/{id}/send-whatsapp
     */
    public function sendWhatsApp(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/users');
            return;
        }

        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM usuarios WHERE id = ? AND rol != 'admin'", [(int)$id]);
        if (!$user) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/users');
            return;
        }

        $telefono = $user['telefono'] ?? '';
        if (empty($telefono)) {
            $this->flash('error', 'El usuario no tiene teléfono registrado. No se puede enviar WhatsApp.');
            $this->redirect('/admin/users');
            return;
        }

        // Generar nueva contraseña
        $newPassword = $this->generateSecurePassword();
        $db->update('usuarios', [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ], 'id = ?', [(int)$id]);

        // Buscar código de contrato asociado
        $contractCode = $user['codigo'];
        $clienteRow = $db->fetchOne('SELECT id FROM clientes WHERE usuario_id = ?', [(int)$id]);
        if ($clienteRow) {
            $contratoRow = $db->fetchOne('SELECT codigo FROM contratos WHERE cliente_id = ? ORDER BY id DESC LIMIT 1', [$clienteRow['id']]);
            if ($contratoRow && !empty($contratoRow['codigo'])) {
                $contractCode = $contratoRow['codigo'];
            }
        }

        // Enviar por WhatsApp
        require_once BASE_PATH . '/app/services/WhatsAppService.php';
        $whatsAppService = new WhatsAppService();
        $phoneNormalized = WhatsAppService::normalizePhoneNumber($telefono);

        $result = $whatsAppService->sendCredentials(
            phoneNumber: $phoneNormalized,
            clientName: $user['nombre'] . ' ' . $user['apellido'],
            contractCode: $contractCode,
            password: $newPassword
        );

        // Guardar en sesión para mostrar credenciales
        $_SESSION['creds_once_' . (int)$id] = [
            'codigo'          => $user['codigo'],
            'password'        => $newPassword,
            'nombre'          => $user['nombre'] . ' ' . $user['apellido'],
            'telefono'        => $telefono,
            'email'           => $user['email'] ?? '',
            'rol'             => $user['rol'],
            'reset'           => true,
            'whatsapp_result' => $result,
        ];

        $this->redirect('/admin/users/' . (int)$id . '/credentials');
    }

    /**
     * Genera una contraseña segura y legible
     */
    private function generateSecurePassword(): string {
        $chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $len   = strlen($chars);
        $pwd   = '';
        for ($i = 0; $i < 10; $i++) {
            $pwd .= $chars[random_int(0, $len - 1)];
        }
        return $pwd;
    }
}

