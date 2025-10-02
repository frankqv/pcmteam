<?php
// selecion de un producto del catalogo
ob_start();
session_start();
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7])){
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