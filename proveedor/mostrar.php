<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])) {
    header('location: ../error404.php');
}
// Incluir conexión a la base de datos
require_once '../../config/ctconex.php';
// Obtener información del usuario
$userInfo = null;
if (isset($_SESSION['id'])) {
    try {
        $stmt = $connect->prepare("SELECT id, nombre, usuario, correo, foto, idsede FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['id']]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error obteniendo información del usuario: " . $e->getMessage());
        $userInfo = [
            'nombre' => 'PCUsuario',
            'usuario' => 'pc_usuario',
            'correo' => 'correo@pcmarkett.co',
            'foto' => 'reere.webp',
            'idsede' => 'Sede sin definir'
        ];
    }
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
    <title>Proveedores - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <!-- Data Tables -->
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <!-- TODOS los iconos de Google material icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
</head>
<body>
    <div class="wrapper">
        <!-- layouts nav.php  |  Sidebar -->
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php';
        include_once '../layouts/menu_data.php'; ?>
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
<!-- begin:: top-navbar -->
<div class="top-navbar">
    <nav class="navbar navbar-expand-lg" style="background:rgb(250, 107, 107);">
        <div class="container-fluid">
        <!-- Botón Sidebar -->
        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
        <span class="material-icons">arrow_back_ios</span>
        </button>
        <!-- Título dinámico -->
        <?php
        $titulo = "";
        switch ($_SESSION['rol']) {
        case 1:
        $titulo = "ADMINISTRADOR";
        break;
        case 2:
        $titulo = "DEFAULT";
        break;
        case 3:
        $titulo = "CONTABLE";
        break;
        case 4:
        $titulo = "COMERCIAL";
        break;
        case 5:
        $titulo = "JEFE TÉCNICO";
        break;
        case 6:
        $titulo = "TÉCNICO";
        break;
        case 7:
        $titulo = "BODEGA";
        break;
        default:
        $titulo = $userInfo['nombre'] ?? 'USUARIO';
        break;
        }
        ?>
        <!-- Branding -->
        <a class="navbar-brand" href="#" style="color: #fff; font-weight: bold;">
        <i class="fas fa-tools" style="margin-right: 8px; color: #f39c12;"></i>
        <b>PROVEEDOR | USUARIO </b><?php echo htmlspecialchars($titulo); ?> 
        </a>
        <!-- Menú derecho (usuario) -->
        <ul class="nav navbar-nav ml-auto">
        <li class="dropdown nav-item active">
        <a href="#" class="nav-link" data-toggle="dropdown">
        <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
            alt="Foto de perfil"
            style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
        </a>
        <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
        <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
        <li><?php echo htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></li>
        <li><?php echo htmlspecialchars($userInfo['correo'] ?? 'correo@ejemplo.com'); ?></li>
        <li>
            <?php echo htmlspecialchars(trim($userInfo['idsede'] ?? '') !== '' ? $userInfo['idsede'] : 'Sede sin definir'); ?>
        </li>
        <li class="mt-2">
            <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi
                perfil</a>
        </li>
        </ul>
        </li>
        </ul>
        </div>
        <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
            data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="material-icons">more_vert</span>
        </button>
    </nav>
</div>
<!--- end:: top_navbar -->
            <div class="main-content">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title material-symbols-outlined"> delivery_truck_bolt Proveedores</h4>
                                <p class="category">Lista de proveedores registrados en el sistema</p>
                            </div>
                            <br>
                            <a href="../proveedor/nuevo.php" class="btn btn-danger text-white">Nuevo Proveedor</a>
                            <a href="../proveedor/importar.php" class="btn btn-success text-white ml-2">Importar
                                Excel</a>
                            <br>
                            <div class="card-content table-responsive">
                                <?php
                                // La conexión ya está incluida arriba
                                $sentencia = $connect->prepare("SELECT * FROM proveedores ORDER BY nombre ASC;");
                                $sentencia->execute();
                                $data = array();
                                if ($sentencia) {
                                    while ($r = $sentencia->fetchObject()) {
                                        $data[] = $r;
                                    }
                                }
                                ?>
                                <?php if (count($data) > 0): ?>
                                    <table class="table table-hover" id="example">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>Nomenclatura</th>
                                                <th>Nombre</th>
                                                <th>Celular</th>
                                                <th>Correo</th>
                                                <th>Dirección</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $d): ?>
                                                <tr>
                                                    <td><?php echo $d->nomenclatura; ?></td>
                                                    <td><?php echo $d->nombre; ?></td>
                                                    <td><?php echo $d->celu; ?></td>
                                                    <td><?php echo $d->correo; ?></td>
                                                    <td><?php echo $d->dire; ?></td>
                                                    <td>
                                                        <?php if ($d->privado == 1) { ?>
                                                            <span class="badge badge-success">Activo</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-info btn-sm"
                                                            href="../proveedor/ver.php?id=<?php echo $d->id; ?>">
                                                            <i class='material-icons' data-toggle='tooltip'
                                                                title='Ver detalles'>visibility</i>
                                                        </a>
                                                        <a class="btn btn-danger btn-sm"
                                                            href="../proveedor/editar.php?id=<?php echo $d->id; ?>">
                                                            <i class='material-icons' data-toggle='tooltip'
                                                                title='Editar'>edit</i>
                                                        </a>
                                                        <?php if ($d->privado == 1): ?>
                                                            <a class="btn btn-danger btn-sm"
                                                                href="../proveedor/desactivar.php?id=<?php echo $d->id; ?>">
                                                                <i class='material-icons' data-toggle='tooltip'
                                                                    title='Desactivar'>block</i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a class="btn btn-success btn-sm"
                                                                href="../proveedor/activar.php?id=<?php echo $d->id; ?>">
                                                                <i class='material-icons' data-toggle='tooltip'
                                                                    title='Activar'>check_circle</i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-warning" role="alert">
                                        No se encontraron proveedores registrados!
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!---  Contenido de MAIN -->
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
        <script src="../assets/js/loader.js"></script>
        <!-- Data Tables -->
        <script type="text/javascript" src="../assets/js/datatable.js"></script>
        <script type="text/javascript" src="../assets/js/datatablebuttons.js"></script>
        <script type="text/javascript" src="../assets/js/jszip.js"></script>
        <script type="text/javascript" src="../assets/js/pdfmake.js"></script>
        <script type="text/javascript" src="../assets/js/vfs_fonts.js"></script>
        <script type="text/javascript" src="../assets/js/buttonshtml5.js"></script>
        <script type="text/javascript" src="../assets/js/buttonsprint.js"></script>
        <script type="text/javascript" src="../assets/js/example.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="../assets/js/chart/Chart.js"></script>
        <script>
            google.charts.load('current', {
                'packages': ['corechart']
            });
            google.charts.setOnLoadCallback(drawChart);
        </script>
        <script>
            $(document).ready(function () {
                if (!$.fn.DataTable.isDataTable('#example')) {
                    $('#example').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                        }
                    });
                }
            });
        </script>
</body>
</html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>