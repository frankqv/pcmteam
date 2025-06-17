<?php
ob_start();
     session_start();
    
    if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 4, 5, 6, 7])){
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
    <link rel="stylesheet" href="../../backend/css/custom.css">
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
        <!-- layouts nav.php  |  Sidebar -->
        <?php    include_once '../layouts/nav.php';  include_once '../layouts/menu_data.php';    ?>
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

                        <a class="navbar-brand" href="#"> Productos </a>

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

                <div class="row ">
                    <div class="col-lg-12 col-md-12">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel
                                        administrativo</a></li>
                                <li class="breadcrumb-item"><a href="../producto/mostrar.php">Productos </a></li>
                                <li class="breadcrumb-item active" aria-current="page">Informacion</li>
                            </ol>
                        </nav>
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Productos recientes</h4>
                                <p class="category">Informacion del producto reciente añadidos el dia de hoy</p>
                            </div>

                            <div class="card-content table-responsive">

                                <?php
 require '../../backend/bd/ctconex.php'; 
 $id = $_GET['id'];
 $sentencia = $connect->prepare("SELECT producto.idprod, producto.codba, producto.nomprd, categoria.idcate, categoria.nomca, producto.precio, producto.stock, producto.foto, producto.venci, producto.esta, producto.fere FROM producto INNER JOIN categoria ON producto.idcate = categoria.idcate WHERE producto.idprod= '$id';");
 $sentencia->execute();

$data =  array();
if($sentencia){
  while($r = $sentencia->fetchObject()){
    $data[] = $r;
  }
}
   ?>
                                <?php if(count($data)>0):?>
                                <?php foreach($data as $f):?>
                                <form enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <center><img src="../../backend/img/subidas/<?php echo  $f->foto  ; ?>"
                                                        height="150"></center>

                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">

                                            <div class="form-group">
                                                <label for="email">Código del producto<span
                                                        class="text-danger">*</span></label>
                                                <input readonly type="text" maxlength="14"
                                                    value="<?php echo  $f->codba  ; ?>" class="form-control"
                                                    name="txtcode" placeholder="Código del producto">
                                                <input type="hidden" value="<?php echo  $f->idprod  ; ?>" name="txtidc">
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Nombre del producto<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo  $f->nomprd  ; ?>" name="txtnampr" readonly
                                                    placeholder="Nombre del producto">

                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Categoria del producto<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" readonly name="txtcate">
                                                    <option value="<?php echo  $f->idcate  ; ?>">
                                                        <?php echo  $f->nomca  ; ?></option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Precio del producto<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" value="<?php echo  $f->precio  ; ?>"
                                                    class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    name="txtpre" readonly placeholder="Precio del producto">

                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Stock del producto<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" value="<?php echo  $f->stock  ; ?>"
                                                    class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    name="txtstc" readonly placeholder="Precio del producto">

                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Vencimiento del producto<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" value="<?php echo  $f->venci  ; ?>"
                                                    class="form-control" name="txtvenc" readonly
                                                    placeholder="Precio del producto">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="email">Estado del producto<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" readonly name="txtesta">
                                                    <option value="<?php echo  $f->esta  ; ?>">
                                                        <?php echo  $f->esta  ; ?></option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">

                                            <a class="btn btn-danger text-white"
                                                href="../producto/mostrar.php">Cancelar</a>
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
    <script src="../../backend/js/jquery-3.3.1.slim.min.js"></script>
    <script src="../../backend/js/popper.min.js"></script>
    <script src="../../backend/js/bootstrap.min.js"></script>
    <script src="../../backend/js/jquery-3.3.1.min.js"></script>
    <script src="../../backend/js/sweetalert.js"></script>


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