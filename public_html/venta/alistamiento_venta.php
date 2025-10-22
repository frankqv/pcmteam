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