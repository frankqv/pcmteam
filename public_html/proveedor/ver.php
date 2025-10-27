<?php
ob_start();
session_start();
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2,5, 6,7])){
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
?>
<?php if(isset($_SESSION['id'])) { ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ver Proveedor - PCMARKETTEAM</title>
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
            <nav class="navbar navbar-expand-lg" style="background:#fa6b6bff;">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                        <span class="material-icons">arrow_back_ios</span>
                    </button>
                    <a class="navbar-brand" href="#"> Detalles del Proveedor </a>
                </div>
            </nav>
        </div>
        <div class="main-content">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Proveedor: <?php echo htmlspecialchars($proveedor->nombre); ?></h4>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4">ID PROVEEDOR <br/><small>Fundamenta importar desde Excel</small> </dt>
                                <dd class="col-sm-8"><?php echo htmlspecialchars($proveedor->id); ?></dd>
                                <dt class="col-sm-4">Nomenclatura</dt>
                                <dd class="col-sm-8"><?php echo htmlspecialchars($proveedor->nomenclatura); ?></dd>
                                <dt class="col-sm-4">Nombre</dt>
                                <dd class="col-sm-8"><?php echo htmlspecialchars($proveedor->nombre); ?></dd>
                                <dt class="col-sm-4">Celular</dt>
                                <dd class="col-sm-8"><?php echo htmlspecialchars($proveedor->celu); ?></dd>
                                <dt class="col-sm-4">Correo</dt>
                                <dd class="col-sm-8"><?php echo htmlspecialchars($proveedor->correo); ?></dd>
                                <dt class="col-sm-4">Dirección</dt>
                                <dd class="col-sm-8"><?php echo htmlspecialchars($proveedor->dire); ?></dd>
                                <dt class="col-sm-4">Estado</dt>
                                <dd class="col-sm-8">
                                    <?php if($proveedor->privado == 1) { ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php } else { ?>
                                        <span class="badge badge-danger">Inactivo</span>
                                    <?php } ?>
                                </dd>
                            </dl>
                            <a href="mostrar.php" class="btn btn-secondary">Volver a la lista</a>
                            <a href="editar.php?id=<?php echo $proveedor->id; ?>" class="btn btn-warning text-white ml-2">Editar</a>
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
