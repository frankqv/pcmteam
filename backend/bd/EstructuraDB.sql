-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.3.23



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `u171145084_pcmteam` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE u171145084_pcmteam;

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
) ;

INSERT INTO `bodega_control_calidad` (`id`, `inventario_id`, `fecha_control`, `tecnico_id`, `burning_test`, `sentinel_test`, `estado_final`, `categoria_rec`, `observaciones`) VALUES
(1, 1, '2025-06-30 17:32:02', 13, 'Pasó 24h sin problemas', 'Sin amenazas detectadas', 'aprobado', 'REC-A', 'Equipo listo para venta'),
(2, 2, '2025-06-30 17:32:02', 13, 'Pasó 12h, se detuvo por sobrecalentamiento', 'Limpio', 'rechazado', 'REC-SCRAP', 'Requiere revisión del sistema de refrigeración');

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
  `estado_reparacion` enum('falla_mecanica','falla_electrica','reparacion_cosmetica','aprobado') NOT NULL,
  `observaciones` text DEFAULT NULL
) ;

INSERT INTO `bodega_diagnosticos` (`id`, `inventario_id`, `fecha_diagnostico`, `tecnico_id`, `camara`, `teclado`, `parlantes`, `bateria`, `microfono`, `pantalla`, `puertos`, `disco`, `estado_reparacion`, `observaciones`) VALUES
(1, 1, '2025-06-30 17:32:02', 8, 'Funcional', 'Funcional', 'Funcional', '85% capacidad', 'Funcional', 'Sin píxeles muertos', 'Todos funcionales', 'Estado excelente', 'aprobado', 'Equipo en perfectas condiciones'),
(2, 2, '2025-06-30 17:32:02', 8, 'N/A', 'Funcional', 'Funcional', 'N/A', 'N/A', 'Funcional', 'Puerto USB dañado', 'Buen estado', 'falla_mecanica', 'Requiere cambio de puerto USB'),
(3, 3, '2025-06-30 17:32:02', 8, 'Funcional', 'Tecla Space pegajosa', 'Funcional', 'N/A', 'Funcional', 'Rayones superficiales', 'Funcionales', 'Fragmentación alta', 'reparacion_cosmetica', 'Requiere limpieza de teclado y desfragmentación');

CREATE TABLE `bodega_entradas` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_entrada` datetime NOT NULL DEFAULT current_timestamp(),
  `proveedor_id` int(11) NOT NULL COMMENT 'ID del proveedor',
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que registra',
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `observaciones` text DEFAULT NULL
) ;

INSERT INTO `bodega_entradas` (`id`, `inventario_id`, `fecha_entrada`, `proveedor_id`, `usuario_id`, `cantidad`, `observaciones`) VALUES
(1, 1, '2025-06-30 17:32:02', 1, 1, 1, 'Entrada inicial de inventario'),
(2, 2, '2025-06-30 17:32:02', 1, 1, 1, 'Entrada desde proveedor principal'),
(3, 3, '2025-06-30 17:32:02', 2, 1, 1, 'Entrada desde proveedor secundario'),
(4, 4, '2025-07-02 17:19:56', 1, 1, 1, 'TESTEO4');

CREATE TABLE `bodega_inventario` (
  `id` int(11) NOT NULL,
  `codigo_g` varchar(50) NOT NULL COMMENT 'Código general del equipo',
  `item` int(11) NOT NULL COMMENT 'Número de ítem secuencial',
  `ubicacion` varchar(100) NOT NULL COMMENT 'Zona específica en bodega/laboratorio',
  `posicion` varchar(50) NOT NULL COMMENT 'Posición exacta dentro de la ubicación',
  `fecha_ingreso` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` datetime NOT NULL DEFAULT current_timestamp(),
  `activo_fijo` varchar(50) DEFAULT NULL COMMENT 'Identificador de activo fijo',
  `codigo_lote` varchar(50) NOT NULL COMMENT 'Código de lote de ingreso',
  `producto` varchar(50) NOT NULL COMMENT 'Tipo de producto (Laptop, Desktop, Monitor, AIO, etc.)',
  `marca` varchar(50) NOT NULL COMMENT 'Marca del equipo',
  `serial` varchar(100) NOT NULL COMMENT 'Número de serie del fabricante',
  `modelo` varchar(100) NOT NULL COMMENT 'Modelo específico del equipo',
  `procesador` varchar(100) DEFAULT NULL COMMENT 'Especificaciones del procesador',
  `ram` varchar(50) DEFAULT NULL COMMENT 'Memoria RAM instalada',
  `disco` varchar(100) DEFAULT NULL COMMENT 'Tipo y capacidad del disco',
  `pulgadas` varchar(20) DEFAULT NULL COMMENT 'Tamaño de pantalla',
  `observaciones` text DEFAULT NULL COMMENT 'Notas técnicas y observaciones',
  `grado` enum('A','B','C') NOT NULL COMMENT 'Clasificación según procedimiento técnico',
  `disposicion` varchar(50) NOT NULL COMMENT 'Estado actual del equipo en el proceso',
  `estado` text NOT NULL DEFAULT '\'activo\'',
  `tecnico_id` int(11) DEFAULT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `tactil` text DEFAULT NULL
) ;

