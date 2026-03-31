<?php
/**
 * AdminMiddleware.php - Verificación de rol admin
 */
class AdminMiddleware {
    public function handle(): bool {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, '/api/') !== false) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Acceso denegado', 'code' => 403]);
            } else {
                header('Location: ' . Router::url('/login'));
            }
            return false;
        }
        return true;
    }
}
