<?php
// selecion de varios productos del catalogo | Falta correciones
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4,5,6,7])) {
    header('location: ../error404.php');
    exit;
}
require_once('../../config/ctconex.php');
// --- LÓGICA DE PROCESAMIENTO DE VENTA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? null;
    $metodo_pago = $_POST['metodo_pago'] ?? null;
    $carrito_json = $_POST['carrito_json'] ?? null;
    $total_venta = $_POST['total_venta'] ?? 0;
    $vendedor_id = $_SESSION['id'] ?? null;
    if (empty($cliente_id) || empty($metodo_pago) || empty($carrito_json) || $total_venta <= 0) {
        $_SESSION['mensaje_error'] = 'Error: Faltan datos requeridos para procesar la venta. Asegúrate de seleccionar un cliente, un método de pago y agregar productos.';
        header('Location: nuevo.php');
        exit;
    }
    $carrito = json_decode($carrito_json, true);
    try {
        $connect->beginTransaction();
        // 1. Crear la orden de venta principal
        $sql_order = "INSERT INTO orders (user_cli, total_price, metodo_pago, placed_on, payment_status, despacho) VALUES (?, ?, ?, NOW(), 'Aceptado', 'Pendiente')";
        $stmt_order = $connect->prepare($sql_order);
        $stmt_order->execute([$cliente_id, $total_venta, $metodo_pago]);
        $order_id = $connect->lastInsertId();
        if (!$order_id) {
            throw new Exception("Error al crear la orden de venta.");
        }
        // 2. Procesar cada producto del carrito
        foreach ($carrito as $item) {
            $cantidad_vendida = $item['cantidad'] ?? 0;
            $precio_unitario = $item['precio'] ?? 0;
            
            // a. Encontrar los IDs de inventario disponibles
            $sql_inventario_ids = "SELECT id, serial, codigo_g FROM bodega_inventario 
                                   WHERE marca = ? AND modelo = ? AND procesador = ? AND ram = ? AND disco = ? AND grado = ? 
                                   AND disposicion = 'Para Venta' AND estado = 'activo'
                                   LIMIT ?";
            $stmt_inventario_ids = $connect->prepare($sql_inventario_ids);
            $stmt_inventario_ids->execute([$item['marca'], $item['modelo'], $item['procesador'], $item['ram'], $item['disco'], $item['grado'], $cantidad_vendida]);
            $inventario_disponibles = $stmt_inventario_ids->fetchAll(PDO::FETCH_ASSOC);
            if (count($inventario_disponibles) < $cantidad_vendida) {
                throw new Exception("No hay suficiente stock para " . $item['marca'] . " " . $item['modelo']);
            }
            // b. Insertar en `venta_detalles` y actualizar inventario
            foreach ($inventario_disponibles as $inventario_item) {
                $sql_detalle = "INSERT INTO venta_detalles (orden_id, inventario_id, serial, codigo_g, precio_unitario, fecha_venta, vendedor_id) VALUES (?, ?, ?, ?, ?, NOW(), ?)";
                $stmt_detalle = $connect->prepare($sql_detalle);
                $stmt_detalle->execute([$order_id, $inventario_item['id'], $inventario_item['serial'], $inventario_item['codigo_g'], $precio_unitario, $vendedor_id]);
                
                $sql_update_inventario = "UPDATE bodega_inventario SET disposicion = 'Por Alistamiento', estado = 'inactivo' WHERE id = ?";
                $stmt_update_inventario = $connect->prepare($sql_update_inventario);
                $stmt_update_inventario->execute([$inventario_item['id']]);
            }
        }
        
        $connect->commit();
        $_SESSION['mensaje_exito'] = "Venta procesada con éxito. Orden ID: $order_id";
        header('Location: mostrar.php');
        exit;
    } catch (Exception $e) {
        $connect->rollBack();
        $_SESSION['mensaje_error'] = "Error al procesar la venta: " . $e->getMessage();
        header('Location: nuevo.php');
        exit;
    }
}
// --- FIN DE LA LÓGICA DE PROCESAMIENTO DE VENTA ---
// --- CÓDIGO PARA MOSTRAR LA INTERFAZ ---
$userInfo = []; 
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    try {
        $sqlUser = "SELECT nombre, usuario, correo, foto, idsede FROM usuarios WHERE id = ?";
        $stmtUser = $connect->prepare($sqlUser);
        $stmtUser->execute([$userId]);
        $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $userInfo = [];
    }
}
$sql_inventario = "
    SELECT 
        marca, modelo, procesador, ram, disco, grado, precio, 
        COUNT(id) as stock_disponible
    FROM bodega_inventario 
    WHERE disposicion = 'Para Venta' 
      AND precio IS NOT NULL AND precio > 0 AND estado = 'activo'
    GROUP BY marca, modelo, procesador, ram, disco, grado, precio
    ORDER BY marca, modelo";
