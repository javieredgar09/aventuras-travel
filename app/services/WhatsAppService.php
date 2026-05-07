<?php
/**
 * WhatsAppService.php
 * Envía mensajes de WhatsApp utilizando la API de Meta (Facebook)
 * 
 * Plantilla: envio_acceso (APPROVED - parameter_format: NAMED)
 * Estructura:
 *   HEADER (TEXT): "Hola {{nombre}}"
 *   BODY:          "{{contrato}}" + "{{password}}"
 *   FOOTER:        "Aventuras Travel Pucallpa a tu servicio"
 */

class WhatsAppService {

    // ── Credenciales de la API de Meta ──────────────────────────────────
    private const PHONE_NUMBER_ID = '843905918814564';
    private const WHATSAPP_BUSINESS_ACCOUNT_ID = '860294979769298';
    private const TEMPLATE_NAME = 'envio_acceso';
    private const API_VERSION = 'v22.0';
    private const API_BASE_URL = 'https://graph.facebook.com';

    // ── Datos de contacto de Aventuras Travel ──────────────────────────
    private const BUSINESS_PHONE = '51976324716'; // +51 976 324 716
    private const BUSINESS_EMAIL = 'aventurastravelpucallpa@gmail.com';

    private $accessToken;
    private $templateLanguage;

    public function __construct() {
        $this->accessToken = getenv('WHATSAPP_ACCESS_TOKEN') ?: 'EAAJpIm0vbI8BRd0ak6RziMbPAYt0BjTcIsQr6sGTaoJTbWEKec4FU0ztfZAbwmKC4mox4KQEys2ccpv9P4Qdj0uD80ZAxxmdLs3J21GFmfkrzQnoBTyZBWtFfDhOOJe6C3ZCYdZAZAzCmeSZCTLiXmIzG4bIlrZAlU9GWsUIDF303FnSLkIjYZCFBnNp9XEeWdwZDZD';
        $this->templateLanguage = 'es';
    }

    /**
     * Enviar credenciales de acceso usando la plantilla envio_acceso
     * 
     * La plantilla tiene:
     *   HEADER (TEXT): "Hola {{nombre}}"
     *   BODY: "{{contrato}}" + "{{password}}"
     *   FOOTER: texto fijo
     * 
     * @param string $phoneNumber Número en formato internacional (ej: 51976324716)
     * @param string $clientName  Nombre del cliente (para {{nombre}})
     * @param string $contractCode Código del contrato (para {{contrato}})
     * @param string $password    Contraseña generada (para {{password}})
     * @return array Respuesta de la API
     */
    public function sendCredentials(
        string $phoneNumber,
        string $clientName,
        string $contractCode,
        string $password
    ): array {
        // Validar teléfono
        if (!$this->validatePhoneNumber($phoneNumber)) {
            return [
                'success'    => false,
                'error'      => 'Número de teléfono inválido. Debe estar en formato internacional (ej: 51976324716)',
                'message_id' => null,
            ];
        }

        // Construir payload con parámetros NAMED
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $phoneNumber,
            'type'              => 'template',
            'template'          => [
                'name'       => self::TEMPLATE_NAME,
                'language'   => ['code' => $this->templateLanguage],
                'components' => [
                    // HEADER: "Hola {{nombre}}"
                    [
                        'type'       => 'header',
                        'parameters' => [
                            [
                                'type'           => 'text',
                                'parameter_name' => 'nombre',
                                'text'           => $clientName,
                            ],
                        ],
                    ],
                    // BODY: "{{contrato}}" y "{{password}}"
                    [
                        'type'       => 'body',
                        'parameters' => [
                            [
                                'type'           => 'text',
                                'parameter_name' => 'contrato',
                                'text'           => $contractCode,
                            ],
                            [
                                'type'           => 'text',
                                'parameter_name' => 'password',
                                'text'           => $password,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->sendRequest($payload);
    }

    /**
     * Enviar una solicitud a la API de Meta
     * @param array $payload Los datos a enviar
     * @return array Respuesta de la API
     */
    private function sendRequest(array $payload): array {
        $url = self::API_BASE_URL . '/' . self::API_VERSION . '/' . self::PHONE_NUMBER_ID . '/messages';

        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
        ];

        $toNumber = $payload['to'] ?? 'N/A';
        $templateName = $payload['template']['name'] ?? ($payload['type'] ?? 'N/A');
        error_log("[WhatsAppService] Enviando a: {$toNumber} | Template: {$templateName}");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Error de cURL
        if ($curlError) {
            error_log("[WhatsAppService] cURL Error: $curlError");
            return [
                'success'    => false,
                'error'      => 'Error de conexión con la API de WhatsApp',
                'message_id' => null,
                'curl_error' => $curlError,
            ];
        }

        $responseData = json_decode($response, true);

        // Éxito
        if ($httpCode === 200 || $httpCode === 201) {
            $msgId = $responseData['messages'][0]['id'] ?? 'N/A';
            error_log("[WhatsAppService] ✅ Mensaje enviado. ID: $msgId");
            return [
                'success'    => true,
                'error'      => null,
                'message_id' => $responseData['messages'][0]['id'] ?? null,
                'response'   => $responseData,
            ];
        }

        // Error de la API
        $errorMessage = $responseData['error']['message'] ?? 'Error desconocido';
        $errorCode = $responseData['error']['code'] ?? null;
        error_log("[WhatsAppService] ❌ HTTP: $httpCode | Error: $errorMessage | Code: $errorCode");
        error_log("[WhatsAppService] Payload: " . json_encode($payload, JSON_UNESCAPED_UNICODE));

        return [
            'success'     => false,
            'error'       => $errorMessage,
            'message_id'  => null,
            'http_code'   => $httpCode,
            'error_code'  => $errorCode,
            'response'    => $responseData,
        ];
    }

    /**
     * Validar formato del número de teléfono (E.164)
     */
    private function validatePhoneNumber(string $phoneNumber): bool {
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        return preg_match('/^(\+)?[1-9]\d{9,14}$/', $cleaned) === 1;
    }

    /**
     * Normalizar número de teléfono al formato WhatsApp
     * Convierte: +51 976 324 716 → 51976324716
     */
    public static function normalizePhoneNumber(string $phoneNumber): string {
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        if (substr($cleaned, 0, 1) === '+') {
            $cleaned = substr($cleaned, 1);
        }

        // Números peruanos locales (9 dígitos empezando con 9 o 8)
        if (strlen($cleaned) === 9 && preg_match('/^[89]/', $cleaned)) {
            $cleaned = '51' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Enviar mensaje de texto libre
     * @param string $phoneNumber Número de teléfono
     * @param string $message Mensaje de texto
     * @return array Respuesta de la API
     */
    public function sendMessage(string $phoneNumber, string $message): array {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $phoneNumber,
            'type'              => 'text',
            'text'              => [
                'preview_url' => false,
                'body'        => $message,
            ],
        ];

        return $this->sendRequest($payload);
    }

    /**
     * Obtener el estado del token y la conexión
     */
    public function testConnection(): array {
        $url = self::API_BASE_URL . '/' . self::API_VERSION . '/' . self::PHONE_NUMBER_ID;
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'success'   => $httpCode === 200,
            'http_code' => $httpCode,
            'response'  => json_decode($response, true),
        ];
    }
}
