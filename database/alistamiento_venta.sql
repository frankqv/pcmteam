-- ==================================================================
-- SISTEMA DE ALISTAMIENTO DE VENTAS - PCMARKETTEAM
-- Versión: 1.0
-- Fecha: 2025-01-23
-- ==================================================================

-- ==================================================================
-- TABLA PRINCIPAL: Encabezado de Venta/Pedido
-- ==================================================================
CREATE TABLE `alistamiento_venta` (
    `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
    `idventa` VARCHAR(50) UNIQUE NOT NULL COMMENT 'Número de venta: AV-2025-0001',
    `ticket` VARCHAR(160) UNIQUE NOT NULL COMMENT 'Ticket alfanumérico único',

    -- ==================== FECHAS ====================
    `fecha_venta` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `fecha_actualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),

    -- ==================== SOLICITANTE ====================
    `usuario_id` INT(11) NOT NULL COMMENT 'ID del usuario que solicita',
    `sede` VARCHAR(150) NOT NULL,

    -- ==================== CLIENTE ====================
    `idcliente` INT(11) NOT NULL COMMENT 'FK a tabla clientes',

    -- ==================== ENVÍO ====================
    `ubicacion` VARCHAR(250) NOT NULL COMMENT 'Dirección de envío',
    `numguia_envio` VARCHAR(250) DEFAULT NULL COMMENT 'Número de guía',

    -- ==================== VALORES FINANCIEROS ====================
    `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `descuento` DECIMAL(12,2) DEFAULT 0.00,
    `total_venta` DECIMAL(12,2) NOT NULL COMMENT 'Total final',
    `valor_abono` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Cuánto abonó',
    `saldo` DECIMAL(12,2) NOT NULL COMMENT 'Cuánto queda',

    -- ==================== MÉTODOS DE PAGO ====================
    `medio_abono` ENUM('efectivo', 'transferencia', 'tarjeta_credito', 'tarjeta_debito',
                       'nequi', 'daviplata', 'bancolombia', 'otro') DEFAULT NULL,
    `medio_saldo` ENUM('efectivo', 'transferencia', 'tarjeta_credito', 'tarjeta_debito',
                       'nequi', 'daviplata', 'bancolombia', 'otro') DEFAULT NULL,

    -- ==================== ESTADO ====================
    `estado` ENUM('borrador', 'pendiente', 'aprobado', 'en_alistamiento',
                  'alistado', 'despachado', 'en_transito', 'entregado',
                  'cancelado') NOT NULL DEFAULT 'borrador',

    -- ==================== OBSERVACIONES ====================
    `observacion_global` TEXT DEFAULT NULL,
    `observacion_tecnico` TEXT DEFAULT NULL,

    -- ==================== AUDITORÍA ====================
    `creado_por` INT(11) NOT NULL,
    `modificado_por` INT(11) DEFAULT NULL,

    -- ==================== ÍNDICES ====================
    INDEX idx_fecha (`fecha_venta`),
    INDEX idx_cliente (`idcliente`),
    INDEX idx_usuario (`usuario_id`),
    INDEX idx_estado (`estado`),
    INDEX idx_ticket (`ticket`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==================================================================
-- TABLA DETALLE: Items de la Venta
-- ==================================================================
CREATE TABLE `alistamiento_venta_items` (
    `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
    `alistamiento_id` INT(11) NOT NULL COMMENT 'FK a alistamiento_venta',
    `item_numero` INT(3) NOT NULL COMMENT 'Orden: 1, 2, 3...',

    -- ==================== PRODUCTO ====================
    `inventario_id` INT(11) DEFAULT NULL COMMENT 'FK a bodega_inventario (NULL si manual)',

    -- ==================== DESCRIPCIÓN ====================
    `producto` VARCHAR(150) NOT NULL,
    `marca` VARCHAR(150) DEFAULT NULL,
    `modelo` VARCHAR(150) DEFAULT NULL,
    `procesador` VARCHAR(150) DEFAULT NULL,
    `ram` VARCHAR(50) DEFAULT NULL,
    `disco` VARCHAR(50) DEFAULT NULL,
    `grado` ENUM('A', 'B', 'C', 'N/A') DEFAULT NULL,
    `descripcion` TEXT DEFAULT NULL,

    -- ==================== CANTIDADES Y PRECIOS ====================
    `cantidad` INT(5) NOT NULL DEFAULT 1,
    `precio_unitario` DECIMAL(12,2) NOT NULL,
    `subtotal` DECIMAL(12,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED,

    -- ==================== ESTADO ====================
    `estado_alistamiento` ENUM('pendiente', 'en_proceso', 'alistado', 'despachado')
                          DEFAULT 'pendiente',
    `observacion` TEXT DEFAULT NULL,

    -- ==================== ÍNDICES ====================
    INDEX idx_alistamiento (`alistamiento_id`),
    INDEX idx_inventario (`inventario_id`),

    -- ==================== FOREIGN KEYS ====================
    FOREIGN KEY (`alistamiento_id`) REFERENCES `alistamiento_venta`(`id`) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==================================================================
-- TABLA: Archivos Adjuntos
-- ==================================================================
CREATE TABLE `alistamiento_venta_archivos` (
    `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
    `alistamiento_id` INT(11) NOT NULL,
    `nombre_archivo` VARCHAR(255) NOT NULL,
    `ruta_archivo` VARCHAR(500) NOT NULL COMMENT 'Ruta: a_img/ventas/archivo.jpg',
    `tipo_archivo` VARCHAR(50) DEFAULT NULL,
    `tamano` INT(11) DEFAULT NULL COMMENT 'Bytes',
    `fecha_subida` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `subido_por` INT(11) NOT NULL,

    INDEX idx_alistamiento (`alistamiento_id`),
    FOREIGN KEY (`alistamiento_id`) REFERENCES `alistamiento_venta`(`id`) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==================================================================
-- VISTA: Consulta Completa de Ventas
-- ==================================================================
CREATE OR REPLACE VIEW `vista_alistamiento_ventas` AS
SELECT
    av.id,
    av.idventa,
    av.ticket,
    av.fecha_venta,
    av.estado,

    -- Cliente
    c.numid as nit_cliente,
    CONCAT(c.nomcli, ' ', IFNULL(c.apecli, '')) as nombre_cliente,
    c.celu as telefono_cliente,
    c.idsede as cliente_sede,

    -- Usuario
    u.nombre as solicitante,
    av.sede,

    -- Ubicación
    av.ubicacion,
    av.numguia_envio,

    -- Financiero
    av.subtotal,
    av.descuento,
    av.total_venta,
    av.valor_abono,
    av.saldo,
    av.medio_abono,

    -- Conteo items
    COUNT(avi.id) as total_items,
    SUM(avi.cantidad) as cantidad_productos,

    -- Observaciones
    av.observacion_global

FROM alistamiento_venta av
LEFT JOIN clientes c ON av.idcliente = c.idclie
LEFT JOIN usuarios u ON av.usuario_id = u.id
LEFT JOIN alistamiento_venta_items avi ON av.id = avi.alistamiento_id
GROUP BY av.id;


-- ==================================================================
-- TRIGGERS: Actualizar Totales Automáticamente
-- ==================================================================
DELIMITER $$

CREATE TRIGGER `trg_actualizar_total_insert`
AFTER INSERT ON `alistamiento_venta_items`
FOR EACH ROW
BEGIN
    UPDATE alistamiento_venta
    SET subtotal = (
        SELECT IFNULL(SUM(subtotal), 0)
        FROM alistamiento_venta_items
        WHERE alistamiento_id = NEW.alistamiento_id
    ),
    total_venta = subtotal - IFNULL(descuento, 0),
    saldo = (subtotal - IFNULL(descuento, 0)) - IFNULL(valor_abono, 0)
    WHERE id = NEW.alistamiento_id;
END$$

CREATE TRIGGER `trg_actualizar_total_update`
AFTER UPDATE ON `alistamiento_venta_items`
FOR EACH ROW
BEGIN
    UPDATE alistamiento_venta
    SET subtotal = (
        SELECT IFNULL(SUM(subtotal), 0)
        FROM alistamiento_venta_items
        WHERE alistamiento_id = NEW.alistamiento_id
    ),
    total_venta = subtotal - IFNULL(descuento, 0),
    saldo = (subtotal - IFNULL(descuento, 0)) - IFNULL(valor_abono, 0)
    WHERE id = NEW.alistamiento_id;
END$$

CREATE TRIGGER `trg_actualizar_total_delete`
AFTER DELETE ON `alistamiento_venta_items`
FOR EACH ROW
BEGIN
    UPDATE alistamiento_venta
    SET subtotal = IFNULL((
        SELECT SUM(subtotal)
        FROM alistamiento_venta_items
        WHERE alistamiento_id = OLD.alistamiento_id
    ), 0),
    total_venta = subtotal - IFNULL(descuento, 0),
    saldo = (subtotal - IFNULL(descuento, 0)) - IFNULL(valor_abono, 0)
    WHERE id = OLD.alistamiento_id;
END$$

DELIMITER ;


-- ==================================================================
-- DATOS DE EJEMPLO (Opcional - Comentar si no necesitas)
-- ==================================================================
-- INSERT INTO `alistamiento_venta`
-- (`idventa`, `ticket`, `usuario_id`, `sede`, `idcliente`, `ubicacion`,
--  `total_venta`, `valor_abono`, `saldo`, `creado_por`)
-- VALUES
-- ('AV-2025-0001', 'TKT-2025-001', 1, 'Bogotá', 1, 'Calle 100 #15-20',
--  1500000.00, 500000.00, 1000000.00, 1);
