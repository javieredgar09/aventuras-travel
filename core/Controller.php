<?php
/**
 * Controller.php - Base controller
 * Aventuras Travel - Core
 */
class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Renderizar vista con layout
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void {
        extract($data);
        $content = BASE_PATH . '/app/views/' . $view . '.php';
        
        if (!file_exists($content)) {
            throw new RuntimeException("Vista no encontrada: {$view}");
        }

        // Capturar contenido de la vista
        ob_start();
        include $content;
        $viewContent = ob_get_clean();

        // Incluir layout
        $layoutFile = BASE_PATH . '/app/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $viewContent;
        }
    }

    /**
     * Renderizar vista sin layout
     */
    protected function renderPartial(string $view, array $data = []): void {
        extract($data);
        include BASE_PATH . '/app/views/' . $view . '.php';
    }

    /**
     * Respuesta JSON
     */
    protected function json($data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Redireccionar
     */
    protected function redirect(string $url): void {
        header('Location: ' . Router::url($url));
        exit;
    }

    /**
     * Obtener datos sanitizados del request
     */
    protected function input(string $key, $default = null) {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        if (is_string($value)) {
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        return $value;
    }

    /**
     * Obtener datos raw del request (sin sanitizar, para passwords)
     */
    protected function inputRaw(string $key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Verificar si la petición es POST
     */
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verificar si la petición es AJAX
     */
    protected function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Generar token CSRF
     */
    protected function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verificar token CSRF
     */
    protected function verifyCsrfToken(): bool {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Establecer mensaje flash
     * @param string $type 'exito'|'error' etc
     * @param string $message Mensaje corto para mostrar
     * @param mixed $details (opcional) Detalle adicional: string o array para debugging
     */
    protected function flash(string $type, string $message, $details = null): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        if ($details !== null) {
            $_SESSION['flash']['details'] = $details;
        }
    }

    /**
     * Obtener y limpiar mensaje flash
     */
    protected function getFlash(): ?array {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Obtener usuario autenticado
     */
    protected function auth(): ?array {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Verificar si el usuario está autenticado
     */
    protected function isAuthenticated(): bool {
        return isset($_SESSION['user']);
    }
}
