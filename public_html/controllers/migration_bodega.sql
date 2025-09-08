-- SQL Migraciones Bodega: ordenes, carrito, compras, ingresos, alter inventario, alter salidas

-- bodega_ordenes
CREATE TABLE IF NOT EXISTS bodega_ordenes (
  idord INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  responsable INT NOT NULL,
  total_items INT NOT NULL DEFAULT 0,
  total_pago DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  fecha_pago DATETIME DEFAULT NULL,
  metodo_pago VARCHAR(100) DEFAULT NULL,
  estado_pago ENUM('Debe_plata','Pendiente','Aceptado','total_pagado') DEFAULT 'Pendiente',
  tipo_doc ENUM('factura','ticket','remision') DEFAULT 'ticket',
  num_documento VARCHAR(100) DEFAULT NULL,
  evidencia_pago VARCHAR(255) DEFAULT NULL,
  despachado_en TEXT DEFAULT NULL,
  creado_por INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX (cliente_id),
  INDEX (responsable)
);

-- bodega_cart_compra
CREATE TABLE IF NOT EXISTS bodega_cart_compra (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  inventario_id INT NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  precio_unit DECIMAL(12,2) DEFAULT NULL,
  subtotal DECIMAL(12,2) GENERATED ALWAYS AS (qty * IFNULL(precio_unit,0)) STORED,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (usuario_id),
  INDEX (inventario_id)
);

-- bodega_compra
CREATE TABLE IF NOT EXISTS bodega_compra (
  id INT AUTO_INCREMENT PRIMARY KEY,
  proveedor_id INT DEFAULT NULL,
  usuario_id INT NOT NULL,
  total_compra DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
  estado ENUM('pendiente','recibido','cancelado') DEFAULT 'pendiente',
  tipo_doc ENUM('factura','remision','otro') DEFAULT 'factura',
  referencia VARCHAR(150) DEFAULT NULL,
  evidencia VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- bodega_ingresos
CREATE TABLE IF NOT EXISTS bodega_ingresos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  orden_id INT NOT NULL,
  monto DECIMAL(12,2) NOT NULL,
  fecha_ingreso DATETIME DEFAULT CURRENT_TIMESTAMP,
  metodo_pago VARCHAR(100) DEFAULT NULL,
  referencia_pago VARCHAR(150) DEFAULT NULL,
  recibido_por INT DEFAULT NULL,
  notas TEXT DEFAULT NULL,
  INDEX (orden_id)
);

-- ALTER bodega_inventario (precio, foto)
ALTER TABLE bodega_inventario
  ADD COLUMN IF NOT EXISTS precio VARCHAR(250) NOT NULL AFTER activo_fijo,
  ADD COLUMN IF NOT EXISTS foto VARCHAR(250) NOT NULL AFTER precio;

-- ALTER bodega_salidas (campos despacho + itemización)
ALTER TABLE bodega_salidas
  ADD COLUMN IF NOT EXISTS orden_id INT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS cliente_id INT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS responsable INT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS despacho_fecha DATETIME DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS estado_despacho ENUM('pendiente','en_ruta','entregado','cancelado') DEFAULT 'pendiente',
  ADD COLUMN IF NOT EXISTS guia_remision VARCHAR(200) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS transportista VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS evidencia_foto VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS inventario_id INT NULL,
  ADD COLUMN IF NOT EXISTS precio_unit DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS serial VARCHAR(120) DEFAULT NULL;


