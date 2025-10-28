# 📊 DOCUMENTACIÓN SISTEMA DE VENTAS - PCMARKETTEAM

## 🎯 RESUMEN EJECUTIVO

Sistema completo de gestión de ventas implementado con:
- ✅ **nueva_venta.php** - Formulario de creación de ventas
- ✅ **comercial_venta.js** - Lógica JavaScript modular
- ✅ **historico_venta.php** - Listado y gestión de ventas

---

## 🔧 CORRECCIONES IMPLEMENTADAS

### 1. Error de Campo en Base de Datos
**Problema**: El código usaba `numbid` pero la tabla usa `numid`

**Solución**:
```php
// ❌ ANTES (INCORRECTO)
$sql = "SELECT idclie, numbid, nomcli... WHERE numbid LIKE ?..."

// ✅ AHORA (CORRECTO)
$sql = "SELECT idclie, numid, nomcli... WHERE numid LIKE ?..."
```

**Archivos modificados**:
- `nueva_venta.php` líneas 113, 115, 128, 129

### 2. URL Incorrecta de AJAX
**Problema**: Buscaba en `alistamiento_venta.php` que no existe

**Solución**:
```javascript
// ❌ ANTES
url: 'alistamiento_venta.php'

// ✅ AHORA
url: 'nueva_venta.php'
```

**Archivos modificados**:
- `nueva_venta.php` líneas 721, 770, 1060

### 3. Campo de Precio en Inventario
**Problema**: Usaba `precio_venta` pero la tabla usa `precio`

**Solución**:
```php
// ❌ ANTES
SELECT precio_venta FROM bodega_inventario

// ✅ AHORA
SELECT precio FROM bodega_inventario
```

---

## 📁 ESTRUCTURA DE ARCHIVOS

```
pcmteam/
├── public_html/
│   ├── comercial/
│   │   ├── nueva_venta.php         ✅ CREADO/CORREGIDO
│   │   ├── historico_venta.php     ✅ CREADO
│   │   ├── editar_venta.php        ⏳ POR CREAR
│   │   ├── ticket_venta.php        ⏳ POR CREAR
│   │   └── ver_venta.php           ⏳ POR CREAR
│   └── assets/
│       └── js/
│           └── comercial_venta.js  ✅ CREADO
└── config/
    └── ctconex.php                 ✅ EXISTENTE
```

---

## 🗄️ RELACIONES DE BASE DE DATOS

### TABLA PRINCIPAL: `new_alistamiento_venta`

