# ✈️ Aventuras Travel — Sistema Web de Turismo y Gestión de Clientes

<p align="center">
  <img src="public/img/a_color.png" alt="Aventuras Travel Logo" width="220">
</p>

<p align="center">
  <strong>Plataforma web completa para agencia de viajes</strong><br>
  PHP Puro MVC · MySQL · Tailwind CSS · API REST
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind">
  <img src="https://img.shields.io/badge/Licencia-MIT-green" alt="MIT License">
</p>

---

## 📋 Descripción

Sistema web profesional desarrollado en **PHP puro (sin frameworks)** con arquitectura **MVC desde cero**, diseñado para la gestión integral de una agencia de viajes. Incluye panel administrativo, área de clientes (familiares y escolares), buscador de destinos con APIs externas, sistema de pagos con cuotas y API REST interna.

## 🎯 Características Principales

### 🌐 Frontend Público
- **Buscador de Destinos** con integración de APIs externas:
  - 🌦️ Clima en tiempo real ([Open-Meteo](https://open-meteo.com/))
  - 💱 Tipo de cambio ([MoneyConvert](https://moneyconvert.net/))
  - 🗺️ Google Maps, hoteles, lugares turísticos
- **Promociones** dinámicas desde base de datos
- Diseño responsive con Tailwind CSS

### 👥 Sistema de Clientes
| Tipo | Login | Funcionalidad |
|------|-------|--------------|
| **Familiar** | Código FILE + contraseña | Ver vuelos, pasajeros, servicios, pagos; subir comprobantes |
| **Escolar (Colegio)** | Código por contrato | Ver su contrato, cuotas, subir comprobantes |
| **Representante** | Código representante | Ver TODOS los contratos del grupo, control de pagos |

### ⚙️ Panel Administrativo
- Gestión completa de **ventas** (Familiar / Escolar)
- CRUD de **contratos**, **pasajeros**, **vuelos**, **servicios**
- Sistema de **pagos** con cuotas en cascada y aprobación/rechazo
- Gestión de **usuarios** (crear, editar, activar/desactivar, reset password)
- **Promociones** con CRUD completo
- **Reportes** y exportación CSV

### 💳 Sistema de Pagos Avanzado
- Plan de cuotas por contrato
- Pago en cascada (excedente se aplica automáticamente a la siguiente cuota)
- Subida de comprobantes con validación
- Flujo: Cliente sube comprobante → Admin valida → Sistema actualiza cuotas
- Estados: `pendiente`, `aprobado`, `rechazado`, `atrasado`

### 🔌 API REST Interna
```
GET    /api/clientes          → Listar clientes (auth)
GET    /api/contratos/{id}    → Detalle de contrato (auth)
GET    /api/pagos             → Listar pagos (auth)
POST   /api/pagos             → Registrar pago (auth + CSRF)
GET    /api/promociones       → Listar promociones (público)
GET    /api/images?q=...      → Proxy SerpAPI imágenes
```

---

## 🏗️ Arquitectura

```
aventuras-travel/
├── app/
│   ├── controllers/        # Controladores (Admin, Client, Auth, API...)
│   │   └── api/            # API REST controllers
│   ├── models/             # Modelos (Contrato, Pago, Cuota, Usuario...)
│   ├── views/              # Vistas PHP con Tailwind
│   │   ├── admin/          # Panel administrativo
│   │   ├── client/         # Área de clientes
│   │   ├── auth/           # Login
│   │   ├── home/           # Público
│   │   └── layouts/        # Layouts (admin, auth, main, client)
│   ├── middlewares/         # AuthMiddleware, AdminMiddleware
│   └── services/           # EmailService, PaymentService, SerpApiService
├── core/
│   ├── Controller.php      # Base controller (render, CSRF, flash, auth)
│   ├── Model.php           # Base model (CRUD genérico con PDO)
│   ├── Database.php        # Singleton PDO con prepared statements
│   ├── Router.php          # Enrutador web + API
│   └── Session.php         # Sesiones seguras (httponly, samesite)
├── database/
│   ├── schema.sql          # Esquema completo de la BD
│   ├── seed_data.sql       # Datos de prueba
│   └── migration_*.sql     # Migraciones incrementales
├── public/
│   ├── index.php           # Front Controller (entry point)
│   └── assets/             # CSS y JS (1 archivo cada uno)
├── routes/
│   ├── web.php             # Rutas web (públicas, admin, cliente)
│   └── api.php             # Rutas API REST
└── storage/                # Archivos subidos (comprobantes, cache)
```

---

## 🔒 Seguridad

| Protección | Implementación |
|-----------|---------------|
| **SQL Injection** | PDO con prepared statements en todas las queries |
| **XSS** | `htmlspecialchars()` en todas las salidas, `JSON_HEX_*` flags |
| **CSRF** | Tokens en todos los formularios + verificación server-side |
| **Passwords** | `password_hash()` + `password_verify()` (bcrypt) |
| **Sesiones** | `httponly`, `samesite=Strict`, `session_regenerate_id()` |
| **File Upload** | Validación tipo MIME, tamaño (5MB), renombrado con hash |
| **Rutas** | Middleware Auth + Admin en todas las rutas protegidas |
| **API** | Autenticación requerida en endpoints sensibles |

---

## 🚀 Instalación

### Requisitos
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.4+
- Apache con `mod_rewrite` habilitado
- XAMPP, WAMP, Laragon o servidor web compatible

### Pasos

1. **Clonar el repositorio**
   ```bash
   cd /ruta/a/htdocs
   git clone https://github.com/javieredgar09/aventuras-travel.git aventuras
   ```

2. **Crear la base de datos**
   ```sql
   CREATE DATABASE aventuras CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Ejecutar el schema y migraciones**
   ```bash
   mysql -u root aventuras < database/schema.sql
   mysql -u root aventuras < database/migration_ventas.sql
   mysql -u root aventuras < database/migration_v3.sql
   mysql -u root aventuras < database/migration_v4_payments.sql
   ```

4. **Cargar datos de prueba (opcional)**
   ```bash
   mysql -u root aventuras < database/seed_data.sql
   ```

5. **Configurar la base de datos**
   
   Editar `core/Database.php` si tu configuración difiere:
   ```php
   $host = 'localhost';
   $dbname = 'aventuras';
   $username = 'root';
   $password = '';
   ```

6. **Verificar Apache**
   - `mod_rewrite` habilitado
   - `AllowOverride All` en la configuración del DirectoryRoot

7. **Acceder al sistema**
   ```
   http://localhost/aventuras
   ```

### Credenciales de Prueba

| Rol | Código | Contraseña |
|-----|--------|-----------|
| **Admin** | `admin` | `admin123` |
| **Cliente Familiar** | `AV-2026-001` | *(ver seed_data.sql)* |
| **Representante** | `REP-CCPA-001` | *(ver seed_data.sql)* |
| **Cliente Escolar** | `CCPA-2026-001` | *(ver seed_data.sql)* |

---

## 📸 Demo

### Página Principal
> Buscador de destinos con clima, tipo de cambio y mapa interactivo.

### Panel Admin — Dashboard
> Estadísticas, ventas recientes, gráficos de pagos por mes.

### Panel Admin — Gestión de Pagos
> Tabs Familiar/Escolar, aprobación de comprobantes, cascada de cuotas.

### Panel Admin — Detalle de Contrato
> Plan de cuotas visual, historial de transacciones, aprobar/rechazar pagos.

### Panel Admin — Gestión de Usuarios
> CRUD completo: crear, editar, activar/desactivar, reset password por tipo.

### Área de Cliente
> Dashboard personalizado, vuelos, servicios, subida de comprobantes de pago.

---

## 🛠️ Tecnologías

- **Backend:** PHP 8.2 puro (MVC desde cero, sin frameworks)
- **Base de datos:** MySQL / MariaDB con PDO
- **Frontend:** Tailwind CSS 3 (CDN), JavaScript vanilla
- **APIs externas:** Open-Meteo, MoneyConvert, Google Maps, SerpAPI
- **Iconos:** Material Symbols Outlined (Google Fonts)
- **Patrones:** MVC, Singleton (DB), Repository, Service Layer, Middleware

---

## 📄 Licencia

Este proyecto se distribuye bajo la licencia MIT. Ver [LICENSE](LICENSE) para más detalles.

---

<p align="center">
  Desarrollado con ❤️ para <strong>Aventuras Travel — Pucallpa, Perú</strong>
</p>
