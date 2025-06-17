-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-06-2025 a las 16:32:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
USE `u171145084_pcmteam`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cart_compra`
--

INSERT INTO `cart_compra` (`idcarco`, `user_id`, `idprod`, `name`, `price`, `quantity`) VALUES
(0, 1, 5, 'Computador ASUS', 12500, 1),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `correo` text NOT NULL,
  `celu` char(10) NOT NULL,
  `estad` varchar(15) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp(),
  `dircli` text DEFAULT NULL,
  `ciucli` text DEFAULT NULL,
  `idsede` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`idclie`, `numid`, `nomcli`, `apecli`, `naci`, `correo`, `celu`, `estad`, `fere`, `dircli`, `ciucli`, `idsede`) VALUES
(1, '1231213', 'holman', 'grimaldo', '2019-03-13', 'grimaldox@gmail.com', '3026169292', 'Activo', '2024-03-14 04:02:53', 'Cra. 53 #121-51', 'Funza', 'Medellin'),
(2, '78901234', 'Ana Maria', 'Perez Gonzales', '1990-05-25', 'ana@example.com', '3157229001', 'Activo', '2024-03-14 04:30:20', 'Cra. 52 #14-51', 'Medellin, Antioquia', 'Unilago'),
(3, '56789012', 'Pedro', 'Gomez', '1985-10-12', 'pedro@example.com', '3123456789', 'Inactivo', '2023-08-18 12:45:10', 'Cra. 53 #14-53', 'Cucuta', 'Medellin'),
(4, '34567890', 'Laura', 'Lopez', '2000-03-08', 'laura@example.com', '3163993481', 'Inactivo', '2024-09-12 15:20:30', 'Cra. 54 #14-51', 'Cali', 'Cucuta'),
(5, '90123456', 'Juan Guillermo Cuadrado', 'Martinez', '1978-12-03', 'carlos@example.com', '3136497264', 'Activo', '2023-10-25 18:10:15', 'Cra. 55 #14-51', 'Cartagena', 'Bodega1'),
(6, '10232432', 'Joel Sebastian', 'Penagos Ortiz Trinidad de la Cruz', '0000-00-00', 'jsPenagos@gmail.com', '3058250638', 'Activo', '2024-03-21 06:22:37', 'Cra. 56 #14-51', 'Manizales', 'Bodega1'),
(7, '12345678', 'Andrea Berlin', 'Crawford Díaz', '2018-11-20', 'ClienteGenerico@pcmarkett.com', '3058250623', 'Activo', '2025-05-30 16:33:24', 'Cra. 57 #14-51', 'Bogota', 'Bodega1'),
(8, '12321343', 'Juan Andres', 'Burgos Alcala', '1997-07-03', 'jandresba@gmail.com', '381932101', 'Activo', '2025-06-10 16:27:06', 'Cra. 58 #14-51', 'Bogota', 'Bodega1'),
(9, '13471293', 'Sergio', 'Lara Bello', '2002-01-14', 'segioqw@gmial.com', '3282262171', 'Activo', '2025-06-10 17:54:33', 'Cra. 53 #14-51', '', 'Unilago');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`idga`, `detall`, `total`, `fec`) VALUES
(1, 'Gasto1', 10000.00, '2024-03-15'),
(2, 'Gasto2', 20000.00, '2024-03-15'),
(3, 'Gasto3', 15000.00, '2023-08-18'),
(4, 'Gasto4', 18500.00, '2023-09-22'),
(5, 'Compra de productos de Limpieza Protex', 22000.00, '2023-10-25'),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, 'VENTA DE MEMBRESIAS', 89500.00, '2023-04-05'),
(11, 'VENTA DE MEMBRESIAS', 80000.00, '2025-05-30'),
(12, 'VENTA DE MEMBRESIAS', 80000.00, '2025-05-30'),
(13, 'VENTA DE MEMBRESIAS', 6000.00, '2025-06-12'),
(14, 'VENTA DE MEMBRESIAS', 129000.00, '2025-06-16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marketing`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plan`
--

INSERT INTO `plan` (`idplan`, `foto`, `nompla`, `estp`, `prec`, `fere`) VALUES
(1, '515419.png', 'MANTENIMIENTO PREVENTIVO', 'Activo', 89500.00, '2024-03-15 08:27:45'),
(2, '767524.png', 'BORRADO SEGURO', 'Activo', 49500.00, '2024-03-15 08:27:46'),
(3, 'plan2.jpg', 'COMPONENTE', 'Activo', 99500.00, '2024-03-15 08:27:46'),
(4, '657987.jpg', 'REPARACION', 'Activo', 129000.00, '2024-03-31 08:27:46'),
(5, '997554.png', 'SERVICIO TECNICO', 'Activo', 6000.00, '2024-03-19 20:35:44');

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
  `fere` timestamp NOT NULL DEFAULT current_timestamp(),
  `serial` varchar(50) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `ram` varchar(8) DEFAULT NULL,
  `disco` varchar(8) DEFAULT NULL,
  `prcpro` varchar(8) DEFAULT NULL,
  `pntpro` varchar(8) DEFAULT NULL,
  `tarpro` varchar(30) DEFAULT NULL,
  `grado` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idprod`, `codba`, `nomprd`, `idcate`, `precio`, `stock`, `foto`, `venci`, `esta`, `fere`, `serial`, `marca`, `ram`, `disco`, `prcpro`, `pntpro`, `tarpro`, `grado`) VALUES
