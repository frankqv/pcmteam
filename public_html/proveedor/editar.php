<?php
ob_start();
session_start();
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])){
    header('location: ../error404.php');
    exit;
}
require '../../config/ctconex.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('location: ../error404.php');
    exit;
}
$id = intval($_GET['id']);
$sentencia = $connect->prepare("SELECT * FROM proveedores WHERE id = ? LIMIT 1;");
$sentencia->execute([$id]);
$proveedor = $sentencia->fetchObject();
if(!$proveedor){
    header('location: ../error404.php');
    exit;
}
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomenclatura = trim($_POST['nomenclatura'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $celu = trim($_POST['celu'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $dire = trim($_POST['dire'] ?? '');
    $privado = isset($_POST['privado']) ? 1 : 0;
    $update = $connect->prepare("UPDATE proveedores SET nomenclatura=?, nombre=?, celu=?, correo=?, dire=?, privado=? WHERE id=?");
    $ok = $update->execute([$nomenclatura, $nombre, $celu, $correo, $dire, $privado, $id]);
    if($ok){
        $mensaje = '<div class="alert alert-success">Proveedor actualizado correctamente.</div>';
        // Refrescar datos
        $sentencia = $connect->prepare("SELECT * FROM proveedores WHERE id = ? LIMIT 1;");
        $sentencia->execute([$id]);
        $proveedor = $sentencia->fetchObject();
    } else {
        $mensaje = '<div class="alert alert-danger">Error al actualizar el proveedor.</div>';
    }
}
?>
<?php if(isset($_SESSION['id'])) { ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Editar Proveedor - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
</head>
<body>
<div class="wrapper">
    <div class="body-overlay"></div>
    <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
        </div>
        <?php renderMenu($menu); ?>
    </nav>
    <div id="content">
        <div class="top-navbar">
            <nav class="navbar navbar-expand-lg" style="background: #fa6b6bff">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                        <span class="material-icons">arrow_back_ios</span>
                    </button>
                    <a class="navbar-brand" href="#"> Editar Proveedor </a>
                </div>
            </nav>
        </div>
        <div class="main-content">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card mt-4">
                        <div class="card-header bg-warning text-white">
                            <h4 class="mb-0">Editar Proveedor: <?php echo htmlspecialchars($proveedor->nombre); ?></h4>
                        </div>
                        <div class="card-body">
                            <?php echo $mensaje; ?>
                            <form method="POST">
                                <div class="form-group mb-3">
                                    <label>Nomenclatura</label>
                                    <input type="text" name="nomenclatura" class="form-control" value="<?php echo htmlspecialchars($proveedor->nomenclatura); ?>" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($proveedor->nombre); ?>" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Celular</label>
                                    <input type="text" name="celu" class="form-control" value="<?php echo htmlspecialchars($proveedor->celu); ?>">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Correo</label>
                                    <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($proveedor->correo); ?>">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Dirección</label>
                                    <input type="text" name="dire" class="form-control" value="<?php echo htmlspecialchars($proveedor->dire); ?>">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Estado</label><br>
                                    <input type="checkbox" name="privado" value="1" <?php if($proveedor->privado == 1) echo 'checked'; ?>> Activo
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-warning text-white">Guardar Cambios</button>
                                    <a href="../proveedor/mostrar.php" class="btn btn-secondary ml-2">Cancelar /Volver a la lista</a>
                                    <!-- <a href="ver.php?id= ?php echo $proveedor->id; ?>" class="btn btn-secondary ml-2">Cancelar /Volver a la lista</a> -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
<script src="../assets/js/loader.js"></script>
</body>
</html>
<?php } else { header('Location: ../error404.php'); } ?>
<?php ob_end_flush(); ?>