<!-- menu.php-->
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

# Inicializar el menú principal
$menu = [
    [
        'label' => $panelName,
        'url' => $panelUrl,
        'icon' => 'dashboard'
    ],
];

# ==================== GRUPO 1: PROCESO ====================
if (in_array($rol, [1, 2, 3, 4, 5, 6, 7])) {
    $menu[] = [
        'label' => 'PROCESO',
        'icon' => 'factory',
        'id' => 'proceso_group',
        'children' => [
            [
                'label' => '1° TRIAGE',
                'icon' => 'assignment_turned_in',
                'class' => 'style-triage1',
                'children' => [
                    ['icon' => 'local_shipping', 'label' => '1) Proveedores', 'url' => '../proveedor/mostrar.php'],
                    ['icon' => 'barcode_reader', 'label' => '2) barcode Zebra', 'url' => '../bodega/barcode.php'],
                    ['icon' => 'app_registration', 'label' => '3) Entradas', 'url' => '../bodega/entradas.php'],
                    ['icon' => 'inventory', 'label' => '4) Inventario', 'url' => '../bodega/inventario.php'],
                    ['icon' => 'assignment', 'label' => '5) Asignar Técnico', 'url' => '../bodega/asignar.php'],
                ]
            ],
            [
                'label' => '2° TRIAGE',
                'class' => 'style-triage2',
                'icon' => 'assignment_late',
                'children' => [
                    ['label' => '◖ LISTADO TRIAGE 2', 'url' => '../bodega/lista_triage_2.php'],
                    ['label' => '◖ INGRESAR TRIAGE 2', 'url' => '../bodega/triage2.php'],
                ]
            ],
            [
                'label' => 'MANTENIMIENTO LIMPIEZA',
                'class' => 'style-mantenimiento',
                'children' => [
                    ['label' => '◖LISTADO EQUIPOS', 'url' => '../laboratorio/mostrar.php'],
                    ['label' => '◖INGRESAR', 'url' => '../lab/myl.php'],
                ]
            ],
            [
                'label' => 'ELECTRICO', 
                'url' => '../bodega/electrico.php',
                'class' => 'style-electrico'
            ],
            [
                'label' => 'ESTETICO',
                'url' => '../bodega/estetico.php',
                'class' => 'style-estetico'
            ],            
            [
                'label' => 'CONTROL DE CALIDAD',
                'icon' => 'verified',
                'id' => 'control_calidad_group',
                'url' => '../control_calidad/dashboard.php',
                'class' => 'style-control-calidad'
            ],
            [
                'label' => 'BUSINESS ROOM',
                'icon' => 'paid',
                'id' => 'control_calidad_group',
                'url' => '../b_room/mostrar.php',
                'class' => 'business'
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

// Ventas
if (in_array($rol, [1, 4, 5, 6, 7])) {
    $comercialItems[] = [
        'label' => 'VENTAS',
        'icon' => 'shopping_basket',
        'children' => [
            ['label' => '> Mostrar Ventas', 'url' => '../compra/mostrar.php'],
            ['label' => '> Nueva Venta', 'url' => '../compra/nuevo.php']
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
        'label' => 'ÁREA TÉCNICA',
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

# ==================== GRUPO 5: FINANZAS Y CONTABILIDAD ====================
$finanzasItems = [];

// Gastos Generales
if (in_array($rol, [1, 2, 3, 4])) {
    $finanzasItems[] = [
        'label' => 'Gastos Generales',
        'icon' => 'savings',
        'children' => [
            ['label' => '> Mostrar Gastos', 'url' => '../gastos/mostrar.php'],
            ['label' => '> Nuevo Gasto', 'url' => '../gastos/nuevo.php']
        ]
    ];
}

// Facturas y Comprobantes
if (in_array($rol, [1, 3, 4])) {
    $finanzasItems[] = [
        'label' => 'Facturación',
        'icon' => 'receipt',
        'children' => [
            ['label' => '> Facturas', 'url' => '../factura/mostrar.php'],
            ['label' => '> Comprobantes', 'url' => '../comprobante/mostrar.php']
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

# ==================== GRUPO 6: ANÁLISIS Y REPORTES ====================
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
            ['label' => '> Técnicos', 'url' => '../reporte/tecnicos.php']
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

# ==================== GRUPO 7: ADMINISTRACIÓN DEL SISTEMA ====================
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
if (in_array($rol, [1, 3, 5])) {
    $adminItems[] = [
        'label' => 'Usuarios',
        'url' => '../usuario/mostrar.php',
        'icon' => 'manage_accounts'
    ];
}

// Configuración
if (in_array($rol, [1, 3, 5, 7])) {
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
// Información de versión (solo para desarrollo)
if (in_array($rol, [1, 7])) { // Solo admin y bodega ven la versión
    $menu[] = [
        'label' => 'Información',
        'icon' => 'info',
        'children' => [
            ['label' => 'Versión: 0.790', 'url' => '#'],
            ['label' => 'Beτα - AGOSTO 2025', 'url' => '#']
        ]
    ];
}

// Salir - siempre al final
if (!in_array($rol, [0])) {
    $menu[] = [
        'label' => 'Salir',
        'url' => '../cuenta/salir.php',
        'icon' => 'logout'
    ];
}
?>