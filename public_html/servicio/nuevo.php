<!-- servicio/nuevo-php -->
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
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">

    <!-- Data Tables -->

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
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel
                                        administrativo</a></li>
                                <li class="breadcrumb-item"><a href="../servicio/mostrar.php">Servicios </a></li>
                                <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
                            </ol>
                        </nav>
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Servicios recientes</h4>
                                <p class="category">Nuevos servicios reciente añadidos el dia de hoy</p>
                            </div>
                            <div class="card-content table-responsive">
                                <div class="alert alert-warning">
                                    <strong>Estimado usuario!</strong> Los campos remarcados con <span
                                        class="text-danger">*</span> son necesarios.
                                    <br>
                                    <strong>Al registrar un servicio en el apartado clientes debes añadir uno nuevo si
                                        es primera vez</strong>
                                </div>
                                <center>
                                    <h4><b>Informacion</b> del Cliente</h4>
                                </center>
                                <hr />
                                <form enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Servicio Tecnico<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" id="plan" required name="txtpln">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4" style="display:none;">
                                            <div class="form-group">
                                                <label for="email">Precio<span class="text-danger">*</span></label>
                                                <select class="form-control" id="total" name="txtprec">

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Clientes <span class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtcli">
                                                    <option value="">Seleccione un cliente</option>
                                                    <?php
                                                        require '../../config/ctconex.php';
                                                        $stmt = $connect->prepare("SELECT * FROM clientes where estad='Activo' order by idclie desc");
                                                        $stmt->execute();
                                                        while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                                                            extract($row);
                                                            echo "<option value='$idclie'>$nomcli $apecli</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Estado del servicio<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtesta">
                                                    <option value="Activo">Activo</option>
                                                    <!-- <option value="Inactivo">Inactivo</option> -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div>
                                                <label for="email">COMPRO EN LA SEDE<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtesta">
                                                    <option value="Principal">Principal</option>
                                                    <option value="Medellin">Medellin</option>
                                                    <option value="Cucuta">Cucuta</option>
                                                    <option value="Unilago">Unilago</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-6">
                                            <label for="Comercial">Comercial<span class="text-danger">*</span></label>
                                            <select class="form-control" required name="Comercial">
                                                <option value="">----------Seleccione Comercial------------</option>
                                                <?php
                                                        // Consulta para obtener usuarios con rol 5 y 6
                                                        $stmt_comerciales = $connect->prepare("SELECT id, nombre, rol FROM usuarios WHERE rol IN (4) AND estado = '1' ORDER BY nombre ASC");
                                                        $stmt_comerciales->execute();
                                                        while($row_resp = $stmt_comerciales->fetch(PDO::FETCH_ASSOC)) {
                                                            $rol_texto = ($row_resp['rol'] == 4) ? 'Comercial' : 'Soporte Técnico';
                                                            ?>
                                                <option value="<?php echo htmlspecialchars($row_resp['nombre']); ?>">
                                                    <?php echo htmlspecialchars($row_resp['nombre']); ?> -
                                                    <?php echo $rol_texto; ?>
                                                </option>
                                                <?php
                                                        }
                                                    ?>
                                            </select>
                                        </div>

                                    </div>
                                    <center>
                                        <h4><b>Descripcion</b></h4>
                                        <hr>
                                    </center>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <label for="responsable">Responsable<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" required name="responsable">
                                                <option value="">----------Seleccione Responsable------------</option>
                                                <?php
                                                        // Consulta para obtener usuarios con rol 5 y 6
                                                        $stmt_responsables = $connect->prepare("SELECT id, nombre, rol FROM usuarios WHERE rol IN (5, 6, 7) AND estado = '1' ORDER BY nombre ASC");
                                                        $stmt_responsables->execute();
                                                        while($row_resp = $stmt_responsables->fetch(PDO::FETCH_ASSOC)) {
                                                            $rol_texto = ($row_resp['rol'] == 5) ? 'Técnico' : 'Soporte Técnico';
                                                            ?>
                                                <option value="<?php echo htmlspecialchars($row_resp['nombre']); ?>">
                                                    <?php echo htmlspecialchars($row_resp['nombre']); ?> -
                                                    <?php echo $rol_texto; ?>
                                                </option>
                                                <?php
                                                        }
                                                    ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="servtxt">Observación del Técnico<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="servtxt" required
                                                    placeholder="Ingrese observación del servicio">
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="email">Foto del servicio<span
                                                        class="text-danger">*</span></label>
                                                <input type="file" id="imagen" name="foto" onchange="readURL(this);"
                                                    data-toggle="tooltip" required>
                                                <img id="blah" src="../assets/img/noimage.webp" width="100"
                                                    heigth="100" alt="your image" style="max-width:90px;" />
                                            </div>
                                        </div>
                                    </div>
                                    <center>
                                        <h4><b>Detalles</b> del servicio</h4>
                                    </center>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Fecha de inicio<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" id="fechaActual" class="form-control" name="txtini"
                                                    required placeholder="Nombre del producto">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Metodo de pago<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtmeto">
                                                    <option value="">----------Seleccione------------</option>
                                                    <option value="Transferencia">Transferencia</option>
                                                    <option value="Nequi_Daviplata">Nequi_Daviplata</option>
                                                    <option value="Efectivo">Efectivo</option>
                                                    <option value="Tarjeta">Tarjeta</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Cancelo<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    name="txtcanc" required placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button name='staddserv' class="btn btn-success text-white">Guardar</button>
                                            <a class="btn btn-danger text-white"
                                                href="../servicio/mostrar.php">Cancelar</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                                          <a href="../plan/mostrar.php" class="btn btn-success text-white">Nueva Categoria de Servicio Tecnico</a>
                        </div>
                    </div>
                </div>

            </div> <!-- main -->


            <!-- Contenido -->

        </div>
    </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/sweetalert.js"></script>
    <?php
    include_once '../../backend/php/st_stservic.php'
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
    <script src="../assets/js/loader.js"></script>
    <script type="text/javascript">
    window.onload = function() {
        var fecha = new Date(); //Fecha actual
        var mes = fecha.getMonth() + 1; //obteniendo mes
        var dia = fecha.getDate(); //obteniendo dia
        var ano = fecha.getFullYear(); //obteniendo año
        if (dia < 10)
            dia = '0' + dia; //agrega cero si el menor de 10
        if (mes < 10)
            mes = '0' + mes //agrega cero si el menor de 10
        document.getElementById('fechaActual').value = ano + "-" + mes + "-" + dia;
    }
    </script>
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
    <script src="../assets/js/plan.js"></script>



</body>

</html>

<?php }else{ 
    header('Location: ../error404.php');
 } ?>
<?php ob_end_flush(); ?>