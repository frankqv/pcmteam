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
        $panelUrl = '../cliente/escritorio.php';
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

# Menu Lateral de la Aplicación

$menu = [
    [
        'label' => $panelName,
        'url' => $panelUrl,
        'icon' => 'dashboard'
    ],
    [ 
        'label' => 'Salir',
        'url' => '../cuenta/salir.php',
        'icon' => 'logout'
    ]
];

# Cliente 1
// Agregar Clientes solo si el rol NO es 2, 3 o 4
if (in_array($rol, [1,2 , 4, 5])) {
    // Insertar Cliente después de Panel de usuario (posición 1)
    array_splice($menu, 1, 0, [[
        'label' => 'Clientes',
        'icon' => 'group',
        'id' => 'clientes',
        'children' => [
            ['label' => 'Listado', 'url' => '../clientes/mostrar.php'],
            [
                'label' => 'TIENDA',
                'id' => 'tienda',
                'icon' => 'store',
                'children' => [
                    ['label' => 'Puente Aranda', 'url' => '../clientes/mostrar.php'],
                    ['label' => 'Unilago', 'url' => '../clientes/mostrar.php'],
                    ['label' => 'Cúcuta', 'url' => '../clientes/mostrar.php'],
                    ['label' => 'Medellín', 'url' => '../clientes/mostrar.php']
                ]
            ]
        ]
    ]]);
}

# Pedidos 2
if (in_array($rol, [1, 4, 5, 6, 7])) {
    // Insertar Pedidos (posición 2)
    array_splice($menu, 2, 0, [[
        'label' => 'Pedidos',
        'url' => '../servicio/mostrar.php',
        'icon' => 'view_timeline'
    ]]);
}

# Servicios 3
if (in_array($rol, [1, 4, 5, 7])) {
    // Insertar Servicios (posición 3)
    array_splice($menu, 3, 0, [[
        'label' => 'Servicios',
        'icon' => 'dataset',
        'id' => 'planes',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../plan/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../plan/nuevo.php']
        ]
    ]]);
}

# Productos 4
if (in_array($rol, [1, 4, 5, 6, 7])) {
    array_splice($menu, 4, 0, [[
        'label' => 'Productos',
        'icon' => 'conveyor_belt',
        'id' => 'productos',
        'children' => [
            ['label' => '> Lista de Productos', 'url' => '../producto/mostrar.php'],
            ['label' => '> Categoría', 'id' => 'categorias', 'url' => '../categoria/mostrar.php']
        ]
    ]]);
}

# Historial de venta 5
if (in_array($rol, [1, 3, 4])) {
    array_splice($menu, 5, 0, [[
        'label' => 'Historial de Ventas',
        'icon' => 'point_of_sale',
        'id' => 'ventas',
        'url' => '../venta/mostrar.php'
    ]]);
}

# Compras 6
if (in_array($rol, [1, 4, 5, 6, 7])) {
    array_splice($menu, 6, 0, [[
        'label' => 'Compras',
        'icon' => 'shopping_basket',
        'id' => 'compras',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../compra/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../compra/nuevo.php']
        ]
    ]]);
}

# Gastos Generales 7
if (in_array($rol, [1, 2, 3, 4])) {
    array_splice($menu, 7, 0, [[
        'label' => 'Gastos Generales',
        'icon' => 'savings',
        'id' => 'gastos',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../gastos/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../gastos/nuevo.php']
        ]
    ]]);
}

# Pedidos En Ruta 8
if (in_array($rol, [1, 4, 5])) {
    array_splice($menu, 8, 0, [[
        'label' => 'Pedidos En Ruta',
        'url' => '../pedidos_ruta/mostrar.php',
        'icon' => 'local_mall'
    ]]);
}

# Laboratorio 9
if (in_array($rol, [1, 4, 5, 6, 7])) {
    array_splice($menu, 9, 0, [[
        'label' => 'Laboratorio Técnico',
        'url' => '../laboratorio/mostrar.php',
        'icon' => 'biotech'
    ]]);
}

# PROVEEDORES 10
// Agregar Proveedores solo si el rol NO es 2, 3 o 4
if (!in_array($rol, [2, 3, 4])) {
    // Insertar Proveedores después de Gastos (posición 10)
    array_splice($menu, 10, 0, [[
        'label' => 'Proveedores',
        'url' => '../proveedor/mostrar.php',
        'icon' => 'local_shipping'
    ]]);
}

// Bodega 11
// Crear condicional para que comerciales no puedan ver los listados de portátiles
if (in_array($rol, [1, 4, 5, 7])) {
    array_splice($menu, 11, 0, [[
        'label' => 'Bodega',
        'url' => '../bodega/mostrar.php',
        'icon' => 'warehouse'
    ]]);
}

# Reportes 12
if (in_array($rol, [1, 3])) {
    array_splice($menu, 12, 0, [[
        'label' => 'Reportes',
        'icon' => 'signal_cellular_alt',
        'id' => 'reportes',
        'children' => [
            ['label' => '> Productos', 'url' => '../reporte/productos.php'],
            ['label' => '> Clientes', 'url' => '../reporte/clientes.php'],
            ['label' => '> Ventas', 'url' => '../reporte/ventas.php']
        ]
    ]]);
}

# Gráficos 13
if (in_array($rol, [1, 3])) {
    array_splice($menu, 13, 0, [[
        'label' => 'Gráficos',
        'url' => '../graficos/mostrar.php',
        'icon' => 'grain'
    ]]);
}

# Marketing 14
if (in_array($rol, [1])) {
    array_splice($menu, 14, 0, [[
        'label' => 'Marketing',
        'url' => '../marketing/mostrar.php',
        'icon' => 'campaign'
    ]]);
}

# Usuarios 15
if (in_array($rol, [1])) {
    array_splice($menu, 15, 0, [[
        'label' => 'Usuarios',
        'url' => '../usuario/mostrar.php',
        'icon' => 'manage_accounts'
    ]]);
}

# Configuraciones 16
if (in_array($rol, [1, 3, 5])) {
    array_splice($menu, 16, 0, [[
        'label' => 'Configuración',
        'url' => '../cuenta/configuracion.php',
        'icon' => 'settings'
    ]]);
}

# Salir 17
// Esta por default dentro del menu para todos los usuarios

?>