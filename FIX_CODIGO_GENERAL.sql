-- =====================================================
-- VERIFICACIÓN: bodega_inventario
-- La columna correcta es 'codigo_g', NO 'codigo_general'
-- =====================================================

-- ✅ ESTADO: El código PHP ya fue corregido para usar 'codigo_g'
-- ✅ NO SE REQUIERE NINGUNA MODIFICACIÓN EN LA BASE DE DATOS

-- Verificar estructura actual
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'bodega_inventario'
  AND COLUMN_NAME LIKE '%codigo%';

-- Resultado esperado:
-- codigo_g | varchar(50) | NO

-- Verificar datos de ejemplo
SELECT
    id,
    codigo_g,
    serial,
    marca,
    modelo,
    grado,
    disposicion,
    estado,
    precio
FROM bodega_inventario
WHERE disposicion = 'Para Venta'
  AND estado = 'activo'
  AND precio > 0
LIMIT 10;

-- =====================================================
-- ÍNDICES RECOMENDADOS PARA RENDIMIENTO
-- =====================================================

-- Agregar índices para optimizar búsquedas de ventas
ALTER TABLE `bodega_inventario`
ADD INDEX IF NOT EXISTS `idx_venta` (`disposicion`, `estado`, `grado`),
ADD INDEX IF NOT EXISTS `idx_producto` (`marca`, `modelo`, `procesador`, `ram`, `disco`);

-- =====================================================
-- ✅ ARCHIVO INFORMATIVO - NO REQUIERE EJECUCIÓN
-- El código PHP ya usa 'codigo_g' correctamente
-- =====================================================
