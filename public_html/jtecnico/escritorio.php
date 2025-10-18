<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
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
// ==================== ESTADÍSTICAS DEL INVENTARIO ====================
// Total equipos activos
$sql_total_equipos = "SELECT COUNT(*) as total FROM bodega_inventario WHERE estado = 'activo'";
$stmt_total = $connect->query($sql_total_equipos);
$total_equipos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
// Equipos disponibles (Business Room + Para Venta)
$sql_disponibles = "SELECT COUNT(*) as total FROM bodega_inventario
                    WHERE estado = 'activo'
                    AND disposicion IN ('Business Room', 'Para Venta', 'disponible', 'aprobado')";
$stmt_disp = $connect->query($sql_disponibles);
$equipos_disponibles = $stmt_disp->fetch(PDO::FETCH_ASSOC)['total'];
// Equipos en proceso
$sql_proceso = "SELECT COUNT(*) as total FROM bodega_inventario
                WHERE estado = 'activo'
                AND disposicion IN ('en_diagnostico', 'en_reparacion', 'En revisión', 'en_control')";
$stmt_proc = $connect->query($sql_proceso);
$equipos_proceso = $stmt_proc->fetch(PDO::FETCH_ASSOC)['total'];
// Equipos pendientes
$sql_pendientes = "SELECT COUNT(*) as total FROM bodega_inventario
                   WHERE estado = 'activo'
                   AND disposicion IN ('pendiente', 'En reparación')";
$stmt_pend = $connect->query($sql_pendientes);
$equipos_pendientes = $stmt_pend->fetch(PDO::FETCH_ASSOC)['total'];
// ==================== ESTADÍSTICAS DE TÉCNICOS ====================
// Total técnicos activos
$sql_tecnicos = "SELECT COUNT(*) as total FROM usuarios WHERE rol IN (5, 6, 7) AND estado = 1";
$stmt_tec = $connect->query($sql_tecnicos);
$total_tecnicos = $stmt_tec->fetch(PDO::FETCH_ASSOC)['total'];
// ==================== SOLICITUDES DE ALISTAMIENTO ====================
$sql_solicitudes = "SELECT COUNT(*) as total FROM solicitud_alistamiento WHERE estado = 'pendiente'";
$stmt_sol = $connect->query($sql_solicitudes);
$solicitudes_pendientes = $stmt_sol->fetch(PDO::FETCH_ASSOC)['total'];
$sql_solicitudes_proceso = "SELECT COUNT(*) as total FROM solicitud_alistamiento WHERE estado = 'en_proceso'";
$stmt_sol_proc = $connect->query($sql_solicitudes_proceso);
$solicitudes_proceso = $stmt_sol_proc->fetch(PDO::FETCH_ASSOC)['total'];
// ==================== SOLICITUDES RECIENTES ====================
$sql_solicitudes_recientes = "SELECT sa.*, u.nombre as solicitante_nombre
                               FROM solicitud_alistamiento sa
                               LEFT JOIN usuarios u ON sa.usuario_id = u.id
                               ORDER BY sa.fecha_solicitud DESC
                               LIMIT 8";
$stmt_sol_rec = $connect->prepare($sql_solicitudes_recientes);
$stmt_sol_rec->execute();
$solicitudes_recientes = $stmt_sol_rec->fetchAll(PDO::FETCH_ASSOC);
// ==================== ÚLTIMAS ACTIVIDADES EN INVENTARIO ====================
$sql_actividades = "SELECT i.*, u.nombre as tecnico_nombre
                    FROM bodega_inventario i
                    LEFT JOIN usuarios u ON i.tecnico_id = u.id
                    WHERE i.estado = 'activo'
                    ORDER BY i.fecha_modificacion DESC
                    LIMIT 10";
$stmt_act = $connect->prepare($sql_actividades);
$stmt_act->execute();
$actividades_recientes = $stmt_act->fetchAll(PDO::FETCH_ASSOC);

