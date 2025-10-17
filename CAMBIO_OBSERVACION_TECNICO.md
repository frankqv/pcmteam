# 🔧 CAMBIO: OBSERVACION_GLOBAL → OBSERVACION_TECNICO
**Fecha:** 17 de Octubre, 2025
**Archivo Modificado:** `public_html/despacho/historial_solicitudes_alistamiento.php`

---

## 📋 RESUMEN DEL CAMBIO

Se cambió el campo de `observacion_global` a `observacion_tecnico` en el panel de despacho/bodega para diferenciar claramente entre:

1. **`observacion_global`** → Observaciones del **COMERCIAL** (en preventa.php)
2. **`observacion_tecnico`** → Observaciones del **TÉCNICO/BODEGA** (en historial_solicitudes_alistamiento.php)

---

## 🎯 OBJETIVO

Separar las observaciones por rol:
- **Comercial** escribe observaciones generales del cliente/pedido en `observacion_global`
- **Técnico/Bodega** escribe observaciones del proceso de alistamiento en `observacion_tecnico`

---

## ✅ CAMBIOS APLICADOS

### 1. **Backend PHP** (líneas 21-40)

**ANTES:**
```php
$observacion_global = trim($_POST['observacion_global'] ?? '');

$sql = "UPDATE solicitud_alistamiento
        SET estado = :estado,
            observacion_global = :observacion_global,
            tecnico_responsable = :tecnico,
            fecha_actualizacion = NOW()
        WHERE id = :id";

$stmt->execute([
    ':observacion_global' => $observacion_global,
    ...
]);
```

**DESPUÉS:**
```php
$observacion_tecnico = trim($_POST['observacion_tecnico'] ?? '');

$sql = "UPDATE solicitud_alistamiento
        SET estado = :estado,
            observacion_tecnico = :observacion_tecnico,
            tecnico_responsable = :tecnico,
            fecha_actualizacion = NOW()
        WHERE id = :id";

$stmt->execute([
    ':observacion_tecnico' => $observacion_tecnico,
    ...
]);
```

---

### 2. **Botón Editar - Data Attribute** (línea 355)

**ANTES:**
```php
data-observacion="<?php echo e($sol['observacion_global'] ?? ''); ?>"
```

**DESPUÉS:**
```php
data-observacion="<?php echo e($sol['observacion_tecnico'] ?? ''); ?>"
```

---

### 3. **Modal de Detalle** (línea 412-413)

**ANTES:**
```html
<h6><strong>Observación Global (Admin/Tecnico/Bodega):</strong></h6>
<p id="det-observacion-global" class="text-muted"></p>
```

**DESPUÉS:**
```html
<h6><strong>Observación del Técnico:</strong></h6>
<p id="det-observacion-tecnico" class="text-muted"></p>
```

---

### 4. **Modal de Edición - Formulario** (líneas 454-457)

**ANTES:**
```html
<label for="edit-observacion">Observación Global (Admin/Bodega)</label>
<textarea class="form-control" name="observacion_global" id="edit-observacion" rows="4"
        placeholder="Comentarios sobre el proceso de alistamiento, problemas encontrados, etc."></textarea>
```

**DESPUÉS:**
```html
<label for="edit-observacion">Observación del Técnico</label>
<textarea class="form-control" name="observacion_tecnico" id="edit-observacion" rows="4"
        placeholder="Comentarios del técnico sobre el proceso de alistamiento, problemas encontrados, etc."></textarea>
```

---

### 5. **JavaScript - Ver Detalle** (línea 534)

**ANTES:**
```javascript
$('#det-observacion-global').text(solicitudData.observacion_global || 'Sin observaciones globales');
```

**DESPUÉS:**
```javascript
$('#det-observacion-tecnico').text(solicitudData.observacion_tecnico || 'Sin observaciones del técnico');
```

---

### 6. **JavaScript - Editar Modal** (línea 544)

**ANTES:**
```javascript
$('#edit-observacion').val(solicitudData.observacion_global || '');
```

