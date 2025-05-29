-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 29-05-2025 a las 17:56:59
-- Versión del servidor: 10.11.10-MariaDB-log
-- Versión de PHP: 7.2.34

START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u171145084_pcmteam`
--
CREATE DATABASE IF NOT EXISTS `u171145084_pcmteam` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE u171145084_pcmteam;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart`
--

CREATE TABLE `cart` (
  `idv` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `idprod` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ;

--
-- Volcado de datos para la tabla `cart`
--

INSERT INTO `cart` (`idv`, `user_id`, `idprod`, `name`, `price`, `quantity`) VALUES
(1, 1, 1, 'Producto1', 10, 2),
(4, 4, 4, 'Producto4', 13, 4),
(6, 2, 5, 'creatina 1Kg', 18000, 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart_compra`
--

CREATE TABLE `cart_compra` (
  `idcarco` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `idprod` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ;

--
-- Volcado de datos para la tabla `cart_compra`
--

INSERT INTO `cart_compra` (`idcarco`, `user_id`, `idprod`, `name`, `price`, `quantity`) VALUES
(2, 3, 3, 'LTE MEMORIAS', 25, 2),
(3, 4, 4, 'CAJA DE SSD', 13, 4),
(4, 5, 5, 'LTE PORTAILES', 200000, 1),
(7, 2, 5, 'LOTE DE CARCASAS', 18000, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `idcate` int(11) NOT NULL,
  `nomca` text NOT NULL,
  `estado` varchar(15) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`idcate`, `nomca`, `estado`, `fere`) VALUES
(1, 'COMPUTADOR DE MESA', 'Activo', '2024-03-15 08:27:45'),
(2, 'PORTATIL', 'Activo', '2024-03-15 08:27:46'),
(3, 'PIEZAS', 'Activo', '2024-03-15 08:27:46'),
(4, 'CELULARES', 'Activo', '2024-03-15 08:27:46'),
(5, 'MONITOR', 'Inactivo', '2024-03-21 18:58:56'),
(6, 'TODO EN UNO', 'Activo', '2024-03-21 18:59:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `idclie` int(11) NOT NULL,
  `numid` char(8) NOT NULL,
  `nomcli` text NOT NULL,
  `apecli` text NOT NULL,
  `naci` date NOT NULL,
  `correo` varchar(30) NOT NULL,
  `celu` char(10) NOT NULL,
  `estad` varchar(15) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`idclie`, `numid`, `nomcli`, `apecli`, `naci`, `correo`, `celu`, `estad`, `fere`) VALUES
(1, '1231213', 'holman', 'grimaldo', '2019-03-13', 'grimaldox@gmail.com', '3026169292', 'Activo', '2024-03-14 04:02:53'),
(2, '78901234', 'Ana', 'Perez', '1990-05-25', 'ana@example.com', '3157229001', 'Activo', '2024-03-14 04:30:20'),
(3, '56789012', 'Pedro', 'Gomez', '1985-10-12', 'pedro@example.com', '3123456789', 'Inactivo', '2023-08-18 12:45:10'),
(4, '34567890', 'Laura', 'Lopez', '2000-03-08', 'laura@example.com', '3163993481', 'Activo', '2024-09-12 15:20:30'),
(5, '90123456', 'Carlos', 'Martinez', '1978-12-03', 'carlos@example.com', '3136497264', 'Activo', '2023-10-25 18:10:15'),
(6, '10232432', 'Joel Sebastian', 'Penagos Ortiz Trinidad de la Cruz', '0000-00-00', 'jsPenagos@gmail.com', '3058250638', 'Activo', '2024-03-21 06:22:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `idcomp` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `method` text NOT NULL,
  `total_products` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `placed_on` text NOT NULL,
  `payment_status` text NOT NULL,
  `tipc` text NOT NULL
) ;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`idcomp`, `user_id`, `method`, `total_products`, `total_price`, `placed_on`, `payment_status`, `tipc`) VALUES
(1, 1, 'Tarjeta', 'Producto1', 20.00, '2024-03-15', 'Pagado', 'Tipc'),
(2, 2, 'Efectivo', 'Producto2, Producto3', 70.00, '2024-03-14', 'Pendiente', 'Tipc'),
(3, 3, 'Transferencia', 'Producto4, Producto5', 60.50, '2023-08-18', 'Pagado', 'Tipc'),
(4, 4, 'Tarjeta', 'Producto2, Producto3, Producto5', 58.50, '2023-09-22', 'Pagado', 'Tipc'),
(5, 5, 'Efectivo', 'Producto4', 12.50, '2023-10-25', 'Pagado', 'Tipc'),
(6, 2, 'Transferencia', ', Producto2 ( 3 ), Producto4 ( 1 ), creatina 1Kg ( 1 )', 75500.00, '2024-03-19', 'Aceptado', 'Ticket');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `idga` int(11) NOT NULL,
  `detall` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text NOT NULL
) ;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`idga`, `detall`, `total`, `fec`) VALUES
(1, 'Gasto1', 10000.00, '2024-03-15'),
(2, 'Gasto2', 20000.00, '2024-03-15'),
(3, 'Gasto3', 15000.00, '2023-08-18'),
(4, 'Gasto4', 18500.00, '2023-09-22'),
(5, 'Gasto5', 22000.00, '2023-10-25'),
(6, 'COMPRA DE PRODUCTOS', 75500.00, '2024-03-19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `iding` int(11) NOT NULL,
  `detalle` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text NOT NULL
) ;

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`iding`, `detalle`, `total`, `fec`) VALUES
(1, 'Ingreso1', 30000.00, '2024-03-15'),
(2, 'Ingreso2', 35000.00, '2024-03-15'),
(3, 'Ingreso3', 28000.00, '2023-08-18'),
(4, 'Ingreso4', 455000.00, '2023-09-22'),
(5, 'Ingreso5', 50000.00, '2023-10-25'),
(6, 'VENTA DE PRODUCTOS', 95000.00, '2024-03-15'),
(7, 'VENTA DE PRODUCTOS', 50000.00, '2024-03-19'),
(8, 'VENTA DE PRODUCTOS', 108500.00, '2024-03-19'),
(9, 'VENTA DE PRODUCTOS', 5000000.00, '2024-03-19'),
(10, 'VENTA DE MEMBRESIAS', 89500.00, '2023-04-05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `idord` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_cli` int(11) NOT NULL,
  `method` text NOT NULL,
  `total_products` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `placed_on` text NOT NULL,
  `payment_status` text NOT NULL,
  `tipc` text NOT NULL
) ;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`idord`, `user_id`, `user_cli`, `method`, `total_products`, `total_price`, `placed_on`, `payment_status`, `tipc`) VALUES
(1, 1, 1, 'Tarjeta', 'Producto1', 20000.00, '2024-03-15', 'Pagado', 'Tipc'),
(2, 2, 2, 'Efectivo', 'Producto2, Producto3', 70000.00, '2024-03-14', 'Pendiente', 'Tipc'),
(3, 3, 3, 'Transferencia', 'Producto4, Producto5', 65000.00, '2023-08-18', 'Pagado', 'Tipc'),
(4, 4, 4, 'Tarjeta', 'Producto2, Producto3, Producto5', 58500.00, '2023-09-22', 'Pagado', 'Tipc'),
(5, 5, 5, 'Efectivo', 'Producto4', 125000.00, '2023-10-25', 'Pagado', 'Tipc'),
(6, 2, 2, 'Efectivo', ', Producto2 ( 3 )', 450000.00, '2024-03-15', 'Aceptado', 'Ticket'),
(7, 3, 5, 'Transferencia', ', Producto3 ( 2 )', 50000.00, '2024-03-19', 'Aceptado', 'Ticket'),
(8, 3, 5, 'Transferencia', ', creatina 1Kg ( 2 ), Producto1 ( 1 ), Producto4 ( 1 ), Producto3 ( 2 )', 108500.00, '2024-03-19', 'Aceptado', 'Ticket'),
(9, 3, 5, 'Efectivo', ', Producto1 ( 500 )', 5000000.00, '2024-03-19', 'Aceptado', 'Ticket');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan`
--

CREATE TABLE `plan` (
  `idplan` int(11) NOT NULL,
  `foto` text NOT NULL,
  `nompla` text NOT NULL,
  `estp` varchar(15) NOT NULL,
  `prec` decimal(10,2) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `plan`
--

INSERT INTO `plan` (`idplan`, `foto`, `nompla`, `estp`, `prec`, `fere`) VALUES
(1, '734337.jpg', 'PLAN BASICO', 'Activo', 89500.00, '2024-03-15 08:27:45'),
(2, 'plan2.jpg', ' PLAN STANDARD', 'Activo', 49500.00, '2024-03-15 08:27:46'),
(3, 'plan2.jpg', 'PLAN PLATINO', 'Activo', 99500.00, '2024-03-15 08:27:46'),
(4, 'plan1.jpg', 'PLAN PREMIUM', 'Activo', 129000.00, '2024-03-31 08:27:46'),
(6, 'plan1.jpg', 'PLAN PREMIUM 2', 'Inactivo', 6000.00, '2024-03-19 20:35:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

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
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idprod`, `codba`, `nomprd`, `idcate`, `precio`, `stock`, `foto`, `venci`, `esta`, `fere`) VALUES
(1, '12345678901234', 'Computador Lenovo', 1, 10000.00, 1000, '115365.jpg', '2024-12-31', 'Activo', '2024-03-15 08:27:45'),
(2, '56789012340123', 'Computador 117', 2, 15000.00, 50, '341946.jpg', '2025-06-30', 'Activo', '2024-03-15 08:27:46'),
(3, '67890123451234', 'Computador DELL', 3, 25000.00, 26, '680339.jpg', '2025-12-31', 'Activo', '2024-03-15 08:27:46'),
(4, '78901234562345', 'Computador ASUS', 1, 12500.00, 80, '579718.jpg', '2024-10-31', 'Activo', '2024-03-15 08:27:46'),
(5, '89012345673456', 'Computador Compax', 4, 18000.00, 59, '956303.jpg', '2024-08-31', 'Activo', '2024-03-15 08:27:46'),
(6, 'H7YY7MINAndznR', 'Computador HP', 1, 229500.00, 1000, '375961.png', '2025-04-01', 'Activo', '2024-03-21 19:19:20'),
(0, 'vAZCeYThjC6An7', 'lenovo', 4, 5000000.00, 2, '10878.jpg', '2025-05-01', 'Activo', '2025-05-28 20:32:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `idservc` int(11) NOT NULL,
  `idplan` int(11) NOT NULL,
  `ini` date NOT NULL,
  `fin` date NOT NULL,
  `idclie` int(11) NOT NULL,
  `estod` varchar(15) NOT NULL,
  `meto` text NOT NULL,
  `canc` decimal(10,2) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`idservc`, `idplan`, `ini`, `fin`, `idclie`, `estod`, `meto`, `canc`, `fere`) VALUES