// ==================== FECHA EN ESPAÑOL ====================
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
$dias_semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$fecha_actual = $dias_semana[date('w')] . ', ' . date('d') . ' de ' . $meses[date('n')] . ' ' . date('Y');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Jefe Técnico - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .dashboard-card {
            border-radius: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
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
            background: linear-gradient(135deg, #7B2CBF 0%, #9D4EDD 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(123, 44, 191, 0.25);
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
            font-size: 16px;
            font-weight: 500;
            border-radius: 10px;
            margin-bottom: 15px;
            border: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        .action-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            text-decoration: none;
        }
        .btn-inventario {
            background: linear-gradient(135deg, #2B41CC 0%, #4657D8 100%);
            color: white;
        }
        .btn-business {
            background: linear-gradient(135deg, #00CC54 0%, #00E05F 100%);
            color: white;
        }
        .btn-solicitudes {
            background: linear-gradient(135deg, #F0DD00 0%, #FFE500 100%);
            color: #333;
        }
        .btn-tecnicos {
            background: linear-gradient(135deg, #7B2CBF 0%, #9D4EDD 100%);
            color: white;
        }
        .btn-laboratorio {
            background: linear-gradient(135deg, #CC0618 0%, #E61F30 100%);
            color: white;
        }
        .btn-despacho {
            background: linear-gradient(135deg, #00CC54 0%, #00E05F 100%);
            color: white;
        }
        .badge-estado {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-pendiente { background: #FFF3CD; color: #856404; }
        .badge-proceso { background: #D1ECF1; color: #0C5460; }
        .badge-completado { background: #D4EDDA; color: #155724; }
        .badge-rechazado { background: #F8D7DA; color: #721C24; }

        /* Fix para evitar doble scroll */
        .solicitudes-container {
            max-height: 420px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Personalizar scrollbar */
        .solicitudes-container::-webkit-scrollbar {
            width: 6px;
        }

        .solicitudes-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .solicitudes-container::-webkit-scrollbar-thumb {
            background: #F0DD00;
            border-radius: 10px;
        }

        .solicitudes-container::-webkit-scrollbar-thumb:hover {
            background: #d4c400;
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
                        <a class="navbar-brand" href="#">Panel Jefe Técnico</a>
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
                        <h2>🔧 Panel del Jefe Técnico</h2>
                        <p style="font-size: 16px; opacity: 0.9;">
                            <i class="material-icons" style="vertical-align: middle;">person</i>
                            <?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?> |
                            <i class="material-icons" style="vertical-align: middle;">today</i>
                            <?php echo $fecha_actual; ?>
                        </p>
                    </div>
                    <!-- Estadísticas Rápidas -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #2B41CC;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #2B41CC;">inventory</i> Total Equipos</p>
                                <h3 style="color: #2B41CC;"><?php echo $total_equipos; ?></h3>
                                <small class="text-muted">Activos en sistema</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #00CC54;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #00CC54;">check_circle</i> Disponibles</p>
                                <h3 style="color: #00CC54;"><?php echo $equipos_disponibles; ?></h3>
                                <small class="text-muted">Listos para venta</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #F0DD00;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #F0DD00;">build</i> En Proceso</p>
                                <h3 style="color: #F0DD00;"><?php echo $equipos_proceso; ?></h3>
                                <small class="text-muted">En reparación</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="border-left-color: #CC0618;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #CC0618;">pending</i> Pendientes</p>
                                <h3 style="color: #CC0618;"><?php echo $equipos_pendientes; ?></h3>
                                <small class="text-muted">Por asignar</small>
                            </div>
                        </div>
                    </div>
                    <!-- Segunda Fila de Estadísticas -->
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="stat-card" style="border-left-color: #7B2CBF;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #7B2CBF;">engineering</i> Técnicos Activos</p>
                                <h3 style="color: #7B2CBF;"><?php echo $total_tecnicos; ?></h3>
                                <small class="text-muted">En el equipo</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card" style="border-left-color: #F0DD00;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #F0DD00;">assignment</i> Solicitudes Pendientes</p>
                                <h3 style="color: #F0DD00;"><?php echo $solicitudes_pendientes; ?></h3>
                                <small class="text-muted">Por alistamiento</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card" style="border-left-color: #2B41CC;">
                                <p><i class="material-icons" style="vertical-align: middle; color: #2B41CC;">schedule</i> En Proceso</p>
                                <h3 style="color: #2B41CC;"><?php echo $solicitudes_proceso; ?></h3>
                                <small class="text-muted">Alistamientos activos</small>
                            </div>
                        </div>
                    </div>
                    <!-- Acciones Rápidas -->
                    <div class="row mt-4">
                        <div class="col-lg-8">
                            <div class="card dashboard-card">
                                <div class="card-header" style="background: linear-gradient(135deg, #7B2CBF 0%, #9D4EDD 100%); color: white;">
                                    <h4 class="mb-0"><i class="material-icons" style="vertical-align: middle;">flash_on</i> Acciones Rápidas</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="../laboratorio/mostrar.php" class="action-button btn-inventario">
                                                <i class="material-icons" style="font-size: 24px;">inventory_2</i>
                                                Ver Inventario General
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../b_room/mostrar.php" class="action-button btn-business">
                                                <i class="material-icons" style="font-size: 24px;">store</i>
                                                Business Room
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../despacho/historial_solicitudes_alistamiento.php" class="action-button btn-solicitudes">
                                                <i class="material-icons" style="font-size: 24px;">assignment</i>
                                                Solicitudes Alistamiento
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../usuario/mostrar.php" class="action-button btn-tecnicos">
                                                <i class="material-icons" style="font-size: 24px;">groups</i>
                                                Gestionar Técnicos
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../bodega/asignar.php" class="action-button btn-laboratorio">
                                                <i class="material-icons" style="font-size: 24px;">assignment_ind</i>
                                                Asignar Equipos
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../despacho/pendientes.php" class="action-button btn-despacho">
                                                <i class="material-icons" style="font-size: 24px;">local_shipping</i>
                                                Órdenes Pendientes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Solicitudes Recientes -->
                        <div class="col-lg-4">
                            <div class="card dashboard-card">
                                <div class="card-header" style="background: linear-gradient(135deg, #F0DD00 0%, #FFE500 100%);">
                                    <h5 class="mb-0" style="color: #333;"><i class="material-icons" style="vertical-align: middle;">assignment</i> Solicitudes Recientes</h5>
                                </div>
                                <div class="card-body p-0 solicitudes-container">
                                    <?php if (count($solicitudes_recientes) > 0): ?>
                                        <?php foreach ($solicitudes_recientes as $sol): ?>
                                            <div class="recent-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div style="flex: 1;">
                                                        <strong>Sol. #<?php echo $sol['id']; ?></strong>
                                                        <span class="badge-estado badge-<?php
                                                            echo $sol['estado'] == 'pendiente' ? 'pendiente' :
                                                                 ($sol['estado'] == 'en_proceso' ? 'proceso' :
                                                                  ($sol['estado'] == 'completado' ? 'completado' : 'rechazado'));
                                                        ?>"><?php echo ucfirst($sol['estado']); ?></span>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="material-icons" style="font-size: 12px; vertical-align: middle;">person</i>
                                                            <?php echo htmlspecialchars($sol['solicitante_nombre'] ?? 'Sin asignar'); ?>
                                                        </small><br>
                                                        <small class="text-muted">
                                                            <i class="material-icons" style="font-size: 12px; vertical-align: middle;">schedule</i>
                                                            <?php echo date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-muted">
                                            <i class="material-icons" style="font-size: 48px;">assignment</i>
                                            <p>No hay solicitudes recientes</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="../despacho/historial_solicitudes_alistamiento.php" class="btn btn-sm btn-warning">
                                        Ver Todas <i class="material-icons" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Actividades Recientes del Inventario -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card dashboard-card">
                                <div class="card-header" style="background: linear-gradient(135deg, #2B41CC 0%, #4657D8 100%); color: white;">
                                    <h5 class="mb-0"><i class="material-icons" style="vertical-align: middle;">history</i> Últimas Actividades en Inventario</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead style="background: #f8f9fa;">
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Producto</th>
                                                    <th>Marca/Modelo</th>
                                                    <th>Disposición</th>
                                                    <th>Técnico</th>
                                                    <th>Última Modificación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($actividades_recientes) > 0): ?>
                                                    <?php foreach ($actividades_recientes as $act): ?>
                                                        <tr>
                                                            <td><strong><?php echo htmlspecialchars($act['codigo_g']); ?></strong></td>
                                                            <td><?php echo htmlspecialchars($act['producto']); ?></td>
                                                            <td><?php echo htmlspecialchars($act['marca'] . ' ' . $act['modelo']); ?></td>
                                                            <td>
                                                                <span class="badge badge-info"><?php echo htmlspecialchars($act['disposicion']); ?></span>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($act['tecnico_nombre'] ?? 'Sin asignar'); ?></td>
                                                            <td>
                                                                <small><?php echo date('d/m/Y H:i', strtotime($act['fecha_modificacion'])); ?></small>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">
                                                            <i class="material-icons" style="font-size: 48px;">inventory</i>
                                                            <p>No hay actividades recientes</p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="../laboratorio/mostrar.php" class="btn btn-sm btn-primary">
                                        Ver Inventario Completo <i class="material-icons" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>
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
    <script>
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
</body>
</html>
<?php ob_end_flush(); ?>
