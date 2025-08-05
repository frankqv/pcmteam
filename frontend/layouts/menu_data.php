<!-- layout/menu_data.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rol = $_SESSION['rol'] ?? 0; // Obtener el rol de la sesión

// Configuración del panel según rol
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

# Menu Lateral de la Aplicación - Reorganizado por Grupos Funcionales
$menu = [
    [
        'label' => $panelName,
        'url' => $panelUrl,
        'icon' => 'dashboard'
    ],
];



# ==================== GRUPO 1: TRIAGE Y ATENCIÓN PRIORITARIA ====================
if (in_array($rol, [1, 2, 3, 4, 5, 6, 7])) {
    $menu[] = [
        'label' => 'PROCESO',
        'icon' => 'emergency',
        'id' => 'triage_group',
        'children' => [
            [
                'label' => 'PROCESO',
                'icon' => 'dashboard',
                'children' => [
                    ['label' => ' > Puente Aranda', 'url' => '../clientes/bodega.php'],
                    ['label' => ' > Unilago', 'url' => '../clientes/unilago.php'],
                    ['label' => ' > Cúcuta', 'url' => '../clientes/cucuta.php'],
                    ['label' => ' > Medellín', 'url' => '../clientes/medellin.php']
                ]
            ],
            [
                'label' => '2° TRIAGE',
                'icon' => 'dashboard',
                'children' => [
                    ['label' => ' > Puente Aranda', 'url' => '../clientes/bodega.php'],
                    ['label' => ' > Unilago', 'url' => '../clientes/unilago.php'],
                    ['label' => ' > Cúcuta', 'url' => '../clientes/cucuta.php'],
                    ['label' => ' > Medellín', 'url' => '../clientes/medellin.php']
                ]
            ],
            [
                'label' => '1° TRIAGE',
                'icon' => 'dashboard',
                'children' => [
                    ['icon' => 'store', 'label' => '4) Inventario', 'url' => '../bodega/inventario.php'],
                    ['icon' => 'app_registration', 'label' => '3) Entradas', 'url' => '../bodega/entradas.php'],
                    ['icon' => 'barcode_reader', 'label' => '2) BARCODE ZEBRA', 'url' => '../bodega/barcode.php'],
                    ['icon' => 'local_shipping', 'label' => '1) Proveedores', 'url' => '../proveedor/mostrar.php'],
                    
                ]
            ]
        ]
    ];
}

# ==================== GRUPO 2: GESTIÓN COMERCIAL ====================
$comercialItems = [];

// Clientes
if (in_array($rol, [1, 2, 4, 5, 7])) {
    $comercialItems[] = [
        'label' => 'CLIENTES',
        'icon' => 'group',
        'url' => '../clientes/mostrar.php',
    ];
}

// Clientes por Tienda
if (in_array($rol, [1, 2, 4, 5])) {
    $comercialItems[] = [
        'label' => 'Mis Clientes',
        'icon' => 'store',
        'children' => [
            ['label' => ' > Puente Aranda', 'url' => '../clientes/bodega.php'],
            ['label' => ' > Unilago', 'url' => '../clientes/unilago.php'],
            ['label' => ' > Cúcuta', 'url' => '../clientes/cucuta.php'],
            ['label' => ' > Medellín', 'url' => '../clientes/medellin.php']
        ]
    ];
}

// Compras
if (in_array($rol, [1, 4, 5, 6, 7])) {
    $comercialItems[] = [
        'label' => 'VENTAS',
        'icon' => 'shopping_basket',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../compra/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../compra/nuevo.php']
        ]
    ];
}


// Historial de Ventas
if (in_array($rol, [1, 3, 4])) {
    $comercialItems[] = [
        'label' => 'Historial de Ventas',
        'icon' => 'point_of_sale',
        'url' => '../venta/mostrar.php'
    ];
}

// Marketing
if (in_array($rol, [1])) {
    $comercialItems[] = [
        'label' => 'Marketing',
        'url' => '../marketing/mostrar.php',
        'icon' => 'campaign'
    ];
}

if (!empty($comercialItems)) {
    $menu[] = [
        'label' => 'COMERCIAL',
        'icon' => 'storefront',
        'id' => 'comercial_group',
        'children' => $comercialItems
    ];
}

# ==================== GRUPO 3: OPERACIONES TÉCNICAS ====================
$tecnicoItems = [];

// Servicios Técnicos
if (in_array($rol, [1, 4, 5, 6, 7])) {
    $tecnicoItems[] = [
        'label' => 'Servicios Técnicos',
        'url' => '../servicio/mostrar.php',
        'icon' => 'view_timeline'
    ];
}

// Mis Servicios
if (in_array($rol, [5, 6, 7])) {
    $tecnicoItems[] = [
        'label' => 'Mis Servicios',
        'icon' => 'dataset',
        'url' => '../mis_servicios/mostrar.php'
    ];
}

