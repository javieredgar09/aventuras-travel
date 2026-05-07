# 📱 Integración de WhatsApp - Aventuras Travel

## Descripción

Este módulo integra la **API de WhatsApp Business de Meta** para enviar automáticamente las credenciales de acceso a los clientes cuando se crea una nueva cuenta de usuario.

## 📋 Características

✅ Envío automático de credenciales al crear usuarios  
✅ Mensajes con plantilla profesional  
✅ Normalización automática de números telefónicos  
✅ Logging completo de envíos exitosos y errores  
✅ API REST para reenviar mensajes manualmente  
✅ Soporte para múltiples formatos de números telefónicos  

## 🔧 Configuración

### 1. Credenciales de Meta (WhatsApp Business API)

Todas las credenciales se encuentran en el archivo `WhatsAppService.php`:

```php
// En app/services/WhatsAppService.php
private const PHONE_NUMBER_ID = '843905918814564';
private const WHATSAPP_BUSINESS_ACCOUNT_ID = '860294979769298';
private const TEMPLATE_NAME = 'envio_acceso';
private const API_VERSION = 'v22.0';
```

### 2. Token de Acceso

El token se obtiene de la variable de entorno `WHATSAPP_ACCESS_TOKEN` o usa el token por defecto configurado en el servicio:

```php
// Token permanente (no expira)
EAAJpIm0vbI8BRd0ak6RziMbPAYt0BjTcIsQr6sGTaoJTbWEKec4FU0ztfZAbwmKC4mox4KQEys2ccpv9P4Qdj0uD80ZAxxmdLs3J21GFmfkrzQnoBTyZBWtFfDhOOJe6C3ZCYdZAZAzCmeSZCTLiXmIzG4bIlrZAlU9GWsUIDF303FnSLkIjYZCFBnNp9XEeWdwZDZD
```

### 3. Plantilla de WhatsApp

**Nombre de plantilla:** `envio_acceso`  
**Idioma:** Español (`es`)

**Contenido:**
```
✈️✨ ¡Hola {{1}}! Tu viaje con Aventuras Travel Pucallpa está listo 🌊🏔️🏛️

📄 Contrato: {{2}}
👤 Usuario: {{3}}
🔐 Contraseña: {{4}}

Guarda estos datos en un lugar seguro 🎫

¿Playas, montañas o cultura? Escríbenos y te ayudamos 🗺️

¡Buen viaje! 🌅
```

**Parámetros:**
1. Nombre del cliente
2. Código del contrato
3. Usuario (código)
4. Contraseña

## 📦 Archivos Creados/Modificados

### Creados:
- `app/services/WhatsAppService.php` - Servicio principal
- `app/controllers/api/WhatsAppApiController.php` - Controlador API
- `scripts/test_whatsapp.php` - Script de prueba

### Modificados:
- `app/controllers/UserController.php` - Integración en crear usuario
- `app/views/admin/users/credentials.php` - Mostrar estado del envío
- `routes/api.php` - Rutas de la API

## 🚀 Uso

### 1. Envío Automático (Crear Usuario)

Cuando se crea un usuario desde el admin panel (`/admin/users/create`):

1. Se completa el formulario con los datos del usuario
2. El sistema genera una contraseña segura
3. Se crea el usuario en la base de datos
4. **Automáticamente** se envía el mensaje de WhatsApp si el teléfono es válido
5. Se muestra el estado del envío en la página de credenciales

### 2. API REST - Enviar Credenciales

**Endpoint:** `POST /api/whatsapp/send-credentials`

```bash
curl -X POST http://localhost/aventuras/api/whatsapp/send-credentials \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-Token: TOKEN" \
  -d '{
    "phone_number": "51987479046",
    "client_name": "Juan Pérez",
    "contract_code": "AV-2026-001",
    "username": "AV-2026-001",
    "password": "PasswordSegura123"
  }'
```

**Respuesta exitosa:**
```json
{
  "success": true,
  "message_id": "wamid.xxxxx",
  "error": null
}
```

