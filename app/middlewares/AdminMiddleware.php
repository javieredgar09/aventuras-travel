<?php
/**
 * AdminMiddleware.php - Verificación de rol admin
 * Redirige a 403 si está autenticado pero sin permisos.
 * Redirige a login si no está autenticado.
 */
class AdminMiddleware {
    public function handle(): bool {
        $uri = $_SERVER['REQUEST_URI'];
        $isApi = strpos($uri, '/api/') !== false;

        if (!isset($_SESSION['user'])) {
            // No autenticado → login
            if ($isApi) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'No autorizado', 'code' => 401]);
            } else {
                header('Location: ' . Router::url('/login'));
            }
            return false;
        }

        if ($_SESSION['user']['rol'] !== 'admin') {
            // Autenticado pero sin rol admin → 403
            if ($isApi) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Acceso denegado', 'code' => 403]);
            } else {
                http_response_code(403);
                $errorView = BASE_PATH . '/app/views/errors/403.php';
                if (file_exists($errorView)) {
                    include $errorView;
                } else {
                    echo '<h1>403 - Acceso denegado</h1>';
                }
            }
            return false;
        }
        return true;
    }
}
