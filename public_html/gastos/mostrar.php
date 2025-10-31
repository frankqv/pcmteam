<?php
ob_start();
     session_start();
    
    if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4])){
    header('location: ../error404.php');
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
    <title>PCMARKETTEAM</title>
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

        <div class="body-overlay"></div>
        <!-- layouts nav.php  |  Sidebar -->
        <?php    include_once '../layouts/nav.php';  include_once '../layouts/menu_data.php';    ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
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

                        <a class="navbar-brand" href="#"> Gastos </a>

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


            <div class="main-content">

                <div class="row ">
                    <div class="col-lg-12 col-md-12">
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Gastos recientes</h4>
                                <p class="category">Nuevas gastos reciente añadidos el dia de hoy</p>
                            </div>
                            <br>
                            <a href="../gastos/nuevo.php" class="btn btn-danger text-white">Nuevo gasto</a>
                            <br>
                            <div class="card-content table-responsive">
                                <?php
                               require '../../config/ctconex.php'; 
 $sentencia = $connect->prepare("SELECT gastos.idga, gastos.detalle, gastos.total, gastos.metodo_pago, gastos.fecha_resgistro FROM gastos ORDER BY idga DESC;");
 $sentencia->execute();

$data =  array();
if($sentencia){
  while($r = $sentencia->fetchObject()){
    $data[] = $r;
  }
}
   ?>
                                <?php if(count($data)>0):?>
                                <table class="table table-hover" id="example">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Detalle</th>
                                            <th>Método Pago</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($data as $g):
                                            // Decodificar JSON de gastos
                                            $gastos_json = json_decode($g->detalle, true);
                                            $es_json = is_array($gastos_json);
                                        ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y H:i', strtotime($g->fecha_resgistro)); ?></td>
                                            <td>
                                                <?php if ($es_json): ?>
                                                    <strong><?php echo count($gastos_json); ?> gasto(s)</strong>
                                                    <ul class="mb-0 pl-3">
                                                        <?php foreach ($gastos_json as $item): ?>
                                                            <li><?php echo htmlspecialchars($item['descripcion']); ?> - $<?php echo number_format($item['monto'], 0, ',', '.'); ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php else: ?>
                                                    <?php echo htmlspecialchars($g->detalle); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge badge-info"><?php echo htmlspecialchars($g->metodo_pago); ?></span></td>
                                            <td><strong class="text-danger">$<?php echo number_format($g->total, 0, ',', '.'); ?></strong></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="verDetalle(<?php echo $g->idga; ?>)" title="Ver Detalle">
                                                    <i class="material-icons" style="font-size: 16px;">visibility</i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else:?>
                                <!-- Warning Alert -->
                                <div class="alert alert-warning" role="alert">
                                    No se encontró ningún dato!
                                </div>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>


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
    <script src="../assets/js/loader.js"></script>
    <!-- Data Tables -->
    <script type="text/javascript" src="../assets/js/datatable.js"></script>
    <script type="text/javascript" src="../assets/js/datatablebuttons.js"></script>
    <script type="text/javascript" src="../assets/js/jszip.js"></script>
    <script type="text/javascript" src="../assets/js/pdfmake.js"></script>
    <script type="text/javascript" src="../assets/js/vfs_fonts.js"></script>
    <script type="text/javascript" src="../assets/js/buttonshtml5.js"></script>
    <script type="text/javascript" src="../assets/js/buttonsprint.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            language: {
                search: "🔍buscar:"
            }
        });
    });
    </script>

    <script>
    function verDetalle(id) {
        window.location.href = 'ver.php?id=' + id;
    }
    </script>

</body>

</html>





<?php }else{ 
    header('Location: ../error404.php');
 } ?>
<?php ob_end_flush(); ?>