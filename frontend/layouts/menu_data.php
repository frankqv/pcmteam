<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rol = $_SESSION['rol'] ?? 0; // Obtener el rol de la sesión
switch ($rol) {
    case 1:
        $panelName = 'Panel Administrativo';
        $panelUrl = '../administrador/escritorio.php';
        break;
    case 2:
        $panelName = 'Panel Cliente';
        $panelUrl = '../u_generico/escritorio.php';
        break;
    case 3:
        $panelName = 'Panel Contable';
        $panelUrl = '../contable/escritorio.php';
        break;
    case 4:
        $panelName = 'Panel Comercial';
        $panelUrl = '../comercial/escritorio.php';
        break;
    case 5:
        $panelName = 'Panel Jefe Técnico';
        $panelUrl = '../jtecnico/escritorio.php';
        break;
    case 6:
        $panelName = 'Panel Técnico';
        $panelUrl = '../tecnico/escritorio.php';
        break;
    case 7:
        $panelName = 'Panel Bodega';
        $panelUrl = '../bodega/escritorio.php';
        break;
    default:
        $panelName = 'Panel';
        $panelUrl = '../administrador/escritorio.php';
        break;
}
# Menu Lateral de la Aplicación - Reorganizado por Prioridad
$menu = [
    [
        'label' => $panelName,
        'url' => $panelUrl,
        'icon' => 'dashboard'
    ],
];
# PRIORIDAD 1: CLIENTES - Base del negocio
if (in_array($rol, [1, 2, 4, 5, 7])) {
    $menu[] = [
        'label' => 'Clientes',
        'icon' => 'group',
        'id' => 'clientes',
        'url' => '../clientes/mostrar.php',
    ];
}
# PRIORIDAD 2: CLIENTES POR TIENDA - Acceso rápido por ubicación
if (in_array($rol, [1, 2, 4, 5])) {
    $menu[] = [
        'label' => 'Mis Clientes',
        'id' => 'tienda',
        'icon' => 'store',
        'children' => [
            ['label' => ' > Puente Aranda', 'url' => '../clientes/bodega.php'],
            ['label' => ' > Unilago', 'url' => '../clientes/unilago.php'],
            ['label' => ' > Cúcuta', 'url' => '../clientes/cucuta.php'],
            ['label' => ' > Medellín', 'url' => '../clientes/medellin.php']
        ]
    ];
}
# PRIORIDAD 3: SERVICIOS TÉCNICOS - Operación diaria crítica
if (in_array($rol, [1, 4, 5, 6, 7])) {
    $menu[] = [
        'label' => 'Servicios Técnicos',
        'url' => '../servicio/mostrar.php',
        'icon' => 'view_timeline'
    ];
}

