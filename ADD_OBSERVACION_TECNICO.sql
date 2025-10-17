-- =====================================================
-- AGREGAR COLUMNA: observacion_tecnico
-- Tabla: solicitud_alistamiento
-- =====================================================

-- Esta columna permitirá a los técnicos del despacho/bodega
-- agregar observaciones sobre el proceso de alistamiento

-- Verificar si la columna ya existe
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'solicitud_alistamiento'
  AND COLUMN_NAME = 'observacion_tecnico';

-- Si NO existe, ejecutar este ALTER:
ALTER TABLE `solicitud_alistamiento`
ADD COLUMN `observacion_tecnico` TEXT NULL AFTER `observacion_global`,
ADD COMMENT = 'Observaciones del técnico durante el alistamiento';

-- Verificar que se agregó correctamente
DESCRIBE solicitud_alistamiento;

-- =====================================================
-- DIFERENCIA ENTRE CAMPOS DE OBSERVACIÓN:
-- =====================================================
-- observacion_global   → Escrito por el COMERCIAL en preventa.php
-- observacion_tecnico  → Escrito por ADMIN/TÉCNICO/BODEGA en historial_solicitudes_alistamiento.php
-- observacion          → Datos técnicos del producto + JSON (sistema interno)
-- =====================================================

-- ✅ EJECUTAR ESTE ARCHIVO EN phpMyAdmin
