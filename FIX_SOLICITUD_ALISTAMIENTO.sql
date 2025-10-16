-- =====================================================
-- FIX: Tabla solicitud_alistamiento
-- Ejecutar este SQL para corregir la tabla
-- =====================================================

-- 1. Verificar si el id tiene AUTO_INCREMENT
SELECT
    COLUMN_NAME,
    COLUMN_TYPE,
    EXTRA
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'solicitud_alistamiento'
  AND COLUMN_NAME = 'id';

-- Si el resultado NO muestra "auto_increment" en EXTRA, ejecutar:

-- 2. Agregar AUTO_INCREMENT y PRIMARY KEY (si no existe)
ALTER TABLE `solicitud_alistamiento`
MODIFY `id` int NOT NULL AUTO_INCREMENT,
ADD PRIMARY KEY (`id`);

-- 3. Agregar índices para mejorar rendimiento
ALTER TABLE `solicitud_alistamiento`
ADD INDEX `idx_usuario_id` (`usuario_id`),
ADD INDEX `idx_estado` (`estado`(100)),
ADD INDEX `idx_fecha_solicitud` (`fecha_solicitud`),
ADD INDEX `idx_tecnico` (`tecnico_responsable`);

-- 4. Verificar que la tabla esté correcta
DESCRIBE solicitud_alistamiento;

-- 5. Verificar datos existentes
SELECT COUNT(*) as total_registros FROM solicitud_alistamiento;

-- 6. Ver estructura completa
SHOW CREATE TABLE solicitud_alistamiento;

-- =====================================================
-- RESULTADO ESPERADO:
-- =====================================================
-- El campo `id` debe tener:
-- - Tipo: int
-- - Null: NO
-- - Key: PRI
-- - Extra: auto_increment
-- =====================================================
