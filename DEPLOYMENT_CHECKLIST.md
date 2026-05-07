# 📋 CHECKLIST DEPLOYMENT - AVENTURAS TRAVEL

**Dominio:** aventurastravelpucallpa.com  
**BD Usuario:** u850459552_aventuraspcl  
**Estado:** PRE-DEPLOYMENT

---

## 📝 ANTES DE SUBIR (En tu computadora)

### Preparación de archivos
- [ ] Crear archivo `.env` en raíz del proyecto
- [ ] `.env` contiene credenciales correctas de Hostinger
- [ ] Crear archivo `.htaccess` en carpeta `public_html/`
- [ ] Revisar que `config.php` está actualizado ✅
- [ ] Crear `.gitignore` con `.env` y `logs/`

### Contenido de `.env` verificado
- [ ] `DB_HOST=localhost`
- [ ] `DB_NAME=u850459552_aventuraspcl`
- [ ] `DB_USER=u850459552_aventuraspcl`
- [ ] `DB_PASS=Arejade2409.`
- [ ] `APP_ENV=production`
- [ ] `APP_URL=https://aventurastravelpucallpa.com`
- [ ] `SERPAPI_KEY` incluida

### Estructura de carpetas verificada
- [ ] `/app/` existe ✅
- [ ] `/core/` existe ✅
- [ ] `/routes/` existe ✅
- [ ] `/database/` existe con `schema.sql` y `seed_data.sql` ✅
- [ ] `/public_html/` existe con `index.php` ✅
- [ ] `/storage/` existe con subcarpetas ✅
- [ ] `/logs/` existe (crear si no existe)

---

## 🌐 EN HOSTINGER

### Paso 1: cPanel File Manager

- [ ] Acceder a cPanel con credenciales
- [ ] File Manager abierto
- [ ] Ubicarse en carpeta raíz del dominio
- [ ] Ver carpeta `public_html`

### Paso 2: Subir archivos a `/public_html/`

**Archivos principales:**
- [ ] `index.php`
- [ ] `descargar-recibo.php`
- [ ] `storage.php`
- [ ] `.htaccess` (archivo oculto)

**Carpetas:**
- [ ] `assets/` (con CSS y JS)
- [ ] `img/` (imágenes)
- [ ] `storage/` (carpeta para uploads)

### Paso 3: Subir archivos a raíz del dominio (FUERA de public_html)

- [ ] `app/` (carpeta completa)
- [ ] `core/` (carpeta completa)
- [ ] `routes/` (carpeta completa)
- [ ] `database/` (carpeta con SQL)
- [ ] `config.php` (archivo modificado)
- [ ] `vendor/` (si existe)

### Paso 4: Crear carpetas vacías

**En raíz del dominio:**
- [ ] `logs/` (permisos: 777)
- [ ] `storage/` (permisos: 755)

**Subcarpetas en `storage/`:**
- [ ] `storage/comprobantes/`
- [ ] `storage/contratos/`
- [ ] `storage/recibos/`
- [ ] `storage/vouchers/`

### Paso 5: Crear archivo `.env` en servidor

**Ubicación:** Raíz del dominio (NO en `public_html`)

