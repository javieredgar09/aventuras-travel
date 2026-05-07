<?php
/**
 * AuthController.php - Login unificado (admin + clientes)
 */
class AuthController extends Controller {

    /**
     * Mostrar formulario de login unificado
     */
    public function showLogin(): void {
        if ($this->auth()) {
            $user = $this->auth();
            if ($user['rol'] === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/');
            }
            return;
        }
        $data = [
            'title'      => 'Iniciar Sesión - Aventuras Travel',
            'csrf_token' => $this->generateCsrfToken(),
            'flash'      => $this->getFlash(),
        ];
        $this->render('auth/login', $data, 'auth');
    }

    /**
     * Procesar login unificado – detecta admin o cliente
     */
    public function login(): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->redirect('/login');
            return;
        }

        $codigo = trim($this->input('codigo', ''));
        $password = $this->inputRaw('password') ?? '';

        if (empty($codigo) || empty($password)) {
            $this->flash('error', 'Ingresa tu código y contraseña.');
            $this->redirect('/login');
            return;
        }

        // Buscar usuario por código (funciona para admin Y clientes)
        $user = $this->db->fetchOne(
            "SELECT * FROM usuarios WHERE codigo = ? AND activo = 1",
            [$codigo]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            $this->flash('error', 'Credenciales inválidas. Verifica tu código y contraseña.');
            $this->redirect('/login');
            return;
        }

        // Actualizar último acceso
        $this->db->update('usuarios', ['ultimo_acceso' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

        // Crear sesión
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'codigo'   => $user['codigo'],
            'nombre'   => $user['nombre'],
            'apellido' => $user['apellido'],
            'email'    => $user['email'],
            'rol'      => $user['rol'],
        ];

        session_regenerate_id(true);
        $this->redirectByRole($user['rol']);
    }

    /**
     * Redirigir según rol del usuario
     */
    private function redirectByRole(string $rol): void {
        switch ($rol) {
            case 'admin':
                $this->redirect('/admin/dashboard');
                break;
            case 'representante':
                $this->redirect('/leader/dashboard');
                break;
            case 'cliente_colegio':
                $this->redirect('/');
                break;
            default: // cliente_familiar
                $this->redirect('/client/dashboard');
                break;
        }
    }

    /**
     * Mantener ruta legacy /admin/login → redirige al login unificado
     */
    public function showAdminLogin(): void {
        $this->redirect('/login');
    }

    public function adminLogin(): void {
        $this->redirect('/login');
    }

    /**
     * Cerrar sesión de forma segura
     */
    public function logout(): void {
        Session::destroy();
        $this->redirect('/');
    }
}
