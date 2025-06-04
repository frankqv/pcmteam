<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2 && $_SESSION['rol'] != 3 && $_SESSION['rol'] != 4 && $_SESSION['rol'] != 5 && $_SESSION['rol'] != 6)) {
    header('../error404.php');
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

    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
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
            <!-- Listado de opciones del sidebar -->
        </nav>

        <!-- Page Content  -->
        <div id="content">
            <div class="top-navbar">
                <!-- Barra de navegación superior -->
            </div>
            <div class="main-content">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <!-- Breadcrumb -->
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel
                                        administrativo</a></li>
                                <li class="breadcrumb-item"><a href="../usuario/mostrar.php">Usuarios</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
                            </ol>
                        </nav>

                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Usuarios recientes</h4>
                                <p class="category">Actualizar usuarios recientes añadidos el día de hoy</p>
                            </div>
                            <div class="card-content table-responsive">
                                <!-- Formulario para actualizar usuario -->
                                <?php
                                require '../../backend/bd/ctconex.php';
                                $id = $_GET['id'];
                                $sentencia = $connect->prepare("SELECT * FROM usuarios WHERE usuarios.id= '$id';");
                                $sentencia->execute();
                                $data = array();
                                if ($sentencia) {
                                    while ($r = $sentencia->fetchObject()) {
                                        $data[] = $r;
                                    }
                                }
                                ?>
                                <?php if (count($data) > 0) : ?>
                                <?php foreach ($data as $f) : ?>
                                <form enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Nombre del usuario<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" value="<?php echo $f->nombre; ?>"
                                                    class="form-control" name="txtnaame" required
                                                    placeholder="Nombre de la categoría">
                                                <input type="hidden" value="<?php echo $f->id; ?>" name="txtidc">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Usuario<span class="text-danger">*</span></label>
                                                <input type="text" value="<?php echo $f->usuario; ?>"
                                                    class="form-control" name="txtuse" required
                                                    placeholder="Nombre de la categoría">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Correo<span class="text-danger">*</span></label>
                                                <input type="email" value="<?php echo $f->correo; ?>"
                                                    class="form-control" name="txtmail" required
                                                    placeholder="Nombre de la categoría">
                                            </div>
                                        </div>
                                        <!-- Nuevo campo de selección para el rol -->
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="rol">Rol<span class="text-danger">*</span></label>
                                                <select class="form-control" name="txtrol" required>
                                                    <option value="1" <?php echo ($f->rol == 1) ? 'selected' : ''; ?>>
                                                        Rol #1: Administrador</option>
                                                    <option value="2" <?php echo ($f->rol == 2) ? 'selected' : ''; ?>>
                                                        Rol #2: Usuario Generio</option>
                                                    <option value="3" <?php echo ($f->rol == 3) ?  'selected' : '';?>>
                                                        Rol #3: Contable</option>
                                                    <option value="4" <?php echo ($f->rol == 4) ? 'selected' : ''; ?>>
                                                        Rol #4: Comercial</option>
                                                    <option value="5" <?php echo ($f->rol == 5) ?  'selected' : '';?>>
                                                        Rol #4: Jefe Técnico</option>
                                                    <option value="6" <?php echo ($f->rol == 6) ?  'selected' : '';?>>
                                                        Rol #6: Técnico</option>
                                                    <option value="7" <?php echo ($f->rol == 7) ?  'selected' : '';?>>
                                                        Rol #7: Bodega</option>
                                                    <!-- Agrega más opciones de roles según sea necesario -->
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Cambiar foto * opcion subir archivo-->
                                        
                                        foto
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button name='stupduser' class="btn btn-success text-white">Guardar</button>
                                            <a class="btn btn-danger text-white"
                                                href="../usuario/mostrar.php">Cancelar</a>
                                        </div>
                                    </div>
                                </form>
                                <?php endforeach; ?>
                                <?php else : ?>
                                <div class="alert alert-warning" role="alert">
                                    ¡No se encontró ningún dato!
                                </div>
                                <?php endif; ?>
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
    include_once '../../backend/php/st_upduser.php';
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


<?php
} else {
    header('../error404.php');
}
?>
<?php ob_end_flush(); ?>