(1, 'vAZCeYThjC6An7', 'lenovo', 4, 5000000.00, 2, '10878.jpg', '2025-05-01', 'Activo', '2025-05-28 20:32:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '12345678901234', 'Computador Lenovo', 1, 10000.00, 1000, '115365.jpg', '2024-12-31', 'Activo', '2024-03-15 08:27:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '56789012340123', 'Computador 117', 2, 15000.00, 50, '341946.jpg', '2025-06-30', 'Activo', '2024-03-15 08:27:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '67890123451234', 'Computador DELL', 3, 25000.00, 26, '680339.jpg', '2025-12-31', 'Activo', '2024-03-15 08:27:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '78901234562345', 'Computador ASUS', 1, 12500.00, 80, '579718.jpg', '2024-10-31', 'Activo', '2024-03-15 08:27:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '89012345673456', 'Computador Compax', 4, 18000.00, 59, '956303.jpg', '2024-08-31', 'Activo', '2024-03-15 08:27:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'H7YY7MINAndznR', 'Computador HP', 1, 229500.00, 1000, '375961.png', '2025-04-01', 'Activo', '2024-03-21 19:19:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `privado` int(2) DEFAULT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `celu` int(10) DEFAULT NULL,
  `correo` varchar(30) DEFAULT NULL,
  `dire` varchar(30) DEFAULT NULL,
  `cuiprov` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `fere` timestamp NOT NULL DEFAULT current_timestamp(),
  `servtxt` varchar(250) DEFAULT NULL,
  `servfoto` varchar(255) DEFAULT NULL,
  `responsable` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`idservc`, `idplan`, `ini`, `fin`, `idclie`, `estod`, `meto`, `canc`, `fere`, `servtxt`, `servfoto`, `responsable`) VALUES