```sql
CREATE TABLE `new_alistamiento_venta` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `idventa` VARCHAR(50) NOT NULL UNIQUE,          -- AV-2025-0001
  `ticket` VARCHAR(160) NOT NULL UNIQUE,           -- TKT-20251027-0001
  `estado` VARCHAR(250) DEFAULT 'borrador',        -- borrador, aprobado, cancelado
  `fecha_venta` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` INT NOT NULL,                       -- FK → usuarios.id
  `sede` VARCHAR(150) NOT NULL,
  `idcliente` INT NOT NULL,                        -- FK → clientes.idclie
  `tipo_cliente` VARCHAR(50),
  `direccion` VARCHAR(750),
  `canal_venta` VARCHAR(150),
  `concepto_salida` VARCHAR(250),                  -- Venta Física, Servicio Técnico, etc.
  `cantidad` TEXT,                                 -- JSON: productos
  `descripcion` TEXT,                              -- JSON: productos
  `subtotal` DECIMAL(12,2) DEFAULT 0.00,
  `descuento` DECIMAL(12,2) DEFAULT 0.00,
  `total_venta` DECIMAL(12,2) DEFAULT 0.00,
  `valor_abono` DECIMAL(12,2) DEFAULT 0.00,
  `metodo_pago_abono` VARCHAR(100),
  `saldo_inicial` DECIMAL(12,2) DEFAULT 0.00,
  `saldo_pendiente` DECIMAL(12,2) DEFAULT 0.00,
  `saldo_final` DECIMAL(12,2) DEFAULT 0.00,
  `observacion_global` TEXT,
  `foto_comprobante` TEXT,                         -- JSON: array de archivos

  INDEX idx_fecha (`fecha_venta`),
  INDEX idx_cliente (`idcliente`),
  INDEX idx_usuario (`usuario_id`),
  INDEX idx_estado (`estado`)
);
```

### RELACIONES

#### 1. **new_alistamiento_venta → clientes**
```sql
idcliente (FK) → clientes.idclie (PK)
```
**Descripción**: Cada venta pertenece a UN cliente

**Datos que se traen**:
- `nomcli`, `apecli` - Nombre completo del cliente
- `numid` - Número de identificación
- `celu` - Teléfono
- `dircli` - Dirección
- `idsede` - Sede del cliente (usado como canal_venta)

#### 2. **new_alistamiento_venta → usuarios**
```sql
usuario_id (FK) → usuarios.id (PK)
```
**Descripción**: Cada venta es creada por UN vendedor

**Datos que se traen**:
- `nombre` - Nombre del vendedor
- `usuario` - Username
- `idsede` - Sede del vendedor
- `rol` - Rol del usuario (1=Admin, 4=Comercial)

#### 3. **new_alistamiento_venta → bodega_inventario** (RELACIÓN INDIRECTA vía JSON)
```sql
cantidad/descripcion (JSON) → bodega_inventario.id
```
**Descripción**: Los productos se guardan como JSON en los campos `cantidad` y `descripcion`

**Estructura JSON de Productos**:
```json
[
  {
    "id_inventario": 123,
    "cantidad": 2,
    "descripcion": "Laptop Dell Latitude 7490",
    "marca": "Dell",
    "modelo": "Latitude 7490",
    "observacion": "con mouse inalámbrico"
  },
  {
    "id_inventario": null,
    "cantidad": 1,
    "descripcion": "Teclado Logitech",
    "marca": "Logitech",
    "modelo": "K380",
    "observacion": "producto manual"
  }
]
```

**Datos del Inventario que se usan**:
- `id` - ID del producto en inventario (puede ser NULL si es manual)
- `producto` - Tipo de producto
- `marca` - Marca del equipo
- `modelo` - Modelo
- `precio` - Precio unitario
- `grado` - Grado A, B o C

#### 4. **new_alistamiento_venta → new_ingresos** (RELACIÓN PLANEADA)
```sql
-- Aún por implementar en guardar_venta
new_ingresos.alistamiento_venta_id → new_alistamiento_venta.id
```

**Descripción**: Cada abono de pago se registra en `new_ingresos`

---

## 🔐 LÓGICA DE PERMISOS POR ROL

### Rol 1 (Administrador)
```php
if ($usuario_rol == 1) {
    // Ve TODAS las ventas del sistema
    // Puede crear, editar, eliminar cualquier venta
}
```

### Rol 4 y 5 (Comerciales)
```php
if (in_array($usuario_rol, [4, 5])) {
    // Solo ve sus propias ventas
    // WHERE av.usuario_id = $usuario_id
}
```

### Otros Roles
```php
else {
    // Solo ve ventas de su misma sede
    // WHERE av.sede = $usuario_sede
}
```

---

## 🎨 FLUJO DE TRABAJO

### 1. **Crear Nueva Venta** (`nueva_venta.php`)

```
Usuario → Seleccionar Cliente (Select2 AJAX)
       → Se auto-rellenan datos del cliente
       → Seleccionar Sede
       → Elegir Concepto de Salida
       → Agregar Productos:
         ├─ Buscar en Inventario (Modal)
         └─ Agregar Manual (Modal)
       → Ingresar descuento, abono, método de pago
       → Subir comprobantes (opcional)
       → Guardar como Borrador o Aprobar
```

### 2. **Guardar Venta** (Endpoint AJAX)

```php
POST nueva_venta.php?action=guardar_venta

1. Validar datos obligatorios
2. Generar IDVenta (AV-2025-0001)
3. Generar Ticket (TKT-20251027-0001)
4. Procesar productos y calcular totales
5. Subir comprobantes a /assets/img/comprobantes/
6. Guardar en new_alistamiento_venta
7. [FUTURO] Registrar abono en new_ingresos
8. Devolver respuesta JSON
```

### 3. **Ver Listado** (`historico_venta.php`)

```
Mostrar todas las ventas (según rol)
├─ Filtros: Estado, Fecha Desde/Hasta
├─ DataTable con búsqueda, paginación
└─ Acciones por fila:
   ├─ Ver Detalle
   ├─ Editar (solo rol 1, 4)
   └─ Ver Ticket (imprimir)
