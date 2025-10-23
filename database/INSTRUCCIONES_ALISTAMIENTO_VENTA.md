# Sistema de Alistamiento de Ventas - PCMARKETTEAM

## 📋 Resumen de Correcciones Realizadas

### ✅ Problemas Corregidos

1. **Búsqueda de Clientes - CORREGIDO**
   - ❌ Antes: Usaba `numbid` (columna incorrecta)
   - ✅ Ahora: Usa `numid` (columna correcta de la tabla)
   - ✅ Busca en: `numid`, `nomcli`, `apecli`, `correo`, `celu`

2. **Información del Cliente - MEJORADO**
   - ✅ Muestra: Nombre completo, teléfono, sede del cliente
   - ✅ Auto-rellena la dirección de envío desde `dircli` y `ciucli`

3. **Búsqueda de Inventario - MEJORADO**
   - ✅ Muestra productos con grado **A** y **B**
   - ✅ Filtra por estado = 'activo'
   - ✅ Excluye solo disposición = 'Vendido'
   - ✅ **AHORA MUESTRA**: en_proceso, en_diagnostico, en_revision, Por Alistamiento, disponible, etc.
   - ✅ Muestra: código, serial, táctil, pulgadas, ubicación, lote, precio
   - ✅ Límite aumentado a 50 productos

4. **Visualización de Items - MEJORADO**
   - ✅ Tabla responsive con toda la información
   - ✅ Muestra grado del equipo con badge de color
   - ✅ Indica si es táctil
   - ✅ Distingue entre productos de inventario vs manuales
   - ✅ Muestra código del equipo
   - ✅ Permite editar cantidad y precio

---

## 🚀 Instalación

### Paso 1: Ejecutar SQL
```bash
# Opción 1: Desde MySQL CLI
mysql -u u171145084_pcmteam -p u171145084_pcmteam < C:\laragon\www\pcmteam\database\alistamiento_venta.sql

# Opción 2: Desde phpMyAdmin
# 1. Ir a phpMyAdmin
# 2. Seleccionar base de datos u171145084_pcmteam
# 3. Ir a pestaña "SQL"
# 4. Copiar y pegar contenido de alistamiento_venta.sql
# 5. Ejecutar
```

### Paso 2: Verificar Archivos Creados
```
✅ database/alistamiento_venta.sql
✅ public_html/comercial/alistamiento_venta.php
✅ backend/php/alistamiento_api.php
✅ public_html/assets/js/alistamiento_venta.js
```

### Paso 3: Agregar al Menú
Editar `public_html/layouts/menu_data.php` y agregar:

```php
[
    'title' => 'Alistamiento Ventas',
    'icon' => 'shopping_cart',
    'url' => '../comercial/alistamiento_venta.php',
    'roles' => [1, 4] // Admin y Comercial
]
```

---

## 📊 Estructura de Base de Datos

### Tablas Creadas

#### 1. `alistamiento_venta` (Encabezado)
```sql
- id                  INT PRIMARY KEY
- idventa             VARCHAR(50) UNIQUE (AV-2025-0001)
- ticket              VARCHAR(160) UNIQUE
- fecha_venta         DATETIME
- usuario_id          INT (FK usuarios)
- sede                VARCHAR(150)
- idcliente           INT (FK clientes)
- ubicacion           VARCHAR(250)
- numguia_envio       VARCHAR(250)
- subtotal            DECIMAL(12,2)
- descuento           DECIMAL(12,2)
- total_venta         DECIMAL(12,2)
- valor_abono         DECIMAL(12,2)
- saldo               DECIMAL(12,2)
- medio_abono         ENUM
- estado              ENUM (borrador, pendiente, aprobado...)
- observacion_global  TEXT
- observacion_tecnico TEXT
```

#### 2. `alistamiento_venta_items` (Detalle)
```sql
- id                  INT PRIMARY KEY
- alistamiento_id     INT (FK alistamiento_venta)
- item_numero         INT
- inventario_id       INT (FK bodega_inventario) - PUEDE SER NULL
- producto            VARCHAR(150)
- marca, modelo, procesador, ram, disco, grado
- cantidad            INT
- precio_unitario     DECIMAL(12,2)
- subtotal            DECIMAL (CALCULADO AUTOMÁTICO)
```

#### 3. `alistamiento_venta_archivos` (Archivos - Opcional)
```sql
- id                  INT PRIMARY KEY
- alistamiento_id     INT
- nombre_archivo      VARCHAR(255)
- ruta_archivo        VARCHAR(500)
```

### Triggers Automáticos
- ✅ Calcula `subtotal` automáticamente al agregar items
- ✅ Calcula `total_venta` = subtotal - descuento
- ✅ Calcula `saldo` = total - abono
- ✅ Se ejecuta en INSERT, UPDATE y DELETE de items

---

## 🎯 Funcionalidades

### ✅ Crear Nueva Venta

1. **Buscar Cliente**
   - Input con autocompletado (Select2)
   - Busca por: NIT, nombre, apellido, correo, celular
   - Muestra información del cliente seleccionado
   - Auto-rellena dirección de envío

