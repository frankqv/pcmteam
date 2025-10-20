<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 7])) {
    header('location: ../error404.php');
    exit;
}
?>
<?php if (isset($_SESSION['id'])) { ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Escritorio Bodega - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .stats-card {
            border-radius: 8px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .stats-card h3 {
            font-size: 2.5rem;
            margin: 0;
            font-weight: bold;
        }
        .stats-card p {
            margin: 10px 0 0 0;
            font-size: 1rem;
        }
        .quick-action-btn {
            height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            border-radius: 8px;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .quick-action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .quick-action-btn .material-icons {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .quick-action-btn span {
            font-weight: 500;
            font-size: 14px;
        }
        .recent-activity {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .activity-item {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .badge-disponible { background: #00CC54; }
        .badge-proceso { background: #F0DD00; color: #333; }
        .badge-pendiente { background: #CC0618; }
        .badge-reparacion { background: #2B41CC; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <!-- Page Content -->
        <div id="content">
            <div class='pre-loader'>
                <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
            </div>
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#">
                            <span class="material-icons" style="vertical-align: middle;">warehouse</span>
                            Escritorio Bodega
                        </a>
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
                                        <li><a href="../cuenta/perfil.php">Mi perfil</a></li>
                                        <li><a href="../cuenta/salir.php">Salir</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="main-content">
                <?php
                require_once '../../config/ctconex.php';

                // Obtener estadísticas de inventario
                try {
                    $sqlStats = "SELECT
                        COUNT(*) as total,
                        SUM(CASE WHEN estado IN ('activo', 'inactivo', 'Business') AND disposicion = 'disponible' THEN 1 ELSE 0 END) as disponibles,
                        SUM(CASE WHEN estado = 'activo' AND disposicion IN ('en_diagnostico', 'en_reparacion', 'en_control') THEN 1 ELSE 0 END) as en_proceso,
                        SUM(CASE WHEN estado = 'activo' AND disposicion = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN grado = 'A' THEN 1 ELSE 0 END) as grado_a,
                        SUM(CASE WHEN grado = 'B' THEN 1 ELSE 0 END) as grado_b,
                        SUM(CASE WHEN grado = 'C' THEN 1 ELSE 0 END) as grado_c
                    FROM bodega_inventario";

                    $stmtStats = $connect->query($sqlStats);
                    $stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

                    // Obtener estadísticas de solicitudes de alistamiento
                    $sqlAlistamiento = "SELECT
                        COUNT(*) as total_solicitudes,
                        SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes_alistamiento,
                        SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso_alistamiento,
                        SUM(CASE WHEN estado IN ('despachado', 'entregado') THEN 1 ELSE 0 END) as despachados
                    FROM solicitudes_alistamiento";

                    $stmtAlistamiento = $connect->query($sqlAlistamiento);
                    $statsAlistamiento = $stmtAlistamiento->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $stats = [
                        'total' => 0,
                        'disponibles' => 0,
                        'en_proceso' => 0,
                        'pendientes' => 0,
                        'grado_a' => 0,
                        'grado_b' => 0,
                        'grado_c' => 0
                    ];
                    $statsAlistamiento = [
                        'total_solicitudes' => 0,
                        'pendientes_alistamiento' => 0,
                        'en_proceso_alistamiento' => 0,
                        'despachados' => 0
                    ];
                }
                ?>

                <!-- Resumen de Estadísticas -->
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card" style="background: #2B6B5D;">
                            <h3><?php echo number_format($stats['total']); ?></h3>
                            <p>Total Equipos en Bodega</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card" style="background: #00CC54;">
                            <h3><?php echo number_format($stats['disponibles']); ?></h3>
                            <p>Equipos Disponibles</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card" style="background: #F0DD00; color: #333;">
                            <h3><?php echo number_format($stats['en_proceso']); ?></h3>
                            <p>Equipos en Proceso</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card" style="background: #CC0618;">
                            <h3><?php echo number_format($stats['pendientes']); ?></h3>
                            <p>Equipos Pendientes</p>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Alistamiento -->
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card" style="background: #7B2CBF;">
                            <h3><?php echo number_format($statsAlistamiento['total_solicitudes']); ?></h3>
                            <p>Solicitudes de Alistamiento</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card" style="background: #CC0618;">
                            <h3><?php echo number_format($statsAlistamiento['pendientes_alistamiento']); ?></h3>
                            <p>Pendientes Alistamiento</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card" style="background: #F0DD00; color: #333;">
                            <h3><?php echo number_format($statsAlistamiento['en_proceso_alistamiento']); ?></h3>
                            <p>En Proceso Alistamiento</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-card" style="background: #00CC54;">
                            <h3><?php echo number_format($statsAlistamiento['despachados']); ?></h3>
                            <p>Despachados/Entregados</p>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acceso Rápido -->
                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-3">Acciones Rápidas</h4>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="../bodega/mostrar_inventario.php" class="btn btn-primary btn-block quick-action-btn">
                            <span class="material-icons">inventory_2</span>
                            <span>Ver Inventario Completo</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="../bodega/nuevo_inventario.php" class="btn btn-success btn-block quick-action-btn">
                            <span class="material-icons">add_box</span>
                            <span>Nuevo Equipo</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="../despacho/historial_solicitudes_alistamiento.php" class="btn btn-warning btn-block quick-action-btn" style="color: #333;">
                            <span class="material-icons">fact_check</span>
                            <span>Solicitudes Alistamiento</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="../bodega/entradas_salidas.php" class="btn btn-info btn-block quick-action-btn">
                            <span class="material-icons">swap_horiz</span>
                            <span>Entradas y Salidas</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="../bodega/reportes.php" class="btn btn-secondary btn-block quick-action-btn">
                            <span class="material-icons">assessment</span>
                            <span>Reportes</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="../bodega/buscar_equipo.php" class="btn btn-dark btn-block quick-action-btn">
                            <span class="material-icons">search</span>
                            <span>Buscar Equipo</span>
                        </a>
                    </div>
                </div>

                <!-- Estadísticas por Grado -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h4 class="mb-3">Inventario por Grado</h4>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h2 style="color: #00CC54; font-weight: bold;"><?php echo number_format($stats['grado_a']); ?></h2>
                                <p class="mb-0">Grado A - Excelente Estado</p>
                                <small class="text-muted">Equipos en perfectas condiciones</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h2 style="color: #F0DD00; font-weight: bold;"><?php echo number_format($stats['grado_b']); ?></h2>
                                <p class="mb-0">Grado B - Buen Estado</p>
                                <small class="text-muted">Equipos funcionales con desgaste menor</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h2 style="color: #CC0618; font-weight: bold;"><?php echo number_format($stats['grado_c']); ?></h2>
                                <p class="mb-0">Grado C - Estado Regular</p>
                                <small class="text-muted">Equipos con desgaste visible</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actividad Reciente -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h4 class="mb-3">Actividad Reciente</h4>
                    </div>
                    <div class="col-lg-6">
                        <div class="recent-activity">
                            <h5 class="mb-3">Últimos Movimientos de Inventario</h5>
                            <?php
                            try {
                                $sqlRecent = "SELECT i.codigo_g, i.producto, i.marca, i.modelo, i.fecha_modificacion, i.disposicion
                                    FROM bodega_inventario i
                                    ORDER BY i.fecha_modificacion DESC
                                    LIMIT 5";

                                $stmtRecent = $connect->query($sqlRecent);
                                $recentItems = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

                                if (count($recentItems) > 0) {
                                    foreach ($recentItems as $item) {
                                        $badgeClass = 'badge-disponible';
                                        if ($item['disposicion'] == 'en_diagnostico' || $item['disposicion'] == 'en_reparacion' || $item['disposicion'] == 'en_control') {
                                            $badgeClass = 'badge-proceso';
                                        } elseif ($item['disposicion'] == 'pendiente') {
                                            $badgeClass = 'badge-pendiente';
                                        }

                                        echo '<div class="activity-item">';
                                        echo '<div class="d-flex justify-content-between align-items-center">';
                                        echo '<div>';
                                        echo '<strong>' . htmlspecialchars($item['codigo_g']) . '</strong> - ';
                                        echo htmlspecialchars($item['producto']) . ' ' . htmlspecialchars($item['marca']);
                                        echo '<br><small class="text-muted">' . htmlspecialchars($item['fecha_modificacion']) . '</small>';
                                        echo '</div>';
                                        echo '<span class="badge ' . $badgeClass . '">' . htmlspecialchars($item['disposicion']) . '</span>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p class="text-muted">No hay movimientos recientes</p>';
                                }
                            } catch (PDOException $e) {
                                echo '<p class="text-danger">Error al cargar movimientos</p>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="recent-activity">
                            <h5 class="mb-3">Últimas Solicitudes de Alistamiento</h5>
                            <?php
                            try {
                                $sqlRecentAlistamiento = "SELECT sa.id, sa.cliente, sa.estado, sa.fecha_solicitud
                                    FROM solicitudes_alistamiento sa
                                    ORDER BY sa.fecha_solicitud DESC
                                    LIMIT 5";

                                $stmtRecentAlistamiento = $connect->query($sqlRecentAlistamiento);
                                $recentAlistamiento = $stmtRecentAlistamiento->fetchAll(PDO::FETCH_ASSOC);

                                if (count($recentAlistamiento) > 0) {
                                    foreach ($recentAlistamiento as $solicitud) {
                                        $badgeClass = 'badge-disponible';
                                        if ($solicitud['estado'] == 'pendiente') {
                                            $badgeClass = 'badge-pendiente';
                                        } elseif ($solicitud['estado'] == 'en_proceso') {
                                            $badgeClass = 'badge-proceso';
                                        }

                                        echo '<div class="activity-item">';
                                        echo '<div class="d-flex justify-content-between align-items-center">';
                                        echo '<div>';
                                        echo '<strong>Solicitud #' . htmlspecialchars($solicitud['id']) . '</strong> - ';
                                        echo htmlspecialchars($solicitud['cliente']);
                                        echo '<br><small class="text-muted">' . htmlspecialchars($solicitud['fecha_solicitud']) . '</small>';
                                        echo '</div>';
                                        echo '<span class="badge ' . $badgeClass . '">' . htmlspecialchars($solicitud['estado']) . '</span>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p class="text-muted">No hay solicitudes recientes</p>';
                                }
                            } catch (PDOException $e) {
                                echo '<p class="text-danger">Error al cargar solicitudes</p>';
                            }
                            ?>
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
    <script src="../assets/js/loader.js"></script>
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
</body>
</html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>