$stmt_inventario = $connect->prepare($sql_inventario);
$stmt_inventario->execute();
$productos_inventario = $stmt_inventario->fetchAll(PDO::FETCH_ASSOC);
$stmt_clientes = $connect->prepare("SELECT idclie, nomcli, apecli FROM clientes WHERE estad='Activo' ORDER BY nomcli ASC");
$stmt_clientes->execute();
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nueva Venta - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <style>
        .product-card { border: 1px solid #e9ecef; border-radius: .25rem; }
        .cart-summary { background-color: #f8f9fa; border-radius: .25rem; }
        .selected-items { max-height: 350px; overflow-y: auto; }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
        </div>
        <?php if(function_exists('renderMenu')) { renderMenu($menu); } ?>
    </nav>
    
    <div id="content">
        <div class="top-navbar">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                        <span class="material-icons">arrow_back_ios</span>
                    </button>
                    <a class="navbar-brand" href="#"> Ventas </a>
                    <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                        <span class="material-icons">more_vert</span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                            <li class="dropdown nav-item active">
                                <a href="#" class="nav-link" data-toggle="dropdown">
                                    <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
                                        alt="Foto de perfil" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                </a>
                                <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                    <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
                                    <li><small><?php echo htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></small></li>
                                    <li><small class="text-muted"><?php echo htmlspecialchars(trim($userInfo['idsede'] ?? '') ?: 'Sede sin definir'); ?></small></li>
                                    <li class="mt-2">
                                        <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a>
                                        <a href="../cuenta/salir.php" class="btn btn-sm btn-danger btn-block mt-1">Salir</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    <div id="content">
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Carrito y Datos de la Venta</h4></div>
                            <div class="card-body">
                                <div class="cart-summary p-3 mb-4">
                                    <h5>Productos en el Carrito</h5>
                                    <div class="selected-items" id="selectedItems">
                                        <p class="text-center text-muted">Aún no has agregado productos.</p>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Total:</strong>
                                        <h4 class="mb-0 text-success">$<span id="totalPrice">0</span></h4>
                                    </div>
                                </div>
                                <form id="ventaForm" action="nuevo.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Cliente <span class="text-danger">*</span></label>
                                                <select class="form-control" name="cliente_id" required>
                                                    <option value="">-- Seleccione un cliente --</option>
                                                    <?php foreach ($clientes as $cliente): ?>
                                                        <option value="<?= $cliente['idclie'] ?>"><?= htmlspecialchars($cliente['nomcli'] . ' ' . $cliente['apecli']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Método de Pago <span class="text-danger">*</span></label>
                                                <select class="form-control" name="metodo_pago" required>
                                                    <option value="">-- Seleccione --</option>
                                                    <option value="Efectivo">Efectivo</option>
                                                    <option value="Transferencia">Transferencia</option>
                                                    <option value="Tarjeta">Tarjeta</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="productosSeleccionados" name="carrito_json">
                                    <input type="hidden" id="totalVenta" name="total_venta">
                                    <button type="submit" id="procesarVentaBtn" class="btn btn-success" disabled>
                                        <i class="material-icons">check_circle</i> Procesar Venta
                                    </button>
                                    <a href="mostrar.php" class="btn btn-secondary">Cancelar</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Inventario Disponible</h4></div>
                            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                                <?php if(count($productos_inventario) > 0): ?>
                                    <?php foreach($productos_inventario as $producto): ?>
                                    <div class="product-card p-2 mb-2" data-producto-json='<?= json_encode($producto, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                        <div class="d-flex justify-content-between">
                                            <strong><?= htmlspecialchars($producto['marca'] . ' ' . $producto['modelo']) ?></strong>
                                            <span class="badge badge-info">Stock: <?= $producto['stock_disponible'] ?></span>
                                        </div>
                                        <small class="text-muted"><?= htmlspecialchars($producto['procesador'] . ' | ' . $producto['ram'] . ' | ' . $producto['disco']) ?></small>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="text-success font-weight-bold">$<?= number_format((float)$producto['precio'], 0, ',', '.') ?></span>
                                            <div class="input-group" style="width: 120px;">
                                                <input type="number" class="form-control form-control-sm cantidad-input" value="1" min="1" max="<?= $producto['stock_disponible'] ?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary btn-sm add-to-cart-btn" type="button"><i class="material-icons" style="font-size: 16px;">add</i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning">No hay productos "Para Venta" en el inventario.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let carrito = [];
    function generarCartId(producto) {
        return `prod_${producto.marca}_${producto.modelo}_${producto.procesador}_${producto.ram}_${producto.disco}_${producto.precio}`.replace(/\s+/g, '');
    }
    function actualizarCarrito() {
        const itemsContainer = $('#selectedItems');
        itemsContainer.empty();
        let total = 0;
        if (carrito.length === 0) {
            itemsContainer.html('<p class="text-center text-muted">Aún no has agregado productos.</p>');
            $('#procesarVentaBtn').prop('disabled', true);
            $('#totalPrice').text('0');
            return;
        }
        carrito.forEach(item => {
            const subtotal = parseFloat(item.precio) * item.cantidad;
            total += subtotal;
            const itemHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>${item.marca} ${item.modelo}</strong><br>
                        <small>${item.cantidad} x $${parseFloat(item.precio).toLocaleString()}</small>
                    </div>
                    <div>
                        <strong>$${subtotal.toLocaleString()}</strong>
                        <button class="btn btn-danger btn-sm ml-2 remove-from-cart" data-cart-id="${item.cartId}">&times;</button>
                    </div>
                </div>`;
            itemsContainer.append(itemHtml);
        });
        $('#totalPrice').text(total.toLocaleString());
        $('#productosSeleccionados').val(JSON.stringify(carrito));
        $('#totalVenta').val(total);
        $('#procesarVentaBtn').prop('disabled', false);
    }
    // Muestra los mensajes de error o éxito si existen
    const mensajeError = "<?php echo isset($_SESSION['mensaje_error']) ? $_SESSION['mensaje_error'] : ''; unset($_SESSION['mensaje_error']); ?>";
    const mensajeExito = "<?php echo isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : ''; unset($_SESSION['mensaje_exito']); ?>";
    
    if (mensajeError) {
        Swal.fire('Error', mensajeError, 'error');
    }
    if (mensajeExito) {
        Swal.fire('Éxito', mensajeExito, 'success');
    }
    $('.add-to-cart-btn').on('click', function() {
        const card = $(this).closest('.product-card');
        let producto = card.data('producto-json');
        const cantidad = parseInt(card.find('.cantidad-input').val());
        producto.cartId = generarCartId(producto);
        const itemExistente = carrito.find(p => p.cartId === producto.cartId);
        
        if (itemExistente) {
            const nuevaCantidad = itemExistente.cantidad + cantidad;
            if (nuevaCantidad > producto.stock_disponible) {
                Swal.fire('Error', 'No hay suficiente stock para agregar esa cantidad.', 'error');
                return;
            }
            itemExistente.cantidad = nuevaCantidad;
        } else {
            if (cantidad > producto.stock_disponible) {
                Swal.fire('Error', 'No hay suficiente stock.', 'error');
                return;
            }
            producto.cantidad = cantidad;
            carrito.push(producto);
        }
        
        Swal.fire({
            toast: true, position: 'top-end',
            icon: 'success', title: 'Producto agregado',
            showConfirmButton: false, timer: 1500
        });
        
        actualizarCarrito();
    });
    $('#selectedItems').on('click', '.remove-from-cart', function() {
        const cartIdToRemove = $(this).data('cart-id');
        carrito = carrito.filter(item => item.cartId !== cartIdToRemove);
        actualizarCarrito();
    });
});
</script>
</body>
</html>
<?php ob_end_flush(); ?>