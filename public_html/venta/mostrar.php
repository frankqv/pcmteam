<?php
// /public_html/venta/mostrar_autocontenido.php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7])) {
    header('location: ../error404.php');
    exit;
}
require_once('../../config/ctconex.php');
// MANEJO DE PETICIONES AJAX
if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    // OBTENER DATOS DE VENTAS PARA DATATABLE
    if ($_POST['ajax_action'] === 'get_sales_data') {
        try {
            $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
            $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
            $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
            $cliente = isset($_POST['cliente']) ? $_POST['cliente'] : '';
            $vendedor = isset($_POST['vendedor']) ? $_POST['vendedor'] : '';
            $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';
            $sql = "SELECT 
                        o.idord as id_orden,
                        CONCAT(c.nomcli, ' ', c.apecli) as cliente_nombre,
                        o.total_price as total_venta,
                        o.method as metodo_pago,
                        o.payment_status as estado_pago,
                        o.placed_on as fecha_creacion,
                        COALESCE(u.nombre, 'N/D') as vendedor_nombre
                    FROM orders o
                    LEFT JOIN clientes c ON o.user_cli = c.idclie
                    LEFT JOIN usuarios u ON o.user_id = u.id
                    WHERE 1=1";
            $params = [];
            if (!empty($fecha_inicio)) {
                $sql .= " AND DATE(o.placed_on) >= :fecha_inicio";
                $params[':fecha_inicio'] = $fecha_inicio;
            }
            if (!empty($fecha_fin)) {
                $sql .= " AND DATE(o.placed_on) <= :fecha_fin";
                $params[':fecha_fin'] = $fecha_fin;
            }
            if (!empty($cliente)) {
                $sql .= " AND o.user_cli = :cliente";
                $params[':cliente'] = $cliente;
            }
            if (!empty($vendedor)) {
                $sql .= " AND o.user_id = :vendedor";
                $params[':vendedor'] = $vendedor;
            }
            if (!empty($metodo_pago)) {
                $sql .= " AND o.method = :metodo_pago";
                $params[':metodo_pago'] = $metodo_pago;
            }
            if (!empty($searchValue)) {
                $sql .= " AND (
                    CONCAT(c.nomcli, ' ', c.apecli) LIKE :search
                    OR o.method LIKE :search
                    OR o.payment_status LIKE :search
                    OR u.nombre LIKE :search
                )";
                $params[':search'] = "%$searchValue%";
            }
            $stmtCount = $connect->prepare($sql);
            foreach ($params as $key => $value) {
                $stmtCount->bindValue($key, $value);
            }
            $stmtCount->execute();
            $recordsFiltered = $stmtCount->rowCount();
            $orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
            $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';
            $columns = ['o.idord', 'cliente_nombre', 'o.total_price', 'o.method', 'o.payment_status', 'o.placed_on', 'vendedor_nombre'];
            $orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'o.idord';
            $sql .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";
            $stmt = $connect->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':start', $start, PDO::PARAM_INT);
            $stmt->bindValue(':length', $length, PDO::PARAM_INT);
            $stmt->execute();
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = [
                    'id_orden' => $row['id_orden'],
                    'cliente_nombre' => $row['cliente_nombre'],
                    'total_venta' => number_format($row['total_venta'], 2, '.', ''),
                    'metodo_pago' => $row['metodo_pago'],
                    'estado_pago' => $row['estado_pago'],
                    'fecha_creacion' => $row['fecha_creacion'],
                    'vendedor_nombre' => $row['vendedor_nombre'],
                    'acciones' => '<button class="btn btn-sm btn-info btn-view" data-id="' . $row['id_orden'] . '">
                        <i class="material-icons">visibility</i>
                    </button>'
                ];
            }
            $stmtTotal = $connect->prepare("SELECT COUNT(*) as total FROM orders");
            $stmtTotal->execute();
            $recordsTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
            echo json_encode([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
            exit;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
            exit;
        }
    }
    // OBTENER DETALLES DE UNA VENTA
    if ($_POST['ajax_action'] === 'get_venta_details') {
        if (!isset($_POST['id_orden'])) {
            echo json_encode(['error' => 'No se recibió ID de orden']);
            exit;
        }
        $orden_id = intval($_POST['id_orden']);
        try {
            $sql = "SELECT 
                        o.idord,
                        o.total_price,
                        o.method,
                        o.payment_status,
                        o.placed_on,
                        o.total_products,
                        CONCAT(c.nomcli, ' ', c.apecli) as cliente_nombre,
                        c.correo as cliente_correo,
                        c.celu as cliente_telefono,
                        u.nombre as vendedor_nombre
                    FROM orders o
                    LEFT JOIN clientes c ON o.user_cli = c.idclie
                    LEFT JOIN usuarios u ON o.user_id = u.id
                    WHERE o.idord = :orden_id";
            $stmt = $connect->prepare($sql);
            $stmt->bindParam(':orden_id', $orden_id, PDO::PARAM_INT);
            $stmt->execute();
            $orden = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$orden) {
                echo json_encode(['error' => 'No se encontró la orden']);
                exit;
            }
            $sqlItems = "SELECT * FROM venta_detalles WHERE orden_id = :orden_id";
            $stmtItems = $connect->prepare($sqlItems);
            $stmtItems->bindParam(':orden_id', $orden_id, PDO::PARAM_INT);
            $stmtItems->execute();
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            ob_start(); ?>
            <div class="row">
                <div class="col-md-6">
                    <h6>Información del Cliente</h6>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($orden['cliente_nombre']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($orden['cliente_correo']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($orden['cliente_telefono']); ?></p>
                </div>
                <div class="col-md-6">
                    <h6>Información de la Venta</h6>
                    <p><strong>Orden #:</strong> <?php echo $orden['idord']; ?></p>
                    <p><strong>Fecha:</strong> <?php echo $orden['placed_on']; ?></p>
                    <p><strong>Vendedor:</strong> <?php echo htmlspecialchars($orden['vendedor_nombre']); ?></p>
                    <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($orden['method']); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($orden['payment_status']); ?></p>
                </div>
            </div>
            <hr>
            <h6>Productos</h6>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Serial</th>
                        <th>Código</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['serial']); ?></td>
                            <td><?php echo htmlspecialchars($item['codigo_g']); ?></td>
                            <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">Total:</th>
                        <th>$<?php echo number_format($orden['total_price'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
<?php
            $html = ob_get_clean();
            echo json_encode(['html' => $html]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
            exit;
        }
    }
}
// CÓDIGO PARA LA VISTA NORMAL (NO AJAX)
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
try {
    $stmtToday = $connect->prepare("SELECT SUM(total_price) as total FROM orders WHERE DATE(placed_on) = CURDATE()");
    $stmtToday->execute();
    $totalHoy = $stmtToday->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $stmtMonth = $connect->prepare("SELECT SUM(total_price) as total FROM orders WHERE MONTH(placed_on) = MONTH(CURDATE()) AND YEAR(placed_on) = YEAR(CURDATE())");
    $stmtMonth->execute();
    $totalMes = $stmtMonth->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $stmtTotal = $connect->prepare("SELECT SUM(total_price) as total FROM orders");
    $stmtTotal->execute();
    $totalGeneral = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (PDOException $e) {
    $totalHoy = $totalMes = $totalGeneral = 0;
}
$stmt_clientes = $connect->prepare("SELECT idclie, nomcli, apecli FROM clientes WHERE estad='Activo' ORDER BY nomcli ASC");
$stmt_clientes->execute();
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
$stmt_vendedores = $connect->prepare("SELECT id, nombre FROM usuarios ORDER BY nombre ASC");
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
                                        <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>" alt="Foto" style="width:35px;height:35px;border-radius:50%;object-fit:cover;">
                                    </a>
                                    <ul class="dropdown-menu p-3 text-center" style="min-width:220px;">
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
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Filtros y Búsqueda</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <input type="date" id="fecha_inicio" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" id="fecha_fin" class="form-control">
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
                                                    <option value="<?php echo $vendedor['id']; ?>"><?php echo htmlspecialchars($vendedor['nombre']); ?></option>
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
    <div class="modal fade" id="detalleVentaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleVentaModalLabel">Detalles de la Venta</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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
                    "url": "<?php echo $_SERVER['PHP_SELF']; ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.ajax_action = 'get_sales_data';
                        d.fecha_inicio = $('#fecha_inicio').val();
                        d.fecha_fin = $('#fecha_fin').val();
                        d.cliente = $('#cliente_filtro').val();
                        d.vendedor = $('#vendedor_filtro').val();
                        d.metodo_pago = $('#metodo_pago_filtro').val();
                    }
                },
                "columns": [{
                        "data": "id_orden"
                    },
                    {
                        "data": "cliente_nombre"
                    },
                    {
                        "data": "total_venta",
                        "render": function(data) {
                            return '$' + parseFloat(data).toLocaleString('es-CO', {
                                minimumFractionDigits: 2
                            });
                        }
                    },
                    {
                        "data": "metodo_pago"
                    },
                    {
                        "data": "estado_pago"
                    },
                    {
                        "data": "fecha_creacion"
                    },
                    {
                        "data": "vendedor_nombre"
                    },
                    {
                        "data": "acciones",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "dom": 'Bfrtip',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        className: 'btn btn-danger'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        className: 'btn btn-info'
                    }
                ],
                "order": [
                    [0, "desc"]
                ]
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
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>",
                    type: 'POST',
                    data: {
                        ajax_action: 'get_venta_details',
                        id_orden: ordenId
                    },
                    success: function(response) {
                        if (response.html) {
                            $('#detalleVentaContenido').html(response.html);
                            $('#detalleVentaModal').modal('show');
                        } else if (response.error) {
                            Swal.fire('Error', response.error, 'error');
                        }
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