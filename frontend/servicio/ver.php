<!-- Ver.php -->
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

                        <a class="navbar-brand" href="#"> Servicio Y Pedidos </a>

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
                                <li class="breadcrumb-item"><a href="../servicio/mostrar.php">Pedidos </a></li>
                                <li class="breadcrumb-item active" aria-current="page">Informacion</li>
                            </ol>
                        </nav>
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Servicios recientes</h4>
                                <p class="category">Informacion del servicio reciente añadidos el dia de hoy</p>
                            </div>

                            <div class="card-content table-responsive">

                                <?php
                    require '../../backend/bd/ctconex.php'; 
                    $id = $_GET['id'];
                    $sentencia = $connect->prepare("SELECT servicio.canc,servicio.meto,servicio.idservc, plan.prec,plan.idplan, plan.foto, plan.nompla, servicio.ini, servicio.fin, clientes.idclie, clientes.numid, clientes.nomcli, clientes.apecli, clientes.naci, clientes.celu, clientes.correo, clientes.dircli, clientes.ciucli, clientes.idsede, servicio.estod, servicio.fere, servicio.servtxt, servicio.servfoto, servicio.responsable FROM servicio INNER JOIN plan ON servicio.idplan = plan.idplan INNER JOIN clientes ON servicio.idclie = clientes.idclie WHERE servicio.idservc= '$id';");
      
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
                                    <!--<div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="form-group">
                                    <center><img src="../../backend/img/subidas/<?php echo  $f->foto  ; ?>"
                                            height="150"></center>

                                </div>
                            </div>
                        </div>-->
                                    <br>
                                    <!-- Datos Nuevos -->
                                    <center>
                                        <h4><b>Informacion</b> del Cliente</h4>
                                    </center>
                                    <hr>
                                    <div class="row">

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Clientes<span class="text-danger">*</span></label>
                                                <select class="form-control" disabled name="txtcli">
                                                    <option value="<?php echo  $f->idclie  ; ?>">
                                                        <?php echo  $f->nomcli  ; ?> <?php echo $f->apecli; ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Celular<span class="text-danger">*</span></label>
                                                <select class="form-control" disabled name="txtcli">
                                                    <option value="<?php echo  $f->idclie  ; ?>">
                                                        <?php echo $f->celu; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Correo<span class="text-danger">*</span></label>
                                                <select class="form-control" disabled name="txtcli">
                                                    <option value="<?php echo  $f->idclie  ; ?>">
                                                        <?php echo $f->correo ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Dirección<span class="text-danger">*</span></label>
                                                <select class="form-control" disabled name="txtcli">
                                                    <option value="<?php echo  $f->idclie  ; ?>">
                                                        <?php echo $f->dircli; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Cuidad<span class="text-danger">*</span></label>
                                                <select class="form-control" disabled name="txtcli">
                                                    <option value="<?php echo  $f->idclie  ; ?>">
                                                        <?php echo $f->ciucli; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">ID SEDE<span class="text-danger">*</span></label>
                                                <select class="form-control" disabled name="txtcli">
                                                    <option value="<?php echo  $f->idclie  ; ?>">
                                                        <?php echo $f->idsede; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Datos nuevos -->
                                    <center>
                                        <h4><b>Descripcion</b></h4>
                                    </center>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">


                                            <div class="form-group">
                                                <label>Detalles del Tecnico<span class="text-danger">*</span></label>
                                                <div class="alert alert-info" style="text-align: justify; ">
                                                    <?php echo htmlspecialchars($f->servtxt); ?>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-lg-12">
                                                <label for="email">Foto del estado de llegada<span
                                                        class="text-danger">*</span></label>
                                                <div class="form-group">
                                                    <center><img
                                                            src="../../backend/img/subidas/<?php echo  $f->servfoto  ; ?>"
                                                            height="150"></center>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Responsable<span class="text-danger">*</span></label>
                                                <div class="alert alert-info" style="text-align: justify; ">
                                                    <?php echo htmlspecialchars($f->responsable); ?>
                                                </div>
                                            </div>




                                        </div>
                                    </div>
                                    <!-- Datos nuevos -->
                                    <center>
                                        <h4>Detalles del <b>Servicio</b></h4>
                                    </center>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Servicio Adquirido<span
                                                        class="text-danger">*</span></label>
                                                <select disabled class="form-control" name="txtpln">
                                                    <option value="<?php echo  $f->idplan  ; ?>">
                                                        <?php echo  $f->nompla  ; ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Clientes<span class="text-danger">*</span></label>
                                                <select class="form-control" disabled name="txtcli">
                                                    <option value="<?php echo  $f->idclie  ; ?>">
                                                        <?php echo  $f->nomcli  ; ?> <?php echo $f->apecli; ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Estado del servicio<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" disabled name="txtesta">
                                                    <option value="<?php echo  $f->estod  ; ?>">
                                                        <?php echo  $f->estod  ; ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Fecha de inicio<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" value="<?php echo  $f->ini  ; ?>"
                                                    class="form-control" name="txtini" disabled
                                                    placeholder="Nombre del producto">
                                                <input type="hidden" value="<?php echo  $f->idservc  ; ?>"
                                                    name="txtidc">
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Fecha de Entrega<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" id="fechad" value="<?php echo  $f->fin  ; ?>"
                                                    class="form-control" name="txtfin" disabled
                                                    placeholder="Nombre del producto">

                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Metodo de pago<span
                                                        class="text-danger">*</span></label>
                                                <select disabled class="form-control" name="txtmeto">
                                                    <option value="<?php echo  $f->meto  ; ?>">
                                                        <?php echo  $f->meto  ; ?></option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Decripcion del tecnico -->
                                    <div class="row">

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Precio del servicio<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    value="<?php echo  $f->prec  ; ?>" disabled name="txtcanc"
                                                    placeholder="">

                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Cancelo<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    value="<?php echo  $f->canc  ; ?>" disabled name="txtcanc"
                                                    placeholder="">

                                            </div>
                                        </div>


                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Debe<span class="text-danger">*</span></label>
                                                <?php 
                                      $number1 =  $f->prec;
                                      $number2 =  $f->canc;
                                      $result  = $number1  -  $number2;
                                      ?>
                                                <input type="text" value="<?php echo $result; ?>" class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    disabled name="txtcanc" placeholder="">

                                            </div>
                                        </div>

                                    </div>

                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">

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

    <script>
    function calcularMultiplicacion() {
        // Obtener los valores ingresados por el usuario
        var numero1 = parseInt(document.getElementById('numero1').value);
        var numero2 = parseInt(document.getElementById('numero2').value);

        // Calcular el resultado de la multiplicación
        var resultado = numero1 * numero2;

        // Mostrar el resultado en el tercer campo de entrada
        document.getElementById('resultado').value = resultado;
    }
    </script>
</body>

</html>

<?php }else{ 
    header('../error404.php');
 } ?>
<?php ob_end_flush(); ?>