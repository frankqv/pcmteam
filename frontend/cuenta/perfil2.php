<?php
ob_start();
     session_start();
    
    if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 2){
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
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../../backend/css/custom2.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">


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

        <div class="body-overlay"></div>
        <!-- Sidebar   -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
            </div>
            <ul class="list-unstyled components">
                <li class="active">
                    <a href="../cliente/escritorio.php" class="dashboard"><i
                            class="material-icons">dashboard</i><span>Panel Cliente</span></a>
                </li>


                <li class="dropdown">
                    <a href="#pageSubmenu2" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="material-icons">dataset</i><span>Planes</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu2">
                        <li>
                            <a href="../cliente/plan.php">Mostrar</a>
                        </li>
                    </ul>
                </li>

                <li class="">
                    <a href="../cliente/servicio.php"><i
                            class="material-icons">view_timeline</i><span>Servicio</span></a>
                </li>
        </nav>
        <!-- Sidebar   -->


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

                        <a class="navbar-brand" href="#"> Perfil </a>

                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
                            data-toggle="collapse" data-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="material-icons">more_vert</span>
                        </button>

                        <div class="collapse navbar-collapse d-lg-block d-xl-block d-sm-none d-md-none d-none"
                            id="navbarSupportedContent">
                            <ul class="nav navbar-nav ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="../cuenta/perfil.php">
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

                <div class="row ">
                    <div class="col-lg-12 col-md-12">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../cliente/escritorio.php">Panel Cliente</a></li>
                                <li class="breadcrumb-item"><a href="../cliente/escritorio.php">Perfil </a></li>
                                <li class="breadcrumb-item active" aria-current="page">Actualizar </li>
                            </ol>
                        </nav>
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Perfil del cliente</h4>
                                <p class="category">actualizar </p>
                            </div>

                            <div class="card-content table-responsive">

                                <?php
 require '../../backend/bd/ctconex.php'; 
 $id = $_SESSION['id'];
 $sentencia = $connect->prepare("SELECT * FROM usuarios  WHERE usuarios.id= '$id';");
 $sentencia->execute();

$data =  array();
if($sentencia){
  while($r = $sentencia->fetchObject()){
    $data[] = $r;
  }
}
   ?>
                                <?php if(count($data)>0):?>
                                <?php foreach($data as $d):?>
                                <form enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Nombre del cliente<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" value="<?php echo  $d->nombre; ?>"
                                                    class="form-control" id="nombres" name="txtnaame" required
                                                    placeholder="Nombre de la categoria">
                                                <input type="hidden" value="<?php echo  $d->id; ?>" name="txtidadm">
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Estado del cliente<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtesta">
                                                    <?php    if($d->estado =='1')  { ?>
                                                    <option value="<?php echo  $d->estado; ?>">activo</option>

                                                    <?php  }   else {?>

                                                    <?php  } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Usuario<span class="text-danger">*</span></label>
                                                <input type="text" value="<?php echo  $d->usuario; ?>"
                                                    class="form-control" name="txtusr" required
                                                    placeholder="ejm: asistente">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Correo electronico<span
                                                        class="text-danger">*</span></label>
                                                <input type="email" value="<?php echo  $d->correo; ?>"
                                                    class="form-control" name="txtcorr" required
                                                    placeholder="ejm: asistente">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="email">Cargo<span class="text-danger">*</span></label>


                                                <select class="form-control" required name="txtcarr">
                                                    <?php    if($d->rol =='2')  { ?>
                                                    <option value="<?php echo  $d->rol; ?>">cliente</option>

                                                    <?php  }   else {?>

                                                    <?php  } ?>

                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button name='stupdprof' class="btn btn-success text-white">Guardar</button>
                                            <a class="btn btn-danger text-white"
                                                href="../cuenta/perfil.php">Cancelar</a>
                                        </div>
                                    </div>
                                </form>
                                <?php endforeach; ?>
                                <?php else:?>
                                <div class="alert alert-warning" role="alert">
                                    No se encontró ningún dato!
                                </div>

                                <?php endif; ?>
                            </div>


                        </div>
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Perfil del cliente</h4>
                                <p class="category">actualizar contraseña</p>
                            </div>
                            <div class="card-content table-responsive">
                                <?php
 $id = $_SESSION['id'];
 $sentencia = $connect->prepare("SELECT * FROM usuarios  WHERE usuarios.id= '$id';");
 $sentencia->execute();

$data =  array();
if($sentencia){
  while($r = $sentencia->fetchObject()){
    $data[] = $r;
  }
}
   ?>
                                <?php if(count($data)>0):?>
                                <?php foreach($data as $d):?>


                                <form enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="email">Nueva contraseña<span
                                                        class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="nombres" name="txtpawd"
                                                    required placeholder="">
                                                <input type="hidden" value="<?php echo  $d->id; ?>" name="txtidadm">
                                            </div>
                                        </div>


                                    </div>

                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button name='stupdprofpsd'
                                                class="btn btn-success text-white">Guardar</button>
                                            <a class="btn btn-danger text-white"
                                                href="../cuenta/perfil.php">Cancelar</a>
                                        </div>
                                    </div>
                                </form>


                                <?php endforeach; ?>
                                <?php else:?>
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



    <!--Vamos-->
    <script src="../../backend/js/jquery-3.3.1.slim.min.js"></script>

    <script src="../../backend/js/popper.min.js"></script>

    <script src="../../backend/js/bootstrap.min.js"></script>

    <script src="../../backend/js/jquery-3.3.1.min.js"></script>

    <script src="../../backend/js/sweetalert.js"></script>








    <?php
    include_once '../../backend/php/st_updpro2.php'
?>
    <?php
    include_once '../../backend/php/st_updpropsd2.php'
?>

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


    <script src="../../backend/js/loader.js"></script>


</body>

</html>





<?php }else{ 
    header('Location: ../error404.php');
 } ?>
<?php ob_end_flush(); ?>