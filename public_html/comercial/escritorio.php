<!-- Comercial 4 -->
<?php
ob_start();
session_start();
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 4])){
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

// Obtener estadísticas del comercial
$usuario_id = $_SESSION['id'];

// Mis solicitudes de alistamiento (últimas 5)
$sql_mis_solicitudes = "SELECT COUNT(*) as total,
                        SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
                        SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas
                        FROM solicitud_alistamiento
                        WHERE usuario_id = :usuario_id";
$stmt_mis_sol = $connect->prepare($sql_mis_solicitudes);
$stmt_mis_sol->execute([':usuario_id' => $usuario_id]);
$stats_solicitudes = $stmt_mis_sol->fetch(PDO::FETCH_ASSOC);

// Últimas 5 solicitudes
$sql_ultimas = "SELECT * FROM solicitud_alistamiento
                WHERE usuario_id = :usuario_id
                ORDER BY fecha_solicitud DESC LIMIT 5";
$stmt_ultimas = $connect->prepare($sql_ultimas);
$stmt_ultimas->execute([':usuario_id' => $usuario_id]);
$ultimas_solicitudes = $stmt_ultimas->fetchAll(PDO::FETCH_ASSOC);

// Mis ventas del mes
$sql_ventas_mes = "SELECT COUNT(*) as total_ventas,
                   SUM(total_price) as total_dinero,
                   SUM(total_products) as total_productos
                   FROM orders
                   WHERE user_id = :usuario_id
                   AND MONTH(placed_on) = MONTH(CURRENT_DATE())
                   AND YEAR(placed_on) = YEAR(CURRENT_DATE())";
$stmt_ventas = $connect->prepare($sql_ventas_mes);
$stmt_ventas->execute([':usuario_id' => $usuario_id]);
$stats_ventas = $stmt_ventas->fetch(PDO::FETCH_ASSOC);

// Total de clientes registrados
$sql_clientes = "SELECT COUNT(*) as total FROM clientes WHERE estad = 'Activo'";
$stmt_clientes = $connect->query($sql_clientes);
$total_clientes = $stmt_clientes->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Comercial - PCMARKETTEAM</title>
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
        .quick-action-card {
            background: linear-gradient(135deg, #2B41CC 0%, #5865F2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .quick-action-card:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(43, 65, 204, 0.3);
        }
        .quick-action-card .material-icons {
            font-size: 48px;
            margin-bottom: 10px;
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
        .bg-gradient-blue {
            background: linear-gradient(135deg, #2B41CC 0%, #4657D8 100%);
        }
        .bg-gradient-green {
            background: linear-gradient(135deg, #00CC54 0%, #00E05F 100%);
        }
        .bg-gradient-red {
            background: linear-gradient(135deg, #CC0618 0%, #E61F30 100%);
        }
        .bg-gradient-yellow {
            background: linear-gradient(135deg, #F0DD00 0%, #FFE500 100%);
        }
        .bg-gradient-purple {
            background: linear-gradient(135deg, #7B2CBF 0%, #9D4EDD 100%);
        }
        .welcome-banner {
            background: linear-gradient(135deg, #2B41CC 0%, #5865F2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(43, 65, 204, 0.25);
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
        .btn-solicitud {
            background: linear-gradient(135deg, #7B2CBF 0%, #9D4EDD 100%);
            color: white;
        }
        .btn-solicitud:hover {
            box-shadow: 0 8px 20px rgba(123, 44, 191, 0.3);
        }
        .btn-venta {
            background: linear-gradient(135deg, #2B41CC 0%, #4657D8 100%);
            color: white;
        }
        .btn-venta:hover {
            box-shadow: 0 8px 20px rgba(43, 65, 204, 0.3);
        }
        .btn-cliente {
            background: linear-gradient(135deg, #00CC54 0%, #00E05F 100%);
            color: white;
        }
        .btn-cliente:hover {
            box-shadow: 0 8px 20px rgba(0, 204, 84, 0.3);
        }
        .btn-historial {
            background: linear-gradient(135deg, #F0DD00 0%, #FFE500 100%);
            color: #333;
        }
        .btn-historial:hover {
            box-shadow: 0 8px 20px rgba(240, 221, 0, 0.3);
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
                        <a class="navbar-brand" href="#">Panel Comercial</a>
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
                        <h2>¡Bienvenid@, <?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?>!</h2>
                        <p style="font-size: 16px; opacity: 0.9;">
                            <i class="material-icons" style="vertical-align: middle;">place</i>
                            <?php echo htmlspecialchars($userInfo['idsede'] ?? 'Sede no definida'); ?> |
                            <i class="material-icons" style="vertical-align: middle;">today</i>
                            <?php echo date('l, d \d\e F Y'); ?>
                        </p>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #7B2CBF;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #7B2CBF;">inventory_2</i> Mis Solicitudes</p>
                                <h3 style="color: #7B2CBF;"><?php echo $stats_solicitudes['total'] ?? 0; ?></h3>
                                <small class="text-muted">Pendientes: <?php echo $stats_solicitudes['pendientes'] ?? 0; ?></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #F0DD00;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #F0DD00;">trending_up</i> En Proceso</p>
                                <h3 style="color: #F0DD00;"><?php echo $stats_solicitudes['en_proceso'] ?? 0; ?></h3>
                                <small class="text-muted">Solicitudes activas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #2B41CC;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #2B41CC;">shopping_cart</i> Ventas del Mes</p>
                                <h3 style="color: #2B41CC;"><?php echo $stats_ventas['total_ventas'] ?? 0; ?></h3>
                                <small class="text-muted">
                                    <?php echo ($stats_ventas['total_productos'] ?? 0); ?> productos vendidos<br>
                                    $<?php echo number_format($stats_ventas['total_dinero'] ?? 0, 0, ',', '.'); ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #00CC54;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #00CC54;">groups</i> Clientes Activos</p>
                                <h3 style="color: #00CC54;"><?php echo $total_clientes; ?></h3>
                                <small class="text-muted">En el sistema</small>
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
                                            <a href="../venta/preventa.php" class="text-decoration-none">
                                                <button class="action-button btn-solicitud">
                                                    <i class="material-icons" style="font-size: 28px;">add_circle</i>
                                                    Nueva Solicitud de Alistamiento
                                                </button>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../venta/nuevo_multiproducto.php" class="text-decoration-none">
                                                <button class="action-button btn-venta">
                                                    <i class="material-icons" style="font-size: 28px;">point_of_sale</i>
                                                    Nueva Venta Multi-Producto
                                                </button>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../clientes/nuevo.php" class="text-decoration-none">
                                                <button class="action-button btn-cliente">
                                                    <i class="material-icons" style="font-size: 28px;">person_add</i>
                                                    Registrar Nuevo Cliente
                                                </button>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../venta/historico_preventa.php" class="text-decoration-none">
                                                <button class="action-button btn-historial">
                                                    <i class="material-icons" style="font-size: 28px;">history</i>
                                                    Ver Historial de Solicitudes
                                                </button>
                                            </a>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <a href="../clientes/mostrar.php" class="btn btn-outline-primary btn-block">
                                                <i class="material-icons" style="vertical-align: middle;">search</i>
                                                Buscar Clientes
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="../venta/mostrar.php" class="btn btn-outline-success btn-block">
                                                <i class="material-icons" style="vertical-align: middle;">receipt_long</i>
                                                Ver Todas las Ventas
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="../venta/catalogo.php" class="btn btn-outline-info btn-block">
                                                <i class="material-icons" style="vertical-align: middle;">inventory</i>
                                                Catálogo de Productos
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Últimas Solicitudes -->
                        <div class="col-lg-4">
                            <div class="card dashboard-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="material-icons" style="vertical-align: middle;">notifications</i> Mis Últimas Solicitudes</h5>
                                </div>
                                <div class="card-body p-0" style="max-height: 450px; overflow-y: auto;">
                                    <?php if (count($ultimas_solicitudes) > 0): ?>
                                        <?php foreach ($ultimas_solicitudes as $sol): ?>
                                            <div class="recent-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>#<?php echo $sol['id']; ?></strong> - <?php echo htmlspecialchars(substr($sol['descripcion'], 0, 30)); ?>...
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="material-icons" style="font-size: 14px; vertical-align: middle;">schedule</i>
                                                            <?php echo date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])); ?>
                                                        </small>
                                                    </div>
                                                    <div>
                                                        <?php
                                                        $badge_class = 'badge-secondary';
                                                        switch($sol['estado']) {
                                                            case 'pendiente': $badge_class = 'badge-warning'; break;
                                                            case 'en_proceso': $badge_class = 'badge-info'; break;
                                                            case 'completada': $badge_class = 'badge-success'; break;
                                                        }
                                                        ?>
                                                        <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($sol['estado']); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-muted">
                                            <i class="material-icons" style="font-size: 48px;">inventory_2</i>
                                            <p>No tienes solicitudes aún</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="../venta/historico_preventa.php" class="btn btn-sm btn-primary">
                                        Ver Todas <i class="material-icons" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clientes de Mi Sede -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card dashboard-card">
                                <div class="card-header">
                                    <h4 class="mb-0">
                                        <i class="material-icons" style="vertical-align: middle;">store</i>
                                        Clientes de Mi Sede
                                    </h4>
                                </div>
                                <div class="card-body text-center" style="padding: 40px;">
                                    <a href="../clientes/mostrar.php?sede=<?php echo urlencode($userInfo['idsede'] ?? ''); ?>" class="btn btn-lg" style="background: linear-gradient(135deg, #00CC54 0%, #00E05F 100%); color: white; padding: 20px 50px; border-radius: 10px; font-size: 18px; box-shadow: 0 4px 15px rgba(0, 204, 84, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0, 204, 84, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 204, 84, 0.3)';">
                                        <i class="material-icons" style="vertical-align: middle; font-size: 28px;">groups</i>
                                        Ver Clientes de <?php echo htmlspecialchars($userInfo['idsede'] ?? 'Mi Sede'); ?>
                                    </a>
                                    <p class="text-muted mt-3 mb-0">
                                        <small>Solo se mostrarán los clientes registrados en tu sede actual</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>
