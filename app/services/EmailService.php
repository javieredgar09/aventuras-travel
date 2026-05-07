<?php
/**
 * EmailService.php
 * Handles sending transactional emails (Credentials, Notifications).
 */

class EmailService {

    private $fromEmail;
    private $fromName;

    public function __construct() {
        // En un entorno de producción, esto vendría del archivo de configuración o variables de entorno.
        $this->fromEmail = 'aventurastravelpucallpa@gmail.com';
        $this->fromName = 'Aventuras Travel Pucallpa';
    }

    /**
     * Enviar correo genérico usando la función nativa mail()
     */
    public function sendEmail(string $to, string $subject, string $htmlBody): bool {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromEmail}>" . "\r\n";
        $headers .= "Reply-To: {$this->fromEmail}" . "\r\n";

        // En entornos locales como XAMPP, mail() puede no funcionar sin configuración previa.
        // Utilizaremos un log para simular el envío en caso de fallar.
        $success = false;
        try {
            $success = @mail($to, $subject, $htmlBody, $headers);
        } catch (Throwable $e) {
            $success = false;
        }

        if (!$success) {
            error_log("❌ Error enviando email a: $to | Asunto: $subject");
            // Para propósitos de debug/local: usar STORAGE_PATH si está definido, sino fallback a BASE_PATH/storage
            $storagePath = defined('STORAGE_PATH') ? STORAGE_PATH : (defined('BASE_PATH') ? BASE_PATH . '/storage' : __DIR__ . '/../../storage');
            // asegurar directorio
            if (!is_dir($storagePath)) {
                @mkdir($storagePath, 0755, true);
            }
            $filename = $storagePath . '/ultimo_correo_enviado.html';
            @file_put_contents($filename, $htmlBody);
        }

