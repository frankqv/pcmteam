<!---- /pedidos_ruta/mostrar.php -->
<?php
ob_start();
    session_start();

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 4, 7])){
    header('location: ../error404.php');
    exit;
}
include_once '../../config/ctconex.php';

// Obtener técnicos (roles 5, 6, 7)
$tecnicos = $connect->query("SELECT id, nombre FROM usuarios WHERE rol IN ('5','6','7')")->fetchAll(PDO::FETCH_ASSOC);

// Consulta principal de pedidos
$sql = "
SELECT o.*, c.nomcli, c.apecli, c.idsede, s.servtxt, s.ini, s.fin, u.nombre as tecnico
FROM orders o
LEFT JOIN clientes c ON o.user_cli = c.idclie
LEFT JOIN servicio s ON s.idclie = c.idclie
LEFT JOIN usuarios u ON o.user_id = u.id
ORDER BY o.idord DESC
";
$pedidos = $connect->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Mapeo de estados
function estadoPedido($status) {
    $map = [
        'Pagado' => 'Finalizado',
        'Pendiente' => 'Alistamiento',
        'Aceptado' => 'Enviado',
    ];
    return $map[$status] ?? $status;
}
?>
<?php if(isset($_SESSION['id'])) { ?>

<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Pedidos en Ruta - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <!-- Data Tables -->
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
</head>

<body>
    <div class="wrapper">
        <!-- layouts nav.php  |  Sidebar -->
        <div class="body-overlay"></div>
        <?php    include_once '../layouts/nav.php';  include_once '../layouts/menu_data.php';    ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> Pedidos en Ruta </a>
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
                                        <li>
                                            <a href="../cuenta/perfil.php">Mi perfil</a>
                                        </li>
                                        <li>
                                            <a href="../cuenta/salir.php">Salir</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        <!-- Page Content  -->
        <div id="content">
            <div class='pre-loader'>
                <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
            </div>
            <div class="container mt-4">
                <h2>Pedidos en Ruta</h2>
                <a href="../pedidos_ruta/nuevo_pedido.php" class="btn btn-info text-white ml-2">Generar Alistamiento</a>
                <br>
                <table class="table table-bordered table-hover" id="tablaPedidos">
                    <thead class="thead-dark">
                        <tr>
                            <th>Técnico Responsable</th>
                            <th>Fecha de Inicio</th>
                            <th>Número de Pedido</th>
                            <th>Descripción</th>
                            <th>Nombre Cliente</th>
                            <th>Tienda</th>
                            <th>Saldo</th>
                            <th>Ver Fotos</th>
                            <th>Estado</th>
                            <th>Imprimir</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($pedidos as $pedido): ?>
                        <tr>
                            <td>
                                <form method="post" action="asignar_tecnico.php" style="display:inline-block;">
                                    <input type="hidden" name="idord" value="<?php echo $pedido['idord']; ?>">
                                    <select name="tecnico_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                        <option value="">Seleccionar</option>
                                        <?php foreach($tecnicos as $tec): ?>
                                            <option value="<?php echo $tec['id']; ?>" <?php if($pedido['user_id'] == $tec['id']) echo 'selected'; ?>><?php echo htmlspecialchars($tec['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo htmlspecialchars($pedido['ini'] ?? $pedido['placed_on']); ?></td>
                            <td>#<?php echo 1000 + (int)$pedido['idord']; ?></td>
                            <td><?php echo htmlspecialchars($pedido['servtxt'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($pedido['nomcli'] . ' ' . $pedido['apecli']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['idsede']); ?></td>
                            <td>$<?php echo number_format($pedido['total_price'], 0, ',', '.'); ?></td>
                            <td><a href="ver.php?id=<?php echo $pedido['idord']; ?>" class="btn btn-info btn-sm">Ver</a></td>
                            <td><?php echo estadoPedido($pedido['payment_status']); ?></td>
                            <td><a href="factura.php?id=<?php echo $pedido['idord']; ?>" class="btn btn-primary btn-sm" target="_blank">Imprimir</a> <hr/>
                            <a href="delicado.php?id=<?php echo $pedido['idord']; ?>" class="btn btn-danger btn-sm">Guia Envio</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!---  Contenido de MAIN -->

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery-3.3.1.min.js"></script>
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
        <script type="text/javascript" src="../assets/js/example.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="../assets/js/chart/Chart.js"></script>
        <script>
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);
        </script>
        <script>
        $(document).ready(function() {
            $('#tablaPedidos').DataTable();
        });
        </script>

</body>

</html>


<?php }else{ 
header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>