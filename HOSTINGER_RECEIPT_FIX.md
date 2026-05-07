# CORRECCIÓN DE RECIBOS - Guía Hostinger

**Para:** Implementación en Producción en Hostinger  
**Fecha:** 7 de Mayo de 2026

---

## 🚀 Paso 1: Acceder al Panel de Control Hostinger

1. Ir a [Hostinger.la](https://hostinger.la)
2. Login con tus credenciales
3. Ir a **Mis Dominios → aventurastravel.com (o tu dominio)**
4. Click en **Administrador de Archivos** o **File Manager**

---

## 📝 Paso 2: Actualizar Archivos (Opción Recomendada: Git)

### Si tienes Git configurado en Hostinger:

```bash
# Conectar por SSH a Hostinger
ssh usuario@servidor.hostinger.com

# Navegar a la carpeta del proyecto
cd /home/tuusuario/public_html/aventuras

# Descargar cambios desde git
git pull origin main
```

### Si NO tienes Git (Método Manual):

1. Desde tu computadora, edita estos archivos:
   - `app/services/ReceiptService.php`
   - `app/controllers/PaymentController.php`
   - `app/views/admin/payments/index.php`
   - `routes/web.php`

2. Sube los archivos editados al **File Manager** de Hostinger en las rutas correspondientes

---

## 🔄 Paso 3: Regenerar Recibos Existentes

### Opción A: Desde Panel Admin (SIN Terminal) ✅ RECOMENDADO

1. Accede a tu panel admin: `https://aventurastravel.com/admin`
2. Ve a **Gestión de Pagos**
3. Busca el pago de $350 que tiene recibo incorrecto
4. Haz click en el botón **🔄 (refresh)** junto al PDF
5. Confirma: `¿Regenerar recibo?`
6. ✅ Listo, se regeneró automáticamente

### Opción B: Script CLI (Si tienes acceso terminal)

1. Conexión SSH a Hostinger:
   ```bash
   ssh usuario@servidor.hostinger.com
   cd /home/tuusuario/public_html/aventuras
   php regenerate_receipts.php
   ```

2. Sigue las instrucciones interactivas

---

## 🧪 Paso 4: Verificación

### Verificar que el recibo muestra el monto correcto:

1. Ve a **Gestión de Pagos**
2. Busca el pago de **$350**
3. Haz click en el **PDF** (icono de documento)
4. Verifica que la caja grande muestre: **MONTO PAGADO: $350.00**

**Debe mostrar:**
```
MONTO PAGADO: $350.00
(NO $1,199.00)
```

---

## 📋 Cambios Realizados (Resumen)

| Componente | Cambio |
|-----------|--------|
| **Recibos PDF** | Ahora muestra monto actual, no acumulado |
| **Etiqueta** | "TOTAL PAGADO" → "MONTO PAGADO" (más claro) |
| **Panel Admin** | Nuevo botón 🔄 para regenerar recibos |
| **Ruta nueva** | POST `/admin/payments/{id}/regenerate` |

---

## ⚠️ Si Algo Falla

### Error: "Token CSRF inválido"
- Asegúrate de estar logueado en el admin
- Recarga la página y vuelve a intentar

### Error: "Pago no encontrado"
- Verifica el ID del pago en la URL
- El pago debe estar en estado "aprobado"

### El recibo SIGUE mostrando $1,199
- Usa el script de diagnóstico en SSH:
  ```bash
  php diagnose_payment.php
  ```
- O contacta a soporte técnico con el ID del pago

---

## 📞 Contacto Hostinger

Si necesitas acceso SSH o tienes problemas con el File Manager:

- **Email:** support@hostinger.la
- **Chat:** Desde tu panel de Hostinger
- **Teléfono:** +51 1 6500-500

---

## ✅ Checklist Post-Implementación

Después de implementar, verifica:

- [ ] Archivos subidos correctamente a Hostinger
- [ ] Panel admin funciona sin errores
- [ ] Botón 🔄 aparece en Gestión de Pagos
- [ ] Puedes regenerar un recibo sin errores
- [ ] El recibo regenerado muestra el monto correcto
- [ ] Otros pagos no fueron afectados

---

## 🆘 Rollback (Si necesitas revertir)

Si algo no funciona y necesitas revertir:

```bash
git revert HEAD
git push origin main
```

O manualmente:
1. Restaura los archivos originales desde backup
2. En Hostinger File Manager, reemplaza los archivos
3. Limpia cache (si aplica)

---

**Problema Reportado:** Recibos muestran monto incorrecto  
**Solución:** ✅ Implementada y lista  
**Impacto:** Solo pagos aprobados se ven afectados  
**Tiempo de implementación:** 5-10 minutos  

🎉 **¡Listo! Los recibos ahora mostrarán los montos correctos.**
