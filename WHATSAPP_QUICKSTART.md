# 🚀 Quick Start - WhatsApp Integration

## ⚡ Verificación Rápida

Ejecuta este script para verificar que todo está funcionando:

```bash
php scripts/test_whatsapp.php
```

Deberías ver:
- ✅ Conexión exitosa con API de Meta
- ✅ Números telefónicos normalizados correctamente
- ✅ Ejemplos de uso

## 🔧 Setup Inicial

### 1. Verificar Archivos Creados

Confirma que existen:
```
app/services/WhatsAppService.php
app/controllers/api/WhatsAppApiController.php
app/traits/WhatsAppTrait.php
scripts/test_whatsapp.php
WHATSAPP_INTEGRATION.md
```

### 2. Verificar Rutas API

Abre `routes/api.php` y confirma que existen:
```php
Router::post('/api/whatsapp/send-credentials', ...);
Router::post('/api/whatsapp/send-message', ...);
Router::get('/api/whatsapp/test', ...);
```

### 3. Verificar Cambios en UserController

Abre `app/controllers/UserController.php` y busca:
- Instancia de `WhatsAppService`
- Llamada a `sendCredentials()`
- Guardado de `whatsapp_result` en sesión

### 4. Verificar Vista de Credenciales

Abre `app/views/admin/users/credentials.php` y busca:
- Variable `$whatsappResult`
- Bloque de estado del envío (verde/rojo)

## 📝 Flujo de Prueba Completo

### Paso 1: Crear un Usuario

1. Ir a `/admin/users/create`
2. Llenar el formulario:
   - Nombre: `Juan`
   - Apellido: `Pérez`
   - Email: `juan@example.com`
   - Teléfono: `+51 987 479 046` ← **Importante**
   - Rol: `Cliente Familiar`
3. Hacer clic en "Crear Usuario"

### Paso 2: Verificar Envío

Deberías ver en `/admin/users/{id}/credentials`:
- ✅ Bloque VERDE: "✅ WhatsApp enviado exitosamente"
- Message ID del envío

O si hay error:
- ❌ Bloque ROJO: "❌ Error al enviar WhatsApp"
- Descripción del error

### Paso 3: Revisar Logs

```bash
# En Linux/Mac
tail -f logs/error.log | grep WhatsApp

# En Windows PowerShell
Get-Content logs/error.log -Tail 10 -Wait | Select-String "WhatsApp"
```

Busca líneas como:
```
[UserController::store] ✅ WhatsApp enviado a: 51987479046 | Message ID: wamid.xxxxx
```

## 🔄 Reenviar Credenciales

### Opción 1: Desde Edición de Usuario

1. Ir a `/admin/users/{id}/edit`
2. Hacer clic en botón verde "Reenviar por WhatsApp"
3. Confirmar en modal

### Opción 2: Usar API

```bash
curl -X POST http://localhost/aventuras/api/whatsapp/send-credentials \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-Token: CSRF_TOKEN" \
  -d '{
    "phone_number": "51987479046",
    "client_name": "Juan Pérez",
    "contract_code": "AV-2026-001",
    "username": "AV-2026-001",
    "password": "PasswordSegura123"
  }'
```

## ⚠️ Troubleshooting

### Problema: "cURL error"
**Solución:** Verificar que curl está habilitado en PHP
```bash
php -m | grep curl
```

### Problema: "Access Token Expired"
**Solución:** Usar token permanente (el que está en `WhatsAppService.php`)

### Problema: "Template not found"
**Solución:** Verificar que la plantilla `envio_acceso` existe en Meta Business Manager

### Problema: El mensaje no se envía
**Solución:** 
1. Revisar logs: `tail -f logs/error.log`
2. Validar número: `+51 9xxxxxxxxxx` (Perú)
3. Probar conexión: `GET /api/whatsapp/test`

### Problema: "Token CSRF inválido"
**Solución:** Asegurar que el formulario incluye `csrf_token`

## 📞 Números de Prueba

Para desarrollo, Meta proporciona números de prueba.  
Consultar: https://developers.facebook.com/docs/whatsapp/

Formato para Perú:
- ✅ Válido: `+51987479046` o `51987479046`
- ❌ Inválido: `987479046` (sin país)

## 📊 Monitoreo

### Ver últimos envíos
```bash
grep "WhatsApp" logs/error.log | tail -20
```

### Contar envíos exitosos
```bash
grep -c "✅ Mensaje enviado exitosamente" logs/error.log
```

### Contar errores
```bash
grep -c "❌ Error enviando" logs/error.log
```

## 🔐 Seguridad

- ✅ Solo admins pueden acceder a `/api/whatsapp/*`
- ✅ CSRF token requerido
- ✅ Token almacenado en constante (nunca en git)
- ✅ Números normalizados antes de enviar
- ✅ Logs no contienen contraseñas

## 📚 Documentación Completa

Ver: `WHATSAPP_INTEGRATION.md`

## ✅ Checklist de Implementación

- [ ] Archivos creados ✓
- [ ] Rutas agregadas ✓
- [ ] UserController modificado ✓
- [ ] Vista de credenciales actualizada ✓
- [ ] Script de prueba ejecutado ✓
- [ ] Crear usuario con teléfono ✓
- [ ] Verificar envío de WhatsApp ✓
- [ ] Revisar logs ✓
- [ ] Reenviar desde edición ✓
- [ ] API funciona ✓
