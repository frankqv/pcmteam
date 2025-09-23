<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 5])) {
    header('location: ../error404.php');
    exit();
}
require_once '../../config/ctconex.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reporte de Técnicos - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
</head>
<body>
<div class="wrapper">
    <div class="body-overlay"></div>
    <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
        </div>
        <?php renderMenu($menu); ?>
    </nav>
    <div id="content">
        <div class="top-navbar">
            <nav class="navbar navbar-expand-lg" style="background:#2980b9;">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                        <span class="material-icons">arrow_back_ios</span>
                    </button>
                    <a class="navbar-brand" href="#" style="color:#fff;"><b>REPORTE DE TÉCNICOS</b></a>
                </div>
            </nav>
        </div>
        <div class="main-content">
            <div class="card">
                <div class="card-header"><h4>Productividad por Técnico (últimos 30 días)</h4></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Técnico</th>
                                <th>Diagnósticos</th>
                                <th>Eléctrico</th>
                                <th>Estético</th>
                                <th>Calidad</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            try {
                                $sql = "
                                    SELECT u.id, u.nombre,
                                        SUM(CASE WHEN t.tabla = 'bodega_diagnosticos' THEN t.cnt ELSE 0 END) AS diagnosticos,
                                        SUM(CASE WHEN t.tabla = 'bodega_electrico' THEN t.cnt ELSE 0 END) AS electrico,
                                        SUM(CASE WHEN t.tabla = 'bodega_estetico' THEN t.cnt ELSE 0 END) AS estetico,
                                        SUM(CASE WHEN t.tabla = 'bodega_control_calidad' THEN t.cnt ELSE 0 END) AS calidad
                                    FROM usuarios u
                                    LEFT JOIN (
                                        SELECT tecnico_id, 'bodega_diagnosticos' AS tabla, COUNT(*) cnt
                                        FROM bodega_diagnosticos
                                        WHERE fecha_diagnostico >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                        GROUP BY tecnico_id
                                        UNION ALL
                                        SELECT tecnico_id, 'bodega_electrico' AS tabla, COUNT(*) cnt
                                        FROM bodega_electrico
                                        WHERE fecha_proceso >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                        GROUP BY tecnico_id
                                        UNION ALL
                                        SELECT tecnico_id, 'bodega_estetico' AS tabla, COUNT(*) cnt
                                        FROM bodega_estetico
                                        WHERE fecha_proceso >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                        GROUP BY tecnico_id
                                        UNION ALL
                                        SELECT tecnico_id, 'bodega_control_calidad' AS tabla, COUNT(*) cnt
                                        FROM bodega_control_calidad
                                        WHERE fecha_control >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                        GROUP BY tecnico_id
                                    ) t ON t.tecnico_id = u.id
                                    WHERE u.rol IN ('5','6','7')
                                    GROUP BY u.id, u.nombre
                                    ORDER BY u.nombre ASC";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                                    echo '<td>' . intval($row['diagnosticos']) . '</td>';
                                    echo '<td>' . intval($row['electrico']) . '</td>';
                                    echo '<td>' . intval($row['estetico']) . '</td>';
                                    echo '<td>' . intval($row['calidad']) . '</td>';
                                    echo '</tr>';
                                }
                            } catch (Exception $e) {
                                echo '<tr><td colspan="5" class="text-danger">Error al generar reporte</td></tr>';
                                error_log('Reporte tecnicos: ' . $e->getMessage());
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
<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
<script src="../assets/js/loader.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>


