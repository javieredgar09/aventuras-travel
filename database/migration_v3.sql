-- MIGRACIÓN v3: Pagos Flexibles, Cuotas y Vouchers
-- ============================================

-- 1. Tabla de Vouchers
DROP TABLE IF EXISTS vouchers;
CREATE TABLE vouchers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entidad_id INT UNSIGNED NOT NULL,
    tipo_entidad ENUM('grupo', 'contrato') NOT NULL,
    tipo_voucher ENUM('hotel', 'excursion', 'seguro', 'vuelos', 'general') NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    archivo_url VARCHAR(255) NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla de Plan de Cuotas (La deuda)
DROP TABLE IF EXISTS plan_cuotas;
CREATE TABLE plan_cuotas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entidad_id INT UNSIGNED NOT NULL,
    tipo_entidad ENUM('grupo', 'contrato') NOT NULL,
    numero_cuota INT DEFAULT 0,
    concepto VARCHAR(100) NOT NULL,
    monto_esperado DECIMAL(10,2) NOT NULL,
    fecha_vencimiento DATE DEFAULT NULL,
    monto_pagado DECIMAL(10,2) DEFAULT 0.00,
    estado ENUM('pendiente', 'parcial', 'pagada') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Volcar los datos actuales de cuotas desde "pagos" hacia "plan_cuotas"
INSERT INTO plan_cuotas (entidad_id, tipo_entidad, numero_cuota, concepto, monto_esperado, fecha_vencimiento, monto_pagado, estado)
SELECT 
    IFNULL(contrato_id, grupo_id), 
    entidad_tipo, 
    cuota_numero, 
    concepto, 
    monto, 
    fecha_vencimiento, 
    IF(estado = 'aprobado', monto, 0),
    IF(estado = 'aprobado', 'pagada', 'pendiente')
FROM pagos;

-- 4. Adaptar la tabla `pagos` para que sea solo el recibo de transacción
-- Primero eliminamos los registros que eran "pagos pendientes" (cuotas sin pagar)
DELETE FROM pagos WHERE estado = 'pendiente';

-- Añadimos las nuevas columnas a pagos
-- (Use IF NOT EXISTS logic implicitamente, o si falla no importa si corremos en limpio)
ALTER TABLE pagos ADD COLUMN comprobante_url VARCHAR(255) DEFAULT NULL;
ALTER TABLE pagos ADD COLUMN fecha_aprobacion DATE DEFAULT NULL;
ALTER TABLE pagos ADD COLUMN notas_admin TEXT DEFAULT NULL;
ALTER TABLE pagos ADD COLUMN cuota_aplica INT DEFAULT NULL;
ALTER TABLE pagos ADD COLUMN excedente DECIMAL(10,2) DEFAULT 0.00;

-- Modificamos estado (Si da error por constraint, lo reescribimos)
ALTER TABLE pagos MODIFY COLUMN estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente';

SELECT 'OK - Migración v3 completada' AS resultado;
