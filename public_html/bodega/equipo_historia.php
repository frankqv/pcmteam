<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('Location: ../error404.php');
    exit;
}
require_once '../../config/ctconex.php';
// Obtener ID del inventario (equipo) de la URL
$inventario_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($inventario_id <= 0) {
    echo "<p>ID de equipo inválido.</p>";
    exit;
}
// Primero obtener información del equipo desde bodega_inventario
$sql_equipo = "SELECT * FROM bodega_inventario WHERE id = ?";
$stmt = $conn->prepare($sql_equipo);
$stmt->bind_param("i", $inventario_id);
$stmt->execute();
$equipo = $stmt->get_result()->fetch_assoc();
if (!$equipo) {
    echo "<p>Equipo no encontrado.</p>";
    exit;
}
// Función para obtener nombre del técnico/usuario
function getNombreTecnico($conn, $tecnico_id) {
    if (!$tecnico_id || $tecnico_id == 0) return 'No asignado';
    $sql = "SELECT nombre FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tecnico_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['nombre'] : 'Usuario #' . $tecnico_id;
}
// Función para obtener nombre del proveedor
function getNombreProveedor($conn, $proveedor_id) {
    if (!$proveedor_id || $proveedor_id == 0) return null;
    $sql = "SELECT nombre FROM proveedores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $proveedor_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['nombre'] : 'Proveedor #' . $proveedor_id;
}
// Obtener historial completo del equipo
$historial = [];
// 1. Entradas
$sql = "SELECT 'ENTRADA' as tipo, fecha_entrada as fecha, usuario_id as tecnico_id, 
        observaciones, cantidad, NULL as estado, proveedor_id
        FROM bodega_entradas 
        WHERE inventario_id = ? ORDER BY fecha_entrada DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inventario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['tecnico_nombre'] = getNombreTecnico($conn, $row['tecnico_id']);
    $row['proveedor_nombre'] = $row['proveedor_id'] ? getNombreProveedor($conn, $row['proveedor_id']) : null;
    $row['detalles'] = "Cantidad: " . $row['cantidad'] . ($row['proveedor_nombre'] ? " | Proveedor: " . $row['proveedor_nombre'] : "");
    $historial[] = $row;
}
// 2. Diagnósticos (Triage 2)
$sql = "SELECT 'DIAGNOSTICO' as tipo, fecha_diagnostico as fecha, tecnico_id,
        observaciones, estado_reparacion as estado,
        CONCAT('Cámara: ', IFNULL(camara, 'N/D'), 
               ' | Teclado: ', IFNULL(teclado, 'N/D'),
               ' | Pantalla: ', IFNULL(pantalla, 'N/D'),
               ' | Batería: ', IFNULL(bateria, 'N/D'),
               ' | Parlantes: ', IFNULL(parlantes, 'N/D'),
               ' | Micrófono: ', IFNULL(microfono, 'N/D'),
               ' | Disco: ', IFNULL(disco, 'N/D')) as detalles,
        falla_electrica, detalle_falla_electrica,
        falla_estetica, detalle_falla_estetica
        FROM bodega_diagnosticos WHERE inventario_id = ? 
        ORDER BY fecha_diagnostico DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inventario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['tecnico_nombre'] = getNombreTecnico($conn, $row['tecnico_id']);
    $historial[] = $row;
}
// 3. Mantenimiento
$sql = "SELECT 'MANTENIMIENTO' as tipo, fecha_registro as fecha, tecnico_id,
        observaciones_globales as observaciones, estado,
        CONCAT('Limpieza: ', IFNULL(limpieza_general, 'N/D'),
               ' | Mantenimiento Partes: ', IFNULL(mantenimiento_partes, 'N/D'),
               ' | Falla Eléctrica: ', IFNULL(falla_electrica, 'No'),
               ' | Falla Estética: ', IFNULL(falla_estetica, 'No'),
               ' | Cambio Piezas: ', IFNULL(cambio_piezas, 'No')) as detalles,
        detalle_falla_electrica, detalle_falla_estetica,
        piezas_solicitadas_cambiadas
        FROM bodega_mantenimiento WHERE inventario_id = ? 
        ORDER BY fecha_registro DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inventario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['tecnico_nombre'] = getNombreTecnico($conn, $row['tecnico_id']);
    $historial[] = $row;
}
// 4. Proceso Eléctrico
$sql = "SELECT 'ELECTRICO' as tipo, fecha_proceso as fecha, tecnico_id,
        observaciones, estado_final as estado,
        CONCAT('Batería: ', IFNULL(estado_bateria, 'N/D'),
               ' | Fuente: ', IFNULL(estado_fuente, 'N/D'),
               ' | Pantalla: ', IFNULL(estado_pantalla, 'N/D'),
               ' | Puertos: ', IFNULL(estado_puertos, 'N/D'),
               ' | Audio: ', IFNULL(estado_audio, 'N/D')) as detalles,
        fallas_detectadas, reparaciones_realizadas
        FROM bodega_electrico WHERE inventario_id = ? 
        ORDER BY fecha_proceso DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inventario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['tecnico_nombre'] = getNombreTecnico($conn, $row['tecnico_id']);
    $historial[] = $row;
}
// 5. Proceso Estético
$sql = "SELECT 'ESTETICO' as tipo, fecha_proceso as fecha, tecnico_id,
        observaciones, estado_final as estado,
        CONCAT('Grado: ', IFNULL(grado_asignado, 'N/D'), 
               ' | Carcasa: ', IFNULL(estado_carcasa, 'N/D'),
               ' | Pantalla Física: ', IFNULL(estado_pantalla_fisica, 'N/D'),
               ' | Limpieza: ', IFNULL(limpieza_realizada, 'No')) as detalles,
        partes_reemplazadas, rayones_golpes
        FROM bodega_estetico WHERE inventario_id = ? 
        ORDER BY fecha_proceso DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inventario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['tecnico_nombre'] = getNombreTecnico($conn, $row['tecnico_id']);
    $historial[] = $row;
}
// 6. Control de Calidad
$sql = "SELECT 'CONTROL_CALIDAD' as tipo, fecha_control as fecha, tecnico_id,
        observaciones, estado_final as estado,
        CONCAT('Burning Test: ', IFNULL(burning_test, 'N/D'),
               ' | Sentinel: ', IFNULL(sentinel_test, 'N/D'),
               ' | Categoría REC: ', IFNULL(categoria_rec, 'N/D')) as detalles
        FROM bodega_control_calidad WHERE inventario_id = ? 
        ORDER BY fecha_control DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inventario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['tecnico_nombre'] = getNombreTecnico($conn, $row['tecnico_id']);
    $historial[] = $row;
}
// 7. Log de Cambios
$sql = "SELECT 'CAMBIO' as tipo, fecha_cambio as fecha, usuario_id as tecnico_id,
        CONCAT('Campo: ', IFNULL(campo_modificado,'N/D'), 
               ' | De: ', IFNULL(LEFT(valor_anterior, 50),'N/D'), 
               ' | A: ', IFNULL(LEFT(valor_nuevo, 50),'N/D')) as observaciones,
        tipo_cambio as estado,
        CONCAT('Tipo: ', IFNULL(tipo_cambio, 'N/D')) as detalles,
        valor_anterior, valor_nuevo, campo_modificado
        FROM bodega_log_cambios WHERE inventario_id = ? 
        ORDER BY fecha_cambio DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inventario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['tecnico_nombre'] = getNombreTecnico($conn, $row['tecnico_id']);
    $historial[] = $row;
}
// Ordenar historial por fecha (más reciente primero)
usort($historial, function($a, $b) {
    return strtotime($b['fecha']) - strtotime($a['fecha']);
});
// Función para obtener el color del badge según el tipo de proceso
function getTipoBadge($tipo) {
    switch($tipo) {
        case 'ENTRADA': return 'badge-primary';
        case 'DIAGNOSTICO': return 'badge-warning';
        case 'MANTENIMIENTO': return 'badge-info';
        case 'ELECTRICO': return 'badge-danger';
        case 'ESTETICO': return 'badge-success';
        case 'CONTROL_CALIDAD': return 'badge-dark';
        case 'CAMBIO': return 'badge-secondary';
        default: return 'badge-light';
    }
}
function getEstadoBadge($estado) {
    if (!$estado) return '';
    switch(strtolower($estado)) {
        case 'aprobado': return 'badge-success';
        case 'realizado': return 'badge-success';
        case 'rechazado': return 'badge-danger';
        case 'pendiente': return 'badge-warning';
        case 'requiere_revision': return 'badge-warning';
        default: return 'badge-secondary';
    }
}
// Estadísticas
$tipos_count = [];
foreach ($historial as $evento) {
    $tipos_count[$evento['tipo']] = ($tipos_count[$evento['tipo']] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial de Trazabilidad - <?= htmlspecialchars($equipo['codigo_g']) ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #007bff 0%, #6f42c1 100%);
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }
        .timeline-item {
            position: relative;
            margin-bottom: 25px;
            padding-left: 70px;
        }
        .timeline-marker {
            position: absolute;
            left: 18px;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 2;
        }
        .timeline-marker.entrada { background: #007bff; }
        .timeline-marker.diagnostico { background: #ffc107; }
        .timeline-marker.mantenimiento { background: #17a2b8; }
        .timeline-marker.electrico { background: #dc3545; }
        .timeline-marker.estetico { background: #28a745; }
        .timeline-marker.control_calidad { background: #6c757d; }
        .timeline-marker.cambio { background: #6f42c1; }
        
        .timeline-content {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .timeline-content:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .timeline-content.entrada { border-left-color: #007bff; }
        .timeline-content.diagnostico { border-left-color: #ffc107; }
        .timeline-content.mantenimiento { border-left-color: #17a2b8; }
        .timeline-content.electrico { border-left-color: #dc3545; }
        .timeline-content.estetico { border-left-color: #28a745; }
        .timeline-content.control_calidad { border-left-color: #6c757d; }
        .timeline-content.cambio { border-left-color: #6f42c1; }
        
        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .timeline-date {
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .equipo-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .proceso-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.9rem;
            border: 1px solid #e9ecef;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .modal-body .detail-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .detail-section h6 {
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        @print {
            .timeline::before { background: #000 !important; }
            .timeline-content { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <!-- Información del Equipo -->
                <div class="equipo-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-3">
                                <span class="material-icons" style="vertical-align: middle; margin-right: 10px;">computer</span>
                                Historial de Trazabilidad Completo
                            </h2>
                            <h4><?= htmlspecialchars($equipo['marca']) ?> <?= htmlspecialchars($equipo['modelo']) ?></h4>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Código:</strong> <?= htmlspecialchars($equipo['codigo_g']) ?></p>
                                    <p class="mb-2"><strong>Serial:</strong> <?= htmlspecialchars($equipo['serial']) ?></p>
                                    <p class="mb-2"><strong>Ubicación:</strong> <?= htmlspecialchars($equipo['ubicacion']) ?> - <?= htmlspecialchars($equipo['posicion']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Lote:</strong> <?= htmlspecialchars($equipo['lote'] ?: 'No asignado') ?></p>
                                    <p class="mb-2"><strong>Procesador:</strong> <?= htmlspecialchars($equipo['procesador'] ?: 'No especificado') ?></p>
                                    <p class="mb-2"><strong>RAM:</strong> <?= htmlspecialchars($equipo['ram'] ?: 'No especificado') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="mb-2">
                                <span class="badge badge-info badge-lg"><?= htmlspecialchars($equipo['disposicion']) ?></span>
                            </div>
                            <div class="mb-2">
                                <span class="badge badge-<?= ($equipo['estado'] == 'activo') ? 'success' : 'danger' ?>"><?= htmlspecialchars($equipo['estado']) ?></span>
                            </div>
                            <div class="mb-2">
                                <span class="badge badge-secondary">Grado: <?= htmlspecialchars($equipo['grado']) ?></span>
                            </div>
                            <p class="mt-3 mb-0"><small>Ingresó: <?= date('d/m/Y', strtotime($equipo['fecha_ingreso'])) ?></small></p>
                        </div>
                    </div>
                </div>
                <!-- Botones de Navegación -->
                <div class="mb-3 d-flex justify-content-between">
                    <a href="lista_triage_2.php" class="btn btn-secondary">
                        <span class="material-icons" style="vertical-align: middle;">arrow_back</span>
                        Volver al Listado
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <span class="material-icons" style="vertical-align: middle;">print</span>
                        Imprimir Historial
                    </button>
                </div>
                <!-- Resumen Estadístico -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5>
                            <span class="material-icons" style="vertical-align: middle; margin-right: 10px;">analytics</span>
                            Resumen de Actividad (<?= count($historial) ?> eventos registrados)
                        </h5>
                    </div>
                    <?php foreach ($tipos_count as $tipo => $count): ?>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                            <div class="stats-card">
                                <div class="h3 mb-1"><?= $count ?></div>
                                <span class="badge <?= getTipoBadge($tipo) ?>"><?= htmlspecialchars($tipo) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Timeline del Historial -->
                <div class="timeline">                    
                    <?php if (empty($historial)): ?>
                        <div class="alert alert-info">
                            <span class="material-icons" style="vertical-align: middle; margin-right: 10px;">info</span>
                            No se encontraron registros de procesos para este equipo.
                        </div>
                    <?php else: ?>
                        <?php foreach ($historial as $index => $registro): ?>
                            <?php $tipo_lower = strtolower($registro['tipo']); ?>
                            <div class="timeline-item">
                                <div class="timeline-marker <?= $tipo_lower ?>"></div>
                                <div class="timeline-content <?= $tipo_lower ?>">
                                    <div class="timeline-header">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge <?= getTipoBadge($registro['tipo']) ?> mr-2">
                                                    <?= htmlspecialchars($registro['tipo']) ?>
                                                </span>
                                                <?php if ($registro['estado']): ?>
                                                    <span class="badge <?= getEstadoBadge($registro['estado']) ?>">
                                                        <?= htmlspecialchars($registro['estado']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </h6>
                                        </div>
                                        <div class="timeline-date">
                                            <span class="material-icons" style="font-size: 16px;">schedule</span>
                                            <?= date('d/m/Y H:i', strtotime($registro['fecha'])) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-8">
                                            <?php if ($registro['observaciones']): ?>
                                                <div class="mb-3">
                                                    <strong>Observaciones:</strong>
                                                    <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($registro['observaciones'])) ?></p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($registro['detalles']) && $registro['detalles']): ?>
                                                <div class="proceso-details">
                                                    <strong>Detalles del Proceso:</strong><br>
                                                    <small><?= htmlspecialchars($registro['detalles']) ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-right">
                                                <p class="mb-1">
                                                    <span class="material-icons" style="vertical-align: middle; font-size: 16px;">person</span>
                                                    <strong>Responsable:</strong>
                                                </p>
                                                <p class="mb-2"><?= htmlspecialchars($registro['tecnico_nombre']) ?></p>
                                                
                                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#detalleModal" 
                                                        data-evento='<?= json_encode($registro) ?>'>
                                                    <span class="material-icons" style="font-size: 16px;">visibility</span>
                                                    Ver Detalles
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para Ver Detalles -->
    <div class="modal fade" id="detalleModal" tabindex="-1" role="dialog" aria-labelledby="detalleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleModalLabel">Detalles del Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalDetalleContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        $('#detalleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var evento = button.data('evento');
            var content = '<div class="detail-section">';
            content += '<h6>Información General</h6>';
            content += '<div class="row">';
            content += '<div class="col-md-6"><strong>Tipo:</strong> ' + evento.tipo + '</div>';
            content += '<div class="col-md-6"><strong>Fecha:</strong> ' + evento.fecha + '</div>';
            content += '<div class="col-md-6"><strong>Responsable:</strong> ' + evento.tecnico_nombre + '</div>';
            if (evento.estado) {
                content += '<div class="col-md-6"><strong>Estado:</strong> ' + evento.estado + '</div>';
            }
            content += '</div></div>';
            if (evento.observaciones) {
                content += '<div class="detail-section">';
                content += '<h6>Observaciones</h6>';
                content += '<p>' + evento.observaciones.replace(/\n/g, '<br>') + '</p>';
                content += '</div>';
            }
            if (evento.detalles) {
                content += '<div class="detail-section">';
                content += '<h6>Detalles Técnicos</h6>';
                content += '<p>' + evento.detalles + '</p>';
                content += '</div>';
            }
            // Detalles específicos por tipo
            if (evento.tipo === 'DIAGNOSTICO') {
                content += '<div class="detail-section">';
                content += '<h6>Información de Fallas</h6>';
                if (evento.falla_electrica === 'si') {
                    content += '<div class="alert alert-warning"><strong>Falla Eléctrica:</strong> ' + (evento.detalle_falla_electrica || 'Sin detalles') + '</div>';
                }
                if (evento.falla_estetica === 'si') {
                    content += '<div class="alert alert-info"><strong>Falla Estética:</strong> ' + (evento.detalle_falla_estetica || 'Sin detalles') + '</div>';
                }
                content += '</div>';
            }
            if (evento.tipo === 'ELECTRICO' && (evento.fallas_detectadas || evento.reparaciones_realizadas)) {
                content += '<div class="detail-section">';
                content += '<h6>Reparaciones Eléctricas</h6>';
                if (evento.fallas_detectadas) {
                    content += '<p><strong>Fallas Detectadas:</strong> ' + evento.fallas_detectadas + '</p>';
                }
                if (evento.reparaciones_realizadas) {
                    content += '<p><strong>Reparaciones Realizadas:</strong> ' + evento.reparaciones_realizadas + '</p>';
                }
                content += '</div>';
            }
            $('#modalDetalleContent').html(content);
        });
    </script>
</body>
</html>