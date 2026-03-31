<?php
/**
 * AuthMiddleware.php - Verificación de autenticación
 */
class AuthMiddleware {
    public function handle(): bool {
        if (!isset($_SESSION['user'])) {
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, '/api/') !== false) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'No autorizado', 'code' => 401]);
            } else {
                header('Location: ' . Router::url('/login'));
            }
            return false;
        }
        return true;
    }
}
