<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 4, 5, 6, 7])) {
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
        <link rel="icon" type="image/png" href="../../backend/img/favicon.webp" />
    </head>
    <body>
        <div class="wrapper">
            <div class="body-overlay"></div>
            <!-- layouts nav.php  |  Sidebar -->
            <?php include_once '../layouts/nav.php';
            include_once '../layouts/menu_data.php'; ?>
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3><img src="../../backend/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
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
                            <a class="navbar-brand" href="#"> Categorias </a>
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
                                            <img src="../../backend/img/reere.webp">
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
                                    <li class="breadcrumb-item"><a href="../categoria/mostrar.php">Categorias </a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
                                </ol>
                            </nav>
                            <div class="card" style="min-height: 485px">
                                <div class="card-header card-header-text">
                                    <h4 class="card-title">Categorias recientes</h4>
                                    <p class="category">Nuevas categorias reciente añadidos el dia de hoy</p>
                                </div>
                                <div class="card-content table-responsive">
                                    <div class="alert alert-warning">
                                        <strong>Estimado usuario!</strong> Los campos remarcados con <span
                                            class="text-danger">*</span> son necesarios.
                                    </div>
                                    <form enctype="multipart/form-data" method="POST" autocomplete="off">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="email">Nombre de la categoria<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nombres" name="txtnaame"
                                                        required placeholder="Nombre de la categoria">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="email">Estado de la categoria<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" required name="txtesta">
                                                        <option value="Activo">Activo</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button name='staddcate' class="btn btn-success text-white">Guardar</button>
                                                <a class="btn btn-danger text-white"
                                                    href="../categoria/mostrar.php">Cancelar</a>
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
        <?php
        include_once '../../backend/php/st_stcate.php'
            ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#sidebarCollapse').on('click', function () {
                    $('#sidebar').toggleClass('active');
                    $('#content').toggleClass('active');
                });
                $('.more-button,.body-overlay').on('click', function () {
                    $('#sidebar,.body-overlay').toggleClass('show-nav');
                });
            });
        </script>
        <script src="../../backend/js/loader.js"></script>
    </body>
    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>