- [ ] Nuevo archivo: `.env`
- [ ] Contenido correcto pegado:
```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=u850459552_aventuraspcl
DB_USER=u850459552_aventuraspcl
DB_PASS=Arejade2409.

APP_ENV=production
APP_DEBUG=false
APP_URL=https://aventurastravelpucallpa.com

SERPAPI_KEY=544a43ee854dfa60b1d14779cdc6f9e58f0ff02831d3ad21f11dd35dc019260b

MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

### Paso 6: Configurar permisos

**En File Manager, seleccionar carpeta → Properties → Permissions:**

- [ ] `logs/` = 777 (Read, Write, Execute para todos)
- [ ] `storage/` = 755 (Read/Write/Execute owner, Read/Execute otros)
- [ ] `storage/comprobantes/` = 755
- [ ] `storage/contratos/` = 755
- [ ] `storage/recibos/` = 755
- [ ] `storage/vouchers/` = 755
- [ ] `public_html/` = 755
- [ ] `config.php` = 644 (Read/Write owner, Read otros)
- [ ] `.env` = 644 (Read/Write owner, Read otros)

### Paso 7: Base de datos

**phpMyAdmin en cPanel:**

- [ ] Base de datos existe: `u850459552_aventuraspcl` ✅
- [ ] Usuario existe: `u850459552_aventuraspcl` ✅
- [ ] Importado `database/schema.sql` ✅
- [ ] Importado `database/seed_data.sql` ✅
- [ ] Tablas creadas (ver en phpMyAdmin)
- [ ] Datos iniciales insertados

---

## 🧪 PRUEBAS DE FUNCIONAMIENTO

### Test 1: Conectividad

- [ ] https://aventurastravelpucallpa.com/ → Carga sin error
- [ ] Redirige a HTTPS automáticamente
- [ ] Muestra homepage correctamente

### Test 2: Admin

- [ ] https://aventurastravelpucallpa.com/admin/login → Carga
- [ ] Login funciona con credenciales
- [ ] Dashboard muestra datos

### Test 3: Funcionalidades críticas

- [ ] Admin/sales/create → Carga formulario
- [ ] Búsqueda de vuelos → Funciona (<5s)
- [ ] Búsqueda de hoteles → Retorna resultados
- [ ] Teléfono flexible → Acepta formatos internacionales
- [ ] Upload de archivos → Funciona en `/storage/`

### Test 4: Base de datos

- [ ] phpMyAdmin → Se conecta sin error
- [ ] SELECT COUNT(*) FROM grupos; → Retorna número
- [ ] Datos visibles en tablas

### Test 5: Seguridad

- [ ] HTTPS funciona sin avisos
- [ ] `.env` no es accesible públicamente
- [ ] `config.php` no está en web root
- [ ] `/logs/` no es accesible públicamente

---

## 🆘 TROUBLESHOOTING

### Problema: 500 Internal Server Error

**Acciones:**
- [ ] Revisar que carpeta `/app/` está FUERA de `public_html`
- [ ] Revisar que `config.php` está en raíz
- [ ] Ver logs: `cPanel → File Manager → logs/php_errors.log`
- [ ] Verificar permisos (755, 777)

### Problema: "Cannot find module" o "Class not found"

**Acciones:**
- [ ] BASE_PATH en `config.php` debe ser: `/home/usuario/public_html/..`
- [ ] NO debe ser: `C:\xampp\htdocs\aventuras`
- [ ] Revisar ruta de autoload en `index.php`

### Problema: BD no conecta

**Acciones:**
- [ ] Host: `localhost` (NO IP externa)
- [ ] Usuario: `u850459552_aventuraspcl` (correcto)
- [ ] Password: `Arejade2409.` (exacto)
- [ ] Base de datos: `u850459552_aventuraspcl` (existe)
- [ ] Probar en phpMyAdmin primero

### Problema: Archivos no se suben

**Acciones:**
- [ ] Permisos `/storage/`: 755+
- [ ] Usuarios correctos en config
- [ ] No hay restricciones de tamaño

### Problema: HTTPS no redirige

**Acciones:**
- [ ] `.htaccess` en `public_html/` debe tener:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```
- [ ] mod_rewrite activado en Hostinger
- [ ] Contactar soporte Hostinger si no funciona

---

## ✅ LISTA FINAL DE VERIFICACIÓN

**Estado actual:** ________________  
**Responsable:** ________________  
**Fecha:** ________________

### Antes de marcar como LISTO:

- [ ] Todos los archivos subidos
- [ ] Todas las carpetas creadas
- [ ] Permisos configurados correctamente
- [ ] `.env` creado en servidor
- [ ] BD importada y accesible
- [ ] Home page carga ✅
- [ ] Admin login funciona ✅
- [ ] Formularios funcionan ✅
- [ ] Búsquedas funcionan ✅
- [ ] Uploads funcionan ✅
- [ ] HTTPS redirige ✅
- [ ] Sin errores en logs ✅
- [ ] Usuario creado en BD ✅

---

## 🎉 DEPLOYMENT COMPLETADO

✅ Sistema en producción  
✅ Dominio activo  
✅ BD conectada  
✅ Todos los tests PASS  

**URL:** https://aventurastravelpucallpa.com/  
**Admin:** https://aventurastravelpucallpa.com/admin/login

---

## 📞 SOPORTE RÁPIDO

**Ver archivo:** `PRODUCTION_DEPLOYMENT_HOSTINGER.md` para detalles completos

**Logs:**
- `logs/php_errors.log` - Errores PHP
- cPanel → Error Logs - Errores de servidor

---

*Documento: 2026-05-14*
