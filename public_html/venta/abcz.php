<?php
ob_start();
session_start();

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4])){
    header('location: ../error404.php');
}

// Verificar si viene desde el catálogo
$from_catalog = isset($_GET['marca']) && isset($_GET['modelo']);
$producto_data = null;

if ($from_catalog) {
    $producto_data = [
        'marca' => $_GET['marca'] ?? '',
        'modelo' => $_GET['modelo'] ?? '',
        'procesador' => $_GET['procesador'] ?? '',
        'ram' => $_GET['ram'] ?? '',
        'disco' => $_GET['disco'] ?? '',
        'grado' => $_GET['grado'] ?? '',
        'precio' => floatval($_GET['precio'] ?? 0),
        'cantidad' => intval($_GET['cantidad'] ?? 1)
    ];
}
?>
<?php if(isset($_SESSION['id'])) { ?>

<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <!-- Data Tables -->
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <!-- layouts nav.php  |  Sidebar -->
        <?php    include_once '../layouts/nav.php';  include_once '../layouts/menu_data.php';    ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <!-- Page Content  -->
        <div id="content">
            <div class='pre-loader'>
                <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
            </div>
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> Ventas </a>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
                            data-toggle="collapse" data-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="material-icons">more_vert</span>
                        </button>
                        <div class="collapse navbar-collapse d-lg-block d-xl-block d-sm-none d-md-none d-none"
                            id="navbarSupportedContent">
                            <ul class="nav navbar-nav ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="../cuenta/configuracion.php">
                                        <span class="material-icons">settings</span>
                                    </a>
                                </li>
                                <li class="dropdown nav-item active">
                                    <a href="#" class="nav-link" data-toggle="dropdown">
                                        <img src="../assets/img/reere.webp">
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="../cuenta/perfil.php">Mi perfil</a>
                                        </li>
                                        <li>
                                            <a href="../cuenta/salir.php">Salir</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="main-content">
                <?php if ($from_catalog && $producto_data): ?>
                <!-- Producto seleccionado desde catálogo -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="material-icons">shopping_cart</i> Producto Seleccionado</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h4><?php echo htmlspecialchars($producto_data['marca'] . ' ' . $producto_data['modelo']); ?></h4>
                                        <div class="row">
                                            <?php if ($producto_data['procesador']): ?>
                                            <div class="col-md-6">
                                                <strong>Procesador:</strong> <?php echo htmlspecialchars($producto_data['procesador']); ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($producto_data['ram']): ?>
                                            <div class="col-md-6">
                                                <strong>RAM:</strong> <?php echo htmlspecialchars($producto_data['ram']); ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($producto_data['disco']): ?>
                                            <div class="col-md-6">
                                                <strong>Disco:</strong> <?php echo htmlspecialchars($producto_data['disco']); ?>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-md-6">
                                                <strong>Grado:</strong> <?php echo htmlspecialchars($producto_data['grado']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <h3 class="text-success">$<?php echo number_format($producto_data['precio'], 0, ',', '.'); ?></h3>
                                        <p class="mb-0">Cantidad: <strong><?php echo $producto_data['cantidad']; ?></strong></p>
                                        <p class="mb-0">Total: <strong>$<?php echo number_format($producto_data['precio'] * $producto_data['cantidad'], 0, ',', '.'); ?></strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!$from_catalog): ?>
                <!-- Solo mostrar esta sección si NO viene desde el catálogo -->
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <h4><i class="material-icons">info</i> Sistema de Ventas Actualizado</h4>
                            <p>Para realizar ventas, por favor utilice el <strong>Catálogo de Productos</strong> que muestra los equipos disponibles del inventario.</p>
                            <a href="catalogo.php" class="btn btn-success btn-lg">
                                <i class="material-icons">shopping_cart</i> Ir al Catálogo de Productos
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Procesar Venta</h4>
                                <p class="category">Complete los datos para procesar la venta</p>
                            </div>
                            <div class="card-content table-responsive">
                                <div class="alert alert-warning">
                                    <strong>Estimado usuario!</strong> Los campos remarcados con <span
                                        class="text-danger">*</span> son necesarios.
                                    <br>
                                    <strong>Al registrar una venta en el apartado clientes debes añadir uno nuevo si es
                                        primera vez</strong>
                                </div>
                                <form id="ventaForm" enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Clientes<span class="text-danger">*</span></label>
                                                <input type="hidden" name="pdrus" value="<?php echo $_SESSION['id']; ?>">
                                                <?php if ($from_catalog): ?>
                                                <input type="hidden" id="productoData" value='<?php echo json_encode($producto_data); ?>'>
                                                <?php endif; ?>
                                                <select class="form-control" required name="cxtip" id="clienteSelect">
                                                    <option value="">----------Seleccione------------</option>
                                                    <?php
                                                    require_once '../../config/ctconex.php';
                                                    $stmt = $connect->prepare("SELECT * FROM clientes where estad='Activo' order by idclie desc");
                                                    $stmt->execute();
                                                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        extract($row);
                                                        ?>
                                                    <option value="<?php echo $idclie; ?>"><?php echo $nomcli; ?>
                                                        <?php echo $apecli; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Comprobante<span class="text-danger">*</span></label>
                                                <select class="form-control" required name="cxcom">
                                                    <option value="">----------Seleccione------------</option>
                                                    <option value="Factura">Factura</option>
                                                    <option value="Boleta">Boleta</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Tipo de pago<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" required name="cxtcre">
                                                    <option value="">----------Seleccione------------</option>
                                                    <option value="Transferencia">Transferencia PSE</option>
                                                    <option value="Efectivo">Efectivo</option>
                                                    <option value="Tarjeta">Tarjeta</option>
                                                    <option value="Addi">Addi</option>
                                                    <option value="Wompi">Wompi</option>
                                                    <option value="SisteCredito">SisteCredito</option>
                                                    <option value="PSE">PSE</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="email">Fecha<span class="text-danger">*</span></label>
                                                <input type="text" id="fechaActual" class="form-control" name="txtdate"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <?php if ($from_catalog): ?>
                                            <div class="form-group">
                                                <label for="email">Producto Seleccionado<span class="text-danger">*</span></label>
                                                <input readonly class="form-control" type="text"
                                                    value="<?php echo htmlspecialchars($producto_data['marca'] . ' ' . $producto_data['modelo']); ?> (Cantidad: <?php echo $producto_data['cantidad']; ?>)"
                                                    name="">
                                            </div>
                                            <?php else: ?>
                                            <div class="alert alert-info">
                                                <p>No hay productos seleccionados. Por favor, seleccione un producto desde el catálogo.</p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <?php if ($from_catalog): ?>
                                            <h1 style="font-size:42px; color:#000000;"><strong>Precio Total: $<?php echo number_format($producto_data['precio'] * $producto_data['cantidad'], 0, ',', '.'); ?></strong></h1>
                                            <?php else: ?>
                                            <h1 style="font-size:42px; color:#000000;"><strong>Precio Total: $0</strong></h1>
                                            <?php endif; ?>
                                        </div>
                                    </div> 
                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <?php if ($from_catalog): ?>
                                            <button type="button" id="procesarVentaCatalogo" class="btn btn-success text-white">
                                                Procesar Venta
                                            </button>
                                            <?php else: ?>
                                            <button type="button" class="btn btn-secondary text-white" disabled>
                                                Seleccione un producto del catálogo
                                            </button>
                                            <?php endif; ?>
                                            <a class="btn btn-danger text-white" href="mostrar.php">Cancelar</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/sweetalert.js"></script>
    
    <script type="text/javascript">
    $(document).ready(function() {
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });
        $('.more-button,.body-overlay').on('click', function() {
            $('#sidebar,.body-overlay').toggleClass('show-nav');
        });
    });
    </script>
    <script src="../assets/js/loader.js"></script>
    
    <script type="text/javascript">
    window.onload = function() {
        var fecha = new Date(); //Fecha actual
        var mes = fecha.getMonth() + 1; //obteniendo mes
        var dia = fecha.getDate(); //obteniendo dia
        var ano = fecha.getFullYear(); //obteniendo año
        if (dia < 10)
            dia = '0' + dia; //agrega cero si el menor de 10
        if (mes < 10)
            mes = '0' + mes //agrega cero si el menor de 10
        document.getElementById('fechaActual').value = ano + "-" + mes + "-" + dia;
    }

    // Manejar ventas desde catálogo
    $(document).ready(function() {
        $('#procesarVentaCatalogo').click(function() {
            var clienteId = $('#clienteSelect').val();
            var metodoPago = $('select[name="cxtcre"]').val();
            var tipoComprobante = $('select[name="cxcom"]').val();
            var productoData = JSON.parse($('#productoData').val());
            
            if (!clienteId || !metodoPago || !tipoComprobante) {
                alert('Por favor complete todos los campos obligatorios');
                return;
            }
            
            if (!confirm('¿Está seguro de procesar esta venta?')) {
                return;
            }
            
            var button = $(this);
            button.prop('disabled', true).text('Procesando...');
            
            $.ajax({
                url: '../../backend/php/procesar_venta_inventario.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    producto_data: productoData,
                    cantidad: productoData.cantidad,
                    cliente_id: clienteId,
                    metodo_pago: metodoPago,
                    tipo_comprobante: tipoComprobante
                }),
                success: function(response) {
                    try {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            alert('Venta procesada exitosamente!\nOrden #' + result.orden_id + '\nTotal: $' + parseFloat(result.total_venta).toLocaleString());
                            window.location.href = 'mostrar.php';
                        } else {
                            alert('Error: ' + result.message);
                            button.prop('disabled', false).text('Procesar Venta');
                        }
                    } catch (e) {
                        alert('Error al procesar la respuesta del servidor');
                        button.prop('disabled', false).text('Procesar Venta');
                    }
                },
                error: function() {
                    alert('Error de conexión. Intente nuevamente.');
                    button.prop('disabled', false).text('Procesar Venta');
                }
            });
        });
    });
    </script>
