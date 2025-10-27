  CREATE TABLE `new_alistamiento_venta` (
      -- ============================================
      -- IDENTIFICADORES Y CONTROL
      -- ============================================
      `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `idventa` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE COMMENT 'Número de venta: AV-2025-0001',
      `ticket` VARCHAR(160) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE COMMENT 'Ticket alfanumérico único',
      `numero_alistamiento` VARCHAR(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número de alistamiento - Se pone más tarde',
      `estado` VARCHAR(250) COLLATE utf8mb4_unicode_ci DEFAULT 'borrador' COMMENT 'Estado del pedido',
      
      -- ============================================
      -- FECHAS Y AUDITORÍA
      -- ============================================
      `fecha_venta` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de primer registro de la venta',
      `fecha_actualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última fecha cambio registrado',
      
      -- ============================================
      -- USUARIO Y SEDE
      -- ============================================
      `usuario_id` INT NOT NULL COMMENT 'ID del usuario que solicita - El que esté logueado',
      `sede` VARCHAR(150) COLLATE utf8mb4_unicode_ci NOT NULL,
      
      -- ============================================
      -- INFORMACIÓN DEL CLIENTE
      -- ============================================
      `idcliente` INT NOT NULL COMMENT 'FK a tabla clientes',
      `tipo_cliente` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Es un label',
      `direccion` VARCHAR(750) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dirección de envío - Trae la información de (clientes.dircli) con la opción de edición',
      
      -- ============================================
      -- TIPO Y CANAL DE VENTA
      -- ============================================
      `canal_venta` VARCHAR(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Es un label',
      `concepto_salida` VARCHAR(250) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Lista desplegable: venta física, servicio técnico, etc',
      
      -- ============================================
      -- PRODUCTOS/ITEMS (JSON) ✅ CAMBIADO A TEXT
      -- ============================================
      `id_inventario` VARCHAR(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Se auto rellena si producto se encuentra en bodega_inventario',
      `cantidad` TEXT COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON de varios productos',
      `descripcion` TEXT COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON de varios productos - Ejemplo: [{"cantidad":"2", "producto":"Laptop"}]',
      `marca` VARCHAR(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `modelo` VARCHAR(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `observacion` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      
      -- ============================================
      -- VALORES FINANCIEROS ✅ CAMBIADO A DECIMAL
      -- ============================================
      `subtotal` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Subtotal de la venta',
      `descuento` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Descuento aplicado',
      `total_venta` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Total final',
      
      -- ============================================
      -- PAGOS Y SALDOS ✅ CAMBIADO A DECIMAL
      -- ============================================
      `valor_abono` DECIMAL(12,2) DEFAULT 0.00,
      `metodo_pago_abono` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `saldo_pagado` DECIMAL(12,2) DEFAULT 0.00,
      `metodo_pago_saldo` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `saldo_inicial` DECIMAL(12,2) DEFAULT 0.00,
      `saldo_pendiente` DECIMAL(12,2) DEFAULT 0.00,
      `saldo_final` DECIMAL(12,2) DEFAULT 0.00,
      
      -- ============================================
      -- OBSERVACIONES DE PAGOS ✅ CAMBIADO A TEXT
      -- ============================================
      `observaciones_saldo` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Trae la información de los abonos anteriores y sus métodos de pago',
      `observaciones_fechas_abono` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Se guarda la colección de objetos - Se trae información anterior con posibilidad de añadir más',
      
      -- ============================================
      -- OBSERVACIONES POR ROL ✅ CAMBIADO A TEXT
      -- ============================================
      `observacion_global` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observaciones que realice la comercial - Todos los usuarios con rol 4 (usuarios.rol = 4)',
      `observacion_contable` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observaciones del perfil contable al poner número de factura',
      `observacion_tecnico` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observaciones del técnico',
      -- ============================================
      -- sólo la ruta del comprobante del pago 
      -- Analiza mis demas partes del proyecto solo querio que cuando se suban las fotos de los comprobantes se guarden 
      -- en esta siguiete ruta: public_html/assetes/img/comprobantes
      -- ============================================
      `foto_comprobante` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observaciones del técnico', 
      
      -- ============================================
      -- ÍNDICES
      -- ============================================
      INDEX idx_fecha (`fecha_venta`),
      INDEX idx_cliente (`idcliente`),
      INDEX idx_usuario (`usuario_id`),
      INDEX idx_estado (`estado`)
      
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;







CREATE table `new_alistamiento_venta`(
    `id` int NOT NULL,
    `idventa` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de venta: AV-2025-0001',
    'ticket' varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ticket alfanumérico único',
  `fecha_venta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Fecha de primer registro de la venta */
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, /* ultima fecha cmbio registrado */
  `usuario_id` int NOT NULL COMMENT 'ID del usuario que solicita', /* El qeu este registrado o logueado  que inicio la sesion, que es la persona de la venta*/
  `sede` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idcliente` int NOT NULL COMMENT 'FK a tabla clientes',
  `direccion` varchar(750 )not null  commet `direccion de envio Tregeu la informacionde (clientes.dircli) con la opcion de edicion`,
  `tipo_cliente` varchar(50) not null, /* es un label*/
  `canal_venta` varchar(150) not null, /* es un label*/
  `concepto_salida` varchar(250) not null, /* es un label, es una lista desplegable sólo en la parte del php opciones son: venta fisica; servicio tecnico; */
  `numero_alistamiento` varchar(150) not null commet `numero de alistamiento SE PONE MAS TARDE`,
  `id_inventario` varchar(250) not null, /* que se auto rellene si porducto si se encuantra en la base de datos "bodega_inventario" */
  `cantidad` varchar (1600) not null commet `json de varios productos`,  
  `descripcion` varchar (1600) not null commet `json de varios productos se ponne varios en la misma celda como en el archivos de EJEMPLO de Ingresar datos en el frotend  EJEMPLO: [{\"cantidad\":public_html/venta/preventa.php", pero con la nueva logica`
  `marca` varchar(250) DEFAULT NULL,
`modelo` varchar(250) DEFAULT NULL,
`observacion` varchar(1200) DEFAULT NULL,
`subtotal` varchar(1600) DEFAULT NULL,
`descuento` varchar(250) DEFAULT NULL,
`total_venta` varchar(1600) DEFAULT NULL,
`valor_abono` varchar(1600) DEFAULT NULL,
`saldo_pagado` varchar(1600) DEFAULT NULL,
`metdo_pago_abono` varchar(1600) DEFAULT NULL,
`metodo_pago_saldo` varchar(1600) DEFAULT NULL,
`saldo_inicial` varchar(1600) DEFAULT NULL,
`saldo_pediente` varchar(1600) DEFAULT NULL,
`saldo_final` varchar(1600) DEFAULT NULL,
`estado` varchar(250) DEFAULT NULL,
`observaciones_saldo` varchar(1600) DEFAULT NULL, /* trae la informacion de los abonos anterios sus metodos de pagos  */
`observaciones_fechas_abono` varchar(1600) default null, /* se guarada la coleccion de objestos se trae informacionanterior con la posibilidad, porder añadir mas informacion */
`observacion_global` varchar(1600) DEFAULT NULL, /* observacione que realice la comercial todos los usuarios con el rol 4 usuaruios.rol(4) */
`observacion_contable` varchar(1600) DEFAULT NULL, /* observaciones del perfil contable al poner numero de factura*/
`observacion_tecnico` varchar(1600) DEFAULT NULL,
)



/* como querio se guarde los datos en el la tabla de new_alistamiento_venta.observacio
EJEMPLO:
Despacho: Interrapidisimo Pte Aranda | Productos JSON: [{"cantidad":5,"descripcion":"MONITOR 20","marca":"hp","modelo":"lex2002","observacion":"n/d"},{"cantidad":1,"descripcion":"monitor 27 leopard","marca":"leopard","modelo":"leopard 27\"","observacion":"TASA DE REFRIGERACIÓN DE 75GHZ"}]
*/


CREATE TABLE `alistamiento_venta` (
  `id` int NOT NULL, /* ✓ */
  `idventa` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de venta: AV-2025-0001',/* ✓ */
  `ticket` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ticket alfanumérico único',/* ✓ */
  `fecha_venta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,/* ✓ */
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,/* ✓ */
  `usuario_id` int NOT NULL COMMENT 'ID del usuario que solicita',/* ✓ */
  `sede` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,/* ✓ */
  `idcliente` int NOT NULL COMMENT 'FK a tabla clientes',/* ✓ */
  `ubicacion` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dirección de envío',/* ✓ */
  `numguia_envio` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número de guía', /* ✓ */
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',/* ✓ */
  `descuento` decimal(12,2) DEFAULT '0.00',/* ✓ */
  `total_venta` decimal(12,2) NOT NULL COMMENT 'Total final',/* ✓ */
  `valor_abono` decimal(12,2) DEFAULT '0.00' COMMENT 'Cuánto abonó',/* ✓ */
  `saldo` decimal(12,2) NOT NULL COMMENT 'Cuánto queda',/* ✓ */
  `medio_abono` enum('efectivo','transferencia','tarjeta_credito','tarjeta_debito','nequi','daviplata','bancolombia','otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medio_saldo` enum('efectivo','transferencia','tarjeta_credito','tarjeta_debito','nequi','daviplata','bancolombia','otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('borrador','pendiente','aprobado','en_alistamiento','alistado','despachado','en_transito','entregado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
  `observacion_global` text COLLATE utf8mb4_unicode_ci,
  `observacion_tecnico` text COLLATE utf8mb4_unicode_ci,
  `creado_por` int NOT NULL,
  `modificado_por` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `alistamiento_venta` (`id`, `idventa`, `ticket`, `fecha_venta`, `fecha_actualizacion`, `usuario_id`, `sede`, `idcliente`, `ubicacion`, `numguia_envio`, `subtotal`, `descuento`, `total_venta`, `valor_abono`, `saldo`, `medio_abono`, `medio_saldo`, `estado`, `observacion_global`, `observacion_tecnico`, `creado_por`, `modificado_por`) VALUES
(1, 'AV-2025-0001', 'TXT-2025-1', '2025-10-23 16:29:41', '2025-10-23 16:30:07', 1, 'Bogotá', 24, 'Cra. 53 #14-51, Puente Aranda, Bogotá, BOGOTA (C/MARCA) (110110)', NULL, 0.00, 100000.00, 0.00, 7000000.00, 0.00, 'transferencia', NULL, 'aprobado', '', '\n2025-10-23 16:30:07 - cambios de estado als 430', 1, 1),
(2, 'AV-2025-0002', 'txt wada', '2025-10-23 16:40:45', '2025-10-23 16:40:45', 1, 'Bogotá', 24, 'Cra. 53 #14-51, Puente Aranda, Bogotá, BOGOTA (C/MARCA) (110110)', NULL, 0.00, 5.00, 0.00, 100.00, 0.00, 'transferencia', NULL, 'aprobado', 'con programas', NULL, 1, NULL);


CREATE TABLE `alistamiento_venta_archivos` (
  `id` int NOT NULL, /* ✓ */
  `alistamiento_id` int NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta_archivo` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ruta: a_img/ventas/archivo.jpg',
  `tipo_archivo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamano` int DEFAULT NULL COMMENT 'Bytes',
  `fecha_subida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subido_por` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `alistamiento_venta_items` (
  `id` int NOT NULL,/* ✓ */
  `alistamiento_id` int NOT NULL COMMENT 'FK a alistamiento_venta',/* ✓ */
  `item_numero` int NOT NULL COMMENT 'Orden: 1, 2, 3...',/* ✓ */
  `inventario_id` int DEFAULT NULL COMMENT 'FK a bodega_inventario (NULL si manual)',/* ✓ */
  `producto` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL, 
  `procesador` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ram` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disco` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grado` enum('A','B','C','N/A') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `cantidad` int NOT NULL DEFAULT '1',
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) GENERATED ALWAYS AS ((`cantidad` * `precio_unitario`)) STORED,
  `estado_alistamiento` enum('pendiente','en_proceso','alistado','despachado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `observacion` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `alistamiento_venta_items` (`id`, `alistamiento_id`, `item_numero`, `inventario_id`, `producto`, `marca`, `modelo`, `procesador`, `ram`, `disco`, `grado`, `descripcion`, `cantidad`, `precio_unitario`, `estado_alistamiento`, `observacion`) VALUES
(1, 1, 1, 411, 'Portatil', 'Lenovo', 'Thinkpad x390', 'Intel core i7 8665U', '16GB', '256GB SSD', 'A', 'Lenovo Thinkpad x390 - Intel core i7 8665U | RAM: 16GB | Disco: 256GB SSD | 13.5 | Táctil: SI', 1, 970000.00, 'pendiente', NULL),
(2, 1, 2, NULL, 'Disco toshiba 1TB Canvio Advance', 'TOHSIBA', 'Canvio Advance 1TB', NULL, '', '1TB', NULL, '', 50, 129900.00, 'pendiente', NULL),
(3, 2, 1, 412, 'Portatil', 'Lenovo', 'Thinkpad x390', 'Intel core i7 8565U', '16GB', '512GB SSD', 'A', 'Lenovo Thinkpad x390 - Intel core i7 8565U | RAM: 16GB | Disco: 512GB SSD | 13.5 | Táctil: NO', 1, 110.00, 'pendiente', NULL);


// guardar todo en esta tabla, solo va existir dos tablas la Principal
// y otra que guarde los log de los cambios hechos






CREATE TABLE `solicitud_alistamiento` (
  `id` int(11) NOT NULL, /* ✓ */
  `solicitante` varchar(255) NOT NULL, /* ✓ */
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que solicita',/* ✓ */
  `sede` varchar(100) NOT NULL,/* ✓ */
  `cliente` varchar(255) DEFAULT NULL,/* ✓ */
`cantidad` varchar(1600) NOT NULL,/* ✓ */
`descripcion` varchar(1600) NOT NULL,/* ✓ */
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