# PRIORIDAD 4: MIS SERVICIOS - Trabajo personal del técnico
if (in_array($rol, [4, 5, 7])) {
    $menu[] = [
        'label' => 'Mis Servicios',
        'icon' => 'dataset',
        'id' => 'planes',
        'url' => '../mis_servicios/mostrar.php'
    ];
}
# PRIORIDAD 5: PRODUCTOS - Inventario y catálogo
if (in_array($rol, [1, 4, 5, 6, 7])) {
    $menu[] = [
        'label' => 'Productos',
        'icon' => 'conveyor_belt',
        'id' => 'productos',
        'children' => [
            ['label' => '> Lista de Productos', 'url' => '../producto/mostrar.php'],
            ['label' => '> Categoría', 'id' => 'categorias', 'url' => '../categoria/mostrar.php']
        ]
    ];
}
# PRIORIDAD 6: BODEGA - Control de inventario
if (in_array($rol, [1, 4, 5, 7])) {
    $menu[] = [
        'label' => 'Bodega',
        'icon' => 'warehouse',
        'children' => [
            ['label' => '> Inventario', 'url' => '../bodega/inventario.php'],
            ['label' => '> Entradas', 'url' => '../bodega/entradas.php'],
            ['label' => '> Salidas', 'url' => '../bodega/salidas.php'],
            ['label' => '> Listado General', 'url' => '../bodega/mostrar.php'],
            ['label' => '> Código de Barras', 'url' => '../bodega/barcode.php'],
            ['label' => '> Partes', 'url' => '../bodega/partes.php'],
            ['label' => '> Baterías', 'url' => '../bodega/bateria.php'],
        ]
    ];
}
# PRIORIDAD 7: ALISTAMIENTOS - Preparación de pedidos
if (in_array($rol, [1, 4, 5])) {
    $menu[] = [
        'label' => 'Alistamientos',
        'url' => '../pedidos_ruta/mostrar.php',
        'icon' => 'unarchive'
    ];
}
# PRIORIDAD 8: LABORATORIO TÉCNICO - Área especializada
if (in_array($rol, [1, 4, 5, 6])) {
    $menu[] = [
        'label' => 'Laboratorio Técnico',
        'url' => '../laboratorio/mostrar.php',
        'icon' => 'biotech'
    ];
}
# PRIORIDAD 9: HISTORIAL DE VENTAS - Seguimiento comercial
if (in_array($rol, [1, 3, 4])) {
    $menu[] = [
        'label' => 'Historial de Ventas',
        'icon' => 'point_of_sale',
        'id' => 'ventas',
        'url' => '../venta/mostrar.php'
    ];
}
# PRIORIDAD 10: COMPRAS - Gestión de proveedores
if (in_array($rol, [1, 4, 5, 6, 7])) {
    $menu[] = [
        'label' => 'Compras',
        'icon' => 'shopping_basket',
        'id' => 'compras',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../compra/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../compra/nuevo.php']
        ]
    ];
}
# PRIORIDAD 11: PROVEEDORES - Gestión de terceros
if (!in_array($rol, [2, 3, 4, 6])) {
    $menu[] = [
        'label' => 'Proveedores',
        'url' => '../proveedor/mostrar.php',
        'icon' => 'local_shipping'
    ];
}
# PRIORIDAD 12: BUSINESS ROOM - Análisis de negocio
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $menu[] = [
        'label' => 'Business Room',
        'url' => '../b_room/mostrar.php',
        'icon' => 'paid'
    ];
}
# PRIORIDAD 13: GASTOS GENERALES - Control financiero
if (in_array($rol, [1, 2, 3, 4])) {
    $menu[] = [
        'label' => 'Gastos Generales',
        'icon' => 'savings',
        'id' => 'gastos',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../gastos/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../gastos/nuevo.php']
        ]
    ];
}
# PRIORIDAD 14: DOCUMENTOS GENERALES - Recursos de apoyo
if (!in_array($rol, [0, 8])) {
    $menu[] = [
        'label' => 'Docs Generales',
        'icon' => 'library_books',
        'id' => 'docs',
        'url' => '../docs/mostrar.php',
    ];
}
# PRIORIDAD 15: REPORTES - Análisis y estadísticas
if (in_array($rol, [1, 3])) {
    $menu[] = [
        'label' => 'Reportes',
        'icon' => 'signal_cellular_alt',
        'id' => 'reportes',
        'children' => [
            ['label' => '> Productos', 'url' => '../reporte/productos.php'],
            ['label' => '> Clientes', 'url' => '../reporte/clientes.php'],
            ['label' => '> Ventas', 'url' => '../reporte/ventas.php'],
        ]
    ];
}
# PRIORIDAD 16: GRÁFICOS - Visualización de datos
if (in_array($rol, [1, 3])) {
    $menu[] = [
        'label' => 'Gráficos',
        'url' => '../graficos/mostrar.php',
        'icon' => 'grain'
    ];
}
# PRIORIDAD 17: MARKETING - Promoción y publicidad
if (in_array($rol, [1])) {
    $menu[] = [
        'label' => 'Marketing',
        'url' => '../marketing/mostrar.php',
        'icon' => 'campaign'
    ];
}
# PRIORIDAD 18: USUARIOS - Administración del sistema
if (in_array($rol, [1])) {
    $menu[] = [
        'label' => 'Usuarios',
        'url' => '../usuario/mostrar.php',
        'icon' => 'manage_accounts'
    ];
}
# PRIORIDAD 19: CONFIGURACIÓN - Ajustes del sistema
if (in_array($rol, [1, 3, 5])) {
    $menu[] = [
        'label' => 'Configuración',
        'url' => '../cuenta/configuracion.php',
        'icon' => 'settings'
    ];
}
# PRIORIDAD 20: SALIR - Siempre al final
if (!in_array($rol, [0])) {
    $menu[] = [
        'label' => 'Salir',
        'url' => '../cuenta/salir.php',
        'icon' => 'logout'
    ];
}
?>
