SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `u171145084_pcmteam` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `u171145084_pcmteam`;

CREATE TABLE `bodega_areas` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `bodega_control_calidad` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_control` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tecnico_id` int NOT NULL COMMENT 'ID del técnico que realiza el control',
  `burning_test` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Resultado de Burning Test',
  `sentinel_test` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Resultado de Sentinel',
  `estado_final` enum('aprobado','rechazado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria_rec` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Categorización REC',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `bodega_diagnosticos` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_diagnostico` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tecnico_id` int NOT NULL COMMENT 'ID del técnico que realiza el diagnóstico',
  `camara` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de cámara',
  `teclado` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de teclado',
  `parlantes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de audio',
  `bateria` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de batería',
  `microfono` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de micrófono',
  `pantalla` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de pantalla',
  `puertos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de puertos',
  `disco` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Resultado prueba de disco',
  `falla_electrica` enum('si','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `detalle_falla_electrica` text COLLATE utf8mb4_unicode_ci,
  `falla_estetica` enum('si','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `detalle_falla_estetica` text COLLATE utf8mb4_unicode_ci,
  `estado_reparacion` enum('falla_mecanica','falla_electrica','reparacion_cosmetica','aprobado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `bodega_entradas` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_entrada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `proveedor_id` int NOT NULL COMMENT 'ID del proveedor',
  `usuario_id` int NOT NULL COMMENT 'ID del usuario que registra',
  `cantidad` int NOT NULL DEFAULT '1',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_inventario` (
  `id` int NOT NULL,
  `codigo_g` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código general del equipo',
  `ubicacion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Zona específica en bodega/laboratorio',
  `posicion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Posición exacta dentro de la ubicación',
  `fecha_ingreso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `producto` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de producto (Laptop, Desktop, Monitor, AIO, etc.)',
  `marca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Marca del equipo',
  `serial` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de serie del fabricante',
  `modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Modelo o Referencia del equipo',
  `procesador` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Especificaciones del procesador',
  `ram` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Memoria RAM instalada',
  `disco` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipo y capacidad del disco',
  `pulgadas` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tamaño de pantalla',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Notas técnicas y observaciones',
  `grado` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Clasificación según procedimiento técnico (antes ENUM)',
  `disposicion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Estado actual del equipo en el proceso',
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `tecnico_id` int DEFAULT NULL,
  `pedido_id` int DEFAULT NULL,
  `producto_id` int DEFAULT NULL,
  `tactil` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lote` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo_fijo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_log_cambios` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL COMMENT 'ID del equipo en inventario',
  `usuario_id` int NOT NULL COMMENT 'ID del usuario que realizó el cambio',
  `fecha_cambio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del cambio',
  `campo_modificado` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del campo modificado',
  `valor_anterior` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Valor anterior del campo',
  `valor_nuevo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Nuevo valor del campo',
  `tipo_cambio` enum('edicion_manual','importacion','sistema') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'edicion_manual' COMMENT 'Tipo de cambio realizado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de cambios realizados en equipos del inventario';

CREATE TABLE `bodega_mantenimiento` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tecnico_id` int DEFAULT NULL,
  `usuario_registro` int DEFAULT NULL,
  `estado` enum('pendiente','realizado','rechazado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `tipo_proceso` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `falla_electrica` enum('si','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `detalle_falla_electrica` text COLLATE utf8mb4_unicode_ci,
  `falla_estetica` enum('si','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `detalle_falla_estetica` text COLLATE utf8mb4_unicode_ci,
  `partes_solicitadas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `referencia_externa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tecnico_diagnostico` int DEFAULT NULL,
  `limpieza_electronico` enum('pendiente','realizada','no_aplica') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `observaciones_limpieza_electronico` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mantenimiento_crema_disciplinaria` enum('pendiente','realizada','no_aplica') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `observaciones_mantenimiento_crema` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mantenimiento_partes` enum('pendiente','realizada','no_aplica') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `cambio_piezas` enum('si','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `piezas_solicitadas_cambiadas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `proceso_reconstruccion` enum('si','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `parte_reconstruida` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `limpieza_general` enum('pendiente','realizada','no_aplica') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `remite_otra_area` enum('si','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `area_remite` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `proceso_electronico` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `observaciones_globales` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_partes` (
  `id` int NOT NULL,
  `caja` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `marca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `generacion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_parte` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condicion` enum('Nuevo','Usado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `teclado` enum('Con Teclado','Sin Teclado','n/n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'n/n',
  `precio` decimal(12,2) NOT NULL,
  `precio_nuevo_con_teclado` decimal(12,2) DEFAULT NULL,
  `precio_nuevo_sin_teclado` decimal(12,2) DEFAULT NULL,
  `precio_usado_con_teclado` decimal(12,2) DEFAULT NULL,
  `precio_usado_sin_teclado` decimal(12,2) DEFAULT NULL,
  `producto` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `detalles` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bodega_salidas` (
  `id` int NOT NULL,
  `inventario_id` int NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_salida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tecnico_id` int NOT NULL COMMENT 'ID del técnico responsable',
  `usuario_id` int NOT NULL COMMENT 'ID del usuario que registra',
  `cantidad` int NOT NULL DEFAULT '1',
  `razon_salida` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `inventario_id` int DEFAULT NULL,
  `fecha_solicitud DATETIME` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cart` (
  `idv` int NOT NULL,
  `user_id` int NOT NULL,
  `idprod` int NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cart_compra` (
  `idcarco` int NOT NULL,
  `user_id` int NOT NULL,
  `idprod` int NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cart_compra` (`idcarco`, `user_id`, `idprod`, `name`, `price`, `quantity`) VALUES
(2, 3, 3, 'LTE MEMORIAS', 25, 2),
(3, 4, 4, 'CAJA DE SSD', 13, 4),
(4, 5, 5, 'LTE PORTAILES', 200000, 1),
(7, 2, 5, 'LOTE DE CARCASAS', 18000, 2);

CREATE TABLE `categoria` (
  `idcate` int NOT NULL,
  `nomca` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categoria` (`idcate`, `nomca`, `estado`, `fere`) VALUES
(1, 'COMPUTADOR DE MESA', 'Activo', '2024-03-15 13:27:45'),
(2, 'PORTATIL', 'Activo', '2024-03-15 13:27:46'),
(3, 'PIEZAS', 'Activo', '2024-03-15 13:27:46'),
(4, 'CELULARES', 'Activo', '2024-03-15 13:27:46'),
(5, 'MONITOR', 'Inactivo', '2024-03-21 23:58:56'),
(6, 'TODO EN UNO', 'Activo', '2024-03-21 23:59:10');

CREATE TABLE `clientes` (
  `idclie` int NOT NULL,
  `numid` char(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomcli` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apecli` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `naci` date NOT NULL DEFAULT '1900-01-01',
  `correo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `celu` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estad` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dircli` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ciucli` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `idsede` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `compra` (
  `idcomp` int NOT NULL,
  `user_id` int NOT NULL,
  `method` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_products` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `placed_on` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gastos` (
  `idga` int NOT NULL,
  `detall` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ingresos` (
  `iding` int NOT NULL,
  `detalle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `marketing` (
  `id` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `canal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `gastos` decimal(12,2) DEFAULT '0.00',
  `ingresos` decimal(12,2) DEFAULT '0.00',
  `retorno_inversion` decimal(12,2) GENERATED ALWAYS AS ((case when (`gastos` > 0) then ((`ingresos` - `gastos`) / `gastos`) else NULL end)) STORED,
  `responsable` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activa','finalizada','pendiente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `fuente_datos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `idord` int NOT NULL,
  `user_id` int NOT NULL,
  `user_cli` int NOT NULL,
  `method` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_products` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `placed_on` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `despacho` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsable` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plan` (
  `idplan` int NOT NULL,
  `foto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nompla` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estp` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prec` decimal(10,2) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `producto` (
  `idprod` int NOT NULL,
  `codba` char(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomprd` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idcate` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `foto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `venci` date NOT NULL,
  `esta` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `serial` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ram` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disco` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `prcpro` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pntpro` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tarpro` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grado` enum('A','B','C','SCRAP','#N/D','','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `proveedores` (
  `id` int NOT NULL,
  `privado` int DEFAULT NULL,
  `nombre` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dire` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cuiprov` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomenclatura` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nit` int DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `servicio` (
  `idservc` int NOT NULL,
  `idplan` int NOT NULL,
  `ini` date NOT NULL,
  `fin` date DEFAULT NULL,
  `idclie` int NOT NULL,
  `estod` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `canc` decimal(10,2) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `servtxt` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `servfoto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsable` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `setting` (
  `idsett` int NOT NULL,
  `nomem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` char(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `decrp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `corr` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `direc1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `direc2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `celu` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `setting` (`idsett`, `nomem`, `ruc`, `decrp`, `corr`, `direc1`, `direc2`, `celu`, `foto`) VALUES
(1, 'PCMARKET SAS', '901232273', 'PCMARKET SAS', 'comercial@pcmarkett.com', 'Cl. 14 #53-19, Bogota', '', '304 4177847', 'logo.jpg');

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `clave` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fere` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idsede` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
