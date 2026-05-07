# 🚀 GUÍA RÁPIDA - SUBIR A HOSTINGER (15 MINUTOS)

**Tu dominio:** https://aventurastravelpucallpa.com/  
**Tu base de datos:** u850459552_aventuraspcl  
**Tu usuario BD:** u850459552_aventuraspcl  
**Tu contraseña BD:** Arejade2409.

---

## ⚡ MÉTODO RÁPIDO (SIN TERMINAL)

### PASO 1: Crear archivo `.env` (2 minutos)

En tu computadora local, crea un archivo llamado `.env` en la raíz del proyecto con esto:

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

⚠️ **NO SUBIR ESTE ARCHIVO A GIT**

---

### PASO 2: Crear archivo `.htaccess` (1 minuto)

Crear archivo llamado `.htaccess` en la carpeta `public_html` con esto:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [QSA,L]
    
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

<FilesMatch "\.(env|json|lock|md)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

---

### PASO 3: Organizar archivos para subida (1 minuto)

Tu proyecto debe estar así:

```
aventurastravelpucallpa.com/
├── .env                    ← Crear (NO SUBIR)
├── config.php              ← Ya existe (MODIFICADO ✅)
├── app/                    ← SUBIR
├── core/                   ← SUBIR
├── routes/                 ← SUBIR
├── database/               ← SUBIR
├── logs/                   ← Crear carpeta vacía
├── storage/                ← SUBIR
│   ├── comprobantes/
│   ├── contratos/
│   ├── recibos/
│   └── vouchers/
├── public_html/            ← SUBIR
│   ├── .htaccess           ← Crear
│   ├── index.php
│   ├── storage.php
│   ├── assets/
│   ├── img/
│   └── storage/            ← Subcarpetas
├── vendor/                 ← SUBIR (si existe)
└── .gitignore              ← CREAR (opcional)
```

---

### PASO 4: Subir archivos por FTP (8 minutos)

#### 4.1 Usar File Manager de Hostinger

1. Login → cPanel
2. File Manager → public_html
3. Subir archivos:
   - `index.php` ✅
   - `storage.php` ✅
   - `descargar-recibo.php` ✅
   - `.htaccess` ✅ (archivo oculto)
   - Carpetas: `assets/`, `img/`, `storage/`

#### 4.2 Subir carpetas FUERA de public_html

1. File Manager → Raíz del dominio
2. Subir:
   - `app/` ✅
   - `core/` ✅
   - `routes/` ✅
   - `database/` ✅
   - `config.php` ✅ (ya existe, actualizar si cambió)
   - `vendor/` ✅ (si existe)

#### 4.3 Crear carpetas vacías

1. Crear carpeta: `logs/` (permisos 777)
2. Crear carpeta: `storage/` si no existe
3. Subcarpetas en storage:
   - `comprobantes/` (755)
   - `contratos/` (755)
   - `recibos/` (755)
   - `vouchers/` (755)

---

### PASO 5: Crear archivo `.env` en servidor (2 minutos)

1. **cPanel** → File Manager
2. Navegar a raíz del dominio (NO public_html)
3. Click derecho → Crear nuevo archivo → `.env`
4. Editar y copiar esto:

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

---

### PASO 6: Importar base de datos (1 minuto)

1. **cPanel** → phpMyAdmin
2. Seleccionar BD: `u850459552_aventuraspcl`
3. Ir a **Import**
4. Seleccionar archivos en orden:
   - `database/schema.sql` ✅
   - `database/seed_data.sql` ✅

---

### PASO 7: Verificar permisos (1 minuto)

1. **cPanel** → File Manager
2. Click derecho en carpeta → Permissions
3. Configurar:
   - `/logs/` → 777
   - `/storage/` → 755
   - `/storage/comprobantes/` → 755
   - `/public_html/` → 755

---

### PASO 8: Probar (1 minuto)

Acceder a:
- Home: https://aventurastravelpucallpa.com/ ✅
- Admin login: https://aventurastravelpucallpa.com/admin/login ✅

---

## 🆘 SI ALGO FALLA

### Error: "Cannot find module" o 500
**Solución:** Revisar que carpetas `app/`, `core/`, `routes/` estén FUERA de `public_html`

### Error: "Access denied" en BD
**Solución:** 
1. Verificar usuario/contraseña en `.env`
2. Usuario correcto: `u850459552_aventuraspcl`
3. Password: `Arejade2409.`

### Ruta 404 en admin
**Solución:** `.htaccess` debe estar en `public_html/` + `mod_rewrite` activado en Hostinger

### No se suben archivos
**Solución:** Carpeta `/storage/` debe tener permisos 755+ (755 es OK)

---

## ✅ CHECKLIST FINAL

- [ ] `.env` creado localmente
- [ ] `.htaccess` creado en `public_html/`
- [ ] Archivos subidos por FTP
- [ ] `.env` creado en servidor
- [ ] BD importada (schema + seed)
- [ ] Permisos configurados
- [ ] Home page carga ✅
- [ ] Admin login funciona ✅
- [ ] HTTPS redirige correctamente ✅

---

## 🎉 ¡LISTO!

Tu sitio está en: **https://aventurastravelpucallpa.com/**

Admin: `https://aventurastravelpucallpa.com/admin/login`

---

**¿Necesitas ayuda?** Ver: `PRODUCTION_DEPLOYMENT_HOSTINGER.md` para más detalles
