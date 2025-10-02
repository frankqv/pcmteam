<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2,3,4,5,6, 7])) {
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
    <title>Historial de Despachos - PCMARKETTEAM</title>
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
        .serial-list {
            font-family: monospace;
            font-size: 0.9rem;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .status-enviado {
            background-color: #d4edda;
            color: #155724;
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
                        <a class="navbar-brand" href="#"> <B>HISTORIAL DE DESPACHOS</B> </a>
                        <a class="navbar-brand" href="#"> Registro de Envíos </a>
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
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Filtros de Búsqueda</h4>
                            </div>
                            <div class="card-body">
                                <form id="filterForm" class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Fecha Desde</label>
                                            <input type="date" class="form-control" id="fechaDesde">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Fecha Hasta</label>
                                            <input type="date" class="form-control" id="fechaHasta">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Cliente</label>
                                            <input type="text" class="form-control" id="clienteFiltro" placeholder="Buscar cliente...">
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
                        </div>
                    </div>
                </div>

                <!-- Tabla de Historial -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Historial de Despachos</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="historialTable" class="table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Orden</th>
                                                <th>Cliente</th>
                                                <th>Productos</th>
                                                <th>Total</th>
                                                <th>Fecha Venta</th>
                                                <th>Fecha Despacho</th>
                                                <th>Responsable</th>
                                                <th>Seriales</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Consulta para obtener historial de despachos
                                            $sql_historial = "SELECT o.*, c.nomcli, c.apecli, c.celu, c.dircli,
                                                                    d.fecha_despacho, d.responsable as despacho_responsable
                                                            FROM orders o
                                                            INNER JOIN clientes c ON o.user_cli = c.idclie
                                                            LEFT JOIN despachos d ON o.idord = d.orden_id
                                                            WHERE o.despacho = 'Enviado' 
                                                            AND o.payment_status = 'Aceptado'
                                                            ORDER BY d.fecha_despacho DESC";
                                            
                                            $result_historial = $conn->query($sql_historial);
                                            
                                            if ($result_historial && $result_historial->num_rows > 0) {
                                                while ($orden = $result_historial->fetch_assoc()) {
                                                    // Obtener seriales de la orden
                                                    $sql_seriales = "SELECT serial FROM venta_detalles WHERE orden_id = ?";
                                                    $stmt_seriales = $conn->prepare($sql_seriales);
                                                    $stmt_seriales->bind_param("i", $orden['idord']);
                                                    $stmt_seriales->execute();
                                                    $seriales_result = $stmt_seriales->get_result();
                                                    
                                                    $seriales = [];
                                                    while ($serial = $seriales_result->fetch_assoc()) {
                                                        $seriales[] = $serial['serial'];
                                                    }
                                                    
                                                    echo "<tr>";
                                                    echo "<td><strong>#" . $orden['idord'] . "</strong></td>";
                                                    echo "<td>" . htmlspecialchars($orden['nomcli'] . ' ' . $orden['apecli']) . "<br>";
                                                    echo "<small class='text-muted'>" . htmlspecialchars($orden['celu']) . "</small></td>";
                                                    echo "<td>" . htmlspecialchars($orden['total_products']) . "</td>";
                                                    echo "<td>$" . number_format($orden['total_price'], 0, ',', '.') . "</td>";
                                                    echo "<td>" . htmlspecialchars($orden['placed_on']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($orden['fecha_despacho'] ?? 'N/A') . "</td>";
                                                    echo "<td>" . htmlspecialchars($orden['despacho_responsable'] ?? 'N/A') . "</td>";
                                                    echo "<td>";
                                                    foreach ($seriales as $serial) {
                                                        echo "<span class='badge badge-secondary mr-1 mb-1'>" . htmlspecialchars($serial) . "</span>";
                                                    }
                                                    echo "</td>";
                                                    echo "<td><span class='badge badge-success status-enviado'>Enviado</span></td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='9' class='text-center'>No hay despachos en el historial</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <!-- Data Tables -->
    <script type="text/javascript" src="../assets/js/datatable.js"></script>
    <script type="text/javascript" src="../assets/js/datatablebuttons.js"></script>
    <script type="text/javascript" src="../assets/js/jszip.js"></script>
    <script type="text/javascript" src="../assets/js/pdfmake.js"></script>
    <script type="text/javascript" src="../assets/js/vfs_fonts.js"></script>
    <script type="text/javascript" src="../assets/js/buttonshtml5.js"></script>
    <script type="text/javascript" src="../assets/js/buttonsprint.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#historialTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                },
                pageLength: 25,
                responsive: true,
                order: [[5, 'desc']] // Ordenar por fecha de despacho
            });
            
            // Funcionalidad de filtros
            $('#applyFilters').click(function() {
                var fechaDesde = $('#fechaDesde').val();
                var fechaHasta = $('#fechaHasta').val();
                var cliente = $('#clienteFiltro').val().toLowerCase();
                
                // Filtrar por fechas
                if (fechaDesde) {
                    table.column(5).search(fechaDesde);
                }
                if (fechaHasta) {
                    // Agregar filtro adicional para fecha hasta
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var fechaDespacho = data[5];
                        if (fechaHasta && fechaDespacho > fechaHasta) {
                            return false;
                        }
                        return true;
                    });
                }
                
                // Filtrar por cliente
                if (cliente) {
                    table.column(1).search(cliente);
                }
                
                table.draw();
            });
            
            // Limpiar filtros
            $('#filterForm').append('<div class="col-md-12 mt-2"><button type="button" class="btn btn-secondary" id="clearFilters">Limpiar Filtros</button></div>');
            $('#clearFilters').click(function() {
                $('#fechaDesde, #fechaHasta, #clienteFiltro').val('');
                $.fn.dataTable.ext.search.pop(); // Remover filtros personalizados
                table.search('').columns().search('').draw();
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
