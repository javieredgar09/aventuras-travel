-- DATOS DE PRUEBA SOLAMENTE (tablas ya existen)
INSERT INTO grupos (nombre, tipo, operador, destino, fecha_viaje, fecha_retorno, valor_total, deposito, tipo_pago, total_cuotas, meses_pago, estado) VALUES
('Familia Rodríguez - Cancún', 'familiar', 'Avianca Tours', 'Cancún, México', '2026-07-15', '2026-07-22', 12500.00, 3000.00, 'cuotas', 6, 'Enero,Febrero,Marzo,Abril,Mayo,Junio', 'activo'),
('Familia García - Punta Cana', 'familiar', 'Copa Travel', 'Punta Cana', '2026-08-10', '2026-08-17', 15000.00, 15000.00, 'contado', 0, NULL, 'activo'),
('Familia López - Cusco', 'familiar', NULL, 'Cusco, Perú', '2026-06-20', '2026-06-25', 4500.00, 1500.00, 'cuotas', 3, 'Abril,Mayo,Junio', 'activo');
INSERT INTO grupos (nombre, tipo, operador, destino, fecha_viaje, fecha_retorno, valor_total, deposito, tipo_pago, estado, institucion) VALUES
('Promo 2026 - Colegio CCPA', 'colegio', 'Aventuras Travel', 'Punta Cana, Rep. Dominicana', '2026-10-26', '2026-11-02', 85000.00, 25000.00, 'cuotas', 'activo', 'Colegio CCPA Pucallpa'),
('Promo 2026 - San Agustín', 'colegio', 'Aventuras Travel', 'Cancún, México', '2026-11-15', '2026-11-22', 72000.00, 20000.00, 'cuotas', 'activo', 'Colegio San Agustín');
