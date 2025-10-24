-- ==================================================================
-- VERIFICAR Y CORREGIR SALDOS - ALISTAMIENTO DE VENTAS
-- Ejecutar este script para verificar y corregir el problema de saldos
-- ==================================================================

-- ==================================================================
-- PASO 1: VERIFICAR ESTADO ACTUAL
-- ==================================================================
SELECT
    '=== ESTADO ACTUAL DE VENTAS ===' as titulo;

SELECT
    id,
    idventa,
    ticket,
    subtotal as subtotal_actual,
    descuento,
    total_venta as total_actual,
    valor_abono as abono,
    saldo as saldo_actual,
    estado
FROM alistamiento_venta
ORDER BY id;

-- ==================================================================
-- PASO 2: MOSTRAR CÁLCULOS CORRECTOS
-- ==================================================================
SELECT
    '=== CÁLCULOS CORRECTOS (LO QUE DEBERÍA SER) ===' as titulo;

SELECT
    av.id,
    av.idventa,
    av.ticket,
    -- Subtotal calculado (suma de items)
    IFNULL((SELECT SUM(avi.subtotal)
            FROM alistamiento_venta_items avi
            WHERE avi.alistamiento_id = av.id), 0) as subtotal_correcto,

    av.descuento,

    -- Total calculado (subtotal - descuento)
    (IFNULL((SELECT SUM(avi.subtotal)
             FROM alistamiento_venta_items avi
             WHERE avi.alistamiento_id = av.id), 0) - IFNULL(av.descuento, 0)) as total_correcto,

    av.valor_abono as abono,

    -- Saldo calculado (total - abono)
    ((IFNULL((SELECT SUM(avi.subtotal)
              FROM alistamiento_venta_items avi
              WHERE avi.alistamiento_id = av.id), 0) - IFNULL(av.descuento, 0))
     - IFNULL(av.valor_abono, 0)) as saldo_correcto,

    -- Comparación
    CASE
        WHEN av.saldo = ((IFNULL((SELECT SUM(avi.subtotal)
                                  FROM alistamiento_venta_items avi
                                  WHERE avi.alistamiento_id = av.id), 0) - IFNULL(av.descuento, 0))
                         - IFNULL(av.valor_abono, 0))
        THEN '✅ CORRECTO'
        ELSE '❌ INCORRECTO'
    END as estado_calculo
FROM alistamiento_venta av
ORDER BY av.id;

-- ==================================================================
-- PASO 3: VERIFICAR SI LOS TRIGGERS EXISTEN
-- ==================================================================
SELECT
    '=== TRIGGERS EXISTENTES ===' as titulo;

SELECT
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = DATABASE()
  AND TRIGGER_NAME LIKE '%actualizar_total%'
ORDER BY TRIGGER_NAME;

-- ==================================================================
-- PASO 4: VER ITEMS DE LAS VENTAS
-- ==================================================================
SELECT
    '=== ITEMS DE VENTAS ===' as titulo;

SELECT
    avi.alistamiento_id,
    av.idventa,
    avi.item_numero,
    avi.producto,
    avi.cantidad,
    avi.precio_unitario,
    avi.subtotal,
    av.subtotal as subtotal_venta
FROM alistamiento_venta_items avi
LEFT JOIN alistamiento_venta av ON avi.alistamiento_id = av.id
ORDER BY avi.alistamiento_id, avi.item_numero;

-- ==================================================================
-- PASO 5: ELIMINAR TRIGGERS EXISTENTES (PREPARACIÓN)
-- ==================================================================
DROP TRIGGER IF EXISTS `trg_actualizar_total_insert`;
DROP TRIGGER IF EXISTS `trg_actualizar_total_update`;
DROP TRIGGER IF EXISTS `trg_actualizar_total_delete`;

-- ==================================================================
-- PASO 6: CREAR TRIGGERS CORRECTOS
-- ==================================================================
DELIMITER $$

CREATE TRIGGER `trg_actualizar_total_insert`
AFTER INSERT ON `alistamiento_venta_items`
FOR EACH ROW
BEGIN
    DECLARE v_subtotal DECIMAL(12,2);
    DECLARE v_descuento DECIMAL(12,2);
    DECLARE v_total DECIMAL(12,2);
    DECLARE v_abono DECIMAL(12,2);
    DECLARE v_saldo DECIMAL(12,2);

    -- Calcular subtotal sumando todos los items
    SELECT IFNULL(SUM(subtotal), 0) INTO v_subtotal
    FROM alistamiento_venta_items
    WHERE alistamiento_id = NEW.alistamiento_id;

    -- Obtener descuento y abono actuales
    SELECT IFNULL(descuento, 0), IFNULL(valor_abono, 0)
    INTO v_descuento, v_abono
    FROM alistamiento_venta
    WHERE id = NEW.alistamiento_id;

    -- Calcular total y saldo
    SET v_total = v_subtotal - v_descuento;
    SET v_saldo = v_total - v_abono;

    -- Actualizar tabla principal
    UPDATE alistamiento_venta
    SET subtotal = v_subtotal,
        total_venta = v_total,
        saldo = v_saldo
    WHERE id = NEW.alistamiento_id;
