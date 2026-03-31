<?php
/**
 * Router.php - Enrutador para web y API
 * Aventuras Travel - Core
 */
class Router {
    private static $routes = [];
    private static $basePath = '';

    /**
     * Registrar ruta GET
     */
    public static function get(string $path, $handler, array $middleware = []): void {
        self::addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Registrar ruta POST
     */
    public static function post(string $path, $handler, array $middleware = []): void {
        self::addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * Registrar ruta PUT
     */
    public static function put(string $path, $handler, array $middleware = []): void {
        self::addRoute('PUT', $path, $handler, $middleware);
    }

    /**
     * Registrar ruta DELETE
     */
    public static function delete(string $path, $handler, array $middleware = []): void {
        self::addRoute('DELETE', $path, $handler, $middleware);
    }

    /**
     * Agregar ruta al array
     */
    private static function addRoute(string $method, string $path, $handler, array $middleware): void {
        self::$routes[] = [
            'method'     => $method,
            'path'       => $path,
            'handler'    => $handler,
            'middleware'  => $middleware,
        ];
    }

    /**
     * Resolver y despachar la ruta actual
     */
    public static function dispatch(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = self::getRequestUri();

        // Soporte para PUT/DELETE via _method
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        foreach (self::$routes as $route) {
            $pattern = self::convertToRegex($route['path']);
            
            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                // Extraer parámetros con nombre
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Ejecutar middlewares
                foreach ($route['middleware'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    if (!$middleware->handle()) {
                        return;
                    }
                }

                // Ejecutar handler
                // Ordenar parámetros por el orden de aparición en la ruta ({id}, {foo}, ...)
                $orderedParams = [];
                if (preg_match_all('/\{(\w+)\}/', $route['path'], $m)) {
                    foreach ($m[1] as $pname) {
                        if (isset($params[$pname])) $orderedParams[] = $params[$pname];
                    }
                }

                if (empty($orderedParams)) {
                    // si no hay placeholders o no se obtuvieron, pasar parámetros posicionales si existen
                    $orderedParams = array_values($params);
                }

                if (is_array($route['handler'])) {
                    [$controllerClass, $method] = $route['handler'];
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $method], $orderedParams);
                } elseif (is_callable($route['handler'])) {
                    call_user_func_array($route['handler'], $orderedParams);
                }
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        if (self::isApiRequest($requestUri)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Recurso no encontrado', 'code' => 404]);
        } else {
            include BASE_PATH . '/app/views/errors/404.php';
        }
    }

    /**
     * Obtener URI limpio
     */
    private static function getRequestUri(): string {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Remover base path del proyecto (soporta /aventuras/public y /aventuras)
        $basePaths = ['/aventuras/public', '/aventuras'];
        foreach ($basePaths as $basePath) {
            if (strpos($uri, $basePath) === 0) {
                $uri = substr($uri, strlen($basePath));
                break;
            }
        }
        $uri = '/' . trim($uri, '/');
        return $uri === '' ? '/' : $uri;
    }

    /**
     * Convertir path a regex
     */
    private static function convertToRegex(string $path): string {
        // Convertir {param} a grupo con nombre
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Verificar si es una petición API
     */
    private static function isApiRequest(string $uri): bool {
        return strpos($uri, '/api/') === 0;
    }

    /**
     * Generar URL
     */
    public static function url(string $path = ''): string {
        return '/aventuras' . $path;
    }
}
