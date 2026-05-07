# 📱 HOSTINGER FILE MANAGER - PASO A PASO VISUAL

**Dominio:** aventurastravelpucallpa.com  
**Método:** cPanel File Manager (Sin FTP)

---

## 🎯 RESUMEN VISUAL DE ESTRUCTURA

```
HOSTINGER - Raíz del dominio
│
├── 📁 public_html/             ← DENTRO DE AQUÍ
│   ├── index.php
│   ├── storage.php
│   ├── descargar-recibo.php
│   ├── .htaccess               ← CREAR
│   ├── 📁 assets/
│   ├── 📁 img/
│   └── 📁 storage/ (vacía)
│
├── 📁 app/                     ← FUERA de public_html
├── 📁 core/
├── 📁 routes/
├── 📁 database/
├── 📁 logs/                    ← CREAR (permisos 777)
├── 📁 storage/                 ← CREAR (permisos 755)
│   ├── 📁 comprobantes/
│   ├── 📁 contratos/
│   ├── 📁 recibos/
│   └── 📁 vouchers/
├── 📄 config.php               ← YA EXISTE (actualizar)
├── 📄 .env                     ← CREAR AQUÍ
└── 📄 vendor/ (si existe)
```

---

## 🔑 PASO 1: ENTRAR A cPANEL

### 1.1 Login

1. **URL:** https://tu-dominio.com:2083/
   - O: Tu panel de Hostinger

2. **Credenciales:** 
   - Usuario: (tu email)
   - Contraseña: (tu contraseña Hostinger)

3. **Click:** LOGIN

### 1.2 Ir a File Manager

1. Buscar **File Manager** en cPanel
2. Click en icono de carpeta
3. Seleccionar: **Web Root (public_html)** o **Home Directory**

---

## 📁 PASO 2: ESTRUCTURA ACTUAL

**Lo que VAS a ver:**

```
📁 public_html/
   ├── index.php                 ← Ya existe
   ├── storage.php               ← Ya existe
   ├── descargar-recibo.php      ← Ya existe
   ├── 📁 assets/                ← Ya existe
   ├── 📁 img/                   ← Ya existe
   ├── 📁 storage/               ← Puede existir o no
   ├── 📁 cache/
   ├── 📁 mailoutput/
   └── ...
```

---

## ✅ PASO 3: SUBIDA DE ARCHIVOS A `/public_html/`

### 3.1 Crear archivo `.htaccess`

1. **En File Manager:** Click derecho → **Create New File**
2. **Nombre:** `.htaccess`
3. **Edit:** Copiar esto:

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

<FilesMatch "\.(env|json|lock)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

4. **Save**

### 3.2 Subir archivos a `public_html/`

1. **Upload:**
   - `public_html/index.php` (reemplazar)
   - `public_html/storage.php` (reemplazar)
   - `public_html/descargar-recibo.php` (reemplazar)
   - `public_html/assets/` (carpeta completa)
   - `public_html/img/` (carpeta completa)

2. **Verificar carpeta `storage/` en `public_html/`:**
   - Si no existe, crear subdirectories:
     - `storage/`
     - `storage/comprobantes/`
     - `storage/contratos/`
     - `storage/recibos/`
     - `storage/vouchers/`

---

## 🚀 PASO 4: SUBIDA A CARPETA RAÍZ

### 4.1 Ir a carpeta superior (raíz del dominio)

1. **File Manager:** Click en home icon o click **Up** en la barra
2. **Debe verse:** Carpeta `public_html` + otras carpetas

### 4.2 Subir carpetas principales

1. **Upload carpetas:**
   - `app/` (completa)
   - `core/` (completa)
   - `routes/` (completa)
   - `database/` (completa)

2. **Upload archivo:**
   - `config.php` (reemplazar si existe)

3. **Upload vendor/ si existe:**
   - `vendor/` (completa, si tienes Composer)

### 4.3 Crear carpetas faltantes

1. **Click derecho → Create Folder:**
   - `logs/`
   - `storage/`

2. **En `storage/` crear subcarpetas:**
   - Click dentro de `storage/`
   - Create Folder: `comprobantes/`
   - Create Folder: `contratos/`
   - Create Folder: `recibos/`
   - Create Folder: `vouchers/`

---

## 🔐 PASO 5: CONFIGURAR PERMISOS

### 5.1 Permisos de carpetas

