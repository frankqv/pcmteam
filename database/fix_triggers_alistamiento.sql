-- ==================================================================
-- FIX: Triggers para cálculo automático de totales
-- Ejecutar este script para corregir el problema del saldo
-- ==================================================================

-- 1. ELIMINAR TRIGGERS EXISTENTES (si existen)
DROP TRIGGER IF EXISTS `trg_actualizar_total_insert`;
DROP TRIGGER IF EXISTS `trg_actualizar_total_update`;
DROP TRIGGER IF EXISTS `trg_actualizar_total_delete`;

-- 2. CREAR TRIGGERS CORRECTOS

DELIMITER $$

-- ==================================================================
-- TRIGGER: Calcular totales al INSERTAR item
-- ==================================================================
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


-- ==================================================================
-- TRIGGER: Calcular totales al ACTUALIZAR item
-- ==================================================================
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


-- ==================================================================
-- TRIGGER: Calcular totales al ELIMINAR item
-- ==================================================================
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
-- 3. RECALCULAR VENTAS EXISTENTES
-- ==================================================================

-- Actualizar todas las ventas existentes con los totales correctos
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
-- 4. VERIFICAR RESULTADOS
-- ==================================================================

SELECT
    'Verificación de Cálculos' as titulo,
    av.id,
    av.idventa,
    av.subtotal,
    av.descuento,
    av.total_venta,
    av.valor_abono,
    av.saldo,
    (SELECT SUM(avi.subtotal) FROM alistamiento_venta_items avi WHERE avi.alistamiento_id = av.id) as subtotal_calculado
FROM alistamiento_venta av
ORDER BY av.id;

-- ==================================================================
-- SCRIPT COMPLETADO
-- ==================================================================
