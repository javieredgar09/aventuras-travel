<?php
/**
 * Session.php - Manejo seguro de sesiones
 * Aventuras Travel - Core
 */
class Session {
    /**
     * Iniciar sesión de forma segura
     */
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'httponly'  => true,
                'secure'    => isset($_SERVER['HTTPS']),
                'samesite'  => 'Strict',
            ]);
            session_start();
        }
    }

    /**
     * Obtener valor de sesión
     */
    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Establecer valor de sesión
     */
    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Verificar si existe un valor
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * Eliminar valor de sesión
     */
    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    /**
     * Destruir sesión completamente
     */
    public static function destroy(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Regenerar ID de sesión (prevenir session fixation)
     */
    public static function regenerate(): void {
        session_regenerate_id(true);
    }
}
