<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('location: ../error404.php');
    exit();
}
require_once '../../backend/bd/ctconex.php';

// Obtener técnicos para filtros
$tecnicos = [];
$resultTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN ('5','6','7')");
while ($rowTec = $resultTec->fetch_assoc()) {
    $tecnicos[] = $rowTec;
}

// Verificar que el usuario existe y obtener su información
$userInfo = null;
if (isset($_SESSION['id'])) {
    $sql = "SELECT nombre, usuario, correo, rol, foto, idsede FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
}

if (!$userInfo) {
    header('Location: ../error404.php');
    exit();
}
?>
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
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
    <style>
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-disponible {
            background-color: #d4edda;
            color: #155724;
        }
        .status-en_diagnostico {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-en_reparacion {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-en_control {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-pendiente {
            background-color: #f5c6cb;
            color: #721c24;
        }
        .status-business_room {
            background-color: #d4edda;
            color: #155724;
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
                <h3><img src="../../backend/img/favicon.png" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <!-- Page Content -->
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background: #27ae60;">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
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
                                $titulo = $userInfo['nombre'];
                                break;
                        }
                        ?>
                        <a class="navbar-brand" href="#"> <B>BUSINESS ROOM </B> <?php echo htmlspecialchars($titulo); ?>
                        </a>
                        <a class="navbar-brand" href="#"> Inventario </a>
                    </div>
                    <!-- Menú derecho (usuario) -->
                    <ul class="nav navbar-nav ml-auto">
                        <li class="dropdown nav-item active">
                            <a href="#" class="nav-link" data-toggle="dropdown">
                                <img src="../../backend/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.png'); ?>"
                                    alt="Foto de perfil"
                                    style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                            </a>
                            <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong>
                                </li>
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
                </nav>
            </div>
            <!--- end:: top_navbar -->
            <div class="main-content">
                <!-- Resumen de Inventario -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <?php
                                // Filtro mejorado para mostrar equipos del técnico actual
                                $whereClause = "estado = 'activo'";
                                if (in_array($_SESSION['rol'], [5, 6, 7])) {
                                    $whereClause .= " AND tecnico_id = " . $_SESSION['id'];
                                }
                                $sql = "SELECT COUNT(*) as total FROM bodega_inventario WHERE " . $whereClause;
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
                                $sql = "SELECT COUNT(*) as disponibles FROM bodega_inventario WHERE " . $whereClause . " AND disposicion IN ('disponible', 'Business Room')";
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
                                $sql = "SELECT COUNT(*) as en_proceso FROM bodega_inventario WHERE " . $whereClause . " AND disposicion IN ('en_diagnostico', 'en_reparacion', 'en_control', 'En revisión', 'En Alistamiento', 'En_Alistamiento')";
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
                                $sql = "SELECT COUNT(*) as pendientes FROM bodega_inventario WHERE " . $whereClause . " AND disposicion IN ('pendiente', 'En reparación')";
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
                                                <option value="Business Room">Business Room</option>
                                                <option value="en_diagnostico">En Diagnóstico</option>
                                                <option value="en_reparacion">En Reparación</option>
                                                <option value="En revisión">En Revisión</option>
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
                                                <option value="Principal">Principal</option>
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
                                                <th>Técnico</th>
                                                <th>Última Modificación</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Consulta mejorada sin filtro fijo de Business Room
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
                                                WHERE i.estado = 'Business '";
                                            // Filtrar por técnico si no es administrador
                                            if (in_array($_SESSION['rol'], [5, 6, 7])) {
                                                $sql .= " AND i.tecnico_id = " . $_SESSION['id'];
                                            }
                                            $sql .= " ORDER BY i.fecha_modificacion DESC";
                                            $result = $conn->query($sql);
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    // Determinar clase CSS para el estado
                                                    $statusClass = 'status-' . strtolower(str_replace(' ', '_', $row['estado_actual']));
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['codigo_g']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['serial']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['ubicacion']) . "</td>";
                                                    echo "<td><span class='badge badge-info'>" . htmlspecialchars($row['grado']) . "</span></td>";
                                                    echo "<td><span class='status-badge " . $statusClass . "'>" . htmlspecialchars($row['estado_actual']) . "</span></td>";
                                                    echo "<td>" . htmlspecialchars($row['tecnico_nombre'] ?? 'Sin asignar') . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['fecha_modificacion']) . "</td>";
                                                    echo "<td class='text-center'>
                                                        <a href='javascript:void(0)' class='btn btn-info btn-sm view-btn' data-id='" . $row['id'] . "' title='Ver detalles'><i class='material-icons'>visibility</i></a>
                                                        <a href='javascript:void(0)' class='btn btn-primary btn-sm edit-btn' data-id='" . $row['id'] . "' title='Editar'><i class='material-icons'>edit</i></a>";
                                                    // Solo mostrar botón eliminar para administradores
                                                    if ($_SESSION['rol'] == 1) {
                                                        echo "<a href='javascript:void(0)' class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "' title='Eliminar'><i class='material-icons'>delete</i></a>";
                                                    }
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='11' class='text-center'>No hay equipos registrados</td></tr>";
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
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
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
                },
                pageLength: 25,
                responsive: true,
                order: [[9, 'desc']] // Ordenar por fecha de modificación
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
            // Limpiar filtros
            $('#filterForm').append('<div class="col-md-12 mt-2"><button type="button" class="btn btn-secondary" id="clearFilters">Limpiar Filtros</button></div>');
            $('#clearFilters').click(function () {
                $('#filterEstado, #filterUbicacion, #filterGrado').val('');
                table.search('').columns().search('').draw();
            });
            // Ver detalles
            $(document).on('click', '.view-btn', function () {
                var id = $(this).data('id');
                $('#viewModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div></div>');
                $('#viewModal').modal('show');
                $.ajax({
                    url: '../../backend/php/get_inventario_details.php',
                    type: 'GET',
                    data: { id: id },
                    success: function (response) {
                        $('#viewModalBody').html(response);
                    },
                    error: function () {
                        $('#viewModalBody').html('<div class="alert alert-danger">Error al cargar los detalles del equipo.</div>');
                    }
                });
            });
            // Editar equipo
            $(document).on('click', '.edit-btn', function () {
                var id = $(this).data('id');
                window.location.href = 'editar_inventario.php?id=' + id;
            });
            // Eliminar equipo (solo para administradores)
            $(document).on('click', '.delete-btn', function () {
                if (confirm('¿Está seguro de que desea eliminar este equipo del inventario?')) {
                    var id = $(this).data('id');
                    var button = $(this);
                    button.prop('disabled', true);
                    $.ajax({
                        url: '../../backend/php/delete_inventario.php',
                        type: 'POST',
                        data: { id: id },
                        success: function (response) {
                            try {
                                var result = JSON.parse(response);
                                if (result.success) {
                                    alert('Equipo eliminado exitosamente');
                                    location.reload();
                                } else {
                                    alert('Error: ' + result.message);
                                    button.prop('disabled', false);
                                }
                            } catch (e) {
                                alert('Equipo eliminado exitosamente');
                                location.reload();
                            }
                        },
                        error: function () {
                            alert('Error al eliminar el equipo');
                            button.prop('disabled', false);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>