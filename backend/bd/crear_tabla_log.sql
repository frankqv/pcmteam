-- Crear tabla de log para cambios en equipos (opcional)
-- Esta tabla permite mantener un historial de todos los cambios realizados

USE `u171145084_pcmteam`;

-- Crear tabla de log si no existe
CREATE TABLE IF NOT EXISTS `bodega_log_cambios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventario_id` int(11) NOT NULL COMMENT 'ID del equipo en inventario',
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que realizó el cambio',
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha y hora del cambio',
  `campo_modificado` varchar(100) NOT NULL COMMENT 'Nombre del campo modificado',
  `valor_anterior` text DEFAULT NULL COMMENT 'Valor anterior del campo',
  `valor_nuevo` text DEFAULT NULL COMMENT 'Nuevo valor del campo',
  `tipo_cambio` enum('edicion_manual','importacion','sistema') NOT NULL DEFAULT 'edicion_manual' COMMENT 'Tipo de cambio realizado',
  PRIMARY KEY (`id`),
  KEY `idx_inventario_id` (`inventario_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_fecha_cambio` (`fecha_cambio`),
  KEY `idx_campo_modificado` (`campo_modificado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de cambios realizados en equipos del inventario';

-- Crear índices para mejorar rendimiento
CREATE INDEX IF NOT EXISTS `idx_inventario_fecha` ON `bodega_log_cambios` (`inventario_id`, `fecha_cambio`);
CREATE INDEX IF NOT EXISTS `idx_usuario_fecha` ON `bodega_log_cambios` (`usuario_id`, `fecha_cambio`);

-- Verificar que la tabla se creó correctamente
DESCRIBE `bodega_log_cambios`;

-- Insertar datos de ejemplo (opcional)
-- INSERT INTO `bodega_log_cambios` (
--     `inventario_id`, 
--     `usuario_id`, 
--     `campo_modificado`, 
--     `valor_anterior`, 
--     `valor_nuevo`, 
--     `tipo_cambio`
-- ) VALUES (
--     1, -- inventario_id
--     1, -- usuario_id
--     'modelo', -- campo_modificado
--     'Latitude 5520', -- valor_anterior
--     'Latitude 5520 Pro', -- valor_nuevo
--     'edicion_manual' -- tipo_cambio
-- );
