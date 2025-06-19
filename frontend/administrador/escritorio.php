<?php
ob_start();
session_start();

// Verificación de autenticación mejorada
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2])) {
    header('location: ../error404.php');
    exit();
}

if (!isset($_SESSION['id'])) {
    header('Location: ../error404.php');
    exit();
}

require '../../backend/bd/ctconex.php';

// Funciones para obtener datos del dashboard
function getStatistics($connect) {
    $stats = [];
    
    try {
        // Total de clientes
        $stmt = $connect->prepare("SELECT COUNT(*) as total FROM clientes");
        $stmt->execute();
        $stats['clientes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de productos
        $stmt = $connect->prepare("SELECT COUNT(*) as total FROM producto");
        $stmt->execute();
        $stats['productos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Ventas de hoy
        $stmt = $connect->prepare("SELECT COALESCE(SUM(total_price), 0) as total FROM orders WHERE DATE(placed_on) = CURDATE()");
        $stmt->execute();
        $stats['ventas_hoy'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de usuarios
        $stmt = $connect->prepare("SELECT COUNT(*) as total FROM usuarios");
        $stmt->execute();
        $stats['usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Productos con stock bajo
        $stmt = $connect->prepare("SELECT COUNT(*) as total FROM producto WHERE stock <= 5");
        $stmt->execute();
        $stats['stock_bajo'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
    } catch (Exception $e) {
        error_log("Error en getStatistics: " . $e->getMessage());
        $stats = [
            'clientes' => 0,
            'productos' => 0,
            'ventas_hoy' => 0,
            'usuarios' => 0,
            'stock_bajo' => 0
        ];
    }
    
    return $stats;
}

function getRecentClients($connect, $limit = 10) {
    try {
        $stmt = $connect->prepare("SELECT idclie, nomcli, apecli, celu, correo, DATE(fere) as fecha_registro 
                                  FROM clientes 
                                  ORDER BY idclie DESC 
                                  LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error en getRecentClients: " . $e->getMessage());
        return [];
    }
}

function getRecentProducts($connect, $limit = 10) {
    try {
        $stmt = $connect->prepare("SELECT p.idprod, p.nomprd, c.nomca, p.stock, p.precio, DATE(p.fere) as fecha_registro
                                  FROM producto p 
                                  INNER JOIN categoria c ON p.idcate = c.idcate 
                                  ORDER BY p.idprod DESC 
                                  LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error en getRecentProducts: " . $e->getMessage());
        return [];
    }
}

function getBirthdayClients($connect) {
    try {
        $stmt = $connect->prepare("SELECT nomcli, apecli, celu, naci 
                                  FROM clientes 
                                  WHERE DAY(naci) = DAY(NOW()) AND MONTH(naci) = MONTH(NOW())");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error en getBirthdayClients: " . $e->getMessage());
        return [];
    }
}

function getProductsForChart($connect) {
    try {
        $stmt = $connect->prepare("SELECT p.nomprd, p.stock 
                                  FROM producto p 
                                  INNER JOIN categoria c ON p.idcate = c.idcate 
                                  WHERE p.stock > 0 
                                  ORDER BY p.stock DESC 
                                  LIMIT 10");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error en getProductsForChart: " . $e->getMessage());
        return [];
    }
}

function getSalesData($connect) {
    try {
        $stmt = $connect->prepare("SELECT DATE(placed_on) as fecha, SUM(total_price) as total
                                  FROM orders 
                                  WHERE placed_on >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                  GROUP BY DATE(placed_on)
                                  ORDER BY fecha ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error en getSalesData: " . $e->getMessage());
        return [];
    }
}

// Obtener todos los datos
$stats = getStatistics($connect);
$recentClients = getRecentClients($connect);
$recentProducts = getRecentProducts($connect);
$birthdayClients = getBirthdayClients($connect);
$productsChart = getProductsForChart($connect);
$salesData = getSalesData($connect);
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PCMARKETTEAM - Dashboard</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/font.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />

    <style>
    .dashboard-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .chart-container {
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .birthday-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stock-badge {
        font-size: 0.85rem;
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .alert-custom {
        border-radius: 8px;
        border: none;
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="body-overlay"></div>

        <!-- Sidebar -->
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Preloader -->
            <div class='pre-loader'>
                <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
            </div>

            <!-- Top Navigation -->
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>

                        <a class="navbar-brand" href="#">Panel Administrativo</a>

                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
                            data-toggle="collapse" data-target="#navbarSupportedContent">
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
                                        <img src="../../backend/img/reere.png" alt="Usuario">
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="../cuenta/perfil.php">Mi perfil</a></li>
                                        <li><a href="../cuenta/salir.php">Salir</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="card card-stats dashboard-card">
                            <div class="card-header">
                                <div class="icon icon-warning">
                                    <span class="material-icons stat-icon">group</span>
                                </div>
                            </div>
                            <div class="card-content">
                                <p class="category"><strong>Clientes</strong></p>
                                <h3 class="card-title"><?php echo number_format($stats['clientes']); ?></h3>
                            </div>
                            <div class="card-footer">
                                <div class="stats">
                                    <i class="material-icons">update</i> Recién actualizado
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="card card-stats dashboard-card">
                            <div class="card-header">
                                <div class="icon icon-rose">
                                    <span class="material-icons stat-icon">inventory</span>
                                </div>
                            </div>
                            <div class="card-content">
                                <p class="category"><strong>Productos</strong></p>
                                <h3 class="card-title"><?php echo number_format($stats['productos']); ?></h3>
                            </div>
                            <div class="card-footer">
                                <div class="stats">
                                    <i class="material-icons">update</i> Recién actualizado
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="card card-stats dashboard-card">
                            <div class="card-header">
                                <div class="icon icon-success">
                                    <span class="material-icons stat-icon">point_of_sale</span>
                                </div>
                            </div>
                            <div class="card-content">
                                <p class="category"><strong>Ventas de Hoy</strong></p>
                                <h3 class="card-title">S/<?php echo number_format($stats['ventas_hoy'], 2); ?></h3>
                            </div>
                            <div class="card-footer">
                                <div class="stats">
                                    <i class="material-icons">update</i> Recién actualizado
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="card card-stats dashboard-card">
                            <div class="card-header">
                                <div class="icon icon-info">
                                    <span class="material-icons stat-icon">manage_accounts</span>
                                </div>
                            </div>
                            <div class="card-content">
                                <p class="category"><strong>Usuarios</strong></p>
                                <h3 class="card-title"><?php echo number_format($stats['usuarios']); ?></h3>
                            </div>
                            <div class="card-footer">
                                <div class="stats">
                                    <i class="material-icons">update</i> Recién actualizado
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert for low stock -->
                <?php if ($stats['stock_bajo'] > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning alert-custom" role="alert">
                            <i class="material-icons">warning</i>
                            <strong>Atención:</strong> Hay <?php echo $stats['stock_bajo']; ?> producto(s) con stock
                            bajo (≤5 unidades).
                            <a href="../productos/" class="alert-link">Ver productos</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Recent Clients and Products -->
                <div class="row mb-4">
                    <!-- Recent Clients -->
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card dashboard-card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Clientes Recientes</h4>
                                <p class="category">Últimos clientes registrados</p>
                            </div>
                            <div class="card-content table-responsive">
                                <?php if (count($recentClients) > 0): ?>
                                <table class="table table-hover" id="clientsTable">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Celular</th>
                                            <th>Correo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentClients as $client): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($client['idclie']); ?></td>
                                            <td><?php echo htmlspecialchars($client['nomcli'] . ' ' . $client['apecli']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($client['celu']); ?></td>
                                            <td><?php echo htmlspecialchars($client['correo']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <div class="alert alert-info alert-custom" role="alert">
                                    <i class="material-icons">info</i>
                                    No se encontraron clientes recientes.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Products -->
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card dashboard-card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Productos Recientes</h4>
                                <p class="category">Últimos productos añadidos</p>
                            </div>
                            <div class="card-content table-responsive">
                                <?php if (count($recentProducts) > 0): ?>
                                <table class="table table-hover" id="productsTable">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>ID</th>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th>Stock</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentProducts as $product): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['idprod']); ?></td>
                                            <td><?php echo htmlspecialchars($product['nomprd']); ?></td>
                                            <td><?php echo htmlspecialchars($product['nomca']); ?></td>
                                            <td>
                                                <?php 
                                                $stock = $product['stock'];
                                                if ($stock <= 0) {
                                                    echo '<span class="badge badge-danger stock-badge">Sin stock</span>';
                                                } elseif ($stock <= 5) {
                                                    echo '<span class="badge badge-warning stock-badge">Stock bajo (' . $stock . ')</span>';
                                                } else {
                                                    echo '<span class="badge badge-success stock-badge">' . $stock . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>S/<?php echo number_format($product['precio'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <div class="alert alert-info alert-custom" role="alert">
                                    <i class="material-icons">info</i>
                                    No se encontraron productos recientes.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Birthday Section -->
                <div class="row mb-4">
                    <!-- Product Statistics Chart -->
                    <div class="col-lg-8 col-md-12 mb-4">
                        <div class="card dashboard-card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Estadísticas de Productos</h4>
                                <p class="category">Stock por producto (Top 10)</p>
                            </div>
                            <div class="card-content">
                                <div id="piechart" class="chart-container"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Birthday Clients -->
                    <div class="col-lg-4 col-md-12 mb-4">
                        <div class="card dashboard-card birthday-card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title text-white">🎉 Cumpleaños de Hoy</h4>
                                <p class="category text-white-50">Clientes que cumplen años hoy</p>
                            </div>
                            <div class="card-content text-center">
                                <img src="../../backend/img/pastel-de-cumple.png" width='120' height='120' class="mb-3"
                                    alt="Cumpleaños">
                                <?php if (count($birthdayClients) > 0): ?>
                                <div class="birthday-list">
                                    <?php foreach ($birthdayClients as $birthday): ?>
                                    <div class="birthday-item mb-2 p-2 bg-white bg-opacity-10 rounded">
                                        <strong><?php echo htmlspecialchars($birthday['nomcli'] . ' ' . $birthday['apecli']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($birthday['celu']); ?></small>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info bg-black bg-opacity-20 border-white text-black"
                                    role="alert">
                                    <i class="material-icons">info</i>
                                    No hay cumpleaños hoy.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sales Chart -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card dashboard-card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Ventas de los Últimos 7 Días</h4>
                                <p class="category">Evolución de las ventas diarias</p>
                            </div>
                            <div class="card-content">
                                <div id="salesChart" class="chart-container"></div>
                            </div>
                        </div>
                    </div>
                    <!-- End Sales Chart de hoy 
                    <div class="col-lg-6 col-md-6">
                        <div class="card dashboard-card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Venta de hoy</h4>
                            </div>
                            <div class="card-content">
                                <div id="sale_values"></div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <!-- Gastos e Ingresos -->
                <div class="row">
                    <div class="col-lg-4 col-md-4"> 
                        <div class="card dashboard-card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Gastos totales</h4>
                            </div>
                            <div class="card-content">
                                <div id="gast_div" height="50" wight="50"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Ingresos -->
                    <div class="col-lg-8 col-md-8">
                        <div class="card dashboard-card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Ingresos totales</h4>
                            </div>
                            <div class="card-content">
                                <div id="chart_div" height="25" wight="25"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../../backend/js/jquery-3.3.1.slim.min.js"></script>
    <script src="../../backend/js/popper.min.js"></script>
    <script src="../../backend/js/bootstrap.min.js"></script>
    <script src="../../backend/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>
    <script src="../../backend/js/loader.js"></script>

    <!-- Data Tables -->
    <script type="text/javascript" src="../../backend/js/datatable.js"></script>
    <script type="text/javascript" src="../../backend/js/datatablebuttons.js"></script>
    <script type="text/javascript" src="../../backend/js/jszip.js"></script>
    <script type="text/javascript" src="../../backend/js/pdfmake.js"></script>
    <script type="text/javascript" src="../../backend/js/vfs_fonts.js"></script>
    <script type="text/javascript" src="../../backend/js/buttonshtml5.js"></script>
    <script type="text/javascript" src="../../backend/js/buttonsprint.js"></script>
    <script type="text/javascript" src="../../backend/js/example.js"></script>

    <!-- Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script>
    // Initialize DataTables
    $(document).ready(function() {
        $('#clientsTable').DataTable({
            "pageLength": 5,
            "ordering": false,
            "searching": false,
            "lengthChange": false,
            "info": false
        });

        $('#productsTable').DataTable({
            "pageLength": 5,
            "ordering": false,
            "searching": false,
            "lengthChange": false,
            "info": false
        });
    });

    // Products Pie Chart
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawPieChart);

    function drawPieChart() {
        var data = google.visualization.arrayToDataTable([
            ['Producto', 'Stock'],
            <?php
                foreach ($productsChart as $product) {
                    echo "['" . addslashes($product['nomprd']) . "', " . $product['stock'] . "],";
                }
                ?>
        ]);

        var options = {
            pieHole: 0.4,
            colors: ['#3366CC', '#DC3912', '#FF9900', '#109618', '#990099', '#0099C6', '#DD4477', '#66AA00',
                '#B82E2E', '#316395'
            ],
            chartArea: {
                width: '90%',
                height: '80%'
            },
            legend: {
                position: 'bottom',
                textStyle: {
                    fontSize: 12
                }
            },
            pieSliceTextStyle: {
                fontSize: 11
            }
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }

    // Sales Line Chart
    google.charts.setOnLoadCallback(drawSalesChart);

    function drawSalesChart() {
        var data = google.visualization.arrayToDataTable([
            ['Fecha', 'Ventas'],
            <?php
                foreach ($salesData as $sale) {
                    echo "['" . $sale['fecha'] . "', " . $sale['total'] . "],";
                }
                ?>
        ]);

        var options = {
            curveType: 'function',
            legend: {
                position: 'bottom'
            },
            hAxis: {
                title: 'Fecha'
            },
            vAxis: {
                title: 'Monto (S/)',
                format: 'currency'
            },
            chartArea: {
                width: '85%',
                height: '70%'
            },
            colors: ['#109618']
        };

        var chart = new google.visualization.LineChart(document.getElementById('salesChart'));
        chart.draw(data, options);
    }

    // Responsive charts
    window.addEventListener('resize', function() {
        drawPieChart();
        drawSalesChart();
    });
    </script>

    <!-- old-file.php -->
    <script>
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Articulo', 'Stock'],




            <?php
                        
        $stmt = $connect->prepare("SELECT producto.idprod, producto.codba, producto.nomprd, categoria.idcate, categoria.nomca, producto.precio, producto.stock, producto.foto, producto.venci, producto.esta, producto.fere, producto.serial, producto.marca, producto.ram, producto.disco, producto.prcpro, producto.pntpro, producto.tarpro, producto.grado FROM producto INNER JOIN categoria ON producto.idcate = categoria.idcate");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['nomprd']."', ".$row['stock']."],";
        }

            ?>
        ]);
        var options = {

            //is3D:true,  
            pieHole: 0.4
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }
    </script>
    <script>
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Articulo', 'Stock'],


            <?php
                        
        $stmt = $connect->prepare("SELECT * FROM clientes");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['apecli']."', ".$row['idclie']."],";
        }

            ?>
        ]);
        var options = {

            //is3D:true,  
            pieHole: 0.4
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechartcli'));
        chart.draw(data, options);
    }
    </script>

    <script type="text/javascript">
    google.charts.load('current', {
        'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
            ['Fecha', 'Monto'],

            <?php
        $id=$_SESSION['id'];
        $stmt = $connect->prepare("SELECT SUM(total_price) total_price,placed_on FROM orders where placed_on = CURDATE()");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['placed_on']."', ".$row['total_price']."],";
        }

            ?>

        ]);

        var options = {
            width: 900,
            legend: {
                position: 'none'
            },
            chart: {
                title: '',
                subtitle: ''
            },
            bars: 'horizontal', // Required for Material Bar Charts.
            axes: {
                x: {
                    0: {
                        side: 'top',
                        label: 'Monto'
                    } // Top x-axis.
                }
            },
            bar: {
                groupWidth: "90%"
            }
        };

        var chart = new google.charts.Bar(document.getElementById('sale_values'));
        chart.draw(data, options);
    };
    </script>
    <script type="text/javascript">
    google.charts.load('current', {
        'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
            ['Fecha', 'Monto'],

            <?php
        $id=$_SESSION['id'];
        $stmt = $connect->prepare("SELECT servicio.idservc, plan.idplan, plan.prec,plan.foto, plan.nompla, servicio.ini, servicio.fin, clientes.idclie, clientes.numid, clientes.nomcli, clientes.apecli, clientes.naci, clientes.celu, clientes.correo, servicio.estod, servicio.fere, SUM(prec) as prec FROM servicio INNER JOIN plan ON servicio.idplan = plan.idplan INNER JOIN clientes ON servicio.idclie = clientes.idclie where servicio.ini = CURDATE()");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['ini']."', ".$row['prec']."],";
        }

            ?>

        ]);

        var options = {
            width: 900,
            legend: {
                position: 'none'
            },
            chart: {
                title: '',
                subtitle: ''
            },
            bars: 'horizontal', // Required for Material Bar Charts.
            axes: {
                x: {
                    0: {
                        side: 'top',
                        label: 'Monto'
                    } // Top x-axis.
                }
            },
            bar: {
                groupWidth: "90%"
            }
        };

        var chart = new google.charts.Bar(document.getElementById('services_values'));
        chart.draw(data, options);
    };
    </script>

    <script type="text/javascript">
    google.charts.load('current', {
        'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
            ['Fecha', 'Monto'],

            <?php
        $id=$_SESSION['id'];
        $stmt = $connect->prepare("SELECT ingresos.iding, ingresos.detalle, ingresos.total, ingresos.fec, SUM(total) as total FROM ingresos");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['fec']."', ".$row['total']."],";
        }

            ?>

        ]);

        var options = {
            width: "90%",
            legend: {
                position: 'none'
            },
            chart: {
                title: '',
                subtitle: ''
            },
            bars: 'horizontal', // Required for Material Bar Charts.
            axes: {
                x: {
                    0: {
                        side: 'top',
                        label: 'Monto'
                    } // Top x-axis.
                }
            },
            bar: {
                groupWidth: "90%"
            }
        };

        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        chart.draw(data, options);
    };
    </script>
    <script type="text/javascript">
    google.charts.load('current', {
        'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
            ['Fecha', 'Monto'],

            <?php
        $id=$_SESSION['id'];
        $stmt = $connect->prepare("SELECT gastos.idga, gastos.detall, gastos.total, gastos.fec, SUM(total) as total FROM gastos ");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['fec']."', ".$row['total']."],";
        }

            ?>

        ]);

        var options = {
            width: "90%",
            legend: {
                position: 'none'
            },
            chart: {
                title: '',
                subtitle: ''
            },
            bars: 'horizontal', // Required for Material Bar Charts.
            axes: {
                x: {
                    0: {
                        side: 'top',
                        label: 'Monto'
                    } // Top x-axis.
                }
            },
            bar: {
                groupWidth: "90%"
            }
        };

        var chart = new google.charts.Bar(document.getElementById('gast_div'));
        chart.draw(data, options);
    };
    </script>
</body>

</html>

<?php ob_end_flush(); ?>