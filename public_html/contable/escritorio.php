<!-- CONTABLE 3 -->
<?php
ob_start();
session_start();
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1,3])){
    header('location: ../error404.php');
    exit;
}
require_once('../../config/ctconex.php');
// Obtener información del usuario
$userInfo = [];
if (isset($_SESSION['id'])) {
    $sqlUser = "SELECT nombre, usuario, correo, foto, rol, idsede FROM usuarios WHERE id = :id";
    $stmtUser = $connect->prepare($sqlUser);
    $stmtUser->execute([':id' => $_SESSION['id']]);
    $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);
}
// Estadísticas de ventas
$usuario_id = $_SESSION['id'];
// Ventas totales del día
$sql_ventas_dia = "SELECT COUNT(*) as total_ventas,
                   SUM(total_price) as total_dinero,
                   SUM(total_products) as total_productos
                   FROM orders
                   WHERE DATE(placed_on) = CURDATE()";
$stmt_ventas_dia = $connect->prepare($sql_ventas_dia);
$stmt_ventas_dia->execute();
$stats_ventas_dia = $stmt_ventas_dia->fetch(PDO::FETCH_ASSOC);
// Ventas del mes
$sql_ventas_mes = "SELECT COUNT(*) as total_ventas,
                   SUM(total_price) as total_dinero,
                   SUM(total_products) as total_productos
                   FROM orders
                   WHERE MONTH(placed_on) = MONTH(CURRENT_DATE())
                   AND YEAR(placed_on) = YEAR(CURRENT_DATE())";
$stmt_ventas_mes = $connect->prepare($sql_ventas_mes);
$stmt_ventas_mes->execute();
$stats_ventas_mes = $stmt_ventas_mes->fetch(PDO::FETCH_ASSOC);
// Total de clientes
$sql_clientes = "SELECT COUNT(*) as total FROM clientes WHERE estad = 'Activo'";
$stmt_clientes = $connect->query($sql_clientes);
$total_clientes = $stmt_clientes->fetch(PDO::FETCH_ASSOC)['total'];
// Ventas recientes (últimas 10)
$sql_ventas_recientes = "SELECT o.*, u.nombre as vendedor_nombre
                         FROM orders o
                         LEFT JOIN usuarios u ON o.user_id = u.id
                         ORDER BY o.placed_on DESC
                         LIMIT 10";
