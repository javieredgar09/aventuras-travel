# 🚀 GUÍA COMPLETA - DEPLOYMENT A HOSTINGER

**Dominio:** https://aventurastravelpucallpa.com/  
**Hosting:** Hostinger  
**Base de Datos:** u850459552_aventuraspcl  

---

## 📋 INFORMACIÓN DE HOSTINGER (Adjuntada)

```
MySQL Database: u850459552_aventuraspcl
MySQL Username: u850459552_aventuraspcl
MySQL Password: Arejade2409.
```

---

## 🔧 PASO 1: PREPARAR CONFIG LOCAL

### 1.1 Crear archivo `.env` (NO subir a git)
```bash
# En raíz del proyecto
cp .env.example .env
```

### 1.2 Editar `.env` con datos de PRODUCCIÓN
```env
# PRODUCTION CONFIG
DB_HOST=localhost
DB_PORT=3306
DB_NAME=u850459552_aventuraspcl
DB_USER=u850459552_aventuraspcl
DB_PASS=Arejade2409.

APP_ENV=production
APP_DEBUG=false
APP_URL=https://aventurastravelpucallpa.com

# SERPAPI
SERPAPI_KEY=544a43ee854dfa60b1d14779cdc6f9e58f0ff02831d3ad21f11dd35dc019260b

# EMAIL
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USER=tu_email@aventurastravelpucallpa.com
MAIL_PASS=tu_contraseña
```

---

## 📁 PASO 2: PREPARAR CARPETAS PARA SUBIDA

### Estructura a subir:
```
aventurastravelpucallpa.com/
├── public_html/               ← Apunta aquí
│   ├── index.php
│   ├── storage.php
│   ├── descargar-recibo.php
│   ├── assets/
│   ├── img/
│   ├── storage/              ← PERMISOS 755
│   │   ├── comprobantes/
│   │   ├── contratos/
│   │   ├── recibos/
│   │   └── vouchers/
│
├── app/                       ← Fuera de public_html
├── core/
├── routes/
├── database/
├── logs/                      ← PERMISOS 777
├── config.php
├── .env                       ← NO SUBIR AQUÍ, crear en servidor
├── .htaccess                  ← Archivo de reescritura
```

---

## 🌐 PASO 3: CREAR ARCHIVO `.htaccess`

Crear `public_html/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirigir a index.php
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [QSA,L]
    
    # HTTPS Force
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# Seguridad: Bloquear acceso a archivos sensibles
<FilesMatch "\.(env|json|lock|md)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

---

## 🔐 PASO 4: CREAR ARCHIVO `.env` EN HOSTINGER (vía cPanel)

### Método 1: Via File Manager de cPanel

1. **Acceder a cPanel** → File Manager
2. Navegar a raíz del dominio (fuera de `public_html`)
3. Crear nuevo archivo: `.env`
4. Editar y pegar:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=u850459552_aventuraspcl
DB_USER=u850459552_aventuraspcl
DB_PASS=Arejade2409.

APP_ENV=production
APP_DEBUG=false
APP_URL=https://aventurastravelpucallpa.com

SERPAPI_KEY=544a43ee854dfa60b1d14779cdc6f9e58f0ff02831d3ad21f11dd35dc019260b
```

---

## 📤 PASO 5: SUBIR ARCHIVOS VÍA FTP

### 5.1 Configurar FTP (desde cPanel)

1. **cPanel** → FTP Accounts
2. Crear FTP user (ej: `deployuser`)
3. Contraseña fuerte
4. Home Directory: `/public_html`

### 5.2 Usar cliente FTP (ej: FileZilla)

```
Host: aventurastravelpucallpa.com
User: deployuser@aventurastravelpucallpa.com
Pass: [contraseña creada]
Port: 21
```

### 5.3 Estructura de subida

**Carpetas a crear en `/public_html`:**

```
/public_html/
├── index.php                ← Subir
├── descargar-recibo.php     ← Subir
├── storage.php              ← Subir
├── assets/                  ← Subir (CSS/JS)
├── img/                     ← Subir (imágenes)
├── storage/                 ← Crear carpetas vacías:
│   ├── comprobantes/
│   ├── contratos/
│   ├── recibos/
│   └── vouchers/
```

