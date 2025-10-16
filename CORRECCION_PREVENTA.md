# 🔧 CORRECCIÓN: venta/preventa.php - Solicitud de Alistamiento

**Fecha:** 16 de Octubre, 2025
**Archivo:** `public_html/venta/preventa.php`
**Problema:** No guarda datos en `solicitud_alistamiento`

---

## ❌ PROBLEMA RAÍZ

El código intentaba insertar en columnas que **NO EXISTEN** en la tabla `solicitud_alistamiento`.

### Query Original (INCORRECTO):
```php
INSERT INTO solicitud_alistamiento (
    solicitante,
    usuario_id,
    sede,
    despacho,          // ❌ NO EXISTE
    cliente,
    cliente_id,        // ❌ NO EXISTE
    tecnico_responsable,
    productos_json     // ❌ NO EXISTE
)
```

### Estructura REAL de la tabla:
```sql
CREATE TABLE `solicitud_alistamiento` (
  `id` int NOT NULL,
  `solicitante` varchar(255) NOT NULL,
  `usuario_id` int NOT NULL,
  `sede` varchar(100) NOT NULL,
  `cliente` varchar(255),
  `cantidad` varchar(1600) NOT NULL,      // ⚠️ Faltaba
  `descripcion` varchar(1600) NOT NULL,   // ⚠️ Faltaba
  `marca` varchar(100),
  `modelo` varchar(100),
  `observacion` varchar(1200),            // ⚠️ Faltaba
  `tecnico_responsable` int,
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` varchar(500) NOT NULL
)
```

---

## ✅ SOLUCIÓN IMPLEMENTADA

### 1. Mapeo de Datos

El formulario envía **múltiples productos** en formato JSON, pero la tabla **NO tiene columna** `productos_json`.

**Estrategia:**
- Convertir el array de productos en campos individuales
- Guardar JSON completo en `observacion` como backup

```php
// ANTES: Intentaba guardar JSON directamente
':productos_json' => $productos_json  // ❌ Columna no existe

// DESPUÉS: Mapeo a columnas existentes
':cantidad' => (string)$cantidad_total,
':descripcion' => $descripcion_completa,
':marca' => $primera_marca,
':modelo' => $primer_modelo,
':observacion' => $observacion_completa  // JSON va aquí
```

### 2. Construcción de Datos

```php
// Construir descripción y cantidad desde productos JSON
$cantidad_total = 0;
$descripcion_completa = '';
$primera_marca = '';
$primer_modelo = '';

foreach ($productos as $prod) {
    $cantidad_total += $prod['cantidad'];
    $descripcion_completa .= $prod['cantidad'] . 'x ' . $prod['descripcion'] . "\n";
    if (empty($primera_marca) && !empty($prod['marca'])) {
        $primera_marca = $prod['marca'];
    }
    if (empty($primer_modelo) && !empty($prod['modelo'])) {
        $primer_modelo = $prod['modelo'];
    }
}

$descripcion_completa = trim($descripcion_completa);
$observacion_completa = "Despacho: " . $despacho . " | Productos JSON: " . $productos_json;
```

### 3. Query Corregida

```php
$sql = "INSERT INTO solicitud_alistamiento (
            solicitante,
            usuario_id,
            sede,
            cliente,
            cantidad,              // ✅ Agregado
            descripcion,           // ✅ Agregado
            marca,                 // ✅ Agregado
            modelo,                // ✅ Agregado
            observacion,           // ✅ Agregado (contiene JSON + despacho)
            tecnico_responsable,
            estado                 // ✅ Agregado
        ) VALUES (
            :solicitante,
            :usuario_id,
            :sede,
            :cliente,
            :cantidad,
            :descripcion,
            :marca,
            :modelo,
            :observacion,
            :tecnico_responsable,
            'pendiente'
        )";
