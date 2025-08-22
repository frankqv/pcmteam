<?php
session_start();
require_once '../../config/ctconex.php';

// Verificar sesión y obtener datos del usuario
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? '';

// Obtener estadísticas para las cards resumen
function getEstadisticas($pdo, $user_id, $user_role) {
    try {
        $where_clause = '';
        $params = [];
        
        // Si es técnico (roles 5,6,7), filtrar por técnico asignado
        if (in_array($user_role, ['1', '5', '6', '7'])) {
            $where_clause = ' AND i.tecnico_id = :user_id';
            $params['user_id'] = $user_id;
        }
        
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN i.disposicion = 'Para Venta' THEN 1 ELSE 0 END) as disponibles,
                    SUM(CASE WHEN i.disposicion IN ('En revisión', 'En Laboratorio') THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN i.disposicion IN ('en_diagnostico', 'en_proceso') THEN 1 ELSE 0 END) as pendientes
                FROM bodega_inventario i 
                WHERE i.estado = 'activo' $where_clause";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ['total' => 0, 'disponibles' => 0, 'en_proceso' => 0, 'pendientes' => 0];
    }
}

// Obtener técnicos para dropdown
function getTecnicos($pdo) {
    try {
        $sql = "SELECT id, nombre FROM usuarios WHERE rol IN ('5', '6', '7') AND estado = '1' ORDER BY nombre";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

$estadisticas = getEstadisticas($pdo, $user_id, $user_role);
$tecnicos = getTecnicos($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpieza y Mantenimiento - PCMARKET</title>
    
    <!-- CSS del proyecto existente -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/font.css">
    <link rel="stylesheet" href="../../backend/css/material-icons.css">
    <link rel="stylesheet" href="../../backend/css/datatables.min.css">
    <link rel="icon" href="../../backend/img/favicon.ico" type="image/x-icon">
    
    <!-- Estilos específicos del módulo -->
    <style>
        .ficha-panel {
            background: linear-gradient(135deg, #1e1e1e 0%, #2a2a2a 100%);
            border: 2px solid #00ffff;
            border-radius: 10px;
            color: #ffffff;
            min-height: 600px;
        }
        
        .mantenimiento-panel {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e1e1e 100%);
            border: 2px solid #00ffff;
            border-radius: 10px;
            color: #ffffff;
            min-height: 600px;
        }
        
        .panel-header {
            background: rgba(0, 255, 255, 0.1);
            border-bottom: 1px solid #00ffff;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .diagnostico-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 15px;
        }
        
        .diagnostico-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            border-left: 3px solid #00ffff;
        }
        
        .diagnostico-item .label {
            font-weight: bold;
            color: #00ffff;
        }
        
        .diagnostico-item .value {
            color: #ffffff;
        }
        
        .proceso-section {
            background: rgba(0, 255, 255, 0.05);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 8px;
            margin: 10px;
            padding: 15px;
        }
        
        .proceso-title {
            color: #00ffff;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(0, 255, 255, 0.3);
            padding-bottom: 5px;
        }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #00ffff;
            color: #ffffff;
            border-radius: 5px;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #00ffff;
            color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .btn-cyan {
            background: linear-gradient(45deg, #00ffff, #00cccc);
            border: none;
            color: #000000;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-cyan:hover {
            background: linear-gradient(45deg, #00cccc, #00ffff);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.4);
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-aprobado { background: #28a745; color: white; }
        .status-pendiente { background: #ffc107; color: black; }
        .status-rechazado { background: #dc3545; color: white; }
        
        .card-stats {
            background: linear-gradient(135deg, #1e1e1e 0%, #2a2a2a 100%);
            border: 1px solid #00ffff;
            color: #ffffff;
        }
        
        .modal-dark {
            background: rgba(0, 0, 0, 0.9);
        }
        
        .modal-dark .modal-content {
            background: linear-gradient(135deg, #1e1e1e 0%, #2a2a2a 100%);
            border: 2px solid #00ffff;
            color: #ffffff;
        }
        
        .table-dark-custom {
            background: #1e1e1e;
            color: #ffffff;
        }
        
        .table-dark-custom thead th {
            background: #00ffff;
            color: #000000;
            border-bottom: 2px solid #00cccc;
        }
        
        .table-dark-custom tbody tr:hover {
            background: rgba(0, 255, 255, 0.1);
        }
        
        .filter-section {
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid #00ffff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-4">
        <!-- Header del módulo -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="text-cyan">
                        <i class="material-icons">build</i>
                        LIMPIEZA Y MANTENIMIENTO
                    </h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-cyan" onclick="refreshData()">
                            <i class="material-icons">refresh</i> Actualizar
                        </button>
                        <button class="btn btn-outline-cyan" onclick="showBuscarEquipo()">
                            <i class="material-icons">search</i> Buscar Equipo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-stats bg-primary">
                    <div class="card-body text-center">
                        <h3 class="card-title"><?= $estadisticas['total'] ?></h3>
                        <p class="card-text">TOTAL EQUIPOS ASIGNADOS</p>
                        <i class="material-icons" style="font-size: 2rem;">computer</i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-success">
                    <div class="card-body text-center">
                        <h3 class="card-title"><?= $estadisticas['disponibles'] ?></h3>
                        <p class="card-text">EQUIPOS PROCESADOS</p>
                        <i class="material-icons" style="font-size: 2rem;">check_circle</i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-warning">
                    <div class="card-body text-center">
                        <h3 class="card-title"><?= $estadisticas['en_proceso'] ?></h3>
                        <p class="card-text">EN PROCESO</p>
                        <i class="material-icons" style="font-size: 2rem;">hourglass_empty</i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-danger">
                    <div class="card-body text-center">
                        <h3 class="card-title"><?= $estadisticas['pendientes'] ?></h3>
                        <p class="card-text">PENDIENTES</p>
                        <i class="material-icons" style="font-size: 2rem;">pending</i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-section">
                    <h5 class="text-cyan mb-3">
                        <i class="material-icons">filter_list</i> Filtros
                    </h5>
                    <div class="row">
                        <div class="col-md-2">
                            <label class="form-label text-cyan">Disposición</label>
                            <select class="form-select" id="filter_disposicion">
                                <option value="">Todas</option>
                                <option value="Para Venta">Para Venta</option>
                                <option value="En revisión">En revisión</option>
                                <option value="En Laboratorio">En Laboratorio</option>
                                <option value="en_diagnostico">En diagnóstico</option>
                                <option value="en_proceso">En proceso</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-cyan">Ubicación</label>
                            <select class="form-select" id="filter_ubicacion">
                                <option value="">Todas</option>
                                <option value="Principal">Principal</option>
                                <option value="Cúcuta">Cúcuta</option>
                                <option value="Medellín">Medellín</option>
                                <option value="Unilago">Unilago</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-cyan">Grado</label>
                            <select class="form-select" id="filter_grado">
                                <option value="">Todos</option>
                                <option value="A">Grado A</option>
                                <option value="B">Grado B</option>
                                <option value="C">Grado C</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-cyan">Producto</label>
                            <select class="form-select" id="filter_producto">
                                <option value="">Todos</option>
                                <option value="Portatil">Portátil</option>
                                <option value="Desktop">Desktop</option>
                                <option value="AIO">AIO</option>
                                <option value="Periferico">Periférico</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-cyan">Estado</label>
                            <select class="form-select" id="filter_estado">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button class="btn btn-cyan" onclick="aplicarFiltros()">
                                    <i class="material-icons">search</i> Aplicar
                                </button>
                                <button class="btn btn-outline-cyan" onclick="limpiarFiltros()">
                                    <i class="material-icons">clear</i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de equipos -->
        <div class="row">
            <div class="col-12">
                <div class="card card-stats">
                    <div class="card-body">
                        <table id="equipos_table" class="table table-dark-custom table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Serial</th>
                                    <th>Ubicación</th>
                                    <th>Disposición</th>
                                    <th>Grado</th>
                                    <th>Técnico</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargan via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ficha del equipo -->
    <div class="modal fade modal-dark" id="fichaEquipoModal" tabindex="-1" aria-labelledby="fichaEquipoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-cyan" id="fichaEquipoModalLabel">
                        <i class="material-icons">build</i> Limpieza y Mantenimiento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Panel izquierdo - Ficha del equipo -->
                        <div class="col-md-5">
                            <div class="ficha-panel h-100">
                                <div class="panel-header">
                                    <i class="material-icons">computer</i>
                                    FICHA DEL EQUIPO
                                </div>
                                <div class="p-3">
                                    <!-- Información básica -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-cyan fw-bold">CÓDIGO:</span>
                                            <span id="equipo_codigo">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-cyan fw-bold">SERIAL:</span>
                                            <span id="equipo_serial">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-cyan fw-bold">TÉCNICO ASIGNADO:</span>
                                            <span id="equipo_tecnico">-</span>
                                        </div>
                                    </div>

                                    <!-- Grid de diagnósticos -->
                                    <div class="diagnostico-grid">
                                        <div class="diagnostico-item">
                                            <span class="label">CÁMARA</span>
                                            <span class="value" id="diag_camara">N/A</span>
                                        </div>
                                        <div class="diagnostico-item">
                                            <span class="label">TECLADO</span>
                                            <span class="value" id="diag_teclado">N/A</span>
                                        </div>
                                        <div class="diagnostico-item">
                                            <span class="label">PARLANTES</span>
                                            <span class="value" id="diag_parlantes">N/A</span>
                                        </div>
                                        <div class="diagnostico-item">
                                            <span class="label">BATERÍA</span>
                                            <span class="value" id="diag_bateria">N/A</span>
                                        </div>
                                        <div class="diagnostico-item">
                                            <span class="label">MICRÓFONO</span>
                                            <span class="value" id="diag_microfono">N/A</span>
                                        </div>
                                        <div class="diagnostico-item">
                                            <span class="label">DISCO</span>
                                            <span class="value" id="diag_disco">N/A</span>
                                        </div>
                                        <div class="diagnostico-item">
                                            <span class="label">PANTALLA</span>
                                            <span class="value" id="diag_pantalla">N/A</span>
                                        </div>
                                        <div class="diagnostico-item">
                                            <span class="label">PUERTOS</span>
                                            <span class="value" id="diag_puertos">N/A</span>
                                        </div>
                                    </div>

                                    <!-- Observaciones del diagnóstico -->
                                    <div class="mt-3">
                                        <label class="text-cyan fw-bold mb-2">OBSERVACIONES:</label>
                                        <div class="p-3" style="background: rgba(255,255,255,0.1); border-radius: 5px; min-height: 100px;">
                                            <span id="diag_observaciones">N/A</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel derecho - Limpieza y Mantenimiento -->
                        <div class="col-md-7">
                            <div class="mantenimiento-panel h-100">
                                <div class="panel-header">
                                    <i class="material-icons">build</i>
                                    LIMPIEZA Y MANTENIMIENTO
                                </div>
                                <div class="p-3">
                                    <form id="mantenimientoForm">
                                        <input type="hidden" id="inventario_id" name="inventario_id">
                                        
                                        <!-- Procesos de limpieza -->
                                        <div class="proceso-section">
                                            <div class="proceso-title">
                                                <i class="material-icons">cleaning_services</i>
                                                PROCESO DE LIMPIEZA
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label text-cyan">Limpieza Electrónica</label>
                                                    <select class="form-select" name="limpieza_electronica">
                                                        <option value="PENDIENTE">PENDIENTE</option>
                                                        <option value="REALIZADA">REALIZADA</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-cyan">Cambio de Crema Disipadora</label>
                                                    <select class="form-select" name="cambio_crema">
                                                        <option value="PENDIENTE">PENDIENTE</option>
                                                        <option value="REALIZADA">REALIZADA</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Proceso de mantenimiento -->
                                        <div class="proceso-section">
                                            <div class="proceso-title">
                                                <i class="material-icons">build_circle</i>
                                                PROCESO DE MANTENIMIENTO
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-cyan">Mantenimiento de Partes</label>
                                                <select class="form-select" name="mantenimiento_partes">
                                                    <option value="NO">NO</option>
                                                    <option value="SI">SI</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-cyan">¿Qué piezas se cambiaron?</label>
                                                <textarea class="form-control" name="piezas_cambiadas" rows="2" placeholder="Detalle las piezas cambiadas..."></textarea>
                                            </div>
                                        </div>

                                        <!-- Proceso de reconstrucción -->
                                        <div class="proceso-section">
                                            <div class="proceso-title">
                                                <i class="material-icons">construction</i>
                                                PROCESO DE RECONSTRUCCIÓN
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label text-cyan">Reconstrucción</label>
                                                    <select class="form-select" name="reconstruccion">
                                                        <option value="NO">NO</option>
                                                        <option value="SI">SI</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-cyan">Parte Reconstruida</label>
                                                    <input type="text" class="form-control" name="parte_reconstruida" placeholder="Especifique la parte...">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Limpieza general y otra área -->
                                        <div class="proceso-section">
                                            <div class="proceso-title">
                                                <i class="material-icons">checklist</i>
                                                PROCESO DE LIMPIEZA GENERAL
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="form-label text-cyan">Limpieza General</label>
                                                    <select class="form-select" name="limpieza_general">
                                                        <option value="PENDIENTE">PENDIENTE</option>
                                                        <option value="REALIZADA">REALIZADA</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label text-cyan">¿Remite a otra área?</label>
                                                    <select class="form-select" name="remite_otra_area">
                                                        <option value="NO">NO</option>
                                                        <option value="SI">SI</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label text-cyan">¿A qué área(s) remite?</label>
                                                    <input type="text" class="form-control" name="area_remision" placeholder="Especifique el área...">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Información adicional -->
                                        <div class="proceso-section">
                                            <div class="proceso-title">
                                                <i class="material-icons">info</i>
                                                INFORMACIÓN ADICIONAL
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label text-cyan">Estado del Procedimiento</label>
                                                    <select class="form-select" name="estado_procedimiento">
                                                        <option value="PENDIENTE">PENDIENTE</option>
                                                        <option value="EN_PROCESO">EN PROCESO</option>
                                                        <option value="COMPLETADO">COMPLETADO</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-cyan">Técnico Responsable</label>
                                                    <select class="form-select" name="tecnico_responsable">
                                                        <option value="">Seleccionar técnico...</option>
                                                        <?php foreach ($tecnicos as $tecnico): ?>
                                                            <option value="<?= $tecnico['id'] ?>"><?= htmlspecialchars($tecnico['nombre'] ?? 'N/A') ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <label class="form-label text-cyan">Observaciones</label>
                                                <textarea class="form-control" name="observaciones" rows="3" placeholder="Observaciones adicionales del mantenimiento..."></textarea>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-cyan" data-bs-dismiss="modal">
                        <i class="material-icons">close</i> Cerrar
                    </button>
                    <button type="button" class="btn btn-cyan" onclick="guardarMantenimiento()">
                        <i class="material-icons">save</i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para buscar equipo -->
    <div class="modal fade modal-dark" id="buscarEquipoModal" tabindex="-1" aria-labelledby="buscarEquipoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-cyan" id="buscarEquipoModalLabel">
                        <i class="material-icons">search</i> Buscar Equipo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-cyan">Código del Equipo</label>
                        <input type="text" class="form-control" id="buscar_codigo" placeholder="Ingrese el código del equipo...">
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-cyan" onclick="buscarEquipoPorCodigo()">
                            <i class="material-icons">search</i> Buscar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts del proyecto existente -->
    <script src="../../backend/js/jquery.min.js"></script>
    <script src="../../backend/js/bootstrap.bundle.min.js"></script>
    <script src="../../backend/js/datatables.min.js"></script>
    <script src="../../backend/js/sweetalert2.min.js"></script>
    
    <!-- Script específico del módulo -->
    <script>
        let equiposTable;
        
        $(document).ready(function() {
            initDataTable();
            loadEquiposData();
        });
        
        // Inicializar DataTable
        function initDataTable() {
            equiposTable = $('#equipos_table').DataTable({
                responsive: true,
                language: {
                    url: '../../backend/js/datatables-spanish.json'
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="material-icons">content_copy</i> Copiar',
                        className: 'btn btn-outline-cyan btn-sm'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="material-icons">download</i> CSV',
                        className: 'btn btn-outline-cyan btn-sm'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="material-icons">download</i> Excel',
                        className: 'btn btn-outline-cyan btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="material-icons">picture_as_pdf</i> PDF',
                        className: 'btn btn-outline-cyan btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="material-icons">print</i> Imprimir',
                        className: 'btn btn-outline-cyan btn-sm'
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                columnDefs: [
                    {
                        targets: -1, // Columna de acciones
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }
        
        // Cargar datos de equipos
        function loadEquiposData() {
            $.ajax({
                url: '../../backend/php/get_equipos_mantenimiento.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        equiposTable.clear();
                        
                        response.data.forEach(function(equipo) {
                            const statusBadge = getStatusBadge(equipo.disposicion);
                            const gradeBadge = getGradeBadge(equipo.grado);
                            
                            equiposTable.row.add([
                                equipo.codigo_g,
                                equipo.marca || 'N/A',
                                equipo.modelo || 'N/A',
                                equipo.serial || 'N/A',
                                equipo.ubicacion || 'N/A',
                                statusBadge,
                                gradeBadge,
                                equipo.tecnico_nombre || 'Sin asignar',
                                equipo.estado === 'activo' ? '<span class="status-badge status-aprobado">Activo</span>' : '<span class="status-badge status-rechazado">Inactivo</span>',
                                generateActionButtons(equipo.id)
                            ]);
                        });
                        
                        equiposTable.draw();
                    } else {
                        showError('Error al cargar los datos: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showError('Error de conexión: ' + error);
                }
            });
        }
        
        // Generar badges de estado
        function getStatusBadge(disposicion) {
            const badges = {
                'Para Venta': '<span class="status-badge status-aprobado">Para Venta</span>',
                'En revisión': '<span class="status-badge status-pendiente">En Revisión</span>',
                'En Laboratorio': '<span class="status-badge status-pendiente">En Laboratorio</span>',
                'en_diagnostico': '<span class="status-badge status-rechazado">En Diagnóstico</span>',
                'en_proceso': '<span class="status-badge status-pendiente">En Proceso</span>'
            };
            return badges[disposicion] || '<span class="status-badge">'+disposicion+'</span>';
        }
        
        // Generar badges de grado
        function getGradeBadge(grado) {
            const badges = {
                'A': '<span class="badge bg-success">A</span>',
                'B': '<span class="badge bg-warning">B</span>',
                'C': '<span class="badge bg-danger">C</span>'
            };
            return badges[grado] || '<span class="badge bg-secondary">'+(grado || 'N/A')+'</span>';
        }
        
        // Generar botones de acción
        function generateActionButtons(id) {
            return `
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="verFichaEquipo(${id})" title="Ver ficha">
                        <i class="material-icons">visibility</i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="editarEquipo(${id})" title="Editar">
                        <i class="material-icons">edit</i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarEquipo(${id})" title="Eliminar">
                        <i class="material-icons">delete</i>
                    </button>
                </div>
            `;
        }
        
        // Ver ficha del equipo
        function verFichaEquipo(id) {
            $.ajax({
                url: '../../backend/php/get_inventario_details.php',
                type: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Llenar datos básicos
                        $('#inventario_id').val(id);
                        $('#equipo_codigo').text(data.codigo_g || 'N/A');
                        $('#equipo_serial').text(data.serial || 'N/A');
                        $('#equipo_tecnico').text(data.tecnico_nombre || 'Sin asignar');
                        
                        // Llenar datos de diagnóstico
                        if (data.diagnostico) {
                            $('#diag_camara').text(data.diagnostico.camara || 'N/A');
                            $('#diag_teclado').text(data.diagnostico.teclado || 'N/A');
                            $('#diag_parlantes').text(data.diagnostico.parlantes || 'N/A');
                            $('#diag_bateria').text(data.diagnostico.bateria || 'N/A');
                            $('#diag_microfono').text(data.diagnostico.microfono || 'N/A');
                            $('#diag_disco').text(data.diagnostico.disco || 'N/A');
                            $('#diag_pantalla').text(data.diagnostico.pantalla || 'N/A');
                            $('#diag_puertos').text(data.diagnostico.puertos || 'N/A');
                            $('#diag_observaciones').text(data.diagnostico.observaciones || 'N/A');
                        }
                        
                        // Llenar datos de control de calidad si existen
                        if (data.control_calidad) {
                            const cc = data.control_calidad;
                            $('[name="limpieza_electronica"]').val(cc.burning_test ? 'REALIZADA' : 'PENDIENTE');
                            $('[name="estado_procedimiento"]').val(cc.estado_final === 'aprobado' ? 'COMPLETADO' : 'PENDIENTE');
                            $('[name="observaciones"]').val(cc.observaciones || '');
                            $('[name="tecnico_responsable"]').val(cc.tecnico_id || '');
                        }
                        
                        $('#fichaEquipoModal').modal('show');
                    } else {
                        showError('Error al obtener los detalles: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showError('Error de conexión: ' + error);
                }
            });
        }
        
        // Guardar mantenimiento
        function guardarMantenimiento() {
            const formData = new FormData($('#mantenimientoForm')[0]);
            
            $.ajax({
                url: '../../backend/php/save_mantenimiento.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccess('Mantenimiento guardado exitosamente');
                        $('#fichaEquipoModal').modal('hide');
                        refreshData();
                    } else {
                        showError('Error al guardar: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showError('Error de conexión: ' + error);
                }
            });
        }
        
        // Editar equipo
        function editarEquipo(id) {
            window.location.href = 'editar_inventario.php?id=' + id;
        }
        
        // Eliminar equipo
        function eliminarEquipo(id) {
            Swal.fire({
                title: '¿Está seguro?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00ffff',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#1e1e1e',
                color: '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../../backend/php/delete_inventario.php',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showSuccess('Equipo eliminado exitosamente');
                                refreshData();
                            } else {
                                showError('Error al eliminar: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            showError('Error de conexión: ' + error);
                        }
                    });
                }
            });
        }
        
        // Aplicar filtros
        function aplicarFiltros() {
            const filtros = {
                disposicion: $('#filter_disposicion').val(),
                ubicacion: $('#filter_ubicacion').val(),
                grado: $('#filter_grado').val(),
                producto: $('#filter_producto').val(),
                estado: $('#filter_estado').val()
            };
            
            $.ajax({
                url: '../../backend/php/get_equipos_mantenimiento.php',
                type: 'GET',
                data: filtros,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        equiposTable.clear();
                        
                        response.data.forEach(function(equipo) {
                            const statusBadge = getStatusBadge(equipo.disposicion);
                            const gradeBadge = getGradeBadge(equipo.grado);
                            
                            equiposTable.row.add([
                                equipo.codigo_g,
                                equipo.marca || 'N/A',
                                equipo.modelo || 'N/A',
                                equipo.serial || 'N/A',
                                equipo.ubicacion || 'N/A',
                                statusBadge,
                                gradeBadge,
                                equipo.tecnico_nombre || 'Sin asignar',
                                equipo.estado === 'activo' ? '<span class="status-badge status-aprobado">Activo</span>' : '<span class="status-badge status-rechazado">Inactivo</span>',
                                generateActionButtons(equipo.id)
                            ]);
                        });
                        
                        equiposTable.draw();
                        showSuccess('Filtros aplicados exitosamente');
                    } else {
                        showError('Error al aplicar filtros: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showError('Error de conexión: ' + error);
                }
            });
        }
        
        // Limpiar filtros
        function limpiarFiltros() {
            $('#filter_disposicion, #filter_ubicacion, #filter_grado, #filter_producto, #filter_estado').val('');
            loadEquiposData();
            showInfo('Filtros limpiados');
        }
        
        // Mostrar modal de buscar equipo
        function showBuscarEquipo() {
            $('#buscarEquipoModal').modal('show');
        }
        
        // Buscar equipo por código
        function buscarEquipoPorCodigo() {
            const codigo = $('#buscar_codigo').val().trim();
            
            if (!codigo) {
                showError('Por favor ingrese un código');
                return;
            }
            
            $.ajax({
                url: '../../backend/php/get_inventario_details.php',
                type: 'GET',
                data: { codigo: codigo },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#buscarEquipoModal').modal('hide');
                        verFichaEquipo(response.data.id);
                    } else {
                        showError('Equipo no encontrado: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showError('Error de búsqueda: ' + error);
                }
            });
        }
        
        // Actualizar datos
        function refreshData() {
            loadEquiposData();
            // Actualizar estadísticas
            location.reload();
        }
        
        // Funciones de notificaciones
        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: message,
                background: '#1e1e1e',
                color: '#ffffff',
                confirmButtonColor: '#00ffff'
            });
        }
        
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                background: '#1e1e1e',
                color: '#ffffff',
                confirmButtonColor: '#dc3545'
            });
        }
        
        function showInfo(message) {
            Swal.fire({
                icon: 'info',
                title: 'Información',
                text: message,
                background: '#1e1e1e',
                color: '#ffffff',
                confirmButtonColor: '#00ffff'
            });
        }
        
        // Debounce para búsqueda en tiempo real
        let searchTimeout;
        $('#buscar_codigo').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                const codigo = $('#buscar_codigo').val().trim();
                if (codigo.length >= 3) {
                    // Búsqueda automática cuando hay 3+ caracteres
                    buscarEquipoPorCodigo();
                }
            }, 300);
        });
        
        // Enter en el campo de búsqueda
        $('#buscar_codigo').on('keypress', function(e) {
            if (e.which === 13) {
                buscarEquipoPorCodigo();
            }
        });
    </script>
</body>
</html>