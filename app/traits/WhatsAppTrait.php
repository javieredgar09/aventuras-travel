<?php
/**
 * WhatsAppTrait.php
 * Trait reutilizable para envío de WhatsApp desde cualquier controlador
 * 
 * Uso:
 * class MiControlador extends Controller {
 *     use WhatsAppTrait;
 *     
 *     public function miMetodo() {
 *         $result = $this->sendWhatsAppCredentials(
 *             phone: '51987479046',
 *             name: 'Juan Pérez',
 *             contractCode: 'AV-2026-001',
 *             username: 'AV-2026-001',
 *             password: 'Password123'
 *         );
 *     }
 * }
 */

trait WhatsAppTrait {

    /**
     * Enviar credenciales por WhatsApp de forma segura
     * @param string $phone Número de teléfono
     * @param string $name Nombre del cliente
     * @param string $contractCode Código del contrato
     * @param string $username Usuario
     * @param string $password Contraseña
     * @return array Resultado del envío
     */
    protected function sendWhatsAppCredentials(
        string $phone,
        string $name,
        string $contractCode,
        string $username,
        string $password
    ): array {
        if (empty($phone)) {
            return [
                'success' => false,
                'error' => 'Teléfono no proporcionado',
                'message_id' => null,
            ];
        }

        try {
            $whatsAppService = new WhatsAppService();
            $phoneNormalized = WhatsAppService::normalizePhoneNumber($phone);

            $result = $whatsAppService->sendCredentials(
                phoneNumber: $phoneNormalized,
                clientName: $name,
                contractCode: $contractCode,
                username: $username,
                password: $password
            );

            if ($result['success']) {
                error_log("[WhatsAppTrait] ✅ Enviado a: $phoneNormalized | Message ID: " . $result['message_id']);
            } else {
                error_log("[WhatsAppTrait] ❌ Error: " . $result['error']);
            }

            return $result;

        } catch (Exception $e) {
            error_log("[WhatsAppTrait] Exception: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error interno: ' . $e->getMessage(),
                'message_id' => null,
            ];
        }
    }

    /**
     * Enviar mensaje genérico por WhatsApp
     * @param string $phone Número de teléfono
     * @param string $message Mensaje a enviar
     * @return array Resultado del envío
     */
    protected function sendWhatsAppMessage(
        string $phone,
        string $message
    ): array {
        if (empty($phone) || empty($message)) {
            return [
                'success' => false,
                'error' => 'Teléfono o mensaje vacío',
                'message_id' => null,
            ];
        }

        try {
            $whatsAppService = new WhatsAppService();
            $phoneNormalized = WhatsAppService::normalizePhoneNumber($phone);

            $result = $whatsAppService->sendMessage(
                phoneNumber: $phoneNormalized,
                message: $message
            );

            if ($result['success']) {
                error_log("[WhatsAppTrait::sendMessage] ✅ Enviado a: $phoneNormalized");
            } else {
                error_log("[WhatsAppTrait::sendMessage] ❌ Error: " . $result['error']);
            }

            return $result;

        } catch (Exception $e) {
            error_log("[WhatsAppTrait::sendMessage] Exception: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error interno: ' . $e->getMessage(),
                'message_id' => null,
            ];
        }
    }

    /**
     * Validar y normalizar número de teléfono
     * @param string $phone Número a validar
     * @return string|null Número normalizado o null si es inválido
     */
    protected function validateAndNormalizePhone(string $phone): ?string {
        if (empty($phone)) {
            return null;
        }

        return WhatsAppService::normalizePhoneNumber($phone);
    }
}
