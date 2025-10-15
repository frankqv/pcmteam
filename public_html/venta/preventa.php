<?php
// public_html/venta/preventa.php
// Solicitudes de Alistamiento - Área Comercial
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 3, 4, 5, 6, 7])) {
    header('location: ../error404.php');
    exit;
}
require_once '../../config/ctconex.php';
require_once '../../backend/pdf/fpdf.php';
function e($v) {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
$mensaje = '';
$tipo_mensaje = '';
// Procesar formulario de solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_solicitud'])) {
    try {
        $solicitante = $_SESSION['nombre'];
        $usuario_id = $_SESSION['id'];
        $sede = trim($_POST['sede']);
        $despacho = trim($_POST['despacho']);
        $cliente_id = !empty($_POST['cliente_id']) ? intval($_POST['cliente_id']) : null;
        $cliente_nombre = trim($_POST['cliente_nombre'] ?? '');
        $productos_json = $_POST['productos_json'] ?? '[]';
        $tecnico_responsable = !empty($_POST['tecnico_responsable']) ? intval($_POST['tecnico_responsable']) : null;
        // Decodificar productos
        $productos = json_decode($productos_json, true);
        // Validaciones
        if (empty($sede)) {
            throw new Exception('La sede es obligatoria');
        }
        if (empty($productos) || count($productos) === 0) {
            throw new Exception('Debe agregar al menos un producto');
        }
        // Iniciar transacción
        $connect->beginTransaction();
        // Insertar solicitud principal
        $sql = "INSERT INTO solicitud_alistamiento (
                    solicitante,
                    usuario_id,
                    sede,
                    despacho,
                    cliente,
                    cliente_id,
                    tecnico_responsable,
                    productos_json
                ) VALUES (
                    :solicitante,
                    :usuario_id,
                    :sede,
                    :despacho,
                    :cliente,
                    :cliente_id,
                    :tecnico_responsable,
                    :productos_json
                )";
        $stmt = $connect->prepare($sql);
        $stmt->execute([
            ':solicitante' => $solicitante,
            ':usuario_id' => $usuario_id,
            ':sede' => $sede,
            ':despacho' => $despacho,
            ':cliente' => $cliente_nombre,
            ':cliente_id' => $cliente_id,
            ':tecnico_responsable' => $tecnico_responsable,
            ':productos_json' => $productos_json
        ]);
        $solicitud_id = $connect->lastInsertId();
        $connect->commit();
        $mensaje = 'Solicitud de alistamiento creada exitosamente. ID: ' . $solicitud_id;
        $tipo_mensaje = 'success';
    } catch (PDOException $e) {
        $connect->rollBack();
        $mensaje = 'Error en la base de datos: ' . $e->getMessage();
        $tipo_mensaje = 'danger';
    } catch (Exception $e) {
        $connect->rollBack();
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'warning';
    }
}
// Obtener técnicos para asignar
$stmt_tecnicos = $connect->prepare("SELECT id, nombre FROM usuarios WHERE rol IN (1) ORDER BY nombre ASC");
$stmt_tecnicos->execute();
$tecnicos = $stmt_tecnicos->fetchAll(PDO::FETCH_ASSOC);
// Obtener solicitudes del usuario o todas si es admin
$whereSolicitud = $_SESSION['rol'] == 1 ? "" : "WHERE usuario_id = :usuario_id";
$sql_solicitudes = "SELECT
                        s.*,
                        u.nombre as tecnico_nombre
                    FROM solicitud_alistamiento s
                    LEFT JOIN usuarios u ON s.tecnico_responsable = u.id
                    $whereSolicitud
                    ORDER BY s.fecha_solicitud DESC";
