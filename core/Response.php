<?php
/**
 * Response.php - Wrapper para la respuesta HTTP saliente
 * Aventuras Travel - Core
 */
class Response {
    private int $statusCode = 200;
    private array $headers = [];
    private string $body = '';

    public function setStatusCode(int $code): self {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $name, string $value): self {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setBody(string $content): self {
        $this->body = $content;
        return $this;
    }

    public function json($data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public function redirect(string $url, int $statusCode = 302): void {
        http_response_code($statusCode);
        header('Location: ' . $url);
        exit;
    }

    public function html(string $content, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=utf-8');
        echo $content;
        exit;
    }

    public function download(string $filePath, string $fileName = ''): void {
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            exit;
        }

        $fileName = $fileName ?: basename($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public function noContent(): void {
        http_response_code(204);
        exit;
    }

    public function error(string $message, int $statusCode = 500): void {
        $this->json(['error' => true, 'message' => $message], $statusCode);
    }

    public function send(): void {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        echo $this->body;
    }
}