// Laboratorio Técnico
if (in_array($rol, [1, 4, 5, 6])) {
    $tecnicoItems[] = [
        'label' => 'Laboratorio Técnico',
        'url' => '../laboratorio/mostrar.php',
        'icon' => 'biotech'
    ];
}

if (!empty($tecnicoItems)) {
    $menu[] = [
        'label' => 'AREA TÉCNICA',
        'icon' => 'engineering',
        'id' => 'tecnico_group',
        'children' => $tecnicoItems
    ];
}

# ==================== GRUPO 4: INVENTARIO Y LOGÍSTICA ====================
$inventarioItems = [];

// Productos
if (in_array($rol, [1, 4, 5, 6, 7])) {
    $inventarioItems[] = [
        'label' => 'Productos',
        'icon' => 'conveyor_belt',
        'children' => [
            ['label' => '> Lista de Productos', 'url' => '../producto/mostrar.php'],
            ['label' => '> Categoría', 'url' => '../categoria/mostrar.php']
        ]
    ];
}

// Bodega
if (in_array($rol, [1, 4, 5, 7])) {
    $inventarioItems[] = [
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

// Alistamientos
if (in_array($rol, [1, 4, 5])) {
    $inventarioItems[] = [
        'label' => 'Alistamientos',
        'url' => '../pedidos_ruta/mostrar.php',
        'icon' => 'unarchive'
    ];
}

if (!empty($inventarioItems)) {
    $menu[] = [
        'label' => 'LOGÍSTICA',
        'icon' => 'inventory_2',
        'id' => 'inventario_group',
        'children' => $inventarioItems
    ];
}

# ==================== GRUPO 5: COMPRAS Y PROVEEDORES ====================


# ==================== GRUPO 6: FINANZAS Y CONTABILIDAD ====================
$finanzasItems = [];

// Gastos Generales
if (in_array($rol, [1, 2, 3, 4])) {
    $finanzasItems[] = [
        'label' => 'Gastos Generales',
        'icon' => 'savings',
        'children' => [
            ['label' => '> Mostrar', 'url' => '../gastos/mostrar.php'],
            ['label' => '> Nuevo', 'url' => '../gastos/nuevo.php']
        ]
    ];
}

if (!empty($finanzasItems)) {
    $menu[] = [
        'label' => 'CONTABILIDAD',
        'icon' => 'account_balance',
        'id' => 'finanzas_group',
        'children' => $finanzasItems
    ];
}

# ==================== GRUPO 7: ANÁLISIS Y REPORTES ====================
$reportesItems = [];

// Reportes
if (in_array($rol, [1, 3])) {
    $reportesItems[] = [
        'label' => 'Reportes',
        'icon' => 'signal_cellular_alt',
        'children' => [
            ['label' => '> Productos', 'url' => '../reporte/productos.php'],
            ['label' => '> Clientes', 'url' => '../reporte/clientes.php'],
            ['label' => '> Ventas', 'url' => '../reporte/ventas.php'],
        ]
    ];
}

// Gráficos
if (in_array($rol, [1, 3])) {
    $reportesItems[] = [
        'label' => 'Gráficos',
        'url' => '../graficos/mostrar.php',
        'icon' => 'grain'
    ];
}

if (!empty($reportesItems)) {
    $menu[] = [
        'label' => 'ANÁLISIS Y REPORTES',
        'icon' => 'analytics',
        'id' => 'reportes_group',
        'children' => $reportesItems
    ];
}

# ==================== GRUPO 8: ADMINISTRACIÓN DEL SISTEMA ====================
$adminItems = [];

// Documentos Generales
if (!in_array($rol, [0])) {
    $adminItems[] = [
        'label' => 'Docs Generales',
        'icon' => 'library_books',
        'url' => '../docs/mostrar.php',
    ];
}

// Usuarios
if (in_array($rol, [1,3,5])) {
    $adminItems[] = [
        'label' => 'Usuarios',
        'url' => '../usuario/mostrar.php',
        'icon' => 'manage_accounts'
    ];
}

// Configuración
if (in_array($rol, [1, 3, 5,7])) {
    $adminItems[] = [
        'label' => 'Configuración',
        'url' => '../cuenta/configuracion.php',
        'icon' => 'settings'
    ];
}

if (!empty($adminItems)) {
    $menu[] = [
        'label' => 'ADMINISTRACIÓN',
        'icon' => 'admin_panel_settings',
        'id' => 'admin_group',
        'children' => $adminItems
    ];
}

# ==================== OPCIONES FINALES ====================
// Salir
if (!in_array($rol, [0])) {
    $menu[] = [
        'label' => 'Salir',
        'url' => '../cuenta/salir.php',
        'icon' => 'logout'
    ];
}

// Información de versión
if (!in_array($rol, [1, 2])) {
    echo '<p><b>Version</b>0.700</p>';
}

$menu[] = [
    'label' => '<i>Version JULIO 2025</i> ',
];

$menu[] = [
    'label' => '<i>Version Beta</i> '
];

?>