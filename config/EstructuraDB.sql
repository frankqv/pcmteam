SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "-05:00";



CREATE TABLE `clientes` (
  `idclie` int NOT NULL primary key auto_increment,
  `numid` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomcli` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `apecli` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `naci` date NOT NULL DEFAULT '1900-01-01',
  `correo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `celu` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estad` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dircli` text COLLATE utf8mb4_unicode_ci,
  `ciucli` text COLLATE utf8mb4_unicode_ci,
  `idsede` text COLLATE utf8mb4_unicode_ci,
  `canal_venta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_cliente` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` (`idclie`, `numid`, `nomcli`, `apecli`, `naci`, `correo`, `celu`, `estad`, `fere`, `dircli`, `ciucli`, `idsede`, `canal_venta`, `tipo_cliente`) VALUES
(20, '12345678', 'Juan', 'Perez', '1990-01-01', 'juan@correo.com', '3001234567', 'Activo', '2025-09-19 15:13:30', 'Calle 1 #2-3', 'Bogotá', 'Medellin', 'whatsapp', 'usuario_final'),
(21, '87654321', 'Maria', 'Garay', '1985-05-15', 'maria@correo.com', '3009876543', 'Activo', '2025-09-19 15:13:30', 'Carrera 5 #10-20', 'Bogotá', 'Capitaltech', 'facebook', 'mayorista'),
(22, '11223344', 'Carlos', 'Lopez Vanegas', '1992-08-22', 'carlos@correo.com', '3005556772', 'Activo', '2025-09-19 15:13:30', 'Avenida 3 #15-8', 'Cucuta', 'Cucuta', 'whatsapp_pauta', 'mayorista'),
(23, '55667788', 'Anyi', 'Rodriguez Vidal', '1988-12-10', 'ana@correo.com', '3001112222', 'Activo', '2025-09-19 15:13:30', 'Calle 8 #25-12', 'Bogotá', 'Principal', 'venta_punto_fisico', 'mayorista'),
(24, '1000603244', 'Gabiriel Arturo', 'Pacheco Franco', '2025-10-16', 'arturpacheco@gmail.com', '3242023365', 'Activo', '2025-10-16 15:19:37', 'Cra. 53 #14-51, Puente Aranda, Bogotá', 'BOGOTA (C/MARCA) (110110)', 'Principal', 'venta_fisica_por_anuncio', 'usuario_final'),
(25, '1022324324', 'Andres', 'Christensen Central', '2025-10-23', 'andreschristensen@gmail.com', '3001234567', 'Activo', '2025-10-23 21:16:41', 'Calle 14 #53-19', 'Bogotá D.C', 'Medellin', 'whatsapp', 'mayorista'),
(26, '1012365789', 'Susana', 'Distacia Lopez', '2025-10-24', 'susanalopez@gmail.com', '3001421092', 'Activo', '2025-10-24 20:55:40', 'Cra. 53 #14-51, Puente Aranda, Bogotá', 'BOGOTA (C/MARCA) (110110)', 'Medellin', 'venta_punto_final', 'usuario_final'),
(27, '1234598760', 'Fernando', 'Perez Rodriguez', '2025-10-28', 'fernandoperez@gmail.com', '3245012342', 'Activo', '2025-10-28 22:35:11', 'Cra. 53 #14-51, Puente Aranda, Bogotá', 'BOGOTA (C/MARCA) (110110)', 'Medellin', 'whatsapp_pauta', 'mayorista');

CREATE TABLE `usuarios` (
  `id` int NOT NULL primary key auto_increment,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clave` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(450) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'reere.webp',
  `estado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idsede` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cumple` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Fecha de cumpleaños del usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `correo`, `clave`, `rol`, `foto`, `estado`, `fere`, `idsede`, `cumple`) VALUES
(1, 'Frank Quiñonez Vidal', 'frank', 'frank@admin.com', '202cb962ac59075b964b07152d234b70', '1', 'reere.webp', '1', '2025-05-29 00:48:15', 'Todo', ''),
(2, 'Cristhian Romero', 'CristhianRomeropc', 'cr123@data.com', '53c9051e332d17250009640d364414c4', '1', 'reere.webp', '1', '2025-05-29 08:37:54', 'Principal', NULL),
(3, 'Jasson Robles', 'Jassonroblespc', 'jr123@data.com', '75dbf8a92d4276fb51528da4e4a9d2c3', '1', 'reere.webp', '1', '2025-05-29 08:38:35', 'Principal', NULL),
(4, 'Andrés Buitrago', 'AndresBuitragopc', 'ab123@data.com', '4b812d068c142583012bfb70131a61ab', '1', 'reere.webp', '1', '2025-05-29 08:38:57', 'Principal', NULL),
(5, 'Nohelia Jaraba', 'Noheliajarabapc', 'nj123@data.com', '58b906fa888b10ebd49aa571bcef5149', '1', 'reere.webp', '1', '2025-05-29 08:39:16', 'Principal', NULL),
(6, 'Anyi González', 'AnyiGonzalezpc', 'anyig123@data.com', '45deac01a8028dd922151f30e78e54ae', '1', 'reere.webp', '1', '2025-05-29 08:39:49', 'Principal', NULL),
(7, 'FranciscoQV', 'Francisco QV', 'fqv123@data.com', 'e555b59d75e072eb5f18124db1cf1e22', '1', 'reere.webp', '1', '2025-05-29 08:40:11', 'Principal', ''),
(8, 'Sergio Lara', 'Sergiolarapc', 'sl123@data.com', '16170c99b0432f43d245347aa04aceaf', '6', 'reere.webp', '1', '2025-05-29 08:40:48', 'Principal', NULL),
(9, 'Juan González', 'Juangonzalezpc', 'jg123@data.com', '210a23d675fe23128f532944f408089c', '5', 'reere.webp', '1', '2025-05-29 08:41:07', 'Principal', NULL),
(10, 'Luis González', 'Luisgonzalezpc', 'lg123@data.com', '2f04e635bab80099208ccdd506acad69', '6', 'reere.webp', '1', '2025-05-29 08:41:31', 'Principal', NULL),
(11, 'Natali Florez', 'Nataliflorezpc', 'nf123@data.com', 'd8ef5df38ad01af8d7c5e6e7a478f00d', '2', 'reere.webp', '1', '2025-05-30 00:25:58', 'Principal', NULL),
(12, 'Fabian Sanchez', 'Fabiansanchezpc', 'fs123@data.com', 'c7417ff8f3f5c8600b914497b6b73492', '6', 'reere.webp', '1', '2025-05-30 00:27:34', 'Principal', NULL),
(13, 'José Borda', 'Josebordapc', 'jb123@data.com', '4cd26c72d84d1e1d8fe7da2194d5153e', '5', 'reere.webp', '1', '2025-05-30 00:30:57', 'Principal', NULL),
(14, 'Felipe Romero', 'Feliperomeropc', 'fr123@data.com', 'c843da53f7e567b80ff967cb3ba23aee', '5', 'reere.webp', '1', '2025-05-30 00:31:24', 'Capitaltech', NULL),
(15, 'Rodrigo Martínez', 'Rodrigomartinezpc', 'rm123@data.com', 'eafabe7aff85735469db0f134663b7cb', '7', 'reere.webp', '1', '2025-05-30 00:31:43', 'Principal', NULL),
(16, 'Deivi Lopez', 'Deivilopezpc', 'dl123@data.com', 'facbcd76dde2c647198b1bab1d5d834d', '7', 'reere.webp', '1', '2025-05-30 00:32:08', 'Principal', NULL),
(17, 'Maricela Tabla', 'Maricelatablapc', 'mt123@data.com', '0e57650e147ce827aec8b788db5a25ab', '3', 'reere.webp', '1', '2025-05-30 00:32:29', 'Principal', NULL),
(18, 'Ana Gaviria Contable', 'Anagaviriapc', 'ag123@data.com', '30e5488c3c420588715fe3a51143e7ec', '0', 'reere.webp', '1', '2025-05-30 00:32:51', 'Remoto', '2025-09-01'),
(19, 'Laura Pedraza', 'Laurapedrazapc', 'lp123@data.com', '39382aa4884af196f11ed8feba7d128f', '4', 'reere.webp', '1', '2025-05-30 00:33:16', 'Capital tech', NULL),
(22, 'Mónica Valencia', 'Monicavalenciapc', 'mv123@data.com', '213a253bf5cce2d84e4032ace9e29aa7', '4', 'reere.webp', '1', '2025-05-30 00:34:06', 'Medellin', NULL),
(28, 'frank2', 'frank2', 'frank2@gmail.com', '202cb962ac59075b964b07152d234b70', '2', 'reere.webp', '1', '2025-06-07 07:40:21', 'Principal', NULL),
(29, 'frank3', 'frank3', 'frank3@gmail.com', '202cb962ac59075b964b07152d234b70', '3', 'reere.webp', '1', '2025-06-10 06:07:48', 'Cucuta', NULL),
(31, 'frank Comercial Medellin', 'frank4', 'frank4@gmail.com', 'caf1a3dfb505ffed0d024130f58c5cfa', '4', 'reere.webp', '1', '2025-06-10 06:08:22', 'Medellin', ''),
(32, 'frank5', 'frank5', 'frank5@gmail.com', '202cb962ac59075b964b07152d234b70', '5', 'reere.webp', '1', '2025-06-10 06:08:38', 'Medellin', NULL),
(33, 'Tecnico FranciscoQV', 'frank6', 'frank6@gmail.com', '202cb962ac59075b964b07152d234b70', '6', 'reere.webp', '1', '2025-06-10 06:09:04', 'Medellin', ''),
(34, 'frank7', 'frank7', 'frank7@gmail.com', '202cb962ac59075b964b07152d234b70', '7', 'reere.webp', '1', '2025-06-10 06:09:18', 'Principal', NULL),
(35, 'salome', 'salome', 'salome@gmail.com', '202cb962ac59075b964b07152d234b70', '2', 'reere.webp', '1', '2025-07-13 01:22:31', 'Medellin', NULL),
(36, 'Karen Perez', 'karenperez', 'karenperez@testeo.com', 'a98d0843cade39eb83e1807304341392', '1', 'reere.webp', '1', '2025-09-23 14:40:32', NULL, NULL),
(37, 'American System Medellin', 'AmericanMedellin', 'americansystem25@gmail.com', '9e82511a70f25d277b3105a56b65ed92', '4', '1', '1', '2025-10-18 15:19:37', 'Medellin', '');


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