$stmt_ventas_recientes = $connect->prepare($sql_ventas_recientes);
$stmt_ventas_recientes->execute();
$ventas_recientes = $stmt_ventas_recientes->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Contable - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .dashboard-card {
            border-radius: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            height: 100%;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border-left: 5px solid;
        }
        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-card p {
            color: #6c757d;
            margin: 0;
        }
        .welcome-banner {
            background: #7AD48E;
            color: #333;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(240, 221, 0, 0.25);
        }
        .welcome-banner h2 {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .recent-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            transition: background 0.2s;
        }
        .recent-item:hover {
            background: #f8f9fa;
        }
        .recent-item:last-child {
            border-bottom: none;
        }
        .action-button {
            width: 100%;
            padding: 20px;
            font-size: 18px;
            font-weight: 500;
            border-radius: 10px;
            margin-bottom: 15px;
            border: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .action-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .btn-ventas {
            background: linear-gradient(135deg, #2B41CC 0%, #4657D8 100%);
            color: white;
        }
        .btn-ventas:hover {
            box-shadow: 0 8px 20px rgba(43, 65, 204, 0.3);
        }
        .btn-clientes {
            background: #2B6B5D;
            color: white;
        }
        .btn-clientes:hover {
            box-shadow: 0 8px 20px rgba(0, 204, 84, 0.3);
        }
        .btn-reportes {
            background: linear-gradient(135deg, #F0DD00 0%, #FFE500 100%);
            color: #333;
        }
        .btn-reportes:hover {
            box-shadow: 0 8px 20px rgba(240, 221, 0, 0.3);
        }
        .btn-inventario {
            background: linear-gradient(135deg, #7B2CBF 0%, #9D4EDD 100%);
            color: white;
        }
        .btn-inventario:hover {
            box-shadow: 0 8px 20px rgba(123, 44, 191, 0.3);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <div id="content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#">Panel Contable</a>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                            <span class="material-icons">more_vert</span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="nav navbar-nav ml-auto">
                                <li class="dropdown nav-item active">
                                    <a href="#" class="nav-link" data-toggle="dropdown">
                                        <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
                                             style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
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
            <!-- Main Content -->
            <div class="main-content">
                <div class="container-fluid">
                    <!-- Welcome Banner -->
                    <div class="welcome-banner">
                        <h2>💰 Panel Contable</h2>
                        <p style="font-size: 16px; opacity: 0.9;">
                            <i class="material-icons" style="vertical-align: middle;">person</i>
                            <?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?> |
                            <i class="material-icons" style="vertical-align: middle;">today</i>
                            <?php echo date('l, d \d\e F Y'); ?>
                        </p>
                    </div>
                    <!-- Estadísticas Rápidas -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #2B41CC;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #2B41CC;">today</i> Ventas de Hoy</p>
                                <h3 style="color: #2B41CC;"><?php echo $stats_ventas_dia['total_ventas'] ?? 0; ?></h3>
                                <small class="text-muted">
                                    $<?php echo number_format($stats_ventas_dia['total_dinero'] ?? 0, 0, ',', '.'); ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #00CC54;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #00CC54;">calendar_month</i> Ventas del Mes</p>
                                <h3 style="color: #00CC54;"><?php echo $stats_ventas_mes['total_ventas'] ?? 0; ?></h3>
                                <small class="text-muted">
                                    <?php echo ($stats_ventas_mes['total_productos'] ?? 0); ?> productos vendidos<br>
                                    $<?php echo number_format($stats_ventas_mes['total_dinero'] ?? 0, 0, ',', '.'); ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #F0DD00;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #F0DD00;">groups</i> Clientes Activos</p>
                                <h3 style="color: #F0DD00;"><?php echo $total_clientes; ?></h3>
                                <small class="text-muted">En el sistema</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #7B2CBF;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #7B2CBF;">trending_up</i> Promedio Venta</p>
                                <h3 style="color: #7B2CBF;">$<?php
                                    $promedio = ($stats_ventas_mes['total_ventas'] ?? 0) > 0
                                        ? number_format(($stats_ventas_mes['total_dinero'] ?? 0) / $stats_ventas_mes['total_ventas'], 0, ',', '.')
                                        : 0;
                                    echo $promedio;
                                ?></h3>
                                <small class="text-muted">Por venta este mes</small>
                            </div>
                        </div>
                    </div>
                    <!-- Acciones Rápidas -->
                    <div class="row mt-4">
                        <div class="col-lg-8">
                            <div class="card dashboard-card">
                                <div class="card-header">
                                    <h4 class="mb-0"><i class="material-icons" style="vertical-align: middle;">flash_on</i> Acciones Rápidas</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="../venta/mostrar.php" class="text-decoration-none">
                                                <button class="action-button btn-ventas">
                                                    <i class="material-icons" style="font-size: 28px;">receipt_long</i>
                                                    Ver Todas las Ventas
                                                </button>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../clientes/mostrar.php" class="text-decoration-none">
                                                <button class="action-button btn-clientes">
                                                    <i class="material-icons" style="font-size: 28px;">groups</i>
                                                    Ver Todos los Clientes
                                                </button>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../venta/mostrar.php" class="text-decoration-none">
                                                <button class="action-button btn-reportes">
                                                    <i class="material-icons" style="font-size: 28px;">assessment</i>
                                                    Generar Reportes
                                                </button>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../laboratorio/mostrar.php" class="text-decoration-none">
                                                <button class="action-button btn-inventario">
                                                    <i class="material-icons" style="font-size: 28px;">inventory</i>
                                                    Ver Inventario
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Ventas Recientes -->
                        <div class="col-lg-4">
                            <div class="card dashboard-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="material-icons" style="vertical-align: middle;">history</i> Ventas Recientes</h5>
                                </div>
                                <div class="card-body p-0" style="max-height: 450px; overflow-y: auto;">
                                    <?php if (count($ventas_recientes) > 0): ?>
                                        <?php foreach ($ventas_recientes as $venta): ?>
                                            <div class="recent-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>Orden #<?php echo $venta['idord']; ?></strong><br>
                                                        <small class="text-muted">
                                                            <i class="material-icons" style="font-size: 14px; vertical-align: middle;">person</i>
                                                            <?php echo htmlspecialchars($venta['vendedor_nombre'] ?? 'Sin vendedor'); ?>
                                                        </small><br>
                                                        <small class="text-muted">
                                                            <i class="material-icons" style="font-size: 14px; vertical-align: middle;">schedule</i>
                                                            <?php echo date('d/m/Y H:i', strtotime($venta['placed_on'])); ?>
                                                        </small>
                                                    </div>
                                                    <div class="text-right">
                                                        <strong class="text-success">$<?php echo number_format($venta['total_price'], 0, ',', '.'); ?></strong><br>
                                                        <small class="text-muted"><?php echo $venta['total_products']; ?> productos</small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-muted">
                                            <i class="material-icons" style="font-size: 48px;">receipt</i>
                                            <p>No hay ventas recientes</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="../venta/mostrar.php" class="btn btn-sm btn-primary">
                                        Ver Todas <i class="material-icons" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>