**Carpetas FUERA de `/public_html`** (directorio raíz del dominio):

```
/
├── .htaccess                ← Crear
├── .env                     ← Crear aquí (NO subir local)
├── app/                     ← Subir completa
├── core/                    ← Subir completa
├── routes/                  ← Subir completa
├── database/                ← Subir completa
├── logs/                    ← Crear carpeta vacía
├── config.php               ← MODIFICAR (ver paso 6)
├── RESUMEN_FIXES.md         ← Opcional (subir)
├── DEPLOYMENT_FIXES.md      ← Opcional (subir)
```

---

## ⚙️ PASO 6: MODIFICAR `config.php` PARA PRODUCCIÓN

### Crear versión de producción

Editar `config.php` para que lea de `.env`:

```php
<?php
// config.php - Aventuras Travel

// Detectar ambiente
$env_file = dirname(__DIR__) . '/.env';
$isProduction = file_exists($env_file);

// Cargar .env si existe (producción)
if ($isProduction) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') === false || strpos($line, '#') === 0) continue;
        [$key, $value] = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// Definir constantes con fallback a valores locales
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');

// Base de datos
define('DATABASE_HOST', getenv('DB_HOST') ?: 'localhost');
define('DATABASE_PORT', getenv('DB_PORT') ?: 3306);
define('DATABASE_NAME', getenv('DB_NAME') ?: 'aventuras');
define('DATABASE_USER', getenv('DB_USER') ?: 'root');
define('DATABASE_PASS', getenv('DB_PASS') ?: '');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', BASE_PATH . '/core');
define('PUBLIC_PATH', BASE_PATH . '/public_html');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('LOGS_PATH', BASE_PATH . '/logs');

// URLs
define('APP_NAME', 'Aventuras Travel');
define('TIMEZONE', 'America/Lima');

// API Keys (from .env or hardcoded fallback)
define('SERPAPI_KEY', getenv('SERPAPI_KEY') ?: '544a43ee854dfa60b1d14779cdc6f9e58f0ff02831d3ad21f11dd35dc019260b');

// Seguridad
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// HTTPS en producción
if (APP_ENV === 'production' && !isset($_SERVER['HTTPS'])) {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
    exit;
}

// Manejo de errores
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . '/php_errors.log');
}

// Timezone
date_default_timezone_set(TIMEZONE);

// Incluir autoload si existe
$autoload = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}
```

---

## 🔒 PASO 7: PERMISOS EN HOSTINGER

### Via cPanel File Manager:

```
/logs/                 → 777 (read/write/execute)
/storage/              → 755 (lectura para web)
/storage/comprobantes/ → 755
/storage/contratos/    → 755
/storage/recibos/      → 755
/storage/vouchers/     → 755
/public_html/          → 755
.env                   → 644 (privado)
config.php             → 644
```

### Command via SSH (si tienes acceso):
```bash
chmod 777 logs/
chmod 755 storage/
chmod 755 storage/comprobantes/
chmod 755 storage/contratos/
chmod 755 storage/recibos/
chmod 755 storage/vouchers/
chmod 644 .env
chmod 644 config.php
```

---

## 🗄️ PASO 8: CREAR BASE DE DATOS EN HOSTINGER

### 8.1 Via cPanel MySQL

1. **cPanel** → MySQL Databases
2. Ya debe estar creada: `u850459552_aventuraspcl`

### 8.2 Importar esquema de BD

1. **cPanel** → phpMyAdmin
2. Seleccionar base de datos: `u850459552_aventuraspcl`
3. Ir a **Import** → Seleccionar archivo

**Archivos SQL a ejecutar en orden:**
1. `database/schema.sql` ← Estructura
2. `database/seed_data.sql` ← Datos iniciales

O ejecutar en terminal:

```bash
# Via SSH
mysql -h localhost -u u850459552_aventuraspcl -p u850459552_aventuraspcl < database/schema.sql
mysql -h localhost -u u850459552_aventuraspcl -p u850459552_aventuraspcl < database/seed_data.sql
```

