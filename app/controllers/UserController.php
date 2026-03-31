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
        $data = [
            'title'      => 'Nuevo Usuario - Aventuras Travel',
            'csrf_token' => $this->generateCsrfToken(),
            'roles'      => self::ROLES_ALLOWED,
        ];
        $this->render('admin/users/create', $data, 'admin');
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token CSRF inválido.');
            $this->redirect('/admin/users/create');
            return;
        }

        $nombre   = trim($this->input('nombre', ''));
        $apellido = trim($this->input('apellido', ''));
        $email    = trim($this->input('email', ''));
        $telefono = trim($this->input('telefono', ''));
        $rol      = $this->input('rol', 'cliente_familiar');
        $password = $this->inputRaw('password', '');

        // Validaciones
        $errors = [];
        if (empty($nombre))   $errors[] = 'El nombre es obligatorio.';
        if (empty($apellido))  $errors[] = 'El apellido es obligatorio.';
        if (empty($password) || strlen($password) < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        if (!in_array($rol, self::ROLES_ALLOWED)) $errors[] = 'Rol no válido.';

        if ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email no válido.';
            }
        }

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/admin/users/create');
            return;
        }

        $db = Database::getInstance();

        // Generar código único
        $lastId = $db->fetchOne('SELECT MAX(id) as max_id FROM usuarios');
        $nextId = ($lastId['max_id'] ?? 0) + 1;
        $year = date('Y');
        $codigo = "AV-{$year}-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);

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

        // Crear registro en clientes si es un rol de cliente
        $tipoCliente = in_array($rol, ['cliente_colegio', 'representante', 'cliente_grupal']) ? 'colegio' : 'familiar';
        $db->insert('clientes', [
            'usuario_id' => $userId,
            'tipo'       => $tipoCliente,
        ]);

        $this->flash('exito', "Usuario <strong>{$codigo}</strong> creado. Contraseña: <code>{$password}</code>");
        $this->redirect('/admin/users');
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
     * Eliminar usuario (soft: desactiva, ó hard delete si no tiene datos)
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

        // Revisar si tiene pagos/contratos asociados
        $hasPagos = $db->fetchOne("SELECT COUNT(*) as cnt FROM pagos p INNER JOIN contratos c ON p.contrato_id = c.id INNER JOIN clientes cl ON c.cliente_id = cl.id WHERE cl.usuario_id = ?", [(int)$id]);
        
        if (($hasPagos['cnt'] ?? 0) > 0) {
            // Soft delete: desactivar
            $db->update('usuarios', ['activo' => 0], 'id = ?', [(int)$id]);
            $this->flash('exito', "Usuario {$user['codigo']} desactivado (tiene datos asociados).");
        } else {
            // Hard delete: eliminar cliente y usuario
            $db->delete('clientes', 'usuario_id = ?', [(int)$id]);
            $db->delete('usuarios', 'id = ?', [(int)$id]);
            $this->flash('exito', "Usuario {$user['codigo']} eliminado permanentemente.");
        }
        $this->redirect('/admin/users');
    }

    /**
     * Reset de contraseña
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

        $newPassword = bin2hex(random_bytes(6)); // 12 chars hex
        $db->update('usuarios', [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ], 'id = ?', [(int)$id]);

        // Enviar por email si tiene
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

        $this->flash('exito', "Contraseña reseteada para {$user['codigo']}: <code>{$newPassword}</code>");
        $this->redirect('/admin/users');
    }
}
