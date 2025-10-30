SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "-05:00";

CREATE TABLE `ingresos` (
  `iding` int NOT NULL primary key AUTO_INCREMENT,
  `alistamiento_venta_id` int NOT NULL,
  `detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(16,2) NOT NULL,
  `metodo_pago` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia_pago` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recibido_por` int NOT NULL,
  `idcliente` int NOT NULL,
  `observacion_ingresos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ingresos` (`iding`, `alistamiento_venta_id`, `detalle`, `total`, `metodo_pago`, `referencia_pago`, `recibido_por`, `idcliente`, `observacion_ingresos`, `fecha_registro`, `fecha_modificacion`) VALUES
(1, 4, 'Abono inicial - Venta AV-2025-0002', 2600000.00, 'Transferencia', '', 31, 21, 'Primer abono registrado al momento de crear la venta', '2025-10-28 17:53:28', '2025-10-28 17:53:28'),
(2, 5, 'Abono inicial - Venta AV-2025-0003', 5500000.00, 'Transferencia', '', 31, 26, 'Primer abono registrado al momento de crear la venta', '2025-10-29 10:28:07', '2025-10-29 10:28:07');

ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`iding`),
  ADD KEY `idcliente` (`idcliente`),
  ADD KEY `recibido_por` (`recibido_por`);

ALTER TABLE `ingresos`
  ADD CONSTRAINT `ingresos_ibfk_1` FOREIGN KEY (`idcliente`) REFERENCES `clientes` (`idclie`),
  ADD CONSTRAINT `ingresos_ibfk_2` FOREIGN KEY (`recibido_por`) REFERENCES `usuarios` (`id`);
COMMIT;

CREATE TABLE `gastos` (
  `idga` int NOT NULL primary key AUTO_INCREMENT,
  `detalle` varchar(350) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(16,2) NOT NULL,
  `metodo_pago` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gasto_por` int NOT NULL,
  `idcliente` int NOT NULL,
  `foto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_resgistro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`gasto_por`) REFERENCES `usuarios` (`id`);
COMMIT;