---

## ✅ PASO 9: VERIFICAR INSTALACIÓN

### 9.1 Test de conexión
Crear archivo `test.php` en `public_html/`:

```php
<?php
require_once dirname(__DIR__) . '/config.php';

echo "=== PRODUCTION TEST ===\n\n";

// Test 1: Config
echo "✓ APP_ENV: " . APP_ENV . "\n";
echo "✓ APP_URL: " . APP_URL . "\n";
echo "✓ BASE_PATH: " . BASE_PATH . "\n";

// Test 2: Base de datos
try {
    $pdo = new PDO(
        "mysql:host=" . DATABASE_HOST . ";dbname=" . DATABASE_NAME,
        DATABASE_USER,
        DATABASE_PASS
    );
    $result = $pdo->query("SELECT 1")->fetch();
    echo "✓ BD Conexión: OK\n";
    
    // Contar registros
    $grupos = $pdo->query("SELECT COUNT(*) as cnt FROM grupos")->fetch();
    echo "✓ Grupos en BD: " . $grupos['cnt'] . "\n";
} catch (Exception $e) {
    echo "✗ BD Error: " . $e->getMessage() . "\n";
}

// Test 3: Archivos
echo "✓ /storage: " . (is_dir(STORAGE_PATH) ? "OK" : "MISSING") . "\n";
echo "✓ /logs: " . (is_dir(LOGS_PATH) ? "OK" : "MISSING") . "\n";

echo "\n✅ Tests completados\n";
?>
```

Acceder a: `https://aventurastravelpucallpa.com/test.php`

### 9.2 Test de rutas
- Admin login: `https://aventurastravelpucallpa.com/admin/login`
- Home: `https://aventurastravelpucallpa.com/`

---

## 🆘 PASO 10: TROUBLESHOOTING

### Problema: "Cannot find module" o error 500
**Solución:**
```bash
# Verificar rutas en config.php
# BASE_PATH debe ser: /home/username/public_html/..
# No debe ser: C:\xampp\htdocs\aventuras
```

### Problema: BD no conecta
**Solución:**
```bash
# Verificar en cPanel:
# 1. Usuario BD: u850459552_aventuraspcl
# 2. Contraseña: Arejade2409.
# 3. Host: localhost (no IP externa)
# 4. Base datos: u850459552_aventuraspcl
```

### Problema: HTTPS no redirige
**Solución:**
```apache
# En .htaccess, asegurar:
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Problema: Archivos no se suben correctamente
**Solución:**
```bash
# Verificar permisos:
ls -la /public_html/storage/
# Debe ser: drwxr-xr-x (755)

# Si necesita escribir:
chmod 777 /public_html/storage/
```

---

## 📊 CHECKLIST FINAL ANTES DE DEPLOYMENT

- [ ] `.env` creado con credenciales correctas
- [ ] `config.php` modificado para leer `.env`
- [ ] `.htaccess` creado en `public_html/`
- [ ] Base de datos importada (`schema.sql` + `seed_data.sql`)
- [ ] Carpetas `/storage/`, `/logs/` con permisos correctos
- [ ] FTP configurado y archivos subidos
- [ ] `test.php` retorna OK
- [ ] Admin login funciona
- [ ] Home page carga sin errores
- [ ] HTTPS funciona correctamente
- [ ] `test.php` eliminado de `public_html/`

---

## 🎉 ¡LISTO!

Tu sitio está en producción en:
```
https://aventurastravelpucallpa.com/
```

**Acceso Admin:**
```
URL: https://aventurastravelpucallpa.com/admin/login
Usuario: admin (o según datos seed)
```

---

## 📞 SOPORTE RÁPIDO

**Si algo falla:**

1. Revisar logs:
```bash
tail -f /home/username/public_html/../logs/php_errors.log
```

2. Ejecutar test:
```bash
php -r "require '/path/config.php'; echo 'OK';"
```

3. Verificar permisos:
```bash
ls -la /home/username/
```

---

**¡Deployment completado exitosamente!** 🚀

Documento actualizado: 2026-05-14