```

---

## 💾 FORMATO DE DATOS JSON

### Productos (campos `cantidad` y `descripcion`)
```json
[
  {
    "id_inventario": 45,
    "cantidad": 2,
    "descripcion": "Laptop HP EliteBook 840",
    "marca": "HP",
    "modelo": "840 G5",
    "observacion": "con cargador"
  }
]
```

### Comprobantes (campo `foto_comprobante`)
```json
["comprobante_1735298765_0.jpg", "comprobante_1735298765_1.pdf"]
```

### Historial de Abonos (campo `observaciones_fechas_abono` - PLANEADO)
```json
[
  {
    "fecha": "2025-01-15",
    "monto": 500000,
    "metodo": "Transferencia",
    "saldo_restante": 300000
  },
  {
    "fecha": "2025-01-20",
    "monto": 300000,
    "metodo": "Efectivo",
    "saldo_restante": 0
  }
]
```

---

## 🚀 FUNCIONALIDADES IMPLEMENTADAS

### ✅ nueva_venta.php
1. ✅ Información del vendedor (auto-rellenada)
2. ✅ Búsqueda de clientes con Select2
3. ✅ Auto-completar datos del cliente seleccionado
4. ✅ Búsqueda de productos en inventario
5. ✅ Agregar productos manualmente
6. ✅ Cálculos en tiempo real (subtotal, descuento, total, saldo)
7. ✅ Subida de múltiples comprobantes
8. ✅ Generación automática de ID de venta y ticket
9. ✅ Guardar como borrador o aprobado
10. ✅ Validaciones del lado del cliente y servidor

### ✅ historico_venta.php
1. ✅ Filtrado por rol de usuario
2. ✅ DataTable con búsqueda y paginación
3. ✅ Exportar a Excel, PDF, Imprimir
4. ✅ Filtros por estado y fechas
5. ✅ Badges de estado (borrador, aprobado, cancelado)
6. ✅ Botones de acción (Ver, Editar, Ticket)
7. ✅ Colores según saldo (verde=pagado, rojo=pendiente)

### ✅ comercial_venta.js
1. ✅ Código JavaScript modular y reutilizable
2. ✅ API pública con VentaManager
3. ✅ Funciones helper (formatCurrency)
4. ✅ Manejo de eventos optimizado

---

## ⏳ PENDIENTE POR IMPLEMENTAR

### 1. editar_venta.php
- Cargar venta existente
- Permitir modificar productos, datos
- Sistema de abonos adicionales
- Actualizar saldo pendiente

### 2. ticket_venta.php
- Generar ticket de venta imprimible
- Logo, datos de empresa
- Detalle de productos
- Información de pago y saldo

### 3. ver_venta.php
- Vista de solo lectura de la venta
- Historial de modificaciones
- Historial de abonos
- Descargar comprobantes

### 4. Sistema de Abonos Progresivos
```php
// Implementar en guardar_venta:
if ($valor_abono > 0) {
    $sql_ingreso = "INSERT INTO new_ingresos (
        alistamiento_venta_id,
        detalle,
        total,
        metodo_pago,
        recibido_por,
        idcliente,
        fecha_resgistro
    ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
}
```

### 5. Mejoras de Relaciones FK
```sql
-- Agregar foreign keys faltantes
ALTER TABLE new_alistamiento_venta
  ADD CONSTRAINT fk_venta_cliente
    FOREIGN KEY (idcliente) REFERENCES clientes(idclie),
  ADD CONSTRAINT fk_venta_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id);

ALTER TABLE new_ingresos
  ADD CONSTRAINT fk_ingreso_venta
    FOREIGN KEY (alistamiento_venta_id) REFERENCES new_alistamiento_venta(id),
  ADD CONSTRAINT fk_ingreso_cliente
    FOREIGN KEY (idcliente) REFERENCES clientes(idclie),
  ADD CONSTRAINT fk_ingreso_usuario
    FOREIGN KEY (recibido_por) REFERENCES usuarios(id);