**Respuesta de error:**
```json
{
  "success": false,
  "message_id": null,
  "error": "Número de teléfono inválido..."
}
```

### 3. API REST - Enviar Mensaje Genérico

**Endpoint:** `POST /api/whatsapp/send-message`

```bash
curl -X POST http://localhost/aventuras/api/whatsapp/send-message \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-Token: TOKEN" \
  -d '{
    "phone_number": "51987479046",
    "message": "¡Hola! Este es un mensaje de prueba"
  }'
```

### 4. Prueba de Conexión

**Endpoint:** `GET /api/whatsapp/test`

```bash
curl -X GET http://localhost/aventuras/api/whatsapp/test \
  -H "Authorization: Bearer TOKEN"
```

### 5. Script de Prueba

Ejecutar desde terminal:

```bash
php scripts/test_whatsapp.php
```

Esto realiza varias pruebas:
- ✅ Verificar conexión con API de Meta
- ✅ Normalizar números telefónicos
- ✅ Ejemplo de código para enviar credenciales

## 📝 Formatos de Números Telefónicos Soportados

El servicio normaliza automáticamente los siguientes formatos:

```
+51 987 479 046      → 51987479046
+51987479046         → 51987479046
51987479046          → 51987479046
987479046            → 51987479046 (agrega +51 para Perú)
+1 (555) 123-4567    → 1555123 4567
```

**Validación:**
- Debe tener entre 10-15 dígitos
- Puede incluir: números, +, espacios, guiones, paréntesis
- Tras normalización: solo números, sin +

## 📊 Logging

Todos los envíos se registran en los logs del sistema:

**Exitosos:**
```
[WhatsAppService] ✅ Mensaje enviado exitosamente. ID: wamid.xxxxx
```

**Errores:**
```
[WhatsAppService] ❌ Error enviando mensaje. HTTP Code: 400 | Error: Invalid phone number format | Code: 1001
```

Los logs se guardan en:
- `logs/error.log` (errores)
- Salida estándar en desarrollo

## 🔐 Seguridad

- ✅ Token almacenado en variable de entorno o constante
- ✅ Solo admins pueden usar la API
- ✅ CSRF token requerido para POST
- ✅ Validación de entrada en servidor
- ✅ Números telefónicos normalizados antes de enviar
- ✅ Contraseñas no enviadas en logs (solo en sesión)

## ⚠️ Consideraciones

### Limitaciones de la API de Meta

- Máximo 1000 mensajes por cuenta por hora
- Solo con números verificados
- La plantilla debe estar aprobada por Meta
- El teléfono del cliente debe haber iniciado una conversación o aceptado mensajes

### Números de Prueba

Meta proporciona números de prueba para desarrollo. Consultar documentación de Meta para obtener un número de prueba.

## 🆘 Solución de Problemas

### Error: "Invalid phone number format"
- Validar que el número esté en formato internacional
- Usar la normalización: `WhatsAppService::normalizePhoneNumber($phone)`

### Error: "Access Token Expired"
- Usar un token permanente (como el proporcionado)
- Los tokens temporales expiran cada ~2 meses

### Error: "Template not found"
- Verificar que la plantilla `envio_acceso` esté aprobada en Meta Business Manager
- Confirmar el idioma es `es` (español)

### El mensaje no se envía
- Validar que el número telefónico está en la lista permitida
- Revisar logs en `logs/error.log`
- Usar endpoint `/api/whatsapp/test` para probar conexión

## 📞 Contacto de Soporte

Para problemas con:
- **API de Meta:** Contactar [Facebook Developer Support](https://developers.facebook.com/support/)
- **Sistema Aventuras Travel:** Contactar administrador de sistemas

## 📚 Referencias

- [Meta WhatsApp Business API](https://developers.facebook.com/docs/whatsapp/cloud-api)
- [Template Management](https://developers.facebook.com/docs/whatsapp/message-templates)
- [Send Messages](https://developers.facebook.com/docs/whatsapp/cloud-api/reference/send-messages)