END$$


CREATE TRIGGER `trg_actualizar_total_update`
AFTER UPDATE ON `alistamiento_venta_items`
FOR EACH ROW
BEGIN
    DECLARE v_subtotal DECIMAL(12,2);
    DECLARE v_descuento DECIMAL(12,2);
    DECLARE v_total DECIMAL(12,2);
    DECLARE v_abono DECIMAL(12,2);
    DECLARE v_saldo DECIMAL(12,2);

    -- Calcular subtotal sumando todos los items
    SELECT IFNULL(SUM(subtotal), 0) INTO v_subtotal
    FROM alistamiento_venta_items
    WHERE alistamiento_id = NEW.alistamiento_id;

    -- Obtener descuento y abono actuales
    SELECT IFNULL(descuento, 0), IFNULL(valor_abono, 0)
    INTO v_descuento, v_abono
    FROM alistamiento_venta
    WHERE id = NEW.alistamiento_id;

    -- Calcular total y saldo
    SET v_total = v_subtotal - v_descuento;
    SET v_saldo = v_total - v_abono;

    -- Actualizar tabla principal
    UPDATE alistamiento_venta
    SET subtotal = v_subtotal,
        total_venta = v_total,
        saldo = v_saldo
    WHERE id = NEW.alistamiento_id;
END$$


CREATE TRIGGER `trg_actualizar_total_delete`
AFTER DELETE ON `alistamiento_venta_items`
FOR EACH ROW
BEGIN
    DECLARE v_subtotal DECIMAL(12,2);
    DECLARE v_descuento DECIMAL(12,2);
    DECLARE v_total DECIMAL(12,2);
    DECLARE v_abono DECIMAL(12,2);
    DECLARE v_saldo DECIMAL(12,2);

    -- Calcular subtotal sumando todos los items restantes
    SELECT IFNULL(SUM(subtotal), 0) INTO v_subtotal
    FROM alistamiento_venta_items
    WHERE alistamiento_id = OLD.alistamiento_id;

    -- Obtener descuento y abono actuales
    SELECT IFNULL(descuento, 0), IFNULL(valor_abono, 0)
    INTO v_descuento, v_abono
    FROM alistamiento_venta
    WHERE id = OLD.alistamiento_id;

    -- Calcular total y saldo
    SET v_total = v_subtotal - v_descuento;
    SET v_saldo = v_total - v_abono;

    -- Actualizar tabla principal
    UPDATE alistamiento_venta
    SET subtotal = v_subtotal,
        total_venta = v_total,
        saldo = v_saldo
    WHERE id = OLD.alistamiento_id;
END$$

DELIMITER ;

-- ==================================================================
-- PASO 7: RECALCULAR TODAS LAS VENTAS EXISTENTES
-- ==================================================================
SELECT
    '=== RECALCULANDO VENTAS EXISTENTES ===' as titulo;

UPDATE alistamiento_venta av
SET
    av.subtotal = (
        SELECT IFNULL(SUM(avi.subtotal), 0)
        FROM alistamiento_venta_items avi
        WHERE avi.alistamiento_id = av.id
    ),
    av.total_venta = (
        SELECT IFNULL(SUM(avi.subtotal), 0) - IFNULL(av.descuento, 0)
        FROM alistamiento_venta_items avi
        WHERE avi.alistamiento_id = av.id
    ),
    av.saldo = (
        SELECT IFNULL(SUM(avi.subtotal), 0) - IFNULL(av.descuento, 0) - IFNULL(av.valor_abono, 0)
        FROM alistamiento_venta_items avi
        WHERE avi.alistamiento_id = av.id
    );

-- ==================================================================
-- PASO 8: VERIFICAR RESULTADOS FINALES
-- ==================================================================
SELECT
    '=== ESTADO DESPUÉS DE LA CORRECCIÓN ===' as titulo;

SELECT
    av.id,
    av.idventa,
    av.ticket,
    av.subtotal,
    av.descuento,
    av.total_venta,
    av.valor_abono,
    av.saldo,
    -- Verificación
    (SELECT SUM(avi.subtotal) FROM alistamiento_venta_items avi WHERE avi.alistamiento_id = av.id) as subtotal_items,
    CASE
        WHEN av.subtotal = (SELECT IFNULL(SUM(avi.subtotal), 0) FROM alistamiento_venta_items avi WHERE avi.alistamiento_id = av.id)
        THEN '✅ OK'
        ELSE '❌ ERROR'
    END as verificacion
FROM alistamiento_venta av
ORDER BY av.id;

-- ==================================================================
-- PASO 9: MOSTRAR RESUMEN
-- ==================================================================
SELECT
    '=== RESUMEN ===' as titulo;

SELECT
    COUNT(*) as total_ventas,
    SUM(subtotal) as suma_subtotales,
    SUM(total_venta) as suma_totales,
    SUM(valor_abono) as suma_abonos,
    SUM(saldo) as suma_saldos
FROM alistamiento_venta;

-- ==================================================================
-- FIN DEL SCRIPT
-- ==================================================================
SELECT '✅ SCRIPT COMPLETADO - Revisa los resultados arriba' as resultado;
