// Mis Servicios
if (in_array($rol, [1, 5, 6, 7])) {
    $tecnicoItems[] = [
        'label' => 'Mis Servicios',
        'icon' => 'dataset',
        'url' => '../mis_servicios/mostrar.php'
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
            ['label' => '> Despacho', 'url' => '../bodega/despacho.php'],
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













# ==================== GRUPO 4: OPERACIONES TÉCNICAS ====================
$tecnicoItems = [];
// Servicios Técnicos
if (in_array($rol, [1, 4, 5, 6, 7])) {
    $tecnicoItems[] = [
        'label' => '◖Lista Servicios Técnicos',
        'url' => '../servicio/mostrar.php',
    ];
}
// Laboratorio Técnico
if (in_array($rol, [1, 4, 5, 6])) {
    $tecnicoItems[] = [
        'label' => '◖Mateniemiento Y Liempieza',
        'url' => '../laboratorio/mostrar.php',
    ];
}
if (!empty($tecnicoItems)) {
    $menu[] = [
        'label' => 'Venta De Servicio',
        'icon' => 'engineering',
        'id' => 'tecnico_group',
        'children' => $tecnicoItems
    ];
}


if (!empty($tecnicoItems)) {
    $menu[] = [
        'label' => 'Venta De Servicio',
        'icon' => 'engineering',
        'id' => 'tecnico_group',
        'url' => '../servicio/mostrar.php',
        'children' => $tecnicoItems
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
if (!empty($comercialItems)) {
    $menu[] = [
        'label' => 'COMERCIAL',
        'icon' => 'storefront',
        'id' => 'comercial_group',
        'children' => $comercialItems
    ];
}