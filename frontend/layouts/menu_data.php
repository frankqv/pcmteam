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
        $panelUrl = '../comercial/escritorio.php';
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
		    ['label' => 'Medellín', 'url' => '../clientes/mostrar.php'],
                ]
            ]
        ]
    ],

    [
        'label' => 'Pedidos',
        'url' => '../servicio/mostrar.php',
        'icon' => 'view_timeline'
    ],
    [
        'label' => 'Servicios',
        'icon' => 'dataset',
        'id' => 'planes',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../plan/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../plan/nuevo.php'],
        ]
    ],
    [
        'label' => 'Productos',
        'icon' => 'conveyor_belt',
        'id' => 'productos',
        'children' => [
            ['label' => '> Lista de Productos', 'url' => '../producto/mostrar.php'],
            ['label' => '> Categoría','id' => 'categorias', 'url' => '../categoria/mostrar.php'
        ]]
    ],
    [
        'label' => 'Historial de Ventas',
        'icon' => 'point_of_sale',
        'id' => 'ventas',
        'url' => '../venta/mostrar.php',
    ],
    [
        'label' => 'Compras',
        'icon' => 'shopping_basket',
        'id' => 'compras',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../compra/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../compra/nuevo.php'],
        ]
    ],
    [
        'label' => 'Gastos Generales',
        'icon' => 'savings',
        'id' => 'gastos',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../gastos/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../gastos/nuevo.php'],
        ]
    ],
    [
        'label' => 'Pedidos En Ruta',
        'url' => '../pedidos_ruta/mostrar.php',
        'icon' => 'local_mall'
    ],
    [
        'label' => 'Laboratorio Técnico',
        'url' => '../laboratorio/mostrar.php',
        'icon' => 'biotech'
    ],
    [
        'label' => 'Bodega',
        'url' => '../bodega/mostrar.php',
        'icon' => 'warehouse'
    ],
    [
        'label' => 'Reportes',
        'icon' => 'signal_cellular_alt',
        'id' => 'reportes',
        'children' => [
            ['label' => '> Productos', 'url' => '../reporte/productos.php'],
            ['label' => '> Clientes', 'url' => '../reporte/clientes.php'],
            ['label' => '> Ventas', 'url' => '../reporte/ventas.php'],
        ]
    ],
    [
        'label' => 'Gráficos',
        'url' => '../graficos/mostrar.php',
        'icon' => 'grain'
    ],
    [
        'label' => 'Marketing',
        'url' => '../marketing/mostrar.php',
        'icon' => 'campaign'
    ],
    [
        'label' => 'Usuarios',
        'url' => '../usuario/mostrar.php',
        'icon' => 'manage_accounts',
    ],
    [
        'label' => 'Configuración',
        'url' => '../cuenta/configuracion.php',
        'icon' => 'settings'
    ],
    [
        'label' => 'Salir',
        'url' => '../cuenta/salir.php',
        'icon' => 'logout'
    ]
];

// Agregar Proveedores solo si el rol NO es 2, 3 o 4
if (!in_array($rol, [2, 3, 4])) {
    // Insertar Proveedores después de Gastos (posición 10)
    array_splice($menu, 10, 0, [[
        'label' => 'Proveedores',
        'url' => '../proveedor/mostrar.php',
        'icon' => 'local_shipping'
    ]]);
}

?>