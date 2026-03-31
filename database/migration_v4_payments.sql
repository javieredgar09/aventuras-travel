-- MIGRACIÓN v4: Separar plan de cuotas de pagos transaccionales
-- ============================================================

-- 1. Agregar columna excedente a pagos si no existe
ALTER TABLE pagos ADD COLUMN excedente DECIMAL(10,2) DEFAULT 0.00;

-- 2. Limpiar plan_cuotas si tiene datos previos
DELETE FROM plan_cuotas;

-- 3. Crear plan de cuotas a partir de TODOS los pagos (pendientes y aprobados)
--    Los aprobados se marcan como 'pagada', los pendientes como 'pendiente'
INSERT INTO plan_cuotas (entidad_id, tipo_entidad, numero_cuota, concepto, monto_esperado, fecha_vencimiento, monto_pagado, estado)
SELECT 
    contrato_id,
    'contrato',
    @rn := @rn + 1,
    concepto,
    monto,
    fecha_vencimiento,
    CASE WHEN estado = 'aprobado' THEN monto ELSE 0 END,
    CASE WHEN estado = 'aprobado' THEN 'pagada' ELSE 'pendiente' END
FROM pagos, (SELECT @rn := 0) r
WHERE contrato_id = 1
ORDER BY fecha_vencimiento ASC, id ASC;

SET @rn := 0;
INSERT INTO plan_cuotas (entidad_id, tipo_entidad, numero_cuota, concepto, monto_esperado, fecha_vencimiento, monto_pagado, estado)
SELECT 
    contrato_id,
    'contrato',
    @rn := @rn + 1,
    concepto,
    monto,
    fecha_vencimiento,
    CASE WHEN estado = 'aprobado' THEN monto ELSE 0 END,
    CASE WHEN estado = 'aprobado' THEN 'pagada' ELSE 'pendiente' END
FROM pagos
WHERE contrato_id = 2
ORDER BY fecha_vencimiento ASC, id ASC;

SET @rn := 0;
INSERT INTO plan_cuotas (entidad_id, tipo_entidad, numero_cuota, concepto, monto_esperado, fecha_vencimiento, monto_pagado, estado)
SELECT 
    contrato_id,
    'contrato',
    @rn := @rn + 1,
    concepto,
    monto,
    fecha_vencimiento,
    CASE WHEN estado = 'aprobado' THEN monto ELSE 0 END,
    CASE WHEN estado = 'aprobado' THEN 'pagada' ELSE 'pendiente' END
FROM pagos
WHERE contrato_id = 3
ORDER BY fecha_vencimiento ASC, id ASC;

SET @rn := 0;
INSERT INTO plan_cuotas (entidad_id, tipo_entidad, numero_cuota, concepto, monto_esperado, fecha_vencimiento, monto_pagado, estado)
SELECT 
    contrato_id,
    'contrato',
    @rn := @rn + 1,
    concepto,
    monto,
    fecha_vencimiento,
    CASE WHEN estado = 'aprobado' THEN monto ELSE 0 END,
    CASE WHEN estado = 'aprobado' THEN 'pagada' ELSE 'pendiente' END
FROM pagos
WHERE contrato_id = 4
ORDER BY fecha_vencimiento ASC, id ASC;

SET @rn := 0;
INSERT INTO plan_cuotas (entidad_id, tipo_entidad, numero_cuota, concepto, monto_esperado, fecha_vencimiento, monto_pagado, estado)
SELECT 
    contrato_id,
    'contrato',
    @rn := @rn + 1,
    concepto,
    monto,
    fecha_vencimiento,
    CASE WHEN estado = 'aprobado' THEN monto ELSE 0 END,
    CASE WHEN estado = 'aprobado' THEN 'pagada' ELSE 'pendiente' END
FROM pagos
WHERE contrato_id = 5
ORDER BY fecha_vencimiento ASC, id ASC;

SET @rn := 0;
INSERT INTO plan_cuotas (entidad_id, tipo_entidad, numero_cuota, concepto, monto_esperado, fecha_vencimiento, monto_pagado, estado)
SELECT 
    contrato_id,
    'contrato',
    @rn := @rn + 1,
    concepto,
    monto,
    fecha_vencimiento,
    CASE WHEN estado = 'aprobado' THEN monto ELSE 0 END,
    CASE WHEN estado = 'aprobado' THEN 'pagada' ELSE 'pendiente' END
FROM pagos
WHERE contrato_id = 6
ORDER BY fecha_vencimiento ASC, id ASC;

-- 4. Eliminar los pagos "pendiente" (son plan de cuotas, no transacciones reales)
DELETE FROM pagos WHERE estado = 'pendiente';

-- 5. Verificación
SELECT 'plan_cuotas' as tabla, COUNT(*) as registros FROM plan_cuotas
UNION ALL
SELECT 'pagos', COUNT(*) FROM pagos;
