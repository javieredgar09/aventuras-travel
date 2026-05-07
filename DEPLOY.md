# 🚀 Guía de Deploy – Aventuras Travel en Hostinger

## Requisitos
- **PHP** ≥ 8.0
- **MySQL** ≥ 5.7 / MariaDB 10+
- **Apache** con `mod_rewrite` habilitado
- Soporte para `.htaccess`

---

## PASO 1 – Preparar la Base de Datos en Hostinger

1. Ve al **Panel de Hostinger → Bases de Datos → MySQL Databases**
2. Crea una nueva base de datos (ej: `u123456_aventuras`)
3. Crea un usuario BD y asígnale todos los permisos
4. Ve a **phpMyAdmin** e importa el archivo SQL:
   - Archivo: `/database/schema.sql` (o el export de tu BD local)

### Exportar BD local desde XAMPP
```bash
# En XAMPP Terminal o CMD:
"C:\xampp\mysql\bin\mysqldump.exe" -u root aventuras > aventuras_backup.sql
```

---

## PASO 2 – Subir los Archivos

### Opción A: File Manager de Hostinger
1. Comprime la carpeta `/aventuras` en un ZIP
2. En File Manager, sube el ZIP al directorio `public_html/`
3. Extrae el ZIP ahí mismo

### Opción B: FTP (FileZilla)
```
Host: tu-dominio.com (o IP de Hostinger)
Usuario: u123456 (tu usuario FTP)
Contraseña: la que asignaste
Puerto: 21
```
- Sube TODO el contenido de `c:\xampp\htdocs\aventuras\` a `public_html/`

---

## PASO 3 – Configurar `config.php` en Hostinger

⚠️ **CRÍTICO**: Edita el archivo `config.php` en el servidor con los datos reales:

```php
// Cambiar estas líneas:
define('APP_ENV',  'production');    // ← cambiar a 'production'
define('APP_BASE', '');              // ← vacío si está en dominio raíz
                                     //    o '/carpeta' si está en subdirectorio

define('DB_HOST', 'localhost');      // ← normalmente 'localhost' en Hostinger
define('DB_NAME', 'u123456_aventuras'); // ← tu nombre de BD en Hostinger
define('DB_USER', 'u123456_user');   // ← tu usuario BD en Hostinger
define('DB_PASS', 'TuPasswordSeguro123!'); // ← tu contraseña BD
```

---

## PASO 4 – Verificar `.htaccess`

Si tu sitio está en el **dominio raíz** (`tudominio.com`):
```apache
RewriteBase /
```

Si está en una **subcarpeta** (`tudominio.com/aventuras`):
```apache
RewriteBase /aventuras/
```

También cambia las referencias a `/aventuras/public/$1` por la ruta correcta.

---

## PASO 5 – Permisos de Carpetas

```bash
chmod 755 storage/
chmod 755 storage/comprobantes/
chmod 755 storage/contratos/
chmod 755 logs/
```

En Hostinger File Manager: clic derecho → Change Permissions → 755

---

## PASO 6 – Verificación

Visita tu dominio y verifica:
- [ ] Página principal carga correctamente
- [ ] Login funciona (admin y clientes)
- [ ] Dashboard admin accesible
- [ ] Subida de comprobantes funciona
- [ ] `/seed-fresh` NO es accesible desde afuera sin login admin
- [ ] `config.php` NO es descargable (retorna 403)

---

## 🔐 CHECKLIST DE SEGURIDAD FINAL

- [ ] `APP_ENV = 'production'` en config.php
- [ ] `display_errors = Off` (se activa automáticamente con production)
- [ ] Contraseña BD segura (no vacía)
- [ ] `APP_BASE` configurado correctamente
- [ ] Archivos `.env`, `seed.php`, `*.bak` NO subidos
- [ ] HTTPS habilitado en Hostinger (Let's Encrypt gratuito)
- [ ] Permisos de carpetas correctos (755 directorios, 644 archivos)

---

## 📁 Archivos que NO debes subir a producción

```
.git/
logs/*.log
public/seed.php
scripts/
*.bak
.gitignore
README.md
DEPLOY.md (este archivo)
```

---

## 🆘 Soporte

Si hay errores en producción, revisa:
1. `logs/error.log` en el servidor
2. Los logs de Apache en Hostinger → `hPanel → Error Logs`