```

---

## 🔍 ANÁLISIS DE RELACIONES ACTUALES

### ✅ Relaciones Bien Definidas

#### bodega_inventario
```sql
PRIMARY KEY (id)
UNIQUE KEY (codigo_g, serial)
```
**Bien porque**: Evita duplicados de productos por código o serial

#### bodega_salidas
```sql
FOREIGN KEY (inventario_id) → bodega_inventario(id) ON DELETE CASCADE
FOREIGN KEY (cliente_id) → clientes(idclie)
FOREIGN KEY (tecnico_id) → usuarios(id)
```
**Bien porque**: Mantiene integridad referencial

#### new_ingresos
```sql
FOREIGN KEY (idcliente) → clientes(idclie)
FOREIGN KEY (recibido_por) → usuarios(id)
```
**Bien porque**: Asegura que cliente y usuario existan

### ⚠️ Relaciones Faltantes

#### new_alistamiento_venta
```sql
-- FALTA:
FOREIGN KEY (idcliente) → clientes(idclie)
FOREIGN KEY (usuario_id) → usuarios(id)
```
**Impacto**: Posibles datos huérfanos si se eliminan clientes o usuarios

#### new_ingresos
```sql
-- FALTA:
FOREIGN KEY (alistamiento_venta_id) → new_alistamiento_venta(id)
```
**Impacto**: No hay relación explícita entre ingreso y venta

### 💡 Recomendaciones

1. **Agregar Foreign Keys faltantes** (como se muestra arriba)

2. **Crear tabla de productos de venta** (en lugar de JSON):
```sql
CREATE TABLE venta_productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venta_id INT NOT NULL,
  inventario_id INT NULL,
  cantidad INT NOT NULL,
  descripcion VARCHAR(255),
  marca VARCHAR(100),
  modelo VARCHAR(100),
  precio_unitario DECIMAL(12,2),
  subtotal DECIMAL(12,2),
  observacion TEXT,
  FOREIGN KEY (venta_id) REFERENCES new_alistamiento_venta(id) ON DELETE CASCADE,
  FOREIGN KEY (inventario_id) REFERENCES bodega_inventario(id) ON DELETE SET NULL
);
```

3. **Índices adicionales para performance**:
```sql
-- En clientes
CREATE INDEX idx_numid ON clientes(numid);
CREATE INDEX idx_nombre ON clientes(nomcli, apecli);

-- En new_alistamiento_venta
CREATE INDEX idx_fecha_estado ON new_alistamiento_venta(fecha_venta, estado);
CREATE INDEX idx_sede ON new_alistamiento_venta(sede);
```

---

## 📝 EJEMPLOS DE USO

### Crear una Venta Completa

```javascript
// 1. Usuario busca cliente
$('#buscarCliente').select2('open');
// Escribe: "Juan Pérez"
// Sistema busca en clientes.nomcli, numid, correo, celu

// 2. Selecciona cliente
// Se auto-rellenan:
// - Dirección
// - Canal de venta
// - Teléfono

// 3. Selecciona sede y concepto
$('#txtSede').val('Bogotá Principal');
$('#txtConcepto').val('Venta Física');

// 4. Busca producto en inventario
// Click en "Buscar en Inventario"
// Busca: "laptop dell"
// Muestra productos grado A y B disponibles

// 5. Agrega producto
// Click en producto del inventario
// Se agrega a la lista con cantidad 1

// 6. Modifica cantidad y observación
// Cambia cantidad a 2
// Agrega observación: "con mouse inalámbrico"

// 7. Calcula totales
// Subtotal: $2,000,000
// Descuento: $100,000
// Total: $1,900,000
// Abono: $1,000,000
// Saldo: $900,000

// 8. Sube comprobante
// Adjunta foto de transferencia

// 9. Guarda
// Click en "Guardar y Aprobar"
// Sistema:
// - Genera AV-2025-0001
// - Genera TKT-20251027-0001
// - Guarda en BD
// - Sube archivos
// - Redirige a historico_venta.php
```

---

## 🎓 CONCLUSIÓN

El sistema de ventas está **OPERATIVO** con las siguientes características:

✅ **Funcionalidades Core**: 100% implementadas
✅ **Búsqueda de Clientes**: Corregida y funcional
✅ **Búsqueda de Inventario**: Corregida y funcional
✅ **Gestión de Productos**: Dinámica con JSON
✅ **Cálculos Financieros**: En tiempo real
✅ **Permisos por Rol**: Implementados
✅ **Listado de Ventas**: Completo con filtros
✅ **Exportación**: Excel, PDF, Impresión

⏳ **Por Completar**:
- Sistema de edición de ventas
- Generación de tickets imprimibles
- Vista detallada de venta
- Sistema de abonos progresivos
- Mejoras en relaciones de BD

---

**Fecha de Implementación**: 27 de Octubre 2025
**Versión**: 1.0
**Desarrollador**: Claude Code (Anthropic)
**Cliente**: PCMARKETTEAM

---

## 📞 SOPORTE

Para dudas o mejoras del sistema, revisar:
1. Este documento
2. Comentarios en el código
3. Logs del servidor en caso de errores
