<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4])) {
    header('location: ../error404.php');
    exit;
}
require_once('../../config/ctconex.php');

// --- CÓDIGO PARA LA BARRA DE NAVEGACIÓN ---
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

// --- CÓDIGO PARA OBTENER ESTADÍSTICAS DE VENTAS ---
$today = date("Y-m-d");
$month = date("Y-m");

// Total ventas del día
$stmt_day = $connect->prepare("SELECT SUM(total_price) as total_dia FROM orders WHERE DATE(placed_on) = :today");
$stmt_day->bindParam(':today', $today);
$stmt_day->execute();
$total_dia = $stmt_day->fetch(PDO::FETCH_ASSOC)['total_dia'] ?? 0;

// Total ventas del mes
$stmt_month = $connect->prepare("SELECT SUM(total_price) as total_mes FROM orders WHERE DATE_FORMAT(placed_on, '%Y-%m') = :month");
$stmt_month->bindParam(':month', $month);
$stmt_month->execute();
$total_mes = $stmt_month->fetch(PDO::FETCH_ASSOC)['total_mes'] ?? 0;

// Total ventas general
$stmt_general = $connect->prepare("SELECT SUM(total_price) as total_general FROM orders");
$stmt_general->execute();
$total_general = $stmt_general->fetch(PDO::FETCH_ASSOC)['total_general'] ?? 0;

// Obtener todas las ventas para la tabla
$stmt_ventas = $connect->prepare("SELECT o.idord, c.nomcli, c.apecli, o.total_price, o.placed_on, o.responsable FROM orders o JOIN clientes c ON o.user_cli = c.idclie ORDER BY o.placed_on DESC");
$stmt_ventas->execute();
$ventas = $stmt_ventas->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
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
                    <a class="navbar-brand" href="#"> Reporte de Ventas </a>
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
        <div class="main-content">
            <div class="container-fluid">
                <!-- Cards de Resumen -->
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="material-icons">today</i>
                                    </div>
                                    <div class="ml-3">
                                        <h6>Ventas del Día</h6>
                                        <h4 class="card-title">$<?php echo number_format($total_dia, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-big text-center icon-success">
                                        <i class="material-icons">calendar_today</i>
                                    </div>
                                    <div class="ml-3">
                                        <h6>Ventas del Mes</h6>
                                        <h4 class="card-title">$<?php echo number_format($total_mes, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-big text-center icon-primary">
                                        <i class="material-icons">assessment</i>
                                    </div>
                                    <div class="ml-3">
                                        <h6>Ventas Generales</h6>
                                        <h4 class="card-title">$<?php echo number_format($total_general, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Ventas -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Historial de Ventas</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID Venta</th>
                                                <th>Cliente</th>
                                                <th>Total</th>
                                                <th>Fecha</th>
                                                <th>Responsable</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ventas as $venta): ?>
                                            <tr>
                                                <td><?php echo $venta['idord']; ?></td>
                                                <td><?php echo htmlspecialchars($venta['nomcli'] . ' ' . $venta['apecli']); ?></td>
                                                <td>$<?php echo number_format($venta['total_price'], 2); ?></td>
                                                <td><?php echo date("d/m/Y H:i", strtotime($venta['placed_on'])); ?></td>
                                                <td><?php echo htmlspecialchars($venta['responsable']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info btn-details" data-id="<?php echo $venta['idord']; ?>" data-toggle="modal" data-target="#detailsModal">
                                                        <i class="material-icons">visibility</i>
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
    </div>
</div>

<!-- Modal para Detalles de Venta -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="saleDetails">Cargando...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#sidebarCollapse').on('click', function() {
        $('#sidebar').toggleClass('active');
        $('#content').toggleClass('active');
    });

    $('.more-button,.body-overlay').on('click', function() {
        $('#sidebar,.body-overlay').toggleClass('show-nav');
    });

    $('.btn-details').on('click', function() {
        var saleId = $(this).data('id');
        $('#saleDetails').html('Cargando...');
        $.ajax({
            url: '../controllers/get_venta_details.php',
            type: 'POST',
            data: { id: saleId },
            success: function(response) {
                $('#saleDetails').html(response);
            },
            error: function() {
                $('#saleDetails').html('<div class="alert alert-danger">No se pudieron cargar los detalles.</div>');
            }
        });
    });
});
</script>
</body>
</html>
