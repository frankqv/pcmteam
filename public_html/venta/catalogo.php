<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 4,5,6,7])) {
    header('location: ../error404.php');
    exit();
}
require_once '../../config/ctconex.php';
// Verificar que el usuario existe y obtener su información
$userInfo = null;
if (isset($_SESSION['id'])) {
    $sql = "SELECT nombre, usuario, correo, rol, foto, idsede FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
}
if (!$userInfo) {
    header('Location: ../error404.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Catálogo de Ventas - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <!-- Data Tables -->
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .product-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s ease;
        }
        .product-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .stock-badge {
            font-size: 0.9rem;
            padding: 0.4rem 0.8rem;
        }
        .price-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }
        .specs-list {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php';
        include_once '../layouts/menu_data.php'; ?>
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <!-- Page Content -->
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background: #27ae60;">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> <B>CATÁLOGO DE VENTAS</B> </a>
                        <a class="navbar-brand" href="#"> Productos Disponibles </a>
                    </div>
                    <!-- Menú derecho (usuario) -->
                    <ul class="nav navbar-nav ml-auto">
                        <li class="dropdown nav-item active">
                            <a href="#" class="nav-link" data-toggle="dropdown">
                                <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
                                    alt="Foto de perfil"
                                    style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                            </a>
                            <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
                                <li><?php echo htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></li>
                                <li><?php echo htmlspecialchars($userInfo['correo'] ?? 'correo@ejemplo.com'); ?></li>
                                <li class="mt-2">
                                    <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <div class="main-content">
                <!-- Filtros -->
                <div class="filter-section">
                    <h4>Filtros de Búsqueda</h4>
                    <form id="filterForm" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Ubicación</label>
                                <select class="form-control" id="filterUbicacion">
                                    <option value="">Todas</option>
                                    <option value="Principal">Principal</option>
                                    <option value="Bodega">Bodega</option>
                                    <option value="Laboratorio">Laboratorio</option>
                                    <option value="Exhibición">Exhibición</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Producto</label>
                                <select class="form-control" id="filterProducto">
                                    <option value="">Todos</option>
                                    <option value="Portatil">Portátil</option>
                                    <option value="Desktop">Desktop</option>
                                    <option value="Monitor">Monitor</option>
                                    <option value="AIO">Todo en Uno</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Marca</label>
                                <select class="form-control" id="filterMarca">
                                    <option value="">Todas</option>
                                    <?php
                                    $marcas = $conn->query("SELECT DISTINCT marca FROM bodega_inventario WHERE disposicion = 'Para Venta' AND marca IS NOT NULL ORDER BY marca");
                                    while ($marca = $marcas->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($marca['marca']) . "'>" . htmlspecialchars($marca['marca']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" id="applyFilters">
                                    Aplicar Filtros
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Catálogo de Productos -->
                <div class="row" id="productosContainer">
                    <?php
                    // Consulta SQL para obtener productos agrupados
                    $sql = "SELECT marca, modelo, procesador, ram, disco, grado, precio, 
                                   COUNT(id) as stock_disponible,
                                   GROUP_CONCAT(DISTINCT ubicacion) as ubicaciones,
                                   GROUP_CONCAT(DISTINCT serial) as seriales
                            FROM bodega_inventario 
                            WHERE disposicion = 'Para Venta' 
                            AND estado = 'activo'
                            GROUP BY marca, modelo, procesador, ram, disco, grado, precio
                            ORDER BY marca, modelo";
                    $result = $conn->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $precioFormateado = '$' . number_format((float)$row['precio'], 0, ',', '.');
                            $stockClass = $row['stock_disponible'] > 5 ? 'success' : ($row['stock_disponible'] > 2 ? 'warning' : 'danger');
                            
                            echo "<div class='col-md-6 col-lg-4 product-item' data-marca='" . htmlspecialchars($row['marca']) . "' data-producto='Portatil' data-ubicacion='Principal'>";
                            echo "<div class='product-card'>";
                            echo "<div class='d-flex justify-content-between align-items-start mb-3'>";
                            echo "<h5 class='mb-0'>" . htmlspecialchars($row['marca'] . ' ' . $row['modelo']) . "</h5>";
                            echo "<span class='badge badge-" . $stockClass . " stock-badge'>Stock: " . $row['stock_disponible'] . "</span>";
                            echo "</div>";
                            
                            echo "<div class='specs-list mb-3'>";
                            if (!empty($row['procesador'])) echo "<div><strong>Procesador:</strong> " . htmlspecialchars($row['procesador']) . "</div>";
                            if (!empty($row['ram'])) echo "<div><strong>RAM:</strong> " . htmlspecialchars($row['ram']) . "</div>";
                            if (!empty($row['disco'])) echo "<div><strong>Disco:</strong> " . htmlspecialchars($row['disco']) . "</div>";
                            echo "<div><strong>Grado:</strong> " . htmlspecialchars($row['grado']) . "</div>";
                            echo "</div>";
                            
                            echo "<div class='d-flex justify-content-between align-items-center'>";
                            echo "<div class='price-display'>" . $precioFormateado . "</div>";
                            echo "<button class='btn btn-success btn-sm vender-btn' 
                                        data-marca='" . htmlspecialchars($row['marca']) . "'
                                        data-modelo='" . htmlspecialchars($row['modelo']) . "'
                                        data-procesador='" . htmlspecialchars($row['procesador']) . "'
                                        data-ram='" . htmlspecialchars($row['ram']) . "'
                                        data-disco='" . htmlspecialchars($row['disco']) . "'
                                        data-grado='" . htmlspecialchars($row['grado']) . "'
                                        data-precio='" . $row['precio'] . "'
                                        data-stock='" . $row['stock_disponible'] . "'
                                        data-seriales='" . htmlspecialchars($row['seriales']) . "'>";
                            echo "<i class='material-icons'>shopping_cart</i> Vender";
                            echo "</button>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='col-12'>";
                        echo "<div class='alert alert-info text-center'>";
                        echo "<h4>No hay productos disponibles para la venta</h4>";
                        echo "<p>Los productos aparecerán aquí una vez que sean enviados desde el Business Room.</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para confirmar venta -->
    <div class="modal fade" id="ventaModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Venta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="productoInfo"></div>
                    <div class="form-group mt-3">
                        <label for="cantidadVenta">Cantidad a vender:</label>
                        <input type="number" class="form-control" id="cantidadVenta" min="1" max="1" value="1">
                        <small class="form-text text-muted">Stock disponible: <span id="stockDisponible">0</span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmarVenta">Confirmar Venta</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
    <script src="../assets/js/loader.js"></script>
    
    <script>
        $(document).ready(function() {
            // Funcionalidad de filtros
            $('#applyFilters').click(function() {
                var ubicacion = $('#filterUbicacion').val().toLowerCase();
                var producto = $('#filterProducto').val().toLowerCase();
                var marca = $('#filterMarca').val().toLowerCase();
                
                $('.product-item').each(function() {
                    var itemUbicacion = $(this).data('ubicacion').toLowerCase();
                    var itemProducto = $(this).data('producto').toLowerCase();
                    var itemMarca = $(this).data('marca').toLowerCase();
                    
                    var showItem = true;
                    
                    if (ubicacion && !itemUbicacion.includes(ubicacion)) showItem = false;
                    if (producto && !itemProducto.includes(producto)) showItem = false;
                    if (marca && !itemMarca.includes(marca)) showItem = false;
                    
                    $(this).toggle(showItem);
                });
            });
            
            // Limpiar filtros
            $('#filterForm').append('<div class="col-md-12 mt-2"><button type="button" class="btn btn-secondary" id="clearFilters">Limpiar Filtros</button></div>');
            $('#clearFilters').click(function() {
                $('#filterUbicacion, #filterProducto, #filterMarca').val('');
                $('.product-item').show();
            });
            
            // Manejar clic en botón vender
            $('.vender-btn').click(function() {
                var marca = $(this).data('marca');
                var modelo = $(this).data('modelo');
                var procesador = $(this).data('procesador');
                var ram = $(this).data('ram');
                var disco = $(this).data('disco');
                var grado = $(this).data('grado');
                var precio = $(this).data('precio');
                var stock = $(this).data('stock');
                var seriales = $(this).data('seriales');
                
                // Mostrar información del producto
                var productoInfo = '<h6>' + marca + ' ' + modelo + '</h6>';
                if (procesador) productoInfo += '<p><strong>Procesador:</strong> ' + procesador + '</p>';
                if (ram) productoInfo += '<p><strong>RAM:</strong> ' + ram + '</p>';
                if (disco) productoInfo += '<p><strong>Disco:</strong> ' + disco + '</p>';
                productoInfo += '<p><strong>Grado:</strong> ' + grado + '</p>';
                productoInfo += '<p><strong>Precio:</strong> $' + parseFloat(precio).toLocaleString() + '</p>';
                
                $('#productoInfo').html(productoInfo);
                $('#stockDisponible').text(stock);
                $('#cantidadVenta').attr('max', stock);
                $('#cantidadVenta').val(1);
                
                // Guardar datos para la venta
                $('#confirmarVenta').data('producto-data', {
                    marca: marca,
                    modelo: modelo,
                    procesador: procesador,
                    ram: ram,
                    disco: disco,
                    grado: grado,
                    precio: precio,
                    stock: stock,
                    seriales: seriales
                });
                
                $('#ventaModal').modal('show');
            });
            
            // Confirmar venta
            $('#confirmarVenta').click(function() {
                var cantidad = parseInt($('#cantidadVenta').val());
                var productoData = $(this).data('producto-data');
                
                if (cantidad > productoData.stock) {
                    alert('La cantidad solicitada excede el stock disponible');
                    return;
                }
                
                // Redirigir a nuevo.php con los parámetros
                var params = new URLSearchParams({
                    marca: productoData.marca,
                    modelo: productoData.modelo,
                    procesador: productoData.procesador,
                    ram: productoData.ram,
                    disco: productoData.disco,
                    grado: productoData.grado,
                    precio: productoData.precio,
                    cantidad: cantidad
                });
                
                window.location.href = 'nuevo.php?' + params.toString();
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