(1, 1, '2024-03-15', '2024-05-08', 1, 'Activo', 'Metodo', 20000.00, '2024-03-15 08:27:46'),
(2, 2, '2024-03-15', '2024-05-17', 2, 'Inactivo', 'Metodo2', 30000.00, '2024-03-15 08:27:46'),
(3, 3, '2023-08-18', '2024-04-06', 3, 'Activo', 'Metodo3', 40000.00, '2024-03-15 08:27:46'),
(4, 4, '2023-09-22', '2025-02-21', 4, 'Activo', 'Nequi', 35000.00, '2024-03-15 08:27:46'),
(5, 6, '2023-10-25', '2024-04-04', 5, 'Activo', 'Transferencia', 25000.00, '2024-03-15 08:27:46'),
(6, 3, '2023-04-05', '2024-04-04', 6, 'Activo', 'Tarjeta', 560000.00, '2024-03-21 06:28:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `setting`
--

CREATE TABLE `setting` (
  `idsett` int(11) NOT NULL,
  `nomem` text NOT NULL,
  `ruc` char(14) NOT NULL,
  `decrp` text NOT NULL,
  `corr` varchar(35) NOT NULL,
  `direc1` text NOT NULL,
  `direc2` text NOT NULL,
  `celu` char(10) NOT NULL,
  `foto` text NOT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `usuario` varchar(15) NOT NULL,
  `correo` varchar(30) NOT NULL,
  `clave` text NOT NULL,
  `rol` char(1) NOT NULL,
  `foto` text DEFAULT NULL,
  `estado` char(1) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `correo`, `clave`, `rol`, `foto`, `estado`, `fere`) VALUES
(1, 'FrankQV', 'frank', 'frank@admin.com', '202cb962ac59075b964b07152d234b70', '1', '1', '1', '2025-05-28 14:48:15'),
(2, 'Cristhian Romero', 'CristhianRomero', 'cr123@data.com', '53c9051e332d17250009640d364414c4', '2', '1', '1', '2025-05-28 22:37:54'),
(3, 'Jasson Robles', 'Jassonroblespc', 'jr123@data.com', '75dbf8a92d4276fb51528da4e4a9d2c3', '2', '1', '1', '2025-05-28 22:38:35'),
(4, 'Andrés Buitrago', 'AndresBuitragop', 'ab123@data.com', '4b812d068c142583012bfb70131a61ab', '2', '1', '1', '2025-05-28 22:38:57'),
(5, 'Nohelia Jaraba', 'Noheliajarabapc', 'nj123@data.com', '58b906fa888b10ebd49aa571bcef5149', '2', '1', '1', '2025-05-28 22:39:16'),
(6, 'Anyi González', 'AnyiGonzalezpc', 'anyig123@data.com', '45deac01a8028dd922151f30e78e54ae', '2', '1', '1', '2025-05-28 22:39:49'),
(7, 'Francisco Quiñonez', 'FranciscoQV', 'fqv123@data.com', 'e555b59d75e072eb5f18124db1cf1e22', '2', '1', '1', '2025-05-28 22:40:11'),
(9, 'Sergio Lara', 'Sergiolarapc', 'sl123@data.com', '16170c99b0432f43d245347aa04aceaf', '2', '1', '1', '2025-05-28 22:40:48'),
(10, 'Juan González', 'Juangonzalezpc', 'jg123@data.com', '210a23d675fe23128f532944f408089c', '2', '1', '1', '2025-05-28 22:41:07'),
(11, 'Luis González', 'Luisgonzalezpc', 'lg123@data.com', '2f04e635bab80099208ccdd506acad69', '2', '1', '1', '2025-05-28 22:41:31'),
(13, 'Natali Florez', 'Nataliflorezpc', 'nf123@data.com', 'd8ef5df38ad01af8d7c5e6e7a478f00d', '2', '1', '1', '2025-05-29 14:25:58'),
(17, 'Fabian Sanchez', 'Fabiansanchezpc', 'fs123@data.com', 'c7417ff8f3f5c8600b914497b6b73492', '2', '1', '1', '2025-05-29 14:27:34'),
(18, 'José Borda', 'Josebordapc', 'jb123@data.com', '4cd26c72d84d1e1d8fe7da2194d5153e', '2', '1', '1', '2025-05-29 14:30:57'),
(19, 'Felipe Romero', 'Feliperomeropc', 'fr123@data.com', 'c843da53f7e567b80ff967cb3ba23aee', '2', '1', '1', '2025-05-29 14:31:24'),
(20, 'Rodrigo Martínez', 'Rodrigomartinez', 'rm123@data.com', 'eafabe7aff85735469db0f134663b7cb', '2', '1', '1', '2025-05-29 14:31:43'),
(21, 'Deivi Lopez', 'Deivilopezpc', 'dl123@data.com', 'facbcd76dde2c647198b1bab1d5d834d', '2', '1', '1', '2025-05-29 14:32:08'),
(22, 'Maricela Tabla', 'Maricelatablapc', 'mt123@data.com', '0e57650e147ce827aec8b788db5a25ab', '2', '1', '1', '2025-05-29 14:32:29'),
(23, 'Ana Gaviria', 'Anagaviriapc', 'ag123@data.com', '30e5488c3c420588715fe3a51143e7ec', '2', '1', '1', '2025-05-29 14:32:51'),
(24, 'Laura Pedraza', 'Laurapedrazapc', 'lp123@data.com', '39382aa4884af196f11ed8feba7d128f', '2', '1', '1', '2025-05-29 14:33:16'),
(25, 'Gabriela Gutiérrez', 'gabrielagutierr', 'gg123@data.com', '7d9bfd94d852319998c99d2c07980246', '2', '1', '1', '2025-05-29 14:33:42'),
(26, 'Mónica Valencia', 'Monicavalenciap', 'mv123@data.com', '213a253bf5cce2d84e4032ace9e29aa7', '2', '1', '1', '2025-05-29 14:34:06');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cart_compra`
--
ALTER TABLE `cart_compra`
  ADD PRIMARY KEY (`idcarco`),
  ADD KEY `fk_cart_compra_usuario` (`user_id`),
  ADD KEY `fk_cart_compra_producto` (`idprod`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idcate`),
  ADD UNIQUE KEY `idcate` (`idcate`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`idclie`),
  ADD UNIQUE KEY `correo_UNIQUE` (`correo`),
  ADD UNIQUE KEY `numid_UNIQUE` (`numid`),
  ADD UNIQUE KEY `idclie` (`idclie`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`idcomp`),
  ADD KEY `fk_compra_usuario` (`user_id`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`idga`),
  ADD UNIQUE KEY `idga` (`idga`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`iding`),
  ADD UNIQUE KEY `iding` (`iding`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`idservc`);

--
-- Indices de la tabla `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`idsett`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idcate` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `idclie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `idcomp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `iding` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idservc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `setting`
--
ALTER TABLE `setting`
  MODIFY `idsett` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
