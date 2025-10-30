<!-- menu_data.php - Sistema de Menú Dinámico PCMARKETTEAM -->
<?php
/**
 * ========================================================================
 * MENU_DATA.PHP - Configuración del Menú Principal del Sistema
 * ========================================================================
 * Este archivo genera el menú lateral dinámicamente según el rol del usuario.
 * ROLES DEL SISTEMA:
 * ------------------
 * 1 = Administrador    → Acceso total al sistema
 * 2 = Cliente Genérico → Acceso limitado a ventas y servicios
 * 3 = Contable         → Finanzas, ventas, reportes
 * 4 = Comercial        → Ventas, clientes, alistamientos
 * 5 = Jefe Técnico     → Proceso técnico, inventario, equipo
 * 6 = Técnico          → Proceso técnico, asignaciones
 * 7 = Bodega           → Inventario, logística*
 * ========================================================================
 */
// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener rol del usuario actual
$rol = $_SESSION['rol'] ?? 0;

// Obtener información completa del usuario (incluyendo idsede)
$userIdSede = null;
if (isset($_SESSION['id'])) {
    require_once __DIR__ . '/../../config/ctconex.php';
    try {
        $sqlUserInfo = "SELECT idsede FROM usuarios WHERE id = :id LIMIT 1";
        $stmtUserInfo = $connect->prepare($sqlUserInfo);
        $stmtUserInfo->execute([':id' => $_SESSION['id']]);
        $userData = $stmtUserInfo->fetch(PDO::FETCH_ASSOC);
        $userIdSede = !empty($userData['idsede']) ? trim($userData['idsede']) : null;
    } catch (PDOException $e) {
        // Si hay error, continuar sin sede
        $userIdSede = null;
    }
}
// 1. CONFIGURACIÓN DEL PANEL PRINCIPAL (Escritorio por Rol)
// ========================================================================
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
// Inicializar el menú con el enlace al escritorio
$menu = [
    [
        'label' => $panelName,
        'url' => $panelUrl,
        'icon' => 'dashboard'
    ]
];
// 2. NOTIFICACIONES
// Roles: Admin, Cliente, Contable, Jefe Técnico, Técnico, Bodega
if (in_array($rol, [1, 2, 3, 5, 6, 7])) {
    $menu[] = [
        'label' => 'NOTIFICACIONES',
        'icon' => 'notifications',
        'id' => 'notificaciones_group',
        'children' => [
            [
                'icon' => 'assignment',
                'label' => 'Alistamiento',
                'url' => '../despacho/historial_solicitudes_alistamiento.php'
            ],
            [
                'icon' => 'assignment_ind',
                'label' => 'Equipos Asignados',
                'url' => '../bodega/asignar.php'
            ]
        ]
    ];
}
// 3. PROCESO TÉCNICO (Flujo de trabajo completo de equipos)
// Roles: Todos (temporalmente - revisar permisos reales)
if (in_array($rol, [1, 2, 3, 4, 5, 6, 7])) {
    $menu[] = [
        'label' => 'PROCESO',
        'icon' => 'factory',
        'id' => 'proceso_group',
        'children' => [
            // 3.1. Triage Fase 1 - Recepción e ingreso
            [
                'label' => '1° TRIAGE',
                'icon' => 'assignment_turned_in',
                'class' => 'style-triage1',
                'children' => [
                    [
                        'icon' => 'local_shipping',
                        'label' => '1) Proveedores',
                        'url' => '../proveedor/mostrar.php'
                    ],
                    [
                        'icon' => 'barcode_reader',
                        'label' => '2) Barcode Zebra',
                        'url' => '../bodega/barcode.php'
                    ],
                    [
                        'icon' => 'app_registration',
                        'label' => '3) Entradas',
                        'url' => '../bodega/entradas.php'
                    ],
                    [
                        'icon' => 'inventory',
                        'label' => '4) Inventario',
                        'url' => '../bodega/inventario.php'
                    ],
                    [
                        'icon' => 'assignment',
                        'label' => '5) Asignar Técnico',
                        'url' => '../bodega/asignar.php'
                    ]
                ]
            ],
            // 3.2. Triage Fase 2 - Diagnóstico avanzado
            [
                'label' => '2° TRIAGE',
                'icon' => 'assignment_late',
                'class' => 'style-triage2',
                'children' => [
                    [
                        'label' => '▹ Ingresar Triage 2',
                        'url' => '../bodega/triage2.php'
                    ],
                    [
                        'label' => '◇ Histórico Triage 2',
                        'url' => '../bodega/lista_triage_2.php'
                    ],
                    [
                        'label' => '▹ Asignar Técnico MYL',
                        'url' => '../bodega/asignar.php'
                    ]
                ]
            ],
            // 3.3. Mantenimiento y Limpieza
            [
                'label' => 'MANTENIMIENTO Y LIMPIEZA',
                'icon' => 'build',
                'class' => 'style-mantenimiento',
                'children' => [
                    [
                        'label' => 'Listado de Equipos',
                        'url' => '../laboratorio/mostrar.php'
                    ],
                    [
                        'label' => 'Lista de Partes',
                        'url' => '../bodega/lista_parte.php'
                    ],
                    [
                        'label' => 'Solicitar Parte',
                        'url' => '../bodega/solicitar_parte.php'
                    ],
                    [
                        'label' => 'Historial Mantenimiento',
                        'url' => '../laboratorio/historial_lab.php'
                    ]
                ]
            ],
            // 3.4. Reparación Eléctrica
            [
                'label' => 'ELÉCTRICO',
                'icon' => 'electrical_services',
                'class' => 'style-electrico',
                'children' => [
                    [
                        'label' => 'Reparar',
                        'url' => '../bodega/electrico.php'
                    ],
                    [
                        'label' => 'Historial',
                        'url' => '../bodega/historial_electrico.php'
                    ]
                ]
            ],
            // 3.5. Reparación Estética
            [
                'label' => 'ESTÉTICO',
                'icon' => 'palette',
                'class' => 'style-estetico',
                'children' => [
                    [
                        'label' => 'Reparar',
                        'url' => '../bodega/estetico.php'
                    ],
                    [
                        'label' => 'Listado',
                        'url' => '../bodega/lista_estetico.php'
                    ],
                    [
                        'label' => 'Historial',
                        'url' => '../bodega/historial_estetico.php'
                    ]
                ]
            ],
            // 3.6. Control de Calidad
            [
                'label' => 'CONTROL DE CALIDAD',
                'icon' => 'verified',
                'id' => 'control_calidad_group',
                'class' => 'style-control-calidad',
                'children' => [
                    [
                        'label' => 'Ingresar',
                        'url' => '../control_calidad/mostrar.php'
                    ],
                    [
                        'label' => 'Historial',
                        'url' => '../control_calidad/historial.php'
                    ]
                ]
            ],
            // 3.7. Business Room - Equipos listos para venta
            [
                'label' => 'BUSINESS ROOM',
                'icon' => 'store',
                'id' => 'business_room_group',
                'url' => '../b_room/mostrar.php',
                'class' => 'business'
            ]
        ]
    ];
}
// 3.9 ALISTAMIENTOS (Preparación de equipos para venta)
// ========================================================================
$comercialMenu = [];
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
  $comercialMenu = [
    ['label' => 'Panel Principal', 'icon' => 'dashboard', 'url' =>'../comercial/escritorio.php'],
    ['label' => 'Lista de Clientes', 'icon' => 'list', 'url' => '../clientes/mostrar.php'],
    ['label' => 'Nuevo Cliente', 'icon' => 'group_add', 'url' => '../clientes/nuevo.php'],
    ['label' => 'Catalogo', 'icon' => 'auto_stories', 'url' => '../b_room/mostrar.php'],
    ['label' => 'Nueva Venta', 'icon' => 'add_shopping_cart', 'url' => '../comercial/nueva_venta.php'],
    ['label' => 'Crear Alistamiento',  'icon' => 'add_circle', 'url' => '../venta/preventa.php'],
    ['label' => 'Mi Solitud', 'icon' => 'history', 'url' => '../venta/historico_preventa.php'],
    ['label' => 'Historial de Ventas', 'icon' => 'receipt_long', 'url' => '../comercial/historico_venta.php'],
    ['label' => 'Órdenes Pendientes', 'icon' => 'pending_actions', 'url' => '../despacho/pedientes.php' ],
    ['label' => 'Historico Despacho', 'icon' => 'local_shipping', 'url' => '../despacho/historial.php' ]
  ];
}



