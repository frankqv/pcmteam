# 🔍 Diagnóstico: Problema con Select2 (Búsqueda de Clientes)

## Paso 1: Abrir la Consola del Navegador

1. Abre el navegador (Chrome/Edge/Firefox)
2. Ve a: `http://localhost/pcmteam/public_html/comercial/alistamiento_venta.php`
3. Presiona **F12** para abrir Developer Tools
4. Ve a la pestaña **Console**

## Paso 2: Verificar los Logs de Debug

Deberías ver en la consola:

```
=== DEBUG ALISTAMIENTO VENTA ===
1. Verificando jQuery: ✅ Cargado
2. Verificando Select2: ✅ Cargado
3. Verificando SweetAlert: ✅ Cargado
4. DOM Ready ✅
5. Elemento #buscarCliente existe: ✅ SÍ
6. Probando API de clientes...
✅ API funciona - Respuesta: { results: [...] }
```

### ❌ Si ves "Select2: ❌ NO cargado"

**Problema**: Select2 no se está cargando desde CDN

**Solución**: Descargar Select2 localmente

1. Ve a la pestaña **Network** en Developer Tools
2. Busca `select2.min.js`
3. Si aparece en ROJO o 404, hay problema de conexión

**Fix rápido**: Cambiar línea 526 en `alistamiento_venta.php`:

```html
<!-- Cambiar de: -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- A: -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
```

Y línea 24:
```html
<!-- Cambiar de: -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- A: -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
```

---

### ❌ Si ves "Elemento #buscarCliente existe: ❌ NO"

**Problema**: El modal no se está cargando correctamente

**Solución**: Verificar que el modal existe

1. En la consola, ejecuta:
   ```javascript
   $('#modalNuevaVenta').length
   ```
   Debería devolver `1`

2. Si devuelve `0`, hay un error de sintaxis en el HTML

---

### ❌ Si ves error en la API

**Ejemplo de error**:
```
❌ Error en API: error
Respuesta: {"success":false,"message":"Sesión no válida"}
```

**Problema**: No hay sesión iniciada

**Solución**:
1. Verifica que estás logueado
2. Ve a `http://localhost/pcmteam/public_html/` y loguéate
3. Vuelve a intentar

---

## Paso 3: Probar la API Directamente

Abre en el navegador:
```
http://localhost/pcmteam/backend/php/alistamiento_api.php?action=buscar_clientes&q=a
```

**Respuesta esperada**:
```json
{
  "results": [
    {
      "id": "1",
      "text": "Juan Pérez - 12345678",
      "numid": "12345678",
      "nomcli": "Juan",
      "apecli": "Pérez",
      ...
    }
  ]
}
```

**❌ Si ves error**:
```json
{"success":false,"message":"Sesión no válida"}
```

Significa que no estás logueado. Loguéate primero.

---

## Paso 4: Probar Select2 Manualmente

En la consola del navegador, ejecuta:

```javascript
// Abrir el modal
$('#modalNuevaVenta').modal('show');

// Esperar 1 segundo y probar Select2
setTimeout(function() {
    // Verificar si Select2 está inicializado
    console.log('Select2 inicializado:', $('#buscarCliente').hasClass('select2-hidden-accessible'));

    // Forzar apertura de Select2
    $('#buscarCliente').select2('open');
}, 1000);
```

**Si se abre el dropdown**: Select2 funciona ✅

**Si NO se abre**: Hay un error de inicialización ❌

---

## Paso 5: Ver Errores de Red

1. Ve a la pestaña **Network** en Developer Tools
2. Filtra por `alistamiento_api.php`
3. Haz clic en "Nueva Venta"
4. Escribe en el campo de búsqueda: `test`
5. Deberías ver una petición a:
   ```
   alistamiento_api.php?action=buscar_clientes&q=test
   ```

6. Haz clic en esa petición
7. Ve a la pestaña **Response**

**Respuesta correcta**:
```json
{"results": [...]}
```

**Respuesta con error**:
```json
{"success":false,"message":"..."}
```

---

## Paso 6: Archivo de Prueba

Creé un archivo de prueba. Abre en el navegador:

```
http://localhost/pcmteam/backend/php/test_clientes.php
```

**Debería mostrar**:
```json
{
  "success": true,
  "total_clientes": 5,
  "clientes": [...]
}
```

Si ves esto, significa que:
- ✅ La conexión a BD funciona
- ✅ La tabla `clientes` existe
- ✅ Hay clientes en la BD
- ✅ Las columnas son correctas

---

## 🎯 Soluciones Rápidas Comunes

### Solución 1: Reinicializar Select2

Agrega esto TEMPORALMENTE al final de `alistamiento_venta.js`:

```javascript
// Al final del archivo, después de todo
$(document).ready(function() {
    console.log('Reinicializando Select2...');

    $('#modalNuevaVenta').on('shown.bs.modal', function () {
        console.log('Modal abierto');

        // Destruir Select2 si existe
        if ($('#buscarCliente').hasClass('select2-hidden-accessible')) {
            $('#buscarCliente').select2('destroy');
        }

        // Reinicializar
        $('#buscarCliente').select2({
            placeholder: 'Buscar cliente...',
            allowClear: true,
            dropdownParent: $('#modalNuevaVenta'),
            ajax: {
                url: '../../backend/php/alistamiento_api.php?action=buscar_clientes',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    console.log('Datos recibidos:', data);
                    return {
                        results: data.results
                    };
                }
            }
        });

        console.log('Select2 reinicializado');
    });
});
```

### Solución 2: Usar dropdownParent

El problema puede ser que Select2 se renderiza FUERA del modal.

**Fix**: Agregar `dropdownParent` en línea 86 de `alistamiento_venta.js`:

```javascript
$('#buscarCliente').select2({
    placeholder: 'Buscar cliente...',
    allowClear: true,
    dropdownParent: $('#modalNuevaVenta'), // ← AGREGAR ESTA LÍNEA
    ajax: {
        url: '../../backend/php/alistamiento_api.php?action=buscar_clientes',
        ...
    }
});
```

---

## 📸 Dame esta información:

1. **Captura de pantalla** de la consola (F12) mostrando los logs de debug
2. **Captura de pantalla** de la pestaña Network mostrando la petición a `alistamiento_api.php`
3. **Respuesta** del archivo de prueba: `http://localhost/pcmteam/backend/php/test_clientes.php`

Con esta información podré saber exactamente cuál es el problema 🔍
