<?php
ob_start();
     session_start();
    
    if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 4, 5, 6, 7])){
    header('../error404.php');
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

    <!-- Data Tables -->

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

                        <a class="navbar-brand" href="#"> Servicio </a>

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
                                <li class="breadcrumb-item"><a href="../servicio/mostrar.php">Servicios </a></li>
                                <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
                            </ol>
                        </nav>
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Servicios recientes</h4>
                                <p class="category">Actualizar servicio reciente añadidos el dia de hoy</p>
                            </div>

                            <div class="card-content table-responsive">
                                <div class="alert alert-warning">
                                    <strong>Estimado usuario!</strong> Los campos remarcados con <span
                                        class="text-danger">*</span> son necesarios.

                                </div>
                                <?php
 require '../../backend/bd/ctconex.php'; 
 $id = $_GET['id'];
 $sentencia = $connect->prepare("SELECT servicio.meto,servicio.idservc, plan.idplan, plan.foto, plan.nompla, servicio.ini, servicio.fin, clientes.idclie, clientes.numid, clientes.nomcli, clientes.apecli, clientes.naci, clientes.celu, clientes.correo, servicio.estod, servicio.fere servicio.servtxt, servicio.foto, servicio.responsable FROM servicio INNER JOIN plan ON servicio.idplan = plan.idplan INNER JOIN clientes ON servicio.idclie = clientes.idclie  WHERE servicio.idservc= '$id';");
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
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Servicio Tecnico<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtpln">
                                                    <option value="<?php echo  $f->idplan  ; ?>">
                                                        <?php echo  $f->nompla  ; ?></option>
                                                    <option value="">----------Seleccione------------</option>
                                                    <?php
           require '../../backend/bd/ctconex.php';
            $stmt = $connect->prepare("SELECT * FROM plan where estp='Activo' order by idplan desc");
            $stmt->execute();
            while($row=$stmt->fetch(PDO::FETCH_ASSOC))
                {
                    extract($row);
                    ?>
                                                    <option value="<?php echo $idplan; ?>"><?php echo $nompla; ?>
                                                    </option>
                                                    <?php
                }
        ?>
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Clientes<span class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtcli">
                                                    <option value="<?php echo  $f->idclie  ; ?>">
                                                        <?php echo  $f->nomcli  ; ?> <?php echo $f->apecli; ?></option>
                                                    <option value="">----------Seleccione------------</option>
                                                    <?php
           require '../../backend/bd/ctconex.php';
            $stmt = $connect->prepare("SELECT * FROM clientes where estad='Activo' order by idclie desc");
            $stmt->execute();
            while($row=$stmt->fetch(PDO::FETCH_ASSOC))
                {
                    extract($row);
                    ?>
                                                    <option value="<?php echo $idclie; ?>"><?php echo $nomcli; ?>
                                                        <?php echo $apecli; ?></option>
                                                    <?php
                }
        ?>
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Estado del servicio<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtesta">
                                                    <option value="<?php echo  $f->estod  ; ?>">
                                                        <?php echo  $f->estod  ; ?> </option>
                                                    <option value="">----------Seleccione------------</option>
                                                    <option value="Activo">Activo</option>
                                                    <option value="Inactivo">Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Fecha de inicio<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" value="<?php echo  $f->ini  ; ?>"
                                                    class="form-control" name="txtini" required
                                                    placeholder="Nombre del producto">
                                                <input type="hidden" value="<?php echo  $f->idservc  ; ?>"
                                                    name="txtidc">
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Fecha de vencimiento<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" id="fechad" value="<?php echo  $f->fin  ; ?>"
                                                    class="form-control" name="txtfin" required
                                                    placeholder="Nombre del producto">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="email">Metodo de pago<span
                                                        class="text-danger">*</span></label>
                                                <select required class="form-control" name="txtmeto">
                                                    <option value="<?php echo  $f->meto  ; ?>">
                                                        <?php echo  $f->meto  ; ?></option>
                                                    <option value="">----------Seleccione------------</option>
                                                    <option value="Transferencia">Transferencia</option>
                                                    <option value="Nequi">Nequi</option>
                                                    <option value="Efectivo">Efectivo</option>
                                                    <option value="Tarjeta">Tarjeta</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button name='stupdserv' class="btn btn-success text-white">Guardar</button>
                                            <a class="btn btn-danger text-white"
                                                href="../servicio/mostrar.php">Cancelar</a>
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
    <?php
    include_once '../../backend/php/st_updservic.php'
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

    <script>
    // Obtener fecha actual
    let fecha = new Date();
    // Agregar 3 días falta
    fecha.setDate(fecha.getDate() + 0);
    // Obtener cadena en formato yyyy-mm-dd, eliminando zona y hora
    let fechaMin = fecha.toISOString().split('T')[0];
    // Asignar valor mínimo
    document.querySelector('#fechad').min = fechaMin;
    </script>
</body>

</html>





<?php }else{ 
    header('../error404.php');
 } ?>
<?php ob_end_flush(); ?>