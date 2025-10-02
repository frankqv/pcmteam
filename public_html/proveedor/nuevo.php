<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2,5, 6,7])) {
    header('location: ../error404.php');
}
?>
<?php if (isset($_SESSION['id'])) { ?>
    <!DOCTYPE html>
    <html lang="es">
   <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <title>Nuevo Proveedor - PCMARKETTEAM</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <!----css3---->
        <link rel="stylesheet" href="../assets/css/custom.css">
        <link rel="stylesheet" href="../assets/css/loader.css">
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
            <!-- Sidebar -->
            <?php include_once '../layouts/nav.php';
            include_once '../layouts/menu_data.php'; ?>
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
                </div>
                <?php renderMenu($menu); ?>
            </nav>
           <!-- Page Content -->
            <div id="content">
                <div class='pre-loader'>
                    <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
                </div>
                <div class="top-navbar">
                    <nav class="navbar navbar-expand-lg" style="background:#fa6b6bff;">
                        <div class="container-fluid">
                            <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                                <span class="material-icons">arrow_back_ios</span>
                            </button>
                            <a class="navbar-brand" href="#"> Nuevo Proveedor </a>
                            <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse"
                                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
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
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="card" style="min-height: 485px">
                                <div class="card-header card-header-text">
                                    <h4 class="card-title">Registrar Nuevo Proveedor</h4>
                                    <p class="category">Complete los datos del nuevo proveedor</p>
                                </div>
                                <div class="card-content">
                                    <?php
                                    require '../../config/ctconex.php';
                                    if (isset($_POST['add'])) {
                                        $nombre = $_POST['nombre'];
                                        $telefono = $_POST['telefono'];
                                        $correo = $_POST['correo'];
                                        $direccion = $_POST['direccion'];
                                        $cuiprov = $_POST['cuiprov'];
                                        // Validar campos obligatorios
                                        if (empty($nombre) || empty($telefono) || empty($correo)) {
                                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            Los campos Nombre, Celular y Correo son obligatorios
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>';
                                        } else {
                                            try {
                                                $stmt = $connect->prepare("INSERT INTO proveedores (privado, nombre, celu, correo, dire, cuiprov) VALUES (1, ?, ?, ?, ?, ?)");
                                                $stmt->execute([$nombre, $telefono, $correo, $direccion, $cuiprov]);
                                                // Obtener el ID del proveedor recién insertado
                                                $lastId = $connect->lastInsertId();
                                                // Generar y actualizar la nomenclatura
                                                $nomenclatura = 'PRV' . str_pad($lastId, 3, '0', STR_PAD_LEFT);
                                                $stmt = $connect->prepare("UPDATE proveedores SET nomenclatura = ? WHERE id = ?");
                                                $stmt->execute([$nomenclatura, $lastId]);
                                                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                Proveedor registrado correctamente
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>';
                                                echo '<meta http-equiv="refresh" content="2;url=mostrar.php">';
                                            } catch (PDOException $e) {
                                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                Error al registrar el proveedor: ' . $e->getMessage() . '
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>';
                                            }
                                        }
                                    }
                                    ?>
                                    <form method="POST" autocomplete="off">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="bmd-label-floating">Nombre del Proveedor*</label>
                                                    <input type="text" name="nombre" class="form-control" required maxlength="30">
                                                </div>
                                            </div>
                                            <!-- input telefono -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="bmd-label-floating">Celular*</label>
                                                    <input
                                                        type="text"
                                                        id="telefono"
                                                        name="telefono"
                                                        class="form-control"
                                                        required
                                                        inputmode="numeric"
                                                        pattern="[0-9]{3} [0-9]{4} [0-9]{3}"
                                                        placeholder="301 234 567"
                                                        maxlength="12"
                                                        autocomplete="off">
                                                </div>
                                                <script>
                                                    const telefonoInput = document.getElementById('telefono');
                                                    telefonoInput.addEventListener('input', function() {
                                                        let value = this.value.replace(/\D/g, ''); // 1. Quitar no-dígitos
                                                        value = value.slice(0, 10); // 2. Limitar a 10 dígitos
                                                        let formatted = value;
                                                        // 3. Insertar espacios: "XXX XXXX XXX"
                                                        if (value.length > 3) {
                                                            if (value.length <= 7) {
                                                                formatted = `${value.slice(0,3)} ${value.slice(3)}`;
                                                            } else {
                                                                formatted = `${value.slice(0,3)} ${value.slice(3,7)} ${value.slice(7)}`;
                                                            }
                                                        }
                                                        this.value = formatted;
                                                    });
                                                </script>
                                            </div>
                                            <!--- input telefono -->
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="bmd-label-floating">Correo Electrónico*</label>
                                                    <input type="email" name="correo" class="form-control" required maxlength="30">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="bmd-label-floating">Dirección</label>
                                                    <input type="text" name="direccion" class="form-control" maxlength="250">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="bmd-label-floating">NIT/RUT</label>
                                                    <input type="text" name="cuiprov" class="form-control" maxlength="30">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="submit" name="add" class="btn btn-primary pull-right">
                                                    <i class="material-icons">add</i> Registrar Proveedor
                                                </button>
                                                <a href="mostrar.php" class="btn btn-danger">
                                                    <i class="material-icons">cancel</i> Cancelar
                                                </a>
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
        <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
        <script type="text/javascript" src="../assets/js/loader.js"></script>
    </body>
    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>