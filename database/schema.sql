-- =============================================
-- Aventuras Travel - Database Schema
-- Base de datos: aventuras
-- =============================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- Tabla de usuarios (admin, clientes)
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(50) NOT NULL UNIQUE COMMENT 'FILE code o username',
    `password` VARCHAR(255) NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `apellido` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `telefono` VARCHAR(20) DEFAULT NULL,
    `rol` ENUM('admin','cliente_familiar','cliente_colegio','representante') NOT NULL DEFAULT 'cliente_familiar',
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `ultimo_acceso` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_rol` (`rol`),
    INDEX `idx_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de clientes
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT UNSIGNED NOT NULL,
    `tipo` ENUM('familiar','colegio') NOT NULL,
    `direccion` TEXT DEFAULT NULL,
    `ciudad` VARCHAR(100) DEFAULT NULL,
    `pais` VARCHAR(100) DEFAULT NULL,
    `documento_identidad` VARCHAR(50) DEFAULT NULL,
    `notas` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    INDEX `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de grupos (colegios)
DROP TABLE IF EXISTS `grupos`;
CREATE TABLE `grupos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(200) NOT NULL,
    `destino` VARCHAR(200) DEFAULT NULL,
    `destino_code` VARCHAR(200) DEFAULT NULL,
    `institucion` VARCHAR(200) DEFAULT NULL,
    `representante_id` INT UNSIGNED DEFAULT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `cantidad_pasajeros` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`representante_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    INDEX `idx_representante` (`representante_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de contratos
