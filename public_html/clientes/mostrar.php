<?php
ob_start();
    session_start();
    if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2,3,4,5,6,7])){
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
                        <a class="navbar-brand" href="#"> Clientes </a>
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
                <?php
                require '../../config/ctconex.php';
                // Obtener el idsede del usuario actual (interno, no manipulable desde URL)
                $userIdSede = null;
                try {
                    $sqlUserInfo = "SELECT idsede FROM usuarios WHERE id = :id LIMIT 1";
                    $stmtUserInfo = $connect->prepare($sqlUserInfo);
                    $stmtUserInfo->execute([':id' => $_SESSION['id']]);
                    $userData = $stmtUserInfo->fetch(PDO::FETCH_ASSOC);
                    if ($userData) {
                        $userIdSede = !empty($userData['idsede']) ? trim($userData['idsede']) : null;
                    }
                } catch (PDOException $e) {
                    error_log("Error al obtener idsede del usuario: " . $e->getMessage());
                }
                // Determinar el título según la sede del usuario
                $tituloSede = "Clientes recientes";
                $descripcionSede = "Nuevos clientes recientes añadidos";
                if ($userIdSede === "Todo") {
                    $tituloSede = "Todos los clientes";
                    $descripcionSede =  "Vista completa de todos los clientes del sistema" . "<strong style='color: #28a745;'>" . " Acceso Completo" ."</strong>";
                } elseif (!empty($userIdSede)) {
                    $tituloSede = "Clientes de sede: " . htmlspecialchars($userIdSede);
                    $descripcionSede = "Clientes registrados en la sede " . "<strong style='color: blue;' >" . htmlspecialchars($userIdSede)  . "</strong>";
                }
                ?>
                <div class="row ">
                    <div class="col-lg-12 col-md-12">
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title"><?php echo $tituloSede; ?></h4>
                                <p class="category"><?php echo $descripcionSede; ?></p>
                            </div>
                            <br>
                            <?php if (!empty($userIdSede)): ?>
                            <a href="../clientes/nuevo.php" class="btn btn-danger text-white">Nuevo cliente</a>
                            <a href="../clientes/importar.php" class="btn btn-success text-white ml-2">Subir archivo Excel</a>
                            <?php endif; ?>
                            <br>
                            <div class="card-content table-responsive">
                                <?php
                                // Si no tiene sede asignada, mostrar mensaje de advertencia
                                if (empty($userIdSede)) {
                                    echo '<div class="alert alert-warning" role="alert">';
                                    echo '<strong>Sin acceso!</strong> No tienes una sede asignada. Por favor contacta al administrador.';
                                    echo '</div>';
                                } else {
                                    // Construir query según el idsede del usuario (NO desde URL)
                                    if ($userIdSede === "Todo") {
                                        // Usuario con acceso completo - mostrar todos los clientes
                                        $sentencia = $connect->prepare("SELECT * FROM clientes ORDER BY nomcli DESC;");
                                        $sentencia->execute();
                                    } else {
                                        // Usuario con sede específica - filtrar por su sede
                                        $sentencia = $connect->prepare("SELECT * FROM clientes WHERE idsede = :sede ORDER BY nomcli DESC;");
                                        $sentencia->execute([':sede' => $userIdSede]);
                                    }
                                    $data = array();
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
                                            <th>Nombres</th>
                                            <th>Apellidos</th>
                                            <th>Celular</th>
                                            <th>Correo</th>
                                            <th>Tienda</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($data as $g):?>
                                        <tr>
                                            <td><?php echo  $g->nomcli; ?></td>
                                            <td><?php echo  $g->apecli; ?></td>
                                            <td><?php echo  $g->celu; ?></td>
                                            <td><?php echo  $g->correo; ?></td>
                                            <td><?php echo  $g->idsede; ?></td>
                                            <td><?php    if($g->estad =='Activo')  { ?>
                                                <span class="badge badge-success">Activo</span>
                                                <?php  }   else {?>
                                                <span class="badge badge-danger">Inactivo</span>
                                                <?php  } ?>
                                            </td>
                                            <td>
                                                <?php    if($g->estad =='Activo')  { ?>
                                                <a class="btn btn-warning text-white"
                                                    href="../clientes/actualizar.php?id=<?php echo  $g->idclie; ?>"><i
                                                        class='material-icons' data-toggle='tooltip'
                                                        title='editar'>edit</i></a>
                                                <a class="btn btn-danger text-white"
                                                    href="../clientes/eliminar.php?id=<?php echo  $g->idclie; ?>"><i
                                                        class='material-icons' data-toggle='tooltip'
                                                        title='cancelar'>cancel</i></a>
                                                <a class="btn btn-primary text-white"
                                                    href="../clientes/informacion.php?id=<?php echo  $g->idclie; ?>"><i
                                                        class='material-icons' data-toggle='tooltip'
                                                        title='crear'>info</i></a>
                                                <?php  }   else {?>
                                                <a class="btn btn-warning text-white"
                                                    href="../clientes/actualizar.php?id=<?php echo  $g->idclie; ?>"><i
                                                        class='material-icons' data-toggle='tooltip'
                                                        title='editar'>edit</i></a>
                                                <?php  } ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else:?>
                                <!-- Warning Alert -->
                                <div class="alert alert-warning" role="alert">
                                    <?php
                                    if ($userIdSede === "Todo") {
                                        echo "No hay clientes registrados en el sistema.";
                                    } else {
                                        echo "No se encontraron clientes para tu sede: " . htmlspecialchars($userIdSede) . "Comienza registado el primer cliente ";
                                    }
                                    ?>
                                </div>
                                <?php endif; ?>
                                <?php } // Fin del else de verificación de sede ?>
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
</body>
</html>
<?php }else{ 
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>