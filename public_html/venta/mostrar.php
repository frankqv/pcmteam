<?php
// /public_html/venta/mostrar.php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7])) {
    header('location: ../error404.php');
    exit;
}
require_once('../../config/ctconex.php');

// --- USER INFO FOR NAVBAR ---
$userInfo = [];
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    try {
        $sqlUser = "SELECT nombre, usuario, correo, foto, idsede FROM usuarios WHERE id = :id";
        $stmtUser = $connect->prepare($sqlUser);
        $stmtUser->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmtUser->execute();
        $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $userInfo = [];
    }
}

// --- SUMMARY CARD QUERIES ---
try {
    // Total Sales Today
    $stmtToday = $connect->prepare("SELECT SUM(total_pago) as total FROM bodega_ordenes WHERE DATE(created_at) = CURDATE()");
    $stmtToday->execute();
    $totalHoy = $stmtToday->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Total Sales This Month
    $stmtMonth = $connect->prepare("SELECT SUM(total_pago) as total FROM bodega_ordenes WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
    $stmtMonth->execute();
    $totalMes = $stmtMonth->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Overall Total Sales
    $stmtTotal = $connect->prepare("SELECT SUM(total_pago) as total FROM bodega_ordenes");
    $stmtTotal->execute();
    $totalGeneral = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (PDOException $e) {
    $totalHoy = $totalMes = $totalGeneral = 0;
}

// --- DATA FOR FILTERS ---
// Clientes
$stmt_clientes = $connect->prepare("SELECT idclie, nomcli, apecli FROM clientes WHERE estad='Activo' ORDER BY nomcli ASC");
$stmt_clientes->execute();
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

// Vendedores - CORREGIDO
$stmt_vendedores = $connect->prepare("SELECT id, nombre as vendedor_nombre FROM usuarios ORDER BY nombre ASC");
$stmt_vendedores->execute();
$vendedores = $stmt_vendedores->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial de Ventas - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include_once '../layouts/nav.php';
        include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php if (function_exists('renderMenu')) {
                renderMenu($menu);
            } ?>
        </nav>
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> Historial de Ventas </a>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                            <span class="material-icons">more_vert</span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="nav navbar-nav ml-auto">
                                <li class="dropdown nav-item active">
                                    <a href="#" class="nav-link" data-toggle="dropdown">
                                        <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>" alt="Foto de perfil" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
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
            <div class="main-content">
                <div class="container-fluid">
                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-bg bg-primary text-white p-3 rounded-circle">
                                            <i class="material-icons">today</i>
                                        </div>
                                        <div class="ml-3">
                                            <h5 class="card-title mb-1">Ventas del Día</h5>
                                            <p class="card-text mb-0">$<?php echo number_format($totalHoy, 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-bg bg-success text-white p-3 rounded-circle">
                                            <i class="material-icons">calendar_month</i>
                                        </div>
                                        <div class="ml-3">
                                            <h5 class="card-title mb-1">Ventas del Mes</h5>
                                            <p class="card-text mb-0">$<?php echo number_format($totalMes, 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-bg bg-info text-white p-3 rounded-circle">
                                            <i class="material-icons">receipt_long</i>
                                        </div>
                                        <div class="ml-3">
                                            <h5 class="card-title mb-1">Ventas Generales</h5>
                                            <p class="card-text mb-0">$<?php echo number_format($totalGeneral, 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Filters and Table -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Filtros y Búsqueda</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <input type="date" id="fecha_inicio" class="form-control" placeholder="Fecha Inicio">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" id="fecha_fin" class="form-control" placeholder="Fecha Fin">
                                        </div>
                                        <div class="col-md-3">
                                            <select id="cliente_filtro" class="form-control">
                                                <option value="">Todos los clientes</option>
                                                <?php foreach ($clientes as $cliente): ?>
                                                    <option value="<?php echo $cliente['idclie']; ?>"><?php echo htmlspecialchars($cliente['nomcli'] . ' ' . $cliente['apecli']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="vendedor_filtro" class="form-control">
                                                <option value="">Todos los vendedores</option>
                                                <?php foreach ($vendedores as $vendedor): ?>
                                                    <option value="<?php echo $vendedor['id']; ?>"><?php echo htmlspecialchars($vendedor['vendedor_nombre']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select id="metodo_pago_filtro" class="form-control">
                                                <option value="">Todos los métodos</option>
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="Transferencia">Transferencia</option>
                                                <option value="Tarjeta">Tarjeta</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button id="btn_filtrar" class="btn btn-primary">Filtrar</button>
                                            <button id="btn_limpiar" class="btn btn-secondary">Limpiar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Listado de Ventas</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="ventasTable" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th># Orden</th>
                                                    <th>Cliente</th>
                                                    <th>Total</th>
                                                    <th>Método Pago</th>
                                                    <th>Estado Pago</th>
                                                    <th>Fecha</th>
                                                    <th>Vendedor</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para ver detalles -->
    <div class="modal fade" id="detalleVentaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleVentaModalLabel">Detalles de la Venta</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="detalleVentaContenido"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar, #content').toggleClass('active');
            });
            
            var ventasTable = $('#ventasTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "../controllers/get_sales_data.php",
                    "type": "POST",
                    "data": function(d) {
                        d.fecha_inicio = $('#fecha_inicio').val();
                        d.fecha_fin = $('#fecha_fin').val();
                        d.cliente = $('#cliente_filtro').val();
                        d.vendedor = $('#vendedor_filtro').val();
                        d.metodo_pago = $('#metodo_pago_filtro').val();
                    }
                },
                "columns": [
                    {"data": "id_orden"},
                    {"data": "cliente_nombre"},
                    {
                        "data": "total_venta",
                        "render": function(data) {
                            return '$' + parseFloat(data).toLocaleString('es-CO', {minimumFractionDigits: 2});
                        }
                    },
                    {"data": "metodo_pago"},
                    {"data": "estado_pago"},
                    {"data": "fecha_creacion"},
                    {"data": "vendedor_nombre"},
                    {"data": "acciones", "orderable": false, "searchable": false}
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "dom": 'Bfrtip',
                "buttons": [
                    {extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success'},
                    {extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-danger'},
                    {extend: 'csvHtml5', text: 'CSV', className: 'btn btn-info'}
                ],
                "order": [[0, "desc"]]
            });
            
            $('#btn_filtrar').on('click', function() {
                ventasTable.ajax.reload();
            });
            
            $('#btn_limpiar').on('click', function() {
                $('#fecha_inicio, #fecha_fin, #cliente_filtro, #vendedor_filtro, #metodo_pago_filtro').val('');
                ventasTable.ajax.reload();
            });
            
            $('#ventasTable tbody').on('click', '.btn-view', function() {
                var ordenId = $(this).data('id');
                $('#detalleVentaModalLabel').text('Detalles de la Venta #' + ordenId);
                $.ajax({
                    url: '../controllers/get_venta_details.php',
                    type: 'POST',
                    data: {id_orden: ordenId},
                    success: function(response) {
                        $('#detalleVentaContenido').html(response);
                        $('#detalleVentaModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudieron cargar los detalles.', 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>