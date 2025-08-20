# Sistema de Mantenimiento y Limpieza - PCM Team

## Descripción
Sistema completo para gestionar el proceso de mantenimiento y limpieza de equipos después del TRIAGE 2. Permite visualizar los resultados del diagnóstico en el lado izquierdo y registrar el mantenimiento en el lado derecho.

## Estructura del Sistema

### Backend
- **`backend/php/procesar_mantenimiento.php`** - Procesa y guarda los datos del formulario
- **`backend/php/obtener_diagnostico.php`** - Obtiene datos del diagnóstico (API)
- **`backend/bd/ctconex.php`** - Conexión a la base de datos

### Frontend
- **`frontend/laboratorio/ingresar_m.php`** - Formulario principal de mantenimiento
- **`frontend/laboratorio/test_mantenimiento.php`** - Página de prueba para verificar funcionamiento

## Funcionalidades

### Lado Izquierdo - Datos del TRIAGE 2
Muestra toda la información del diagnóstico previo:
- Fecha del diagnóstico
- Estado de componentes (Cámara, Teclado, Parlantes, Batería, Micrófono, Pantalla)
- Estado de puertos (VGA, DVI, HDMI, USB, Red)
- Estado del disco duro
- Estado de reparación
- Observaciones del diagnóstico

### Lado Derecho - Formulario de Mantenimiento
Permite registrar:
- **Técnico de Diagnóstico**: Selección del técnico responsable
- **Limpieza Electrónico**: Estado y observaciones
- **Mantenimiento Crema Disciplinaria**: Estado y observaciones
- **Mantenimiento Partes**: Estado general
- **Cambio de Piezas**: Si/No y detalles de piezas
- **Proceso de Reconstrucción**: Si/No y parte reconstruida
- **Limpieza General**: Estado general
- **Remisión a Otra Área**: Si/No y área específica
- **Proceso Electrónico**: Detalles técnicos
- **Observaciones Globales**: Comentarios generales

## Base de Datos

### Tabla Origen: `bodega_diagnosticos`
```sql
CREATE TABLE `bodega_diagnosticos` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `fecha_diagnostico` datetime NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `camara` text DEFAULT NULL,
  `teclado` text DEFAULT NULL,
  `parlantes` text DEFAULT NULL,
  `bateria` text DEFAULT NULL,
  `microfono` text DEFAULT NULL,
  `pantalla` text DEFAULT NULL,
  `puertos` text DEFAULT NULL,
  `disco` text DEFAULT NULL,
  `estado_reparacion` enum('falla_mecanica','falla_electrica','reparacion_cosmetica','aprobado') NOT NULL,
  `observaciones` text DEFAULT NULL
);
```

### Tabla Destino: `bodega_mantenimiento`
```sql
CREATE TABLE `bodega_mantenimiento` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `tecnico_id` int(11) DEFAULT NULL,
  `usuario_registro` int(11) DEFAULT NULL,
  `estado` enum('pendiente','realizado','rechazado') NOT NULL DEFAULT 'pendiente',
  `tecnico_diagnostico` int(11) DEFAULT NULL,
  `limpieza_electronico` enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
  `observaciones_limpieza_electronico` text DEFAULT NULL,
  `mantenimiento_crema_disciplinaria` enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
  `observaciones_mantenimiento_crema` text DEFAULT NULL,
  `mantenimiento_partes` enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
  `cambio_piezas` enum('no','si') DEFAULT 'no',
  `piezas_solicitadas_cambiadas` text DEFAULT NULL,
  `proceso_reconstruccion` enum('no','si') DEFAULT 'no',
  `parte_reconstruida` text DEFAULT NULL,
  `limpieza_general` enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
  `remite_otra_area` enum('no','si') DEFAULT 'no',
  `area_remite` varchar(255) DEFAULT NULL,
  `proceso_electronico` text DEFAULT NULL,
  `observaciones_globales` text DEFAULT NULL
);
```

## Cómo Usar

### 1. Acceder al Sistema
```
http://localhost/pcmteam/frontend/laboratorio/test_mantenimiento.php
```

### 2. Seleccionar Equipo
- Ver la lista de equipos disponibles
- Hacer clic en "Ir a Mantenimiento" para el equipo deseado

### 3. Completar Formulario
- **Lado izquierdo**: Revisar datos del diagnóstico previo
- **Lado derecho**: Completar formulario de mantenimiento
- Seleccionar técnico de diagnóstico (obligatorio)
- Completar todos los campos según corresponda

### 4. Guardar
- Hacer clic en "GUARDAR Mantenimiento Y Limpieza"
- El sistema validará los datos y los guardará en la base de datos
- El estado del inventario cambiará a "en_mantenimiento"

## Características Técnicas

### Validaciones
- Técnico de diagnóstico es obligatorio
- Campos condicionales se muestran/ocultan según selecciones
- Validación de datos antes de envío

### Interfaz Responsiva
- Diseño adaptativo para diferentes tamaños de pantalla
- Grid layout que se ajusta automáticamente
- Estilos modernos y profesionales

### Seguridad
- Escape de HTML para prevenir XSS
- Validación de datos en backend
- Uso de prepared statements para prevenir SQL injection

### JavaScript
- Funcionalidad dinámica para campos condicionales
- Validación en tiempo real
- Manejo de errores y respuestas del servidor
- Alertas visuales para el usuario

## Flujo de Trabajo

1. **Equipo en TRIAGE 2** → Se diagnostica y se guarda en `bodega_diagnosticos`
2. **Equipo para Mantenimiento** → Se accede al formulario de `ingresar_m.php`
3. **Registro de Mantenimiento** → Se completa y se guarda en `bodega_mantenimiento`
4. **Estado Actualizado** → El inventario cambia a "en_mantenimiento"

## Archivos de Configuración

### Conexión a Base de Datos
```php
// backend/bd/ctconex.php
define('dbhost', 'localhost');
define('dbuser', 'u171145084_pcmteam');
define('dbpass', 'PCcomercial2025*');
define('dbname', 'u171145084_pcmteam');
```

### Roles de Usuario
- **Rol 6**: Técnicos (se muestran en el selector)
- **Rol 1**: Administradores
- **Otros roles**: Según configuración del sistema

## Solución de Problemas

### Error de Conexión
- Verificar credenciales de base de datos
- Comprobar que MySQL esté funcionando
- Verificar permisos de usuario

### Formulario No Guarda
- Revisar consola del navegador para errores JavaScript
- Verificar logs del servidor PHP
- Comprobar permisos de escritura en la base de datos

### Datos No Se Muestran
- Verificar que el equipo tenga diagnóstico previo
- Comprobar que el ID del inventario sea válido
- Revisar consultas SQL en el código

## Personalización

### Agregar Nuevos Campos
1. Agregar columna en `bodega_mantenimiento`
2. Actualizar formulario en `ingresar_m.php`
3. Modificar `procesar_mantenimiento.php`
4. Actualizar validaciones JavaScript

### Cambiar Estilos
- Modificar CSS en `ingresar_m.php`
- Ajustar clases y estilos según necesidades
- Mantener consistencia con el diseño del sistema

## Contacto y Soporte
Para dudas o problemas técnicos, contactar al equipo de desarrollo de PCM Team.