DROP TABLE IF EXISTS `contratos`;
CREATE TABLE `contratos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Ej: AV-2026-001, CCPA-2026-001',
    `cliente_id` INT UNSIGNED DEFAULT NULL,
    `grupo_id` INT UNSIGNED DEFAULT NULL,
    `tipo` ENUM('familiar','colegio') NOT NULL,
    `destino` VARCHAR(200) NOT NULL,
    `destino_code` VARCHAR(200) DEFAULT NULL,
    `hotel` VARCHAR(200) DEFAULT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `fecha_salida` DATE NOT NULL,
    `fecha_retorno` DATE NOT NULL,
    `valor_total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `deposito` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `saldo` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `fecha_firma` DATE DEFAULT NULL,
    `titular_nombre` VARCHAR(200) DEFAULT NULL,
    `titular_correo` VARCHAR(200) DEFAULT NULL,
    `titular_telefono` VARCHAR(50) DEFAULT NULL,
    `total_cuotas` INT DEFAULT 0,
    `meses_pago` VARCHAR(255) DEFAULT NULL,
    `tipo_pago` VARCHAR(50) DEFAULT NULL,
    `estado` ENUM('activo','completado','cancelado') NOT NULL DEFAULT 'activo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`grupo_id`) REFERENCES `grupos`(`id`) ON DELETE SET NULL,
    INDEX `idx_estado` (`estado`),
    INDEX `idx_tipo` (`tipo`),
    INDEX `idx_fecha_salida` (`fecha_salida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pasajeros
DROP TABLE IF EXISTS `pasajeros`;
CREATE TABLE `pasajeros` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contrato_id` INT UNSIGNED NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `apellido` VARCHAR(100) NOT NULL,
    `tipo` ENUM('adulto','nino','lider') NOT NULL DEFAULT 'adulto',
    `edad` INT DEFAULT NULL,
    `pasaporte` VARCHAR(50) DEFAULT NULL,
    `documento_verificado` TINYINT(1) DEFAULT 0,
    `preferencia_comida` VARCHAR(100) DEFAULT NULL,
    `notas` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contrato_id`) REFERENCES `contratos`(`id`) ON DELETE CASCADE,
    INDEX `idx_contrato` (`contrato_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de vuelos
DROP TABLE IF EXISTS `vuelos`;
CREATE TABLE `vuelos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contrato_id` INT UNSIGNED NOT NULL,
    `aerolinea` VARCHAR(100) NOT NULL,
    `numero_vuelo` VARCHAR(20) NOT NULL,
    `origen` VARCHAR(10) NOT NULL,
    `origen_ciudad` VARCHAR(100) DEFAULT NULL,
    `destino` VARCHAR(10) NOT NULL,
    `destino_ciudad` VARCHAR(100) DEFAULT NULL,
    `fecha_salida` DATETIME NOT NULL,
    `fecha_llegada` DATETIME NOT NULL,
    `tipo` ENUM('ida','vuelta','conexion') NOT NULL DEFAULT 'ida',
    `clase` ENUM('economica','business','primera') NOT NULL DEFAULT 'economica',
    `avion` VARCHAR(50) DEFAULT NULL,
    `estado` ENUM('confirmado','pendiente','cancelado') NOT NULL DEFAULT 'confirmado',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contrato_id`) REFERENCES `contratos`(`id`) ON DELETE CASCADE,
    INDEX `idx_contrato` (`contrato_id`),
    INDEX `idx_fecha` (`fecha_salida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de servicios (hotel, tours, transfers, seguro)
DROP TABLE IF EXISTS `servicios`;
CREATE TABLE `servicios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contrato_id` INT UNSIGNED NOT NULL,
    `tipo` ENUM('hotel','tour','transfer','seguro','actividad','otro') NOT NULL,
    `nombre` VARCHAR(200) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `fecha_inicio` DATE DEFAULT NULL,
    `fecha_fin` DATE DEFAULT NULL,
    `precio` DECIMAL(10,2) DEFAULT 0.00,
    `estado` ENUM('pagado','pendiente','cancelado') NOT NULL DEFAULT 'pendiente',
    `detalles_json` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contrato_id`) REFERENCES `contratos`(`id`) ON DELETE CASCADE,
    INDEX `idx_contrato` (`contrato_id`),
    INDEX `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pagos
DROP TABLE IF EXISTS `pagos`;
CREATE TABLE `pagos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contrato_id` INT UNSIGNED NULL,
    `concepto` VARCHAR(200) NOT NULL COMMENT 'Ej: Depósito, Cuota 1, Cuota 2...',
    `monto` DECIMAL(12,2) NOT NULL,
    `fecha_vencimiento` DATE NOT NULL,
    `fecha_pago` DATE DEFAULT NULL,
    `estado` ENUM('pendiente','aprobado','rechazado','atrasado') NOT NULL DEFAULT 'pendiente',
    `metodo_pago` VARCHAR(50) DEFAULT NULL,
    `referencia` VARCHAR(100) DEFAULT NULL,
    `notas_admin` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contrato_id`) REFERENCES `contratos`(`id`) ON DELETE CASCADE,
    INDEX `idx_contrato` (`contrato_id`),
    INDEX `idx_estado` (`estado`),
    INDEX `idx_vencimiento` (`fecha_vencimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de comprobantes de pago
DROP TABLE IF EXISTS `comprobantes`;
CREATE TABLE `comprobantes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `pago_id` INT UNSIGNED NOT NULL,
    `archivo_nombre` VARCHAR(255) NOT NULL,
    `archivo_hash` VARCHAR(255) NOT NULL,
    `archivo_tipo` VARCHAR(50) NOT NULL,
    `archivo_tamano` INT UNSIGNED NOT NULL,
    `estado` ENUM('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
    `notas` TEXT DEFAULT NULL,
    `revisado_por` INT UNSIGNED DEFAULT NULL,
    `fecha_revision` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`pago_id`) REFERENCES `pagos`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`revisado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    INDEX `idx_pago` (`pago_id`),
    INDEX `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de promociones (CRUD desde admin)
DROP TABLE IF EXISTS `promociones`;
CREATE TABLE `promociones` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(200) NOT NULL,
    `descripcion` TEXT NOT NULL,
    `imagen` VARCHAR(255) DEFAULT NULL,
    `destino` VARCHAR(200) DEFAULT NULL,
    `descuento` VARCHAR(50) DEFAULT NULL,
    `fecha_inicio` DATE NOT NULL,
    `fecha_fin` DATE NOT NULL,
    `activa` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_activa` (`activa`),
    INDEX `idx_fechas` (`fecha_inicio`, `fecha_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de archivos (documentos subidos)
DROP TABLE IF EXISTS `archivos`;
CREATE TABLE `archivos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contrato_id` INT UNSIGNED DEFAULT NULL,
    `pasajero_id` INT UNSIGNED DEFAULT NULL,
    `tipo` ENUM('pasaporte','documento','comprobante','contrato','otro') NOT NULL,
    `nombre_original` VARCHAR(255) NOT NULL,
    `nombre_hash` VARCHAR(255) NOT NULL,
    `ruta` VARCHAR(500) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `tamano` INT UNSIGNED NOT NULL,
    `subido_por` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contrato_id`) REFERENCES `contratos`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`pasajero_id`) REFERENCES `pasajeros`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`subido_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    INDEX `idx_contrato` (`contrato_id`),
    INDEX `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de notificaciones
DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT UNSIGNED NOT NULL,
    `titulo` VARCHAR(200) NOT NULL,
    `mensaje` TEXT NOT NULL,
    `tipo` ENUM('info','exito','advertencia','error') NOT NULL DEFAULT 'info',
    `leida` TINYINT(1) NOT NULL DEFAULT 0,
    `enlace` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    INDEX `idx_usuario` (`usuario_id`),
    INDEX `idx_leida` (`leida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- DATOS DE PRUEBA
-- =============================================

-- Admin (password: admin123)
INSERT INTO `usuarios` (`codigo`, `password`, `nombre`, `apellido`, `email`, `telefono`, `rol`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 'admin@aventurastravel.com', '+1-555-0100', 'admin');

-- Clientes familiares (password: cliente123)
INSERT INTO `usuarios` (`codigo`, `password`, `nombre`, `apellido`, `email`, `telefono`, `rol`) VALUES
('AV-2026-001', '$2y$10$YS2V/V4HJnOBz7h1r7bGOu3sN1GV9uxYlJjNKJiPqnYQ1mlmm8iVK', 'David', 'Miller', 'david.miller@email.com', '+1-555-0201', 'cliente_familiar'),
('AV-2026-002', '$2y$10$YS2V/V4HJnOBz7h1r7bGOu3sN1GV9uxYlJjNKJiPqnYQ1mlmm8iVK', 'Elena', 'Rodriguez', 'elena.rodriguez@email.com', '+34-555-0301', 'cliente_familiar'),
('AV-2026-003', '$2y$10$YS2V/V4HJnOBz7h1r7bGOu3sN1GV9uxYlJjNKJiPqnYQ1mlmm8iVK', 'James', 'Miller', 'james.miller@email.com', '+1-555-0202', 'cliente_familiar');

-- Cliente grupal representante (password: cliente123)
INSERT INTO `usuarios` (`codigo`, `password`, `nombre`, `apellido`, `email`, `telefono`, `rol`) VALUES
('GRP-2026-001', '$2y$10$YS2V/V4HJnOBz7h1r7bGOu3sN1GV9uxYlJjNKJiPqnYQ1mlmm8iVK', 'Christopher', 'Davis', 'c.davis@standrews.edu', '+44-555-0401', 'representante');

-- Clientes grupales (password: cliente123)
INSERT INTO `usuarios` (`codigo`, `password`, `nombre`, `apellido`, `email`, `telefono`, `rol`) VALUES
('GRP-2026-002', '$2y$10$YS2V/V4HJnOBz7h1r7bGOu3sN1GV9uxYlJjNKJiPqnYQ1mlmm8iVK', 'Alexander', 'Adams', 'a.adams@standrews.edu', '+44-555-0402', 'cliente_colegio'),
('GRP-2026-003', '$2y$10$YS2V/V4HJnOBz7h1r7bGOu3sN1GV9uxYlJjNKJiPqnYQ1mlmm8iVK', 'Beatrice', 'Bennett', 'b.bennett@standrews.edu', '+44-555-0403', 'cliente_colegio');

-- Jane Vargas Romero (password: cliente123)
INSERT INTO `usuarios` (`codigo`, `password`, `nombre`, `apellido`, `email`, `telefono`, `rol`) VALUES
('CCPA-2026-001', '$2y$10$YS2V/V4HJnOBz7h1r7bGOu3sN1GV9uxYlJjNKJiPqnYQ1mlmm8iVK', 'Jane', 'Vargas Romero', 'jane.vargas@email.com', '+51-555-0501', 'cliente_familiar');

-- Clientes
INSERT INTO `clientes` (`usuario_id`, `tipo`, `direccion`, `ciudad`, `pais`, `documento_identidad`) VALUES
(2, 'familiar', '123 Oak Street', 'New York', 'USA', 'US-PP-882193441'),
(3, 'familiar', 'Calle Mayor 45', 'Madrid', 'España', 'ES-DNI-12345678'),
(4, 'familiar', '456 Elm Avenue', 'Boston', 'USA', 'US-PP-882193444'),
(8, 'familiar', 'Av. Larco 234', 'Lima', 'Perú', 'PE-DNI-87654321');

-- Grupo escolar
INSERT INTO `grupos` (`nombre`, `institucion`, `representante_id`, `descripcion`, `cantidad_pasajeros`) VALUES
('St. Andrews Senior Trip 2024', 'St. Andrews Academy', 5, 'A curated educational journey across Western Europe focusing on history, art, and the foundations of modern democracy.', 42);

-- Contratos familiares
INSERT INTO `contratos` (`codigo`, `cliente_id`, `tipo`, `destino`, `hotel`, `descripcion`, `fecha_salida`, `fecha_retorno`, `valor_total`, `deposito`, `saldo`, `estado`) VALUES
('AV-2024-8812', 1, 'familiar', 'Swiss Alps & Northern Italy', 'Grand Hotel Zermatt - Suite Superior', 'The Miller Family Expedition. Your curated journey to the Swiss Alps and Northern Italy.', '2024-06-14', '2024-06-22', 14850.00, 4455.00, 10395.00, 'activo'),
('AV-2024-8902', 2, 'familiar', 'Bali, Indonesia', 'Bali Deluxe Resort & Spa', 'Bali Deluxe Package - Premium all-inclusive tropical retreat.', '2024-07-10', '2024-07-20', 3240.00, 1000.00, 2240.00, 'activo'),
('AV-2024-8901', 3, 'familiar', 'Swiss Alps', 'Alpine Lodge Zermatt', 'Swiss Alps Express - Mountain adventure expedition.', '2024-08-01', '2024-08-08', 1850.00, 500.00, 1350.00, 'activo');

-- Contrato de Jane Vargas Romero
INSERT INTO `contratos` (`codigo`, `cliente_id`, `tipo`, `destino`, `hotel`, `descripcion`, `fecha_salida`, `fecha_retorno`, `valor_total`, `deposito`, `saldo`, `estado`) VALUES
('CCPA-2026-001', 4, 'familiar', 'Punta Cana, Dominican Republic', 'Catalonia Bávaro Beach 5★ Resort & Spa', 'Elite Accommodation package with zip line and cultural tours.', '2026-10-26', '2026-11-02', 1549.00, 350.00, 1199.00, 'activo');

-- Contrato de colegio
INSERT INTO `contratos` (`codigo`, `grupo_id`, `tipo`, `destino`, `hotel`, `descripcion`, `fecha_salida`, `fecha_retorno`, `valor_total`, `deposito`, `saldo`, `estado`) VALUES
('GRP-2024-SA01', 1, 'colegio', 'Western Europe (London, Paris)', 'The Kensington Curator Hotel', 'St. Andrews Senior Trip 2024 - Educational journey across Western Europe.', '2024-06-14', '2024-06-18', 122400.00, 21000.00, 101400.00, 'activo');

-- Pasajeros familia Miller
INSERT INTO `pasajeros` (`contrato_id`, `nombre`, `apellido`, `tipo`, `pasaporte`, `preferencia_comida`) VALUES
(1, 'David', 'Miller', 'lider', 'PP-882193441', 'Standard Meal'),
(1, 'Elena', 'Miller', 'adulto', 'PP-882193442', 'Vegetarian'),
(1, 'Leo', 'Miller', 'nino', 'PP-882193443', 'Kids Menu');
UPDATE `pasajeros` SET `edad` = 8 WHERE `nombre` = 'Leo' AND `apellido` = 'Miller';

-- Pasajeros Jane Vargas
INSERT INTO `pasajeros` (`contrato_id`, `nombre`, `apellido`, `tipo`, `pasaporte`, `preferencia_comida`) VALUES
(4, 'Jane', 'Vargas Romero', 'lider', 'PE-PP-12345678', 'Standard'),
(4, 'Roberto', 'Vargas Romero', 'adulto', 'PE-PP-12345679', 'Standard'),
(4, 'Elena', 'Vargas Romero', 'nino', 'PE-PP-12345680', 'Kids Menu');
UPDATE `pasajeros` SET `edad` = 8 WHERE `nombre` = 'Elena' AND `apellido` = 'Vargas Romero';

-- Pasajeros grupo escolar
INSERT INTO `pasajeros` (`contrato_id`, `nombre`, `apellido`, `tipo`, `pasaporte`, `documento_verificado`, `preferencia_comida`) VALUES
(5, 'Alexander', 'Adams', 'adulto', 'UK-PP-001', 1, 'Standard'),
(5, 'Beatrice', 'Bennett', 'adulto', 'UK-PP-002', 0, 'Vegetarian'),
(5, 'Christopher', 'Davis', 'lider', 'UK-PP-003', 1, 'Standard'),
(5, 'Dorothy', 'Evans', 'adulto', 'UK-PP-004', 0, 'Standard');

-- Vuelos familia Miller
INSERT INTO `vuelos` (`contrato_id`, `aerolinea`, `numero_vuelo`, `origen`, `origen_ciudad`, `destino`, `destino_ciudad`, `fecha_salida`, `fecha_llegada`, `tipo`, `clase`, `avion`, `estado`) VALUES
(1, 'Lufthansa', 'LH-401', 'JFK', 'New York', 'ZRH', 'Zurich', '2024-06-14 10:45:00', '2024-06-15 01:05:00', 'ida', 'economica', 'Boeing 747-8', 'confirmado');

-- Vuelos Jane Vargas (ida: PCL→LIM→PUJ, vuelta: PUJ→LIM→PCL)
INSERT INTO `vuelos` (`contrato_id`, `aerolinea`, `numero_vuelo`, `origen`, `origen_ciudad`, `destino`, `destino_ciudad`, `fecha_salida`, `fecha_llegada`, `tipo`, `clase`, `avion`, `estado`) VALUES
(4, 'LATAM', 'LATAM 2581', 'PCL', 'Pucallpa', 'LIM', 'Lima', '2026-10-26 08:30:00', '2026-10-26 09:30:00', 'ida', 'economica', 'Airbus A320', 'confirmado'),
(4, 'Arajet', 'DM 677', 'LIM', 'Lima', 'PUJ', 'Punta Cana', '2026-10-27 10:00:00', '2026-10-27 18:00:00', 'conexion', 'economica', 'Boeing 737 MAX', 'confirmado'),
(4, 'Arajet', 'DM 6774', 'PUJ', 'Punta Cana', 'LIM', 'Lima', '2026-11-01 14:20:00', '2026-11-01 22:20:00', 'vuelta', 'economica', 'Boeing 737 MAX', 'confirmado'),
(4, 'LATAM', 'LATAM 2352', 'LIM', 'Lima', 'PCL', 'Pucallpa', '2026-11-02 06:00:00', '2026-11-02 07:00:00', 'conexion', 'economica', 'Airbus A320', 'confirmado');

-- Vuelos grupo escolar
INSERT INTO `vuelos` (`contrato_id`, `aerolinea`, `numero_vuelo`, `origen`, `origen_ciudad`, `destino`, `destino_ciudad`, `fecha_salida`, `fecha_llegada`, `tipo`, `clase`, `avion`, `estado`) VALUES
(5, 'British Airways', 'BA-175', 'JFK', 'New York', 'LHR', 'London', '2024-06-14 19:00:00', '2024-06-15 07:45:00', 'ida', 'economica', 'Boeing 777', 'confirmado');

-- Servicios familia Miller
INSERT INTO `servicios` (`contrato_id`, `tipo`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `precio`, `estado`) VALUES
(1, 'hotel', 'Grand Hotel Zermatt', '4 Nights - Suite Superior', '2024-06-14', '2024-06-18', 4800.00, 'pagado'),
(1, 'tour', 'Matterhorn Sunrise Trek', 'June 16 - Private Guide', '2024-06-16', '2024-06-16', 350.00, 'pagado'),
(1, 'transfer', 'Private Airport Transfer', 'ZRH to Zermatt - Mercedes V-Class', '2024-06-14', '2024-06-14', 280.00, 'pendiente'),
(1, 'seguro', 'Global Travel Platinum', 'Policy #GT-88219', '2024-06-14', '2024-06-22', 450.00, 'pagado');

-- Servicios Jane Vargas
INSERT INTO `servicios` (`contrato_id`, `tipo`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `precio`, `estado`) VALUES
(4, 'hotel', 'Catalonia Bávaro Beach 5★', 'Resort & Spa - All Inclusive', '2026-10-27', '2026-11-01', 800.00, 'pendiente'),
(4, 'actividad', 'Zip Line Mega Splash', 'Splash of Emotions Included', '2026-10-28', '2026-10-28', 85.00, 'pendiente'),
(4, 'tour', 'Saona Island Tour', 'Optional: Colonial Santo Domingo', '2026-10-29', '2026-10-29', 95.00, 'pendiente');

-- Servicios grupo escolar
INSERT INTO `servicios` (`contrato_id`, `tipo`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `precio`, `estado`) VALUES
(5, 'hotel', 'The Kensington Curator Hotel', 'Centrally located 4-star accommodation with dedicated student wings and 24/7 security concierge.', '2024-06-15', '2024-06-18', 42000.00, 'pagado'),
(5, 'tour', 'British Museum Guided Tour', 'Education Session: The Rosetta Stone', '2024-06-16', '2024-06-16', 2100.00, 'pagado'),
(5, 'seguro', 'Global Guard Plus', 'Policy #AV-778210 - All passengers covered', '2024-06-14', '2024-06-18', 8400.00, 'pagado');

-- Pagos familia Miller
INSERT INTO `pagos` (`contrato_id`, `concepto`, `monto`, `fecha_vencimiento`, `fecha_pago`, `estado`, `metodo_pago`) VALUES
(1, 'Depósito inicial', 4455.00, '2024-03-01', '2024-03-01', 'aprobado', 'Transferencia bancaria'),
(1, 'Saldo final', 10395.00, '2024-06-10', NULL, 'pendiente', NULL);

-- Pagos Elena Rodriguez (Bali)
INSERT INTO `pagos` (`contrato_id`, `concepto`, `monto`, `fecha_vencimiento`, `fecha_pago`, `estado`, `metodo_pago`) VALUES
(2, 'Pago completo', 3240.00, '2024-06-01', '2024-06-01', 'aprobado', 'Tarjeta de crédito');

-- Pagos James Miller (Swiss Alps Express)
INSERT INTO `pagos` (`contrato_id`, `concepto`, `monto`, `fecha_vencimiento`, `fecha_pago`, `estado`, `metodo_pago`) VALUES
(3, 'Depósito', 500.00, '2024-05-01', NULL, 'pendiente', NULL),
(3, 'Saldo', 1350.00, '2024-07-15', NULL, 'pendiente', NULL);

-- Pagos Jane Vargas (cuotas)
INSERT INTO `pagos` (`contrato_id`, `concepto`, `monto`, `fecha_vencimiento`, `fecha_pago`, `estado`, `metodo_pago`) VALUES
(4, 'Prepago Confirmado', 350.00, '2026-01-15', '2026-01-15', 'aprobado', 'Transferencia'),
(4, 'Cuota 1', 199.83, '2026-04-30', NULL, 'pendiente', NULL),
(4, 'Cuota 2', 199.83, '2026-05-31', NULL, 'pendiente', NULL),
(4, 'Cuota 3', 199.83, '2026-06-30', NULL, 'pendiente', NULL),
(4, 'Cuota 4', 199.83, '2026-07-31', NULL, 'pendiente', NULL),
(4, 'Cuota 5', 199.85, '2026-08-31', NULL, 'pendiente', NULL);

-- Pagos grupo escolar
INSERT INTO `pagos` (`contrato_id`, `concepto`, `monto`, `fecha_vencimiento`, `fecha_pago`, `estado`, `metodo_pago`) VALUES
(5, 'Deposit', 500.00, '2024-01-15', '2024-01-15', 'aprobado', 'Bank Transfer'),
(5, 'Installment 01', 1200.00, '2024-03-15', '2024-03-15', 'aprobado', 'Bank Transfer'),
(5, 'Final Balance', 1200.00, '2024-05-15', NULL, 'pendiente', NULL);

-- Promociones activas
INSERT INTO `promociones` (`titulo`, `descripcion`, `destino`, `descuento`, `fecha_inicio`, `fecha_fin`, `activa`) VALUES
('Summer Escape: Bali Bliss', 'Discover the magic of Bali with exclusive resort packages, private temple tours, and sunset beach dinners. Limited availability for summer 2024.', 'Bali, Indonesia', '20%', '2024-05-01', '2024-07-15', 1),
('Alpine Adventures Early Bird', 'Book your Swiss Alps retreat early and enjoy premium mountain lodges, guided glacier hikes, and fondue experiences at reduced rates.', 'Swiss Alps', '15%', '2024-04-01', '2024-08-30', 1),
('European Grand Tour – 15% Off', 'Experience the best of Europe: Paris, Rome, Barcelona. Boutique hotels, private guides, and curated culinary adventures. Book by end of month.', 'Europe Multi-City', '15%', '2024-01-01', '2024-03-31', 0),
('Summer Serenity Package', 'Book by June 30th and receive a complimentary spa retreat and private beach dinner at any of our Maldives resorts.', 'Maldives', 'Spa + Dinner', '2024-04-01', '2024-06-30', 1);

-- Notificaciones
INSERT INTO `notificaciones` (`usuario_id`, `titulo`, `mensaje`, `tipo`, `leida`) VALUES
(2, 'Payment Reminder', 'Your final payment of $10,395.00 is due in 4 days (June 10).', 'advertencia', 0),
(2, 'Flight Confirmed', 'Your Lufthansa flight LH-401 JFK→ZRH has been confirmed for June 14.', 'exito', 1),
(3, 'Booking Confirmed', 'Your Bali Deluxe Package has been confirmed. Payment received.', 'exito', 1),
(5, 'Document Required', 'Please upload passport copies for 2 remaining students.', 'info', 0),
(8, 'Prepago Confirmado', 'Su prepago de $350.00 ha sido confirmado exitosamente.', 'exito', 1),
(8, 'Cuota 1 Próxima', 'Su cuota 1 de $199.83 vence el 30/04/2026.', 'info', 0);
