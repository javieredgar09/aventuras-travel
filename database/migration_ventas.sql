-- MIGRACIÓN v2: Solo lo que falta
-- ============================================

-- 1. servicios_grupo tabla nueva
DROP TABLE IF EXISTS servicios_grupo;
CREATE TABLE servicios_grupo (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grupo_id INT UNSIGNED NOT NULL,
    servicio_tipo VARCHAR(50) NOT NULL,
    detalle_json LONGTEXT DEFAULT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. grupo_id en pasajeros (ignorar si ya existe)
ALTER TABLE pasajeros ADD COLUMN grupo_id INT UNSIGNED DEFAULT NULL;

-- 3. campos en pagos
ALTER TABLE pagos ADD COLUMN grupo_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE pagos ADD COLUMN entidad_tipo VARCHAR(20) DEFAULT 'contrato';
ALTER TABLE pagos ADD COLUMN cuota_numero INT DEFAULT 0;
ALTER TABLE pagos ADD COLUMN mes_correspondiente VARCHAR(20) DEFAULT NULL;

-- 4. Grupos familiares de prueba
INSERT INTO grupos (nombre, tipo, operador, destino, fecha_viaje, fecha_retorno, valor_total, deposito, tipo_pago, total_cuotas, meses_pago, estado) VALUES
('Familia Rodríguez - Cancún', 'familiar', 'Avianca Tours', 'Cancún, México', '2026-07-15', '2026-07-22', 12500.00, 3000.00, 'cuotas', 6, 'Enero,Febrero,Marzo,Abril,Mayo,Junio', 'activo'),
('Familia García - Punta Cana', 'familiar', 'Copa Travel', 'Punta Cana', '2026-08-10', '2026-08-17', 15000.00, 15000.00, 'contado', 0, NULL, 'activo'),
('Familia López - Cusco', 'familiar', NULL, 'Cusco, Perú', '2026-06-20', '2026-06-25', 4500.00, 1500.00, 'cuotas', 3, 'Abril,Mayo,Junio', 'activo');

-- 5. Grupos colegio
INSERT INTO grupos (nombre, tipo, operador, destino, fecha_viaje, fecha_retorno, valor_total, deposito, tipo_pago, estado, institucion) VALUES
('Promo 2026 - Colegio CCPA', 'colegio', 'Aventuras Travel', 'Punta Cana, Rep. Dominicana', '2026-10-26', '2026-11-02', 85000.00, 25000.00, 'cuotas', 'activo', 'Colegio CCPA Pucallpa'),
('Promo 2026 - San Agustín', 'colegio', 'Aventuras Travel', 'Cancún, México', '2026-11-15', '2026-11-22', 72000.00, 20000.00, 'cuotas', 'activo', 'Colegio San Agustín');

-- IDs
SET @fam1 = (SELECT id FROM grupos WHERE nombre LIKE '%Rodríguez%' LIMIT 1);
SET @fam2 = (SELECT id FROM grupos WHERE nombre LIKE '%García - Punta%' LIMIT 1);
SET @fam3 = (SELECT id FROM grupos WHERE nombre LIKE '%López - Cusco%' LIMIT 1);
SET @col1 = (SELECT id FROM grupos WHERE nombre LIKE '%Promo 2026 - Colegio CCPA%' AND tipo='colegio' LIMIT 1);
SET @col2 = (SELECT id FROM grupos WHERE nombre LIKE '%Agustín%' LIMIT 1);

-- 6. Servicios
INSERT INTO servicios_grupo (grupo_id, servicio_tipo, detalle_json) VALUES
(@fam1, 'hotel', '{"nombre":"Grand Oasis Cancún","tipo_habitacion":"Suite Doble","noches":7}'),
(@fam1, 'vuelos', '{"aerolinea":"Avianca","vuelo_ida":"AV-452","origen":"Lima","destino":"Cancún","fecha_ida":"2026-07-15","fecha_vuelta":"2026-07-22"}'),
(@fam1, 'traslados', '{"tipo":"ambos","detalle":"Aeropuerto - Hotel - Aeropuerto"}'),
(@fam1, 'seguro', '{"nombre":"Assist Card","poliza":"AC-2026-8891"}'),
(@fam2, 'hotel', '{"nombre":"Hard Rock Punta Cana","tipo_habitacion":"All Inclusive Suite","noches":7}'),
(@fam2, 'vuelos', '{"aerolinea":"Copa Airlines","vuelo_ida":"CM-802","origen":"Lima","destino":"Punta Cana","fecha_ida":"2026-08-10","fecha_vuelta":"2026-08-17"}'),
(@fam2, 'excursiones', '{"items":[{"nombre":"Isla Saona","fecha":"2026-08-12","costo":85},{"nombre":"Santo Domingo Tour","fecha":"2026-08-14","costo":60}]}'),
(@fam3, 'hotel', '{"nombre":"Casa Andina Premium","tipo_habitacion":"Matrimonial","noches":5}'),
(@fam3, 'excursiones', '{"items":[{"nombre":"Valle Sagrado","fecha":"2026-06-21","costo":120},{"nombre":"Machu Picchu","fecha":"2026-06-22","costo":250},{"nombre":"Montaña 7 Colores","fecha":"2026-06-23","costo":90}]}'),
(@col1, 'hotel', '{"nombre":"Barceló Bávaro Palace","tipo_habitacion":"Standard Triple","noches":7}'),
(@col1, 'vuelos', '{"aerolinea":"LATAM","vuelo_ida":"LA-2456","origen":"Lima","destino":"Punta Cana","fecha_ida":"2026-10-26","fecha_vuelta":"2026-11-02"}'),
(@col1, 'traslados', '{"tipo":"ambos","detalle":"Transporte grupal Aeropuerto - Hotel"}'),
(@col1, 'excursiones', '{"items":[{"nombre":"Isla Saona","fecha":"2026-10-28","costo":75},{"nombre":"Parque Ecológico","fecha":"2026-10-30","costo":45}]}'),
(@col1, 'seguro', '{"nombre":"Travel Guard","poliza":"TG-GROUP-2026"}'),
(@col2, 'hotel', '{"nombre":"Grand Oasis Cancún","tipo_habitacion":"Standard Doble","noches":7}'),
(@col2, 'vuelos', '{"aerolinea":"Avianca","vuelo_ida":"AV-890","origen":"Lima","destino":"Cancún","fecha_ida":"2026-11-15","fecha_vuelta":"2026-11-22"}'),
(@col2, 'seguro', '{"nombre":"Assist Card Grupal","poliza":"AC-GRP-445"}');

-- 7. Pasajeros familiares
INSERT INTO pasajeros (nombre, tipo, documento, grupo_id) VALUES
('Carlos Rodríguez Mendoza', 'adulto', '45678912', @fam1),
('María Elena Torres', 'adulto', '45678913', @fam1),
('Sofía Rodríguez Torres', 'menor', '78901234', @fam1),
('Diego Rodríguez Torres', 'menor', '78901235', @fam1),
('Roberto García Sánchez', 'adulto', '32145678', @fam2),
('Ana García de Pérez', 'adulto', '32145679', @fam2),
('Luisa López Ramírez', 'adulto', '21436587', @fam3),
('Pedro López Vargas', 'adulto', '21436588', @fam3),
('Valentina López', 'menor', '87654321', @fam3);

-- 8. Contratos colegio
INSERT INTO contratos (codigo, grupo_id, tipo, destino, fecha_salida, fecha_retorno, valor_total, deposito, saldo, estado) VALUES
('CCPA-2026-010', @col1, 'colegio', 'Punta Cana', '2026-10-26', '2026-11-02', 8500.00, 2500.00, 6000.00, 'activo'),
('CCPA-2026-011', @col1, 'colegio', 'Punta Cana', '2026-10-26', '2026-11-02', 8500.00, 3000.00, 5500.00, 'activo'),
('CCPA-2026-012', @col1, 'colegio', 'Punta Cana', '2026-10-26', '2026-11-02', 4250.00, 1000.00, 3250.00, 'activo');

SET @c10 = (SELECT id FROM contratos WHERE codigo='CCPA-2026-010' LIMIT 1);
SET @c11 = (SELECT id FROM contratos WHERE codigo='CCPA-2026-011' LIMIT 1);
SET @c12 = (SELECT id FROM contratos WHERE codigo='CCPA-2026-012' LIMIT 1);

-- 9. Pasajeros colegio
INSERT INTO pasajeros (nombre, tipo, documento, grupo_id, contrato_id) VALUES
('Miguel Ángel Paredes', 'adulto', '47001122', @col1, @c10),
('Rosa Paredes Huamán', 'adulto', '47001123', @col1, @c10),
('Laura Paredes', 'menor', '80112233', @col1, @c10),
('Fernando Ríos Castro', 'adulto', '47002244', @col1, @c11),
('Carmen Ríos de Vargas', 'adulto', '47002245', @col1, @c11),
('Adrián Ríos', 'menor', '80223344', @col1, @c11),
('Lucía Ríos', 'menor', '80223345', @col1, @c11),
('Esperanza Ruiz', 'adulto', '47003366', @col1, @c12),
('Mateo Ruiz', 'menor', '80334455', @col1, @c12);

-- 10. Pagos
INSERT INTO pagos (contrato_id, grupo_id, entidad_tipo, concepto, monto, cuota_numero, mes_correspondiente, fecha_vencimiento, estado) VALUES
(NULL, @fam1, 'grupo', 'Cuota 1 - Enero', 1583.33, 1, 'Enero', '2026-01-31', 'aprobado'),
(NULL, @fam1, 'grupo', 'Cuota 2 - Febrero', 1583.33, 2, 'Febrero', '2026-02-28', 'aprobado'),
(NULL, @fam1, 'grupo', 'Cuota 3 - Marzo', 1583.33, 3, 'Marzo', '2026-03-31', 'pendiente'),
(NULL, @fam2, 'grupo', 'Pago Total', 15000.00, 0, NULL, '2026-03-01', 'aprobado'),
(@c10, @col1, 'contrato', 'Depósito', 2500.00, 0, NULL, '2026-01-15', 'aprobado'),
(@c10, @col1, 'contrato', 'Cuota 1', 2000.00, 1, 'Febrero', '2026-02-28', 'aprobado'),
(@c10, @col1, 'contrato', 'Cuota 2', 2000.00, 2, 'Marzo', '2026-03-31', 'pendiente'),
(@c11, @col1, 'contrato', 'Depósito', 3000.00, 0, NULL, '2026-01-15', 'aprobado'),
(@c11, @col1, 'contrato', 'Cuota 1', 2750.00, 1, 'Marzo', '2026-03-31', 'pendiente'),
(@c12, @col1, 'contrato', 'Depósito', 1000.00, 0, NULL, '2026-02-01', 'aprobado');

SELECT 'OK - Migración completada' AS resultado;