```

---

## 📊 EJEMPLO DE DATOS GUARDADOS

### Entrada del Formulario:
```javascript
productos = [
    {
        cantidad: 2,
        descripcion: "Laptop HP EliteBook i5 8GB 256SSD",
        marca: "HP",
        modelo: "EliteBook 840 G7",
        observacion: "Preferiblemente grado A"
    },
    {
        cantidad: 1,
        descripcion: "Mouse inalámbrico",
        marca: "Logitech",
        modelo: "M185",
        observacion: ""
    }
]
```

### Datos Guardados en BD:
```sql
solicitante = "Juan Pérez"
usuario_id = 5
sede = "Principal - Puente Aranda"
cliente = "Acme Corp"
cantidad = "3"                     -- 2 + 1
descripcion = "2x Laptop HP EliteBook i5 8GB 256SSD
1x Mouse inalámbrico"
marca = "HP"                       -- Primera marca encontrada
modelo = "EliteBook 840 G7"        -- Primer modelo encontrado
observacion = "Despacho: Coordinadora | Productos JSON: [{...}, {...}]"
tecnico_responsable = 1
estado = "pendiente"
```

---

## 🔄 LECTURA DE DATOS

Como los productos se guardan en formato texto + JSON en observacion, implementé extracción:

```php
// Extraer JSON de observacion
$productos_json_str = '';
if (preg_match('/Productos JSON: (.+)$/s', $sol['observacion'] ?? '', $matches)) {
    $productos_json_str = $matches[1];
}
$productos = $productos_json_str ? json_decode($productos_json_str, true) : [];

// Extraer despacho
$despacho_extracted = '';
if (preg_match('/Despacho: ([^|]+)/', $sol['observacion'] ?? '', $despacho_match)) {
    $despacho_extracted = trim($despacho_match[1]);
}
```

---

## 📋 MAPEO COMPLETO

| Formulario | Variable PHP | Columna BD | Tipo | Notas |
|------------|--------------|------------|------|-------|
| - | `$_SESSION['nombre']` | `solicitante` | varchar(255) | Auto |
| - | `$_SESSION['id']` | `usuario_id` | int | Auto |
| sede | `$sede` | `sede` | varchar(100) | Select |
| despacho | `$despacho` | `observacion` | varchar(1200) | En texto |
| cliente_nombre | `$cliente_nombre` | `cliente` | varchar(255) | Text/Auto |
| productos[].cantidad | `$cantidad_total` | `cantidad` | varchar(1600) | Suma |
| productos[].descripcion | `$descripcion_completa` | `descripcion` | varchar(1600) | Concatenado |
| productos[0].marca | `$primera_marca` | `marca` | varchar(100) | Primera |
| productos[0].modelo | `$primer_modelo` | `modelo` | varchar(100) | Primera |
| productos_json | `$productos_json` | `observacion` | varchar(1200) | JSON |
| tecnico_responsable | `$tecnico_responsable` | `tecnico_responsable` | int | Select |
| - | `'pendiente'` | `estado` | varchar(500) | Default |

---

## 🎯 CAMPOS NO UTILIZADOS

Estos datos del formulario **NO tienen columna directa** en la tabla:

1. **cliente_id** (int) → Solo se guarda `cliente` (nombre como texto)
2. **despacho** (select) → Se guarda en `observacion`
3. **productos_json** (JSON completo) → Se guarda en `observacion`

**Recomendación Futura:**
- Agregar columna `cliente_id` INT para relación con tabla `clientes`
- Agregar columna `despacho` VARCHAR(100)
- Agregar columna `productos_json` TEXT para guardar estructura completa

---

## ✅ PRUEBA DE FUNCIONAMIENTO

### Test 1: Solicitud con 1 Producto
```
Sede: Principal - Puente Aranda
Despacho: Coordinadora
Cliente: Acme Corp
Productos:
  - Cantidad: 5
  - Descripción: Laptop Dell Latitude i7 16GB
  - Marca: Dell
  - Modelo: Latitude 5420

