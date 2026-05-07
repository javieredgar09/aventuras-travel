<?php
/**
 * WhatsAppApiController.php - API REST para envío de WhatsApp
 * Permite reenviar credenciales o mensajes personalizados
 */

class WhatsAppApiController extends Controller {

    /**
     * POST /api/whatsapp/send-credentials
     * Envía credenciales de acceso por WhatsApp
     * 
     * Body (JSON):
     * {
     *   "phone_number": "51987479046",
     *   "client_name": "Juan Pérez",
     *   "contract_code": "AV-2026-001",
     *   "username": "AV-2026-001",
     *   "password": "PasswordSegura123"
     * }
     */
    public function sendCredentials(): void {
        if (!$this->verifyCsrfToken()) {
            $this->json(['success' => false, 'error' => 'Token CSRF inválido'], 403);
            return;
        }

        // Obtener datos del request
        $data = $this->getJsonInput();

        // Validar campos requeridos
        $errors = [];
        if (empty($data['phone_number'])) $errors[] = 'phone_number es requerido';
        if (empty($data['client_name'])) $errors[] = 'client_name es requerido';
        if (empty($data['contract_code'])) $errors[] = 'contract_code es requerido';
        if (empty($data['password'])) $errors[] = 'password es requerido';

        if (!empty($errors)) {
            $this->json([
                'success' => false,
                'errors' => $errors,
            ], 400);
            return;
        }

        try {
            $whatsAppService = new WhatsAppService();

            // Normalizar número de teléfono
            $phoneNormalized = WhatsAppService::normalizePhoneNumber($data['phone_number']);

            // Enviar credenciales
            $result = $whatsAppService->sendCredentials(
                phoneNumber: $phoneNormalized,
                clientName: $data['client_name'],
                contractCode: $data['contract_code'],
                password: $data['password']
            );

            // Loguear resultado
            if ($result['success']) {
                error_log("[WhatsAppApiController::sendCredentials] ✅ Enviado a: $phoneNormalized | Message ID: " . $result['message_id']);
            } else {
                error_log("[WhatsAppApiController::sendCredentials] ❌ Error: " . $result['error']);
            }

            // Retornar respuesta
            $this->json([
                'success' => $result['success'],
                'message_id' => $result['message_id'] ?? null,
                'error' => $result['error'] ?? null,
            ], $result['success'] ? 200 : 400);

        } catch (Exception $e) {
            error_log("[WhatsAppApiController::sendCredentials] Exception: " . $e->getMessage());
            $this->json([
                'success' => false,
                'error' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * POST /api/whatsapp/send-message
     * Envía un mensaje genérico por WhatsApp
     * 
     * Body (JSON):
     * {
     *   "phone_number": "51987479046",
     *   "message": "Hola, este es un mensaje de prueba"
     * }
     */
    public function sendMessage(): void {
        if (!$this->verifyCsrfToken()) {
            $this->json(['success' => false, 'error' => 'Token CSRF inválido'], 403);
            return;
        }

        // Obtener datos del request
        $data = $this->getJsonInput();

        // Validar campos requeridos
        if (empty($data['phone_number']) || empty($data['message'])) {
            $this->json([
                'success' => false,
                'error' => 'phone_number y message son requeridos',
            ], 400);
            return;
        }

        try {
            $whatsAppService = new WhatsAppService();

            // Normalizar número de teléfono
            $phoneNormalized = WhatsAppService::normalizePhoneNumber($data['phone_number']);

            // Enviar mensaje
            $result = $whatsAppService->sendMessage(
                phoneNumber: $phoneNormalized,
                message: $data['message']
            );

            // Loguear resultado
            if ($result['success']) {
                error_log("[WhatsAppApiController::sendMessage] ✅ Enviado a: $phoneNormalized | Message ID: " . $result['message_id']);
            } else {
                error_log("[WhatsAppApiController::sendMessage] ❌ Error: " . $result['error']);
            }

            // Retornar respuesta
            $this->json([
                'success' => $result['success'],
                'message_id' => $result['message_id'] ?? null,
                'error' => $result['error'] ?? null,
            ], $result['success'] ? 200 : 400);

        } catch (Exception $e) {
            error_log("[WhatsAppApiController::sendMessage] Exception: " . $e->getMessage());
            $this->json([
                'success' => false,
                'error' => 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * GET /api/whatsapp/test
     * Prueba la conexión con la API de Meta
     */
    public function test(): void {
        try {
            $whatsAppService = new WhatsAppService();
            $result = $whatsAppService->testConnection();

            $this->json([
                'success' => $result['success'],
                'http_code' => $result['http_code'],
                'response' => $result['response'],
            ], $result['success'] ? 200 : 400);

        } catch (Exception $e) {
            error_log("[WhatsAppApiController::test] Exception: " . $e->getMessage());
            $this->json([
                'success' => false,
                'error' => 'Error al probar la conexión',
            ], 500);
        }
    }

    /**
     * Obtener entrada JSON del request
     */
    private function getJsonInput(): array {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }
}
