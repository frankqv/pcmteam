<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])) {
    header('location: ../error404.php');
}
?>
<?php if (isset($_SESSION['id'])) { ?>

<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Proveedores - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">

    <!-- Data Tables -->
    <link rel="stylesheet" type="text/css" href="../../backend/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/font.css">
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
</head>

<body>
    <div class="wrapper">

        <!-- layouts nav.php  |  Sidebar -->
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php';
        include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>

        <!-- Page Content  -->
        <div id="content">
            <div class='pre-loader'>
                <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
            </div>
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>

                        <a class="navbar-brand" href="#"> Proveedores </a>

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
                                        <img src="../../backend/img/reere.png">
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

            <div class="main-content">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Proveedores</h4>
                                <p class="category">Lista de proveedores registrados en el sistema</p>
                            </div>
                            <br>
                            <a href="../proveedor/nuevo.php" class="btn btn-danger text-white">Nuevo Proveedor</a>
                            <a href="../proveedor/importar.php" class="btn btn-success text-white ml-2">Importar
                                Excel</a>
                            <br>
                            <div class="card-content table-responsive">
                                <?php
                                require '../../backend/bd/ctconex.php';
                                $sentencia = $connect->prepare("SELECT * FROM proveedores ORDER BY nombre ASC;");
                                $sentencia->execute();
                                $data = array();
                                if ($sentencia) {
                                    while ($r = $sentencia->fetchObject()) {
                                        $data[] = $r;
                                    }
                                }
                                ?>
                                <?php if (count($data) > 0): ?>
                                    <table class="table table-hover" id="example">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>Nomenclatura</th>
                                                <th>Nombre</th>
                                                <th>Teléfono</th>
                                                <th>Correo</th>
                                                <th>Dirección</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $d): ?>
                                                <tr>
                                                    <td><?php echo $d->nomenclatura; ?></td>
                                                    <td><?php echo $d->nombre; ?></td>
                                                    <td><?php echo $d->celu; ?></td>
                                                    <td><?php echo $d->correo; ?></td>
                                                    <td><?php echo $d->dire; ?></td>
                                                    <td>
                                                        <?php if ($d->privado == 1) { ?>
                                                            <span class="badge badge-success">Activo</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-info btn-sm"
                                                            href="../proveedor/ver.php?id=<?php echo $d->id; ?>">
                                                            <i class='material-icons' data-toggle='tooltip'
                                                                title='Ver detalles'>visibility</i>
                                                        </a>
                                                        <a class="btn btn-warning btn-sm"
                                                            href="../proveedor/editar.php?id=<?php echo $d->id; ?>">
                                                            <i class='material-icons' data-toggle='tooltip'
                                                                title='Editar'>edit</i>
                                                        </a>
                                                        <?php if ($d->privado == 1): ?>
                                                            <a class="btn btn-danger btn-sm"
                                                                href="../proveedor/desactivar.php?id=<?php echo $d->id; ?>">
                                                                <i class='material-icons' data-toggle='tooltip'
                                                                    title='Desactivar'>block</i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a class="btn btn-success btn-sm"
                                                                href="../proveedor/activar.php?id=<?php echo $d->id; ?>">
                                                                <i class='material-icons' data-toggle='tooltip'
                                                                    title='Activar'>check_circle</i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-warning" role="alert">
                                        No se encontraron proveedores registrados!
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!---  Contenido de MAIN -->

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
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
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="../../backend/js/chart/Chart.js"></script>
        <script>
            google.charts.load('current', {
                'packages': ['corechart']
            });
            google.charts.setOnLoadCallback(drawChart);
        </script>

        <script>
            $(document).ready(function () {
                if (!$.fn.DataTable.isDataTable('#example')) {
                    $('#example').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                        }
                    });
                }
            });
        </script>
</body>
</html>


<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>