SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `bodega_asignaciones` (
  `id` int(11) NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `bodega_cart_compra` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `precio_unit` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) GENERATED ALWAYS AS (`qty` * ifnull(`precio_unit`,0)) STORED,
  `added_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_compra` (
  `id` int(11) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `total_compra` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fecha_compra` datetime DEFAULT current_timestamp(),
  `estado` enum('pendiente','recibido','cancelado') DEFAULT 'pendiente',
  `tipo_doc` enum('factura','remision','otro') DEFAULT 'factura',
  `referencia` varchar(150) DEFAULT NULL,
  `evidencia` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_control_calidad` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_control` datetime NOT NULL DEFAULT current_timestamp(),
  `tecnico_id` int(11) NOT NULL COMMENT 'ID del técnico que realiza el control',
  `burning_test` text NOT NULL COMMENT 'Resultado de Burning Test',
  `sentinel_test` text NOT NULL COMMENT 'Resultado de Sentinel',
  `estado_final` enum('aprobado','rechazado') NOT NULL,
  `categoria_rec` varchar(50) NOT NULL COMMENT 'Categorización REC',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_control_calidad` (`id`, `inventario_id`, `fecha_control`, `tecnico_id`, `burning_test`, `sentinel_test`, `estado_final`, `categoria_rec`, `observaciones`) VALUES
(8, 57, '2025-09-19 09:27:14', 1, 'bien', 'bien', 'aprobado', 'REC-A', 'n/d'),
(9, 106, '2025-09-24 22:18:01', 38, 'okay', 'okay', 'aprobado', 'REC-A', 'okay');

CREATE TABLE `bodega_diagnosticos` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_diagnostico` datetime NOT NULL DEFAULT current_timestamp(),
  `tecnico_id` int(11) NOT NULL COMMENT 'ID del técnico que realiza el diagnóstico',
  `camara` text DEFAULT NULL COMMENT 'Resultado prueba de cámara',
  `teclado` text DEFAULT NULL COMMENT 'Resultado prueba de teclado',
  `parlantes` text DEFAULT NULL COMMENT 'Resultado prueba de audio',
  `bateria` text DEFAULT NULL COMMENT 'Resultado prueba de batería',
  `microfono` text DEFAULT NULL COMMENT 'Resultado prueba de micrófono',
  `pantalla` text DEFAULT NULL COMMENT 'Resultado prueba de pantalla',
  `puertos` text DEFAULT NULL COMMENT 'Resultado prueba de puertos',
  `disco` text DEFAULT NULL COMMENT 'Resultado prueba de disco',
  `falla_electrica` enum('si','no') NOT NULL DEFAULT 'no',
  `detalle_falla_electrica` text DEFAULT NULL,
  `falla_estetica` enum('si','no') NOT NULL DEFAULT 'no',
  `detalle_falla_estetica` text DEFAULT NULL,
  `estado_reparacion` enum('falla_mecanica','falla_electrica','reparacion_cosmetica','aprobado') NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_diagnosticos` (`id`, `inventario_id`, `fecha_diagnostico`, `tecnico_id`, `camara`, `teclado`, `parlantes`, `bateria`, `microfono`, `pantalla`, `puertos`, `disco`, `falla_electrica`, `detalle_falla_electrica`, `falla_estetica`, `detalle_falla_estetica`, `estado_reparacion`, `observaciones`) VALUES
(60, 57, '2025-09-18 15:58:36', 1, 'MALO', 'N/D', 'BUENO', 'BUENO', 'MALO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"MALO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 100', 'si', 'Puerto USB dañado', 'si', 'rayones', 'aprobado', 'N/D'),
(61, 57, '2025-09-19 09:18:27', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: %', 'no', NULL, 'no', NULL, 'aprobado', ''),
(62, 59, '2025-09-23 16:50:03', 36, 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 76', 'no', '', 'si', 'una tecla pelada (a)', 'falla_electrica', 'no sirve parlante'),
(63, 59, '2025-09-23 16:56:13', 36, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: %', 'no', NULL, 'no', NULL, 'falla_electrica', 'falla padmaouse'),
(64, 79, '2025-09-23 22:17:45', 36, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 93', 'no', '', 'no', '', 'aprobado', 'Pendiente por probar puertos de red y hmdi'),
(65, 64, '2025-09-23 22:38:16', 36, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 93', 'no', '', 'no', '', 'aprobado', 'Pendientes puertos de red y hdmi'),
(66, 78, '2025-09-23 22:51:22', 36, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 0', 'no', '', 'no', '', 'aprobado', 'Pendientes puertos de red y hdmi'),
(67, 105, '2025-09-24 14:17:59', 1, 'MALO', 'BUENO', 'MALO', 'BUENO', 'MALO', 'MALO', '{\"VGA\":\"BUENO\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 100', 'si', 'falla electrica', 'si', 'falla estica2', 'aprobado', 'Falta el puerto'),
(68, 65, '2025-09-24 14:27:15', 36, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 99', 'no', '', 'no', '', 'aprobado', 'buenas condiciones'),
(69, 106, '2025-09-24 14:34:14', 1, 'MALO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'MALO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 100', 'si', 'falla 1', 'si', 'si tiene falla 2', 'aprobado', 'teiene una falla'),
(70, 106, '2025-09-24 15:19:10', 1, 'BUENO', 'BUENO', 'MALO', 'MALO', 'BUENO', 'MALO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"N\\/D\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 93', 'si', 'puerto, camara borrosa', 'si', 'rayones', 'aprobado', 'okay'),
(71, 106, '2025-09-24 15:22:56', 1, 'BUENO', 'BUENO', 'BUENO', 'MALO', 'MALO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 100', 'no', '', 'no', '', 'aprobado', 'okay'),
(72, 106, '2025-09-24 15:51:31', 1, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'MALO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 100', 'no', '', 'no', '', 'aprobado', 'okay'),
(73, 106, '2025-09-24 16:16:14', 1, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'MALO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"MALO\"}', 'Estado: N/D; Vida útil: 100', 'si', 'microfono', 'si', 'rayones', 'aprobado', 'revision'),
(74, 62, '2025-09-24 17:16:25', 36, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 99', 'no', '', 'no', '', 'aprobado', 'pendiente red y hdmi'),
(75, 62, '2025-09-24 17:19:56', 36, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'BUENO', 'MALO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 99', 'no', '', 'no', '', 'falla_electrica', 'pantalla manchada y puertos pendientes red y hmdi'),
(76, 60, '2025-09-24 17:26:27', 36, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 0', 'no', '', 'no', '', 'aprobado', 'pendiente red y hdmi'),
(77, 70, '2025-09-24 17:51:15', 36, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 0', 'no', '', 'no', '', 'aprobado', 'buenas condiciones'),
(78, 69, '2025-09-24 17:59:58', 36, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 100', 'no', '', 'no', '', 'aprobado', 'buenas condiciones'),
(79, 102, '2025-09-24 19:09:52', 36, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 0', 'no', '', 'no', '', 'aprobado', 'Puertos pendientes  de red y hmdi'),
(80, 85, '2025-09-24 19:21:14', 36, 'BUENO', 'BUENO', 'BUENO', 'N/D', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 96', 'no', '', 'no', '', 'aprobado', 'Pendiente puertos de red y hdmi'),
(157, 140, '2025-09-30 09:29:38', 36, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 97', 'si', 'temperatura', 'no', '', 'falla_electrica', 'PENDIENTE PUERTOS DE RED Y HDMI'),
(158, 154, '2025-09-30 09:31:45', 36, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 92', 'si', 'TEMPERATURA', 'no', '', 'falla_electrica', 'PENDIENTE PUERTOS DE RED Y HDMI'),
(159, 129, '2025-09-30 09:35:04', 36, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"N\\/D\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 90', 'si', 'TEMPERATURA', 'no', '', 'falla_electrica', 'PENDIENTE PUERTOS DE RED Y HDMI'),
(160, 74, '2025-10-01 15:41:09', 12, 'MALO', 'N/D', 'MALO', 'BUENO', 'MALO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 97%', 'no', NULL, 'no', NULL, 'falla_electrica', 'NO FUNCIONA CAMMARA Y MICROFONO, NO LE FUCIONA WIFI');

CREATE TABLE `bodega_electrico` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `fecha_proceso` datetime NOT NULL DEFAULT current_timestamp(),
  `tecnico_id` int(11) NOT NULL,
  `estado_bateria` text DEFAULT NULL,
  `estado_fuente` text DEFAULT NULL,
  `estado_puertos` text DEFAULT NULL,
  `estado_pantalla` text DEFAULT NULL,
  `estado_teclado` text DEFAULT NULL,
  `estado_audio` text DEFAULT NULL,
  `fallas_detectadas` text DEFAULT NULL,
  `reparaciones_realizadas` text DEFAULT NULL,
  `estado_final` enum('aprobado','rechazado','requiere_revision') NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_electrico` (`id`, `inventario_id`, `fecha_proceso`, `tecnico_id`, `estado_bateria`, `estado_fuente`, `estado_puertos`, `estado_pantalla`, `estado_teclado`, `estado_audio`, `fallas_detectadas`, `reparaciones_realizadas`, `estado_final`, `observaciones`) VALUES
(5, 57, '2025-09-19 09:23:02', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'Reparado, Puerto USB lateral ', 'Reparado, Puerto USB lateral ', 'aprobado', 'Todo bien'),
(6, 57, '2025-09-19 09:23:19', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'Reparado, Puerto USB lateral ', 'Reparado, Puerto USB lateral ', 'aprobado', 'Todo bien'),
(7, 106, '2025-09-24 22:16:56', 38, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'okay', 'okay', 'aprobado', 'okay'),
(8, 106, '2025-09-24 22:17:03', 38, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'okay', 'okay', 'aprobado', 'okay');

CREATE TABLE `bodega_entradas` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_entrada` datetime NOT NULL DEFAULT current_timestamp(),
  `proveedor_id` int(11) NOT NULL COMMENT 'ID del proveedor',
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que registra',
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_entradas` (`id`, `inventario_id`, `fecha_entrada`, `proveedor_id`, `usuario_id`, `cantidad`, `observaciones`) VALUES
(28, 56, '2025-09-18 15:29:33', 1, 1, 1, 'Prueba de inserción'),
(29, 57, '2025-09-18 15:30:37', 8, 1, 1, 'lista'),
(30, 58, '2025-09-23 14:47:11', 1, 1, 1, 'Prueba de inserción'),
(31, 59, '2025-09-23 15:23:33', 1, 1, 1, 'Prueba de inserción'),
(32, 105, '2025-09-23 19:36:40', 1, 1, 1, 'Prueba de inserción'),
(33, 106, '2025-09-24 14:31:38', 1, 1, 1, 'Prueba de inserción'),
(34, 162, '2025-09-26 22:31:11', 24, 1, 1, ''),
(35, 227, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2026'),
(36, 228, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2027'),
(37, 229, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2028'),
(38, 230, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2029'),
(39, 231, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2030'),
(40, 232, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2031'),
(41, 233, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2032'),
(42, 234, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2033'),
(43, 235, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2034'),
(44, 236, '2025-10-09 21:33:26', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2035'),
(113, 305, '2025-10-09 22:00:02', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - n/d'),
(114, 306, '2025-10-09 22:00:02', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - n/d');

CREATE TABLE `bodega_estetico` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `fecha_proceso` datetime NOT NULL DEFAULT current_timestamp(),
  `tecnico_id` int(11) NOT NULL,
  `estado_carcasa` text DEFAULT NULL,
  `estado_pantalla_fisica` text DEFAULT NULL,
  `estado_teclado_fisico` text DEFAULT NULL,
  `rayones_golpes` text DEFAULT NULL,
  `limpieza_realizada` enum('si','no') NOT NULL DEFAULT 'no',
  `partes_reemplazadas` text DEFAULT NULL,
  `grado_asignado` enum('A','B','C','SCRAP') NOT NULL,
  `estado_final` enum('aprobado','rechazado','requiere_revision') NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_estetico` (`id`, `inventario_id`, `fecha_proceso`, `tecnico_id`, `estado_carcasa`, `estado_pantalla_fisica`, `estado_teclado_fisico`, `rayones_golpes`, `limpieza_realizada`, `partes_reemplazadas`, `grado_asignado`, `estado_final`, `observaciones`) VALUES
(11, 57, '2025-09-19 09:25:29', 1, 'EXCELENTE', 'EXCELENTE', 'EXCELENTE', 'n/d', 'si', 'n/d', 'A', 'aprobado', ''),
(12, 106, '2025-09-24 22:17:29', 38, 'EXCELENTE', 'EXCELENTE', 'EXCELENTE', 'okay', 'si', 'okay', 'A', 'aprobado', 'okay'),
(13, 106, '2025-09-24 22:17:35', 38, 'EXCELENTE', 'EXCELENTE', 'EXCELENTE', 'okay', 'si', 'okay', 'A', 'aprobado', 'okay');

CREATE TABLE `bodega_ingresos` (
  `id` int(11) NOT NULL,
  `orden_id` int(11) NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha_ingreso` datetime DEFAULT current_timestamp(),
  `metodo_pago` varchar(100) DEFAULT NULL,
  `referencia_pago` varchar(150) DEFAULT NULL,
  `recibido_por` int(11) DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_inventario` (
  `id` int(11) NOT NULL,
  `codigo_g` varchar(50) NOT NULL COMMENT 'Código general del equipo',
  `ubicacion` varchar(100) NOT NULL COMMENT 'Zona específica en bodega/laboratorio',
  `posicion` varchar(50) NOT NULL COMMENT 'Posición exacta dentro de la ubicación',
  `fecha_ingreso` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `producto` varchar(50) NOT NULL COMMENT 'Tipo de producto (Laptop, Desktop, Monitor, AIO, etc.)',
  `marca` varchar(50) NOT NULL COMMENT 'Marca del equipo',
  `serial` varchar(100) NOT NULL COMMENT 'Número de serie del fabricante',
  `modelo` varchar(100) NOT NULL COMMENT 'Modelo o Referencia del equipo',
  `procesador` varchar(100) DEFAULT NULL COMMENT 'Especificaciones del procesador',
  `ram` varchar(50) DEFAULT NULL COMMENT 'Memoria RAM instalada',
  `disco` varchar(100) DEFAULT NULL COMMENT 'Tipo y capacidad del disco',
  `pulgadas` varchar(20) DEFAULT NULL COMMENT 'Tamaño de pantalla',
  `observaciones` text DEFAULT NULL COMMENT 'Notas técnicas y observaciones',
  `grado` varchar(50) DEFAULT NULL,
  `disposicion` varchar(50) NOT NULL COMMENT 'Estado actual del equipo en el proceso',
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  `tecnico_id` int(11) DEFAULT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `tactil` text DEFAULT NULL,
  `lote` varchar(50) DEFAULT NULL,
  `activo_fijo` varchar(50) DEFAULT NULL,
  `precio` decimal(16,2) NOT NULL DEFAULT 0.00,
  `foto` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_inventario` (`id`, `codigo_g`, `ubicacion`, `posicion`, `fecha_ingreso`, `fecha_modificacion`, `producto`, `marca`, `serial`, `modelo`, `procesador`, `ram`, `disco`, `pulgadas`, `observaciones`, `grado`, `disposicion`, `estado`, `tecnico_id`, `pedido_id`, `producto_id`, `tactil`, `lote`, `activo_fijo`, `precio`, `foto`) VALUES
(57, 'LPPAD-  1432', 'Principal', 'Enviado', '2025-09-18 15:30:37', '2025-10-02 09:31:56', 'Portatil', 'HP', 'HP987654321', 'EliteDesk 800', 'Intel i5-1135G7', '8GB', '512GB SSD', '16', 'lista', 'B', 'Vendido', 'activo', 1, NULL, NULL, 'NO', 'pchekt542007-25', NULL, 1600000.00, NULL),
(60, 'EULPT2-5-003', 'Principal', '', '2025-09-23 16:20:15', '2025-09-24 19:10:52', 'LAPTOP', 'LENOVO', 'PF1LJ4ME', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'proveedor_id: 1 | cantidad: 1', 'B', 'en_proceso', 'activo', 36, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(61, 'EULPT2-5-007', 'Principal', '', '2025-09-23 16:20:15', '2025-09-24 19:21:21', 'LAPTOP', 'LENOVO', 'PF18G3UC', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'proveedor_id: 1 | cantidad: 1', 'B', 'en_diagnostico', 'activo', 36, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(62, 'EULPT2-5-008', 'Principal', '', '2025-09-23 16:20:15', '2025-10-09 21:32:24', 'LAPTOP', 'LENOVO', 'PF1DS1YZ', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'Mancha en pantalla', 'B', 'Para Venta', 'activo', 38, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(63, 'EULPT2-5-023', 'Principal', '', '2025-09-23 16:20:15', '2025-09-24 19:21:06', 'LAPTOP', 'LENOVO', 'PF15R7EZ', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'proveedor_id: 1 | cantidad: 1', 'B', 'en_diagnostico', 'activo', 36, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(64, 'EULPT2-5-026', 'Principal', '', '2025-09-23 16:20:15', '2025-09-24 21:57:23', 'LAPTOP', 'LENOVO', 'PF15STG9', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'proveedor_id: 1 | cantidad: 1', 'B', 'en_proceso', 'activo', 38, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(65, 'EULPT2-5-028', 'Principal', '', '2025-09-23 16:20:15', '2025-09-24 18:05:26', 'LAPTOP', 'LENOVO', 'PF15RZL9', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'QUEBRADO ESQUINA | proveedor_id: 1 | cantidad: 1', 'B', 'en_proceso', 'activo', 38, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(66, 'EULPT2-5-031', 'Principal', '', '2025-09-23 16:20:15', '2025-09-26 11:31:32', 'LAPTOP', 'LENOVO', 'PF15GK8J', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'proveedor_id: 1 | cantidad: 1', 'B', 'en_proceso', 'activo', 12, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(67, 'EULPT2-5-032', 'Principal', '', '2025-09-23 16:20:15', '2025-09-24 15:33:57', 'LAPTOP', 'LENOVO', 'PF18AB8S', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'proveedor_id: 1 | cantidad: 1', 'B', 'en_proceso', 'activo', 38, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(68, 'EULPT2-5-035', 'Principal', '', '2025-09-23 16:20:15', '2025-10-01 09:35:19', 'LAPTOP', 'LENOVO', 'PF15RPV1', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'proveedor_id: 1 | cantidad: 1', 'B', 'en_proceso', 'activo', 38, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(69, 'EULPT2-5-041', 'Principal', '', '2025-09-23 16:20:15', '2025-09-24 21:06:50', 'LAPTOP', 'LENOVO', 'PF1LXW2Z', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'proveedor_id: 1 | cantidad: 1', 'B', 'en_proceso', 'activo', 38, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(70, 'EULPT2-5-046', 'Principal', '', '2025-09-23 16:20:15', '2025-09-24 21:07:30', 'LAPTOP', 'LENOVO', 'PF1LHRJQ', 'T470', 'I5 6TH', '8GB', '256SSD', '14\"', 'proveedor_id: 1 | cantidad: 1', 'B', 'en_diagnostico', 'activo', 38, 1, NULL, 'SI', 'EULPT2-5', NULL, 0.00, NULL),
(297, 'n_d_cucuta_oct25_8', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Lenovo', 'MJ07CM08', 'M710S', 'I5-6TH', '8GB', '500 HDD', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(298, 'n_d_cucuta_oct25_9', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Lenovo', 'MJ05KEJR', 'M710S', 'I5-6TH', '8GB', '500 HDD', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(299, 'n_d_cucuta_oct25_10', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Lenovo', 'MJPVZ17', 'M82', 'CELERON', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL);
INSERT INTO `bodega_inventario` (`id`, `codigo_g`, `ubicacion`, `posicion`, `fecha_ingreso`, `fecha_modificacion`, `producto`, `marca`, `serial`, `modelo`, `procesador`, `ram`, `disco`, `pulgadas`, `observaciones`, `grado`, `disposicion`, `estado`, `tecnico_id`, `pedido_id`, `producto_id`, `tactil`, `lote`, `activo_fijo`, `precio`, `foto`) VALUES
(300, 'n_d_cucuta_oct25_11', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Lenovo', 'MJ040V2Z', 'M93', 'AMD A8', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(301, 'n_d_cucuta_oct25_12', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Lenovo', 'MJPVE73', 'M90', 'CELERON', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(302, 'n_d_cucuta_oct25_13', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Lenovo', 'MJLGW78', 'M92', 'CELERON', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(303, 'n_d_cucuta_oct25_14', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Lenovo', 'MJ13622', 'M77', 'AMD A8', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(304, 'n_d_cucuta_oct25_15', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Lenovo', 'MJPWN50', 'M77', 'AMD A8', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(305, 'n_d_cucuta_oct25_16', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Dell', 'D8SQDF1', 'VOSTRO 200', 'PENTIUM', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(306, 'n_d_cucuta_oct25_17', 'Cucuta', 'Recibido', '2025-10-09 22:00:02', '2025-10-09 22:00:02', 'Desktop', 'Lenovo', 'PB44R6P', 'M77', 'AMD A8', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL);

CREATE TABLE `bodega_log_cambios` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL COMMENT 'ID del equipo en inventario',
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que realizó el cambio',
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha y hora del cambio',
  `campo_modificado` varchar(100) NOT NULL COMMENT 'Nombre del campo modificado',
  `valor_anterior` text DEFAULT NULL COMMENT 'Valor anterior del campo',
  `valor_nuevo` text DEFAULT NULL COMMENT 'Nuevo valor del campo',
  `tipo_cambio` enum('edicion_manual','importacion','sistema') NOT NULL DEFAULT 'edicion_manual' COMMENT 'Tipo de cambio realizado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de cambios realizados en equipos del inventario';

INSERT INTO `bodega_log_cambios` (`id`, `inventario_id`, `usuario_id`, `fecha_cambio`, `campo_modificado`, `valor_anterior`, `valor_nuevo`, `tipo_cambio`) VALUES
(29, 57, 1, '2025-09-19 09:23:02', 'disposicion_electrico', 'en_mantenimiento', 'pendiente_estetico', 'edicion_manual'),
(30, 57, 1, '2025-09-19 09:23:19', 'disposicion_electrico', 'en_mantenimiento', 'pendiente_estetico', 'edicion_manual'),
(31, 57, 1, '2025-09-19 09:25:29', 'disposicion', 'pendiente_estetico', 'pendiente_control_calidad', 'edicion_manual'),
(32, 57, 1, '2025-09-19 09:27:14', 'disposicion', 'pendiente_control_calidad', 'Para Venta', 'edicion_manual'),
(33, 106, 38, '2025-09-24 22:16:56', 'disposicion_electrico', 'en_mantenimiento', 'pendiente_estetico', 'edicion_manual'),
(34, 106, 38, '2025-09-24 22:17:03', 'disposicion_electrico', 'en_mantenimiento', 'pendiente_estetico', 'edicion_manual'),
(35, 106, 38, '2025-09-24 22:17:29', 'disposicion', 'pendiente_estetico', 'pendiente_control_calidad', 'edicion_manual'),
(36, 106, 38, '2025-09-24 22:17:35', 'disposicion', 'pendiente_estetico', 'pendiente_control_calidad', 'edicion_manual'),
(37, 106, 38, '2025-09-24 22:18:01', 'disposicion', 'pendiente_control_calidad', 'Para Venta', 'edicion_manual'),
(38, 106, 38, '2025-09-24 22:36:23', 'disposicion', 'Para Venta', 'Vendido', 'sistema'),
(39, 57, 1, '2025-10-02 09:31:56', 'disposicion', 'Para Venta', 'Vendido', 'sistema'),
(43, 296, 1, '2025-10-17 15:49:08', 'precio', NULL, '80000', 'edicion_manual'),
(44, 296, 1, '2025-10-17 15:49:21', 'precio', NULL, '850000', 'edicion_manual');

CREATE TABLE `bodega_mantenimiento` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `tecnico_id` int(11) DEFAULT NULL,
  `usuario_registro` int(11) DEFAULT NULL,
  `estado` enum('pendiente','realizado','rechazado') NOT NULL DEFAULT 'pendiente',
  `tipo_proceso` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `falla_electrica` enum('si','no') NOT NULL DEFAULT 'no',
  `detalle_falla_electrica` text DEFAULT NULL,
  `falla_estetica` enum('si','no') NOT NULL DEFAULT 'no',
  `detalle_falla_estetica` text DEFAULT NULL,
  `partes_solicitadas` text DEFAULT NULL,
  `referencia_externa` varchar(255) DEFAULT NULL,
  `tecnico_diagnostico` int(11) DEFAULT NULL,
  `limpieza_electronico` enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
  `observaciones_limpieza_electronico` text DEFAULT NULL,
  `mantenimiento_crema_disciplinaria` enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
  `observaciones_mantenimiento_crema` text DEFAULT NULL,
  `mantenimiento_partes` enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
  `cambio_piezas` enum('si','no') DEFAULT 'no',
  `piezas_solicitadas_cambiadas` text DEFAULT NULL,
  `proceso_reconstruccion` enum('si','no') DEFAULT 'no',
  `parte_reconstruida` text DEFAULT NULL,
  `limpieza_general` enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
  `remite_otra_area` enum('si','no') DEFAULT 'no',
  `area_remite` text DEFAULT NULL,
  `proceso_electronico` text DEFAULT NULL,
  `observaciones_globales` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_mantenimiento` (`id`, `inventario_id`, `fecha_registro`, `tecnico_id`, `usuario_registro`, `estado`, `tipo_proceso`, `observaciones`, `falla_electrica`, `detalle_falla_electrica`, `falla_estetica`, `detalle_falla_estetica`, `partes_solicitadas`, `referencia_externa`, `tecnico_diagnostico`, `limpieza_electronico`, `observaciones_limpieza_electronico`, `mantenimiento_crema_disciplinaria`, `observaciones_mantenimiento_crema`, `mantenimiento_partes`, `cambio_piezas`, `piezas_solicitadas_cambiadas`, `proceso_reconstruccion`, `parte_reconstruida`, `limpieza_general`, `remite_otra_area`, `area_remite`, `proceso_electronico`, `observaciones_globales`) VALUES
(24, 57, '2025-09-19 09:21:27', 38, 1, 'realizado', NULL, NULL, 'si', 'Puerto lateral USB', 'si', 'Tapas rayadas', NULL, NULL, NULL, 'realizada', 'okay', 'realizada', 'okay', 'pendiente', 'si', '{\"detalle\":\"se requiere\",\"cantidad\":\"1\",\"codigo_equipo\":\"LPDA1432\",\"serial_parte\":\"L15L3A03\",\"marca_parte\":\"LENOVO\",\"nivel_urgencia\":\"Baja\",\"referencia_parte\":\"L15L3A03\",\"ubicacion_pieza\":\"CAJA B2\"}', 'no', '', 'pendiente', 'no', '', '', 'Fallos estetico y esteico'),
(25, 59, '2025-09-23 16:57:44', 36, 36, 'realizado', NULL, NULL, 'si', '', 'si', '', NULL, NULL, NULL, 'realizada', '', 'realizada', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'si', 'laboratorio', '', ''),
(26, 64, '2025-09-24 22:01:01', 38, 38, 'realizado', NULL, NULL, 'no', '', 'si', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'si', '', 'pendiente', 'si', 'laboratorio', '', ''),
(27, 64, '2025-09-24 22:07:23', 38, 38, 'realizado', NULL, NULL, 'si', 'puertos usb sulfatados', 'no', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'si', 'laboratorio', '', ''),
(28, 79, '2025-09-24 22:46:09', 38, 38, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', ''),
(29, 67, '2025-09-25 16:18:58', 38, 38, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'realizada', 'Ninguna', 'realizada', 'Estado normal ', 'pendiente', 'no', '', 'si', 'Membrana teclado hacia arriba', 'pendiente', 'no', '', '', 'Se reconstruyo membrana teclado, flecha hacia arriba removiendo la parte dañada y sustituyendo por una nueva.'),
(30, 62, '2025-09-25 16:26:20', 38, 38, 'realizado', NULL, NULL, 'si', 'si tiene fallo electrico', 'si', 'fallo estético', NULL, NULL, NULL, 'realizada', 'realizado', 'realizada', 'realizado', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', 'fallos de unida'),
(31, 62, '2025-09-25 16:27:06', 38, 38, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', ''),
(32, 62, '2025-09-25 16:34:01', 38, 38, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', ''),
(64, 99, '2025-09-29 20:02:24', 38, 38, 'realizado', NULL, NULL, 'no', '', 'si', 'Matizados de posa manos y tapa cubierta', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'si', 'Se realizo reconstruccion de posamanos y tapa cubierta ', 'pendiente', 'si', 'laboratorio', '', 'Se realizo reconstrucción de pasamanos y tapa cubierta '),
(65, 76, '2025-09-29 20:36:18', 12, 12, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'no_aplica', '', 'realizada', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', ''),
(66, 83, '2025-09-29 20:50:02', 12, 12, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'no_aplica', '', 'realizada', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', 'sin disco '),
(67, 78, '2025-09-29 22:02:36', 38, 38, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'si', '{\"detalle\":\"Teclado\",\"cantidad\":\"1\",\"codigo_equipo\":\"EULPT2-5-063\",\"serial_parte\":\"\",\"marca_parte\":\"Lenovo\",\"nivel_urgencia\":\"Baja\",\"referencia_parte\":\"\",\"ubicacion_pieza\":\"\"}', 'no', '', 'pendiente', 'no', '', '', 'Se realizó el cambio del teclado; posteriormente, se llevaron a cabo pruebas de funcionalidad, las cuales arrojaron resultados satisfactorios.'),
(68, 97, '2025-09-29 22:20:10', 12, 12, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'no_aplica', '', 'realizada', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', ''),
(69, 82, '2025-09-29 22:21:12', 12, 12, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'no_aplica', '', 'realizada', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', ''),
(70, 84, '2025-10-01 15:36:57', 12, 12, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'no_aplica', '', 'realizada', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', ''),
(71, 74, '2025-10-01 15:42:42', 12, 12, 'realizado', NULL, NULL, 'si', '', 'no', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'si', 'laboratorio', '', ''),
(72, 155, '2025-10-02 16:23:10', 38, 38, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'realizada', 'Teclado y superficies limpias.\r\nSe retiró el polvo y suciedad acumulada, dejando el equipo en condiciones óptimas de higiene.\r\n\r\nVentilador limpiado correctamente.\r\nSe eliminó el polvo que obstruía la ventilación, mejorando el flujo de aire y el rendimiento térmico.\r\n\r\nCrema disipadora reemplazada.\r\nSe aplicó nueva pasta térmica al procesador, asegurando una adecuada transferencia de calor.\r\n\r\nSistema operativo Windows 10 instalado y operativo.\r\nSe completó la instalación y configuración básica, dejando el equipo funcional y actualizado.\r\n\r\nMantenimiento preventivo completado.\r\nEl equipo queda en condiciones normales de operación. Se recomienda mantener rutina periódica de limpieza.', 'realizada', 'Ninguna', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', 'se requiere el cambio de la teclado pues esta presentando fallas en algunas teclas'),
(73, 87, '2025-10-02 16:28:11', 38, 38, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'realizada', 'Teclado y superficies limpias.\r\nSe retiró el polvo y suciedad acumulada, dejando el equipo en condiciones óptimas de higiene.\r\n\r\nVentilador limpiado correctamente.\r\nSe eliminó el polvo que obstruía la ventilación, mejorando el flujo de aire y el rendimiento térmico.\r\n\r\nCrema disipadora reemplazada.\r\nSe aplicó nueva pasta térmica al procesador, asegurando una adecuada transferencia de calor.\r\n\r\nSistema operativo Windows 10 instalado y operativo.\r\nSe completó la instalación y configuración básica, dejando el equipo funcional y actualizado.\r\n\r\nMantenimiento preventivo completado.\r\nEl equipo queda en condiciones normales de operación. Se recomienda mantener rutina periódica de limpieza.', 'realizada', 'Ninguna', 'pendiente', 'si', '{\"detalle\":\"Tapa superior\",\"cantidad\":\"1\",\"codigo_equipo\":\"EULPT2-5-087\",\"serial_parte\":\"\",\"marca_parte\":\"\",\"nivel_urgencia\":\"Baja\",\"referencia_parte\":\"\",\"ubicacion_pieza\":\"\"}', 'no', '', 'pendiente', 'no', '', '', 'Se procede a desmontar tapa superior  a fin de tener la muestra para buscarla en bodega.'),
(74, 90, '2025-10-02 16:34:58', 38, 38, 'realizado', NULL, NULL, 'no', '', 'si', 'Raya tapa superior ', NULL, NULL, NULL, 'realizada', 'Teclado y superficies limpias.\r\nSe retiró el polvo y suciedad acumulada, dejando el equipo en condiciones óptimas de higiene.\r\n\r\nVentilador limpiado correctamente.\r\nSe eliminó el polvo que obstruía la ventilación, mejorando el flujo de aire y el rendimiento térmico.\r\n\r\nCrema disipadora reemplazada.\r\nSe aplicó nueva pasta térmica al procesador, asegurando una adecuada transferencia de calor.\r\n\r\nSistema operativo Windows 10 instalado y operativo.\r\nSe completó la instalación y configuración básica, dejando el equipo funcional y actualizado.\r\n\r\nMantenimiento preventivo completado.\r\nEl equipo queda en condiciones normales de operación. Se recomienda mantener rutina periódica de limpieza.', 'realizada', 'Ninguna', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', 'Se realiza el cambio de la tecla de desplazamiento superior pues el quipo no contaba con este, adicional a ello se realiza el montaje de disco SSD/256GB,'),
(75, 103, '2025-10-02 16:43:27', 38, 38, 'realizado', NULL, NULL, 'no', '', 'si', 'Rayas tapa superior ', NULL, NULL, NULL, 'realizada', 'Teclado y superficies limpias.\r\nSe retiró el polvo y suciedad acumulada, dejando el equipo en condiciones óptimas de higiene.\r\n\r\nVentilador limpiado correctamente.\r\nSe eliminó el polvo que obstruía la ventilación, mejorando el flujo de aire y el rendimiento térmico.\r\n\r\nCrema disipadora reemplazada.\r\nSe aplicó nueva pasta térmica al procesador, asegurando una adecuada transferencia de calor.\r\n\r\nSistema operativo Windows 10 instalado y operativo.\r\nSe completó la instalación y configuración básica, dejando el equipo funcional y actualizado.\r\n\r\nMantenimiento preventivo completado.\r\nEl equipo queda en condiciones normales de operación. Se recomienda mantener rutina periódica de limpieza.', 'realizada', 'Ninguna', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', 'Se realizó el montaje de unidad SSD de 256 GB, mejorando el rendimiento general del equipo y permitiendo una mayor velocidad de arranque y respuesta del sistema.\r\n\r\nSe instaló la araña de la tecla “N”, ya que el teclado no contaba con este componente, dejando el mismo completamente funcional.'),
(76, 103, '2025-10-02 16:45:26', 38, 38, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'realizada', 'Teclado y superficies limpias.\r\nSe retiró el polvo y suciedad acumulada, dejando el equipo en condiciones óptimas de higiene.\r\n\r\nVentilador limpiado correctamente.\r\nSe eliminó el polvo que obstruía la ventilación, mejorando el flujo de aire y el rendimiento térmico.\r\n\r\nCrema disipadora reemplazada.\r\nSe aplicó nueva pasta térmica al procesador, asegurando una adecuada transferencia de calor.\r\n\r\nSistema operativo Windows 10 instalado y operativo.\r\nSe completó la instalación y configuración básica, dejando el equipo funcional y actualizado.\r\n\r\nMantenimiento preventivo completado.\r\nEl equipo queda en condiciones normales de operación. Se recomienda mantener rutina periódica de limpieza.', 'realizada', 'Ninguna', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', 'se realiza el montaje de disco SSD/256GB.'),
(77, 95, '2025-10-02 16:54:46', 38, 38, 'realizado', NULL, NULL, 'no', '', 'si', 'Matizar en la zona de reconstrucción.', NULL, NULL, NULL, 'realizada', 'Teclado y superficies limpias.\r\nSe retiró el polvo y suciedad acumulada, dejando el equipo en condiciones óptimas de higiene.\r\n\r\nVentilador limpiado correctamente.\r\nSe eliminó el polvo que obstruía la ventilación, mejorando el flujo de aire y el rendimiento térmico.\r\n\r\nCrema disipadora reemplazada.\r\nSe aplicó nueva pasta térmica al procesador, asegurando una adecuada transferencia de calor.\r\n\r\nSistema operativo Windows 10 instalado y operativo.\r\nSe completó la instalación y configuración básica, dejando el equipo funcional y actualizado.\r\n\r\nMantenimiento preventivo completado.\r\nEl equipo queda en condiciones normales de operación. Se recomienda mantener rutina periódica de limpieza.', 'realizada', 'Ninguna', 'pendiente', 'no', '', 'si', 'posa manos parte inferior izquierda.', 'pendiente', 'no', '', '', 'se realiza reconstrucción de la esquina inferior izquierda que presentaba ruptura del posa manos.'),
(78, 106, '2025-10-08 23:03:08', 1, 1, 'realizado', NULL, NULL, 'si', 'puertos sulfatados', 'si', 'rayones en tapas', NULL, NULL, NULL, 'realizada', 'realizaar', 'realizada', 'realizar', 'pendiente', 'si', '{\"detalle\":\"\",\"cantidad\":\"1\",\"codigo_equipo\":\"TEST001\",\"serial_parte\":\"\",\"marca_parte\":\"\",\"nivel_urgencia\":\"Baja\",\"referencia_parte\":\"\",\"ubicacion_pieza\":\"\"}', 'si', 'reparar', 'pendiente', 'no', '', '', 'daños globales');

CREATE TABLE `bodega_ordenes` (
  `idord` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `responsable` int(11) NOT NULL,
  `total_items` int(11) NOT NULL DEFAULT 0,
  `total_pago` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fecha_pago` datetime DEFAULT NULL,
  `metodo_pago` varchar(100) DEFAULT NULL,
  `estado_pago` enum('Debe_plata','Pendiente','Aceptado','total_pagado') DEFAULT 'Pendiente',
  `tipo_doc` enum('factura','ticket','remision') DEFAULT 'ticket',
  `num_documento` varchar(100) DEFAULT NULL,
  `evidencia_pago` varchar(255) DEFAULT NULL,
  `despachado_en` text DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_partes` (
  `id` int(11) NOT NULL,
  `caja` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `referencia` varchar(100) NOT NULL,
  `generacion` varchar(50) DEFAULT NULL,
  `numero_parte` varchar(100) DEFAULT NULL,
  `condicion` enum('Nuevo','Usado') NOT NULL,
  `teclado` enum('Con Teclado','Sin Teclado','n/n') DEFAULT 'n/n',
  `precio` decimal(12,2) NOT NULL,
  `precio_nuevo_con_teclado` decimal(12,2) DEFAULT NULL,
  `precio_nuevo_sin_teclado` decimal(12,2) DEFAULT NULL,
  `precio_usado_con_teclado` decimal(12,2) DEFAULT NULL,
  `precio_usado_sin_teclado` decimal(12,2) DEFAULT NULL,
  `producto` varchar(100) DEFAULT NULL,
  `imagen_url` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `detalles` varchar(250) DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `serial` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_partes` (`id`, `caja`, `cantidad`, `marca`, `referencia`, `generacion`, `numero_parte`, `condicion`, `teclado`, `precio`, `precio_nuevo_con_teclado`, `precio_nuevo_sin_teclado`, `precio_usado_con_teclado`, `precio_usado_sin_teclado`, `producto`, `imagen_url`, `fecha_registro`, `detalles`, `codigo`, `serial`) VALUES
(1, 'CAJA B2', 30, 'LENOVO', 'L15L3A03', NULL, 'L15L3A03', 'Usado', 'n/n', 230000.00, NULL, NULL, NULL, NULL, 'Bateria', '#', '2025-07-15 03:58:18', NULL, 'equipo2000', NULL),
(2, 'CAJA B2', 19, 'LENOVO', 'L20B2PF0', NULL, 'L20B2PF0', 'Usado', 'n/n', 240000.00, NULL, NULL, NULL, NULL, 'Bateria', '#', '2025-07-15 03:58:18', NULL, NULL, NULL),

CREATE TABLE `bodega_salidas` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `tecnico_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `orden_id` int(11) DEFAULT NULL,
  `fecha_salida` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unit` decimal(16,2) DEFAULT 0.00,
  `razon_salida` varchar(255) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado_despacho` enum('pendiente','en_ruta','entregado','cancelado') DEFAULT 'pendiente',
  `guia_remision` varchar(100) DEFAULT NULL,
  `evidencia_foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `bodega_salidas` (`id`, `inventario_id`, `cliente_id`, `tecnico_id`, `usuario_id`, `orden_id`, `fecha_salida`, `cantidad`, `precio_unit`, `razon_salida`, `observaciones`, `estado_despacho`, `guia_remision`, `evidencia_foto`) VALUES
(1, 57, NULL, 1, 1, NULL, '2025-09-18 15:47:55', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(5, 65, NULL, 36, 36, NULL, '2025-09-23 22:10:21', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 36', 'pendiente', NULL, NULL),

CREATE TABLE `bodega_solicitud_parte` (
  `id` int(11) NOT NULL,
  `detalle_solicitud` varchar(250) NOT NULL,
  `cantidad_solicitada` varchar(45) NOT NULL,
  `codigo_equipo` varchar(45) NOT NULL,
  `serial_parte` varchar(250) NOT NULL,
  `marca_parte` varchar(45) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `usuario_solicitante` int(11) DEFAULT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'pendiente',
  `fecha_solicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `nivel_urgencia` varchar(45) NOT NULL,
  `referencia_parte` varchar(45) NOT NULL,
  `ubicacion_pieza` varchar(45) NOT NULL,
  `id_tecnico` int(11) NOT NULL,
  `inventario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_solicitud_parte` (`id`, `detalle_solicitud`, `cantidad_solicitada`, `codigo_equipo`, `serial_parte`, `marca_parte`, `cantidad`, `usuario_solicitante`, `estado`, `fecha_solicitud`, `nivel_urgencia`, `referencia_parte`, `ubicacion_pieza`, `id_tecnico`, `inventario_id`) VALUES
(8, 'se requiere', '1', 'LPDA1432', 'L15L3A03', 'LENOVO', 1, 1, 'pendiente', '2025-09-19 09:21:27', 'Baja', 'L15L3A03', 'CAJA B2', 1, 57),
(9, 'Teclado', '1', 'EULPT2-5-063', '', 'Lenovo', 1, 38, 'pendiente', '2025-09-27 16:52:59', 'Baja', '', '', 38, 78),
(10, 'DISCO DURO', '1', 'EULPT2-5-061', '', '', 1, 10, 'pendiente', '2025-09-29 14:42:16', 'Baja', '', '', 10, 77),
(11, 'Teclado', '1', 'EULPT2-5-063', '', 'Lenovo', 1, 38, 'pendiente', '2025-09-29 22:02:36', 'Baja', '', '', 38, 78),
(12, 'Tapa superior', '1', 'EULPT2-5-087', '', '', 1, 38, 'pendiente', '2025-10-02 16:28:11', 'Baja', '', '', 38, 87);

CREATE TABLE `cart` (
  `idv` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `idprod` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cart_compra` (
  `idcarco` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `idprod` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria` (
  `idcate` int(11) NOT NULL,
  `nomca` text NOT NULL,
  `estado` varchar(15) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `clientes` (
  `idclie` int(11) NOT NULL,
  `numid` char(50) NOT NULL,
  `nomcli` text NOT NULL,
  `apecli` text NOT NULL,
  `naci` date NOT NULL DEFAULT '1900-01-01',
  `correo` text NOT NULL,
  `celu` char(10) NOT NULL,
  `estad` varchar(15) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp(),
  `dircli` text DEFAULT NULL,
  `ciucli` text DEFAULT NULL,
  `idsede` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` (`idclie`, `numid`, `nomcli`, `apecli`, `naci`, `correo`, `celu`, `estad`, `fere`, `dircli`, `ciucli`, `idsede`) VALUES
(20, '12345678', 'Juan', 'Perez', '1990-01-01', 'juan@correo.com', '3001234567', 'Inactivo', '2025-09-19 15:13:30', 'Calle 1 #2-3', 'Bogotá', 'Medellin'),
(21, '87654321', 'Maria', 'Garay', '1985-05-15', 'maria@correo.com', '3009876543', 'Activo', '2025-09-19 15:13:30', 'Carrera 5 #10-20', 'Bogotá', 'Unilago'),

CREATE TABLE `compra` (
  `idcomp` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `method` text NOT NULL,
  `total_products` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `placed_on` text NOT NULL,
  `payment_status` text NOT NULL,
  `tipc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `despachos` (
  `id` int(11) NOT NULL,
  `orden_id` int(11) NOT NULL,
  `fecha_despacho` timestamp NULL DEFAULT current_timestamp(),
  `responsable` varchar(100) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gastos` (
  `idga` int(11) NOT NULL,
  `detall` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ingresos` (
  `iding` int(11) NOT NULL,
  `detalle` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ingresos` (`iding`, `detalle`, `total`, `fec`) VALUES
(23, 'VENTA DE PRODUCTOS - Orden #18', 0.00, '2025-09-24'),
(24, 'VENTA DE PRODUCTOS - Orden #19', 1600000.00, '2025-10-02');

CREATE TABLE `marketing` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `canal` varchar(50) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `gastos` decimal(12,2) DEFAULT 0.00,
  `ingresos` decimal(12,2) DEFAULT 0.00,
  `retorno_inversion` decimal(12,2) GENERATED ALWAYS AS (case when `gastos` > 0 then (`ingresos` - `gastos`) / `gastos` else NULL end) STORED,
  `responsable` varchar(100) DEFAULT NULL,
  `estado` enum('activa','finalizada','pendiente') DEFAULT 'pendiente',
  `fuente_datos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `idord` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_cli` int(11) NOT NULL,
  `method` text NOT NULL,
  `total_products` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `placed_on` text NOT NULL,
  `payment_status` text NOT NULL,
  `tipc` text NOT NULL,
  `despacho` varchar(255) DEFAULT NULL,
  `responsable` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`idord`, `user_id`, `user_cli`, `method`, `total_products`, `total_price`, `placed_on`, `payment_status`, `tipc`, `despacho`, `responsable`) VALUES
(18, 38, 23, 'Efectivo', '0', 0.00, '2025-09-24 22:36:23', 'Aceptado', '0', 'Pendiente', 'Jonathan Calderon'),
(19, 1, 22, 'Efectivo', '0', 1600000.00, '2025-10-02 09:31:56', 'Aceptado', '0', 'Pendiente', 'Frank Quiñonez Vidal'),
(20, 42, 23, 'sistecredito', '3', 3150000.00, '2025-10-17 20:51:31', 'Aceptado', 'Venta', 'Pendiente', 'Local Cucuta'),
(21, 42, 23, 'finanzacion', '1', 1100000.00, '2025-10-17 21:14:02', 'Aceptado', 'Venta', 'Pendiente', 'Local Cucuta');

CREATE TABLE `plan` (
  `idplan` int(11) NOT NULL,
  `foto` text NOT NULL,
  `nompla` text NOT NULL,
  `estp` varchar(15) NOT NULL,
  `prec` decimal(10,2) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `plan` (`idplan`, `foto`, `nompla`, `estp`, `prec`, `fere`) VALUES
(1, '515419.png', 'MANTENIMIENTO PREVENTIVO', 'Activo', 89500.00, '2024-03-15 13:27:45'),
(2, '767524.png', 'BORRADO SEGURO', 'Activo', 49500.00, '2024-03-15 13:27:46'),
(3, 'plan2.jpg', 'COMPONENTE', 'Activo', 99500.00, '2024-03-15 13:27:46'),
(4, '657987.jpg', 'REPARACION', 'Activo', 129000.00, '2024-03-31 13:27:46'),
(5, '997554.png', 'SERVICIO TECNICO', 'Activo', 6000.00, '2024-03-20 01:35:44'),
(6, '756730.png', 'reting', 'Activo', 90000.00, '2025-06-19 19:49:49');

CREATE TABLE `producto` (
  `idprod` int(11) NOT NULL,
  `codba` char(14) NOT NULL,
  `nomprd` text NOT NULL,
  `idcate` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `foto` text NOT NULL,
  `venci` date NOT NULL,
  `esta` varchar(15) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp(),
  `serial` varchar(50) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `ram` varchar(8) DEFAULT NULL,
  `disco` text DEFAULT NULL,
  `prcpro` varchar(8) DEFAULT NULL,
  `pntpro` varchar(8) DEFAULT NULL,
  `tarpro` varchar(30) DEFAULT NULL,
  `grado` enum('A','B','C','SCRAP','#N/D','','0') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `privado` int(11) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `celu` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `dire` varchar(250) DEFAULT NULL,
  `cuiprov` varchar(30) DEFAULT NULL,
  `nomenclatura` varchar(10) DEFAULT NULL,
  `nit` varchar(15) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `proveedores` (`id`, `privado`, `nombre`, `celu`, `correo`, `dire`, `cuiprov`, `nomenclatura`, `nit`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(8, 1, 'Ejemplo S.A.S.', '3202344974', 'ejemplo@email.com', 'Calle 123 #45-67', 'Bogota', 'EJEMSAS', '901234567', '2025-07-14 17:20:31', '2025-09-18 20:19:28'),
(9, 1, 'PcShek Tecnologia Y Servicios S A S', '3186890437', 'comercial@pcshek.com', 'TV 66 # 35 - 11 MD 3 BG 9', 'Bogota', 'PCSH', '900413420', '2025-07-14 17:20:31', '2025-07-14 17:20:31'),
(24, 1, 'COLSOF1', '315 7146 129', 'proteccion.datos@colsof.com.co', 'Vereda Vuelta Grande, Predio San Rafael, Zona Franca Metropolitana, Bodega, 55-56, Cota, Cundinamarca', '800015583', 'COLSOF1', NULL, '2025-09-26 19:42:50', '2025-09-26 19:43:39');

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL COMMENT 'FK a bodega_inventario.id',
  `usuario_id` int(11) NOT NULL COMMENT 'FK a usuarios.id (quien reserva)',
  `cliente_id` int(11) NOT NULL COMMENT 'FK a clientes.idclie (para quien se reserva)',
  `fecha_reserva` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_vencimiento` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('activa','vencida','completada','cancelada') NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla para gestionar reservas de equipos para la venta';

CREATE TABLE `reserva_venta` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'ID del comercial que crea la reserva',
  `cliente_id` int(11) NOT NULL,
  `fecha_reserva` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_vencimiento` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('activa','vencida','completada','cancelada') NOT NULL DEFAULT 'activa',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `servicio` (
  `idservc` int(11) NOT NULL,
  `idplan` int(11) NOT NULL,
  `ini` date NOT NULL,
  `fin` date DEFAULT NULL,
  `idclie` int(11) NOT NULL,
  `estod` varchar(15) NOT NULL,
  `meto` text NOT NULL,
  `canc` decimal(10,2) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp(),
  `servtxt` varchar(250) DEFAULT NULL,
  `servfoto` varchar(255) DEFAULT NULL,
  `responsable` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `setting` (
  `idsett` int(11) NOT NULL,
  `nomem` varchar(150) NOT NULL,
  `ruc` char(14) NOT NULL,
  `decrp` varchar(150) NOT NULL,
  `corr` varchar(250) NOT NULL,
  `direc1` varchar(250) NOT NULL,
  `direc2` varchar(250) NOT NULL,
  `celu` char(16) NOT NULL,
  `foto` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `setting` (`idsett`, `nomem`, `ruc`, `decrp`, `corr`, `direc1`, `direc2`, `celu`, `foto`) VALUES
(1, 'PCMARKETT SAS', '9012322738', 'Venta Computadores', 'pcmarkett2018@gmail.com', 'Cl. 14 #53-19, Bogotá, Colombia', 'CC Monterrey, Cra. 48 #10-45 Local 237, El Poblado, Medellín, Antioquia', '304 4177847', '239999.webp');


CREATE TABLE `solicitud_alistamiento` (
  `id` int(11) NOT NULL,
  `solicitante` varchar(255) NOT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que solicita',
  `sede` varchar(100) NOT NULL,
  `cliente` varchar(255) DEFAULT NULL,
`cantidad` varchar(1600) NOT NULL,
`descripcion` varchar(1600) NOT NULL,
`marca` varchar(100) DEFAULT NULL,
`modelo` varchar(100) DEFAULT NULL,
`observacion` varchar(1200) DEFAULT NULL,
  `tecnico_responsable` int(11) DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(500) NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `observacion_global` varchar(150) DEFAULT NULL,
  `observacion_tecnico` varchar(250) DEFAULT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `solicitud_alistamiento` (`id`, `solicitante`, `usuario_id`, `sede`, `cliente`, `cantidad`, `descripcion`, `marca`, `modelo`, `observacion`, `tecnico_responsable`, `fecha_solicitud`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `observacion_global`, `observacion_tecnico`) VALUES
(1, 'Local Cucuta', 42, 'Cúcuta', 'TEST carlos andres', '2', '2x torres corei 5 de 4ta', 'hp', 'elikbook 80', 'Despacho: Interrapidisimo Pte Aranda | Productos JSON: [{\"cantidad\":2,\"descripcion\":\"torres corei 5 de 4ta\",\"marca\":\"hp\",\"modelo\":\"elikbook 80\",\"observacion\":\"ojo cambiar ram por una de 16gb ojo ponerle un vinilo al costado color gris\"}]', 15, '2025-10-17 21:24:58', 'pendiente', '2025-10-17 21:24:58', '2025-10-21 14:45:41', 'Empacar bien', NULL),
(2, 'frank4', 31, 'Pagina Web', 'Testeo Solicitud de Alistamiento', '3', '3x latop i7 8th, ram 8gb, 256 sdd', 'lenovo', 'testeo', 'Despacho: Despacho Tienda Pte Aranda | Productos JSON: [{\"cantidad\":3,\"descripcion\":\"latop i7 8th, ram 8gb, 256 sdd\",\"marca\":\"lenovo\",\"modelo\":\"testeo\",\"observacion\":\"con progrmas\"}]', 15, '2025-10-20 16:12:45', 'pendiente', '2025-10-20 16:12:45', '2025-10-20 16:12:45', '', NULL),
(3, 'Stefany Ramirez ', 40, 'Principal - Puente Aranda', 'Alberto escobar', '1', '1x Cpu cire i7 7th 16gb 512ssd', 'dell', NULL, 'Despacho: Despacho Tienda Pte Aranda | Productos JSON: [{\"cantidad\":1,\"descripcion\":\"Cpu cire i7 7th 16gb 512ssd\",\"marca\":\"dell\",\"modelo\":\"\",\"observacion\":\"office, win 11 y cables\"}]', 15, '2025-10-20 16:24:56', 'entregado', '2025-10-20 16:24:56', '2025-10-21 14:25:32', 'office, win 11 y cables', NULL),

/*========  Tabla nueva de poreceso de venta con formato de pedido Segun solicitado con Andres  =================================*/
CREATE TABLE `aistamiento_venta`(
`idventa` int(11) not null,
`fecha_venta` datetime NOT NULL DEFAULT current_timestamp(),
`fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp(),
`solicitante` varchar(255) NOT NULL,
`usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que solicita',
`sede` varchar(150) Not null,
`idclien` varchar(150) not null,
`nit_cliente` varchar(150) not null comment `Es el dato con cual se busca en campol label, este dato cualquiera de estos  5 en la labla de clientes(numbid, nombrecli, apecli,correo,celu) en la visat aprevia de busqueda de cliente`,
`nomcliente` varchar(250) not null comment `Nota: trear informacion si o si de la tabla de "clientes" (clietes.nomcli) y (clientes.apecli)`,
`telcliente` varchar(150) not null COMMENT `traer el dato de la tabla de "clientes" segun corresponda el dato a traer`,
`canal_venta` varchar(150) not null comment `traer el dato de la tabla de "clientes" segun corresponda el dato a traer`,
`concepto_salida` varchar (150) not null comment `traer el dato de la tabla de "clientes" segun corresponda el dato a traer`,
`cantidad` varchar(1600) NOT NULL,
`marca` varchar(1600) DEFAULT NULL,
`modelo` varchar(2600) DEFAULT NULL,
`ram` varchar(2600) DEFAULT NULL,
`disco` varchar(2600) DEFAULT NULL,
`ubicacion` varchar(250) not null,
`descripcion` varchar(2600) NOT NULL comment `se auto rellena con la informacion que pondran en la casillas anteriores a ellas  tales como: (marca, modelo ram, disco, opcioon uno si nos lo pone el usaurio ingresado desde  label la informacio, y tambien exista opcion 2. o que se aparezca una venta o recuado o mini venta en la misma pestaña tipo PopUp, para buscar y selecionar lo disponible en bodega esta en la tabla "bodega_inventario" exista forma buscar por (tabla. "bodega_inventario" campos: {producto, marca, modelo, procesador, ram, disco, grado} es un label Input de busqueda), y  selecionar buscar por los siguiente atribustos que severa una vista previa (solo muestre los equipos "bodega_inventari.grado{'A', 'B'}" y tenga un estado "bodega_inventario.estado(activo)" y ademas supremamente importante("bodega_inventario.disposicion('en_proceso', 'en revision', 'en_diagnostico', 'Por Alistamiento',  mejor dicho cualquuier dispocion que no sera 'Vendido', que lo muestre)") )" estado()) [] )`,
`observacion` varchar(1200) DEFAULT NULL,
`precio_unitario` varchar(150) DEFAULT null,
`total_venta` varchar(1600) DEFAULT null comment `total de la venta, con la suma cantidades y, en resumen total de la venta`;
`ticket` varchar(160) not null comment `texto alfanumerio`,
`valor_abono` varchar(250) not null comment `cuanto abono el cliente`,
`medio_abono` varchar(250) not null comment `va ser una lista desplagable voy poner desde el frontEnd`,
`saldo` varchar(250) not null comment `cuanto queda de saldo`,
`medio_saldo` varchar(250) not null comment `va ser una lista desplagable voy poner desde el frontEnd es por que fue pagado es el metedo de pago utilizo por eso los voz hacer esa lista seleccionable desde el fontend, no en la base de datos`,
`numguia_envio` varchar(250) not null comment `numero de seguimiento del paquete , para que cuando tecnicos lo despachen luego, la comercial lo ponga ese numero de seguimiento`
`ruta_archivo` varchar(1600) NOT NULL COMMENT `solamente se guarda nombre del archivo mi ideal esque depediendo donde este el archivo se guarde la informacion aqui 'pcmteam\public_html\a_img' y va guardar varios nombresd e archivos en la en el mismo campo, como si fuera un json `,
`observacion_global` varchar(150) DEFAULT NULL,
`observacion_tecnico` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `correo` varchar(30) NOT NULL,
  `clave` text NOT NULL,
  `rol` char(1) NOT NULL,
  `foto` text DEFAULT NULL,
  `estado` char(1) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp(),
  `idsede` varchar(25) DEFAULT NULL,
  `cumple` varchar(45) DEFAULT NULL COMMENT 'Fecha de cumpleaños del usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `correo`, `clave`, `rol`, `foto`, `estado`, `fere`, `idsede`, `cumple`) VALUES
(1, 'Frank Quiñonez Vidal', 'frank', 'frank@admin.com', '202cb962ac59075b964b07152d234b70', '1', 'reere.webp', '1', '2025-05-29 00:48:15', 'Todo', ''),
(2, 'Cristhian Romero', 'CristhianRomeropc', 'cr123@data.com', '53c9051e332d17250009640d364414c4', '1', 'reere.webp', '1', '2025-05-29 08:37:54', 'Principal', NULL),
(3, 'Jasson Robles', 'Jassonroblespc', 'jr123@data.com', '75dbf8a92d4276fb51528da4e4a9d2c3', '1', 'reere.webp', '1', '2025-05-29 08:38:35', 'Principal', NULL),
(4, 'Andrés Buitrago', 'AndresBuitragopc', 'ab123@data.com', '4b812d068c142583012bfb70131a61ab', '1', 'reere.webp', '1', '2025-05-29 08:38:57', 'Principal', NULL),

CREATE TABLE `venta_detalles` (
  `id` int(11) NOT NULL,
  `orden_id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `serial` varchar(100) NOT NULL,
  `codigo_g` varchar(50) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `fecha_venta` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `venta_detalles` (`id`, `orden_id`, `inventario_id`, `serial`, `codigo_g`, `precio_unitario`, `fecha_venta`) VALUES
(4, 18, 106, 'TEST123456789', 'TEST001', 0.00, '2025-09-24 22:36:23'),


ALTER TABLE `bodega_asignaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_tecnico_inventario` (`tecnico_id`,`inventario_id`);

ALTER TABLE `bodega_cart_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `inventario_id` (`inventario_id`);

ALTER TABLE `bodega_compra`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bodega_control_calidad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `tecnico_id` (`tecnico_id`),
  ADD KEY `idx_cc_inventario` (`inventario_id`);

ALTER TABLE `bodega_diagnosticos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `tecnico_id` (`tecnico_id`),
  ADD KEY `idx_diag_inventario` (`inventario_id`);

ALTER TABLE `bodega_electrico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `tecnico_id` (`tecnico_id`);

ALTER TABLE `bodega_entradas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD KEY `usuario_id` (`usuario_id`);

ALTER TABLE `bodega_estetico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `tecnico_id` (`tecnico_id`);

ALTER TABLE `bodega_ingresos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orden_id` (`orden_id`);

ALTER TABLE `bodega_inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_g` (`codigo_g`),
  ADD UNIQUE KEY `serial` (`serial`);

ALTER TABLE `bodega_log_cambios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventario_id` (`inventario_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_fecha_cambio` (`fecha_cambio`),
  ADD KEY `idx_campo_modificado` (`campo_modificado`),
  ADD KEY `idx_inventario_fecha` (`inventario_id`,`fecha_cambio`),
  ADD KEY `idx_usuario_fecha` (`usuario_id`,`fecha_cambio`);

ALTER TABLE `bodega_mantenimiento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `tecnico_id` (`tecnico_id`),
  ADD KEY `idx_mant_inventario` (`inventario_id`),
  ADD KEY `idx_mant_tecnico` (`tecnico_id`);

ALTER TABLE `bodega_ordenes`
  ADD PRIMARY KEY (`idord`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `responsable` (`responsable`);

ALTER TABLE `bodega_partes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_partes_referencia` (`referencia`);

ALTER TABLE `bodega_salidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_salida_inventario` (`inventario_id`),
  ADD KEY `fk_salida_cliente` (`cliente_id`),
  ADD KEY `fk_salida_tecnico` (`tecnico_id`),
  ADD KEY `fk_salida_usuario` (`usuario_id`),
  ADD KEY `fk_salida_orden` (`orden_id`);

ALTER TABLE `bodega_solicitud_parte`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cart`
  ADD PRIMARY KEY (`idv`);

ALTER TABLE `cart_compra`
  ADD PRIMARY KEY (`idcarco`);

ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idcate`);

ALTER TABLE `clientes`
  ADD PRIMARY KEY (`idclie`);

ALTER TABLE `compra`
  ADD PRIMARY KEY (`idcomp`);

ALTER TABLE `despachos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orden_id` (`orden_id`);

ALTER TABLE `gastos`
  ADD PRIMARY KEY (`idga`);

ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`iding`);

ALTER TABLE `marketing`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`idord`);

ALTER TABLE `plan`
  ADD UNIQUE KEY `idplan` (`idplan`);

ALTER TABLE `producto`
  ADD PRIMARY KEY (`idprod`);

ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nit_unique` (`nit`);

ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventario_id` (`inventario_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_cliente_id` (`cliente_id`);

ALTER TABLE `reserva_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventario` (`inventario_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_vencimiento` (`fecha_vencimiento`);

ALTER TABLE `servicio`
  ADD PRIMARY KEY (`idservc`);

ALTER TABLE `setting`
  ADD PRIMARY KEY (`idsett`);

ALTER TABLE `solicitud_alistamiento`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orden_id` (`orden_id`),
  ADD KEY `idx_inventario_id` (`inventario_id`);


ALTER TABLE `bodega_cart_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_control_calidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `bodega_diagnosticos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

ALTER TABLE `bodega_electrico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `bodega_entradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

ALTER TABLE `bodega_estetico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `bodega_ingresos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=307;

ALTER TABLE `bodega_log_cambios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

ALTER TABLE `bodega_mantenimiento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

ALTER TABLE `bodega_ordenes`
  MODIFY `idord` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `bodega_partes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `bodega_salidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

ALTER TABLE `bodega_solicitud_parte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `cart`
  MODIFY `idv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `cart_compra`
  MODIFY `idcarco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `categoria`
  MODIFY `idcate` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `clientes`
  MODIFY `idclie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

ALTER TABLE `compra`
  MODIFY `idcomp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `despachos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `gastos`
  MODIFY `idga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `ingresos`
  MODIFY `iding` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

ALTER TABLE `marketing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
  MODIFY `idord` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

ALTER TABLE `plan`
  MODIFY `idplan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `producto`
  MODIFY `idprod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `reserva_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `servicio`
  MODIFY `idservc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `setting`
  MODIFY `idsett` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `solicitud_alistamiento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

ALTER TABLE `venta_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;


ALTER TABLE `bodega_salidas`
  ADD CONSTRAINT `fk_salida_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`idclie`),
  ADD CONSTRAINT `fk_salida_inventario` FOREIGN KEY (`inventario_id`) REFERENCES `bodega_inventario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_salida_orden` FOREIGN KEY (`orden_id`) REFERENCES `bodega_ordenes` (`idord`),
  ADD CONSTRAINT `fk_salida_tecnico` FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_salida_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reserva_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`idclie`),
  ADD CONSTRAINT `fk_reserva_inventario` FOREIGN KEY (`inventario_id`) REFERENCES `bodega_inventario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reserva_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
