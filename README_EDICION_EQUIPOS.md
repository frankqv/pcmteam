# Sistema de Edición de Equipos por Posición - PCM Team

## 📋 Descripción
Sistema completo para editar datos de equipos en `bodega_inventario` basado en la posición del equipo. Permite modificar campos específicos manteniendo un historial de cambios.

## 🎯 Funcionalidades Principales

### ✅ **Búsqueda por Posición**
- Buscar equipos ingresando la posición exacta
- Ejemplos: `ESTANTE-1-A`, `CAJA-B2`, `ESTANTE-2-C`
- Validación de posición antes de buscar

### ✅ **Visualización de Datos**
- Muestra información completa del equipo encontrado
- Campos visibles: Código, Producto, Marca, Modelo, Serial, etc.
- Estado actual del equipo (disposición)

### ✅ **Edición de Campos**
Permite editar los siguientes campos:
- **Modelo**: Modelo específico del equipo
- **Procesador**: Especificaciones del procesador
- **RAM**: Memoria RAM instalada
- **Disco**: Tipo y capacidad del disco
- **Pulgadas**: Tamaño de pantalla
- **Grado**: Clasificación A, B, C, SCRAP
- **Táctil**: Sí/No
- **Activo Fijo**: Código u observación

### ✅ **Historial de Cambios**
- Registra todos los cambios realizados
- Muestra usuario, fecha y campo modificado
- Compara valores anteriores vs nuevos

## 🏗️ Estructura del Sistema

### Backend
- **`backend/php/editar_equipo.php`** - Procesa y guarda las ediciones
- **`backend/php/buscar_equipo_posicion.php`** - Busca equipos por posición
- **`backend/bd/crear_tabla_log.sql`** - Script para crear tabla de log (opcional)

### Frontend
- **`frontend/bodega/editar_equipo.php`** - Interfaz principal de edición

## 🚀 Cómo Usar

### 1. **Acceder al Sistema**
```
http://localhost/pcmteam/frontend/bodega/editar_equipo.php
```

### 2. **Buscar Equipo**
- Ingresar la posición exacta del equipo
- Hacer clic en "🔍 Buscar"
- El sistema mostrará la información del equipo

### 3. **Revisar Información**
- Ver todos los datos actuales del equipo
- Identificar campos que necesiten modificación
- Revisar estado y ubicación

### 4. **Editar Campos**
- Hacer clic en "✏️ Editar Equipo"
- Modificar solo los campos necesarios
- Los campos vacíos no se modificarán

### 5. **Confirmar Cambios**
- Revisar resumen de cambios
- Confirmar guardado
- Ver mensaje de éxito

### 6. **Revisar Historial**
- Ver historial de cambios realizados
- Identificar usuario y fecha de cada modificación

## 🔧 Características Técnicas

### **Validaciones**
- Posición requerida para búsqueda
- Solo campos permitidos se pueden editar
- Confirmación antes de guardar cambios
- Validación de datos en backend

### **Seguridad**
- Prepared statements para prevenir SQL injection
- Validación de campos permitidos
- Log de usuario que realiza cambios
- Escape de datos en frontend

### **Interfaz Responsiva**
- Diseño adaptativo para móviles
- Grid layout que se ajusta automáticamente
- Estilos modernos y profesionales
- Iconos intuitivos para mejor UX

### **Base de Datos**
- Actualización de `bodega_inventario`
- Registro automático de `fecha_modificacion`
- Log opcional en `bodega_log_cambios`
- Transacciones seguras

## 📊 Estructura de la Base de Datos

### **Tabla Principal: `bodega_inventario`**
```sql
-- Campos editables:
modelo, procesador, ram, disco, pulgadas, grado, tactil, activo_fijo

-- Campos automáticos:
fecha_modificacion (se actualiza automáticamente)
```

### **Tabla de Log: `bodega_log_cambios` (Opcional)**
```sql
CREATE TABLE `bodega_log_cambios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventario_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `campo_modificado` varchar(100) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `tipo_cambio` enum('edicion_manual','importacion','sistema') NOT NULL DEFAULT 'edicion_manual',
  PRIMARY KEY (`id`)
);
```

## 🎨 Interfaz de Usuario

### **Secciones Principales**
1. **🔍 Búsqueda por Posición** - Campo de entrada y botón buscar
2. **📦 Información del Equipo** - Datos actuales del equipo
3. **✏️ Formulario de Edición** - Campos editables
4. **📜 Historial de Cambios** - Log de modificaciones

### **Estados Visuales**
- **Búsqueda**: Loading con botón deshabilitado
- **Edición**: Formulario con campos pre-llenados
- **Guardado**: Botón con estado "Guardando..."
- **Alertas**: Mensajes de éxito, error e información

## 🔍 Flujo de Trabajo

```
1. Usuario ingresa posición → 2. Sistema busca equipo → 3. Muestra información
                                                           ↓
6. Actualiza interfaz ← 5. Guarda cambios ← 4. Usuario edita campos
```

## 📝 Ejemplos de Uso

### **Ejemplo 1: Cambiar Grado de Equipo**
```
Posición: ESTANTE-1-A
Campo: Grado
Valor anterior: B
Nuevo valor: A
```

### **Ejemplo 2: Actualizar Especificaciones**
```
Posición: CAJA-B2
Campos: RAM, Disco
Valores: 16GB, 512GB SSD
```

### **Ejemplo 3: Marcar como Táctil**
```
Posición: ESTANTE-2-C
Campo: Táctil
Valor anterior: NO
Nuevo valor: SI
```

## ⚠️ Consideraciones Importantes

### **Campos No Editables**
- `id`, `codigo_g`, `serial` (identificadores únicos)
- `fecha_ingreso` (histórico)
- `ubicacion`, `posicion` (organizacionales)

### **Validaciones del Sistema**
- Solo equipos existentes se pueden editar
- Campos vacíos no se modifican
- Se requiere confirmación antes de guardar
- Log de cambios opcional pero recomendado

### **Rendimiento**
- Búsqueda optimizada por posición
- Índices en campos de búsqueda
- Límite de historial (últimos 10 cambios)
- Transacciones eficientes

## 🛠️ Personalización

### **Agregar Nuevos Campos Editables**
1. Agregar campo en `campos_permitidos` del backend
2. Crear input en el formulario frontend
3. Actualizar validaciones JavaScript
4. Modificar función de recopilación de datos

### **Cambiar Estilos**
- Modificar CSS en `editar_equipo.php`
- Ajustar colores, fuentes y layout
- Mantener consistencia con el sistema

### **Agregar Validaciones**
- Validaciones en JavaScript (frontend)
- Validaciones en PHP (backend)
- Mensajes de error personalizados

## 🔧 Solución de Problemas

### **Equipo No Encontrado**
- Verificar posición exacta
- Comprobar que el equipo exista
- Revisar permisos de base de datos

### **Error al Guardar**
- Verificar consola del navegador
- Comprobar logs del servidor
- Validar permisos de escritura

### **Historial No Se Muestra**
- Verificar si existe tabla `bodega_log_cambios`
- Ejecutar script SQL de creación
- Comprobar permisos de usuario

## 📞 Soporte y Contacto

Para dudas técnicas o problemas:
- Revisar logs del sistema
- Verificar consola del navegador
- Contactar al equipo de desarrollo PCM Team

---

**¡Sistema listo para uso en producción!** 🚀