        return true; // Asumimos éxito o fallback a log para que no se detenga el flujo
    }

    /**
     * Generar y descargar la plantilla HTML
     */
    private function getTemplate(string $title, string $content): string {
        $year = date('Y');
        $baseUrl = $this->getBaseUrl();

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; color: #1e293b; }
                .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
                .header { background: #0f766e; color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 1px; }
                .content { padding: 40px 30px; line-height: 1.6; }
                .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
                .btn { display: inline-block; background: #0f766e; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; margin-top: 20px; }
                .box { background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px dashed #cbd5e1; }
                .highlight { color: #0f766e; font-weight: bold; font-size: 18px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>AVENTURAS TRAVEL</h1>
                </div>
                <div class="content">
                    <h2 style="color: #0f766e; margin-top: 0;">{$title}</h2>
                    {$content}
                </div>
                <div class="footer">
                    <p>Este es un mensaje automático. Por favor no responder a este correo.</p>
                    <p>&copy; {$year} Aventuras Travel Pucallpa. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Enviar credenciales de acceso a un nuevo cliente
     */
    public function sendCredentials(string $toEmail, string $nombre, string $codigo, string $passwordPlainText): bool {
        $baseUrl = $this->getBaseUrl();
        $loginUrl = rtrim($baseUrl, '/') . '/login';

        $title = "¡Bienvenido a Aventuras Travel!";
        $content = "
            <p>Hola <strong>{$nombre}</strong>,</p>
            <p>Tu contrato/reserva ha sido generado exitosamente. Se ha creado una cuenta para que puedas gestionar tus pagos, descargar tus vouchers y ver los detalles de tu itinerario.</p>
            
            <div class='box'>
                <p style='margin: 0 0 10px 0;'>Tus credenciales de acceso son:</p>
                <p style='margin: 5px 0;'><strong>Código (Usuario):</strong> <span class='highlight'>{$codigo}</span></p>
                <p style='margin: 5px 0;'><strong>Contraseña:</strong> <span class='highlight'>{$passwordPlainText}</span></p>
            </div>

            <p>Puedes iniciar sesión en cualquier momento ingresando al portal de clientes:</p>
            <div style='text-align: center;'>
                <a href='{$loginUrl}' class='btn' style='color:#ffffff !important;' >Acceder al Portal</a>
            </div>
            
            <p style='margin-top: 30px; font-size: 14px;'>Te recomendamos cambiar tu contraseña una vez hayas ingresado por primera vez.</p>
        ";

        $html = $this->getTemplate($title, $content);

        return $this->sendEmail($toEmail, "Tus Credenciales de Acceso - Aventuras Travel", $html);
    }

    /**
     * Notificar al cliente que su pago fue aprobado
     */
    public function sendPaymentApproval(string $toEmail, string $nombre, string $codigoContrato, float $monto): bool {
        $baseUrl   = $this->getBaseUrl();
        $paymentsUrl = rtrim($baseUrl, '/') . '/client/payments';

        $title   = '✅ Pago Aprobado – Aventuras Travel';
        $montoFmt = number_format($monto, 2);
        $content = "
            <p>Hola <strong>{$nombre}</strong>,</p>
            <p>Nos complace informarte que tu pago ha sido <strong style='color:#16a34a;'>aprobado exitosamente</strong>.</p>

            <div class='box'>
                <p style='margin:0 0 8px 0;'><strong>Detalles del pago:</strong></p>
                <p style='margin:5px 0;'>📄 <strong>Contrato:</strong> <span class='highlight'>{$codigoContrato}</span></p>
                <p style='margin:5px 0;'>💵 <strong>Monto aprobado:</strong> <span class='highlight'>\$ {$montoFmt}</span></p>
            </div>

            <p>Puedes revisar el estado actualizado de tu plan de pagos en el portal de clientes:</p>
            <div style='text-align:center;'>
                <a href='{$paymentsUrl}' class='btn' style='color:#ffffff !important;'>Ver mis Pagos</a>
            </div>
            <p style='margin-top:30px; font-size:14px; color:#64748b;'>Gracias por confiar en Aventuras Travel. ✈️</p>
        ";

        $html = $this->getTemplate($title, $content);
        return $this->sendEmail($toEmail, 'Pago Aprobado – Aventuras Travel', $html);
    }

    /**
     * Notificar al cliente que su pago fue rechazado
     */
    public function sendPaymentRejection(string $toEmail, string $nombre, string $codigoContrato, float $monto, string $motivo = ''): bool {
        $baseUrl     = $this->getBaseUrl();
        $paymentsUrl = rtrim($baseUrl, '/') . '/client/payments';

        $title   = '❌ Pago Rechazado – Aventuras Travel';
        $montoFmt = number_format($monto, 2);
        $motivoHtml = $motivo
            ? "<p style='margin:5px 0;'>📝 <strong>Motivo:</strong> " . htmlspecialchars($motivo) . "</p>"
            : '';

        $content = "
            <p>Hola <strong>{$nombre}</strong>,</p>
            <p>Lamentablemente, tu pago ha sido <strong style='color:#dc2626;'>rechazado</strong> por nuestro equipo de administración.</p>

            <div class='box'>
                <p style='margin:0 0 8px 0;'><strong>Detalles del pago rechazado:</strong></p>
                <p style='margin:5px 0;'>📄 <strong>Contrato:</strong> <span class='highlight'>{$codigoContrato}</span></p>
                <p style='margin:5px 0;'>💵 <strong>Monto:</strong> <span class='highlight'>\$ {$montoFmt}</span></p>
                {$motivoHtml}
            </div>

            <p>Por favor, sube un nuevo comprobante o contáctanos para aclarar cualquier inconveniente:</p>
            <div style='text-align:center;'>
                <a href='{$paymentsUrl}' class='btn' style='color:#ffffff !important;'>Ir a mis Pagos</a>
            </div>
            <p style='margin-top:30px; font-size:14px; color:#64748b;'>Si tienes alguna pregunta, escríbenos a aventurastravelpucallpa@gmail.com</p>
        ";

        $html = $this->getTemplate($title, $content);
        return $this->sendEmail($toEmail, 'Pago Rechazado – Aventuras Travel', $html);
    }

    /**
     * Devuelve la base URL segura (usa fallback si $_SERVER no está disponible)
     * @return string
     */
    private function getBaseUrl(): string {
        $scheme = 'http';
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') $scheme = 'https';
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
        // Si se detecta puerto no estándar, agregarlo
        $port = $_SERVER['SERVER_PORT'] ?? null;
        if ($port && !in_array($port, ['80','443'])) {
            // evitar duplicar el puerto si ya está en host
            if (strpos($host, ':') === false) $host .= ":{$port}";
        }
        return $scheme . '://' . $host . '/aventuras';
    }

}