1. **Seleccionar carpeta → Right Click → Change Permissions**

**Configurar:**

| Carpeta | Permisos | Owner | Group | Other |
|---------|----------|-------|-------|-------|
| `logs/` | 777 | R+W+X | R+W+X | R+W+X |
| `storage/` | 755 | R+W+X | R+X | R+X |
| `comprobantes/` | 755 | R+W+X | R+X | R+X |
| `contratos/` | 755 | R+W+X | R+X | R+X |
| `recibos/` | 755 | R+W+X | R+X | R+X |
| `vouchers/` | 755 | R+W+X | R+X | R+X |
| `public_html/` | 755 | R+W+X | R+X | R+X |

---

## 📄 PASO 6: CREAR ARCHIVO `.env` EN SERVIDOR

### 6.1 En raíz del dominio (NO en public_html)

1. **File Manager:** Estar en raíz (ver carpeta `public_html`)
2. **Right Click → Create New File**
3. **Nombre:** `.env`

### 6.2 Editar `.env`

1. **Click en `.env` → Edit**
2. **Copiar todo esto:**

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

3. **Save**

### 6.3 Permisos de `.env`

1. **Right Click `.env` → Change Permissions**
2. **Configurar:** 644 (solo owner puede leer/escribir)

---

## 🗄️ PASO 7: BASE DE DATOS

### 7.1 Acceder a phpMyAdmin

1. **cPanel → phpMyAdmin**
2. **o:** https://aventurastravelpucallpa.com/cpanel/phpmyadmin

### 7.2 Seleccionar BD

1. **Left panel:** Base de datos `u850459552_aventuraspcl`
2. **Debe ver:** Lista de tablas vacía (primera vez)

### 7.3 Importar SQL

1. **Tab superior: Import**
2. **File to import:** Seleccionar `database/schema.sql`
3. **Click: Go/Import**
4. **Esperar...** hasta que termine

### 7.4 Importar datos iniciales

1. **Tab: Import nuevamente**
2. **File to import:** Seleccionar `database/seed_data.sql`
3. **Click: Go/Import**
4. **Esperar...** hasta que termine

### 7.5 Verificar

1. **Tab: Structure**
2. **Debe ver tablas:**
   - `usuarios`
   - `grupos`
   - `contratos`
   - `pasajeros`
   - `servicios_grupo`
   - `pagos`
   - etc.

---

## 🧪 PASO 8: PRUEBAS

### Test 1: Home page

```
Ir a: https://aventurastravelpucallpa.com/
Debe ver: Página de inicio (sin errores)
```

### Test 2: Admin login

```
Ir a: https://aventurastravelpucallpa.com/admin/login
Debe ver: Formulario de login
```

### Test 3: Admin dashboard

```
User: admin (o el que creaste)
Pass: (tu contraseña)
Debe ver: Dashboard con datos
```

---

## 🔍 SI ALGO FALLA

### Error 500

**Revisar:**
1. `logs/php_errors.log` en File Manager
2. cPanel → **Error Logs**
3. Verificar que `/app/`, `/core/`, `/routes/` están FUERA de `public_html`

### BD no conecta

**Verificar en `.env`:**
```
✅ DB_HOST=localhost (NO IP)
✅ DB_NAME=u850459552_aventuraspcl (exacto)
✅ DB_USER=u850459552_aventuraspcl (exacto)
✅ DB_PASS=Arejade2409. (exacto)
```

### Ruta 404 en admin

**Verificar:**
1. `.htaccess` en `public_html/` existe
2. mod_rewrite activado (Hostinger lo tiene por defecto)

---

## ✅ CHECKLIST RÁPIDO

- [ ] Carpetas subidas a raíz: `app/`, `core/`, `routes/`, `database/`
- [ ] Archivos en `public_html/`: `index.php`, `.htaccess`
- [ ] Carpetas creadas: `logs/` (777), `storage/` (755)
- [ ] `.env` creado en raíz
- [ ] BD importada (schema + seed)
- [ ] Permisos configurados
- [ ] Home funciona ✅
- [ ] Admin login funciona ✅

---

## 🎉 ¡DEPLOYMENT COMPLETADO!

Tu sitio está en: **https://aventurastravelpucallpa.com/**

Admin: `https://aventurastravelpucallpa.com/admin/login`

---

*Documento actualizado: 2026-05-14*
