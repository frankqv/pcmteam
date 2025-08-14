<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])) {
    header('location: ../error404.php');
    exit;
}
require_once '../../backend/bd/ctconex.php';

$userInfo = null;
if (isset($_SESSION['id'])) {
    try {
        // Aquí uso PDO, ajusta si usas mysqli o tu variable de conexión
        $stmt = $connect->prepare("SELECT id, nombre, usuario, correo, foto, idsede FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['id']]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error obteniendo información del usuario: " . $e->getMessage());
        $userInfo = [
            'nombre' => 'PCUsuario',
            'usuario' => 'pc_usuario',
            'correo' => 'correo@pcmarkett.co',
            'foto' => 'reere.png',
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
    <title>Proveedores - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="../../backend/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/font.css">
    <!-- Google Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap" />
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <!-- Include navigation and sidebar -->
        <?php 
            include_once '../layouts/nav.php'; 
            include_once '../layouts/menu_data.php'; 
        ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>
                    <img src="../../backend/img/favicon.png" class="img-fluid" alt="Logo PCMARKETTEAM">
                    <span>PCMARKETTEAM</span>
                </h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <div class="pre-loader">
                <img class="loading-gif" alt="loading" src="https://i.imgflip.com/9vd6wr.gif" />
            </div>

            <!-- Top Navbar -->
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background:rgb(250, 107, 107);">
                    <div class="container-fluid">
                        <!-- Sidebar Button -->
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>

                        <!-- Dynamic Title -->
                        <?php
                        $rolTitles = [
                            1 => "ADMINISTRADOR",
                            2 => "DEFAULT",
                            3 => "CONTABLE",
                            4 => "COMERCIAL",
                            5 => "JEFE TÉCNICO",
                            6 => "TÉCNICO",
                            7 => "BODEGA"
                        ];
                        $titulo = $rolTitles[$_SESSION['rol']] ?? ($userInfo['nombre'] ?? 'USUARIO');
                        ?>
                        <a class="navbar-brand" href="#" style="color: #fff; font-weight: bold;">
                            <i class="fas fa-tools" style="margin-right: 8px; color: #f39c12;"></i>
                            <b>PROVEEDOR | USUARIO </b><?php echo htmlspecialchars($titulo); ?> 
                        </a>

                        <!-- User Menu -->
                        <ul class="nav navbar-nav ml-auto">
                            <li class="dropdown nav-item active">
                                <a href="#" class="nav-link" data-toggle="dropdown">
                                    <img src="../../backend/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.png'); ?>"
                                         alt="Foto de perfil"
                                         style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                </a>
                                <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                    <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
                                    <li><?php echo htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></li>
                                    <li><?php echo htmlspecialchars($userInfo['correo'] ?? 'correo@ejemplo.com'); ?></li>
                                    <li><?php echo htmlspecialchars($userInfo['idsede'] ?: 'Sede sin definir'); ?></li>
                                    <li class="mt-2">
                                        <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <!-- End Top Navbar -->

            <div class="main-content">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title material-symbols-outlined">delivery_truck_bolt Proveedores</h4>
                                <p class="category">Lista de proveedores registrados en el sistema</p>
                            </div>
                            <div class="card-content">
                                <?php
                                // Consultar proveedores
                                $sql = "SELECT id, nomenclatura, nombre, celu, correo, dire, cuiprov FROM proveedores ORDER BY nombre";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute();
                                $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <div class="table-responsive">
                                    <table id="proveedoresTable" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nomenclatura</th>
                                                <th>Nombre</th>
                                                <th>Celular</th>
                                                <th>Correo</th>
                                                <th>Dirección</th>
                                                <th>NIT / RUT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($proveedores as $prov): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($prov['id']); ?></td>
                                                <td><?php echo htmlspecialchars($prov['nomenclatura']); ?></td>
                                                <td><?php echo htmlspecialchars($prov['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($prov['celu']); ?></td>
                                                <td><?php echo htmlspecialchars($prov['correo']); ?></td>
                                                <td><?php echo htmlspecialchars($prov['dire']); ?></td>
                                                <td><?php echo htmlspecialchars($prov['cuiprov']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>            
                    </div>
                </div>  
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="../../backend/js/jquery-3.3.1.min.js"></script>
    <script src="../../backend/js/popper.min.js"></script>
    <script src="../../backend/js/bootstrap.min.js"></script>
    <!-- Sidebar collapse -->
    <script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>
    <!-- Loader -->
    <script src="../../backend/js/loader.js"></script>
    <!-- DataTables -->
    <script type="text/javascript" src="../../backend/js/datatable.js"></script>
    <script type="text/javascript" src="../../backend/js/datatablebuttons.js"></script>
    <script type="text/javascript" src="../../backend/js/jszip.js"></script>
    <script type="text/javascript" src="../../backend/js/pdfmake.js"></script>
    <script type="text/javascript" src="../../backend/js/vfs_fonts.js"></script>
    <script type="text/javascript" src="../../backend/js/buttonshtml5.js"></script>
    <script type="text/javascript" src="../../backend/js/buttonsprint.js"></script>

    <script>
        $(document).ready(function () {
            $('#proveedoresTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                },
                order: [[2, 'asc']]
            });
        });
    </script>
</body>
</html>

<?php
} else {
    header('Location: ../error404.php');
    exit;
}
ob_end_flush();
?>
