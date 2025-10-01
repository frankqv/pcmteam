<!-- public_html/laboratorio/mostrar.php -->
<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('location: ../error404.php');
}
require_once '../../config/ctconex.php';
$tecnicos = [];
$resultTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN ('1','5','6','7')");
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
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/custom.css">
        <link rel="stylesheet" href="../assets/css/loader.css">
        <!-- Data Tables -->
        <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
        <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
        <style>
            /* Filas con mantenimiento registrado */
            .registered-row {
                background-color: #d1ecf1 !important;
            }
            .registered-row:hover {
                background-color: #bee5eb !important;
            }
            /* Indicador visual de mantenimiento */
            .maintenance-indicator {
                display: inline-block;
                width: 10px;
                height: 10px;
                background-color: #28a745;
                border-radius: 50%;
                margin-left: 8px;
                vertical-align: middle;
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0%,
                100% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.5;
                }
            }
            /* Mejorar visualización de los cards */
            .card-body h4 {
                font-size: 2.5rem;
                font-weight: bold;
            }
            .text-white-50 {
                font-size: 0.85rem;
            }
            /* Estilos para los filtros */
            .badge-info {
                background-color: #17a2b8;
                padding: 5px 10px;
                margin-right: 5px;
                border-radius: 12px;
            }
            #clearFilters {
                padding: 0;
                font-size: 12px;
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <div class="body-overlay"></div>
            <?php include_once '../layouts/nav.php';
            include_once '../layouts/menu_data.php'; ?>
            <!-- Sidebar -->
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
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
                                $sql = "SELECT nombre FROM usuarios WHERE id = '" . $_SESSION['id'] . "'";
                                $result = $conn->query($sql);
                                $user = $result->fetch_assoc();
                                $titulo = $user['nombre'];
                            }
                            ?>
                            <a class="navbar-brand" href="#"> LISTADO EQUIPOS ASINGNADOS|</a>
                        </div>
                        <?php
                        $userInfo = [];
                        if (isset($_SESSION['id'])) {
                            $userId = $_SESSION['id'];
                            try {
                                $sql_user = "SELECT nombre, usuario, correo, foto, idsede FROM usuarios WHERE id = :id";
                                $stmt_user = $connect->prepare($sql_user);
                                $stmt_user->bindParam(':id', $userId, PDO::PARAM_INT);
                                $stmt_user->execute();
                                $userInfo = $stmt_user->fetch(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                $userInfo = [];
                            }
                        }
                        ?>
                        <a class="navbar-brand" href="#">
                            <i class="material-icons" style="margin-right: 8px;">handyman</i>
                                MANTENIMIENTO Y LIMPIEZA <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'USUARIO'); ?>
                        </a>
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
                                    <li><?php echo htmlspecialchars(trim($userInfo['idsede'] ?? '') !== '' ? $userInfo['idsede'] : 'Sede sin definir'); ?></li>
                                    <li class="mt-2">
                                        <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a>
                                    </li>
                                    <li><a href="../salir.php" class="btn btn-sm btn-ligth btn-block">Salir</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="main-content">
                    <!-- Resumen de Inventario Mejorado -->
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
                                    <div class="text-white-50">Total Equipos Asignados</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    // Equipos con mantenimiento registrado (ya realizados)
                                    $sql = "SELECT COUNT(DISTINCT i.id) as completados 
                                            FROM bodega_inventario i
                                            INNER JOIN bodega_mantenimiento m ON i.id = m.inventario_id
                                            WHERE i.estado = 'activo' 
                                            AND i.tecnico_id = '" . $_SESSION['id'] . "'";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo $row['completados']; ?></h4>
                                    <div class="text-white-50">Ya Realizados</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    // Equipos en proceso (tienen diagnóstico pero no mantenimiento completo)
                                    $sql = "SELECT COUNT(DISTINCT i.id) as en_proceso 
                                            FROM bodega_inventario i
                                            LEFT JOIN bodega_mantenimiento m ON i.id = m.inventario_id
                                            WHERE i.estado = 'activo' 
                                            AND i.tecnico_id = '" . $_SESSION['id'] . "'
                                            AND i.disposicion IN ('en_diagnostico', 'en_reparacion', 'en_control')
                                            AND m.id IS NULL";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo $row['en_proceso']; ?></h4>
                                    <div class="text-white-50">En Proceso (Sin Mantenimiento)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Filtros Mejorados -->
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
                                                <label>🔵 Estado de Mantenimiento</label>
                                                <select class="form-control" id="filterMantenimiento">
                                                    <option value="">Todos</option>
                                                    <option value="realizado" style="background: #1abc9c; color:#fff;">Ya Realizado ✓</option>
                                                    <option value="pendiente">Por Hacer ✗</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Disposición</label>
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
                                        <div class="col-md-2">
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
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Grado</label>
                                                <select class="form-control" id="filterGrado">
                                                    <option value="">Todos</option>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                    <option value="S">D</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-primary btn-block" id="applyFilters">
                                                    <i class="material-icons" style="font-size: 18px; vertical-align: middle;">filter_list</i>
                                                    Filtrar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Indicador de filtros activos -->
                                    <div id="activeFilters" class="mt-2" style="display: none;">
                                        <small class="text-muted">Filtros activos: <span id="filterTags"></span></small>
                                        <button type="button" class="btn btn-sm btn-link" id="clearFilters">Limpiar filtros</button>
                                    </div>
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
                                                    u.nombre as tecnico_nombre,
                                                    EXISTS(SELECT 1 FROM bodega_mantenimiento m WHERE m.inventario_id = i.id) AS has_mantenimiento
                                                    FROM bodega_inventario i
                                                    LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id 
                                                        AND d.id = (SELECT MAX(id) FROM bodega_diagnosticos WHERE inventario_id = i.id)
                                                    LEFT JOIN bodega_control_calidad cc ON i.id = cc.inventario_id 
                                                        AND cc.id = (SELECT MAX(id) FROM bodega_control_calidad WHERE inventario_id = i.id)
                                                    LEFT JOIN usuarios u ON i.tecnico_id = u.id
                                                    WHERE i.estado = 'activo' AND i.tecnico_id = '" . $_SESSION['id'] . "' 
                                                    ORDER BY i.fecha_modificacion DESC";
                                                $result = $conn->query($sql);
                                                if ($result) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        $has_maintenance = isset($row['has_mantenimiento']) && intval($row['has_mantenimiento']) > 0;
                                                        $trClass = $has_maintenance ? 'registered-row' : '';
                                                        $maintenanceIndicator = $has_maintenance ? '<span class="maintenance-indicator" title="Este equipo tiene registros de mantenimiento"></span>' : '';
                                                        echo "<tr class='" . $trClass . "'>";
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
                                                            <a style='background: #1abc9c;' href='javascript:void(0)' class='btn btn-success btn-sm mantenimiento-btn' data-id='" . $row['id'] . "' title='Editar Mantenimiento y Limpieza'><i class='material-icons'>edit</i></a>
                                                            <a style='background: #dc3545;' href='javascript:void(0)' class='btn btn-primary btn-sm edit-btn' data-id='" . $row['id'] . "' title='Editar Equipo en Inventario'><i class='material-icons'>edit</i></a>
                                                            " . $maintenanceIndicator . "
                                                        </td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    error_log('mostrar.php - Error en consulta SQL: ' . $conn->error);
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
        <script src="../assets/js/jquery-3.3.1.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
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
        <script>
            $(document).ready(function() {
                // Inicializar DataTable
                var table = $('#inventarioTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                    }
                });
                // Función para aplicar filtros
                function applyAllFilters() {
                    var mantenimiento = $('#filterMantenimiento').val();
                    var estado = $('#filterEstado').val();
                    var ubicacion = $('#filterUbicacion').val();
                    var grado = $('#filterGrado').val();
                    // Limpiar filtros personalizados anteriores
                    $.fn.dataTable.ext.search = [];
                    // Filtro especial por mantenimiento
                    if (mantenimiento === 'realizado') {
                        $.fn.dataTable.ext.search.push(
                            function(settings, data, dataIndex) {
                                return $(table.row(dataIndex).node()).hasClass('registered-row');
                            }
                        );
                    } else if (mantenimiento === 'pendiente') {
                        $.fn.dataTable.ext.search.push(
                            function(settings, data, dataIndex) {
                                return !$(table.row(dataIndex).node()).hasClass('registered-row');
                            }
                        );
                    }
                    // Filtros por columnas
                    table.columns(7).search(estado);
                    table.columns(5).search(ubicacion);
                    table.columns(6).search(grado);
                    table.draw();
                    updateFilterTags();
                }
                // Actualizar etiquetas de filtros activos
                function updateFilterTags() {
                    var tags = [];
                    var mantenimiento = $('#filterMantenimiento option:selected').text();
                    var estado = $('#filterEstado option:selected').text();
                    var ubicacion = $('#filterUbicacion option:selected').text();
                    var grado = $('#filterGrado option:selected').text();
                    if ($('#filterMantenimiento').val()) tags.push('<span class="badge badge-info">' + mantenimiento + '</span>');
                    if ($('#filterEstado').val()) tags.push('<span class="badge badge-info">' + estado + '</span>');
                    if ($('#filterUbicacion').val()) tags.push('<span class="badge badge-info">' + ubicacion + '</span>');
                    if ($('#filterGrado').val()) tags.push('<span class="badge badge-info">' + grado + '</span>');
                    if (tags.length > 0) {
                        $('#filterTags').html(tags.join(' '));
                        $('#activeFilters').show();
                    } else {
                        $('#activeFilters').hide();
                    }
                }
                // Botón aplicar filtros
                $('#applyFilters').click(function() {
                    applyAllFilters();
                });
                // Botón limpiar filtros
                $('#clearFilters').click(function() {
                    $('#filterMantenimiento').val('');
                    $('#filterEstado').val('');
                    $('#filterUbicacion').val('');
                    $('#filterGrado').val('');
                    $.fn.dataTable.ext.search = [];
                    table.columns().search('').draw();
                    $('#activeFilters').hide();
                });
                // Ver detalles
                $(document).on('click', '.view-btn', function() {
                    var id = $(this).data('id');
                    $.ajax({
                        url: '../controllers/get_myl_details.php',
                        type: 'GET',
                        data: {
                            inventario_id: id
                        },
                        success: function(response) {
                            $('#viewModalBody').html(response);
                            $('#viewModal').modal('show');
                        },
                        error: function(xhr) {
                            var msg = 'Error al cargar detalles. HTTP ' + xhr.status + ' — ' + xhr.statusText;
                            if (xhr.responseText) msg += '<br><pre style="white-space:pre-wrap;">' + xhr.responseText + '</pre>';
                            $('#viewModalBody').html(msg);
                            $('#viewModal').modal('show');
                        }
                    });
                });
                // Editar mantenimiento
                $('.mantenimiento-btn').click(function() {
                    var id = $(this).data('id');
                    window.location.href = 'ingresar_m.php?id=' + id;
                });
                // Editar equipo
                $('.edit-btn').click(function() {
                    var id = $(this).data('id');
                    window.location.href = 'editar_inventario.php?id=' + id;
                });
            });
        </script>
    </body>
    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>