$stmt_solicitudes = $connect->prepare($sql_solicitudes);
if ($_SESSION['rol'] != 1) {
    $stmt_solicitudes->execute([':usuario_id' => $_SESSION['id']]);
} else {
    $stmt_solicitudes->execute();
}
$solicitudes = $stmt_solicitudes->fetchAll(PDO::FETCH_ASSOC);
// Obtener info del usuario
$userInfo = [];
if (isset($_SESSION['id'])) {
    $sqlUser = "SELECT nombre, usuario, foto, idsede FROM usuarios WHERE id = :id";
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
    <title>Preventa - Solicitud de Alistamiento</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .preventa-header {
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"/><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#" style="color: #fff;">
                            <i class="material-icons" style="vertical-align: middle;">request_quote</i>
                            <b>PREVENTA - SOLICITUD DE ALISTAMIENTO</b>
                        </a>
                        <ul class="nav navbar-nav ml-auto">
                            <li class="dropdown nav-item active">
                                <a href="#" class="nav-link" data-toggle="dropdown">
                                    <img src="../assets/img/<?php echo e($userInfo['foto'] ?? 'reere.webp'); ?>"
                                        style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                </a>
                                <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                    <li><strong><?php echo e($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
                                    <li><small><?php echo e($userInfo['usuario'] ?? 'usuario'); ?></small></li>
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
                    <div class="row">
                        <!-- Formulario de Nueva Solicitud -->
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header preventa-header">
                                    <h4 class="mb-0">
                                        <i class="material-icons" style="vertical-align: middle;">add_circle</i>
                                        Nueva Solicitud de Alistamiento
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" id="solicitudForm">
                                        <div class="row">
                                            <!-- Fecha (Auto) -->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Fecha Solicitud</label>
                                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y'); ?>" readonly>
                                                </div>
                                            </div>
                                            <!-- Número de Solicitud (Auto) -->
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>N° Solicitud</label>
                                                    <input type="text" class="form-control" value="Auto" readonly>
                                                </div>
                                            </div>
                                            <!-- Solicitante (Auto) -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Solicitante</label>
                                                    <input type="text" class="form-control" value="<?php echo e($_SESSION['nombre']); ?>" readonly>
                                                </div>
                                            </div>
                                            <!-- Técnico Responsable -->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tecnico_responsable">Admin/Jefe Técnico</label>
                                                    <select class="form-control form-control-sm" id="tecnico_responsable" name="tecnico_responsable">
                                                        <option value="">Sin asignar</option>
                                                        <?php foreach ($tecnicos as $tecnico): ?>
                                                            <option value="<?php echo $tecnico['id']; ?>"><?php echo e($tecnico['nombre']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Sede -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="sede">Sede <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="sede" name="sede" required>
                                                        <option value="">Seleccione...</option>
                                                        <option value="Principal - Puente Aranda">Principal - Puente Aranda</option>
                                                        <option value="Unilago">Unilago</option>
                                                        <option value="Cúcuta">Cúcuta</option>
                                                        <option value="Medellin">Medellín</option>
                                                        <option value="Pagina Web">Página Web</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Despacho -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="despacho">Despacho <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="despacho" name="despacho" required>
                                                        <option value="">Seleccione...</option>
                                                        <option value="Coordinadora">Coordinadora</option>
                                                        <option value="Interrapidisimo Pte Aranda">Interrapidísimo Pte Aranda</option>
                                                        <option value="Despacho Tienda Pte Aranda">Despacho Tienda Pte Aranda</option>
                                                        <option value="Despacho Tienda Unilago">Despacho Tienda Unilago</option>
                                                        <option value="Despacho Tienda Medellin">Despacho Tienda Medellín</option>
                                                        <option value="Despacho Tienda Cúcuta">Despacho Tienda Cúcuta</option>
                                                        <option value="Picap | Envio por terceros">Picap | Envío por terceros</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Cliente (Búsqueda) -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="cliente_search">Cliente</label>
                                                    <input type="text" class="form-control" id="cliente_search" placeholder="Buscar cliente...">
                                                    <input type="hidden" id="cliente_id" name="cliente_id">
                                                    <input type="hidden" id="cliente_nombre" name="cliente_nombre">
                                                    <small class="form-text text-muted" id="cliente_selected"></small>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="mb-3">
                                            <i class="material-icons" style="vertical-align: middle;">inventory_2</i>
                                            Productos / Equipos Solicitados
                                        </h5>
                                        <!-- Matriz de Productos -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" id="productosTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 8%;">Cantidad</th>
                                                        <th style="width: 35%;">Descripción</th>
                                                        <th style="width: 15%;">Marca</th>
                                                        <th style="width: 15%;">Modelo</th>
                                                        <th style="width: 22%;">Observación</th>
                                                        <th style="width: 5%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="productosBody">
                                                    <tr class="producto-row">
                                                        <td><input type="number" class="form-control form-control-sm cantidad-input" min="1" value="1"></td>
                                                        <td><input type="text" class="form-control form-control-sm descripcion-input" placeholder="Ej: Laptop HP i5 8GB"></td>
                                                        <td><input type="text" class="form-control form-control-sm marca-input" placeholder="HP"></td>
                                                        <td><input type="text" class="form-control form-control-sm modelo-input" placeholder="EliteBook 840"></td>
                                                        <td><input type="text" class="form-control form-control-sm observacion-input" placeholder="Opcional"></td>
                                                        <td><button type="button" class="btn btn-danger btn-sm btn-remove-row" disabled><i class="material-icons" style="font-size: 14px;">close</i></button></td>
                                                    </tr>
                                                    <tr class="producto-row">
                                                        <td><input type="number" class="form-control form-control-sm cantidad-input" min="1" value="1"></td>
                                                        <td><input type="text" class="form-control form-control-sm descripcion-input"></td>
                                                        <td><input type="text" class="form-control form-control-sm marca-input"></td>
                                                        <td><input type="text" class="form-control form-control-sm modelo-input"></td>
                                                        <td><input type="text" class="form-control form-control-sm observacion-input"></td>
                                                        <td><button type="button" class="btn btn-danger btn-sm btn-remove-row"><i class="material-icons" style="font-size: 14px;">close</i></button></td>
                                                    </tr>
                                                    <tr class="producto-row">
                                                        <td><input type="number" class="form-control form-control-sm cantidad-input" min="1" value="1"></td>
                                                        <td><input type="text" class="form-control form-control-sm descripcion-input"></td>
                                                        <td><input type="text" class="form-control form-control-sm marca-input"></td>
                                                        <td><input type="text" class="form-control form-control-sm modelo-input"></td>
                                                        <td><input type="text" class="form-control form-control-sm observacion-input"></td>
                                                        <td><button type="button" class="btn btn-danger btn-sm btn-remove-row"><i class="material-icons" style="font-size: 14px;">close</i></button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" class="btn btn-secondary btn-sm mb-3" id="btnAgregarFila">
                                            <i class="material-icons" style="vertical-align: middle;">add</i>
                                            Agregar Fila
                                        </button>
                                        <input type="hidden" id="productos_json" name="productos_json">
                                        <div class="text-center mt-3">
                                            <button type="submit" name="crear_solicitud" class="btn btn-primary btn-lg" id="btnEnviarSolicitud">
                                                <i class="material-icons" style="vertical-align: middle;">send</i>
                                                Enviar Solicitud de Alistamiento
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Lista de Solicitudes -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Mis Solicitudes de Alistamiento</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="solicitudesTable" class="table table-sm table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Fecha</th>
                                                    <th>Descripción</th>
                                                    <th>Cantidad</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($solicitudes as $sol):
                                                    // Decodificar productos JSON
                                                    $productos = json_decode($sol['productos_json'] ?? '[]', true);
                                                    $total_productos = is_array($productos) ? count($productos) : 0;
                                                    $primera_descripcion = $total_productos > 0 ? $productos[0]['descripcion'] : 'Sin productos';
                                                ?>
                                                    <tr>
                                                        <td><strong>#<?php echo $sol['id']; ?></strong></td>
                                                        <td><?php echo date('d/m/Y', strtotime($sol['fecha_solicitud'])); ?></td>
                                                        <td>
                                                            <?php echo e(substr($primera_descripcion, 0, 50)); ?><?php echo strlen($primera_descripcion) > 50 ? '...' : ''; ?>
                                                            <?php if ($total_productos > 1): ?>
                                                                <br><small class="text-muted">+ <?php echo ($total_productos - 1); ?> producto(s) más</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $total_productos; ?></td>
                                                        <td>
                                                            <?php
                                                            $estado_badges = [
                                                                'pendiente' => 'badge-warning',
                                                                'en_proceso' => 'badge-info',
                                                                'completada' => 'badge-success',
                                                                'cancelada' => 'badge-secondary'
                                                            ];
                                                            $badge = $estado_badges[$sol['estado']] ?? 'badge-secondary';
                                                            ?>
                                                            <span class="badge <?php echo $badge; ?>"><?php echo ucfirst($sol['estado']); ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-info btn-ver-detalle"
                                                                    data-id="<?php echo $sol['id']; ?>"
                                                                    data-solicitante="<?php echo e($sol['solicitante']); ?>"
                                                                    data-sede="<?php echo e($sol['sede']); ?>"
                                                                    data-despacho="<?php echo e($sol['despacho']); ?>"
                                                                    data-cliente="<?php echo e($sol['cliente']); ?>"
                                                                    data-productos='<?php echo htmlspecialchars($sol['productos_json'], ENT_QUOTES); ?>'
                                                                    data-tecnico="<?php echo e($sol['tecnico_nombre'] ?? 'Sin asignar'); ?>"
                                                                    data-fecha="<?php echo $sol['fecha_solicitud']; ?>">
                                                                <i class="material-icons" style="font-size: 14px;">visibility</i>
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
        </div>
    </div>
    <!-- Modal Ver Detalle -->
    <div class="modal fade" id="detalleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white;">
                    <h5 class="modal-title">Detalle de Solicitud de Alistamiento</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID Solicitud:</strong> <span id="det-id"></span></p>
                            <p><strong>Solicitante:</strong> <span id="det-solicitante"></span></p>
                            <p><strong>Sede:</strong> <span id="det-sede"></span></p>
                            <p><strong>Despacho:</strong> <span id="det-despacho"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> <span id="det-cliente"></span></p>
                            <p><strong>Técnico Asignado:</strong> <span id="det-tecnico"></span></p>
                            <p><strong>Fecha de Solicitud:</strong> <span id="det-fecha"></span></p>
                        </div>
                    </div>
                    <hr>
                    <h6><strong>Productos Solicitados:</strong></h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="det-productos-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Cant.</th>
                                    <th>Descripción</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Observación</th>
                                </tr>
                            </thead>
                            <tbody id="det-productos-body">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-generar-pdf">
                        <i class="material-icons" style="vertical-align: middle;">picture_as_pdf</i>
                        Generar PDF
                    </button>
                </div>
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
                order: [[1, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });
            // ========== BÚSQUEDA DE CLIENTES ==========
            let clientesTimeout = null;
            $('#cliente_search').on('input', function() {
                const term = $(this).val().trim();
                if (term.length < 2) {
                    $('#cliente_selected').text('');
                    $('#cliente_id').val('');
                    $('#cliente_nombre').val('');
                    return;
                }
                clearTimeout(clientesTimeout);
                clientesTimeout = setTimeout(function() {
                    $.ajax({
                        method: 'GET',
                        data: { q: term },
                        dataType: 'json',
                        success: function(clientes) {
                            if (clientes && clientes.length > 0) {
                                const cliente = clientes[0]; // Seleccionar el primero
                                $('#cliente_id').val(cliente.id);
                                $('#cliente_nombre').val(cliente.nombre);
                                $('#cliente_selected').html('<span class="text-success">✓ ' + cliente.nombre + ' - ' + cliente.documento + '</span>');
                            } else {
                                $('#cliente_selected').html('<span class="text-warning">No se encontraron clientes</span>');
                                $('#cliente_id').val('');
                                $('#cliente_nombre').val($('#cliente_search').val());
                            }
                        },
                        error: function() {
                            $('#cliente_nombre').val($('#cliente_search').val());
                        }
                    });
                }, 500);
            });
            // ========== MATRIZ DE PRODUCTOS ==========
            // Agregar nueva fila
            $('#btnAgregarFila').on('click', function() {
                const nuevaFila = `
                    <tr class="producto-row">
                        <td><input type="number" class="form-control form-control-sm cantidad-input" min="1" value="1"></td>
                        <td><input type="text" class="form-control form-control-sm descripcion-input"></td>
                        <td><input type="text" class="form-control form-control-sm marca-input"></td>
                        <td><input type="text" class="form-control form-control-sm modelo-input"></td>
                        <td><input type="text" class="form-control form-control-sm observacion-input"></td>
                        <td><button type="button" class="btn btn-danger btn-sm btn-remove-row"><i class="material-icons" style="font-size: 14px;">close</i></button></td>
                    </tr>
                `;
                $('#productosBody').append(nuevaFila);
                actualizarBotonesEliminar();
            });
            // Eliminar fila
            $(document).on('click', '.btn-remove-row', function() {
                $(this).closest('tr').remove();
                actualizarBotonesEliminar();
            });
            function actualizarBotonesEliminar() {
                const totalFilas = $('.producto-row').length;
                if (totalFilas === 1) {
                    $('.btn-remove-row').prop('disabled', true);
                } else {
                    $('.btn-remove-row').prop('disabled', false);
                }
            }
            // ========== ENVIAR FORMULARIO ==========
            $('#solicitudForm').on('submit', function(e) {
                e.preventDefault();
                // Recolectar productos
                const productos = [];
                $('.producto-row').each(function() {
                    const cantidad = parseInt($(this).find('.cantidad-input').val()) || 0;
                    const descripcion = $(this).find('.descripcion-input').val().trim();
                    const marca = $(this).find('.marca-input').val().trim();
                    const modelo = $(this).find('.modelo-input').val().trim();
                    const observacion = $(this).find('.observacion-input').val().trim();
                    // Solo agregar si tiene descripción
                    if (descripcion) {
                        productos.push({
                            cantidad: cantidad,
                            descripcion: descripcion,
                            marca: marca,
                            modelo: modelo,
                            observacion: observacion
                        });
                    }
                });
                // Validar que hay al menos un producto
                if (productos.length === 0) {
                    alert('Debe agregar al menos un producto con descripción');
                    return false;
                }
                // Guardar JSON en campo oculto
                $('#productos_json').val(JSON.stringify(productos));
                // Enviar formulario
                this.submit();
            });
            // ========== VER DETALLE DE SOLICITUD ==========
            $('.btn-ver-detalle').on('click', function() {
                $('#det-id').text('#' + $(this).data('id'));
                $('#det-solicitante').text($(this).data('solicitante'));
                $('#det-sede').text($(this).data('sede'));
                $('#det-despacho').text($(this).data('despacho') || 'No especificado');
                $('#det-cliente').text($(this).data('cliente') || 'No especificado');
                $('#det-tecnico').text($(this).data('tecnico'));
                $('#det-fecha').text(new Date($(this).data('fecha')).toLocaleString('es-CO'));

                // Decodificar y mostrar productos
                const productosJson = $(this).data('productos');
                let productos = [];
                try {
                    productos = typeof productosJson === 'string' ? JSON.parse(productosJson) : productosJson;
                } catch(e) {
                    productos = [];
                }

                // Limpiar tabla
                $('#det-productos-body').empty();

                // Llenar tabla con productos
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
                    $('#det-productos-body').html('<tr><td colspan="5" class="text-center text-muted">No hay productos registrados</td></tr>');
                }

                $('#detalleModal').modal('show');
            });
            // Generar PDF
            $('#btn-generar-pdf').on('click', function() {
                alert('Funcionalidad de PDF en desarrollo');
            });
            // Inicializar
            actualizarBotonesEliminar();
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
