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
# ==================== NOTIFICACIOENS 🔔 ====================
if (in_array($rol, [1, 2, 3, 4, 5, 6, 7])) {
    $menu[] = [
        'label' => 'NOTIFICACIONES (Build)',
        'icon' => 'notifications',
        'id' => 'notificaciones_group',
    ];
}

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
                    ['label' => '◖ INGRESAR TRIAGE 2', 'url' => '../bodega/triage2.php'],
                    ['label' => '◖ HISTORICO TRIAGE 2', 'url' => '../bodega/lista_triage_2.php'],
                    ['label' => '●  Asignar Técnico MYL', 'url' => '../bodega/asignar.php'],
                ]
            ],
            [
                'label' => 'MANTENIMIENTO LIMPIEZA',
                'class' => 'style-mantenimiento',
                'children' => [
                    ['label' => '◖LISTADO EQUIPOS', 'url' => '../laboratorio/mostrar.php'],
                    ['label' => '◖LISTA PARTES', 'url' => '../bodega/lista_parte.php'],
                    ['label' => '◖SOLICITUD PARTE', 'url' => '../bodega/solicitar_parte.php'],
                    ['label' => '● HISTORIAL MANTENIMIENTO', 'url' => '../laboratorio/historial_lab.php'],
                ]
            ],
            [
                'label' => 'ELECTRICO', 
                'class' => 'style-electrico',
                'children' => [
                    ['label' => '◖REPARAR', 'url' => '../bodega/electrico.php',],
                    ['label' => '● HISTORIAL', 'url' => '../bodega/historial_electrico.php'],
                ]
            ],
            [
                'label' => 'ESTETICO',
                'class' => 'style-estetico',
                'children' => [
                    ['label' => '◖REPARAR', 'url' => '../bodega/estetico.php',],
                    ['label' => '◖LISTADO', 'url' => '../bodega/lista_estetico.php'],
                    ['label' => '● HISTORIAL', 'url' => '../bodega/historial_estetico.php'],
                ]
            ],            
            [
                'label' => 'CONTROL DE CALIDAD',
                'icon' => 'verified',
                'id' => 'control_calidad_group',
                'url' => '../control_calidad/mostrar.php',
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
# ==================== GRUPO 2: Proceso de Venta ====================
$ventaItems = [];
// Ventas
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $ventaItems[] = [
        'label' => 'LISTA DE PRODUCTOS',
        'icon' => 'assignment_add',
        'url' => '../bodega/lista_producto.php'
    ];
}
if (in_array($rol, [1, 3, 4, 5,6,7])) {
    $ventaItems[] = [
        'label' => 'CLIENTES',
        'icon' => 'groups',
        'url' => '../clientes/mostrar.php'
    ];
}
// Clientes por Tienda
if (in_array($rol, [1, 2, 4, 5, 7])) {
    $ventaItems[] = [
        'label' => 'CLIENTES × LOCAL',
        'icon' => 'store',
        'children' => [
            ['label' => ' > Puente Aranda', 'url' => '../clientes/bodega.php'],
            ['label' => ' > Unilago', 'url' => '../clientes/unilago.php'],
            ['label' => ' > Cúcuta', 'url' => '../clientes/cucuta.php'],
            ['label' => ' > Medellín', 'url' => '../clientes/medellin.php']
        ]
    ];
}
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $ventaItems[] = [
    'label' => 'CATALOGO',
    'icon' => 'book',
    'url' => '../venta/catalogo.php'
    ];
}
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $ventaItems[] = [
        'label' => 'VENDER',
        'icon' => 'request_page',
        'url' => '../venta/nuevo.php'
    ];
}
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $ventaItems[] = [
        'label' => 'HISTORIA VENTA',
        'icon' => 'store',
        'url' => '../venta/mostrar.php'
    ];
}
// Marketing
if (in_array($rol, [1])) {
    $ventaItems[] = [
        'icon' => 'touch_app',
        'label' => 'MARKETING',
        'url' => '../marketing/mostrar.php'
    ];
}
if (in_array($rol, [1, 2, 4, 5])) {
    $ventaItems[] = [
        'label' => 'Venta De Servicio',
        'icon' => 'engineering',
        'id' => 'tecnico_group',
        'url' => '../servicio/mostrar.php',
    ];
}
if (!empty( $ventaItems)) {
    $menu[] = [
        'label' => 'VENTAS',
        'icon' => 'payments',
        'id' => 'venta_group',
        'children' =>  $ventaItems
    ];
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
# ==================== GRUPO 3: Proceso de Despacho ====================
$despachoItems = [];
// Ordenes pendientes de despacho
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $despachoItems[] = [
        'label' => '📦 ÓRDENES PENDIENTES',
        'url' => '../despacho/pendientes.php'
    ];
}
// Historial de despachos
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $despachoItems[] = [
        'label' => '📄 HISTORIAL DESPACHOS',
        'url' => '../despacho/historial.php'
    ];
}
// Si hay elementos en despacho, agregar al menú
if (!empty($despachoItems)) {
    $menu[] = [
        'label' => 'DESPACHO',
        'icon' => 'local_shipping',
        'id'   => 'despacho_group',
        'children' => $despachoItems
    ];
}
# ==================== GRUPO 5: FINANZAS Y CONTABILIDAD ====================
$finanzasItems = [];
// Ingresos 
if (in_array($rol, [1, 2, 3, 4])) {
    $finanzasItems[] = [
        'label' => 'INGRESOS (Build)',
        'icon' => 'signal_cellular_alt',
        'url' => '../ingresos/mostrar.php'
    ];
}
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
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $finanzasItems[] = [
        'label' => 'HISTORIA VENTA',
        'icon' => 'store',
        'url' => '../venta/mostrar.php'
    ];
}
// Si hay elementos en finanzas, agregar al menú
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
// Graficos Y estadisticas de tecnicos
if (in_array($rol, [1, 3])) {
    $reportesItems[] = [
        'label' => 'TECNICOS (BUILD)',
        'icon' => 'signal_cellular_alt',
        'url' => 'bodega/graficos_tecnicos.php' ];
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
if (in_array($rol, [1, 3, 5])) {
    $adminItems[] = [
        'label' => 'Configuración',
        'url' => '../cuenta/configuracion.php',
        'icon' => 'settings'
    ];
}
if (in_array($rol, [1,2,3,4,5,6,7])){
    $adminItems[] = [
        'label' => 'Perfil',
        'url'   => '../cuenta/perfil.php',
        'icon'  => 'settings_account_box'
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
            ['label' =>  'αlfa - Septiembre 2025', 'url' => '#']
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