<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('Location: ../error404.php');
    exit;
}
require_once '../../config/ctconex.php';
date_default_timezone_set('America/Bogota');
// Obtener técnicos para filtros
$tecnicos = [];
$resTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN (1,5,6,7) ORDER BY nombre");
while ($r = $resTec->fetch_assoc()) {
    $tecnicos[$r['id']] = $r['nombre'];
}
// Obtener usuarios para mostrar nombres
$usuarios = [];
$resUser = $conn->query("SELECT id, nombre FROM usuarios ORDER BY nombre");
while ($r = $resUser->fetch_assoc()) {
    $usuarios[$r['id']] = $r['nombre'];
}
// Consulta principal para obtener datos de mantenimiento con información del inventario
$sql = "
SELECT 
    m.*,
    inv.codigo_g,
    inv.producto,
    inv.marca,
    inv.modelo,
    inv.serial,
    inv.disposicion,
    inv.grado,
    ut.nombre AS tecnico_nombre,
    ur.nombre AS usuario_registro_nombre
FROM bodega_mantenimiento m
LEFT JOIN bodega_inventario inv ON m.inventario_id = inv.id
LEFT JOIN usuarios ut ON m.tecnico_id = ut.id
LEFT JOIN usuarios ur ON m.usuario_registro = ur.id
ORDER BY m.fecha_registro DESC
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
    <title>Historial de Mantenimiento - Laboratorio</title>
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
            background-color: #16a085;
            color: white;
        }
        .small-muted {
            font-size: .9rem;
            color: #6c757d
        }
        .badge-estado {
            font-size: 0.8em;
            padding: 0.3em 0.6em;
        }
        .badge-pendiente { background-color: #ffc107; color: #212529; }
        .badge-realizado { background-color: #28a745; color: white; }
        .badge-rechazado { background-color: #dc3545; color: white; }
        
        /* Modal personalizado para mantenimiento */
        #mantDetailsModal .modal-dialog {
            max-width: 90vw !important;
            width: 90vw !important;
            margin: 1.75rem auto;
        }
        #mantDetailsModal .modal-content {
            max-height: 90vh;
            overflow-y: auto;
            width: 100%;
        }
        #modal-content-body {
            max-height: 75vh;
            overflow-y: auto;
            padding: 20px;
        }
        .info-section {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
        }
        .info-section h5 {
            color: #16a085;
            border-bottom: 2px solid #16a085;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .info-field {
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
        }
        .info-field strong {
            min-width: 140px;
            flex-shrink: 0;
            margin-right: 10px;
        }
        .info-value {
            flex: 1;
            word-wrap: break-word;
            word-break: break-word;
            white-space: pre-wrap;
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
            <!-- begin:: top-navbar -->
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background: #16a085;">
                    <div class="container-fluid">
                        <!-- Botón Sidebar -->
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <!-- Título dinámico -->
                        <?php
                        $titulo = "";
                        switch ($_SESSION['rol']) {
                            case 1: $titulo = "ADMINISTRADOR"; break;
                            case 2: $titulo = "DEFAULT"; break;
                            case 3: $titulo = "CONTABLE"; break;
                            case 4: $titulo = "COMERCIAL"; break;
                            case 5: $titulo = "JEFE TÉCNICO"; break;
                            case 6: $titulo = "TÉCNICO"; break;
                            case 7: $titulo = "BODEGA"; break;
                            default: $titulo = $userInfo['nombre'] ?? 'USUARIO'; break;
                        }
                        ?>
                        <!-- Branding -->
                        <a class="navbar-brand" href="#" style="color: #fff; font-weight: bold;">
                            <i class="material-icons" style="margin-right: 8px; color: #16a085;">build_circle</i>
                            <b>HISTORIAL DE MANTENIMIENTO - LABORATORIO</b>
                        </a>
                        <?php
                            require_once __DIR__ . '/../../config/ctconex.php';
                            $userInfo = [];
                            if (isset($_SESSION['id'])) {
                                $userId = $_SESSION['id'];
                                try {
                                    $sql = "SELECT nombre, usuario, correo, foto, idsede FROM usuarios WHERE id = :id";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) { 
                                    $userInfo = []; 
                                } 
                            }
                        ?>
                        <!-- Menú derecho (usuario) -->
                        <ul class="nav navbar-nav ml-auto">
                            <!-- notificaciones -->
                            <div> <a class="material-icons">notifications</a> </div>
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
                                        <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="material-icons">more_vert</span>
                    </button>
                </nav>
            </div>
            <!-- end:: top_navbar -->
            <div class="container-fluid">
                <h3 class="mt-3">Historial de <strong>Mantenimiento - Laboratorio</strong></h3>
                <p class="small-muted">Registro completo de todos los mantenimientos realizados en el laboratorio.</p>
                <div class="mb-2">
                    <!-- Filtros -->
                    <div class="row">
                        <div class="col-md-4">
                            <label>Filtrar por técnico:</label>
                            <select id="filterTecnico" class="form-control">
                                <option value="">Todos los técnicos</option>
                                <?php foreach ($tecnicos as $id => $nom): ?>
                                    <option value="<?= (int) $id ?>"><?= htmlspecialchars($nom) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Filtrar por estado:</label>
                            <select id="filterEstado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="realizado">Realizado</option>
                                <option value="rechazado">Rechazado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Filtrar por falla eléctrica:</label>
                            <select id="filterFallaElectrica" class="form-control">
                                <option value="">Todos</option>
                                <option value="si">Con falla eléctrica</option>
                                <option value="no">Sin falla eléctrica</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="mantenimientoTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Fecha Registro</th>
                                <th>Código Equipo</th>
                                <th>Producto</th>
                                <th>Marca/Modelo</th>
                                <th>Estado</th>
                                <th>Técnico</th>
                                <th>Falla Eléctrica</th>
                                <th>Falla Estética</th>
                                <th>Usuario Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr data-tecnico="<?= (int) ($row['tecnico_id'] ?? 0) ?>" 
                                    data-estado="<?= htmlspecialchars($row['estado'] ?? '') ?>"
                                    data-falla-electrica="<?= htmlspecialchars($row['falla_electrica'] ?? '') ?>">
                                    <td><?= htmlspecialchars($row['fecha_registro'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['codigo_g'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['producto'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(($row['marca'] ?? '') . ' ' . ($row['modelo'] ?? '')) ?></td>
                                    <td>
                                        <?php 
                                        $estado = $row['estado'] ?? 'pendiente';
                                        $badgeClass = 'badge-' . $estado;
                                        ?>
                                        <span class="badge <?= $badgeClass ?> badge-estado"><?= ucfirst($estado) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($row['tecnico_nombre'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($row['falla_electrica'] == 'si'): ?>
                                            <span class="badge badge-danger">SÍ</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['falla_estetica'] == 'si'): ?>
                                            <span class="badge badge-warning">SÍ</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['usuario_registro_nombre'] ?? '-') ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-mant-btn" 
                                                data-id="<?= (int) $row['id'] ?>" 
                                                title="Ver Detalles Completos">
                                            <span class="material-icons" style="color:#fff">visibility</span>
                                        </button>
                                        <?php if ($row['inventario_id']): ?>
                                            <a href="../bodega/equipo_historia.php?id=<?= (int) $row['inventario_id'] ?>" 
                                                class="btn btn-secondary btn-sm" title="Historial del Equipo">
                                                <span class="material-icons">history</span>
                                            </a>
                                            <a href="ingresar_m.php?id=<?= (int) $row['inventario_id'] ?>" 
                                                class="btn btn-sm" style="background-color: #16a085;" 
                                                title="Nuevo Mantenimiento">
                                                <span class="material-icons" style="color:#fff">add_circle</span>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para detalles del mantenimiento -->
    <div class="modal fade" id="mantDetailsModal" tabindex="-1" role="dialog" aria-labelledby="mantDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-custom-wide" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #16a085; color: white;">
                    <h5 class="modal-title" id="mantDetailsModalLabel">
                        <i class="material-icons" style="vertical-align: middle;">build_circle</i>
                        Detalles del Mantenimiento
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modal-content-body">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando detalles del mantenimiento...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="material-icons" style="vertical-align: middle;">close</i> Cerrar
                    </button>
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
        var table = $('#mantenimientoTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' },
            order: [[0, 'desc']], // Ordenar por fecha descendente
            columnDefs: [
                { targets: [0], type: 'datetime' }
            ]
        });
        // Filtro personalizado para técnico
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'mantenimientoTable') return true;
            
            var selectedTecnico = $('#filterTecnico').val();
            var selectedEstado = $('#filterEstado').val();
            var selectedFallaElectrica = $('#filterFallaElectrica').val();
            
            var rowNode = table.row(dataIndex).node();
            var rowTecnico = $(rowNode).data('tecnico') || 0;
            var rowEstado = $(rowNode).data('estado') || '';
            var rowFallaElectrica = $(rowNode).data('falla-electrica') || '';
            
            // Filtro por técnico
            if (selectedTecnico && parseInt(selectedTecnico, 10) !== parseInt(rowTecnico, 10)) {
                return false;
            }
            
            // Filtro por estado
            if (selectedEstado && selectedEstado !== rowEstado) {
                return false;
            }
            
            // Filtro por falla eléctrica
            if (selectedFallaElectrica && selectedFallaElectrica !== rowFallaElectrica) {
                return false;
            }
            
            return true;
        });
        // Eventos de filtros
        $('#filterTecnico, #filterEstado, #filterFallaElectrica').on('change', function() {
            table.draw();
        });
        // Manejar clic en el botón "Ver Detalles"
        $('#mantenimientoTable').on('click', '.view-mant-btn', function() {
            var mantenimientoId = $(this).data('id');
            
            // Mostrar modal con mensaje de carga
            $('#modal-content-body').html(`
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando detalles del mantenimiento...</p>
                </div>
            `);
            $('#mantDetailsModal').modal('show');
            
            // Petición AJAX para obtener los detalles
            $.ajax({
                url: '../controllers/get_ingresar_m.php',
                type: 'GET',
                data: { id: mantenimientoId },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    console.log('Respuesta recibida:', response);
                    
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        var content = `
                            <div class="container-fluid">
                                <!-- Información del Equipo -->
                                <div class="info-section">
                                    <h5><i class="material-icons" style="vertical-align: middle;">computer</i> Información del Equipo</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-field">
                                                <strong>Código:</strong> 
                                                <span class="info-value">${data.codigo_g || 'N/A'}</span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Producto:</strong> 
                                                <span class="info-value">${data.producto || 'N/A'}</span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Marca:</strong> 
                                                <span class="info-value">${data.marca || 'N/A'}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-field">
                                                <strong>Modelo:</strong> 
                                                <span class="info-value">${data.modelo || 'N/A'}</span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Serial:</strong> 
                                                <span class="info-value">${data.serial || 'N/A'}</span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Grado:</strong> 
                                                <span class="info-value">${data.grado || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Información del Mantenimiento -->
                                <div class="info-section">
                                    <h5><i class="material-icons" style="vertical-align: middle;">build</i> Información del Mantenimiento</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-field">
                                                <strong>Fecha Registro:</strong> 
                                                <span class="info-value">${data.fecha_registro || 'N/A'}</span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Técnico:</strong> 
                                                <span class="info-value">${data.tecnico_nombre || 'N/A'}</span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Usuario Registro:</strong> 
                                                <span class="info-value">${data.usuario_registro_nombre || 'N/A'}</span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Estado:</strong> 
                                                <span class="info-value">
                                                    <span class="badge badge-${data.estado || 'secondary'}">${data.estado ? data.estado.toUpperCase() : 'N/A'}</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-field">
                                                <strong>Falla Eléctrica:</strong> 
                                                <span class="info-value">
                                                    ${data.falla_electrica === 'si' ? 
                                                        '<span class="badge badge-danger">SÍ</span>' : 
                                                        '<span class="badge badge-success">No</span>'
                                                    }
                                                </span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Falla Estética:</strong> 
                                                <span class="info-value">
                                                    ${data.falla_estetica === 'si' ? 
                                                        '<span class="badge badge-warning">SÍ</span>' : 
                                                        '<span class="badge badge-success">No</span>'
                                                    }
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Detalles de Fallas -->
                                ${data.falla_electrica === 'si' || data.falla_estetica === 'si' ? `
                                <div class="info-section">
                                    <h5><i class="material-icons" style="vertical-align: middle;">warning</i> Detalles de Fallas</h5>
                                    ${data.falla_electrica === 'si' && data.detalle_falla_electrica ? `
                                        <div class="info-field">
                                            <strong>Detalle Falla Eléctrica:</strong> 
                                            <div class="info-value alert alert-danger">${data.detalle_falla_electrica}</div>
                                        </div>
                                    ` : ''}
                                    ${data.falla_estetica === 'si' && data.detalle_falla_estetica ? `
                                        <div class="info-field">
                                            <strong>Detalle Falla Estética:</strong> 
                                            <div class="info-value alert alert-warning">${data.detalle_falla_estetica}</div>
                                        </div>
                                    ` : ''}
                                </div>
                                ` : ''}
                                <!-- Proceso de Mantenimiento -->
                                <div class="info-section">
                                    <h5><i class="material-icons" style="vertical-align: middle;">settings</i> Proceso de Mantenimiento</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-field">
                                                <strong>Limpieza Electrónica:</strong> 
                                                <span class="info-value">
                                                    <span class="badge badge-${data.limpieza_electronico === 'realizada' ? 'success' : data.limpieza_electronico === 'pendiente' ? 'warning' : 'secondary'}">${data.limpieza_electronico || 'N/A'}</span>
                                                </span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Mant. Crema Térmica:</strong> 
                                                <span class="info-value">
                                                    <span class="badge badge-${data.mantenimiento_crema_disciplinaria === 'realizada' ? 'success' : data.mantenimiento_crema_disciplinaria === 'pendiente' ? 'warning' : 'secondary'}">${data.mantenimiento_crema_disciplinaria || 'N/A'}</span>
                                                </span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Mant. Partes:</strong> 
                                                <span class="info-value">
                                                    <span class="badge badge-${data.mantenimiento_partes === 'realizada' ? 'success' : data.mantenimiento_partes === 'pendiente' ? 'warning' : 'secondary'}">${data.mantenimiento_partes || 'N/A'}</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-field">
                                                <strong>Cambio de Piezas:</strong> 
                                                <span class="info-value">
                                                    ${data.cambio_piezas === 'si' ? 
                                                        '<span class="badge badge-info">SÍ</span>' : 
                                                        '<span class="badge badge-secondary">No</span>'
                                                    }
                                                </span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Proceso Reconstrucción:</strong> 
                                                <span class="info-value">
                                                    ${data.proceso_reconstruccion === 'si' ? 
                                                        '<span class="badge badge-info">SÍ</span>' : 
                                                        '<span class="badge badge-secondary">No</span>'
                                                    }
                                                </span>
                                            </div>
                                            <div class="info-field">
                                                <strong>Limpieza General:</strong> 
                                                <span class="info-value">
                                                    <span class="badge badge-${data.limpieza_general === 'realizada' ? 'success' : data.limpieza_general === 'pendiente' ? 'warning' : 'secondary'}">${data.limpieza_general || 'N/A'}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Observaciones -->
                                <div class="info-section">
                                    <h5><i class="material-icons" style="vertical-align: middle;">notes</i> Observaciones y Detalles</h5>
                                    
                                    ${data.observaciones_limpieza_electronico ? `
                                        <div class="info-field">
                                            <strong>Obs. Limpieza Electrónica:</strong>
                                            <div class="info-value alert alert-info">${data.observaciones_limpieza_electronico}</div>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.observaciones_mantenimiento_crema ? `
                                        <div class="info-field">
                                            <strong>Obs. Crema Térmica:</strong>
                                            <div class="info-value alert alert-info">${data.observaciones_mantenimiento_crema}</div>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.piezas_solicitadas_cambiadas ? `
                                        <div class="info-field">
                                            <strong>Piezas Cambiadas:</strong>
                                            <div class="info-value alert alert-warning">${data.piezas_solicitadas_cambiadas}</div>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.parte_reconstruida ? `
                                        <div class="info-field">
                                            <strong>Parte Reconstruida:</strong>
                                            <div class="info-value alert alert-warning">${data.parte_reconstruida}</div>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.area_remite ? `
                                        <div class="info-field">
                                            <strong>Área de Remisión:</strong>
                                            <div class="info-value alert alert-secondary">${data.area_remite}</div>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.proceso_electronico ? `
                                        <div class="info-field">
                                            <strong>Proceso Electrónico:</strong>
                                            <div class="info-value alert alert-primary">${data.proceso_electronico}</div>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.observaciones_globales ? `
                                        <div class="info-field">
                                            <strong>Observaciones Generales:</strong>
                                            <div class="info-value alert alert-secondary">${data.observaciones_globales}</div>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.observaciones ? `
                                        <div class="info-field">
                                            <strong>Observaciones Adicionales:</strong>
                                            <div class="info-value alert alert-light">${data.observaciones}</div>
                                        </div>
                                    ` : ''}
                                </div>
                                <!-- Información Adicional -->
                                ${data.referencia_externa || data.partes_solicitadas ? `
                                <div class="info-section">
                                    <h5><i class="material-icons" style="vertical-align: middle;">info</i> Información Adicional</h5>
                                    
                                    ${data.referencia_externa ? `
                                        <div class="info-field">
                                            <strong>Referencia Externa:</strong>
                                            <span class="info-value">${data.referencia_externa}</span>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.partes_solicitadas ? `
                                        <div class="info-field">
                                            <strong>Partes Solicitadas:</strong>
                                            <div class="info-value alert alert-info">${data.partes_solicitadas}</div>
                                        </div>
                                    ` : ''}
                                    
                                    ${data.remite_otra_area === 'si' ? `
                                        <div class="info-field">
                                            <strong>Remite a Otra Área:</strong>
                                            <span class="info-value">
                                                <span class="badge badge-primary">SÍ</span>
                                            </span>
                                        </div>
                                    ` : ''}
                                </div>
                                ` : ''}
                            </div>
                        `;
                        $('#modal-content-body').html(content);
                    } else {
                        $('#modal-content-body').html(`
                            <div class="alert alert-danger">
                                <i class="material-icons" style="vertical-align: middle;">error</i> 
                                Error: ${response.error || 'No se pudieron obtener los detalles del mantenimiento'}
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', status, error);
                    console.error('Respuesta:', xhr.responseText);
                    
                    var errorMsg = 'No se pudo cargar la información del mantenimiento.';
                    if (status === 'timeout') {
                        errorMsg = 'La consulta tardó demasiado tiempo. Intenta nuevamente.';
                    } else if (xhr.status === 404) {
                        errorMsg = 'El archivo de consulta no fue encontrado.';
                    } else if (xhr.status === 500) {
                        errorMsg = 'Error interno del servidor.';
                    }
                    $('#modal-content-body').html(`
                        <div class="alert alert-danger">
                            <i class="material-icons" style="vertical-align: middle;">error</i> 
                            ${errorMsg}
                            <br><small>Error técnico: ${error}</small>
                        </div>
                    `);
                }
            });
        });
    });
    </script>
</body>
</html>