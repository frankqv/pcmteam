<!-- frontend/producto/nuevo.php -->
<?php
ob_start();
session_start();

// Activa el reporte de errores temporalmente para debug:
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 4, 5, 6, 7])){
    header('location: ../error404.php');
    exit;
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
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
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
                                <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
                            </ol>
                        </nav>
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Nuevo Producto</h4>
                                <p class="category">Agregar información completa del producto</p>
                            </div>
                            <div class="card-content table-responsive">
                                <div class="alert alert-warning">
                                    <strong>Estimado usuario!</strong> Los campos remarcados con <span
                                        class="text-danger">*</span> son necesarios.
                                </div>
                                <form enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <!-- Imagen del producto -->
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <center>
                                                    <label for="foto">Imagen del producto</label>
                                                    <input type="file" class="form-control" name="foto" accept="image/*" required>
                                                </center>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    
                                    <!-- Información básica del producto -->
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <?php 
                                                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                                function generate_string($input, $strength = 16) {
                                                    $input_length = strlen($input);
                                                    $random_string = '';
                                                    for($i = 0; $i < $strength; $i++) {
                                                        $random_character = $input[mt_rand(0, $input_length - 1)];
                                                        $random_string .= $random_character;
                                                    }
                                                    return $random_string;
                                                }
                                            ?>
                                            <div class="form-group">
                                                <label for="txtcode">Código del producto<span class="text-danger">*</span></label>
                                                <input type="text" maxlength="14"
                                                    value="<?php echo generate_string($permitted_chars, 14); ?>"
                                                    class="form-control" name="txtcode" required
                                                    placeholder="Código del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtnampr">Nombre del producto<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="txtnampr" required
                                                    placeholder="Nombre del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtcate">Categoria del producto<span class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtcate">
                                                    <option value="">----------Seleccione------------</option>
                                                    <?php
                                                        require '../../backend/bd/ctconex.php';
                                                        $stmt = $connect->prepare("SELECT * FROM categoria where estado='Activo' order by categoria.idcate desc");
                                                        $stmt->execute();
                                                        while($row=$stmt->fetch(PDO::FETCH_ASSOC))
                                                        {
                                                            extract($row);
                                                            ?>
                                                            <option value="<?php echo $idcate; ?>"><?php echo $nomca; ?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Información adicional del producto -->
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtserial">Serial<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="txtserial" required 
                                                    placeholder="Serial del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtmarca">Marca<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="txtmarca" required 
                                                    placeholder="Marca del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtgrado">Grado<span class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtgrado">
                                                    <option value="">----------Seleccione------------</option>
                                                    <option value="A">Grado A</option>
                                                    <option value="B">Grado B</option>
                                                    <option value="C">Grado C</option>
                                                    <option value="0">Grado 0</option>
                                                    <option value="">Por definirse Vacio</option>
                                                    <option value="#N/D">#N/D</option>
                                                    <option value="SCRAP">SCRAP</option>
                                                    <option value="Nuevo">Nuevo</option>
                                                    <option value="Reacondicionado">Reacondicionado</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Configuración del Equipo -->
                                    <center>
                                        <h4><b>Configuración del</b> Equipo</h4>
                                        <hr />
                                    </center>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtprcpro">Procesador<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="txtprcpro" required 
                                                    placeholder="Procesador del equipo">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtram">RAM<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="txtram" required 
                                                    placeholder="Memoria RAM">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtdisco">Disco<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="txtdisco" required 
                                                    placeholder="Disco duro">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="txtpntpro">Dimensiones<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="txtpntpro" required 
                                                    placeholder="Dimensiones del equipo">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="txttarpro">Tarjeta Gráfica<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="txttarpro" required 
                                                    placeholder="Tarjeta gráfica">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detalles Comerciales -->
                                    <center>
                                        <h4><b>Detalles</b> Comerciales</h4>
                                        <hr />
                                    </center>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtpre">Precio del producto<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    name="txtpre" required placeholder="Precio del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtstc">Stock del producto<span class="text-danger">*</span></label>
                                                <input type="text" maxlength="4" class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    name="txtstc" required placeholder="Stock del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtvenc">Vencimiento del producto</label>
                                                <input type="date" class="form-control" name="txtvenc"
                                                    placeholder="Fecha de vencimiento">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="txtesta">Estado del producto<span class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtesta">
                                                    <option value="Activo">Activo</option>
                                                    <option value="Inactivo">Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button name='staddprod' class="btn btn-success text-white">Guardar</button>
                                            <a class="btn btn-danger text-white"
                                                href="../producto/mostrar.php">Cancelar</a>
                                        </div>
                                    </div>
                                </form>
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
    <?php include_once '../../backend/php/st_stprodc.php' ?>
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