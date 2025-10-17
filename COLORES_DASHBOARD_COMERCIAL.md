# 🎨 PALETA DE COLORES - DASHBOARD COMERCIAL
**Fecha:** 17 de Octubre, 2025
**Archivo:** `public_html/comercial/escritorio.php`

---

## 🎯 COLORES BASE DEL SISTEMA

```css
🟢 Verde:    #00CC54
🟡 Amarillo: #F0DD00
🔵 Azul:     #2B41CC
🔴 Rojo:     #CC0618
🟣 Morado:   #7B2CBF (añadido)
```

---

## 📊 MAPEO DE COLORES POR ELEMENTO

### 1. **Welcome Banner (Banner de Bienvenida)**
```css
background: linear-gradient(135deg, #2B41CC 0%, #5865F2 100%);
box-shadow: 0 5px 20px rgba(43, 65, 204, 0.25);
```
**Color:** 🔵 Azul (#2B41CC → Azul claro)
**Uso:** Banner principal con saludo al usuario

---

### 2. **Stat Cards (Tarjetas de Estadísticas)**

#### 🟣 Tarjeta 1: "Mis Solicitudes"
```css
border-left-color: #7B2CBF;
icon color: #7B2CBF;
h3 color: #7B2CBF;
```
**Color:** Morado
**Muestra:** Total de solicitudes y pendientes

#### 🟡 Tarjeta 2: "En Proceso"
```css
border-left-color: #F0DD00;
icon color: #F0DD00;
h3 color: #F0DD00;
```
**Color:** Amarillo
**Muestra:** Solicitudes en proceso activo

#### 🔵 Tarjeta 3: "Ventas del Mes"
```css
border-left-color: #2B41CC;
icon color: #2B41CC;
h3 color: #2B41CC;
```
**Color:** Azul
**Muestra:** Total ventas, productos vendidos, dinero

#### 🟢 Tarjeta 4: "Clientes Activos"
```css
border-left-color: #00CC54;
icon color: #00CC54;
h3 color: #00CC54;
```
**Color:** Verde
**Muestra:** Total de clientes activos

---

### 3. **Action Buttons (Botones de Acción Rápida)**

#### 🟣 Botón "Nueva Solicitud de Alistamiento"
```css
.btn-solicitud {
    background: linear-gradient(135deg, #7B2CBF 0%, #9D4EDD 100%);
    color: white;
}
.btn-solicitud:hover {
    box-shadow: 0 8px 20px rgba(123, 44, 191, 0.3);
}
```
**Color:** Morado
**Link:** `../venta/preventa.php`

#### 🔵 Botón "Nueva Venta Multi-Producto"
```css
.btn-venta {
    background: linear-gradient(135deg, #2B41CC 0%, #4657D8 100%);
    color: white;
}
.btn-venta:hover {
    box-shadow: 0 8px 20px rgba(43, 65, 204, 0.3);
}
```
**Color:** Azul
**Link:** `../venta/nuevo_multiproducto.php`

#### 🟢 Botón "Registrar Nuevo Cliente"
```css
.btn-cliente {
    background: linear-gradient(135deg, #00CC54 0%, #00E05F 100%);
    color: white;
}
.btn-cliente:hover {
    box-shadow: 0 8px 20px rgba(0, 204, 84, 0.3);
}
```
**Color:** Verde
**Link:** `../clientes/nuevo.php`

#### 🟡 Botón "Ver Historial de Solicitudes"
```css
.btn-historial {
    background: linear-gradient(135deg, #F0DD00 0%, #FFE500 100%);
    color: #333;
}
.btn-historial:hover {
    box-shadow: 0 8px 20px rgba(240, 221, 0, 0.3);
}
```
**Color:** Amarillo (texto oscuro por contraste)
**Link:** `../venta/historico_preventa.php`

---

### 4. **Gradientes de Utilidad** (clases reutilizables)

```css
.bg-gradient-blue {
    background: linear-gradient(135deg, #2B41CC 0%, #4657D8 100%);
}

.bg-gradient-green {
    background: linear-gradient(135deg, #00CC54 0%, #00E05F 100%);
}

.bg-gradient-red {
    background: linear-gradient(135deg, #CC0618 0%, #E61F30 100%);
}

.bg-gradient-yellow {
    background: linear-gradient(135deg, #F0DD00 0%, #FFE500 100%);
}

.bg-gradient-purple {
    background: linear-gradient(135deg, #7B2CBF 0%, #9D4EDD 100%);
}
```

---

## 🎨 VARIACIONES DE TONALIDAD

### Azul (#2B41CC)
- **Base:** `#2B41CC`
- **Claro:** `#4657D8` (gradiente end)
- **Muy Claro:** `#5865F2` (welcome banner)

### Verde (#00CC54)
- **Base:** `#00CC54`
- **Claro:** `#00E05F` (gradiente end)

### Amarillo (#F0DD00)
- **Base:** `#F0DD00`
- **Claro:** `#FFE500` (gradiente end)

### Rojo (#CC0618)
- **Base:** `#CC0618`
- **Claro:** `#E61F30` (gradiente end)
- **Nota:** No usado actualmente en escritorio.php, reservado para alertas/errores

### Morado (añadido)
- **Base:** `#7B2CBF`
- **Claro:** `#9D4EDD` (gradiente end)

---

## 📐 ESTRUCTURA VISUAL

```
┌─────────────────────────────────────────────────────┐
│  🔵 WELCOME BANNER (Azul #2B41CC)                  │
│  ¡Bienvenid@, Usuario!                              │
└─────────────────────────────────────────────────────┘

┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐
│🟣 Solic│ │🟡 Proce│ │🔵 Venta│ │🟢 Clien│
│   12   │ │   3    │ │   8    │ │  156   │
└────────┘ └────────┘ └────────┘ └────────┘

┌──────────────────────────────┐ ┌──────────────┐
│  ACCIONES RÁPIDAS            │ │ ÚLTIMAS SOL  │
│  🟣 Nueva Solicitud          │ │ #45 - ...    │
│  🔵 Nueva Venta              │ │ #44 - ...    │
│  🟢 Nuevo Cliente            │ │ #43 - ...    │
│  🟡 Ver Historial            │ └──────────────┘
└──────────────────────────────┘
```

---

## ✅ BENEFICIOS DE LA NUEVA PALETA

1. **Consistencia Visual:** Todos los colores siguen la paleta del sistema
2. **Diferenciación Clara:** Cada tipo de acción tiene su propio color
3. **Accesibilidad:** Buenos contrastes para texto blanco excepto amarillo
4. **Profesionalismo:** Gradientes suaves y sombras sutiles
5. **Coherencia:** Los mismos colores se usan en tarjetas y botones relacionados

---

## 🔄 MAPEO FUNCIONAL

| Elemento | Color | Significado |
|----------|-------|-------------|
| Solicitudes (crear/ver) | 🟣 Morado | Gestión de solicitudes |
| Ventas | 🔵 Azul | Transacciones comerciales |
| Clientes | 🟢 Verde | Gestión de clientes |
| Historial | 🟡 Amarillo | Consultas y reportes |
| En Proceso | 🟡 Amarillo | Estado activo/en progreso |

---

## 📝 NOTAS DE IMPLEMENTACIÓN

### Cambios Realizados:

1. ✅ Welcome banner: Azul (#2B41CC)
2. ✅ Stat card "Mis Solicitudes": Morado (#7B2CBF)
3. ✅ Stat card "En Proceso": Amarillo (#F0DD00)
4. ✅ Stat card "Ventas del Mes": Azul (#2B41CC)
5. ✅ Stat card "Clientes Activos": Verde (#00CC54)
6. ✅ Botón "Nueva Solicitud": Morado
7. ✅ Botón "Nueva Venta": Azul
8. ✅ Botón "Nuevo Cliente": Verde
9. ✅ Botón "Ver Historial": Amarillo
10. ✅ Sombras con transparencia en hover (efecto de profundidad)

### Archivos Modificados:
- `public_html/comercial/escritorio.php` (líneas 70-199)

---

## 🧪 PRUEBAS REQUERIDAS

1. Verificar contraste de texto en todos los botones
2. Verificar que los gradientes se vean correctamente en diferentes navegadores
3. Verificar que las sombras en hover funcionen correctamente
4. Verificar colores en modo oscuro (si aplica)

---

**Desarrollado con:** Claude Code
**Sistema:** PCMTEAM - Dashboard Comercial
**Versión de Colores:** 1.0
