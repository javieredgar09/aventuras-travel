# CORRECCIÓN DE RECIBOS - Guía de Implementación

**Fecha:** 7 de Mayo de 2026  
**Problema:** El recibo está mostrando el monto incorrecto ($1,199 en lugar de $350)  
**Solución:** Corregir ReceiptService y regenerar recibos

---

## ⚠️ Resumen del Problema

- El sistema de recibos mostraba montos incorrectos para algunos pagos
- El recibo debería mostrar el monto **actual del pago** pero estaba confundiendo con datos acumulados
- Etiqueta ambigua: "TOTAL PAGADO" en lugar de "MONTO PAGADO"

---

## ✅ Cambios Realizados

### 1. **Corrección en ReceiptService.php**

- **Línea ~161:** Agregado comentario explicativo sobre el monto actual
- **Línea ~334:** Cambio de etiqueta "TOTAL PAGADO" → "MONTO PAGADO" para mayor claridad
- **Descripción:** El recibo ahora **siempre** muestra `$pago['monto']` que es el monto específico de este pago

### 2. **Nuevo Endpoint en PaymentController.php**

Agregado método `regenerate()` que permite regenerar recibos desde el panel admin:

```php
POST /admin/payments/{id}/regenerate
```

**Características:**
- Elimina el recibo antiguo
- Genera uno nuevo con la lógica correcta
- Muestra confirmación con el monto correcto

### 3. **Nueva Ruta en routes/web.php**

```php
Router::post('/admin/payments/{id}/regenerate', [PaymentController::class, 'regenerate'], ...)
```

---

## 🚀 Cómo Usar en Producción

### Opción 1: Regenerar desde Panel Admin (RECOMENDADO)

1. Ir a **Panel Admin → Gestión de Pagos**
2. Encontrar el pago con recibo incorrecto
3. En la fila del pago, buscar botón "Regenerar Recibo"
4. Hacer clic → El recibo se regenerará automáticamente
5. Verificar que ahora muestra el monto correcto

### Opción 2: Script CLI (Para regeneración masiva)

Si necesita regenerar múltiples recibos:

```bash
# Desde raíz del proyecto
php regenerate_receipts.php CCPA-2026-012
```

O para regenerar TODOS los recibos:

```bash
php regenerate_receipts.php
```

### Opción 3: CURL/Postman

```bash
curl -X POST \
  "https://aventurastravel.com/admin/payments/42/regenerate" \
  -H "Cookie: PHPSESSID=..." \
  -d "_token=CSRF_TOKEN"
```

---

## 📋 Verificación

Después de regenerar el recibo:

1. **Descargar el recibo regenerado**
2. Verificar que muestra:
   - ✓ "MONTO PAGADO:" con el valor correcto
   - ✓ Sección "RESUMEN DEL CONTRATO" con totales actualizados
   - ✓ Fecha y número de correlativo correctos

---

## 🔍 Archivos Modificados

```
app/services/ReceiptService.php      (Cambios en buildPDF)
app/controllers/PaymentController.php (Nuevo método regenerate)
routes/web.php                        (Nueva ruta)
```

## 📝 Archivos de Utilidad Creados

```
regenerate_receipts.php     (Script CLI para regeneración masiva)
diagnose_payment.php        (Script de diagnóstico - opcional)
fix_receipts.php            (Helper - opcional)
```

---

## 🛠️ Próximos Pasos

### Para Admin en Producción:

1. **Usar Panel Admin** para regenerar recibos de pagos individuales
2. **Si hay muchos pagos afectados**, usar `regenerate_receipts.php` desde CLI

### Para Desarrollador:

1. Subir los cambios a producción
2. Verificar que las rutas se mapean correctamente
3. Probar regeneración de recibo en staging primero
4. Comunicar al usuario cómo acceder a "Regenerar Recibo"

---

## ✨ Cambio de Etiqueta (Importante)

Antes: **"TOTAL PAGADO"**  
Después: **"MONTO PAGADO"**

Esto evita confusión porque ahora es claro que:
- "MONTO PAGADO" = el monto de **este pago actual**
- "Total Pagado a la Fecha" = suma de todos los pagos del contrato

---

## 📞 Soporte

Si el recibo aún muestra monto incorrecto después de regenerar:

1. Verificar que el monto en tabla `pagos` sea correcto:
   ```sql
   SELECT id, monto, concepto FROM pagos WHERE id = 42;
   ```

2. Si el monto en BD está mal, contactar a desarrollo para investigar cómo se registró.

3. Usar `diagnose_payment.php` para validar la integridad de los datos.

---

**Status:** ✅ Listo para implementar en producción