INSERT INTO `bodega_inventario` (`id`, `codigo_g`, `item`, `ubicacion`, `posicion`, `fecha_ingreso`, `fecha_modificacion`, `activo_fijo`, `codigo_lote`, `producto`, `marca`, `serial`, `modelo`, `procesador`, `ram`, `disco`, `pulgadas`, `observaciones`, `grado`, `disposicion`, `estado`, `tecnico_id`, `pedido_id`, `producto_id`, `tactil`) VALUES
(1, 'EQ001', 1, 'Principal', 'ESTANTE-1-A', '2025-06-30 17:32:02', '2025-07-11 18:04:01', 'AF001', 'LOTE2025001', 'Portatil', 'Dell', 'DL123456789', 'Latitude 5520', 'Intel i5-1135G7aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '8GB', '256GB SSD', '15.6', 'Equipo en buen estado', 'A', 'Business Room', 'Business', 33, NULL, NULL, 'SI'),
(2, 'EQ002', 2, 'Principal', 'ESTANTE-1-B', '2025-06-30 17:32:02', '2025-07-12 10:57:12', 'AF002', 'LOTE2025001', 'Desktop', 'HP', 'HP987654321', 'EliteDesk 800', 'Intel i7-10700', '16GB', '512GB SSD', '16', 'EQUIPO LISTO', 'A', 'Para Venta', 'Business', 33, NULL, NULL, 'NO'),
(3, 'EQ003', 3, 'Cúcuta', 'ESTANTE-2-A', '2025-06-30 17:32:02', '2025-07-11 15:57:28', 'AF003', 'LOTE2025002', 'AIO', 'Lenovo', 'LN456789123', 'ThinkCentre M90a', 'Intel i5-10400T', '8GB', '1TB HDD', '23.8', 'Pantalla con rayones menores', 'C', 'Business Room', 'activo', 32, NULL, NULL, 'NO'),
(4, 'LPDA 1432', 4, 'Principal', 'DWQDEW', '2025-07-02 17:19:56', '2025-07-12 10:55:38', 'AF004', 'LOTE2025001', 'Periferico', 'HP', 'ds', '132', 'i5 14th', '8GB', '125 gb', '', 'tESTEO', 'A', 'Para Venta', 'Business', 33, NULL, NULL, 'SI');

CREATE TABLE `bodega_partes` (
  `id` int(11) NOT NULL,
  `caja` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `generacion` varchar(50) DEFAULT NULL,
  `numero_parte` varchar(100) DEFAULT NULL,
  `condicion` enum('Nuevo','Usado') NOT NULL,
  `teclado` enum('Con Teclado','Sin Teclado','n/n') DEFAULT 'n/n',
  `precio` decimal(12,2) NOT NULL,
  `precio_nuevo_con_teclado` decimal(12,2) DEFAULT NULL,
  `precio_nuevo_sin_teclado` decimal(12,2) DEFAULT NULL,
  `precio_usado_con_teclado` decimal(12,2) DEFAULT NULL,
  `precio_usado_sin_teclado` decimal(12,2) DEFAULT NULL,
  `etiquetas` varchar(100) DEFAULT NULL,
  `producto` varchar(100) DEFAULT NULL,
  `imagen_url` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ;

INSERT INTO `bodega_partes` (`id`, `caja`, `cantidad`, `marca`, `modelo`, `generacion`, `numero_parte`, `condicion`, `teclado`, `precio`, `precio_nuevo_con_teclado`, `precio_nuevo_sin_teclado`, `precio_usado_con_teclado`, `precio_usado_sin_teclado`, `etiquetas`, `producto`, `imagen_url`, `fecha_registro`) VALUES
(1, 'CAJA B2', 30, 'LENOVO', 'L15L3A03', NULL, 'L15L3A03', 'Usado', 'n/n', 230000.00, NULL, NULL, NULL, NULL, 'Bateria', 'Bateria', '#', '2025-07-14 22:58:18'),
(2, 'CAJA B2', 19, 'LENOVO', 'L20B2PF0', NULL, 'L20B2PF0', 'Usado', 'n/n', 240000.00, NULL, NULL, NULL, NULL, 'Bateria', 'Bateria', '#', '2025-07-14 22:58:18'),
(3, 'CAJA B2', 30, 'LENOVO', 'L15L3A03', NULL, 'L15L3A03', 'Usado', 'n/n', 230000.00, NULL, NULL, NULL, NULL, 'Bateria', 'Bateria', '#', '2025-07-14 22:58:25'),
(4, 'CAJA B2', 19, 'LENOVO', 'L20B2PF0', NULL, 'L20B2PF0', 'Usado', 'n/n', 240000.00, NULL, NULL, NULL, NULL, 'Bateria', 'Bateria', '#', '2025-07-14 22:58:25'),
(5, 'CAJA F1', 12, 'DELL', 'PA-12', 'GEN 3', '0VJCH5', 'Usado', 'n/n', 85000.00, NULL, NULL, NULL, NULL, 'Fuente', 'Fuente', '#', '2025-07-14 22:59:10'),
(6, 'CAJA F2', 8, 'LENOVO', 'ADLX65NLC3A', 'GEN 2', '36200287', 'Nuevo', 'n/n', 120000.00, NULL, NULL, NULL, NULL, 'Fuente', 'Fuente', '#', '2025-07-14 22:59:10'),
(7, 'CAJA F3', 5, 'HP', 'PPP009L-E', 'GEN 1', '677774-002', 'Usado', 'n/n', 70000.00, NULL, NULL, NULL, NULL, 'Fuente', 'Fuente', '#', '2025-07-14 22:59:10');

CREATE TABLE `bodega_salidas` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL COMMENT 'ID del equipo en inventario',
  `fecha_salida` datetime NOT NULL DEFAULT current_timestamp(),
  `tecnico_id` int(11) NOT NULL COMMENT 'ID del técnico responsable',
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que registra',
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `razon_salida` text NOT NULL,
  `observaciones` text DEFAULT NULL
) ;

CREATE TABLE `cart` (
  `idv` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `idprod` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ;


CREATE TABLE `cart_compra` (

  `idcarco` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `idprod` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ;

INSERT INTO `cart_compra` (`idcarco`, `user_id`, `idprod`, `name`, `price`, `quantity`) VALUES
(2, 3, 3, 'LTE MEMORIAS', 25, 2),
(3, 4, 4, 'CAJA DE SSD', 13, 4),
(4, 5, 5, 'LTE PORTAILES', 200000, 1),
(7, 2, 5, 'LOTE DE CARCASAS', 18000, 2);

CREATE TABLE `categoria` (
  `idcate` int(11) NOT NULL,
  `nomca` text NOT NULL,
  `estado` varchar(15) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ;

INSERT INTO `categoria` (`idcate`, `nomca`, `estado`, `fere`) VALUES
(1, 'COMPUTADOR DE MESA', 'Activo', '2024-03-15 08:27:45'),
(2, 'PORTATIL', 'Activo', '2024-03-15 08:27:46'),
(3, 'PIEZAS', 'Activo', '2024-03-15 08:27:46'),
(4, 'CELULARES', 'Activo', '2024-03-15 08:27:46'),
(5, 'MONITOR', 'Inactivo', '2024-03-21 18:58:56'),
(6, 'TODO EN UNO', 'Activo', '2024-03-21 18:59:10');

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
) ;

INSERT INTO `clientes` (`idclie`, `numid`, `nomcli`, `apecli`, `naci`, `correo`, `celu`, `estad`, `fere`, `dircli`, `ciucli`, `idsede`) VALUES
(1, '1231213', 'holman', 'grimaldo', '2019-03-13', 'grimaldox@gmail.com', '3026169292', 'Activo', '2024-03-14 04:02:53', 'Cra. 53 #121-51', 'Funza', 'Medellin'),
(2, '78901234', 'Ana Maria', 'Perez Gonzales', '1990-05-25', 'ana@example.com', '3157229001', 'Activo', '2024-03-14 04:30:20', 'Cra. 52 #14-51', 'Medellin, Antioquia', 'Unilago'),
(3, '56789012', 'Pedro', 'Gomez', '1985-10-12', 'pedro@example.com', '3123456789', 'Inactivo', '2023-08-18 12:45:10', 'Cra. 53 #14-53', 'Cucuta', 'Medellin'),
(4, '34567890', 'Laura', 'Lopez', '2000-03-08', 'laura@example.com', '3163993481', 'Inactivo', '2024-09-12 15:20:30', 'Cra. 54 #14-51', 'Cali', 'Cucuta'),
(5, '90123456', 'Juan Guillermo Cuadrado', 'Martinez', '1978-12-03', 'carlos@example.com', '3136497264', 'Activo', '2023-10-25 18:10:15', 'Cra. 55 #14-51', 'Cartagena', 'Principal'),
(6, '10232432', 'Joel Sebastian', 'Penagos Ortiz Trinidad de la Cruz', '0000-00-00', 'jsPenagos@gmail.com', '3058250638', 'Activo', '2024-03-21 06:22:37', 'Cra. 56 #14-51', 'Manizales', 'Unilago'),
(7, '12345678', 'Andrea Berlin', 'Crawford Díaz', '2018-11-20', 'ClienteGenerico@pcmarkett.com', '3058250623', 'Activo', '2025-05-30 16:33:24', 'Cra. 57 #14-51', 'Bogota', 'Principal'),
(8, '12321343', 'Juan Andres', 'Burgos Alcala', '1997-07-03', 'jandresba@gmail.com', '381932101', 'Activo', '2025-06-10 16:27:06', 'Cra. 58 #14-51', 'Bogota', 'Principal'),
(9, '13471293', 'Sergio', 'Lara Bello', '2002-01-14', 'segioqw@gmial.com', '3282262171', 'Activo', '2025-06-10 17:54:33', 'Cra. 53 #14-51', 'Tocacima', 'Unilago'),
(10, '65321874', 'Mary', 'Bonz Rodriguez', '1997-01-02', 'maryb1997@correo.com', '3001234557', 'Activo', '2025-06-24 19:58:48', 'Calle 1 #2-4', 'Cali', 'Unilago'),
(12, '87654321', 'Maria', 'Garay', '1985-05-15', 'maria@correo.com', '3009876543', 'Activo', '2025-06-24 22:18:58', 'Carrera 5 #10-20', 'Bogotá', 'Unilago'),
(13, '11223344', 'Carlos', 'Lopez Vanegas', '1992-08-22', 'carlos@correo.com', '3005556466', 'Activo', '2025-06-24 22:18:58', 'Avenida 3 #15-8', 'Cucuta', 'Cucuta'),
(14, '51667788', 'Paula', 'Santa Rosa', '1988-12-10', 'paurosa@correo.com', '3001112242', 'Activo', '2025-06-24 22:18:58', 'Calle 8 #25-21', 'Bogotá', 'Principal'),
(15, '91997788', 'Stephany Tatiana', 'Brown Castillo', '2007-12-10', 'stepbc@corre.com', '3201112242', 'Activo', '2025-06-24 22:25:02', 'Calle 3 #4-5', 'Cartagena', 'Medellin'),
(16, '12343214', 'Juan Armando ', 'Torrres Angel', '2025-07-09', 'qwq@ws.com', '3222212321', 'Activo', '2025-07-02 22:18:03', NULL, NULL, NULL),
(17, '55667788', 'Anyi', 'Rodriguez Vidal', '1988-12-10', 'ana@correo.com', '3001112222', 'Activo', '2025-07-14 20:00:44', 'Calle 8 #25-12', 'Bogotá', 'Principal'),
(18, '8', '1', 'Ejemplo S.A.S.', '1900-01-01', 'Calle 123 #45-67', 'Bogota', 'EJEMSAS', '2025-07-14 22:02:45', '900123456', 'ejemplo@email.com', '2025-07-14 12:20:31'),
(19, '9', '1', 'PcShek Tecnologia Y Servicios S A S', '1900-01-01', 'TV 66 # 35 - 11 MD 3 BG 9', 'Bogota', 'PCSH', '2025-07-14 22:02:46', '900123456', 'comercial@pcshek.com', '2025-07-14 12:20:31');

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

INSERT INTO `compra` (`idcomp`, `user_id`, `method`, `total_products`, `total_price`, `placed_on`, `payment_status`, `tipc`) VALUES
(1, 1, 'Tarjeta', 'Producto1', 20.00, '2024-03-15', 'Pagado', 'Tipc'),
(2, 2, 'Efectivo', 'Producto2, Producto3', 70.00, '2024-03-14', 'Pendiente', 'Tipc'),
(3, 3, 'Transferencia', 'Producto4, Producto5', 60.50, '2023-08-18', 'Pagado', 'Tipc'),
(4, 4, 'Tarjeta', 'Producto2, Producto3, Producto5', 58.50, '2023-09-22', 'Pagado', 'Tipc'),
(5, 5, 'Efectivo', 'Producto4', 12.50, '2023-10-25', 'Pagado', 'Tipc'),
(6, 2, 'Transferencia', ', Producto2 ( 3 ), Producto4 ( 1 ), creatina 1Kg ( 1 )', 75500.00, '2024-03-19', 'Aceptado', 'Ticket'),
(7, 1, 'Efectivo', ', Computador ASUS ( 1 )', 12500.00, '2025-06-20', 'Aceptado', 'Ticket'),
(8, 1, 'Efectivo', ', Computador 117 ( 1 )', 15000.00, '2025-07-09', 'Aceptado', 'Ticket'),
(9, 1, 'Transferencia', ', lenovo ( 1 ), Computador 117 ( 1 )', 5015000.00, '2025-07-09', 'Aceptado', 'Ticket');

CREATE TABLE `gastos` (
  `idga` int(11) NOT NULL,
  `detall` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text NOT NULL
) ;

INSERT INTO `gastos` (`idga`, `detall`, `total`, `fec`) VALUES
(1, 'Gasto1', 10000.00, '2024-03-15'),
(2, 'Compra de insumos', 20000.00, '2024-03-15'),
(3, 'Gasto3', 15000.00, '2023-08-18'),
(4, 'Gasto4', 18500.00, '2023-09-22'),
(5, 'Compra de productos de Limpieza Protex', 22000.00, '2023-10-25'),
(6, 'COMPRA DE PRODUCTOS', 75500.00, '2024-03-19'),
(7, 'COMPRA DE PRODUCTOS', 12500.00, '2025-06-20'),
(8, 'COMPRA DE PRODUCTOS', 15000.00, '2025-07-09'),
(9, 'COMPRA DE PRODUCTOS', 5015000.00, '2025-07-09');

CREATE TABLE `ingresos` (
  `iding` int(11) NOT NULL,
  `detalle` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fec` text NOT NULL
) ;

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
(14, 'VENTA DE MEMBRESIAS', 129000.00, '2025-06-16'),
(15, 'VENTA DE PRODUCTOS', 15000.00, '2025-06-25'),
(16, 'VENTA DE PRODUCTOS', 40000.00, '2025-06-24'),
(17, 'VENTA DE PRODUCTOS', 5033000.00, '2025-06-26'),
(18, 'VENTA DE PRODUCTOS', 25000.00, '2025-06-25'),
(19, 'VENTA DE PRODUCTOS', 244500.00, '2025-07-10');

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
) ;

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
) ;

INSERT INTO `orders` (`idord`, `user_id`, `user_cli`, `method`, `total_products`, `total_price`, `placed_on`, `payment_status`, `tipc`, `despacho`, `responsable`) VALUES
(1, 34, 1, 'Tarjeta', 'Producto1', 20000.00, '2024-03-15', 'Pagado', 'Tipc', 'DESPACHO TIENDA PUENTE ARANDA', NULL),
(2, 13, 2, 'Efectivo', 'Producto2, Producto3', 70000.00, '2024-03-14', 'Pendiente', 'Tipc', 'COORDINADORA pte aranda', NULL),
(3, 8, 3, 'Transferencia', 'Producto4, Producto5', 65000.00, '2023-08-18', 'Pagado', 'Tipc', 'cancelado', NULL),
(4, 13, 4, 'Tarjeta', 'Producto2, Producto3, Producto5', 58500.00, '2023-09-22', 'Pagado', 'Tipc', 'DESPACHO TIENDA PUENTE ARANDA', NULL),
(5, 12, 5, 'Efectivo', 'Producto4', 125000.00, '2023-10-25', 'Pagado', 'Tipc', 'DESPACHO TIENDA UNILAGO', NULL),
(6, 13, 2, 'Efectivo', ', Producto2 ( 3 )', 450000.00, '2024-03-15', 'Aceptado', 'Ticket', 'DESPACHO TIENDA PUENTE ARANDA', NULL),
(7, 15, 5, 'Transferencia', ', Producto3 ( 2 )', 50000.00, '2024-03-19', 'Aceptado', 'Ticket', 'PICAP', NULL),
(8, 8, 5, 'Transferencia', ', creatina 1Kg ( 2 ), Producto1 ( 1 ), Producto4 ( 1 ), Producto3 ( 2 )', 108500.00, '2024-03-19', 'Aceptado', 'Ticket', 'DESPACHO TIENDA PUENTE ARANDA', NULL),
(9, 10, 5, 'Efectivo', ', Producto1 ( 500 )', 5000000.00, '2024-03-19', 'Aceptado', 'Ticket', 'DESPACHO TIENDA CUCUTA', NULL),
(10, 9, 12, 'Efectivo', ', Computador 117 ( 1 )', 15000.00, '2025-06-25', 'Aceptado', 'Ticket', 'DESPACHO TIENDA PUENTE ARANDA', NULL),
(11, 32, 9, 'Efectivo', ', Computador DELL ( 1 ), Computador 117 ( 1 )', 40000.00, '2025-06-24', 'Aceptado', 'Ticket', 'DESPACHO TIENDA MEDELLIN', NULL),
(12, 10, 7, 'Efectivo', ', Computador Compax ( 1 ), lenovo ( 1 ), Computador 117 ( 1 )', 5033000.00, '2025-06-26', 'Aceptado', 'Ticket', 'INTERRAPIDISIMO pte aranda', NULL),
(13, 16, 9, 'Efectivo', ', Computador DELL ( 1 )', 25000.00, '2025-06-25', 'Aceptado', 'Ticket', 'DESPACHO TIENDA PUENTE ARANDA', NULL),
(14, 1, 7, 'Efectivo', ', Computador 117 ( 1 ), Computador HP ( 1 )', 244500.00, '2025-07-10', 'Aceptado', 'Ticket', NULL, NULL);

CREATE TABLE `plan` (
  `idplan` int(11) NOT NULL,
  `foto` text NOT NULL,
  `nompla` text NOT NULL,
  `estp` varchar(15) NOT NULL,
  `prec` decimal(10,2) NOT NULL,
  `fere` timestamp NOT NULL DEFAULT current_timestamp()
) ;

INSERT INTO `plan` (`idplan`, `foto`, `nompla`, `estp`, `prec`, `fere`) VALUES
(1, '515419.png', 'MANTENIMIENTO PREVENTIVO', 'Activo', 89500.00, '2024-03-15 08:27:45'),
(2, '767524.png', 'BORRADO SEGURO', 'Activo', 49500.00, '2024-03-15 08:27:46'),
(3, 'plan2.jpg', 'COMPONENTE', 'Activo', 99500.00, '2024-03-15 08:27:46'),
(4, '657987.jpg', 'REPARACION', 'Activo', 129000.00, '2024-03-31 08:27:46'),
(5, '997554.png', 'SERVICIO TECNICO', 'Activo', 6000.00, '2024-03-19 20:35:44'),
(6, '756730.png', 'reting', 'Activo', 90000.00, '2025-06-19 14:49:49');

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
) ;

INSERT INTO `producto` (`idprod`, `codba`, `nomprd`, `idcate`, `precio`, `stock`, `foto`, `venci`, `esta`, `fere`, `serial`, `marca`, `ram`, `disco`, `prcpro`, `pntpro`, `tarpro`, `grado`) VALUES
(1, 'vAZCeYThjC6An7', 'lenovo', 4, 5000000.00, 2, '10878.jpg', '2025-05-01', 'Inactivo', '2025-05-28 20:32:22', 'H7YY7MINAndznR', 'lenovo', NULL, NULL, NULL, NULL, '4GB', '0'),
(2, '12345678901234', 'Computador Lenovo', 1, 10000.00, 1000, '115365.jpg', '2024-12-31', 'Activo', '2024-03-15 08:27:45', 'H7YY7MINAndznR', 'lenovo', NULL, NULL, NULL, NULL, 'Integrada', '#N/D'),
(3, '56789012340123', 'Computador 117', 2, 15000.00, 48, '341946.jpg', '2025-06-30', 'Activo', '2024-03-15 08:27:46', 'H7YY7MINAndznR', NULL, NULL, NULL, NULL, NULL, 'Integrada', 'SCRAP'),
(4, '67890123451234', 'Computador DELL', 3, 25000.00, 24, '680339.jpg', '2025-12-31', 'Activo', '2024-03-15 08:27:46', 'H7YY7MINAndznR', NULL, NULL, NULL, NULL, NULL, 'Integrada', ''),
(5, '78901234562345', 'Computador ASUS', 1, 12500.00, 81, '579718.jpg', '2024-10-31', 'Activo', '2024-03-15 08:27:46', 'H7YY7MINAndznR', NULL, NULL, NULL, NULL, NULL, 'Integrada', 'C'),
(6, '89012345673456', 'Computador Compax', 4, 18000.00, 58, '956303.jpg', '2024-08-31', 'Activo', '2024-03-15 08:27:46', 'H7YY7MINAndznR', NULL, NULL, NULL, NULL, NULL, 'Integrada', 'B'),
(7, 'H7YY7MINAndznR', 'Computador HP', 1, 229500.00, 999, '375961.png', '2025-04-01', 'Activo', '2024-03-21 19:19:20', 'H7YY7MINAndznR', 'HP', '16 GB', '256GB SSD', 'i7 8th', '14\"', '2 GB', 'A');

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `privado` int(2) DEFAULT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `celu` int(10) DEFAULT NULL,
  `correo` varchar(30) DEFAULT NULL,
  `dire` varchar(30) DEFAULT NULL,
  `cuiprov` varchar(30) DEFAULT NULL,
  `nomenclatura` varchar(10) DEFAULT NULL,
  `nit` int(10) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp()
) ;

INSERT INTO `proveedores` (`id`, `privado`, `nombre`, `celu`, `correo`, `dire`, `cuiprov`, `nomenclatura`, `nit`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'Proveedor Principal', 2147483647, 'proveedor1@email.com', 'Calle 123 #45-67', 'Bogotá', 'PRV001', NULL, '2025-07-14 17:20:31', '2025-07-14 17:20:31'),
(2, 1, 'Proveedor Secundario', 2147483647, 'proveedor2@email.com', 'Carrera 89 #12-34', 'Medellín', 'PRV002', NULL, '2025-07-14 17:20:31', '2025-07-14 17:20:31'),
(3, 1, 'SITEC', 2147483647, 'info@sitecsas.com', 'Av Suba #114-69 Local A07, Bog', 'Bogota', 'PRV003', 900432378, '2025-07-14 17:20:31', '2025-07-14 21:02:45'),
(8, 1, 'PcShek Tecnologia Y Servicios ', 2147483647, 'comercial@pcshek.com', 'TV 66 # 35 - 11 MD 3 BG 9', 'Bogota', 'PCSH', 900123456, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

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
) ;

INSERT INTO `servicio` (`idservc`, `idplan`, `ini`, `fin`, `idclie`, `estod`, `meto`, `canc`, `fere`, `servtxt`, `servfoto`, `responsable`) VALUES
(1, 1, '2024-03-15', '2024-05-08', 1, 'Activo', 'Nequi', 20000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(2, 2, '2024-03-15', '2024-05-17', 2, 'Inactivo', 'Nequi', 30000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(3, 3, '2023-08-18', '2024-04-06', 3, 'Activo', 'Nequi', 40000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(4, 4, '2023-09-22', '2025-02-21', 4, 'Activo', 'Nequi', 35000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(5, 6, '2023-10-25', '2024-04-04', 5, 'Activo', 'Transferencia', 25000.00, '2024-03-15 08:27:46', NULL, NULL, NULL),
(6, 3, '2023-04-05', '2025-06-19', 6, 'Activo', 'Tarjeta', 560000.00, '2024-03-21 06:28:16', NULL, 'portatil_display.jpeg', 'Juan David'),
(7, 0, '2025-05-30', '2025-06-12', 7, 'Activo', 'Nequi_Daviplata', 900000.00, '2025-05-30 17:21:20', 'El equipo enciende y funciona por salida externa. Display dañado físicamente, presenta manchas y líneas. Cliente solicita cambio de pantalla y mantenimiento general. 70', 'portatil_display.jpeg', 'José Borda'),
(8, 0, '2025-05-30', '2025-06-21', 2, 'Activo', 'Nequi_Daviplata', 9000000.00, '2025-05-30 17:24:54', 'El equipo enciende y funciona por salida externa. Display dañado físicamente, presenta manchas y líneas. Cliente solicita cambio de pantalla y mantenimiento general. 2', 'portatil_display.jpeg', 'Luis'),
(9, 5, '2025-06-12', '2025-06-19', 0, 'Activo', 'Nequi_Daviplata', 90000.00, '2025-06-12 22:35:50', 'El equipo enciende y funciona por salida externa. Display dañado físicamente, presenta manchas y líneas. Cliente solicita cambio de pantalla y mantenimiento general.', 'portatil_display.jpeg', 'José Borda'),
(10, 4, '2025-06-16', '0000-00-00', 5, 'Medellin', 'Transferencia', 90000.00, '2025-06-16 23:01:11', '', '', '');

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
) ;

INSERT INTO `setting` (`idsett`, `nomem`, `ruc`, `decrp`, `corr`, `direc1`, `direc2`, `celu`, `foto`) VALUES
(1, 'PCMARKET SAS', '901232273', 'PCMARKET SAS', 'comercial@pcmarkett.com', 'Cl. 14 #53-19, Bogotá,', '', '304 4177847', 'logo.jpg');

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
) ;

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `correo`, `clave`, `rol`, `foto`, `estado`, `fere`, `idsede`) VALUES
(1, 'FrankQV', 'frank', 'frank@admin.com', '202cb962ac59075b964b07152d234b70', '1', '1', '1', '2025-05-28 14:48:15', 'Cucuta'),
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
(16, 'Deivi Lopez', 'Deivilopezpc', 'dl123@data.com', 'facbcd76dde2c647198b1bab1d5d834d', '7', '1', '1', '2025-05-29 14:32:08', NULL),
(17, 'Maricela Tabla', 'Maricelatablapc', 'mt123@data.com', '0e57650e147ce827aec8b788db5a25ab', '3', '1', '1', '2025-05-29 14:32:29', ''),
(18, 'Ana Gaviria', 'Anagaviriapc', 'ag123@data.com', '30e5488c3c420588715fe3a51143e7ec', '3', '1', '1', '2025-05-29 14:32:51', NULL),
(19, 'Laura Pedraza', 'Laurapedrazapc', 'lp123@data.com', '39382aa4884af196f11ed8feba7d128f', '4', '1', '1', '2025-05-29 14:33:16', 'Unilago'),
(21, 'Gabriela Gutiérrez', 'gabrielagutierrezpc', 'gg123@data.com', '7d9bfd94d852319998c99d2c07980246', '4', '1', '1', '2025-05-29 14:33:42', 'Cucuta'),
(22, 'Mónica Valencia', 'Monicavalenciapc', 'mv123@data.com', '213a253bf5cce2d84e4032ace9e29aa7', '4', '1', '1', '2025-05-29 14:34:06', 'Principal'),
(28, 'frank2', 'frank2', 'frank2@gmail.com', '202cb962ac59075b964b07152d234b70', '2', '1', '1', '2025-06-06 21:40:21', '2'),
(29, 'frank3', 'frank3', 'frank3@gmail.com', '202cb962ac59075b964b07152d234b70', '3', '1', '1', '2025-06-09 20:07:48', 'Cucuta'),
(31, 'frank4', 'frank4', 'frank4@gmail.com', '202cb962ac59075b964b07152d234b70', '4', '1', '1', '2025-06-09 20:08:22', 'Unilago'),
(32, 'frank5', 'frank5', 'frank5@gmail.com', '202cb962ac59075b964b07152d234b70', '5', '1', '1', '2025-06-09 20:08:38', 'Medellin'),
(33, 'Tecnico FranciscoQV', 'frank6', 'frank6@gmail.com', '202cb962ac59075b964b07152d234b70', '6', '1', '1', '2025-06-09 20:09:04', NULL),
(34, 'frank7', 'frank7', 'frank7@gmail.com', '202cb962ac59075b964b07152d234b70', '7', '1', '1', '2025-06-09 20:09:18', NULL),
(35, 'salome', 'salome', 'salome@gmail.com', '202cb962ac59075b964b07152d234b70', '2', '1', '1', '2025-07-12 15:22:31', NULL);


ALTER TABLE `bodega_control_calidad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `tecnico_id` (`tecnico_id`);

ALTER TABLE `bodega_diagnosticos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `tecnico_id` (`tecnico_id`);

ALTER TABLE `bodega_entradas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD KEY `usuario_id` (`usuario_id`);

ALTER TABLE `bodega_inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_g` (`codigo_g`),
  ADD UNIQUE KEY `serial` (`serial`),
  ADD UNIQUE KEY `item` (`item`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `id_2` (`id`);

ALTER TABLE `bodega_partes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bodega_salidas`
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

ALTER TABLE `servicio`
  ADD PRIMARY KEY (`idservc`);

ALTER TABLE `setting`
  ADD PRIMARY KEY (`idsett`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `bodega_control_calidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_diagnosticos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_entradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_partes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bodega_salidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cart`
  MODIFY `idv` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cart_compra`
  MODIFY `idcarco` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoria`
  MODIFY `idcate` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `clientes`
  MODIFY `idclie` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `compra`
  MODIFY `idcomp` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `gastos`
  MODIFY `idga` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ingresos`
  MODIFY `iding` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `marketing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
  MODIFY `idord` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `plan`
  MODIFY `idplan` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `producto`
  MODIFY `idprod` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `servicio`
  MODIFY `idservc` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `setting`
  MODIFY `idsett` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