// Seccion padre
if (!empty($comercialMenu)) {
    $menu[] = [
        'label' => 'Comercial',
        'icon' => 'payments',
        'id' => 'comercial_venta',
        'children' => $comercialMenu
    ];
}

// 4. ALISTAMIENTOS (Preparación de equipos para venta)
// ========================================================================
$alistamientoItems = [];
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $alistamientoItems[] = [
        'label' => 'Crear Alistamiento',
        'icon' => 'add_circle',
        'url' => '../venta/preventa.php'
    ];
}
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $alistamientoItems[] = [
        'label' => 'MI Solicitud',
        'icon' => 'history',
        'url' => '../venta/historico_preventa.php'
    ];
}
// Roles: Admin, Contable, Jefe Técnico, Técnico, Bodega
if (in_array($rol, [1, 3, 5, 6, 7])) {
    $alistamientoItems[] = [
        'label' => 'Histórico Completo',
        'icon' => 'folder_open',
        'url' => '../despacho/historial_solicitudes_alistamiento.php'
    ];
}
// Agregar sección ALISTAMIENTOS al menú si hay elementos
if (!empty($alistamientoItems)) {
    $menu[] = [
        'label' => 'ALISTAMIENTOS',
        'icon' => 'playlist_add_check',
        'id' => 'alistamiento_group',
        'children' => $alistamientoItems
    ];
}
/*
// 5. VENTAS (Proceso comercial completo)
// ========================================================================
$ventaItems = [];
// 5.1. Lista de Productos
// Roles: Admin, Cliente, Contable, Comercial, Jefe Técnico, Bodega
// 5.2. Gestión de Clientes
// Solo se muestra si el usuario tiene una sede asignada (idsede no vacío)
// El Admin (rol 1) siempre tiene acceso completo
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    // Admin siempre ve Clientes, los demás solo si tienen sede asignada
    if ($rol == 1 || !empty($userIdSede)) {
        $ventaItems[] = [
            'label' => 'Clientes',
            'icon' => 'groups',
            'url' => '../clientes/mostrar.php'
        ];
    }
}
// 5.3. Clientes por Sede
// Roles: Admin, Cliente, Comercial, Jefe Técnico, Bodega
// Solo se muestra si el usuario tiene una sede asignada (idsede no vacío)
if (in_array($rol, [1, 2, 4, 5, 7])) {
    // Admin siempre ve Clientes por Sede, los demás solo si tienen sede asignada
    if ($rol == 1 || !empty($userIdSede)) {
        $ventaItems[] = [
            'label' => 'Clientes por Sede',
            'icon' => 'store',
            'children' => [
                ['label' => 'Puente Aranda', 'url' => '../clientes/bodega.php'],
                ['label' => 'Unilago', 'url' => '../clientes/unilago.php'],
                ['label' => 'Cúcuta', 'url' => '../clientes/cucuta.php'],
                ['label' => 'Medellín', 'url' => '../clientes/medellin.php']
            ]
        ];
    }
}
// 5.4. Catálogo de Productos
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $ventaItems[] = [
        'label' => 'Catálogo',
        'icon' => 'book',
        'url' => '../venta/catalogo.php'
    ];
}
// 5.5. Nueva Venta (Simple)
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $ventaItems[] = [
        'label' => 'Vender',
        'icon' => 'point_of_sale',
        'url' => '../venta/nuevo.php'
    ];
}
// 5.6. Venta Múltiple
// Roles: Admin, Comercial
if (in_array($rol, [1, 4])) {
    $ventaItems[] = [
        'label' => 'Venta Múltiple',
        'icon' => 'shopping_cart',
        'url' => '../venta/nuevo_multiproducto.php'
    ];
}
// 5.7. Historial de Ventas
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $ventaItems[] = [
        'label' => 'Historial de Ventas',
        'icon' => 'receipt_long',
        'url' => '../venta/mostrar.php'
    ];
}
// 5.8. Marketing
// Roles: Solo Admin
if (in_array($rol, [1])) {
    $ventaItems[] = [
        'label' => 'Marketing',
        'icon' => 'campaign',
        'url' => '../marketing/mostrar.php'
    ];
}
// 5.9. Venta de Servicios
// Roles: Admin, Cliente, Comercial, Jefe Técnico
if (in_array($rol, [1, 2, 4, 5])) {
    $ventaItems[] = [
        'label' => 'Venta de Servicio',
        'icon' => 'engineering',
        'id' => 'servicio_group',
        'url' => '../servicio/mostrar.php'
    ];
}
// Agregar sección VENTAS al menú si hay elementos
if (!empty($ventaItems)) {
    $menu[] = [
        'label' => 'VENTAS',
        'icon' => 'payments',
        'id' => 'venta_group',
        'children' => $ventaItems
    ];
} */
// 6. DESPACHO (Logística y entrega de equipos)
// ========================================================================
$despachoItems = [];
// 6.1. Historial de Alistamiento
// Roles: Admin, Contable, Jefe Técnico, Técnico, Bodega
if (in_array($rol, [1, 3, 5, 6, 7])) {
    $despachoItems[] = [
        'label' => 'Historial de Alistamiento',
        'icon' => 'assignment',
        'url' => '../despacho/historial_solicitudes_alistamiento.php'
    ];
}
/*
// 6.2. Órdenes Pendientes
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $despachoItems[] = [
        'label' => 'Órdenes Pendientes',
        'icon' => 'pending_actions',
        'url' => '../despacho/pendientes.php'
    ];
}
// 6.3. Historial de Despachos
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $despachoItems[] = [
        'label' => 'Historico Despacho',
        'icon' => 'local_shipping',
        'url' => '../despacho/historial.php'
    ];
}
// Agregar sección DESPACHO al menú si hay elementos
if (!empty($despachoItems)) {
    $menu[] = [
        'label' => 'DESPACHO',
        'icon' => 'local_shipping',
        'id' => 'despacho_group',
        'children' => $despachoItems
    ];
} */
// 7. CONTABILIDAD (Finanzas y facturación)
// ========================================================================
$contabilidadItems = [];
// 7.1. Ingresos
// Roles: Admin, Cliente, Contable, Comercial
if (in_array($rol, [1, 2, 3, 4])) {
    $contabilidadItems[] = [
        'label' => 'Ingresos',
        'icon' => 'trending_up',
        'url' => '../ingresos/mostrar.php'
    ];
}
// 7.2. Gastos Generales
// Roles: Admin, Cliente, Contable, Comercial
if (in_array($rol, [1, 2, 3, 4])) {
    $contabilidadItems[] = [
        'label' => 'Gastos Generales',
        'icon' => 'account_balance_wallet',
        'children' => [
            ['label' => 'Mostrar Gastos', 'url' => '../gastos/mostrar.php'],
            ['label' => 'Nuevo Gasto', 'url' => '../gastos/nuevo.php']
        ]
    ];
}
// 7.3. Facturación
// Roles: Admin, Contable, Comercial
if (in_array($rol, [1, 3, 4])) {
    $contabilidadItems[] = [
        'label' => 'Facturación',
        'icon' => 'receipt',
        'children' => [
            ['label' => 'Facturas', 'url' => '../factura/mostrar.php'],
            ['label' => 'Comprobantes', 'url' => '../comprobante/mostrar.php']
        ]
    ];
}
// 7.4. Historial de Ventas (También en sección Ventas)
if (in_array($rol, [1, 3, 4, 5, 6, 7])) {
    $contabilidadItems[] = [
        'label' => 'Historial de Ventas',
        'icon' => 'receipt_long',
        'url' => '../venta/mostrar.php'
    ];
}
// Agregar sección CONTABILIDAD al menú si hay elementos
if (!empty($contabilidadItems)) {
    $menu[] = [
        'label' => 'CONTABILIDAD',
        'icon' => 'account_balance',
        'id' => 'contabilidad_group',
        'children' => $contabilidadItems
    ];
}
// 8. ANÁLISIS Y REPORTES (Business Intelligence)
// ========================================================================
$reportesItems = [];
// 8.1. Reportes Detallados
// Roles: Admin, Contable
if (in_array($rol, [1, 3])) {
    $reportesItems[] = [
        'label' => 'Reportes',
        'icon' => 'summarize',
        'children' => [
            ['label' => 'Productos', 'url' => '../reporte/productos.php'],
            ['label' => 'Clientes', 'url' => '../reporte/clientes.php'],
            ['label' => 'Ventas', 'url' => '../reporte/ventas.php'],
            ['label' => 'Técnicos', 'url' => '../reporte/tecnicos.php']
        ]
    ];
}
// 8.2. Estadísticas de Técnicos
// Roles: Admin, Contable
if (in_array($rol, [1, 3])) {
    $reportesItems[] = [
        'label' => 'Estadísticas Técnicos',
        'icon' => 'bar_chart',
        'url' => '../bodega/graficos_tecnicos.php'
    ];
}
// 8.3. Gráficos Generales
// Roles: Admin, Contable
if (in_array($rol, [1, 3])) {
    $reportesItems[] = [
        'label' => 'Gráficos',
        'icon' => 'show_chart',
        'url' => '../graficos/mostrar.php'
    ];
}
// Agregar sección ANÁLISIS Y REPORTES al menú si hay elementos
if (!empty($reportesItems)) {
    $menu[] = [
        'label' => 'ANÁLISIS Y REPORTES',
        'icon' => 'analytics',
        'id' => 'reportes_group',
        'children' => $reportesItems
    ];
}
// 9. ADMINISTRACIÓN (Gestión del sistema y usuarios)
$adminItems = [];
// 9.1. Documentos Generales
// Roles: Todos excepto invitados (rol 0)
if (!in_array($rol, [0])) {
    $adminItems[] = [
        'label' => 'Docs Generales',
        'icon' => 'library_books',
        'url' => '../docs/mostrar.php'
    ];
}
// 9.2. Gestión de Usuarios
// Roles: Admin, Contable, Jefe Técnico
if (in_array($rol, [1, 3, 5])) {
    $adminItems[] = [
        'label' => 'Usuarios',
        'icon' => 'manage_accounts',
        'url' => '../usuario/mostrar.php'
    ];
}
// 9.3. Configuración del Sistema
// Roles: Admin, Contable, Jefe Técnico
if (in_array($rol, [1, 3, 5])) {
    $adminItems[] = [
        'label' => 'Configuración',
        'icon' => 'settings',
        'url' => '../cuenta/configuracion.php'
    ];
}
// 9.4. Mi Perfil
// Roles: Todos los usuarios autenticados
if (in_array($rol, [1, 2, 3, 4, 5, 6, 7])) {
    $adminItems[] = [
        'label' => 'Mi Perfil',
        'icon' => 'account_circle',
        'url' => '../cuenta/perfil.php'
    ];
}
// Agregar sección ADMINISTRACIÓN al menú si hay elementos
if (!empty($adminItems)) {
    $menu[] = [
        'label' => 'ADMINISTRACIÓN',
        'icon' => 'admin_panel_settings',
        'id' => 'admin_group',
        'children' => $adminItems
    ];
}
// 10. DESARROLLO (Solo para testing - ELIMINAR EN PRODUCCIÓN)
// TODO: Eliminar esta sección antes de deployment en producción
if (in_array($rol, [1])) { // Solo Administrador
    $panelesItems = [
        ['label' => 'Panel Admin', 'url' => '../administrador/escritorio.php'],
        ['label' => 'Panel Usuario Genérico', 'url' => '../u_generico/escritorio.php'],
        ['label' => 'Panel Contable', 'url' => '../contable/escritorio.php'],
        ['label' => 'Panel Comercial', 'url' => '../comercial/escritorio.php'],
        ['label' => 'Panel Jefe Técnico', 'url' => '../jtecnico/escritorio.php'],
        ['label' => 'Panel Técnico', 'url' => '../tecnico/escritorio.php'],
        ['label' => 'Panel Bodega', 'url' => '../bodega/escritorio.php'],
        ['label' => 'Panel Avanzado (Dev)', 'url' => '../administrador/dev.php']
    ];
    if (!empty($panelesItems)) {
        $menu[] = [
            'label' => 'DESARROLLO',
            'icon' => 'code',
            'id' => 'dev_group',
            'children' => $panelesItems
        ];
    }
}
// 11. INFORMACIÓN DEL SISTEMA
// Versión del sistema (Solo Admin y Bodega)
if (in_array($rol, [1, 7])) {
    $menu[] = [
        'label' => 'Información',
        'icon' => 'info',
        'children' => [
            ['label' => 'Versión: 0.790', 'url' => '#'],
            ['label' => 'Alfa - Octubre 2025', 'url' => '#']
        ]
    ];
}
// 12. CERRAR SESIÓN
// Opción de salir - siempre visible para usuarios autenticados
if (!in_array($rol, [0])) {
    $menu[] = [
        'label' => 'Salir',
        'icon' => 'logout',
        'url' => '../cuenta/salir.php'
    ];
}
?>