<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])) {
    header('location: ../error404.php');
}
require_once '../../backend/bd/ctconex.php';
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
        <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
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
        <a class="navbar-brand" href="#" style="color: #fff;">
        <i class="fas fa-tools" style="margin-right: 8px; color: #f39c12;"></i>
        <b>BODEGA | INVENTARIO TRIAGE | </b><?php echo htmlspecialchars($titulo); ?> 
        </a>
        <!-- Menú derecho (usuario) -->
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
                                    $sql = "SELECT COUNT(*) as total FROM bodega_inventario";
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
                                    // Corregido: usar disposicion en lugar de estado no existente
                                    $sql = "SELECT COUNT(*) as disponibles FROM bodega_inventario WHERE disposicion = 'disponible'";
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
                                    $sql = "SELECT COUNT(*) as en_proceso FROM bodega_inventario WHERE disposicion IN ('en_diagnostico', 'en_reparacion', 'en_control')";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo $row['en_proceso']; ?></h4>
                                    <div class="text-white-50">En Proceso</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <?php
                                    // Agregado: Business Room como nueva categoría
                                    $sql = "SELECT COUNT(*) as business FROM bodega_inventario WHERE disposicion = 'Business Room'";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h4 class="mb-0"><?php echo $row['business']; ?></h4>
                                    <div class="text-white-50">Business Room</div>
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
                                                <label>Estado</label>
                                                <select class="form-control" id="filterEstado">
                                                    <option value="">Todos</option>
                                                    <option value="activo">Activo</option>
                                                    <option value="Business">Business</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Ubicación</label>
                                                <select class="form-control" id="filterUbicacion">
                                                    <option value="">Todas</option>
                                                    <option value="Principal">Principal</option>
                                                    <option value="Cúcuta">Cúcuta</option>
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
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Producto</label>
                                                <select class="form-control" id="filterProducto">
                                                    <option value="">Todos</option>
                                                    <option value="Portatil">Portátil</option>
                                                    <option value="Desktop">Desktop</option>
                                                    <option value="AIO">AIO</option>
                                                    <option value="Periferico">Periférico</option>
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
                                    </form>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-secondary" id="clearFilters">
                                                Limpiar Filtros
                                            </button>
                                        </div>
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
                                                    <th>Disposición</th>
                                                    <th>Estado</th>
                                                    <th>Técnico a cargo</th>
                                                    <th>Última Modificación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT i.*, 
                                                    u.nombre as tecnico_nombre
                                                    FROM bodega_inventario i
                                                    LEFT JOIN usuarios u ON i.tecnico_id = u.id
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
                                                    echo "<td>" . htmlspecialchars($row['disposicion']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                                                    echo "<td>
            <form method='post' action='asignar_tecnico.php' style='margin:0;'>
                <input type='hidden' name='equipo_id' value='" . $row['id'] . "'>
                <select name='tecnico_id' class='form-control form-control-sm' onchange='this.form.submit()'>
                    <option value=''>Seleccionar</option>";
                                                    foreach ($tecnicos as $tec) {
                                                        $selected = ($row['tecnico_id'] == $tec['id']) ? "selected" : "";
                                                        echo "<option value='" . $tec['id'] . "' $selected>" . htmlspecialchars($tec['nombre']) . "</option>";
                                                    }
                                                    echo "  </select>
                                                        </form>
                                                    </td>";
                                                    echo "<td>" . htmlspecialchars($row['fecha_modificacion']) . "</td>";
                                                    echo "<td class='text-center'>
                                                        <a href='javascript:void(0)' class='btn btn-info btn-sm view-btn' data-id='" . $row['id'] . "'><i class='material-icons'>visibility</i></a>
                                                        <a href='javascript:void(0)' class='btn btn-primary btn-sm edit-btn' data-id='" . $row['id'] . "'><i class='material-icons'>edit</i></a>
                                                        <a href='javascript:void(0)' class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "'><i class='material-icons'>delete</i></a>
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
                    },
                    order: [[10, 'desc']] // Ordenar por fecha de modificación descendente
                });

                // Aplicar filtros - CORREGIDO
                $('#applyFilters').click(function () {
                    var disposicion = $('#filterDisposicion').val();
                    var estado = $('#filterEstado').val();
                    var ubicacion = $('#filterUbicacion').val();
                    var grado = $('#filterGrado').val();
                    var producto = $('#filterProducto').val();

                    // Limpiar filtros anteriores
                    table.search('').columns().search('').draw();

                    // Aplicar filtros por columna (índices corregidos)
                    if (disposicion) {
                        table.column(7).search('^' + disposicion + '$', true, false); // Disposición - columna 7
                    }
                    if (estado) {
                        table.column(8).search('^' + estado + '$', true, false); // Estado - columna 8
                    }
                    if (ubicacion) {
                        table.column(5).search('^' + ubicacion + '$', true, false); // Ubicación - columna 5
                    }
                    if (grado) {
                        table.column(6).search('^' + grado + '$', true, false); // Grado - columna 6
                    }
                    if (producto) {
                        table.column(1).search('^' + producto + '$', true, false); // Producto - columna 1
                    }
                    
                    table.draw();
                });

                // Limpiar filtros - NUEVO
                $('#clearFilters').click(function () {
                    // Limpiar todos los selects
                    $('#filterDisposicion').val('');
                    $('#filterEstado').val('');
                    $('#filterUbicacion').val('');
                    $('#filterGrado').val('');
                    $('#filterProducto').val('');
                    
                    // Limpiar filtros de DataTable
                    table.search('').columns().search('').draw();
                });

                // Ver detalles
                $(document).on('click', '.view-btn', function () {
                    var id = $(this).data('id');
                    $.ajax({
                        url: '../../backend/php/get_inventario_details.php',
                        type: 'GET',
                        data: { id: id },
                        success: function (response) {
                            $('#viewModalBody').html(response);
                            $('#viewModal').modal('show');
                        },
                        error: function() {
                            alert('Error al cargar los detalles del equipo');
                        }
                    });
                });

                // Editar equipo
                $(document).on('click', '.edit-btn', function () {
                    var id = $(this).data('id');
                    window.location.href = 'editar_inventario.php?id=' + id;
                });

                // Eliminar equipo
                $(document).on('click', '.delete-btn', function () {
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