✅ Resultado Esperado:
cantidad = "5"
descripcion = "5x Laptop Dell Latitude i7 16GB"
marca = "Dell"
modelo = "Latitude 5420"
```

### Test 2: Solicitud con Múltiples Productos
```
Productos:
  1. 2x HP EliteBook (HP, EliteBook 840)
  2. 3x Logitech Mouse (Logitech, M185)
  3. 1x Monitor Samsung (Samsung, 24")

✅ Resultado Esperado:
cantidad = "6"
descripcion = "2x HP EliteBook\n3x Logitech Mouse\n1x Monitor Samsung"
marca = "HP"              (primera)
modelo = "EliteBook 840"  (primero)
observacion = "Despacho: ... | Productos JSON: [{...}, {...}, {...}]"
```

---

## 🔍 VALIDACIONES ACTIVAS

```php
// 1. Sede obligatoria
if (empty($sede)) {
    throw new Exception('La sede es obligatoria');
}

// 2. Al menos un producto
if (empty($productos) || count($productos) === 0) {
    throw new Exception('Debe agregar al menos un producto');
}

// 3. Cliente (opcional - puede ser null)
$cliente_nombre = trim($_POST['cliente_nombre'] ?? '');

// 4. Técnico (opcional - puede ser null)
$tecnico_responsable = !empty($_POST['tecnico_responsable']) ? intval($_POST['tecnico_responsable']) : null;
```

---

## ⚙️ COMPORTAMIENTO DEL FORMULARIO

### JavaScript - Construcción de JSON
```javascript
$('#solicitudForm').on('submit', function(e) {
    e.preventDefault();

    const productos = [];
    $('.producto-row').each(function() {
        const cantidad = parseInt($(this).find('.cantidad-input').val()) || 0;
        const descripcion = $(this).find('.descripcion-input').val().trim();
        const marca = $(this).find('.marca-input').val().trim();
        const modelo = $(this).find('.modelo-input').val().trim();
        const observacion = $(this).find('.observacion-input').val().trim();

        // Solo agregar si tiene descripción
        if (descripcion) {
            productos.push({
                cantidad: cantidad,
                descripcion: descripcion,
                marca: marca,
                modelo: modelo,
                observacion: observacion
            });
        }
    });

    // Validar que hay al menos un producto
    if (productos.length === 0) {
        alert('Debe agregar al menos un producto con descripción');
        return false;
    }

    // Guardar JSON en campo oculto
    $('#productos_json').val(JSON.stringify(productos));

    // Enviar formulario
    this.submit();
});
```

---

## 📝 ESTRUCTURA DE observacion

El campo `observacion` guarda dos cosas:

```
Despacho: [método_despacho] | Productos JSON: [array_json]
```

**Ejemplo:**
```
Despacho: Coordinadora | Productos JSON: [{"cantidad":2,"descripcion":"Laptop HP","marca":"HP","modelo":"EliteBook 840","observacion":""},{"cantidad":1,"descripcion":"Mouse","marca":"Logitech","modelo":"M185","observacion":""}]
```

**Extracción:**
```php
// Extraer despacho
preg_match('/Despacho: ([^|]+)/', $observacion, $match);
$despacho = trim($match[1]);

// Extraer JSON
preg_match('/Productos JSON: (.+)$/s', $observacion, $match);
$json = $match[1];
$productos = json_decode($json, true);
```

---

## 🚨 LIMITACIONES ACTUALES

1. **Longitud de observacion:**
   - Columna: `varchar(1200)`
   - Si hay muchos productos, el JSON puede superar el límite
   - **Solución:** Agregar columna `productos_json TEXT`

2. **Sin relación con tabla clientes:**
   - Se guarda solo el nombre como texto
   - **Solución:** Agregar columna `cliente_id INT`

3. **Despacho mezclado en observacion:**
   - Dificulta búsquedas y reportes
   - **Solución:** Agregar columna `despacho VARCHAR(100)`

---

## ✅ ESTADO FINAL

**🟢 SISTEMA FUNCIONAL**

### Antes
- ❌ Error SQL: columnas inexistentes
- ❌ No guardaba datos
- ❌ Query incompatible con estructura

### Después
- ✅ INSERT corregido
- ✅ Mapeo correcto de datos
- ✅ Productos convertidos a texto
- ✅ JSON guardado en observacion
- ✅ Transacciones con rollback
- ✅ Extracción de datos funcionando

---

## 🔄 RECOMENDACIÓN: MIGRACIÓN DE TABLA

Para mejorar el sistema, sugiero ejecutar este ALTER TABLE:

```sql
-- Agregar columnas faltantes
ALTER TABLE `solicitud_alistamiento`
ADD COLUMN `cliente_id` INT NULL AFTER `cliente`,
ADD COLUMN `despacho` VARCHAR(100) NULL AFTER `sede`,
ADD COLUMN `productos_json` TEXT NULL AFTER `observacion`,
ADD INDEX `idx_cliente_id` (`cliente_id`),
ADD FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`idclie`) ON DELETE SET NULL;
```

**Después de la migración,** actualizar el INSERT a:
```php
$sql = "INSERT INTO solicitud_alistamiento (
            solicitante, usuario_id, sede, despacho, cliente, cliente_id,
            cantidad, descripcion, marca, modelo, observacion,
            productos_json, tecnico_responsable, estado
        ) VALUES (...)";
```

---

**Corrección realizada por:** Claude Code
**Fecha:** 16 de Octubre, 2025
**Estado:** ✅ FUNCIONAL (con limitaciones estructurales)
