SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `bodega_asignaciones` (
  `id` int NOT NULL,
  `tecnico_id` int NOT NULL,
  `inventario_id` int NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `bodega_cart_compra` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `inventario_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `precio_unit` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) GENERATED ALWAYS AS ((`qty` * ifnull(`precio_unit`,0))) STORED,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_compra` (
  `id` int NOT NULL,
  `proveedor_id` int DEFAULT NULL,
  `usuario_id` int NOT NULL,
  `total_compra` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fecha_compra` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('pendiente','recibido','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `tipo_doc` enum('factura','remision','otro') COLLATE utf8mb4_unicode_ci DEFAULT 'factura',
  `referencia` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evidencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_control_calidad` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_control` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tecnico_id` int NOT NULL COMMENT 'ID del técnico que realiza el control',
  `burning_test` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Resultado de Burning Test',
  `sentinel_test` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Resultado de Sentinel',
  `estado_final` enum('aprobado','rechazado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria_rec` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Categorización REC',
  `observaciones` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_control_calidad` (`id`, `inventario_id`, `fecha_control`, `tecnico_id`, `burning_test`, `sentinel_test`, `estado_final`, `categoria_rec`, `observaciones`) VALUES
(8, 57, '2025-09-19 09:27:14', 1, 'bien', 'bien', 'aprobado', 'REC-A', 'n/d'),
(9, 109, '2025-09-29 10:05:58', 1, 'Super, ligero sobrecalentamiento', 'vida del disco al 98||', 'aprobado', 'REC-A', 'Esta lisot para la venta'),
(10, 125, '2025-10-02 11:41:12', 33, 'todo okay', 'todo okay', 'aprobado', 'REC-A', 'todo OKAY en control de calidad');

CREATE TABLE `bodega_diagnosticos` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_diagnostico` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tecnico_id` int NOT NULL COMMENT 'ID del técnico que realiza el diagnóstico',
  `camara` text COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de cámara',
  `teclado` text COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de teclado',
  `parlantes` text COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de audio',
  `bateria` text COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de batería',
  `microfono` text COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de micrófono',
  `pantalla` text COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de pantalla',
  `puertos` text COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de puertos',
  `disco` text COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de disco',
  `falla_electrica` enum('si','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `detalle_falla_electrica` text COLLATE utf8mb4_unicode_ci,
  `falla_estetica` enum('si','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `detalle_falla_estetica` text COLLATE utf8mb4_unicode_ci,
  `estado_reparacion` enum('falla_mecanica','falla_electrica','reparacion_cosmetica','aprobado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_diagnosticos` (`id`, `inventario_id`, `fecha_diagnostico`, `tecnico_id`, `camara`, `teclado`, `parlantes`, `bateria`, `microfono`, `pantalla`, `puertos`, `disco`, `falla_electrica`, `detalle_falla_electrica`, `falla_estetica`, `detalle_falla_estetica`, `estado_reparacion`, `observaciones`) VALUES
(60, 57, '2025-09-18 15:58:36', 1, 'MALO', 'N/D', 'BUENO', 'BUENO', 'MALO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"MALO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 100', 'si', 'Puerto USB dañado', 'si', 'rayones', 'aprobado', 'N/D'),
(61, 57, '2025-09-19 09:18:27', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: %', 'no', NULL, 'no', NULL, 'aprobado', ''),
(62, 59, '2025-09-23 16:50:03', 36, 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 76', 'no', '', 'si', 'una tecla pelada (a)', 'falla_electrica', 'no sirve parlante'),
(63, 59, '2025-09-23 16:56:13', 36, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: %', 'no', NULL, 'no', NULL, 'falla_electrica', 'falla padmaouse'),
(64, 109, '2025-09-24 15:04:59', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 100', 'si', 'falla', 'si', 'falla', 'aprobado', 'testando ando'),
(65, 109, '2025-09-24 15:05:05', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 100', 'si', 'falla', 'si', 'falla', 'aprobado', 'testando ando'),
(66, 227, '2025-09-27 12:24:03', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"MALO\",\"HDMI\":\"BUENO\",\"USB\":\"MALO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 98', 'si', 'limpieza¿Presenta falla eléctrica?', 'si', 'Fallas Estéticas', 'aprobado', 'observacion Gobal'),
(67, 122, '2025-09-27 12:24:03', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"MALO\",\"HDMI\":\"BUENO\",\"USB\":\"MALO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 98', 'si', 'limpieza¿Presenta falla eléctrica?', 'si', 'Fallas Estéticas', 'aprobado', 'observacion Gobal'),
(68, 131, '2025-09-27 12:24:03', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"MALO\",\"HDMI\":\"BUENO\",\"USB\":\"MALO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 98', 'si', 'limpieza¿Presenta falla eléctrica?', 'si', 'Fallas Estéticas', 'aprobado', 'observacion Gobal'),
(69, 134, '2025-09-27 12:24:03', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"MALO\",\"HDMI\":\"BUENO\",\"USB\":\"MALO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 98', 'si', 'limpieza¿Presenta falla eléctrica?', 'si', 'Fallas Estéticas', 'aprobado', 'observacion Gobal'),
(70, 150, '2025-09-27 12:24:03', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"MALO\",\"HDMI\":\"BUENO\",\"USB\":\"MALO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 98', 'si', 'limpieza¿Presenta falla eléctrica?', 'si', 'Fallas Estéticas', 'aprobado', 'observacion Gobal'),
(71, 156, '2025-09-27 12:24:03', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"MALO\",\"HDMI\":\"BUENO\",\"USB\":\"MALO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 98', 'si', 'limpieza¿Presenta falla eléctrica?', 'si', 'Fallas Estéticas', 'aprobado', 'observacion Gobal'),
(72, 127, '2025-09-29 17:45:46', 1, 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', 'BUENO', '{\"VGA\":\"BUENO\",\"DVI\":\"BUENO\",\"HDMI\":\"BUENO\",\"USB\":\"MALO\",\"Red\":\"BUENO\"}', 'Estado: N/D; Vida útil: 78', 'si', 'fallo 1 Electronico puerto usb balsatados', 'si', '2 Detalle de la falla estética', 'aprobado', 'AAAAAAA observacioens'),
(73, 227, '2025-10-02 10:01:30', 1, 'BUENO', 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 95', 'si', 'Fallo eléctrico Puerto sulfatado', 'si', 'Esquinas de portátiles para reconstrucción, y tapas talladas', 'aprobado', 'sobre calentamiento'),
(74, 127, '2025-10-02 10:01:30', 1, 'BUENO', 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 95', 'si', 'Fallo eléctrico Puerto sulfatado', 'si', 'Esquinas de portátiles para reconstrucción, y tapas talladas', 'aprobado', 'sobre calentamiento'),
(75, 166, '2025-10-02 10:01:30', 1, 'BUENO', 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 95', 'si', 'Fallo eléctrico Puerto sulfatado', 'si', 'Esquinas de portátiles para reconstrucción, y tapas talladas', 'aprobado', 'sobre calentamiento'),
(76, 122, '2025-10-02 10:01:30', 1, 'BUENO', 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 95', 'si', 'Fallo eléctrico Puerto sulfatado', 'si', 'Esquinas de portátiles para reconstrucción, y tapas talladas', 'aprobado', 'sobre calentamiento'),
(77, 125, '2025-10-02 10:01:30', 1, 'BUENO', 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 95', 'si', 'Fallo eléctrico Puerto sulfatado', 'si', 'Esquinas de portátiles para reconstrucción, y tapas talladas', 'aprobado', 'sobre calentamiento'),
(78, 141, '2025-10-02 10:01:30', 1, 'BUENO', 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 95', 'si', 'Fallo eléctrico Puerto sulfatado', 'si', 'Esquinas de portátiles para reconstrucción, y tapas talladas', 'aprobado', 'sobre calentamiento'),
(79, 143, '2025-10-02 10:01:30', 1, 'BUENO', 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 95', 'si', 'Fallo eléctrico Puerto sulfatado', 'si', 'Esquinas de portátiles para reconstrucción, y tapas talladas', 'aprobado', 'sobre calentamiento'),
(80, 144, '2025-10-02 10:01:30', 1, 'BUENO', 'BUENO', 'BUENO', 'MALO', 'BUENO', 'BUENO', '{\"VGA\":\"N\\/D\",\"DVI\":\"N\\/D\",\"HDMI\":\"BUENO\",\"USB\":\"BUENO\",\"Red\":\"N\\/D\"}', 'Estado: N/D; Vida útil: 95', 'si', 'Fallo eléctrico Puerto sulfatado', 'si', 'Esquinas de portátiles para reconstrucción, y tapas talladas', 'aprobado', 'sobre calentamiento');

CREATE TABLE `bodega_electrico` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL,
  `fecha_proceso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tecnico_id` int NOT NULL,
  `estado_bateria` text COLLATE utf8mb4_unicode_ci,
  `estado_fuente` text COLLATE utf8mb4_unicode_ci,
  `estado_puertos` text COLLATE utf8mb4_unicode_ci,
  `estado_pantalla` text COLLATE utf8mb4_unicode_ci,
  `estado_teclado` text COLLATE utf8mb4_unicode_ci,
  `estado_audio` text COLLATE utf8mb4_unicode_ci,
  `fallas_detectadas` text COLLATE utf8mb4_unicode_ci,
  `reparaciones_realizadas` text COLLATE utf8mb4_unicode_ci,
  `estado_final` enum('aprobado','rechazado','requiere_revision') COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_electrico` (`id`, `inventario_id`, `fecha_proceso`, `tecnico_id`, `estado_bateria`, `estado_fuente`, `estado_puertos`, `estado_pantalla`, `estado_teclado`, `estado_audio`, `fallas_detectadas`, `reparaciones_realizadas`, `estado_final`, `observaciones`) VALUES
(5, 57, '2025-09-19 09:23:02', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'Reparado, Puerto USB lateral ', 'Reparado, Puerto USB lateral ', 'aprobado', 'Todo bien'),
(6, 57, '2025-09-19 09:23:19', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'Reparado, Puerto USB lateral ', 'Reparado, Puerto USB lateral ', 'aprobado', 'Todo bien'),
(7, 109, '2025-09-29 09:38:19', 1, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'Reparacion de puertos sulfatados', 'Cambio de puertos \r\nReconstrucion de posamanos', 'aprobado', 'Rayones en tapa superior'),
(8, 122, '2025-09-29 10:35:24', 1, 'REGULAR', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'REGULAR', 'falla 1 ', 'falla 2 Reparacion', 'rechazado', 'revision'),
(9, 122, '2025-09-29 10:36:54', 1, 'REGULAR', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'REGULAR', 'falla 1 ', 'falla 2 Reparacion', 'rechazado', 'revision'),
(10, 122, '2025-09-29 10:37:59', 1, 'REGULAR', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'REGULAR', 'falla 1 ', 'falla 2 Reparacion', 'rechazado', 'revision'),
(11, 122, '2025-09-29 10:38:01', 1, 'REGULAR', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'REGULAR', 'falla 1 ', 'falla 2 Reparacion', 'rechazado', 'revision'),
(12, 122, '2025-09-29 10:38:01', 1, 'REGULAR', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'REGULAR', 'falla 1 ', 'falla 2 Reparacion', 'rechazado', 'revision'),
(13, 125, '2025-10-02 11:37:38', 33, 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'BUENO', 'Se reparo los puertos sulfatados, \r\nPin de carga doblado', 'reparacion de pin de carga', 'aprobado', '');

CREATE TABLE `bodega_entradas` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_entrada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `proveedor_id` int NOT NULL COMMENT 'ID del proveedor',
  `usuario_id` int NOT NULL COMMENT 'ID del usuario que registra',
  `cantidad` int NOT NULL DEFAULT '1',
  `observaciones` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_entradas` (`id`, `inventario_id`, `fecha_entrada`, `proveedor_id`, `usuario_id`, `cantidad`, `observaciones`) VALUES
(28, 56, '2025-09-18 15:29:33', 1, 1, 1, 'Prueba de inserción'),
(29, 57, '2025-09-18 15:30:37', 8, 1, 1, 'lista'),
(30, 58, '2025-09-23 14:47:11', 1, 1, 1, 'Prueba de inserción'),
(31, 59, '2025-09-23 15:23:33', 1, 1, 1, 'Prueba de inserción'),
(32, 105, '2025-09-23 19:36:40', 1, 1, 1, 'Prueba de inserción'),
(33, 106, '2025-09-23 15:40:49', 1, 1, 1, 'Prueba de inserción'),
(34, 107, '2025-09-24 11:50:16', 1, 1, 1, 'Prueba de inserción'),
(35, 108, '2025-09-24 12:00:35', 1, 1, 1, 'Prueba de inserción'),
(36, 109, '2025-09-24 12:03:40', 8, 1, 1, 'rayones'),
(40, 116, '2025-09-26 15:32:37', 24, 1, 1, 'Importado desde Excel - Fila 2'),
(41, 227, '2025-09-27 10:35:03', 8, 1, 1, 'n/d'),
(42, 228, '2025-10-04 10:19:12', 1, 1, 1, 'Importado desde Excel - Fila 2'),
(43, 229, '2025-10-04 10:19:12', 1, 1, 1, 'Importado desde Excel - Fila 3'),
(44, 357, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2025'),
(45, 358, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2026'),
(46, 359, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2027'),
(47, 360, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2028'),
(48, 361, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2029'),
(49, 362, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2030'),
(50, 363, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2031'),
(51, 364, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2032'),
(52, 365, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2033'),
(53, 366, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2034'),
(54, 367, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2035'),
(55, 368, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2036'),
(56, 369, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2037'),
(57, 370, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2038'),
(58, 371, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2039'),
(59, 372, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2040'),
(60, 373, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2041'),
(61, 374, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2042'),
(62, 375, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2043'),
(63, 376, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2044'),
(64, 377, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2045'),
(103, 416, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2084'),
(104, 417, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2085'),
(105, 418, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2086'),
(106, 419, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2087'),
(107, 420, '2025-10-09 15:37:46', 1, 1, 1, 'Importado desde Excel - Lote: medellin9_octubre2088'),
(171, 421, '2025-10-09 16:59:40', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - PANTALLA TOUCH'),
(172, 422, '2025-10-09 16:59:40', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - n/d'),
(173, 423, '2025-10-09 16:59:40', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - PANTALLA TOUCH'),
(174, 424, '2025-10-09 16:59:40', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - n/d'),
(183, 433, '2025-10-09 16:59:40', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - n/d'),
(184, 434, '2025-10-09 16:59:40', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - n/d'),
(185, 435, '2025-10-09 16:59:40', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - n/d'),
(186, 436, '2025-10-09 16:59:40', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - n/d'),
(187, 437, '2025-10-09 16:59:40', 1, 1, 1, 'Importado desde Excel - Lote: CUCUTA9_octubre2026 - n/d');

CREATE TABLE `bodega_estetico` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL,
  `fecha_proceso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tecnico_id` int NOT NULL,
  `estado_carcasa` text COLLATE utf8mb4_unicode_ci,
  `estado_pantalla_fisica` text COLLATE utf8mb4_unicode_ci,
  `estado_teclado_fisico` text COLLATE utf8mb4_unicode_ci,
  `rayones_golpes` text COLLATE utf8mb4_unicode_ci,
  `limpieza_realizada` enum('si','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `partes_reemplazadas` text COLLATE utf8mb4_unicode_ci,
  `grado_asignado` enum('A','B','C','SCRAP') COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_final` enum('aprobado','rechazado','requiere_revision') COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_estetico` (`id`, `inventario_id`, `fecha_proceso`, `tecnico_id`, `estado_carcasa`, `estado_pantalla_fisica`, `estado_teclado_fisico`, `rayones_golpes`, `limpieza_realizada`, `partes_reemplazadas`, `grado_asignado`, `estado_final`, `observaciones`) VALUES
(11, 57, '2025-09-19 09:25:29', 1, 'EXCELENTE', 'EXCELENTE', 'EXCELENTE', 'n/d', 'si', 'n/d', 'A', 'aprobado', ''),
(12, 109, '2025-09-29 10:04:37', 1, 'EXCELENTE', 'EXCELENTE', 'BUENO', 'super', 'si', 'ninguna', 'A', 'aprobado', 'listo Observaciones Adicionales'),
(13, 125, '2025-10-02 11:38:34', 33, 'EXCELENTE', 'EXCELENTE', 'EXCELENTE', 'todo Okay,', 'si', '', 'A', 'aprobado', 'Reparacion de pieza'),
(14, 125, '2025-10-02 11:39:25', 33, 'EXCELENTE', 'EXCELENTE', 'EXCELENTE', 'todo Okay,', 'si', '', 'A', 'aprobado', 'Reparacion de pieza');

CREATE TABLE `bodega_ingresos` (
  `id` int NOT NULL,
  `orden_id` int NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha_ingreso` datetime DEFAULT CURRENT_TIMESTAMP,
  `metodo_pago` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_pago` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recibido_por` int DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_inventario` (
  `id` int NOT NULL,
  `codigo_g` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código general del equipo',
  `ubicacion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Zona específica en bodega/laboratorio',
  `posicion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Posición exacta dentro de la ubicación',
  `fecha_ingreso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `producto` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de producto (Laptop, Desktop, Monitor, AIO, etc.)',
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Marca del equipo',
  `serial` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de serie del fabricante',
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Modelo o Referencia del equipo',
  `procesador` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Especificaciones del procesador',
  `ram` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Memoria RAM instalada',
  `disco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipo y capacidad del disco',
  `pulgadas` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tamaño de pantalla',
  `observaciones` text COLLATE utf8mb4_unicode_ci COMMENT 'Notas técnicas y observaciones',
  `grado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disposicion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Estado actual del equipo en el proceso',
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `tecnico_id` int DEFAULT NULL,
  `pedido_id` int DEFAULT NULL,
  `producto_id` int DEFAULT NULL,
  `tactil` text COLLATE utf8mb4_unicode_ci,
  `lote` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo_fijo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `foto` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_inventario` (`id`, `codigo_g`, `ubicacion`, `posicion`, `fecha_ingreso`, `fecha_modificacion`, `producto`, `marca`, `serial`, `modelo`, `procesador`, `ram`, `disco`, `pulgadas`, `observaciones`, `grado`, `disposicion`, `estado`, `tecnico_id`, `pedido_id`, `producto_id`, `tactil`, `lote`, `activo_fijo`, `precio`, `foto`) VALUES
(108, 'TEST001', 'Principal', 'ESTANTE-TEST', '2025-09-24 12:00:35', '2025-10-06 14:26:33', 'Portatil', 'Dell', 'TEST123456789', 'Test Model', 'Intel Test', '8GB', '256GB SSD', '15.6', 'Equipo de prueba', 'A', 'en_proceso', 'activo', 13, NULL, NULL, 'NO', 'TEST-LOTE-001', NULL, 0.00, NULL),
(109, 'EQ002', 'Principal', 'recibido_para_garantia', '2025-09-24 12:03:40', '2025-10-01 16:47:03', 'Desktop', 'Dell', 'HP987654321', 'EliteDesk 800', 'Intel i5-1135G7', '4GB', '512GB SSD', '16', 'rayones', 'B', 'Vendido', 'activo', 1, NULL, NULL, 'NO', 'pchekt542007-25', NULL, 0.00, NULL),
(116, 'EQ032', 'Cucuta', 'De_vuelto_garantia', '2025-09-26 15:32:37', '2025-10-01 16:15:13', 'Portatil', 'Dell', 'DL123456789', 'Latitude 5520', 'Intel i5-1135G7', '8GB', '256GB SSD', '15.6', 'Equipo en buen estado', 'A', 'En revisión', 'activo', 13, NULL, NULL, 'NO', 'LOTE-2025-01', NULL, 0.00, NULL),
(117, 'CLLPT1-5-198', 'Principal', 'ESTANTE-1-A', '2025-09-26 15:48:58', '2025-10-06 14:25:08', 'Portatil', 'Lenovo', 'PF1TKJVS', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_proceso', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(118, 'CLLPT1-5-202', 'Principal', 'ESTANTE-1-B', '2025-09-26 15:48:58', '2025-10-06 14:25:08', 'Portatil', 'Lenovo', 'PF1TKGL7', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_proceso', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(119, 'CLLPT1-5-204', 'Unilago', 'Recibido', '2025-09-26 15:48:58', '2025-10-01 16:48:33', 'Portatil', 'Lenovo', 'PF1TKC3S', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_diagnostico', 'activo', 8, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(120, 'CLLPT1-5-205', 'Principal', 'ESTANTE-1-D', '2025-09-26 15:48:58', '2025-10-06 14:26:12', 'Portatil', 'Lenovo', 'PF290E91', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_proceso', 'activo', 13, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(121, 'CLLPT1-5-207', 'Principal', 'ESTANTE-1-E', '2025-09-26 15:48:58', '2025-10-06 14:25:07', 'Portatil', 'Lenovo', 'PF290C50', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_proceso', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(122, 'CLLPT1-5-221', 'Principal', 'ESTANTE-2-A', '2025-09-26 15:48:58', '2025-09-29 10:38:01', 'Portatil', 'Lenovo', 'PF2BB7XR', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_revision', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(123, 'CLLPT1-5-222', 'Principal', 'ESTANTE-2-B', '2025-09-26 15:48:58', '2025-10-06 14:25:08', 'Portatil', 'Lenovo', 'PF2ASHL1', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_proceso', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(124, 'CLLPT1-5-223', 'Principal', 'ESTANTE-2-C', '2025-09-26 15:48:58', '2025-09-27 09:56:57', 'Portatil', 'Lenovo', 'PF2BB7V3', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_diagnostico', 'activo', 8, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(125, 'CLLPT1-5-228', 'Principal', 'Traslado', '2025-09-26 15:48:58', '2025-10-09 10:02:54', 'Portatil', 'Lenovo', 'PF2ASHKP', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '32GB', '512 SSD', '13.3', 'N/A', 'B', 'Vendido', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 1400000.00, NULL),
(126, 'CLLPT1-5-231', 'Principal', 'ESTANTE-2-E', '2025-09-26 15:48:58', '2025-09-27 10:49:10', 'Portatil', 'Lenovo', 'PF2BB9YB', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_diagnostico', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(127, 'CLLPT1-5-239', 'Principal', 'ESTANTE-3-A', '2025-09-26 15:48:58', '2025-09-27 10:49:10', 'Portatil', 'Lenovo', 'PF1TKM75', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_diagnostico', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(128, 'CLLPT1-5-241', 'Principal', 'ESTANTE-3-B', '2025-09-26 15:48:58', '2025-09-27 10:49:10', 'Portatil', 'Lenovo', 'PF1TKEDP', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_diagnostico', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(129, 'CLLPT1-5-246', 'Principal', 'ESTANTE-3-C', '2025-09-26 15:48:58', '2025-09-27 11:10:44', 'Portatil', 'Lenovo', 'PF1TKGNW', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_proceso', 'activo', 33, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(130, 'CLLPT1-5-249', 'Medellin', 'Traslado', '2025-09-26 15:48:58', '2025-10-02 15:43:13', 'Portatil', 'Lenovo', 'PF1TKJWX', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_diagnostico', 'activo', 36, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(131, 'CLLPT1-5-251', 'Principal', 'ESTANTE-3-E', '2025-09-26 15:48:58', '2025-09-27 10:49:10', 'Portatil', 'Lenovo', 'PF290C4R', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_diagnostico', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(132, 'CLLPT1-5-255', 'Principal', 'ESTANTE-4-A', '2025-09-26 15:48:58', '2025-09-27 10:49:10', 'Portatil', 'Lenovo', 'PF290HVC', 'ThinkPad X1 Yoga 4th Gen', 'I7 8TH', '16GB', '512 SSD', '13.3', 'N/A', 'B', 'en_diagnostico', 'activo', 1, NULL, NULL, 'SI', 'COLSOF1', NULL, 0.00, NULL),
(430, 'n_d_cucuta_oct25_10', 'Cucuta', 'Recibido', '2025-10-09 16:59:40', '2025-10-09 16:59:40', 'Desktop', 'Lenovo', 'MJPVZ17', 'M82', 'CELERON', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(431, 'n_d_cucuta_oct25_11', 'Cucuta', 'Recibido', '2025-10-09 16:59:40', '2025-10-09 16:59:40', 'Desktop', 'Lenovo', 'MJ040V2Z', 'M93', 'AMD A8', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(432, 'n_d_cucuta_oct25_12', 'Cucuta', 'Recibido', '2025-10-09 16:59:40', '2025-10-09 16:59:40', 'Desktop', 'Lenovo', 'MJPVE73', 'M90', 'CELERON', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(433, 'n_d_cucuta_oct25_13', 'Cucuta', 'Recibido', '2025-10-09 16:59:40', '2025-10-09 16:59:40', 'Desktop', 'Lenovo', 'MJLGW78', 'M92', 'CELERON', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(434, 'n_d_cucuta_oct25_14', 'Cucuta', 'Recibido', '2025-10-09 16:59:40', '2025-10-09 16:59:40', 'Desktop', 'Lenovo', 'MJ13622', 'M77', 'AMD A8', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(435, 'n_d_cucuta_oct25_15', 'Cucuta', 'Recibido', '2025-10-09 16:59:40', '2025-10-09 16:59:40', 'Desktop', 'Lenovo', 'MJPWN50', 'M77', 'AMD A8', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(436, 'n_d_cucuta_oct25_16', 'Cucuta', 'Recibido', '2025-10-09 16:59:40', '2025-10-09 16:59:40', 'Desktop', 'Dell', 'D8SQDF1', 'VOSTRO 200', 'PENTIUM', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL),
(437, 'n_d_cucuta_oct25_17', 'Cucuta', 'Recibido', '2025-10-09 16:59:40', '2025-10-09 16:59:40', 'Desktop', 'Lenovo', 'PB44R6P', 'M77', 'AMD A8', '4GB', 'SIN DISCO', 'SFF', 'n/d', 'B', 'Para Venta', 'activo', NULL, NULL, NULL, 'NO', 'CUCUTA9_octubre2026', NULL, 0.00, NULL);

CREATE TABLE `bodega_log_cambios` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL COMMENT 'ID del equipo en inventario',
  `usuario_id` int NOT NULL COMMENT 'ID del usuario que realizó el cambio',
  `fecha_cambio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del cambio',
  `campo_modificado` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del campo modificado',
  `valor_anterior` text COLLATE utf8mb4_unicode_ci COMMENT 'Valor anterior del campo',
  `valor_nuevo` text COLLATE utf8mb4_unicode_ci COMMENT 'Nuevo valor del campo',
  `tipo_cambio` enum('edicion_manual','importacion','sistema') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'edicion_manual' COMMENT 'Tipo de cambio realizado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de cambios realizados en equipos del inventario';

INSERT INTO `bodega_log_cambios` (`id`, `inventario_id`, `usuario_id`, `fecha_cambio`, `campo_modificado`, `valor_anterior`, `valor_nuevo`, `tipo_cambio`) VALUES
(29, 57, 1, '2025-09-19 09:23:02', 'disposicion_electrico', 'en_mantenimiento', 'pendiente_estetico', 'edicion_manual'),
(30, 57, 1, '2025-09-19 09:23:19', 'disposicion_electrico', 'en_mantenimiento', 'pendiente_estetico', 'edicion_manual'),
(45, 125, 33, '2025-10-02 11:41:12', 'disposicion', 'pendiente_control_calidad', 'Para Venta', 'edicion_manual'),
(46, 125, 1, '2025-10-09 10:02:54', 'disposicion', 'Para Venta', 'Vendido', 'sistema'),
(47, 375, 1, '2025-10-10 12:46:23', 'precio', NULL, '1230000', 'edicion_manual'),
(48, 375, 1, '2025-10-10 12:46:52', 'precio', NULL, '1200032', 'edicion_manual'),
(49, 375, 1, '2025-10-10 13:57:03', 'precio', NULL, '1950000', 'edicion_manual'),
(50, 375, 1, '2025-10-10 13:59:23', 'precio', NULL, '992300', 'edicion_manual');

CREATE TABLE `bodega_mantenimiento` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tecnico_id` int DEFAULT NULL,
  `usuario_registro` int DEFAULT NULL,
  `estado` enum('pendiente','realizado','rechazado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `tipo_proceso` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `falla_electrica` enum('si','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `detalle_falla_electrica` text COLLATE utf8mb4_unicode_ci,
  `falla_estetica` enum('si','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `detalle_falla_estetica` text COLLATE utf8mb4_unicode_ci,
  `partes_solicitadas` text COLLATE utf8mb4_unicode_ci,
  `referencia_externa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tecnico_diagnostico` int DEFAULT NULL,
  `limpieza_electronico` enum('pendiente','realizada','no_aplica') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `observaciones_limpieza_electronico` text COLLATE utf8mb4_unicode_ci,
  `mantenimiento_crema_disciplinaria` enum('pendiente','realizada','no_aplica') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `observaciones_mantenimiento_crema` text COLLATE utf8mb4_unicode_ci,
  `mantenimiento_partes` enum('pendiente','realizada','no_aplica') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `cambio_piezas` enum('si','no') COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `piezas_solicitadas_cambiadas` text COLLATE utf8mb4_unicode_ci,
  `proceso_reconstruccion` enum('si','no') COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `parte_reconstruida` text COLLATE utf8mb4_unicode_ci,
  `limpieza_general` enum('pendiente','realizada','no_aplica') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `remite_otra_area` enum('si','no') COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `area_remite` text COLLATE utf8mb4_unicode_ci,
  `proceso_electronico` text COLLATE utf8mb4_unicode_ci,
  `observaciones_globales` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_mantenimiento` (`id`, `inventario_id`, `fecha_registro`, `tecnico_id`, `usuario_registro`, `estado`, `tipo_proceso`, `observaciones`, `falla_electrica`, `detalle_falla_electrica`, `falla_estetica`, `detalle_falla_estetica`, `partes_solicitadas`, `referencia_externa`, `tecnico_diagnostico`, `limpieza_electronico`, `observaciones_limpieza_electronico`, `mantenimiento_crema_disciplinaria`, `observaciones_mantenimiento_crema`, `mantenimiento_partes`, `cambio_piezas`, `piezas_solicitadas_cambiadas`, `proceso_reconstruccion`, `parte_reconstruida`, `limpieza_general`, `remite_otra_area`, `area_remite`, `proceso_electronico`, `observaciones_globales`) VALUES
(24, 57, '2025-09-19 09:21:27', 1, 1, 'realizado', NULL, NULL, 'si', 'Puerto lateral USB', 'si', 'Tapas rayadas', NULL, NULL, NULL, 'realizada', 'okay', 'realizada', 'okay', 'pendiente', 'si', '{\"detalle\":\"se requiere\",\"cantidad\":\"1\",\"codigo_equipo\":\"LPDA1432\",\"serial_parte\":\"L15L3A03\",\"marca_parte\":\"LENOVO\",\"nivel_urgencia\":\"Baja\",\"referencia_parte\":\"L15L3A03\",\"ubicacion_pieza\":\"CAJA B2\"}', 'no', '', 'pendiente', 'no', '', '', 'Fallos estetico y esteico'),
(25, 59, '2025-09-23 16:57:44', 36, 36, 'realizado', NULL, NULL, 'si', '', 'si', '', NULL, NULL, NULL, 'realizada', '', 'realizada', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'si', 'laboratorio', '', ''),
(26, 109, '2025-09-24 17:14:15', 1, 1, 'realizado', NULL, NULL, 'si', 'TIENE', 'si', 'TIENE', NULL, NULL, NULL, 'realizada', 'REALIZADO', 'realizada', 'OKAY', 'pendiente', 'no', '', 'si', 'PUERTOS DAÑADOS', 'pendiente', 'si', 'laboratorio', '', 'FALLA'),
(27, 122, '2025-09-29 09:23:44', 1, 1, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', ''),
(36, 118, '2025-10-06 15:36:02', 1, 1, 'realizado', NULL, NULL, 'si', 'Puertos Usb sulfatados', 'si', 'Rayones en tapas ', NULL, NULL, NULL, 'realizada', 'Limpieza de borad, cambio de fluw en conectores USbs', 'realizada', 'Amplicacion de pasta termica, reparaciond e de regilla', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', 'Observaciones Globales de cllpti-5-202, texto  TESTEO muestra de - Observacion Globales'),
(37, 127, '2025-10-06 17:42:29', 1, 1, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', ''),
(38, 127, '2025-10-06 17:55:13', 1, 1, 'realizado', NULL, NULL, 'no', '', 'no', '', NULL, NULL, NULL, 'pendiente', '', 'pendiente', '', 'pendiente', 'no', '', 'no', '', 'pendiente', 'no', '', '', '');

CREATE TABLE `bodega_ordenes` (
  `idord` int NOT NULL,
  `cliente_id` int NOT NULL,
  `responsable` int NOT NULL,
  `total_items` int NOT NULL DEFAULT '0',
  `total_pago` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fecha_pago` datetime DEFAULT NULL,
  `metodo_pago` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_pago` enum('Debe_plata','Pendiente','Aceptado','total_pagado') COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `tipo_doc` enum('factura','ticket','remision') COLLATE utf8mb4_unicode_ci DEFAULT 'ticket',
  `num_documento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evidencia_pago` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `despachado_en` text COLLATE utf8mb4_unicode_ci,
  `creado_por` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_partes` (
  `id` int NOT NULL,
  `caja` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `generacion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_parte` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condicion` enum('Nuevo','Usado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `teclado` enum('Con Teclado','Sin Teclado','n/n') COLLATE utf8mb4_unicode_ci DEFAULT 'n/n',
  `precio` decimal(12,2) NOT NULL,
  `precio_nuevo_con_teclado` decimal(12,2) DEFAULT NULL,
  `precio_nuevo_sin_teclado` decimal(12,2) DEFAULT NULL,
  `precio_usado_con_teclado` decimal(12,2) DEFAULT NULL,
  `precio_usado_sin_teclado` decimal(12,2) DEFAULT NULL,
  `producto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_url` text COLLATE utf8mb4_unicode_ci,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `detalles` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_partes` (`id`, `caja`, `cantidad`, `marca`, `referencia`, `generacion`, `numero_parte`, `condicion`, `teclado`, `precio`, `precio_nuevo_con_teclado`, `precio_nuevo_sin_teclado`, `precio_usado_con_teclado`, `precio_usado_sin_teclado`, `producto`, `imagen_url`, `fecha_registro`, `detalles`, `codigo`, `serial`) VALUES
(1, 'CAJA B2', 30, 'LENOVO', 'L15L3A03', NULL, 'L15L3A03', 'Usado', 'n/n', 230000.00, NULL, NULL, NULL, NULL, 'Bateria', '#', '2025-07-15 03:58:18', NULL, 'equipo2000', NULL),
(2, 'CAJA B2', 19, 'LENOVO', 'L20B2PF0', NULL, 'L20B2PF0', 'Usado', 'n/n', 240000.00, NULL, NULL, NULL, NULL, 'Bateria', '#', '2025-07-15 03:58:18', NULL, NULL, NULL),
(3, 'CAJA B2', 30, 'LENOVO', 'L15L3A03', NULL, 'L15L3A03', 'Usado', 'n/n', 230000.00, NULL, NULL, NULL, NULL, 'Bateria', '#', '2025-07-15 03:58:25', NULL, NULL, NULL),
(4, 'CAJA B2', 19, 'LENOVO', 'L20B2PF0', NULL, 'L20B2PF0', 'Usado', 'n/n', 240000.00, NULL, NULL, NULL, NULL, 'Bateria', '#', '2025-07-15 03:58:25', NULL, NULL, NULL),
(5, 'CAJA F1', 12, 'DELL', 'PA-12', 'GEN 3', '0VJCH5', 'Usado', 'n/n', 85000.00, NULL, NULL, NULL, NULL, 'Fuente', '#', '2025-09-02 21:11:24', NULL, NULL, NULL),
(6, 'CAJA F2', 8, 'LENOVO', 'ADLX65NLC3A', 'GEN 2', '36200287', 'Nuevo', 'n/n', 120000.00, NULL, NULL, NULL, NULL, 'Fuente', '#', '2025-09-02 20:45:41', NULL, NULL, NULL),
(7, 'CAJA F3', 5, 'HP', 'PPP009L-E', 'GEN 1', '677774-002', 'Usado', 'n/n', 70000.00, NULL, NULL, NULL, NULL, 'Fuente', '#', '2025-07-15 03:59:10', NULL, NULL, NULL);

CREATE TABLE `bodega_salidas` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL,
  `cliente_id` int DEFAULT NULL,
  `tecnico_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `orden_id` int DEFAULT NULL,
  `fecha_salida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cantidad` int NOT NULL DEFAULT '1',
  `precio_unit` decimal(10,2) DEFAULT '0.00',
  `razon_salida` varchar(255) NOT NULL,
  `observaciones` text,
  `estado_despacho` enum('pendiente','en_ruta','entregado','cancelado') DEFAULT 'pendiente',
  `guia_remision` varchar(100) DEFAULT NULL,
  `evidencia_foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `bodega_salidas` (`id`, `inventario_id`, `cliente_id`, `tecnico_id`, `usuario_id`, `orden_id`, `fecha_salida`, `cantidad`, `precio_unit`, `razon_salida`, `observaciones`, `estado_despacho`, `guia_remision`, `evidencia_foto`) VALUES
(6, 108, NULL, 1, 1, NULL, '2025-09-24 12:01:54', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(7, 109, NULL, 1, 1, NULL, '2025-09-24 12:13:07', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(8, 130, NULL, 36, 1, NULL, '2025-09-26 16:01:04', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(9, 121, NULL, 36, 1, NULL, '2025-09-26 16:01:11', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(10, 117, NULL, 36, 1, NULL, '2025-09-27 09:56:43', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(11, 118, NULL, 36, 1, NULL, '2025-09-27 09:56:43', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(12, 120, NULL, 36, 1, NULL, '2025-09-27 09:56:43', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(66, 164, NULL, 33, 1, NULL, '2025-09-27 11:10:44', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(67, 166, NULL, 33, 1, NULL, '2025-09-27 11:10:44', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(68, 167, NULL, 33, 1, NULL, '2025-09-27 11:10:44', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(69, 129, NULL, 33, 1, NULL, '2025-09-27 11:10:44', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(70, 227, NULL, 1, 1, NULL, '2025-09-27 11:11:18', 1, 0.00, 'Asignación para triage', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(71, 121, NULL, 1, 1, NULL, '2025-10-06 14:25:08', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(72, 117, NULL, 1, 1, NULL, '2025-10-06 14:25:08', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(73, 118, NULL, 1, 1, NULL, '2025-10-06 14:25:08', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(74, 123, NULL, 1, 1, NULL, '2025-10-06 14:25:08', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(75, 120, NULL, 13, 1, NULL, '2025-10-06 14:26:12', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(76, 145, NULL, 13, 1, NULL, '2025-10-06 14:26:12', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(77, 133, NULL, 13, 1, NULL, '2025-10-06 14:26:12', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL),
(78, 108, NULL, 13, 1, NULL, '2025-10-06 14:26:33', 1, 0.00, 'Asignación para process', 'Asignado desde dashboard por usuario ID: 1', 'pendiente', NULL, NULL);

CREATE TABLE `bodega_solicitud_parte` (
  `id` int NOT NULL,
  `detalle_solicitud` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad_solicitada` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_equipo` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial_parte` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca_parte` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `usuario_solicitante` int DEFAULT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nivel_urgencia` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia_parte` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ubicacion_pieza` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_tecnico` int NOT NULL,
  `inventario_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bodega_solicitud_parte` (`id`, `detalle_solicitud`, `cantidad_solicitada`, `codigo_equipo`, `serial_parte`, `marca_parte`, `cantidad`, `usuario_solicitante`, `estado`, `fecha_solicitud`, `nivel_urgencia`, `referencia_parte`, `ubicacion_pieza`, `id_tecnico`, `inventario_id`) VALUES
(8, 'se requiere', '1', 'LPDA1432', 'L15L3A03', 'LENOVO', 1, 1, 'pendiente', '2025-09-19 09:21:27', 'Baja', 'L15L3A03', 'CAJA B2', 1, 57);

CREATE TABLE `cart` (
  `idv` int NOT NULL,
  `user_id` int NOT NULL,
  `idprod` int NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cart_compra` (
  `idcarco` int NOT NULL,
  `user_id` int NOT NULL,
  `idprod` int NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria` (
  `idcate` int NOT NULL,
  `nomca` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `clientes` (
  `idclie` int NOT NULL,
  `numid` char(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomcli` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `apecli` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `naci` date NOT NULL DEFAULT '1900-01-01',
  `correo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `celu` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estad` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dircli` text COLLATE utf8mb4_unicode_ci,
  `ciucli` text COLLATE utf8mb4_unicode_ci,
  `idsede` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` (`idclie`, `numid`, `nomcli`, `apecli`, `naci`, `correo`, `celu`, `estad`, `fere`, `dircli`, `ciucli`, `idsede`) VALUES
(20, '12345678', 'Juan', 'Perez', '1990-01-01', 'juan@correo.com', '3001234567', 'Activo', '2025-09-19 15:13:30', 'Calle 1 #2-3', 'Bogotá', 'Medellin'),
(21, '87654321', 'Maria', 'Garay', '1985-05-15', 'maria@correo.com', '3009876543', 'Activo', '2025-09-19 15:13:30', 'Carrera 5 #10-20', 'Bogotá', 'Unilago'),
(22, '11223344', 'Carlos', 'Lopez Vanegas', '1992-08-22', 'carlos@correo.com', '3005556772', 'Activo', '2025-09-19 15:13:30', 'Avenida 3 #15-8', 'Cucuta', 'Cucuta'),
(23, '55667788', 'Anyi', 'Rodriguez Vidal', '1988-12-10', 'ana@correo.com', '3001112222', 'Activo', '2025-09-19 15:13:30', 'Calle 8 #25-12', 'Bogotá', 'Principal');

CREATE TABLE `compra` (
  `idcomp` int NOT NULL,
  `user_id` int NOT NULL,
  `method` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_products` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `placed_on` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipc` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `despachos` (
  `id` int NOT NULL,
  `orden_id` int NOT NULL,
  `fecha_despacho` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `responsable` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gastos` (
  `idga` int NOT NULL,
  `detall` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ingresos` (
  `iding` int NOT NULL,
  `detalle` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ingresos` (`iding`, `detalle`, `total`, `fec`) VALUES
(23, 'VENTA DE PRODUCTOS - Orden #18', 0.00, '2025-09-30'),
(24, 'VENTA DE PRODUCTOS - Orden #19', 1400000.00, '2025-10-09');

CREATE TABLE `marketing` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `canal` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `gastos` decimal(12,2) DEFAULT '0.00',
  `ingresos` decimal(12,2) DEFAULT '0.00',
  `retorno_inversion` decimal(12,2) GENERATED ALWAYS AS ((case when (`gastos` > 0) then ((`ingresos` - `gastos`) / `gastos`) else NULL end)) STORED,
  `responsable` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activa','finalizada','pendiente') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `fuente_datos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `idord` int NOT NULL,
  `user_id` int NOT NULL,
  `user_cli` int NOT NULL,
  `method` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_products` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `placed_on` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `despacho` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsable` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`idord`, `user_id`, `user_cli`, `method`, `total_products`, `total_price`, `placed_on`, `payment_status`, `tipc`, `despacho`, `responsable`) VALUES
(18, 1, 20, 'Tarjeta', '0', 0.00, '2025-09-30 10:25:57', 'Aceptado', '0', 'Pendiente', 'Frank Quiñonez Vidal'),
(19, 1, 23, 'Efectivo', '0', 1400000.00, '2025-10-09 10:02:54', 'Aceptado', '0', 'Pendiente', 'Frank Quiñonez Vidal');

CREATE TABLE `plan` (
  `idplan` int NOT NULL,
  `foto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nompla` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estp` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prec` decimal(10,2) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `plan` (`idplan`, `foto`, `nompla`, `estp`, `prec`, `fere`) VALUES
(1, '515419.png', 'MANTENIMIENTO PREVENTIVO', 'Activo', 89500.00, '2024-03-15 13:27:45'),
(2, '767524.png', 'BORRADO SEGURO', 'Activo', 49500.00, '2024-03-15 13:27:46'),
(3, 'plan2.jpg', 'COMPONENTE', 'Activo', 99500.00, '2024-03-15 13:27:46'),
(4, '657987.jpg', 'REPARACION', 'Activo', 129000.00, '2024-03-31 13:27:46'),
(5, '997554.png', 'SERVICIO TECNICO', 'Activo', 6000.00, '2024-03-20 01:35:44'),
(6, '756730.png', 'reting', 'Activo', 90000.00, '2025-06-19 19:49:49');

CREATE TABLE `producto` (
  `idprod` int NOT NULL,
  `codba` char(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomprd` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `idcate` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `foto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `venci` date NOT NULL,
  `esta` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `serial` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ram` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disco` text COLLATE utf8mb4_unicode_ci,
  `prcpro` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pntpro` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tarpro` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grado` enum('A','B','C','SCRAP','#N/D','','0') COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `proveedores` (
  `id` int NOT NULL,
  `privado` int DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celu` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dire` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cuiprov` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomenclatura` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nit` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `proveedores` (`id`, `privado`, `nombre`, `celu`, `correo`, `dire`, `cuiprov`, `nomenclatura`, `nit`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'EULPT2-5-PCmarkett', '304 4177 847', 'pcmarkettingdigital@gmail.com', 'Cl. 14 #53-19,', 'Bogota DC', 'PRV024qeqw', '9012322738', '2025-09-23 20:51:06', '2025-10-06 17:28:59'),
(8, 1, 'Ejemplo S.A.S.', 'A', 'ejemplo@email.com', 'Calle 123 #45-67', 'Bogota', 'EJEMSAS', '901234567', '2025-07-14 17:20:31', '2025-09-23 20:47:40'),
(9, 1, 'PcShek Tecnologia Y Servicios S A S', '3186890437', 'comercial@pcshek.com', 'TV 66 # 35 - 11 MD 3 BG 9', 'Bogota', 'PCSH', '900413420', '2025-07-14 17:20:31', '2025-07-14 17:20:31'),
(24, 1, 'COLSOF1', '315 7146 129', 'proteccion.datos@colsof.com.co', 'Vereda Vuelta Grande, Predio San Rafael, Zona Franca Metropolitana, Bodega, 55-56, Cota, Cundinamarca', '800015583', 'PRV025', NULL, '2025-09-26 19:49:52', '2025-09-26 20:02:41');

CREATE TABLE `reserva_venta` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL,
  `usuario_id` int NOT NULL COMMENT 'ID del comercial que crea la reserva',
  `cliente_id` int NOT NULL,
  `fecha_reserva` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_vencimiento` date NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('activa','vencida','completada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `servicio` (
  `idservc` int NOT NULL,
  `idplan` int NOT NULL,
  `ini` date NOT NULL,
  `fin` date DEFAULT NULL,
  `idclie` int NOT NULL,
  `estod` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `canc` decimal(10,2) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `servtxt` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `servfoto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsable` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `setting` (
  `idsett` int NOT NULL,
  `nomem` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` char(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decrp` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `corr` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direc1` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `direc2` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `celu` char(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `setting` (`idsett`, `nomem`, `ruc`, `decrp`, `corr`, `direc1`, `direc2`, `celu`, `foto`) VALUES
(1, 'PCMARKETT SAS', '9012322738', 'Venta Computadores', 'pcmarkett2018@gmail.com', 'Cl. 14 #53-19, Bogotá, Colombia', 'CC Monterrey, Cra. 48 #10-45 Local 237, El Poblado, Medellín, Antioquia', '304 4177847', NULL);

CREATE TABLE `solicitud_alistamiento` (
  `id` int NOT NULL,
  `solicitante` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario_id` int NOT NULL COMMENT 'ID del usuario que solicita',
  `sede` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad` varchar(1600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(1600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` varchar(1200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tecnico_responsable` int DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `observacion_global` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clave` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` text COLLATE utf8mb4_unicode_ci,
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

CREATE TABLE `venta_detalles` (
  `id` int NOT NULL,
  `orden_id` int NOT NULL,
  `inventario_id` int NOT NULL,
  `serial` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_g` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `fecha_venta` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `venta_detalles` (`id`, `orden_id`, `inventario_id`, `serial`, `codigo_g`, `precio_unitario`, `fecha_venta`) VALUES
(4, 18, 109, 'HP987654321', 'EQ002', 0.00, '2025-09-30 15:25:57'),
(5, 19, 125, 'PF2ASHKP', 'CLLPT1-5-228', 1400000.00, '2025-10-09 15:02:54');


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
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_tecnico` (`tecnico_responsable`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_sede` (`sede`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orden_id` (`orden_id`),
  ADD KEY `idx_inventario_id` (`inventario_id`);


ALTER TABLE `bodega_asignaciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_cart_compra`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_compra`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_control_calidad`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `bodega_diagnosticos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

ALTER TABLE `bodega_electrico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `bodega_entradas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

ALTER TABLE `bodega_estetico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

ALTER TABLE `bodega_ingresos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_inventario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=438;

ALTER TABLE `bodega_log_cambios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

ALTER TABLE `bodega_mantenimiento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

ALTER TABLE `bodega_ordenes`
  MODIFY `idord` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `bodega_partes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `bodega_salidas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

ALTER TABLE `bodega_solicitud_parte`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `cart`
  MODIFY `idv` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `cart_compra`
  MODIFY `idcarco` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `categoria`
  MODIFY `idcate` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `clientes`
  MODIFY `idclie` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

ALTER TABLE `compra`
  MODIFY `idcomp` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `despachos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `gastos`
  MODIFY `idga` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `ingresos`
  MODIFY `iding` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

ALTER TABLE `marketing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
  MODIFY `idord` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

ALTER TABLE `plan`
  MODIFY `idplan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `producto`
  MODIFY `idprod` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `proveedores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

ALTER TABLE `reserva_venta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `servicio`
  MODIFY `idservc` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `setting`
  MODIFY `idsett` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `solicitud_alistamiento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

ALTER TABLE `venta_detalles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `bodega_salidas`
  ADD CONSTRAINT `fk_salida_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`idclie`),
  ADD CONSTRAINT `fk_salida_inventario` FOREIGN KEY (`inventario_id`) REFERENCES `bodega_inventario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_salida_orden` FOREIGN KEY (`orden_id`) REFERENCES `bodega_ordenes` (`idord`),
  ADD CONSTRAINT `fk_salida_tecnico` FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_salida_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `reserva_venta`
  ADD CONSTRAINT `fk_reserva_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`idclie`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reserva_inventario` FOREIGN KEY (`inventario_id`) REFERENCES `bodega_inventario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reserva_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `solicitud_alistamiento`
  ADD CONSTRAINT `fk_solicitud_tecnico` FOREIGN KEY (`tecnico_responsable`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_solicitud_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

