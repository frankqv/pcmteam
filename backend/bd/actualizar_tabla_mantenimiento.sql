-- Actualización de la tabla bodega_mantenimiento
-- Agregar columna faltante para observaciones globales

USE `u171145084_pcmteam`;

-- Agregar columna observaciones_globales si no existe
ALTER TABLE `bodega_mantenimiento` 
ADD COLUMN IF NOT EXISTS `observaciones_globales` text DEFAULT NULL AFTER `proceso_electronico`;

-- Verificar que todas las columnas estén presentes
DESCRIBE `bodega_mantenimiento`;

-- Insertar datos de prueba (opcional)
-- INSERT INTO `bodega_mantenimiento` (
--     `inventario_id`, 
--     `tecnico_diagnostico`,
--     `usuario_registro`,
--     `limpieza_electronico`,
--     `observaciones_limpieza_electronico`,
--     `mantenimiento_crema_disciplinaria`,
--     `observaciones_mantenimiento_crema`,
--     `mantenimiento_partes`,
--     `cambio_piezas`,
--     `piezas_solicitadas_cambiadas`,
--     `proceso_reconstruccion`,
--     `parte_reconstruida`,
--     `limpieza_general`,
--     `remite_otra_area`,
--     `area_remite`,
--     `proceso_electronico`,
--     `observaciones_globales`,
--     `estado`
-- ) VALUES (
--     1, -- inventario_id
--     33, -- tecnico_diagnostico
--     1, -- usuario_registro
--     'pendiente', -- limpieza_electronico
--     NULL, -- observaciones_limpieza_electronico
--     'pendiente', -- mantenimiento_crema_disciplinaria
--     NULL, -- observaciones_mantenimiento_crema
--     'pendiente', -- mantenimiento_partes
--     'no', -- cambio_piezas
--     NULL, -- piezas_solicitadas_cambiadas
--     'no', -- proceso_reconstruccion
--     NULL, -- parte_reconstruida
--     'pendiente', -- limpieza_general
--     'no', -- remite_otra_area
--     NULL, -- area_remite
--     NULL, -- proceso_electronico
--     NULL, -- observaciones_globales
--     'pendiente' -- estado
-- );
