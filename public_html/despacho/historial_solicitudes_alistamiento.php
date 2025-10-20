<?php
// public_html/despacho/historial_solicitudes_alistamiento.php
// Panel de Admin/Bodega - Ver TODAS las solicitudes de alistamiento
ob_start();
session_start();
// Solo Admin (1) y Bodega (7) pueden acceder
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1,3,4,5,6, 7])) {
    header('location: ../error404.php');
    exit;
}
require_once('../../config/ctconex.php');
date_default_timezone_set('America/Bogota');
function e($v) {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
$mensaje = '';
$tipo_mensaje = '';
// ========== PROCESAR ACTUALIZACIÓN DE SOLICITUD ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'actualizar_estado') {
        try {
            $solicitud_id = intval($_POST['solicitud_id']);
            $nuevo_estado = trim($_POST['estado']);
            $observacion_tecnico = trim($_POST['observacion_tecnico'] ?? '');
            $tecnico_id = !empty($_POST['tecnico_responsable']) ? intval($_POST['tecnico_responsable']) : null;
            $connect->beginTransaction();
            // Actualizar estado, observación técnico y técnico
            $sql = "UPDATE solicitud_alistamiento
                    SET estado = :estado,
                        observacion_tecnico = :observacion_tecnico,
                        tecnico_responsable = :tecnico,
                        fecha_actualizacion = NOW()
                    WHERE id = :id";
            $stmt = $connect->prepare($sql);
            $stmt->execute([
                ':estado' => $nuevo_estado,
                ':observacion_tecnico' => $observacion_tecnico,
                ':tecnico' => $tecnico_id,
                ':id' => $solicitud_id
            ]);
            $connect->commit();
            $mensaje = "Solicitud #$solicitud_id actualizada exitosamente";
            $tipo_mensaje = "success";
        } catch (Exception $e) {
            if ($connect->inTransaction()) {
                $connect->rollBack();
            }
            $mensaje = "Error al actualizar: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    }
    // ========== ACTUALIZACIÓN RÁPIDA DESDE TABLA ==========
    if ($_POST['action'] === 'cambiar_estado_rapido') {
        try {
            $solicitud_id = intval($_POST['solicitud_id']);
            $nuevo_estado = trim($_POST['estado']);
            $sql = "UPDATE solicitud_alistamiento SET estado = :estado, fecha_actualizacion = NOW() WHERE id = :id";
            $stmt = $connect->prepare($sql);
            $stmt->execute([':estado' => $nuevo_estado, ':id' => $solicitud_id]);
            echo json_encode(['success' => true, 'mensaje' => 'Estado actualizado']);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
            exit;
        }
    }
    if ($_POST['action'] === 'asignar_tecnico_rapido') {
        try {
            $solicitud_id = intval($_POST['solicitud_id']);
            $tecnico_id = !empty($_POST['tecnico_id']) ? intval($_POST['tecnico_id']) : null;
            $sql = "UPDATE solicitud_alistamiento SET tecnico_responsable = :tecnico, fecha_actualizacion = NOW() WHERE id = :id";
            $stmt = $connect->prepare($sql);
            $stmt->execute([':tecnico' => $tecnico_id, ':id' => $solicitud_id]);
            // Obtener nombre del técnico
            $nombre_tecnico = 'Sin asignar';
            if ($tecnico_id) {
                $stmt = $connect->prepare("SELECT nombre FROM usuarios WHERE id = :id");
                $stmt->execute([':id' => $tecnico_id]);
                $tecnico = $stmt->fetch(PDO::FETCH_ASSOC);
                $nombre_tecnico = $tecnico['nombre'] ?? 'Sin asignar';
            }
            echo json_encode(['success' => true, 'mensaje' => 'Técnico asignado', 'nombre' => $nombre_tecnico]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
            exit;
        }
    }
}
// ========== OBTENER TODAS LAS SOLICITUDES ==========
$filtro_estado = $_GET['filtro_estado'] ?? 'todos';
$busqueda = $_GET['busqueda'] ?? '';
$where_conditions = [];
$params = [];
if ($filtro_estado !== 'todos') {
    $where_conditions[] = "sa.estado = :estado";
    $params[':estado'] = $filtro_estado;
}
if (!empty($busqueda)) {
    $where_conditions[] = "(sa.id = :busqueda OR sa.solicitante LIKE :busqueda_like OR sa.cliente LIKE :busqueda_like OR sa.descripcion LIKE :busqueda_like)";
    $params[':busqueda'] = $busqueda;
    $params[':busqueda_like'] = '%' . $busqueda . '%';
}
$where_clause = count($where_conditions) > 0 ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
$sql = "SELECT
            sa.*,
            u_solicitante.nombre as solicitante_nombre,
            u_tecnico.nombre as tecnico_nombre
        FROM solicitud_alistamiento sa
        LEFT JOIN usuarios u_solicitante ON sa.usuario_id = u_solicitante.id
        LEFT JOIN usuarios u_tecnico ON sa.tecnico_responsable = u_tecnico.id
        $where_clause
        ORDER BY sa.fecha_solicitud DESC";
$stmt = $connect->prepare($sql);
$stmt->execute($params);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Obtener técnicos (Admin, Técnicos, Bodega - roles 1, 6, 7)
$sql_tecnicos = "SELECT id, nombre FROM usuarios WHERE rol IN ('1', '6', '7') AND estado = '1' ORDER BY nombre ASC";
$stmt_tecnicos = $connect->query($sql_tecnicos);
$tecnicos = $stmt_tecnicos->fetchAll(PDO::FETCH_ASSOC);
// Estadísticas rápidas
$sql_stats = "SELECT
    COUNT(*) as total,
    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas
FROM solicitud_alistamiento";
$stmt_stats = $connect->query($sql_stats);
$stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);
// Obtener info del usuario
$userInfo = [];
if (isset($_SESSION['id'])) {
    $sqlUser = "SELECT nombre, usuario, correo, foto, rol, idsede FROM usuarios WHERE id = :id";
    $stmtUser = $connect->prepare($sqlUser);
    $stmtUser->execute([':id' => $_SESSION['id']]);
    $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial de Solicitudes de Alistamiento - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .stats-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-card h3 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: bold;
        }
        .stats-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .badge-pendiente { background: #ffc107; color: #000; }
        .badge-en_proceso { background: #17a2b8; color: #fff; }
        .badge-completada { background: #28a745; color: #fff; }
        .badge-cancelada { background: #6c757d; color: #fff; }
        .modal-header-edit {
            background: #2B6B5D;
            color: white;
        }
    </style>
</head>
<body>
    <div class="wrapper">
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
                <nav class="navbar navbar-expand-lg" style="background: #2B6B5D;">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#" style="color: #fff;">
                            <i class="material-icons" style="vertical-align: middle;">inventory</i>
                            <b>HISTORIAL DE SOLICITUDES DE ALISTAMIENTO</b>
                        </a>
                        <ul class="nav navbar-nav ml-auto">
                            <li class="dropdown nav-item active">
                                <a href="#" class="nav-link" data-toggle="dropdown">
                                    <img src="../assets/img/<?php echo e($userInfo['foto'] ?? 'reere.webp'); ?>"
                                        style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                </a>
                                <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                    <li><strong><?php echo e($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
                                    <li><?php echo e($userInfo['usuario'] ?? 'usuario'); ?></li>
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
                <div class="container-fluid">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                            <?php echo e($mensaje); ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>
                    <!-- Estadísticas -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stats-card" style="background: #275781;">
                                <h3><?php echo $stats['total']; ?></h3>
                                <p>Total Solicitudes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card" style="background: #ad2235ff 100%;">
                                <h3><?php echo $stats['pendientes']; ?></h3>
                                <p>Pendientes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card" style="background: #F0DD00; color:#6E6E6E;">
                                <h3><?php echo $stats['en_proceso']; ?></h3>
                                <p>En Proceso</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card success" style="background: #117f2c;">
                                <h3><?php echo $stats['completadas']; ?></h3>
                                <p>Completadas</p>
                            </div>
                        </div>
                    </div>
                    <!-- Tabla de Solicitudes -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Todas las Solicitudes de Alistamiento</h4>
                        </div>
                        <div class="card-body">
                            <!-- Filtros -->
                            <form method="GET" class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Filtrar por Estado</label>
                                            <select name="filtro_estado" class="form-control" onchange="this.form.submit()">
                                                <option value="todos" <?php echo $filtro_estado === 'todos' ? 'selected' : ''; ?>>Todos</option>
                                                <option value="pendiente" <?php echo $filtro_estado === 'pendiente' ? 'selected' : ''; ?>>Pendientes</option>
                                                <option value="en_proceso" <?php echo $filtro_estado === 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
                                                <option value="completada" <?php echo $filtro_estado === 'completada' ? 'selected' : ''; ?>>Completadas</option>
                                                <option value="despachado" <?php echo $filtro_estado === 'despachado' ? 'selected' : ''; ?>>Despachado</option>
                                                <option value="entregado" <?php echo $filtro_estado === 'entregado' ? 'selected' : ''; ?>>Entregado</option>
                                                <option value="cancelada" <?php echo $filtro_estado === 'cancelada' ? 'selected' : ''; ?>>Canceladas</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Buscar</label>
                                            <input type="text" name="busqueda" class="form-control"
                                                   placeholder="ID, solicitante, cliente, descripción..."
                                                   value="<?php echo e($busqueda); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block"><i class="material-icons" style="font-size: 18px; vertical-align: middle;">search</i>Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table id="solicitudesTable" class="table table-sm table-hover table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Solicitante</th>
                                            <th>Sede</th>
                                            <th>Cliente</th>
                                            <th>Cantidad</th>
                                            <th>Estado</th>
                                            <th>Técnico</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($solicitudes as $sol):
                                            // Extraer JSON de productos
                                            $productos_json_str = '';
                                            if (preg_match('/Productos JSON: (.+)$/s', $sol['observacion'] ?? '', $matches)) {
                                                $productos_json_str = $matches[1];
                                            }
                                            $productos = $productos_json_str ? json_decode($productos_json_str, true) : [];
                                            // Extraer despacho
                                            $despacho = '';
                                            if (preg_match('/Despacho: ([^|]+)/', $sol['observacion'] ?? '', $despacho_match)) {
                                                $despacho = trim($despacho_match[1]);
                                            }
                                        ?>
                                            <tr>
                                                <td><strong>#<?php echo $sol['id']; ?></strong></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])); ?></td>
                                                <td><?php echo e($sol['solicitante']); ?></td>
                                                <td><?php echo e($sol['sede']); ?></td>
                                                <td><?php echo e($sol['cliente'] ?: 'N/A'); ?></td>
                                                <td><?php echo e($sol['cantidad']); ?></td>
                                                <td>
                                                    <select class="form-control form-control-sm estado-select" data-solicitud-id="<?php echo $sol['id']; ?>" style="min-width: 120px;">
                                                        <option value="pendiente" <?php echo $sol['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                        <option value="en_proceso" <?php echo $sol['estado'] === 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
                                                        <option value="completada" <?php echo $sol['estado'] === 'completada' ? 'selected' : ''; ?>>Completada</option>
                                                        <option value="cancelada" <?php echo $sol['estado'] === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm tecnico-select" data-solicitud-id="<?php echo $sol['id']; ?>" style="min-width: 150px;">
                                                        <option value="">Sin asignar</option>
                                                        <?php foreach ($tecnicos as $tec): ?>
                                                            <option value="<?php echo $tec['id']; ?>" <?php echo $sol['tecnico_responsable'] == $tec['id'] ? 'selected' : ''; ?>>
                                                                <?php echo e($tec['nombre']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-info btn-ver-detalle"
                                                        data-id="<?php echo $sol['id']; ?>"
                                                        data-solicitud='<?php echo htmlspecialchars(json_encode($sol), ENT_QUOTES); ?>'
                                                        data-productos='<?php echo htmlspecialchars($productos_json_str, ENT_QUOTES); ?>'
                                                        data-despacho="<?php echo e($despacho); ?>">
                                                        <i class="material-icons" style="font-size: 14px;">visibility</i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary btn-editar"
                                                        data-id="<?php echo $sol['id']; ?>"
                                                        data-estado="<?php echo $sol['estado']; ?>"
                                                        data-observacion="<?php echo e($sol['observacion_tecnico'] ?? ''); ?>">
                                                        <i class="material-icons" style="font-size: 14px;">edit</i>
                                                    </button>
                                                </td>
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
    <!-- Modal Ver Detalle -->
    <div class="modal fade" id="detalleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2B6B5D; color: white;">
                    <h5 class="modal-title">Detalle de Solicitud</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <span id="det-id"></span></p>
                            <p><strong>Solicitante:</strong> <span id="det-solicitante"></span></p>
                            <p><strong>Sede:</strong> <span id="det-sede"></span></p>
                            <p><strong>Despacho:</strong> <span id="det-despacho"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> <span id="det-cliente"></span></p>
                            <p><strong>Técnico:</strong> <span id="det-tecnico"></span></p>
                            <p><strong>Estado:</strong> <span id="det-estado"></span></p>
                            <p><strong>Fecha:</strong> <span id="det-fecha"></span></p>
                        </div>
                    </div>
                    <hr>
                    <h6><strong>Productos Solicitados:</strong></h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Cant.</th>
                                    <th>Descripción</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Observación</th>
                                </tr>
                            </thead>
                            <tbody id="det-productos-body"></tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong>Observación del Comercial:</strong></h6>
                            <p id="det-observacion-global" class="text-muted"></p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Observación del Técnico:</strong></h6>
                            <p id="det-observacion-tecnico" class="text-muted"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Editar Estado -->
    <div class="modal fade" id="editarModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-edit">
                    <h5 class="modal-title">Editar Solicitud #<span id="edit-id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="actualizar_estado">
                    <input type="hidden" name="solicitud_id" id="edit-solicitud-id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-estado">Estado <span class="text-danger">*</span></label>
                            <select class="form-control" name="estado" id="edit-estado" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="completada">Completada</option>
                                <option value="despachado">Despachado</option>
                                <option value="entregado">Entregado</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-tecnico">Técnico Responsable</label>
                            <select class="form-control" name="tecnico_responsable" id="edit-tecnico">
                                <option value="">Sin asignar</option>
                                <?php foreach ($tecnicos as $tec): ?>
                                    <option value="<?php echo $tec['id']; ?>"><?php echo e($tec['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-observacion">Observación del Técnico</label>
                            <textarea class="form-control" name="observacion_tecnico" id="edit-observacion" rows="4"
                                    placeholder="Comentarios del técnico sobre el proceso de alistamiento, problemas encontrados, etc."></textarea>
                            <small class="form-text text-muted">Esta observación es visible para todos los usuarios que vean esta solicitud.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons" style="font-size: 16px; vertical-align: middle;">save</i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
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
            $('#solicitudesTable').DataTable({
                order: [[0, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            // Ver detalle
            $('.btn-ver-detalle').on('click', function() {
                const solicitudData = $(this).data('solicitud');
                const productosJson = $(this).data('productos');
                const despacho = $(this).data('despacho');
                $('#det-id').text('#' + solicitudData.id);
                $('#det-solicitante').text(solicitudData.solicitante);
                $('#det-sede').text(solicitudData.sede);
                $('#det-despacho').text(despacho || 'No especificado');
                $('#det-cliente').text(solicitudData.cliente || 'N/A');
                $('#det-tecnico').text(solicitudData.tecnico_nombre || 'Sin asignar');
                $('#det-estado').html('<span class="badge badge-' + solicitudData.estado + '">' +
                    solicitudData.estado.replace('_', ' ').toUpperCase() + '</span>');
                $('#det-fecha').text(new Date(solicitudData.fecha_solicitud).toLocaleString('es-CO'));
                // Productos
                let productos = [];
                try {
                    productos = typeof productosJson === 'string' ? JSON.parse(productosJson) : productosJson;
                } catch (e) {
                    productos = [];
                }
                $('#det-productos-body').empty();
                if (productos && productos.length > 0) {
                    productos.forEach(function(prod) {
                        const fila = `
                            <tr>
                                <td>${prod.cantidad || 1}</td>
                                <td>${prod.descripcion || ''}</td>
                                <td>${prod.marca || '-'}</td>
                                <td>${prod.modelo || '-'}</td>
                                <td>${prod.observacion || '-'}</td>
                            </tr>
                        `;
                        $('#det-productos-body').append(fila);
                    });
                } else {
                    $('#det-productos-body').html('<tr><td colspan="5" class="text-center text-muted">Sin productos</td></tr>');
                }
                // Observaciones
                $('#det-observacion-global').text(solicitudData.observacion_global || 'Sin observaciones del comercial');
                $('#det-observacion-tecnico').text(solicitudData.observacion_tecnico || 'Sin observaciones del técnico');
                $('#detalleModal').modal('show');
            });
            // Editar
            $('.btn-editar').on('click', function() {
                const solicitudData = $(this).closest('tr').find('.btn-ver-detalle').data('solicitud');
                $('#edit-id').text(solicitudData.id);
                $('#edit-solicitud-id').val(solicitudData.id);
                $('#edit-estado').val(solicitudData.estado);
                $('#edit-tecnico').val(solicitudData.tecnico_responsable || '');
                $('#edit-observacion').val(solicitudData.observacion_tecnico || '');
                $('#editarModal').modal('show');
            });
            // ========== CAMBIO RÁPIDO DE ESTADO ==========
            $('.estado-select').on('change', function() {
                const solicitudId = $(this).data('solicitud-id');
                const nuevoEstado = $(this).val();
                const $select = $(this);
                if (confirm('¿Cambiar el estado de la solicitud #' + solicitudId + '?')) {
                    $.ajax({
                        url: '',
                        method: 'POST',
                        data: {
                            action: 'cambiar_estado_rapido',
                            solicitud_id: solicitudId,
                            estado: nuevoEstado
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Actualizar color del badge
                                $select.removeClass('bg-danger bg-warning bg-info bg-success bg-secondary');
                                switch(nuevoEstado) {
                                    case 'pendiente': $select.addClass('bg-danger'); break;
                                    case 'en_proceso': $select.addClass('bg-info'); break;
                                    case 'completada': $select.addClass('bg-success'); break;
                                    case 'cancelada': $select.addClass('bg-secondary'); break;
                                }
                                alert('✅ ' + response.mensaje);
                            } else {
                                alert('❌ Error: ' + response.mensaje);
                            }
                        },
                        error: function() {
                            alert('❌ Error al actualizar el estado');
                        }
                    });
                } else {
                    // Revertir selección
                    location.reload();
                }
            });
            // ========== ASIGNACIÓN RÁPIDA DE TÉCNICO ==========
            $('.tecnico-select').on('change', function() {
                const solicitudId = $(this).data('solicitud-id');
                const tecnicoId = $(this).val();
                const $select = $(this);
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: {
                        action: 'asignar_tecnico_rapido',
                        solicitud_id: solicitudId,
                        tecnico_id: tecnicoId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('✅ Técnico asignado: ' + response.nombre);
                        } else {
                            alert('❌ Error: ' + response.mensaje);
                        }
                    },
                    error: function() {
                        alert('❌ Error al asignar técnico');
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
