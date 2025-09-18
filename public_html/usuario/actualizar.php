<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2 && $_SESSION['rol'] != 3 && $_SESSION['rol'] != 4 && $_SESSION['rol'] != 5 && $_SESSION['rol'] != 6)) {
    header('../error404.php');
}

require '../../config/ctconex.php';

// BACKEND - Procesar formulario SOLO si viene por POST
if (isset($_POST['stupduser'])) {
    $id = $_POST['txtidc'];
    $nombre = $_POST['txtnaame'];
    $usuario = $_POST['txtuse'];
    $correo = $_POST['txtmail'];
    $rol = $_POST['txtrol'];
    $sede = $_POST['txtsede']; 
    $cumple = $_POST['txtcumple'];
    
    try {
        $query = "UPDATE usuarios SET nombre=:nombre, usuario=:usuario, correo=:correo, rol=:rol, idsede=:sede, cumple=:cumple WHERE id=:id LIMIT 1";
        $statement = $connect->prepare($query);
        $data = [
            ':nombre' => $nombre,
            ':usuario' => $usuario,
            ':correo' => $correo,
            ':rol' => $rol,
            ':sede' => $sede,
            ':cumple' => $cumple,
            ':id' => $id
        ];
        $query_execute = $statement->execute($data);
        if ($query_execute) {
            // Redireccionar inmediatamente después de actualizar
            header("Location: ../usuario/mostrar.php?success=1");
            exit();
        } else {
            $error_message = "Error al actualizar el usuario";
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Obtener datos del usuario SOLO si viene por GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sentencia = $connect->prepare("SELECT * FROM usuarios WHERE usuarios.id= ? LIMIT 1");
    $sentencia->execute([$id]);
    $data = array();
    if ($sentencia) {
        while ($r = $sentencia->fetchObject()) {
            $data[] = $r;
        }
    }
} else {
    header("Location: ../usuario/mostrar.php");
    exit();
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
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <!-- Sidebar   -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
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
                                <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel administrativo</a></li>
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
                                <!-- Mostrar mensaje de error si existe -->
                                <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (count($data) > 0) : ?>
                                <?php foreach ($data as $f) : ?>
                                <form enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Nombre del usuario<span class="text-danger">*</span></label>
                                                <input type="text" value="<?php echo $f->nombre; ?>" class="form-control" name="txtnaame" required placeholder="Nombre del usuario">
                                                <input type="hidden" value="<?php echo $f->id; ?>" name="txtidc">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Usuario<span class="text-danger">*</span></label>
                                                <input type="text" value="<?php echo $f->usuario; ?>" class="form-control" name="txtuse" required placeholder="Nombre de usuario">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email">Correo<span class="text-danger">*</span></label>
                                                <input type="email" value="<?php echo $f->correo; ?>" class="form-control" name="txtmail" required placeholder="Correo electrónico">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="rol">Rol<span class="text-danger">*</span></label>
                                                <select class="form-control" name="txtrol" required>
                                                    <option value="1" <?php echo ($f->rol == 1) ? 'selected' : ''; ?>>Rol #1: Administrador</option>
                                                    <option value="0" <?php echo ($f->rol == 0) ? 'selected' : ''; ?>>Rol #2: Usuario Genérico</option>
                                                    <option value="3" <?php echo ($f->rol == 3) ? 'selected' : ''; ?>>Rol #3: Contable</option>
                                                    <option value="4" <?php echo ($f->rol == 4) ? 'selected' : ''; ?>>Rol #4: Comercial</option>
                                                    <option value="5" <?php echo ($f->rol == 5) ? 'selected' : ''; ?>>Rol #5: Jefe Técnico</option>
                                                    <option value="6" <?php echo ($f->rol == 6) ? 'selected' : ''; ?>>Rol #6: Técnico</option>
                                                    <option value="7" <?php echo ($f->rol == 7) ? 'selected' : ''; ?>>Rol #7: Bodega</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="sede">Sede<span class="text-danger">*</span></label>
                                                <select class="form-control" name="txtsede" required>
                                                    <option value="">Seleccionar Sede...</option>
                                                    <?php
                                                    $query_sedes = "SELECT DISTINCT idsede FROM usuarios WHERE idsede IS NOT NULL AND idsede != '' ORDER BY idsede";
                                                    $result_sedes = $connect->prepare($query_sedes);
                                                    $result_sedes->execute();
                                                    $sedes = $result_sedes->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($sedes as $sede) {
                                                        $selected = (isset($f->idsede) && $f->idsede == $sede['idsede']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($sede['idsede']) . '" ' . $selected . '>';
                                                        echo htmlspecialchars($sede['idsede']);
                                                        echo '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="cumple">Fecha de Cumpleaños</label>
                                                <input type="date" value="<?php echo $f->cumple; ?>" class="form-control" name="txtcumple">
                                                <small class="form-text text-muted">Opcional: Fecha de nacimiento del usuario</small>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button name='stupduser' class="btn btn-success text-white">Guardar</button>
                                            <a class="btn btn-danger text-white" href="../usuario/mostrar.php">Cancelar</a>
                                        </div>
                                    </div>
                                </form>
                                <?php endforeach; ?>
                                <?php else : ?>
                                <div class="alert alert-warning" role="alert">¡No se encontró ningún dato!</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- JavaScript -->
        <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery-3.3.1.min.js"></script>
        <script src="../assets/js/sweetalert.js"></script>
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
</body>
</html>
<?php
} else {
    header('../error404.php');
}
?>
<?php ob_end_flush(); ?>