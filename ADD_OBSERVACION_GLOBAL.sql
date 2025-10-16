-- Modificar columna observacion_global a TEXT (ya existe como VARCHAR(150))
-- Ejecutar para expandir el tamaño

ALTER TABLE `solicitud_alistamiento`
MODIFY COLUMN `observacion_global` TEXT NULL;

-- Agregar índices si no existen
ALTER TABLE `solicitud_alistamiento`
ADD INDEX IF NOT EXISTS `idx_estado` (`estado`(50)),
ADD INDEX IF NOT EXISTS `idx_usuario_id` (`usuario_id`),
ADD INDEX IF NOT EXISTS `idx_tecnico` (`tecnico_responsable`),
ADD INDEX IF NOT EXISTS `idx_fecha_solicitud` (`fecha_solicitud`);

-- Verificar
DESCRIBE solicitud_alistamiento;
