<?php
ob_start();
session_start();
require_once '../../config/ctconex.php';
// =================================================================================
// >> BLOQUE AJAX: Devolver detalles de un registro específico <<
// =================================================================================
if (isset($_GET['action']) && $_GET['action'] == 'get_details' && isset($_GET['id'])) {
    $esteticoId = (int)$_GET['id'];
    $response = ['success' => false, 'data' => null, 'message' => ''];
    $sql_details = "
        SELECT 
            es.*, 
            i.codigo_g, i.producto, i.marca, i.modelo, i.serial, i.ubicacion,
            u.nombre AS tecnico_nombre
        FROM bodega_estetico es
        LEFT JOIN bodega_inventario i ON es.inventario_id = i.id
        LEFT JOIN usuarios u ON es.tecnico_id = u.id
        WHERE es.id = ?
    ";
    $stmt = $conn->prepare($sql_details);
    if ($stmt) {
        $stmt->bind_param("i", $esteticoId);
        $stmt->execute();
        $result_details = $stmt->get_result();
        if ($data = $result_details->fetch_assoc()) {
            $response['success'] = true;
            $response['data'] = $data;
        } else {
            $response['message'] = 'No se encontró el registro con el ID proporcionado.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Error al preparar la consulta: ' . $conn->error;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
// =================================================================================
// >> FIN DEL BLOQUE AJAX <<
// =================================================================================
// Validación de sesión
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('Location: ../error404.php');
    exit;
}
date_default_timezone_set('America/Bogota');
// Obtener técnicos para filtros
$tecnicos = [];
$resTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN (1,5,6,7) ORDER BY nombre");
while ($r = $resTec->fetch_assoc()) {
    $tecnicos[$r['id']] = $r['nombre'];
}
// Consulta principal para obtener todos los procesos estéticos
$sql = "
SELECT 
    es.id,
    es.inventario_id,
    es.fecha_proceso,
    es.tecnico_id,
    es.estado_carcasa,
    es.estado_pantalla_fisica,
    es.estado_teclado_fisico,
    es.rayones_golpes,
    es.limpieza_realizada,
    es.partes_reemplazadas,
    es.grado_asignado,
    es.estado_final,
    es.observaciones,
    i.codigo_g,
    i.producto,
    i.marca,
    i.modelo,
    i.serial,
    i.ubicacion,
    i.grado,
    i.disposicion,
    u.nombre AS tecnico_nombre
FROM bodega_estetico es
LEFT JOIN bodega_inventario i ON es.inventario_id = i.id
LEFT JOIN usuarios u ON es.tecnico_id = u.id
ORDER BY es.fecha_proceso DESC, es.id DESC
";
$result = $conn->query($sql);
if (!$result) {
    die("Error en consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HISTORIAL DE PROCESO ESTÉTICO - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/datatable.css" />
    <link rel="stylesheet" href="../assets/css/buttonsdataTables.css" />
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 12px;
            background: #f8f9fa;
        }
        table.dataTable thead th {
            background-color: #9b59b6;
            color: white;
        }
        .small-muted {
            font-size: .9rem;
            color: #6c757d
        }
        .badge-aprobado {
            background-color: #28a745;
            color: #fff;
        }
        .badge-rechazado {
            background-color: #dc3545;
            color: #fff
        }
        .badge-requiere_revision {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-grado-a {
            background-color: #28a745;
            color: #fff;
        }
        .badge-grado-b {
            background-color: #17a2b8;
            color: #fff;
        }
        .badge-grado-c {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-grado-scrap {
            background-color: #6c757d;
            color: #fff;
        }
        #esteticoDetailsModal .modal-dialog {
            max-width: 70vw !important;
            width: 70vw !important;
            margin: 1.75rem auto;
        }
        #esteticoDetailsModal .modal-content {
            max-height: 90vh;
            overflow-y: auto;
            width: 100%;
        }
        #modal-content-body {
            max-height: 75vh;
            overflow-y: auto;
            padding: 20px;
        }
        #modal-content-body .alert {
            white-space: pre-wrap;
        }
        .info-field {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
        }
        .info-field strong {
            min-width: 200px;
            flex-shrink: 0;
            margin-right: 10px;
        }
        .info-value {
            flex: 1;
            word-wrap: break-word;
        }
        .process-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .process-section h6 {
            color: #9b59b6;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .estado-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .estado-excelente {
            background-color: #d4edda;
            color: #155724;
        }
        .estado-bueno {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .estado-regular {
            background-color: #fff3cd;
            color: #856404;
        }
        .estado-malo {
            background-color: #f8d7da;
            color: #721c24;
        }
        .estado-nd {
            background-color: #e2e3e5;
            color: #383d41;
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
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php if (function_exists('renderMenu')) {
                renderMenu($menu);
            } ?>
        </nav>
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background: #9b59b6;">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#" style="color: #fff; font-weight: bold;">
                            <i class="material-icons" style="margin-right: 8px;">format_paint</i>
                            <b>HISTORIAL DE PROCESO ESTÉTICO</b>
                        </a>
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
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="container-fluid">
                <h3 class="mt-3">Historial Completo de <strong>Procesos Estéticos</strong></h3>
                <p class="small-muted">
                    Visualización completa de todos los procesos estéticos registrados en bodega.
                    Total de registros: <strong><?php echo $result->num_rows; ?></strong>
                </p>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Filtrar por técnico:</label>
                        <select id="filterTecnico" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach ($tecnicos as $id => $nom): ?>
                                <option value="<?= (int) $id ?>"><?= htmlspecialchars($nom) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Estado Final:</label>
                        <select id="filterEstadoFinal" class="form-control">
                            <option value="">Todos</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="rechazado">Rechazado</option>
                            <option value="requiere_revision">Requiere Revisión</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Grado Asignado:</label>
                        <select id="filterGrado" class="form-control">
                            <option value="">Todos</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="SCRAP">SCRAP</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Limpieza Realizada:</label>
                        <select id="filterLimpieza" class="form-control">
                            <option value="">Todos</option>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Código de Equipo:</label>
                        <input type="text" id="filterCodigo" class="form-control" placeholder="Buscar por código...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="esteticoTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código Equipo</th>
                                <th>Producto</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Fecha Proceso</th>
                                <th>Técnico</th>
                                <th>Grado Asignado</th>
                                <th>Limpieza</th>
                                <th>Estado Final</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr data-tecnico="<?= (int) ($row['tecnico_id'] ?? 0) ?>">
                                        <td><?= htmlspecialchars($row['id'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['codigo_g'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['producto'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['marca'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['modelo'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['fecha_proceso'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['tecnico_nombre'] ?? '-') ?></td>
                                        <td>
                                            <?php
                                            $grado = strtoupper($row['grado_asignado'] ?? '-');
                                            $badgeClass = 'secondary';
                                            if ($grado == 'A') $badgeClass = 'grado-a';
                                            elseif ($grado == 'B') $badgeClass = 'grado-b';
                                            elseif ($grado == 'C') $badgeClass = 'grado-c';
                                            elseif ($grado == 'SCRAP') $badgeClass = 'grado-scrap';
                                            ?>
                                            <span class="badge badge-<?= $badgeClass ?>"><?= $grado ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $row['limpieza_realizada'] == 'si' ? 'success' : 'secondary' ?>">
                                                <?= $row['limpieza_realizada'] == 'si' ? 'Sí' : 'No' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $row['estado_final'] == 'aprobado' ? 'aprobado' : ($row['estado_final'] == 'rechazado' ? 'rechazado' : 'requiere_revision') ?>">
                                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['estado_final'] ?? '-'))) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm view-estetico-btn" data-id="<?= (int) $row['id'] ?>" title="Ver Detalles Completos">
                                                <span class="material-icons" style="color:#f2f2f2">visibility</span>
                                            </button>
                                            <?php if ($row['inventario_id']): ?>
                                                <a style="background: #9b59b6;" href="../bodega/estetico.php?id=<?= (int) $row['inventario_id'] ?>" class="btn btn-secondary btn-sm" title="Editar Proceso Estético">
                                                    <span class="material-icons">computer</span>
                                                </a>
                                                <a href="../bodega/equipo_historia.php?id=<?= (int) $row['inventario_id'] ?>" class="btn btn-secondary btn-sm" title="Ver Trazabilidad Completa">
                                                    <span class="material-icons">summarize</span>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">No se encontraron registros de procesos estéticos</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para detalles del proceso estético -->
    <div class="modal fade" id="esteticoDetailsModal" tabindex="-1" role="dialog" aria-labelledby="esteticoDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-custom-wide" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #9b59b6; color: white;">
                    <h5 class="modal-title" id="esteticoDetailsModalLabel">
                        <i class="material-icons" style="vertical-align: middle;">format_paint</i>
                        Detalles Completos del Proceso Estético
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modal-content-body">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando detalles del proceso estético...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/datatable.js"></script>
    <script src="../assets/js/datatablebuttons.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#esteticoTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                },
                order: [
                    [5, 'desc']
                ],
                pageLength: 25,
                columnDefs: [{
                    targets: [10],
                    orderable: false
                }]
            });
            // Filtros personalizados
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'esteticoTable') return true;
                var filterTecnico = $('#filterTecnico').val();
                var filterEstadoFinal = $('#filterEstadoFinal').val();
                var filterGrado = $('#filterGrado').val();
                var filterLimpieza = $('#filterLimpieza').val();
                var filterCodigo = $('#filterCodigo').val().toLowerCase();
                if (filterTecnico && parseInt(filterTecnico, 10) !== parseInt($(table.row(dataIndex).node()).data('tecnico') || 0, 10))
                    return false;
                if (filterEstadoFinal && !data[9].toLowerCase().includes(filterEstadoFinal.toLowerCase().replace('_', ' ')))
                    return false;
                if (filterGrado && !data[7].includes(filterGrado))
                    return false;
                if (filterLimpieza) {
                    var limpiezaText = filterLimpieza === 'si' ? 'Sí' : 'No';
                    if (!data[8].includes(limpiezaText))
                        return false;
                }
                if (filterCodigo && !data[1].toLowerCase().includes(filterCodigo))
                    return false;
                return true;
            });
            // Event listeners para los filtros
            $('#filterTecnico, #filterEstadoFinal, #filterGrado, #filterLimpieza').on('change', function() {
                table.draw();
            });
            $('#filterCodigo').on('keyup', function() {
                table.draw();
            });
            // Manejar clic en el botón "Ver Detalles"
            $('#esteticoTable').on('click', '.view-estetico-btn', function() {
                var esteticoId = $(this).data('id');
                $('#modal-content-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2">Cargando detalles...</p></div>');
                $('#esteticoDetailsModal').modal('show');
                $.ajax({
                    url: 'historial_estetico.php',
                    type: 'GET',
                    data: {
                        action: 'get_details',
                        id: esteticoId
                    },
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        if (response.success && response.data) {
                            var data = response.data;
                            function formatValue(value) {
                                return value && value.trim() !== '' ? value : 'No especificado';
                            }
                            function formatEstado(estado) {
                                if (!estado) return '<span class="estado-badge estado-nd">N/D</span>';
                                estado = estado.toUpperCase();
                                if (estado === 'EXCELENTE') return '<span class="estado-badge estado-excelente">EXCELENTE</span>';
                                if (estado === 'BUENO') return '<span class="estado-badge estado-bueno">BUENO</span>';
                                if (estado === 'REGULAR') return '<span class="estado-badge estado-regular">REGULAR</span>';
                                if (estado === 'MALO') return '<span class="estado-badge estado-malo">MALO</span>';
                                return '<span class="estado-badge estado-nd">' + estado + '</span>'+ '<br/>';
                            }
                            function formatGrado(grado) {
                                if (!grado) return '<span class="badge badge-secondary">N/D</span>';
                                grado = grado.toUpperCase();
                                var badgeClass = 'secondary';
                                if (grado === 'A') badgeClass = 'grado-a';
                                else if (grado === 'B') badgeClass = 'grado-b';
                                else if (grado === 'C') badgeClass = 'grado-c';
                                else if (grado === 'SCRAP') badgeClass = 'grado-scrap';
                                return '<span class="badge badge-' + badgeClass + '">' + grado + '</span>';
                            }
                            var content = `
                            <div class="container-fluid">
                                <div class="process-section">
                                    <h6><i class="material-icons" style="vertical-align: middle;">computer</i> Información del Equipo</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-field"><strong>Código:</strong> <span class="info-value">${formatValue(data.codigo_g)}</span></div>
                                            <div class="info-field"><strong>Producto:</strong> <span class="info-value">${formatValue(data.producto)}</span></div>
                                            <div class="info-field"><strong>Marca:</strong> <span class="info-value">${formatValue(data.marca)}</span></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-field"><strong>Modelo:</strong> <span class="info-value">${formatValue(data.modelo)}</span></div>
                                            <div class="info-field"><strong>Serial:</strong> <span class="info-value">${formatValue(data.serial)}</span></div>
                                            <div class="info-field"><strong>Ubicación:</strong> <span class="info-value">${formatValue(data.ubicacion)}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-section">
                                    <h6><i class="material-icons" style="vertical-align: middle;">info</i> Información General del Proceso</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-field"><strong>ID Proceso:</strong> <span class="info-value">${data.id}</span></div>
                                            <div class="info-field"><strong>Fecha Proceso:</strong> <span class="info-value">${formatValue(data.fecha_proceso)}</span></div>
                                            <div class="info-field"><strong>Técnico Responsable:</strong> <span class="info-value">${formatValue(data.tecnico_nombre)}</span></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-field"><strong>Grado Asignado:</strong> <span class="info-value">${formatGrado(data.grado_asignado)}</span></div>
                                            <div class="info-field"><strong>Limpieza Realizada:</strong> <span class="info-value"><span class="badge badge-${data.limpieza_realizada === 'si' ? 'success' : 'secondary'}">${data.limpieza_realizada === 'si' ? 'Sí' : 'No'}</span></span></div>
                                            <div class="info-field"><strong>Estado Final:</strong> <span class="info-value"><span class="badge badge-${data.estado_final === 'aprobado' ? 'success' : (data.estado_final === 'rechazado' ? 'danger' : 'warning')}">${data.estado_final ? data.estado_final.charAt(0).toUpperCase() + data.estado_final.slice(1).replace('_', ' ') : 'N/D'}</span></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-section">
                                    <h6><i class="material-icons" style="vertical-align: middle;">assessment</i> Estados de Componentes Físicos</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-field"><strong>Estado Carcasa:</strong> <span class="info-value">${formatEstado(data.estado_carcasa)}</span></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-field"><strong>Estado Pantalla Física:</strong> <span class="info-value">${formatEstado(data.estado_pantalla_fisica)}</span></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-field"><strong>Estado Teclado Físico:</strong> <span class="info-value">${formatEstado(data.estado_teclado_fisico)}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-section">
                                    <h6><i class="material-icons" style="vertical-align: middle;">report_problem</i> Daños y Detalles Estéticos</h6>
                                    <div class="info-field">
                                        <strong>Rayones y Golpes:</strong>
                                        <div class="info-value">
                                            <div class="alert alert-warning" role="alert">${formatValue(data.rayones_golpes)}</div>
                                        </div>
                                    </div>
                                    <div class="info-field">
                                        <strong>Partes Reemplazadas:</strong>
                                        <div class="info-value">
                                            <div class="alert alert-info" role="alert">${formatValue(data.partes_reemplazadas)}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-section">
                                    <h6><i class="material-icons" style="vertical-align: middle;">comment</i> Observaciones Adicionales</h6>
                                    <div class="info-field">
                                        <strong>Observaciones del Técnico:</strong>
                                        <div class="info-value">
                                            <div class="alert alert-secondary" role="alert">${formatValue(data.observaciones)}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-section">
                                    <h6><i class="material-icons" style="vertical-align: middle;">star</i> Resumen de Calificación</h6>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert ${data.estado_final === 'aprobado' ? 'alert-success' : (data.estado_final === 'rechazado' ? 'alert-danger' : 'alert-warning')}" role="alert">
                                                <strong>Resultado Final:</strong> ${data.estado_final ? data.estado_final.charAt(0).toUpperCase() + data.estado_final.slice(1).replace('_', ' ') : 'N/D'}
                                                <br><strong>Grado Asignado:</strong> ${data.grado_asignado || 'N/D'}
                                                <br><strong>Limpieza:</strong> ${data.limpieza_realizada === 'si' ? 'Realizada' : 'No realizada'}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                            $('#modal-content-body').html(content);
                        } else {
                            $('#modal-content-body').html('<div class="alert alert-danger">Error: ' + (response.message || 'No se pudieron cargar los detalles.') + '</div>');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#modal-content-body').html('<div class="alert alert-danger">Ocurrió un error de red o del servidor. Por favor, intente de nuevo. (' + textStatus + ')</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
ob_end_flush();
?>