</body>
</html>

<?php }else{ 
    header('Location: ../error404.php');
 } ?>
<?php ob_end_flush(); ?>










































































































<?php
ob_start();
session_start();
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4])){
    header('location: ../error404.php');
    exit;
}
require_once('../../config/ctconex.php');

// Lógica para filtros (ejemplo básico)
$where_clauses = ["disposicion = 'Para Venta'"];
if (!empty($_GET['marca'])) {
    $where_clauses[] = "marca = '" . $connect->quote($_GET['marca']) . "'";
}
// Añadir más filtros si es necesario para producto, ubicación, etc.

$where_sql = implode(' AND ', $where_clauses);

// Consulta principal para agrupar el inventario
$sql = "SELECT marca, modelo, procesador, ram, disco, grado, precio, COUNT(id) as stock_disponible
        FROM bodega_inventario
        WHERE $where_sql
        GROUP BY marca, modelo, procesador, ram, disco, grado, precio
        ORDER BY marca, modelo";
$stmt = $connect->prepare($sql);
$stmt->execute();
$catalogo = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar ítems en el carrito
$items_en_carrito = 0;
if (!empty($_SESSION['carrito_venta'])) {
    foreach ($_SESSION['carrito_venta'] as $item) {
        $items_en_carrito += $item['cantidad'];
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Catálogo de Ventas</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="wrapper">
    <?php include_once '../layouts/nav.php'; /* ... y el resto de tu layout ... */ ?>

    <div id="content">
        <div class="main-content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Catálogo de Productos</h4>
                                <p class="category">Productos disponibles para la venta</p>
                            </div>
                            <a href="nuevo.php" class="btn btn-success">
                                <i class="material-icons">shopping_cart</i> Finalizar Venta (<?php echo $items_en_carrito; ?>)
                            </a>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead class="text-primary">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Especificaciones</th>
                                        <th>Grado</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th style="width: 15%;">Cantidad</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($catalogo as $producto): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($producto['marca'] . ' ' . $producto['modelo']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($producto['procesador'] . ', ' . $producto['ram'] . ', ' . $producto['disco']); ?></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($producto['grado']); ?></span></td>
                                        <td>$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></td>
                                        <td><span class="badge badge-success"><?php echo $producto['stock_disponible']; ?></span></td>
                                        <td>
                                            <input type="number" class="form-control" value="1" min="1" max="<?php echo $producto['stock_disponible']; ?>" id="cantidad-<?php echo md5(implode('', $producto)); ?>">
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm add-to-cart-btn" 
                                                    data-producto='<?php echo json_encode($producto); ?>' 
                                                    data-id-input="cantidad-<?php echo md5(implode('', $producto)); ?>">
                                                <i class="material-icons">add_shopping_cart</i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    $('.add-to-cart-btn').on('click', function() {
        var productoData = $(this).data('producto');
        var inputId = $(this).data('id-input');
        var cantidad = $('#' + inputId).val();

        $.ajax({
            url: '../../backend/php/gestionar_carrito.php', // Debes crear este archivo
            type: 'POST',
            data: {
                accion: 'agregar',
                producto: productoData,
                cantidad: cantidad
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: '¡Agregado!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Recarga para actualizar el contador del carrito
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });
});
</script>
</body>
</html>







<?php
ob_start();
session_start();
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4])){
    header('location: ../error404.php');
    exit;
}
require_once('../../config/ctconex.php');

$carrito = $_SESSION['carrito_venta'] ?? [];
$grand_total = 0;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Finalizar Venta</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="wrapper">
    <?php include_once '../layouts/nav.php'; /* ... y el resto de tu layout ... */ ?>

    <div id="content">
        <div class="main-content">
            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Resumen de la Venta</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Precio Unit.</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($carrito)): ?>
                                        <?php foreach ($carrito as $key => $item): 
                                            $subtotal = $item['precio'] * $item['cantidad'];
                                            $grand_total += $subtotal;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['marca'] . ' ' . $item['modelo']); ?></td>
                                            <td>$<?php echo number_format($item['precio'], 0, ',', '.'); ?></td>
                                            <td><?php echo $item['cantidad']; ?></td>
                                            <td>$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                            <td>
                                                <a href="../../backend/php/gestionar_carrito.php?accion=eliminar&key=<?php echo $key; ?>" class="btn btn-danger btn-sm">
                                                    <i class="material-icons">delete</i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tu carrito está vacío.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <hr>
                            <h3 class="text-right">Total: $<?php echo number_format($grand_total, 0, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Datos del Cliente y Pago</h4>
                        </div>
                        <div class="card-body">
                            <form id="form-procesar-venta" method="POST" action="../../backend/php/procesar_venta_final.php">
                                <div class="form-group">
                                    <label>Cliente <span class="text-danger">*</span></label>
                                    <select name="cliente_id" class="form-control" required>
                                        <option value="">-- Seleccione --</option>
                                        <?php
                                        $stmt_clientes = $connect->prepare("SELECT idclie, nomcli, apecli FROM clientes WHERE estad='Activo'");
                                        $stmt_clientes->execute();
                                        while($cliente = $stmt_clientes->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='{$cliente['idclie']}'>{$cliente['nomcli']} {$cliente['apecli']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de pago <span class="text-danger">*</span></label>
                                    <select name="metodo_pago" class="form-control" required>
                                         <option value="">-- Seleccione --</option>
                                         <option value="Transferencia">Transferencia PSE</option>
                                         <option value="Efectivo">Efectivo</option>
                                         </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-block" <?php echo empty($carrito) ? 'disabled' : ''; ?>>
                                    Procesar Venta
                                </button>
                                <a href="mostrar.php" class="btn btn-secondary btn-block mt-2">Seguir Comprando</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>












<?php
session_start();

if (!isset($_SESSION['carrito_venta'])) {
    $_SESSION['carrito_venta'] = [];
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($accion === 'agregar') {
    $producto = $_POST['producto'];
    $cantidad = (int)$_POST['cantidad'];

    // Crear una clave única para el producto basada en sus características
    $product_key = md5($producto['marca'] . $producto['modelo'] . $producto['procesador'] . $producto['ram'] . $producto['disco'] . $producto['grado'] . $producto['precio']);

    if ($cantidad > 0 && $cantidad <= $producto['stock_disponible']) {
        if (isset($_SESSION['carrito_venta'][$product_key])) {
            // Si ya existe, actualiza la cantidad
            $_SESSION['carrito_venta'][$product_key]['cantidad'] += $cantidad;
        } else {
            // Si es nuevo, lo agrega
            $_SESSION['carrito_venta'][$product_key] = [
                'marca' => $producto['marca'],
                'modelo' => $producto['modelo'],
                'procesador' => $producto['procesador'],
                'ram' => $producto['ram'],
                'disco' => $producto['disco'],
                'grado' => $producto['grado'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad
            ];
        }
        echo json_encode(['status' => 'success', 'message' => 'Producto agregado al carrito.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cantidad no válida o excede el stock.']);
    }
}

if ($accion === 'eliminar') {
    $key = $_GET['key'];
    if (isset($_SESSION['carrito_venta'][$key])) {
        unset($_SESSION['carrito_venta'][$key]);
    }
    header('Location: ../../public_html/venta/nuevo.php'); // Redirige de vuelta al carrito
    exit;
}
?>








<?php
session_start();
require_once('../../config/ctconex.php');
// 1. Validar que el carrito no esté vacío y que los datos del POST existan
$carrito = $_SESSION['carrito_venta'] ?? [];
$cliente_id = $_POST['cliente_id'] ?? null;
// ... otras variables del form
if (empty($carrito) || !$cliente_id) {
    // Redirigir con error
    header('Location: ../../public_html/venta/nuevo.php?error=faltan_datos');
    exit;
}
$connect->beginTransaction();
try {
    // 2. Verificar stock de TODOS los productos ANTES de procesar
    foreach ($carrito as $item) {
        $sql_stock = "SELECT COUNT(id) as stock FROM bodega_inventario WHERE marca=? AND modelo=? AND procesador=? AND ram=? AND disco=? AND grado=? AND precio=? AND disposicion='Para Venta'";
        $stmt_stock = $connect->prepare($sql_stock);
        $stmt_stock->execute([$item['marca'], $item['modelo'], $item['procesador'], $item['ram'], $item['disco'], $item['grado'], $item['precio']]);
        $stock_actual = $stmt_stock->fetchColumn();
        
        if ($item['cantidad'] > $stock_actual) {
            throw new Exception("Stock insuficiente para el producto: " . $item['modelo']);
        }
    }
    // 3. Crear la orden de venta en `bodega_ordenes`
    $total_venta = 0;
    foreach($carrito as $item) { $total_venta += $item['precio'] * $item['cantidad']; }
    $sql_orden = "INSERT INTO bodega_ordenes (cliente_id, total_pago, responsable, estado_pago, creado_por) VALUES (?, ?, ?, 'Pendiente', ?)";
    $stmt_orden = $connect->prepare($sql_orden);
    $stmt_orden->execute([$cliente_id, $total_venta, $_SESSION['id'], $_SESSION['id']]);
    $pedido_id = $connect->lastInsertId();
    // 4. Asignar equipos específicos y actualizar `bodega_inventario`
    foreach ($carrito as $item) {
        $sql_select_unidades = "SELECT id FROM bodega_inventario WHERE marca=? AND modelo=? AND procesador=? AND ram=? AND disco=? AND grado=? AND precio=? AND disposicion='Para Venta' LIMIT ?";
        $stmt_select = $connect->prepare($sql_select_unidades);
        // Bind como enteros para LIMIT
        $stmt_select->bindValue(1, $item['marca']);
        // ... bindear los 6 parámetros restantes
        $stmt_select->bindValue(7, $item['precio']);
        $stmt_select->bindValue(8, (int) $item['cantidad'], PDO::PARAM_INT);
        $stmt_select->execute();
        $unidades_a_vender = $stmt_select->fetchAll(PDO::FETCH_COLUMN);
        // Actualizar cada unidad
        $sql_update_inventario = "UPDATE bodega_inventario SET disposicion='Vendido', pedido_id=? WHERE id=?";
        $stmt_update = $connect->prepare($sql_update_inventario);
        foreach ($unidades_a_vender as $unidad_id) {
            $stmt_update->execute([$pedido_id, $unidad_id]);
        }
    }
    
    // 5. Si todo salió bien, confirmar transacción y limpiar carrito
    $connect->commit();
    unset($_SESSION['carrito_venta']);
    
    // Redirigir a una página de éxito
    header('Location: ../../public_html/despacho/pendientes.php?exito=venta_creada');
    exit;
} catch (Exception $e) {
    // Si algo falla, revertir todo
    $connect->rollBack();
    // Redirigir con mensaje de error
    header('Location: ../../public_html/venta/nuevo.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>