2. **Información General**
   - Sede (auto-detectada del usuario)
   - Ticket (manual)
   - Ubicación de envío

3. **Agregar Productos**

   **Opción A: Desde Inventario**
   - Click en "Buscar en Inventario"
   - Busca por: producto, marca, modelo, procesador, ram, disco, código, serial
   - Muestra: Grado (A/B), táctil, disposición, precio, ubicación
   - Click en producto para agregar

   **Opción B: Manual**
   - Click en "Agregar Manual"
   - Llenar formulario manualmente
   - Útil para productos no registrados en inventario

4. **Información Financiera**
   - Subtotal (calculado automático)
   - Descuento (opcional)
   - Total a Pagar (calculado)
   - Valor Abono + Medio de Pago
   - Saldo Pendiente (calculado)

5. **Guardar**
   - **Guardar como Borrador**: Estado = 'borrador'
   - **Guardar y Aprobar**: Estado = 'aprobado'

### ✅ Gestionar Ventas

- **Ver Detalle**: Muestra toda la información de la venta
- **Cambiar Estado**: Workflow completo de estados
- **Eliminar**: Solo en estado borrador o cancelado

### Estados del Proceso

```
borrador → pendiente → aprobado → en_alistamiento →
alistado → despachado → en_transito → entregado

↓ (en cualquier momento)
cancelado
```

---

## 🎨 Características Visuales

### Colores por Grado
- **Grado A**: Verde (#00CC54)
- **Grado B**: Amarillo (#F0DD00)
- **Grado C**: Rojo (#CC0618)

### Badges de Estado
- Borrador: Gris
- Pendiente: Rojo
- Aprobado: Azul
- En Alistamiento: Amarillo
- Alistado: Verde
- Despachado: Morado
- Entregado: Verde
- Cancelado: Rojo oscuro

### Badges de Disposición (Inventario)
- en_proceso: warning (amarillo)
- en_diagnostico: info (azul claro)
- en_revision: secondary (gris)
- Por Alistamiento: primary (azul)
- disponible: success (verde)

---

## 🔧 Tecnologías Utilizadas

- **Backend**: PHP 7.4+ con PDO
- **Base de Datos**: MySQL 5.7+
- **Frontend**: Bootstrap 4/5, jQuery 3.x
- **Librerías**:
  - DataTables (tablas interactivas)
  - Select2 (autocompletado)
  - SweetAlert2 (alertas modernas)
  - Material Icons (iconografía)

---

## 📝 Ejemplos de Uso

### Crear Venta desde Inventario
```javascript
1. Click "Nueva Venta"
2. Buscar cliente: "Juan" → Seleccionar
3. Ticket: "TKT-2025-001"
4. Click "Buscar en Inventario"
5. Buscar: "lenovo i5" → Seleccionar producto
6. Ajustar cantidad/precio si necesario
7. Ingresar abono: $500,000
8. Seleccionar medio: Transferencia
9. Click "Guardar y Aprobar"
```

### Crear Venta Manual
```javascript
1. Click "Nueva Venta"
2. Buscar cliente → Seleccionar
3. Ticket: "TKT-2025-002"
4. Click "Agregar Manual"
5. Producto: Mouse Inalámbrico
6. Marca: Logitech
7. Cantidad: 2
8. Precio: $35,000
9. Click "Agregar"
10. Click "Guardar como Borrador"
```

---

## 🐛 Solución de Problemas

### Problema: No aparecen clientes al buscar
**Solución**: Verificar que:
- ✅ La tabla `clientes` tiene la columna `numid` (no `numbid`)
- ✅ Hay clientes en la base de datos
- ✅ La API está respondiendo: Ver Console del navegador (F12)

### Problema: No aparecen productos del inventario
**Solución**: Verificar que:
- ✅ Hay productos con `estado = 'activo'`
- ✅ Hay productos con `grado IN ('A', 'B')`
- ✅ Los productos NO tienen `disposicion = 'Vendido'`

### Problema: No se calculan los totales
**Solución**: Verificar que:
- ✅ Los triggers se crearon correctamente
- ✅ Ejecutar: `SHOW TRIGGERS LIKE 'alistamiento_venta_items';`

---

## 📞 Soporte

Para reportar errores o solicitar mejoras:
1. Verificar este documento primero
2. Revisar la consola del navegador (F12)
3. Revisar logs de PHP
4. Contactar al desarrollador con capturas de pantalla del error

---

## 🔄 Changelog

### v1.0.0 (2025-01-23)
- ✅ Sistema completo de alistamiento de ventas
- ✅ Integración con inventario, clientes y usuarios
- ✅ Búsqueda de clientes corregida (numid vs numbid)
- ✅ Filtro de inventario mejorado (más disposiciones)
- ✅ Visualización mejorada de items
- ✅ Cálculos automáticos de totales
- ✅ 4 archivos únicamente (minimalista)

---

**Desarrollado para PCMARKETTEAM** 🚀
