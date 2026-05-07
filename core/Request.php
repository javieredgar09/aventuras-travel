<?php
/**
 * Request.php - Wrapper para la petición HTTP entrante
 * Aventuras Travel - Core
 */
class Request {
    private string $method;
    private string $uri;
    private array $query;
    private array $body;
    private array $files;
    private array $server;
    private array $headers;

    public function __construct() {
        $this->method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri     = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        $this->query   = $_GET;
        $this->body    = $_POST;
        $this->files   = $_FILES;
        $this->server  = $_SERVER;
        $this->headers = $this->parseHeaders();

        // Soporte PUT/DELETE via _method
        if ($this->method === 'POST' && isset($this->body['_method'])) {
            $this->method = strtoupper($this->body['_method']);
        }
    }

    public function method(): string {
        return $this->method;
    }

    public function uri(): string {
        return $this->uri;
    }

    public function isMethod(string $method): bool {
        return $this->method === strtoupper($method);
    }

    public function get(string $key, $default = null) {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, $default = null) {
        return $this->body[$key] ?? $default;
    }

    public function input(string $key, $default = null) {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array {
        return array_merge($this->query, $this->body);
    }

    public function has(string $key): bool {
        return isset($this->body[$key]) || isset($this->query[$key]);
    }

    public function file(string $key): ?array {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function header(string $key, $default = null): ?string {
        $normalized = strtolower($key);
        return $this->headers[$normalized] ?? $default;
    }

    public function isAjax(): bool {
        return $this->header('x-requested-with') === 'XMLHttpRequest';
    }

    public function isJson(): bool {
        return str_contains($this->header('content-type', ''), 'application/json');
    }

    public function json(): array {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    public function ip(): string {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function validate(array $rules): array {
        $errors = [];
        $data   = $this->all();

        foreach ($rules as $field => $ruleSet) {
            $fieldRules = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && (is_null($value) || trim((string) $value) === '')) {
                    $errors[$field][] = "El campo {$field} es obligatorio.";
                }
                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "El campo {$field} debe ser un email válido.";
                }
                if ($rule === 'numeric' && $value && !is_numeric($value)) {
                    $errors[$field][] = "El campo {$field} debe ser numérico.";
                }
                if (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if ($value && strlen((string) $value) > $max) {
                        $errors[$field][] = "El campo {$field} no debe exceder {$max} caracteres.";
                    }
                }
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if ($value && strlen((string) $value) < $min) {
                        $errors[$field][] = "El campo {$field} debe tener al menos {$min} caracteres.";
                    }
                }
            }
        }

        return $errors;
    }

    private function parseHeaders(): array {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = $value;
            }
        }
        if (isset($this->server['CONTENT_TYPE'])) {
            $headers['content-type'] = $this->server['CONTENT_TYPE'];
        }
        return $headers;
    }
}
