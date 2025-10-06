
<?php
// ===================================================================
// ARCHIVO: inventario_rol_sede.php (actualizado)
// Sistema de filtrado por ROL y SEDE
// Ahora: gestiona actualizaciones rápidas (ubicacion, posicion, tecnico)
// ===================================================================
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 4, 5, 6, 7])) {
    header('location: ../error404.php');
    exit;
}
require_once '../../config/ctconex.php';
// Helper para escapar valores
function e($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
// ===================================================================
// OBTENER DATOS DEL USUARIO ACTUAL
// ===================================================================
$usuarioId = $_SESSION['id'];
$usuarioRol = $_SESSION['rol'];
$stmtUser = $conn->prepare("SELECT nombre, idsede FROM usuarios WHERE id = ?");
$stmtUser->bind_param("i", $usuarioId);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
if ($resultUser->num_rows === 0) {
    header('location: ../error404.php');
    exit;
}
$datosUsuario = $resultUser->fetch_assoc();
$usuarioSede = trim($datosUsuario['idsede'] ?? '');
$usuarioNombre = $datosUsuario['nombre'] ?? 'Usuario';
$stmtUser->close();
// ===================================================================
// DETERMINAR PERMISOS DE VISUALIZACIÓN
// ===================================================================
$verTodoInventario = false;
$filtroSede = "";
if ($usuarioRol == 1 && strtolower($usuarioSede) === 'todo') {
    // CASO 1: Admin con sede "todo" - Ve TODO
    $verTodoInventario = true;
    $filtroSede = ""; // Sin filtro
} else {
    // CASO 2: Cualquier otro usuario - Filtra por su sede
    $verTodoInventario = false;
    $filtroSede = " AND i.ubicacion = '" . $conn->real_escape_string($usuarioSede) . "'";
}
// ===================================================================
// OBTENER TÉCNICOS PARA ASIGNACIÓN
// ===================================================================
$tecnicos = [];
$resultTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN ('1','5','6','7')");
while ($rowTec = $resultTec->fetch_assoc()) {
    $tecnicos[] = $rowTec;
}
// ===================================================================
// PROCESAR ACTUALIZACIONES (ubicacion, posicion, tecnico)
// Todas las acciones POST desde los select del listado llegarán aquí
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipo_id'])) {
    $equipo_id = intval($_POST['equipo_id']);
    // 1) Actualizar ubicacion
    if (isset($_POST['ubicacion'])) {
        $ubicacion = trim($_POST['ubicacion']);
        if ($ubicacion !== '') {
            $stmt = $conn->prepare("UPDATE bodega_inventario SET ubicacion = ?, fecha_modificacion = NOW() WHERE id = ?");
            $stmt->bind_param("si", $ubicacion, $equipo_id);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    // 2) Actualizar posicion
    if (isset($_POST['posicion'])) {
        $posicion = trim($_POST['posicion']);
        if ($posicion !== '') {
            $stmt = $conn->prepare("UPDATE bodega_inventario SET posicion = ?, fecha_modificacion = NOW() WHERE id = ?");
            $stmt->bind_param("si", $posicion, $equipo_id);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    // 3) Actualizar/limpiar tecnico_id
    // Usamos array_key_exists para distinguir entre "no enviado para translado" y "enviado para translado vacío"
    if (array_key_exists('tecnico_id', $_POST)) {
        $tec_raw = $_POST['tecnico_id'];
        if ($tec_raw === '' || $tec_raw === null) {
            // Limpiar campo tecnico_id
            $stmt = $conn->prepare("UPDATE bodega_inventario SET tecnico_id = NULL, fecha_modificacion = NOW() WHERE id = ?");
            $stmt->bind_param("i", $equipo_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $tecnico_id = intval($tec_raw);
            $stmt = $conn->prepare("UPDATE bodega_inventario SET tecnico_id = ?, fecha_modificacion = NOW() WHERE id = ?");
            $stmt->bind_param("ii", $tecnico_id, $equipo_id);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<?php if (isset($_SESSION['id'])) { ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Inventario - PCMARKETTEAM</title>
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/custom.css">
        <link rel="stylesheet" href="../assets/css/loader.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
        <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
        <style>
            .sede-badge {
                background: #17a2b8;
                color: white;
                padding: 5px 10px;
                border-radius: 5px;
                font-size: 12px;
            }
            .admin-full-access {
                background: #28a745;
                color: white;
                padding: 8px 15px;
                border-radius: 5px;
                font-weight: bold;
            }
            /* ---- CSS AÑADIDO (CORREGIDO) ---- */
            /* Este CSS es solo indicativo para navegadores que lo soporten. */
            /* La solución principal se basa en JS para colorear el <select> completo. */
            #filterPosicion option[value="Traslado"] {
                background-color: #2C62E8;
            }
            #filterPosicion option[value="Recibido"] {
                background-color: #28a745;
            }
            #filterPosicion option[value="De_vuelto_garantia"] {
                background-color: #CDAB00;
            }
            #filterPosicion option[value="recibido_para_garantia"] {
                background-color: #CC0618;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <div class="body-overlay"></div>
            <?php include_once '../layouts/nav.php';
            include_once '../layouts/menu_data.php'; ?>
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
                </div>
                <?php renderMenu($menu); ?>
            </nav>
            <div id="content">
                <div class="top-navbar">
                    <nav class="navbar navbar-expand-lg" style="background:rgb(250, 107, 107);">
                        <div class="container-fluid">
                            <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                                <span class="material-icons">arrow_back_ios</span>
                            </button>
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
                                    $titulo = $usuarioNombre;
                                    break;
                            }
                            ?>
                            <a class="navbar-brand" href="#" style="color: #fff;">
                                <i class="fas fa-tools" style="margin-right: 8px; color: #f39c12;"></i>
                                <b>BODEGA | INVENTARIO TRIAGE | </b><?php echo e($titulo); ?>
                                <?php if ($verTodoInventario): ?>
                                    <span class="admin-full-access ml-2">
                                        <i class="material-icons" style="vertical-align: middle; font-size: 18px;">lock_open</i>
                                        ACCESO COMPLETO
                                    </span>
                                <?php else: ?>
                                    <span class="sede-badge ml-2">
                                        <i class="material-icons" style="vertical-align: middle; font-size: 14px;">location_on</i>
                                        Sede: <?php echo e($usuarioSede); ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <ul class="nav navbar-nav ml-auto">
                                <li class="dropdown nav-item active">
                                    <a href="#" class="nav-link" data-toggle="dropdown">
                                        <img src="../assets/img/<?php echo e($userInfo['foto'] ?? 'reere.webp'); ?>"
                                            style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                    </a>
                                    <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                        <li><strong><?php echo e($usuarioNombre); ?></strong></li>
                                        <li><?php echo e($userInfo['usuario'] ?? 'usuario'); ?></li>
                                        <li><?php echo e($userInfo['correo'] ?? 'correo@ejemplo.com'); ?></li>
                                        <li><?php echo e($usuarioSede); ?></li>
                                        <li class="mt-2">
                                            <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="main-content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    $sql = "SELECT COUNT(*) as total FROM bodega_inventario i WHERE 1=1 $filtroSede";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo e($row['total']); ?></h4>
                                    <div class="text-white-50">Total Equipos<?php echo $verTodoInventario ? '' : ' (Tu Sede)'; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    $sql = "SELECT COUNT(*) as disponibles FROM bodega_inventario i WHERE disposicion = 'disponible' $filtroSede";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo e($row['disponibles']); ?></h4>
                                    <div class="text-white-50">Disponibles</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    $sql = "SELECT COUNT(*) as en_proceso FROM bodega_inventario i WHERE disposicion IN ('en_diagnostico', 'en_reparacion', 'en_control') $filtroSede";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo e($row['en_proceso']); ?></h4>
                                    <div class="text-white-50">En Proceso</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    $sql = "SELECT COUNT(*) as business FROM bodega_inventario i WHERE disposicion = 'Business Room' $filtroSede";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo e($row['business']); ?></h4>
                                    <div class="text-white-50">Business Room</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (!$verTodoInventario): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong><i class="material-icons" style="vertical-align: middle;">info</i> Filtro por Sede Activo</strong><br>
                            Actualmente solo puedes ver equipos de la sede: <strong><?php echo e($usuarioSede); ?></strong>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Filtros de Búsqueda</h4>
                                </div>
                                <div class="card-body">
                                    <form id="filterForm" class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Producto</label>
                                                <select class="form-control" id="filterProducto">
                                                    <option value="">Todos</option>
                                                    <?php
                                                    $query = "SELECT DISTINCT producto FROM bodega_inventario i WHERE producto IS NOT NULL AND producto != '' $filtroSede ORDER BY producto";
                                                    $result = $connect->prepare($query);
                                                    $result->execute();
                                                    $productos = $result->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($productos as $prod) {
                                                        echo '<option value="' . e($prod['producto']) . '">' . e($prod['producto']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Grado</label>
                                                <select class="form-control" id="filterGrado">
                                                    <option value="">Todos</option>
                                                    <?php
                                                    $query = "SELECT DISTINCT grado FROM bodega_inventario i WHERE grado IS NOT NULL AND grado != '' $filtroSede ORDER BY grado";
                                                    $result = $connect->prepare($query);
                                                    $result->execute();
                                                    $grados = $result->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($grados as $grado) {
                                                        echo '<option value="' . e($grado['grado']) . '">' . e($grado['grado']) . '</option>';
                                                    }
                                                    ?>
                                                    <option value="SCRAP">SCRAP</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Ubicación</label>
                                                <select class="form-control" id="filterUbicacion">
                                                    <option value="">Todas</option>
                                                    <?php
                                                    $ubicacionesFijas = ['Medellin', 'Cucuta', 'Unilago', 'Principal'];
                                                    foreach ($ubicacionesFijas as $ubi) {
                                                        $selected = ($ubi === $usuarioSede) ? 'selected' : '';
                                                        $badge = ($ubi === $usuarioSede) ? ' ★' : '';
                                                        echo '<option value="' . e($ubi) . '" ' . $selected . '>' . e($ubi) . $badge . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <?php if (!$verTodoInventario): ?>
                                                    <small class="form-text text-muted">
                                                        <i class="material-icons" style="font-size: 12px; vertical-align: middle;">location_on</i>
                                                        Tu sede: <?php echo e($usuarioSede); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Posición</label>
                                                <select class="form-control colorable" id="filterPosicion">
                                                    <option value="">Todas</option>
                                                    <option value="Traslado">Traslado</option>
                                                    <option value="Recibido">Recibido</option>
                                                    <option value="De_vuelto_garantia">De Vuelta Garantía</option>
                                                    <option value="recibido_para_garantia">Recibido para Garantía</option>
                                                    <?php
                                                    // Agregar posiciones adicionales desde BD que no estén en la lista fija
                                                    $posicionesFijas = ['Traslado', 'Recibido', 'De_vuelto_garantia', 'recibido_para_garantia'];
                                                    $query = "SELECT DISTINCT posicion FROM bodega_inventario i WHERE posicion IS NOT NULL AND posicion != '' $filtroSede ORDER BY posicion";
                                                    $result = $connect->prepare($query);
                                                    $result->execute();
                                                    $posiciones = $result->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($posiciones as $pos) {
                                                        if (!in_array($pos['posicion'], $posicionesFijas)) {
                                                            echo '<option value="' . e($pos['posicion']) . '">' . e($pos['posicion']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Disposición</label>
                                                <select class="form-control" id="filterDisposicion">
                                                    <option value="">Todas</option>
                                                    <option value="disponible">Disponible</option>
                                                    <option value="en_diagnostico">En Diagnóstico</option>
                                                    <option value="en_reparacion">En Reparación</option>
                                                    <option value="en_control">En Control de Calidad</option>
                                                    <option value="pendiente">Pendiente</option>
                                                    <option value="Business Room">Business Room</option>
                                                    <option value="Para Venta">Para Venta</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-primary btn-block" id="applyFilters">
                                                    Aplicar Filtros
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-secondary" id="clearFilters">
                                                Limpiar Filtros
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Inventario Detallado</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="inventarioTable" class="table table-striped table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Producto</th>
                                                    <th>Marca</th>
                                                    <th>Modelo</th>
                                                    <th>Serial</th>
                                                    <th>Ubicación</th>
                                                    <th>Posicion</th>
                                                    <th>Grado</th>
                                                    <th>Disposición</th>
                                                    <th>Estado</th>
                                                    <th>Técnico a cargo</th>
                                                    <th>Última Modificación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $ubicacionesDisponibles = ['Medellin', 'Cucuta', 'Unilago', 'Principal'];
                                                $posicionesDisponibles = ['Traslado', 'Recibido', 'De_vuelto_garantia', 'recibido_para_garantia'];
                                                $sql = "SELECT i.*, u.nombre as tecnico_nombre
                                                    FROM bodega_inventario i
                                                    LEFT JOIN usuarios u ON i.tecnico_id = u.id
                                                    WHERE 1=1 $filtroSede
                                                    ORDER BY i.fecha_modificacion DESC";
                                                $result = $conn->query($sql);
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . e($row['codigo_g']) . "</td>";
                                                    echo "<td>" . e($row['producto']) . "</td>";
                                                    echo "<td>" . e($row['marca']) . "</td>";
                                                    echo "<td>" . e($row['modelo']) . "</td>";
                                                    echo "<td>" . e($row['serial']) . "</td>";
                                                    // SELECTOR DE UBICACIÓN
                                                    echo "<td>
                                                    <form method='post' action='" . $_SERVER['PHP_SELF'] . "' style='margin:0;'>
                                                        <input type='hidden' name='equipo_id' value='" . (int)$row['id'] . "'>
                                                        <select name='ubicacion' class='form-control form-control-sm' onchange='this.form.submit()' style='min-width:110px;'>
                                                            <option value='" . e($row['ubicacion']) . "' selected>" . e($row['ubicacion']) . "</option>
                                                            <option disabled>──────────</option>";
                                                    foreach ($ubicacionesDisponibles as $ubi) {
                                                        if ($ubi !== $row['ubicacion']) {
                                                            echo "<option value='" . e($ubi) . "'>" . e($ubi) . "</option>";
                                                        }
                                                    }
                                                    echo "      </select>
                                                    </form>
                                                </td>";
                                                    // ---- HTML MODIFICADO ----
                                                    // Se añade la clase 'colorable' y se actualiza el 'onchange'
                                                    $posActual = $row['posicion'] ?? '';
                                                    echo "<td>
<form method='post' action='" . $_SERVER['PHP_SELF'] . "' style='margin:0;'>
    <input type='hidden' name='equipo_id' value='" . (int)$row['id'] . "'>
    <select name='posicion' class='form-control form-control-sm colorable' 
            onchange='cambiarColor(this); this.form.submit()' 
            style='min-width:140px; color: white; font-weight: bold;'>
        <option value='" . e((string)$posActual) . "' selected>"
                                                        . e(str_replace('_', ' ', (string)$posActual)) . "</option>
        <option disabled>──────────</option>";
                                                    foreach ($posicionesDisponibles as $pos) {
                                                        $pos = $pos ?? '';
                                                        $posDisplay = str_replace('_', ' ', (string)$pos);
                                                        if ((string)$pos !== (string)$posActual) {
                                                            echo "<option value='" . e((string)$pos) . "'>" . e($posDisplay) . "</option>";
                                                        }
                                                    }
                                                    echo "</select></form></td>";
                                                    echo "<td>" . e($row['grado']) . "</td>";
                                                    echo "<td>" . e($row['disposicion']) . "</td>";
                                                    echo "<td>" . e($row['estado']) . "</td>";
                                                    // SELECTOR DE TÉCNICO
                                                    echo "<td>
                                                    <form method='post' action='" . $_SERVER['PHP_SELF'] . "' style='margin:0;'>
                                                        <input type='hidden' name='equipo_id' value='" . (int)$row['id'] . "'>
                                                        <select name='tecnico_id' class='form-control form-control-sm' onchange='this.form.submit()'>
                                                            <option value=''>Seleccionar</option>";
                                                    foreach ($tecnicos as $tec) {
                                                        $selected = ($row['tecnico_id'] == $tec['id']) ? "selected" : "";
                                                        echo "<option value='" . (int)$tec['id'] . "' $selected>" . e($tec['nombre']) . "</option>";
                                                    }
                                                    echo "      </select>
                                                    </form>
                                                </td>";
                                                    echo "<td>" . e($row['fecha_modificacion']) . "</td>";
                                                    echo "<td class='text-center'>
                                                    <a href='javascript:void(0)' class='btn btn-info btn-sm view-btn' data-id='" . (int)$row['id'] . "'><i class='material-icons'>visibility</i></a>
                                                    <a href='javascript:void(0)' class='btn btn-primary btn-sm edit-btn' data-id='" . (int)$row['id'] . "'><i class='material-icons'>edit</i></a>
                                                    <a href='javascript:void(0)' class='btn btn-danger btn-sm delete-btn' data-id='" . (int)$row['id'] . "'><i class='material-icons'>delete</i></a>
                                                    </td>";
                                                    echo "</tr>";
                                                }
                                                ?>
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
        <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalles del Equipo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="viewModalBody">
                    </div>
                </div>
            </div>
        </div>
        <script src="../assets/js/jquery-3.3.1.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
        <script src="../assets/js/loader.js"></script>
        <script type="text/javascript" src="../assets/js/datatable.js"></script>
        <script type="text/javascript" src="../assets/js/datatablebuttons.js"></script>
        <script type="text/javascript" src="../assets/js/jszip.js"></script>
        <script type="text/javascript" src="../assets/js/pdfmake.js"></script>
        <script type="text/javascript" src="../assets/js/vfs_fonts.js"></script>
        <script type="text/javascript" src="../assets/js/buttonshtml5.js"></script>
        <script type="text/javascript" src="../assets/js/buttonsprint.js"></script>
        <script>
            $(document).ready(function() {
                var table = $('#inventarioTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    order: [
                        [11, 'desc']
                    ]
                });
                function escapeRegex(text) {
                    return text.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                }
                $('#applyFilters').click(function() {
                    var disposicion = $.trim($('#filterDisposicion').val() || '');
                    var ubicacion = $.trim($('#filterUbicacion').val() || '');
                    var grado = $.trim($('#filterGrado').val() || '');
                    var producto = $.trim($('#filterProducto').val() || '');
                    var posicion = $.trim($('#filterPosicion').val() || '');
                    table.search('').columns().search('').draw();
                    if (producto) table.column(1).search('^' + escapeRegex(producto) + '$', true, false, true);
                    if (ubicacion) table.column(5).search('^' + escapeRegex(ubicacion) + '$', true, false, true);
                    if (posicion) table.column(6).search('^' + escapeRegex(posicion) + '$', true, false, true);
                    if (grado) table.column(7).search('^' + escapeRegex(grado) + '$', true, false, true);
                    if (disposicion) table.column(8).search('^' + escapeRegex(disposicion) + '$', true, false, true);
                    table.draw();
                });
                $('#clearFilters').click(function() {
                    $('#filterDisposicion, #filterEstado, #filterUbicacion, #filterGrado, #filterProducto, #filterPosicion').val('');
                    // Disparar evento de cambio para resetear el color del filtro de posición
                    $('#filterPosicion').trigger('change');
                    table.search('').columns().search('').draw();
                });
                // --- Eventos para Modales y Acciones ---
                $(document).on('click', '.view-btn', function() {
                    var id = $(this).data('id');
                    $.ajax({
                        url: '../controllers/get_inventario_details.php',
                        type: 'GET',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            $('#viewModalBody').html(response);
                            $('#viewModal').modal('show');
                        }
                    });
                });
                $(document).on('click', '.edit-btn', function() {
                    window.location.href = 'editar_inventario.php?id=' + $(this).data('id');
                });
                $(document).on('click', '.delete-btn', function() {
                    if (confirm('¿Está seguro de que desea eliminar este equipo?')) {
                        var id = $(this).data('id');
                        $.ajax({
                            url: '../../backend/php/delete_inventario.php',
                            type: 'POST',
                            data: {
                                id: id
                            },
                            success: function(response) {
                                location.reload();
                            }
                        });
                    }
                });
            });
        </script>
        <script>
            // Mapa de colores para los valores de 'posicion'
            const __posicionColores = {
                "Traslado": "#2C62E8",
                "Recibido": "#28A745",
                "De_vuelto_garantia": "#CDAB00",
                "recibido_para_garantia": "#CC0618"
            };
            // Función para cambiar el color del <select>
            function cambiarColor(sel) {
                if (!sel) return;
                const v = sel.value || "";
                const color = __posicionColores[v] || ""; // Busca el color en el mapa
                sel.style.background = color;
                // Ajusta el color del texto para un mejor contraste
                sel.style.color = color ? "white" : "";
                sel.style.fontWeight = color ? "bold" : "";
            }
            // Al cargar el DOM, aplica el color a todos los selects marcados
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.colorable').forEach(function(s) {
                    cambiarColor(s); // Aplica color en la carga inicial
                    // Escucha cambios para aplicar el color inmediatamente
                    s.addEventListener('change', function() {
                        cambiarColor(s);
                    });
                });
            });
        </script>
    </body>
    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>