(1, 1, '2024-03-15', '2024-05-08', 1, 'Activo', 'Nequi', 20000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(2, 2, '2024-03-15', '2024-05-17', 2, 'Inactivo', 'Nequi', 30000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(3, 3, '2023-08-18', '2024-04-06', 3, 'Activo', 'Nequi', 40000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(4, 4, '2023-09-22', '2025-02-21', 4, 'Activo', 'Nequi', 35000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(5, 6, '2023-10-25', '2024-04-04', 5, 'Activo', 'Transferencia', 25000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(6, 3, '2023-04-05', '2024-04-04', 6, 'Activo', 'Tarjeta', 560000.00, '2024-03-21 06:28:16', NULL, 'portatil_display.jpeg', NULL),
(7, 0, '2025-05-30', '2025-06-12', 7, 'Activo', 'Nequi_Daviplata', 900000.00, '2025-05-30 17:21:20', 'El equipo enciende y funciona por salida externa. Display dañado físicamente, presenta manchas y líneas. Cliente solicita cambio de pantalla y mantenimiento general. 70', 'portatil_display.jpeg', NULL),
(8, 0, '2025-05-30', '2025-06-21', 2, 'Activo', 'Nequi_Daviplata', 9000000.00, '2025-05-30 17:24:54', 'El equipo enciende y funciona por salida externa. Display dañado físicamente, presenta manchas y líneas. Cliente solicita cambio de pantalla y mantenimiento general. 2', 'portatil_display.jpeg', NULL),
(9, 5, '2025-06-12', '2025-06-19', 7, 'Activo', 'Nequi_Daviplata', 90000.00, '2025-06-12 22:35:50', 'El equipo enciende y funciona por salida externa. Display dañado físicamente, presenta manchas y líneas. Cliente solicita cambio de pantalla y mantenimiento general.', 'portatil_display.jpeg', 'José Borda'),
(10, 4, '2025-06-16', '0000-00-00', 5, 'Medellin', 'Transferencia', 90000.00, '2025-06-16 23:01:11', '', '', '');

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
  `celu` char(16) NOT NULL,
  `foto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `setting`
--

INSERT INTO `setting` (`idsett`, `nomem`, `ruc`, `decrp`, `corr`, `direc1`, `direc2`, `celu`, `foto`) VALUES
(1, 'PCMARKET SAS', '901232273', 'PCMARKET SAS', 'comercial@pcmarkett.com', 'Cl. 14 #53-19, Bogotá,', '', '304 4177847', 'logo.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

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
  `idsede` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `correo`, `clave`, `rol`, `foto`, `estado`, `fere`, `idsede`) VALUES
(1, 'FrankQV', 'frank', 'frank@admin.com', '202cb962ac59075b964b07152d234b70', '1', '1', '1', '2025-05-28 14:48:15', '3'),
(2, 'Cristhian Romero', 'CristhianRomeropc', 'cr123@data.com', '53c9051e332d17250009640d364414c4', '1', '1', '1', '2025-05-28 22:37:54', NULL),
(3, 'Jasson Robles', 'Jassonroblespc', 'jr123@data.com', '75dbf8a92d4276fb51528da4e4a9d2c3', '1', '1', '1', '2025-05-28 22:38:35', NULL),
(4, 'Andrés Buitrago', 'AndresBuitragopc', 'ab123@data.com', '4b812d068c142583012bfb70131a61ab', '1', '1', '1', '2025-05-28 22:38:57', NULL),
(5, 'Nohelia Jaraba', 'Noheliajarabapc', 'nj123@data.com', '58b906fa888b10ebd49aa571bcef5149', '1', '1', '1', '2025-05-28 22:39:16', NULL),
(6, 'Anyi González', 'AnyiGonzalezpc', 'anyig123@data.com', '45deac01a8028dd922151f30e78e54ae', '1', '1', '1', '2025-05-28 22:39:49', NULL),
(7, 'Francisco Quiñonez', 'FranciscoQV', 'fqv123@data.com', 'e555b59d75e072eb5f18124db1cf1e22', '1', '1', '1', '2025-05-28 22:40:11', NULL),
(8, 'Sergio Lara', 'Sergiolarapc', 'sl123@data.com', '16170c99b0432f43d245347aa04aceaf', '6', '1', '1', '2025-05-28 22:40:48', NULL),
(9, 'Juan González', 'Juangonzalezpc', 'jg123@data.com', '210a23d675fe23128f532944f408089c', '5', '1', '1', '2025-05-28 22:41:07', NULL),
(10, 'Luis González', 'Luisgonzalezpc', 'lg123@data.com', '2f04e635bab80099208ccdd506acad69', '6', '1', '1', '2025-05-28 22:41:31', NULL),
(11, 'Natali Florez', 'Nataliflorezpc', 'nf123@data.com', 'd8ef5df38ad01af8d7c5e6e7a478f00d', '2', '1', '1', '2025-05-29 14:25:58', NULL),
(12, 'Fabian Sanchez', 'Fabiansanchezpc', 'fs123@data.com', 'c7417ff8f3f5c8600b914497b6b73492', '6', '1', '1', '2025-05-29 14:27:34', NULL),
(13, 'José Borda', 'Josebordapc', 'jb123@data.com', '4cd26c72d84d1e1d8fe7da2194d5153e', '5', '1', '1', '2025-05-29 14:30:57', NULL),
(14, 'Felipe Romero', 'Feliperomeropc', 'fr123@data.com', 'c843da53f7e567b80ff967cb3ba23aee', '5', '1', '1', '2025-05-29 14:31:24', NULL),
(15, 'Rodrigo Martínez', 'Rodrigomartinezpc', 'rm123@data.com', 'eafabe7aff85735469db0f134663b7cb', '7', '1', '1', '2025-05-29 14:31:43', NULL),
(16, 'Deivi Lopez', 'Deivilopezpc', 'dl123@data.com', 'facbcd76dde2c647198b1bab1d5d834d', '6', '1', '1', '2025-05-29 14:32:08', NULL),
(17, 'Maricela Tabla', 'Maricelatablapc', 'mt123@data.com', '0e57650e147ce827aec8b788db5a25ab', '3', '1', '1', '2025-05-29 14:32:29', ''),
(18, 'Ana Gaviria', 'Anagaviriapc', 'ag123@data.com', '30e5488c3c420588715fe3a51143e7ec', '3', '1', '1', '2025-05-29 14:32:51', NULL),
(19, 'Laura Pedraza', 'Laurapedrazapc', 'lp123@data.com', '39382aa4884af196f11ed8feba7d128f', '4', '1', '1', '2025-05-29 14:33:16', '4'),
(21, 'Gabriela Gutiérrez', 'gabrielagutierrezpc', 'gg123@data.com', '7d9bfd94d852319998c99d2c07980246', '4', '1', '1', '2025-05-29 14:33:42', '3'),
(22, 'Mónica Valencia', 'Monicavalenciapc', 'mv123@data.com', '213a253bf5cce2d84e4032ace9e29aa7', '4', '1', '1', '2025-05-29 14:34:06', '2'),
(28, 'frank2', 'frank2', 'frank2@gmail.com', '202cb962ac59075b964b07152d234b70', '2', '1', '1', '2025-06-06 21:40:21', '2'),
(29, 'frank3', 'frank3', 'frank3@gmail.com', '202cb962ac59075b964b07152d234b70', '3', '1', '1', '2025-06-09 20:07:48', '3'),
(31, 'frank4', 'frank4', 'frank4@gmail.com', '202cb962ac59075b964b07152d234b70', '4', '1', '1', '2025-06-09 20:08:22', '4'),
(32, 'frank5', 'frank5', 'frank5@gmail.com', '202cb962ac59075b964b07152d234b70', '5', '1', '1', '2025-06-09 20:08:38', NULL),
(33, 'frank6', 'frank6', 'frank6@gmail.com', '202cb962ac59075b964b07152d234b70', '6', '1', '1', '2025-06-09 20:09:04', NULL),
(34, 'frank7', 'frank7', 'frank7@gmail.com', '202cb962ac59075b964b07152d234b70', '7', '1', '1', '2025-06-09 20:09:18', NULL);

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
  ADD UNIQUE KEY `correo_UNIQUE` (`correo`) USING HASH,
  ADD UNIQUE KEY `numid_UNIQUE` (`numid`),
  ADD UNIQUE KEY `idclie` (`idclie`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`idcomp`),
  ADD UNIQUE KEY `idcomp` (`idcomp`),
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
-- Indices de la tabla `marketing`
--
ALTER TABLE `marketing`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`idord`),
  ADD UNIQUE KEY `idord` (`idord`);

--
-- Indices de la tabla `plan`
--
ALTER TABLE `plan`
  ADD PRIMARY KEY (`idplan`),
  ADD UNIQUE KEY `idplan` (`idplan`),
  ADD UNIQUE KEY `idplan_3` (`idplan`),
  ADD UNIQUE KEY `idplan_2` (`idplan`,`nompla`) USING HASH;

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idprod`,`idcate`),
  ADD UNIQUE KEY `idprod` (`idprod`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`idservc`,`idplan`,`idclie`),
  ADD UNIQUE KEY `idservc` (`idservc`);

--
-- Indices de la tabla `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`idsett`),
  ADD UNIQUE KEY `idsett` (`idsett`);

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
  MODIFY `idcate` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `idclie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `idcomp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `idga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `iding` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `marketing`
--
ALTER TABLE `marketing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `idord` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idprod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idservc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `setting`
--
ALTER TABLE `setting`
  MODIFY `idsett` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
