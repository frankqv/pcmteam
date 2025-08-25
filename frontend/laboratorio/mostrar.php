<!-- frontend/laboratorio/mostrar.php -->
<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('location: ../error404.php');
}
require_once '../../config/ctconex.php';
$tecnicos = [];
$resultTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN ('5','6','7')");
while ($rowTec = $resultTec->fetch_assoc()) {
    $tecnicos[] = $rowTec;
}
?>
<?php if (isset($_SESSION['id'])) { ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Inventario - PCMARKETTEAM</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../backend/css/custom.css">
        <link rel="stylesheet" href="../../backend/css/loader.css">
        <!-- Data Tables -->
        <link rel="stylesheet" type="text/css" href="../../backend/css/datatable.css">
        <link rel="stylesheet" type="text/css" href="../../backend/css/buttonsdataTables.css">
        <link rel="stylesheet" type="text/css" href="../../backend/css/font.css">
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
        <link rel="icon" type="image/png" href="../../backend/img/favicon.webp" />
    </head>
    <body>
        <div class="wrapper">
            <div class="body-overlay"></div>
            <?php include_once '../layouts/nav.php';
            include_once '../layouts/menu_data.php'; ?>
            <!-- Sidebar -->
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3><img src="../../backend/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
                </div>
                <?php renderMenu($menu); ?>
            </nav>
            <!-- Page Content -->
            <div id="content">
                <div class="top-navbar">
                    <nav class="navbar navbar-expand-lg" style="background: #1abc9c;">
                        <div class="container-fluid">
                            <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                                <span class="material-icons">arrow_back_ios</span>
                            </button> 
                            <?php
                            $titulo = "";
                            if ($_SESSION['rol'] == 1) {
                                $titulo = "ADMINISTRADOR";
                            } elseif ($_SESSION['rol'] == 2) {
                                $titulo = "DEFAULT";
                            } elseif ($_SESSION['rol'] == 3) {
                                $titulo = "CONTABLE";
                            } elseif ($_SESSION['rol'] == 4) {
                                $titulo = "COMERCIAL";
                            } elseif ($_SESSION['rol'] == 5) {
                                $titulo = "JEFE TÉCNICO";
                            } elseif ($_SESSION['rol'] == 6) {
                                $titulo = "TÉCNICO";
                            } elseif ($_SESSION['rol'] == 7) {
                                $titulo = "BOGDEGA";
                            } else {
                                // Obtener el nombre del usuario actual
                                $sql = "SELECT nombre FROM usuarios WHERE id = '" . $_SESSION['id'] . "'";
                                $result = $conn->query($sql);
                                $user = $result->fetch_assoc();
                                $titulo = $user['nombre'];
                            }
                            ?>
                            <a class="navbar-brand" href="#"> LISTADO DE MANTENIMIEMNTO Y LIMPIEZA </a>
                            </div>
                        <ul class="nav navbar-nav ml-auto">
                            <li class="dropdown nav-item active">
                                <a href="#" class="nav-link" data-toggle="dropdown">
                                    <img src="../../backend/img/<?php echo htmlspecialchars($user['foto']); ?>"
                                        alt="Foto de perfil" style="width: 30px; height: 30px; border-radius: 50%;">
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <?php
                                        // Obtener información completa del usuario para el dropdown
                                        $sql = "SELECT nombre, usuario, correo, rol, foto, idsede FROM usuarios WHERE id = '" . $_SESSION['id'] . "'";
                                        $result = $conn->query($sql);
                                        $user = $result->fetch_assoc();
                                        ?>
                                        <strong><a href="#"><?php echo htmlspecialchars($user['nombre']); ?></a></strong>
                                        <a href="#"><?php echo htmlspecialchars($user['usuario']); ?></a>
                                        <a href="#"><?php echo htmlspecialchars($user['correo']); ?></a>
                                        <?php echo trim($user['idsede'] ?? '') !== '' ? htmlspecialchars($user['idsede']) : 'Sede sin definir'; ?>
                                        <a href="../cuenta/perfil.php">Mi perfil</a>
                                    </li>
                                    <!-- <li> <a href="../cuenta/salir.php">Salir</a> </li> -->
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="main-content">
                    <!-- Resumen de Inventario -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    $sql = "SELECT COUNT(*) as total FROM bodega_inventario WHERE estado = 'activo' AND tecnico_id = '" . $_SESSION['id'] . "'";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo $row['total']; ?></h4>
                                    <div class="text-white-50">Total Equipos</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    $sql = "SELECT COUNT(*) as disponibles FROM bodega_inventario WHERE (estado = 'activo' OR  estado = 'inactivo' OR estado = 'Business') AND disposicion = 'disponible' AND tecnico_id = '" . $_SESSION['id'] . "'";
                                    //WHERE (i.estado = 'activo' OR i.estado = 'Business Room')
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo $row['disponibles']; ?></h4>
                                    <div class="text-white-50">Disponibles</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    $sql = "SELECT COUNT(*) as en_proceso FROM bodega_inventario WHERE estado = 'activo' AND disposicion IN ('en_diagnostico', 'en_reparacion', 'en_control') AND tecnico_id = '" . $_SESSION['id'] . "'";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo $row['en_proceso']; ?></h4>
                                    <div class="text-white-50">En Proceso</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    $sql = "SELECT COUNT(*) as pendientes FROM bodega_inventario WHERE estado = 'activo' AND disposicion = 'pendiente' AND tecnico_id = '" . $_SESSION['id'] . "'";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo $row['pendientes']; ?></h4>
                                    <div class="text-white-50">Pendientes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Filtros de Búsqueda</h4>
                                </div>
                                <div class="card-body">
                                    <form id="filterForm" class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Estado</label>
                                                <select class="form-control" id="filterEstado">
                                                    <option value="">Todos</option>
                                                    <option value="disponible">Disponible</option>
                                                    <option value="en_diagnostico">En Diagnóstico</option>
                                                    <option value="en_reparacion">En Reparación</option>
                                                    <option value="en_control">En Control de Calidad</option>
                                                    <option value="pendiente">Pendiente</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Ubicación</label>
                                                <select class="form-control" id="filterUbicacion">
                                                    <option value="">Todas</option>
                                                    <option value="Bodega">Bodega</option>
                                                    <option value="Laboratorio">Laboratorio</option>
                                                    <option value="Exhibición">Exhibición</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Grado</label>
                                                <select class="form-control" id="filterGrado">
                                                    <option value="">Todos</option>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-primary btn-block" id="applyFilters">
                                                    Aplicar Filtros
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tabla de Inventario -->
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
                                                    <th>Grado</th>
                                                    <th>Disposicion</th>
                                                    <th>Diagnóstico</th>
                                                    <th>Técnico a cargo</th>
                                                    <th>Última Modificación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT i.*, 
                                                    CASE 
                                                        WHEN d.estado_reparacion IS NOT NULL THEN d.estado_reparacion
                                                        WHEN cc.estado_final IS NOT NULL THEN cc.estado_final
                                                        ELSE i.disposicion 
                                                    END as estado_actual,
                                                    u.nombre as tecnico_nombre
                                                    FROM bodega_inventario i
                                                    LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id 
                                                        AND d.id = (SELECT MAX(id) FROM bodega_diagnosticos WHERE inventario_id = i.id)
                                                    LEFT JOIN bodega_control_calidad cc ON i.id = cc.inventario_id 
                                                        AND cc.id = (SELECT MAX(id) FROM bodega_control_calidad WHERE inventario_id = i.id)
                                                    LEFT JOIN usuarios u ON i.tecnico_id = u.id
                                                    WHERE i.estado = 'activo' AND i.tecnico_id = '" . $_SESSION['id'] . "' 
                                                    ORDER BY i.fecha_modificacion DESC";
                                                $result = $conn->query($sql);
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['codigo_g']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['serial']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['ubicacion']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['grado']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['estado_actual']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['estado_actual']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['tecnico_nombre']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['fecha_modificacion']) . "</td>";
                                                    echo "<td class='text-center'>
                                                        <a href='javascript:void(0)' class='btn btn-secondary btn-sm view-btn' data-id='" . $row['id'] . "' title='VER | Detalles del EQUIPO'><i class='material-icons'>visibility</i></a>
                                                        <a style='background: #1abc9c;' href='javascript:void(0)' class='btn btn-success btn-sm mantenimiento-btn' data-id='" . $row['id'] . "' title='Editar Matenimiento y Limpieza'><i class='material-icons'>edit</i></a>
                                                        <a style='background: #dc3545'; href='javascript:void(0)' class='btn btn-primary btn-sm edit-btn' data-id='" . $row['id'] . "'><i class='material-icons' title='Editar Equipo en Inventario'>edit</i></a>
                                                    </td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                                <!-- <a href='javascript:void(0)' class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "'><i class='material-icons'>delete</i></a> -->
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
        <!-- Modal para ver detalles -->
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
                        <!-- Los detalles se cargarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Scripts -->
        <script src="../../backend/js/jquery-3.3.1.min.js"></script>
        <script src="../../backend/js/popper.min.js"></script>
        <script src="../../backend/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>
        <script src="../../backend/js/loader.js"></script>
        <!-- Data Tables -->
        <script type="text/javascript" src="../../backend/js/datatable.js"></script>
        <script type="text/javascript" src="../../backend/js/datatablebuttons.js"></script>
        <script type="text/javascript" src="../../backend/js/jszip.js"></script>
        <script type="text/javascript" src="../../backend/js/pdfmake.js"></script>
        <script type="text/javascript" src="../../backend/js/vfs_fonts.js"></script>
        <script type="text/javascript" src="../../backend/js/buttonshtml5.js"></script>
        <script type="text/javascript" src="../../backend/js/buttonsprint.js"></script>
        <script>
            $(document).ready(function () {
                // Inicializar DataTable
                var table = $('#inventarioTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                    }
                });
                // Aplicar filtros
                $('#applyFilters').click(function () {
                    var estado = $('#filterEstado').val();
                    var ubicacion = $('#filterUbicacion').val();
                    var grado = $('#filterGrado').val();
                    table.columns(7).search(estado); // Estado
                    table.columns(5).search(ubicacion); // Ubicación
                    table.columns(6).search(grado); // Grado
                    table.draw();
                });
                // Ver detalles
                // Reemplaza el bloque actual ".view-btn" por esto
$(document).on('click', '.view-btn', function () {
    var id = $(this).data('id');

    $.ajax({
        url: '../../backend/php/get_myl_details.php',
        type: 'GET',                  // puedes usar POST si prefieres
        data: { inventario_id: id },  // <-- aquí estaba el error: antes enviabas { id: id }
        success: function (response) {
            $('#viewModalBody').html(response);
            $('#viewModal').modal('show');
        },
        error: function (xhr) {
            // Mostrar error útil dentro del modal para depuración
            var msg = 'Error al cargar detalles. HTTP ' + xhr.status + ' — ' + xhr.statusText;
            if (xhr.responseText) msg += '<br><pre style="white-space:pre-wrap;">' + xhr.responseText + '</pre>';
            $('#viewModalBody').html(msg);
            $('#viewModal').modal('show');
        }
    });
});             
                // Editar equipo
                $('.mantenimiento-btn').click(function () {
                    var id = $(this).data('id');
                    window.location.href = 'ingresar_m.php?id=' + id;
                });
                // Editar equipo
                $('.edit-btn').click(function () {
                    var id = $(this).data('id');
                    window.location.href = 'editar_inventario.php?id=' + id;
                });
                // Eliminar equipo
                $('.delete-btn').click(function () {
                    if (confirm('¿Está seguro de que desea eliminar este equipo?')) {
                        var id = $(this).data('id');
                        $.ajax({
                            url: '../../backend/php/delete_inventario.php',
                            type: 'POST',
                            data: { id: id },
                            success: function (response) {
                                alert('Equipo eliminado exitosamente');
                                location.reload();
                            },
                            error: function () {
                                alert('Error al eliminar el equipo');
                            }
                        });
                    }
                });
            });
        </script>
    </body>
    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>