**DESPUÉS:**
```javascript
$('#edit-observacion').val(solicitudData.observacion_tecnico || '');
```

---

## 🗄️ CAMBIOS EN LA BASE DE DATOS

### SQL Requerido

**Archivo:** `ADD_OBSERVACION_TECNICO.sql`

```sql
ALTER TABLE `solicitud_alistamiento`
ADD COLUMN `observacion_tecnico` TEXT NULL AFTER `observacion_global`,
ADD COMMENT = 'Observaciones del técnico durante el alistamiento';
```

---

## 📊 ESTRUCTURA FINAL DE OBSERVACIONES

| Campo | Escrito Por | Ubicación | Propósito |
|-------|-------------|-----------|-----------|
| `observacion` | Sistema | Automático | Despacho + JSON productos (interno) |
| `observacion_global` | **Comercial** | `preventa.php` | Observaciones del cliente/pedido |
| `observacion_tecnico` | **Técnico/Bodega** | `historial_solicitudes_alistamiento.php` | Proceso de alistamiento |

---

## 🚀 PASOS PARA APLICAR

### Paso 1: Ejecutar SQL
```sql
-- En phpMyAdmin, ejecutar:
-- C:\laragon\www\pcmteam\ADD_OBSERVACION_TECNICO.sql
```

### Paso 2: Verificar Columna
```sql
DESCRIBE solicitud_alistamiento;
-- Debe mostrar: observacion_tecnico | TEXT | YES
```

### Paso 3: Probar Funcionalidad
1. Acceder a `http://192.168.2.20/pcmteam/public_html/despacho/historial_solicitudes_alistamiento.php`
2. Click en botón "Editar" (icono lápiz) de una solicitud
3. Escribir observaciones en "Observación del Técnico"
4. Guardar cambios
5. Ver detalle y verificar que aparezca en "Observación del Técnico"

---

## ✅ FLUJO COMPLETO

### 1. **Comercial crea solicitud** (`preventa.php`)
```
Comercial escribe en "Observaciones Globales"
↓
Se guarda en columna: observacion_global
```

### 2. **Admin/Bodega gestiona solicitud** (`historial_solicitudes_alistamiento.php`)
```
Admin/Técnico/Bodega escribe en "Observación del Técnico"
↓
Se guarda en columna: observacion_tecnico
```

### 3. **Visualización**
```
Ver Detalle muestra:
- Observación Global (del comercial)
- Observación del Técnico (del técnico/bodega)
```

---

## 📝 ARCHIVOS MODIFICADOS

1. ✅ `public_html/despacho/historial_solicitudes_alistamiento.php`
   - Backend PHP: 6 cambios
   - HTML: 2 cambios
   - JavaScript: 2 cambios
   - **Total: 10 cambios**

2. ✅ `ADD_OBSERVACION_TECNICO.sql` (NUEVO)
   - SQL para agregar columna

3. ✅ `CAMBIO_OBSERVACION_TECNICO.md` (NUEVO)
   - Este documento

---

## 🔍 VERIFICACIÓN

### Antes de Ejecutar SQL:
```sql
SELECT observacion_tecnico FROM solicitud_alistamiento LIMIT 1;
-- Error: Unknown column 'observacion_tecnico'
```

### Después de Ejecutar SQL:
```sql
SELECT id, observacion_global, observacion_tecnico
FROM solicitud_alistamiento
LIMIT 5;
-- ✅ Debe mostrar ambas columnas
```

---

## ⚠️ NOTA IMPORTANTE

- La columna `observacion_global` sigue existiendo en la base de datos
- Se usa en `preventa.php` para las observaciones del comercial
- NO eliminar `observacion_global`, solo se agregó `observacion_tecnico` como campo adicional

---

**✅ CAMBIO COMPLETADO**

Sistema actualizado para diferenciar observaciones por rol de usuario.

---

**Desarrollado con:** Claude Code
**Base de Datos:** u171145084_pcmteam
**Servidor:** Laragon (localhost)
