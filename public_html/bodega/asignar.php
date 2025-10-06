<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('location: ../error404.php');
    exit();
}
require_once __DIR__ . '../../../config/ctconex.php';
// Procesar asignación AJAX - MODIFICADO para soportar múltiples equipos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    if ($_POST['action'] == 'assign_equipment') {
        while (ob_get_level()) {
            ob_end_clean();
        }
        // CAMBIO PRINCIPAL: Soportar array de equipos
        $equipoIds = [];
        if (isset($_POST['equipo_ids']) && is_array($_POST['equipo_ids'])) {
            // Modo múltiple
            $equipoIds = array_map('intval', $_POST['equipo_ids']);
            $equipoIds = array_filter($equipoIds, function ($id) {
                return $id > 0;
            });
        } elseif (isset($_POST['equipo_id']) && $_POST['equipo_id'] > 0) {
            // Modo individual (compatibilidad hacia atrás)
            $equipoIds = [intval($_POST['equipo_id'])];
        }
        $tecnicoId = intval($_POST['tecnico_id'] ?? 0);
        $tipo = trim($_POST['tipo'] ?? 'triage');
        $usuarioId = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
        if (empty($equipoIds) || $tecnicoId <= 0 || $usuarioId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parámetros inválidos (equipo/tecnico/sesión).']);
            exit();
        }
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn->begin_transaction();
            $disposicion = ($tipo === 'triage' || $tipo === 'triage') ? 'en_diagnostico' : 'en_proceso';
            $equiposAsignados = [];
            $equiposFallidos = [];
            // Procesar cada equipo
            foreach ($equipoIds as $equipoId) {
                try {
                    // UPDATE inventario
                    $stmt = $conn->prepare("UPDATE bodega_inventario SET tecnico_id = ?, disposicion = ?, fecha_modificacion = NOW() WHERE id = ?");
                    $stmt->bind_param("isi", $tecnicoId, $disposicion, $equipoId);
                    $stmt->execute();
                    $affected = $stmt->affected_rows;
                    $stmt->close();
                    if ($affected <= 0) {
                        throw new Exception("No se pudo actualizar el inventario ID: {$equipoId}");
                    }
                    // INSERT en bodega_salidas (log)
                    $stmt2 = $conn->prepare("INSERT INTO bodega_salidas (inventario_id, tecnico_id, usuario_id, razon_salida, observaciones) VALUES (?, ?, ?, ?, ?)");
                    $razon = "Asignación para " . $tipo;
                    $observaciones = "Asignado desde dashboard por usuario ID: " . $usuarioId;
                    $stmt2->bind_param("iiiss", $equipoId, $tecnicoId, $usuarioId, $razon, $observaciones);
                    $stmt2->execute();
                    $stmt2->close();
                    $equiposAsignados[] = $equipoId;
                } catch (Exception $e) {
                    $equiposFallidos[] = $equipoId;
                    error_log("Error asignando equipo {$equipoId}: " . $e->getMessage());
                }
            }
            $conn->commit();
            // Preparar respuesta
            $totalAsignados = count($equiposAsignados);
            $totalFallidos = count($equiposFallidos);
            if ($totalAsignados > 0 && $totalFallidos == 0) {
                // Todos exitosos
                $message = "DATOS REGISTRADOS CORRECTAMENTE. Equipos asignados: " . implode(', ', $equiposAsignados);
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => $message]);
            } elseif ($totalAsignados > 0 && $totalFallidos > 0) {
                // Parcialmente exitoso
                $message = "Asignados: " . implode(', ', $equiposAsignados) . ". Fallaron: " . implode(', ', $equiposFallidos);
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => $message, 'partial' => true]);
            } else {
                // Todos fallaron
                throw new Exception('No se pudo asignar ningún equipo.');
            }
            exit();
        } catch (Throwable $e) {
            try {
                if ($conn && $conn->connect_errno === 0)
                    $conn->rollback();
            } catch (Throwable $_e) {
                error_log('Rollback falló: ' . $_e->getMessage());
            }
            error_log("Asignar equipos error: " . $e->getMessage());
            $errorMessage = 'Error al asignar equipos. Intente nuevamente.';
            if (stripos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorMessage = 'Uno o más equipos ya están asignados a este técnico.';
            } elseif (stripos($e->getMessage(), 'foreign key') !== false) {
                $errorMessage = 'Error de referencia. Verifique que el técnico y equipos existan.';
            }
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            exit();
        }
    }
    // NUEVO: Endpoint para obtener estadísticas actualizadas
    if ($_POST['action'] == 'get_stats') {
        try {
            $techStats = [];
            // CAMBIO 1: Modificar GROUP_CONCAT para incluir código y serial en la consulta de AJAX.
            $sqlStats = "SELECT u.id, u.nombre, COUNT(bi.id) as total_equipos, GROUP_CONCAT(CONCAT(bi.codigo_g, ' (SN: ', bi.serial, ')') SEPARATOR '|') as equipos_asignados
                        FROM usuarios u 
                        LEFT JOIN bodega_inventario bi ON u.id = bi.tecnico_id 
                        WHERE u.rol IN ('1','5','6','7') AND u.estado = '1'
                        GROUP BY u.id, u.nombre 
                        ORDER BY total_equipos DESC";
            $resultStats = $conn->query($sqlStats);
            if ($resultStats) {
                while ($rowStats = $resultStats->fetch_assoc()) {
                    $techStats[] = $rowStats;
                }
            }
            echo json_encode($techStats);
            exit();
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
            echo json_encode([]);
            exit();
        }
    }
    // NUEVO: Endpoint para obtener equipos actualizados
    if ($_POST['action'] == 'get_equipos') {
        try {
            $tipo = $_POST['tipo'] ?? 'disponibles';
            $equipos = [];
            if ($tipo === 'disponibles') {
                $sqlEquipos = "SELECT id, codigo_g, producto, marca, modelo 
                    FROM bodega_inventario 
                    WHERE (tecnico_id IS NULL OR tecnico_id = 0) AND estado = 'activo'
                    ORDER BY fecha_ingreso";
            } else {
                $sqlEquipos = "SELECT id, codigo_g, producto, marca, modelo 
                    FROM bodega_inventario 
                    WHERE disposicion IN ('en_diagnostico', 'Business Room') 
                    ORDER BY fecha_modificacion";
            }
            $resultEquipos = $conn->query($sqlEquipos);
            if ($resultEquipos) {
                while ($rowEquipo = $resultEquipos->fetch_assoc()) {
                    $equipos[] = $rowEquipo;
                }
            }
            echo json_encode($equipos);
            exit();
        } catch (Exception $e) {
            error_log("Error obteniendo equipos: " . $e->getMessage());
            echo json_encode([]);
            exit();
        }
    }
}
// Obtener técnicos
$tecnicos = [];
$resultTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN ('1','5','6','7') AND estado = '1'");
if ($resultTec) {
    while ($rowTec = $resultTec->fetch_assoc()) {
        $tecnicos[] = $rowTec;
    }
}
// Obtener estadísticas de equipos asignados por técnico
$techStats = [];
// CAMBIO 2: Modificar GROUP_CONCAT para incluir código y serial en la carga inicial de la página.
$sqlStats = "SELECT u.id, u.nombre, COUNT(bi.id) as total_equipos, GROUP_CONCAT(CONCAT(bi.codigo_g, ' (SN: ', bi.serial, ')') SEPARATOR '|') as equipos_asignados
        FROM usuarios u 
        LEFT JOIN bodega_inventario bi ON u.id = bi.tecnico_id 
        WHERE u.rol IN ('1','5','6','7') AND u.estado = '1'
        GROUP BY u.id, u.nombre 
        ORDER BY total_equipos DESC";
$resultStats = $conn->query($sqlStats);
if ($resultStats) {
    while ($rowStats = $resultStats->fetch_assoc()) {
        $techStats[] = $rowStats;
    }
}
// Obtener equipos disponibles para asignar
$equiposDisponibles = [];
// CAMBIO 3: Añadir la columna 'serial' a la consulta de equipos disponibles.
$sqlEquipos = "SELECT id, codigo_g, producto, marca, modelo, serial
        FROM bodega_inventario 
        WHERE (tecnico_id IS NULL OR tecnico_id = 0) AND estado = 'activo'
        ORDER BY fecha_ingreso";
$resultEquipos = $conn->query($sqlEquipos);
if ($resultEquipos) {
    while ($rowEquipo = $resultEquipos->fetch_assoc()) {
        $equiposDisponibles[] = $rowEquipo;
    }
}
?>
<?php if (isset($_SESSION['id'])) { ?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <title>Dashboard Asignación Equipos - PCMARKETTEAM</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/custom.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
        <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #1a1a1a;
                color: white;
                overflow-x: hidden;
            }
            .container-fluid {
                padding: 20px;
                max-width: 1400px;
                margin: 0 auto;
            }
            .dashboard-grid {
                display: grid;
                grid-template-columns: 1fr 300px 1fr;
                gap: 20px;
                min-height: 100vh;
                align-items: start;
            }
            .panel {
                background: #2a2a2a;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                border: 2px solid transparent;
                position: relative;
            }
            .panel-triage {
                border-color: #FFA500;
            }
            .panel-center {
                background: #1e1e1e;
                border: none;
            }
            .panel-process {
                border-color: #00FF00;
            }
            .panel-header {
                background: #000;
                color: white;
                padding: 20px;
                text-align: center;
                font-size: 18px;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .panel-content {
                padding: 25px;
            }
            .equipment-section {
                margin-bottom: 25px;
            }
            /* NUEVOS ESTILOS PARA SELECCIÓN MÚLTIPLE */
            .equipment-list {
                max-height: 300px;
                overflow-y: auto;
                border: 1px solid #555;
                border-radius: 8px;
                background: #3a3a3a;
                padding: 10px;
                margin-bottom: 15px;
            }
            .equipment-item {
                display: flex;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #555;
                font-size: 14px;
            }
            .equipment-item:last-child {
                border-bottom: none;
            }
            .equipment-item input[type="checkbox"] {
                margin-right: 10px;
                transform: scale(1.2);
            }
            .equipment-item label {
                margin: 0;
                cursor: pointer;
                flex: 1;
            }
            .selection-controls {
                display: flex;
                gap: 10px;
                margin-bottom: 15px;
            }
            .selection-controls button {
                padding: 5px 10px;
                background: #555;
                border: none;
                border-radius: 4px;
                color: white;
                font-size: 12px;
                cursor: pointer;
            }
            .selection-controls button:hover {
                background: #666;
            }
            /* Estilos para la barra de búsqueda */
            .search-container {
                margin-bottom: 15px;
                position: relative;
            }
            .search-input {
                width: 100%;
                padding: 8px 35px 8px 12px;
                /* Espacio extra a la derecha para el botón */
                background: #3a3a3a;
                border: 1px solid #555;
                border-radius: 6px;
                color: white;
                font-size: 13px;
                transition: all 0.3s;
            }
            .search-input:focus {
                outline: none;
                border-color: #FFA500;
                box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.2);
            }
            .search-input::placeholder {
                color: #999;
                font-style: italic;
            }
            /* Botón de limpiar búsqueda */
            .search-clear-btn {
                position: absolute;
                right: 8px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: #999;
                cursor: pointer;
                font-size: 16px;
                padding: 4px;
                border-radius: 3px;
                transition: all 0.2s;
                display: none;
                /* Oculto por defecto */
                z-index: 1;
            }
            .search-clear-btn:hover {
                color: #fff;
                background: rgba(255, 255, 255, 0.1);
            }
            .search-clear-btn:active {
                transform: translateY(-50%) scale(0.95);
            }
            /* Mostrar botón cuando hay texto */
            .search-container.has-text .search-clear-btn {
                display: block;
            }
            .search-results-info {
                font-size: 11px;
                color: #999;
                margin-bottom: 8px;
                display: none;
            }
            .equipment-item.hidden {
                display: none;
            }
            .no-results {
                text-align: center;
                color: #999;
                font-style: italic;
                padding: 20px;
                display: none;
            }
            .selected-count {
                color: #FFA500;
                font-weight: bold;
                margin-bottom: 10px;
                font-size: 14px;
            }
            .equipment-input,
            .tech-select {
                width: 100%;
                padding: 12px 15px;
                background: #3a3a3a;
                border: 1px solid #555;
                border-radius: 8px;
                color: white;
                font-size: 14px;
                margin-bottom: 15px;
            }
            .equipment-input:focus,
            .tech-select:focus {
                outline: none;
                border-color: #FFA500;
                box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.2);
            }
            .assign-btn {
                width: 100%;
                padding: 15px;
                background: linear-gradient(135deg, #FFA500, #FF8C00);
                border: none;
                border-radius: 10px;
                color: white;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                text-transform: uppercase;
            }
            .assign-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(255, 165, 0, 0.3);
            }
            .assign-btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }
            .assign-btn-process {
                background: linear-gradient(135deg, #00FF00, #00CC00);
            }
            .assign-btn-process:hover {
                box-shadow: 0 8px 25px rgba(0, 255, 0, 0.3);
            }
            .chart-container {
                background: #2a2a2a;
                padding: 30px;
                border-radius: 15px;
                text-align: center;
                margin-bottom: 30px;
            }
            .chart-title {
                color: white;
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 20px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .chart-wrapper {
                width: 200px;
                height: 200px;
                margin: 0 auto 20px;
                position: relative;
            }
            .chart-time {
                font-size: 14px;
                color: #999;
            }
            .tech-stats {
                background: #2a2a2a;
                border-radius: 10px;
                padding: 20px;
                max-height: 400px;
                overflow-y: auto;
            }
            .tech-row-container {
                border-bottom: 1px solid #444;
            }
            .tech-row-container:last-child {
                 border-bottom: none;
            }
            .tech-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
            }
            .tech-name {
                color: white;
                font-weight: 500;
            }
            .tech-count {
                background: #444;
                padding: 5px 12px;
                border-radius: 15px;
                font-size: 14px;
                font-weight: bold;
            }
            .tech-btn {
                cursor: pointer;
                user-select: none;
                color: #fff;
                transition: color 0.2s;
            }
            .tech-btn:hover {
                color: #68c5ff;
            }
            .tech-btn.active {
                transform: rotate(180deg);
            }
            .tech-equipment-details {
                display: none;
                padding: 10px;
                background-color: #333;
                border-radius: 5px;
                margin-top: 5px;
            }
             .tech-equipment-search {
                width: 100%;
                padding: 5px;
                margin-bottom: 10px;
                background: #444;
                border: 1px solid #555;
                color: white;
                border-radius: 4px;
            }
            .tech-equipment-list {
                list-style: none;
                padding: 0;
                margin: 0;
                max-height: 150px;
                overflow-y: auto;
            }
            .tech-equipment-list li {
                padding: 3px 5px;
                font-size: 12px;
                color: #ccc;
            }

            .process-info {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }
            .process-count {
                font-size: 24px;
                font-weight: bold;
                color: #00FF00;
            }
            .process-stage {
                font-size: 12px;
                color: #999;
                text-transform: uppercase;
            }
            .na-text {
                color: #666;
                font-style: italic;
            }
            .robot-icon {
                width: 30px;
                height: 30px;
                background: white;
                border-radius: 5px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-right: 10px;
            }
            .robot-icon::before {
                content: '🤖';
                font-size: 16px;
            }
            .alert {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                min-width: 300px;
                padding: 15px;
                border-radius: 5px;
                color: white;
                font-weight: bold;
                animation: slideIn 0.3s ease-out;
            }
            .alert-success {
                background: #28a745;
                border-left: 4px solid #20692a;
            }
            .alert-danger {
                background: #dc3545;
                border-left: 4px solid #b52d3a;
            }
            .alert-warning {
                background: #ffc107;
                color: #000;
                border-left: 4px solid #d39e00;
            }
            .close {
                float: right;
                font-size: 20px;
                cursor: pointer;
                background: none;
                border: none;
                color: inherit;
                opacity: 0.8;
            }
            .close:hover {
                opacity: 1;
            }
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                }
                to {
                    transform: translateX(0);
                }
            }
            @media (max-width: 1200px) {
                .dashboard-grid {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
            }
            .loading {
                opacity: 0.6;
                pointer-events: none;
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
                <?php renderMenu($menu); ?>
            </nav>
            <?php
            // Verificar sesión
            if (!isset($_SESSION['id'])) {
                header("Location: ../login.php");
                exit;
            }
            $id_usuario = $_SESSION['id'];
            // Consultar datos del usuario
            $stmt = $connect->prepare("SELECT nombre, usuario, correo, foto, idsede, rol 
            FROM usuarios 
            WHERE id = ?");
            $stmt->execute([$id_usuario]);
            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            // Si no se encuentra el usuario, redirigir
            if (!$userInfo) {
                header("Location: ../login.php");
                exit;
            }
            // Título según rol
            $titulos = [
                '1' => 'ADMINISTRADOR',
                '2' => 'DEFAULT',
                '3' => 'CONTABLE',
                '4' => 'COMERCIAL',
                '5' => 'JEFE TÉCNICO',
                '6' => 'TÉCNICO',
                '7' => 'BODEGA'
            ];
            $titulo = $titulos[$userInfo['rol']] ?? $userInfo['nombre'];
            ?>
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background:rgb(250, 107, 107);">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                    </div>
                    <a class="navbar-brand" href="#" style="color: var(--text-primary); font-weight: 600;">
                        <i class="fas fa-tools" style="margin-right: 8px; color: var(--accent-orange);"></i>
                        ASIGNACION TECNICO | EQUIPOS | <?php echo htmlspecialchars($titulo); ?>
                    </a>
                    <i class="material-icons">notifications</i>
                    <ul class="nav navbar-nav ml-auto">
                        <li class="dropdown nav-item active">
                            <a href="#" class="nav-link" data-toggle="dropdown">
                                <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'default.png'); ?>"
                                    alt="Foto de perfil" style="width: 30px; height: 30px; border-radius: 50%;">
                            </a>
                            <ul class="dropdown-menu p-3">
                                <li><strong><?php echo htmlspecialchars($userInfo['nombre']); ?></strong></li>
                                <li><?php echo htmlspecialchars($userInfo['usuario']); ?></li>
                                <li><?php echo htmlspecialchars($userInfo['correo']); ?></li>
                                <li><?php echo htmlspecialchars(trim($userInfo['idsede'] ?? '') !== '' ? $userInfo['idsede'] : 'Sede sin definir'); ?></li>
                                <li class="mt-2">
                                    <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary">Mi perfil</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="material-icons">more_vert</span>
                    </button>
                </nav>
            </div>
            <div id="content">
                <div class="container-fluid">
                    <div class="dashboard-grid">
                        <div class="panel panel-triage">
                            <div class="panel-header">
                                ASIGNACION EQUIPOS PARA TRIAGE
                            </div>
                            <div class="panel-content">
                                <form id="triageForm">
                                    <div class="equipment-section">
                                        <div class="search-container" id="triageSearchContainer">
                                            <input type="text"
                                                class="search-input"
                                                id="triageSearchInput"
                                                placeholder="🔍 Buscar por código, serial, producto o marca..."
                                                oninput="handleSearchInput('triage')">
                                            <button type="button" class="search-clear-btn" id="triageSearchClear"
                                                onclick="clearSearch('triage')" title="Limpiar búsqueda" style="color:#FFA500">
                                                Limpiar
                                            </button>
                                            <div class="search-results-info" id="triageSearchInfo"></div>
                                        </div>
                                        <div class="selection-controls">
                                            <button type="button" onclick="selectAllEquipment('triage')">Seleccionar Todos</button>
                                            <button type="button" onclick="selectVisibleEquipment('triage')">Seleccionar Visibles</button>
                                            <button type="button" onclick="clearSelectionEquipment('triage')">Limpiar Selección</button>
                                        </div>
                                        <div class="selected-count" id="triageSelectedCount">
                                            Equipos seleccionados: 0
                                        </div>
                                        <div class="equipment-list" id="triageEquipmentList">
                                            <?php foreach ($equiposDisponibles as $equipo): ?>
                                                <div class="equipment-item" data-search="<?php echo strtolower($equipo['codigo_g'] . ' ' . $equipo['serial'] . ' ' . $equipo['producto'] . ' ' . $equipo['marca'] . ' ' . $equipo['modelo']); ?>">
                                                    <input type="checkbox"
                                                        name="triage_equipos[]"
                                                        value="<?php echo $equipo['id']; ?>"
                                                        id="triage_equipo_<?php echo $equipo['id']; ?>"
                                                        onchange="updateSelectedCount('triage')">
                                                    <label for="triage_equipo_<?php echo $equipo['id']; ?>">
                                                        <?php echo htmlspecialchars($equipo['codigo_g'] . ' - ' . $equipo['producto'] . ' ' . $equipo['marca']); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                            <div class="no-results" id="triageNoResults">
                                                No se encontraron equipos que coincidan con la búsqueda
                                            </div>
                                        </div>
                                        <select class="tech-select" id="technicianSelect" required>
                                            <option value="">TÉCNICO</option>
                                            <?php foreach ($tecnicos as $tecnico): ?>
                                                <option value="<?php echo $tecnico['id']; ?>">
                                                    <?php echo strtoupper(htmlspecialchars($tecnico['nombre'])); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="assign-btn" id="triageBtn">
                                        <div class="robot-icon"></div>
                                        ASIGNAR
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="panel panel-center">
                            <div class="chart-container">
                                <div class="chart-title">
                                    TOTAL EQUIPOS ASIGNADOS<br>POR TÉCNICO
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="techChart"></canvas>
                                </div>
                                <div class="chart-time"><?php echo date('j-M'); ?></div>
                            </div>
                            <div class="tech-stats" id="techStats">
                                <?php foreach ($techStats as $stat): ?>
                                <div class="tech-row-container">
                                    <div class="tech-row">
                                        <span class="tech-name"><?php echo strtoupper(htmlspecialchars($stat['nombre'])); ?></span>
                                        <span class="tech-count"><?php echo $stat['total_equipos']; ?></span>
                                        <span class="tech-btn" data-target="tech-details-<?php echo $stat['id']; ?>">▼</span>
                                    </div>
                                    <div class="tech-equipment-details" id="tech-details-<?php echo $stat['id']; ?>">
                                        <input type="text" class="tech-equipment-search" placeholder="Buscar por código o serial...">
                                        <ul class="tech-equipment-list">
                                            <?php
                                            if (!empty($stat['equipos_asignados'])) {
                                                $equipos_lista = explode('|', $stat['equipos_asignados']);
                                                foreach ($equipos_lista as $codigo_equipo) {
                                                    echo '<li>' . htmlspecialchars($codigo_equipo) . '</li>';
                                                }
                                            } else {
                                                echo '<li>No hay equipos asignados.</li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="panel panel-process">
                            <div class="panel-header">
                                ASIGNACION EQUIPOS PROCESO
                            </div>
                            <div class="panel-content">
                                <form id="processForm">
                                    <div class="equipment-section">
                                        <div class="search-container" id="processSearchContainer">
                                            <input type="text"
                                                class="search-input"
                                                id="processSearchInput"
                                                placeholder="🔍 Buscar por código, serial, producto o marca..."
                                                oninput="handleSearchInput('process')">
                                            <button type="button" class="search-clear-btn" id="processSearchClear"
                                                onclick="clearSearch('process')" title="Limpiar búsqueda" style="color:#00CC00">
                                                Limpiar
                                            </button>
                                            <div class="search-results-info" id="processSearchInfo"></div>
                                        </div>
                                        <div class="selection-controls">
                                            <button type="button" onclick="selectAllEquipment('process')">Seleccionar Todos</button>
                                            <button type="button" onclick="selectVisibleEquipment('process')">Seleccionar Visibles</button>
                                            <button type="button" onclick="clearSelectionEquipment('process')">Limpiar Selección</button>
                                        </div>
                                        <div class="selected-count" id="processSelectedCount">
                                            Equipos seleccionados: 0
                                        </div>
                                        <div class="equipment-list" id="processEquipmentList">
                                            <?php
                                            // Equipos en diagnóstico para proceso
                                            // CAMBIO 5: Añadir la columna 'serial' a la consulta de equipos en proceso.
                                            $sqlProceso = "SELECT id, codigo_g, producto, marca, modelo, serial 
                                            FROM bodega_inventario 
                                            WHERE disposicion IN ('en_diagnostico', 'Business Room') 
                                            ORDER BY fecha_modificacion";
                                            $resultProceso = $conn->query($sqlProceso);
                                            if ($resultProceso) {
                                                while ($equipoProceso = $resultProceso->fetch_assoc()): ?>
                                                    <div class="equipment-item" data-search="<?php echo strtolower($equipoProceso['codigo_g'] . ' ' . $equipoProceso['serial'] . ' ' . $equipoProceso['producto'] . ' ' . $equipoProceso['marca'] . ' ' . $equipoProceso['modelo']); ?>">
                                                        <input type="checkbox"
                                                            name="process_equipos[]"
                                                            value="<?php echo $equipoProceso['id']; ?>"
                                                            id="process_equipo_<?php echo $equipoProceso['id']; ?>"
                                                            onchange="updateSelectedCount('process')">
                                                        <label for="process_equipo_<?php echo $equipoProceso['id']; ?>">
                                                            <?php echo htmlspecialchars($equipoProceso['codigo_g'] . ' - ' . $equipoProceso['producto'] . ' ' . $equipoProceso['marca']); ?>
                                                        </label>
                                                    </div>
                                            <?php endwhile;
                                            } ?>
                                            <div class="no-results" id="processNoResults">
                                                No se encontraron equipos que coincidan con la búsqueda
                                            </div>
                                        </div>
                                        <select class="tech-select" id="processTechnicianSelect" required>
                                            <option value="">TÉCNICO</option>
                                            <?php foreach ($tecnicos as $tecnico): ?>
                                                <option value="<?php echo $tecnico['id']; ?>">
                                                    <?php echo strtoupper(htmlspecialchars($tecnico['nombre'])); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="process-info">
                                        <div>
                                            <?php
                                            $sqlProcesoCont = "SELECT COUNT(*) as total FROM bodega_inventario WHERE disposicion = 'en_proceso'";
                                            $resultProcesoCont = $conn->query($sqlProcesoCont);
                                            $procesoCont = $resultProcesoCont ? $resultProcesoCont->fetch_assoc() : ['total' => 0];
                                            ?>
                                            <div class="process-count"><?php echo $procesoCont['total']; ?></div>
                                        </div>
                                    </div>
                                    <div class="process-info">
                                        <span class="process-stage">2° TRIAGE</span>
                                    </div>
                                    <div class="process-info">
                                        <span class="na-text">#N/A</span>
                                        <span class="na-text">#N/A</span>
                                    </div>
                                    <button type="submit" class="assign-btn assign-btn-process" id="processBtn">
                                        <div class="robot-icon"></div>
                                        ASIGNAR
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="alertContainer"></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <script>
                    // Datos iniciales de técnicos desde PHP
                    let technicianData = {};
                    <?php foreach ($techStats as $stat): ?>
                        technicianData['<?php echo addslashes($stat['nombre']); ?>'] = <?php echo $stat['total_equipos']; ?>;
                    <?php endforeach; ?>
                    // FUNCIONES PARA SELECCIÓN MÚLTIPLE
                    function selectAllEquipment(type) {
                        const checkboxes = document.querySelectorAll(`input[name="${type}_equipos[]"]`);
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = true;
                        });
                        updateSelectedCount(type);
                    }
                    // Nueva función para seleccionar solo equipos visibles
                    function selectVisibleEquipment(type) {
                        const checkboxes = document.querySelectorAll(`input[name="${type}_equipos[]"]`);
                        checkboxes.forEach(checkbox => {
                            const equipmentItem = checkbox.closest('.equipment-item');
                            if (equipmentItem && !equipmentItem.classList.contains('hidden')) {
                                checkbox.checked = true;
                            }
                        });
                        updateSelectedCount(type);
                    }
                    function clearSelectionEquipment(type) {
                        const checkboxes = document.querySelectorAll(`input[name="${type}_equipos[]"]`);
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        updateSelectedCount(type);
                    }
                    // FUNCIÓN DE FILTRADO DE EQUIPOS - MODIFICADA para manejar el botón de limpiar
                    function filterEquipment(type) {
                        const searchInput = document.getElementById(`${type}SearchInput`);
                        const searchTerm = searchInput.value.toLowerCase().trim();
                        const equipmentItems = document.querySelectorAll(`#${type}EquipmentList .equipment-item[data-search]`);
                        const noResultsElement = document.getElementById(`${type}NoResults`);
                        const searchInfo = document.getElementById(`${type}SearchInfo`);
                        const searchContainer = document.getElementById(`${type}SearchContainer`);
                        let visibleCount = 0;
                        let totalCount = equipmentItems.length;
                        // Mostrar/ocultar botón de limpiar según si hay texto
                        if (searchTerm !== '') {
                            searchContainer.classList.add('has-text');
                        } else {
                            searchContainer.classList.remove('has-text');
                        }
                        equipmentItems.forEach(item => {
                            const searchData = item.getAttribute('data-search') || '';
                            const matches = searchData.includes(searchTerm);
                            if (matches) {
                                item.classList.remove('hidden');
                                visibleCount++;
                            } else {
                                item.classList.add('hidden');
                            }
                        });
                        // Mostrar/ocultar mensaje "sin resultados"
                        if (visibleCount === 0 && searchTerm !== '') {
                            noResultsElement.style.display = 'block';
                        } else {
                            noResultsElement.style.display = 'none';
                        }
                        // Mostrar información de búsqueda
                        if (searchTerm !== '') {
                            searchInfo.style.display = 'block';
                            searchInfo.textContent = `Mostrando ${visibleCount} de ${totalCount} equipos`;
                            // Cambiar color según resultados
                            if (visibleCount === 0) {
                                searchInfo.style.color = '#dc3545';
                            } else if (visibleCount < totalCount) {
                                searchInfo.style.color = '#ffc107';
                            } else {
                                searchInfo.style.color = '#28a745';
                            }
                        } else {
                            searchInfo.style.display = 'none';
                        }
                        // Actualizar contador después del filtrado
                        updateSelectedCount(type);
                    }
                    // Nueva función para manejar el input y mostrar/ocultar el botón X
                    function handleSearchInput(type) {
                        filterEquipment(type);
                    }
                    // Función para limpiar búsqueda - MEJORADA
                    function clearSearch(type) {
                        const searchInput = document.getElementById(`${type}SearchInput`);
                        const searchContainer = document.getElementById(`${type}SearchContainer`);
                        searchInput.value = '';
                        searchContainer.classList.remove('has-text');
                        filterEquipment(type);
                        // Enfocar el input después de limpiar para mejor UX
                        searchInput.focus();
                    }
                    function updateSelectedCount(type) {
                        const checkboxes = document.querySelectorAll(`input[name="${type}_equipos[]"]:checked`);
                        const count = checkboxes.length;
                        const countElement = document.getElementById(`${type}SelectedCount`);
                        if (countElement) {
                            // Contar también cuántos están visibles
                            const visibleSelected = Array.from(checkboxes).filter(cb => {
                                const item = cb.closest('.equipment-item');
                                return item && !item.classList.contains('hidden');
                            }).length;
                            let displayText = `Equipos seleccionados: ${count}`;
                            // Si hay filtro activo, mostrar información adicional
                            const searchInput = document.getElementById(`${type}SearchInput`);
                            if (searchInput && searchInput.value.trim() !== '') {
                                displayText += ` (${visibleSelected} visibles)`;
                            }
                            countElement.textContent = displayText;
                            // Cambiar color según cantidad
                            if (count === 0) {
                                countElement.style.color = '#999';
                            } else {
                                countElement.style.color = type === 'triage' ? '#FFA500' : '#00FF00';
                            }
                        }
                    }
                    function getSelectedEquipment(type) {
                        const checkboxes = document.querySelectorAll(`input[name="${type}_equipos[]"]:checked`);
                        return Array.from(checkboxes).map(cb => cb.value);
                    }
                    // Función mejorada para mostrar alertas
                    function showAlert(message, type = 'success') {
                        const alertContainer = document.getElementById('alertContainer');
                        // Remover alertas existentes
                        const existingAlerts = alertContainer.querySelectorAll('.alert');
                        existingAlerts.forEach(alert => alert.remove());
                        const alert = document.createElement('div');
                        alert.className = `alert alert-${type}`;
                        alert.innerHTML = `
                        ${message}
                        <button type="button" class="close" onclick="this.parentElement.remove()">
                            <span>&times;</span>
                        </button>`;
                        alertContainer.appendChild(alert);
                        // Auto-remover después de 5 segundos
                        setTimeout(() => {
                            if (alert.parentElement) {
                                alert.remove();
                            }
                        }, 5000);
                    }
                    // Configuración del gráfico
                    const ctx = document.getElementById('techChart').getContext('2d');
                    const techChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(technicianData),
                            datasets: [{
                                data: Object.values(technicianData),
                                backgroundColor: [
                                    '#FFD700', '#90EE90', '#87CEEB', '#DDA0DD',
                                    '#FF6347', '#98FB98', '#F0E68C', '#DEB887'
                                ],
                                borderWidth: 0,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    display: false
                                },
                                y: {
                                    display: false
                                }
                            }
                        }
                    });
                    // Función para deshabilitar/habilitar botones
                    function toggleButton(buttonId, loading = false) {
                        const btn = document.getElementById(buttonId);
                        if (loading) {
                            btn.disabled = true;
                            btn.innerHTML = '<div class="robot-icon"></div>PROCESANDO...';
                        } else {
                            btn.disabled = false;
                            btn.innerHTML = '<div class="robot-icon"></div>ASIGNAR';
                        }
                    }
                    // Función para actualizar lista de equipos - MODIFICADA para soportar búsqueda
                    function updateEquipmentList(tipo = 'disponibles') {
                        $.ajax({
                            url: window.location.href,
                            method: 'POST',
                            data: {
                                action: 'get_equipos',
                                tipo: tipo
                            },
                            dataType: 'json',
                            success: function(equipos) {
                                const listId = tipo === 'disponibles' ? 'triageEquipmentList' : 'processEquipmentList';
                                const namePrefix = tipo === 'disponibles' ? 'triage' : 'process';
                                const list = document.getElementById(listId);
                                if (list) {
                                    // Preservar el elemento "no-results"
                                    const noResults = list.querySelector('.no-results');
                                    list.innerHTML = '';
                                    equipos.forEach(function(equipo) {
                                        const searchData = (equipo.codigo_g + ' ' + equipo.serial + ' ' + equipo.producto + ' ' + equipo.marca + ' ' + (equipo.modelo || '')).toLowerCase();
                                        const item = document.createElement('div');
                                        item.className = 'equipment-item';
                                        item.setAttribute('data-search', searchData);
                                        item.innerHTML = `
                                        <input type="checkbox" 
                                            name="${namePrefix}_equipos[]" 
                                            value="${equipo.id}"
                                            id="${namePrefix}_equipo_${equipo.id}"
                                            onchange="updateSelectedCount('${namePrefix}')">
                                        <label for="${namePrefix}_equipo_${equipo.id}">
                                            ${equipo.codigo_g} - ${equipo.producto} ${equipo.marca}
                                        </label>
                                    `;
                                        list.appendChild(item);
                                    });
                                    // Restaurar el elemento "no-results"
                                    if (noResults) {
                                        list.appendChild(noResults);
                                    }
                                    // Reaplica el filtro si hay búsqueda activa
                                    const searchInput = document.getElementById(`${namePrefix}SearchInput`);
                                    if (searchInput && searchInput.value.trim() !== '') {
                                        filterEquipment(namePrefix);
                                    }
                                    // Actualizar contador
                                    updateSelectedCount(namePrefix);
                                }
                            },
                            error: function() {
                                console.warn('No se pudo actualizar la lista de equipos');
                            }
                        });
                    }
                    // Util: intenta parsear JSON limpio desde responseText (tolerante)
                    function safeParseJSON(text) {
                        if (!text) return null;
                        // Intento directo
                        try {
                            return JSON.parse(text);
                        } catch (e) {}
                        // Si hay contenido antes, buscar primer { ... } válido
                        const first = text.indexOf('{');
                        const last = text.lastIndexOf('}');
                        if (first !== -1 && last !== -1 && last > first) {
                            try {
                                return JSON.parse(text.slice(first, last + 1));
                            } catch (e) {}
                        }
                        return null;
                    }
                    // Reutilizable: procesa respuesta (sea desde success o desde error)
                    function handleAssignResponse(xhrOrData, formId, successButtonId, onSuccessCallback) {
                        let data = xhrOrData;
                        // si viene de error handler: intentar parsear xhr.responseText
                        if (xhrOrData && xhrOrData.responseText !== undefined) {
                            const parsed = safeParseJSON(xhrOrData.responseText);
                            if (parsed) data = parsed;
                        }
                        if (data && data.success) {
                            const alertType = data.partial ? 'warning' : 'success';
                            showAlert('✅ ' + data.message, alertType);
                            document.getElementById(formId).reset();
                            if (typeof onSuccessCallback === 'function') onSuccessCallback();
                        } else {
                            const msg = (data && data.message) ? data.message : 'Error de conexión. Intente nuevamente.';
                            showAlert('❌ ' + msg, 'danger');
                        }
                        toggleButton(successButtonId, false);
                    }
                    // TRIAGE form - MODIFICADO para selección múltiple
                    document.getElementById('triageForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const selectedEquipos = getSelectedEquipment('triage');
                        const tecnicoId = document.getElementById('technicianSelect').value;
                        if (selectedEquipos.length === 0) {
                            showAlert('Por favor seleccione al menos un equipo', 'warning');
                            return;
                        }
                        if (!tecnicoId) {
                            showAlert('Por favor seleccione un técnico', 'warning');
                            return;
                        }
                        toggleButton('triageBtn', true);
                        $.ajax({
                            url: window.location.href,
                            method: 'POST',
                            data: {
                                action: 'assign_equipment',
                                equipo_ids: selectedEquipos, // Enviar array de IDs
                                tecnico_id: tecnicoId,
                                tipo: 'triage'
                            },
                            dataType: 'json',
                            timeout: 10000,
                            cache: false,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(response) {
                                console.log('Respuesta triage (success):', response);
                                handleAssignResponse(response, 'triageForm', 'triageBtn', function() {
                                    updateStats();
                                    updateEquipmentList('disponibles');
                                    updateEquipmentList('proceso');
                                    // Limpiar selección después de asignar
                                    clearSelectionEquipment('triage');
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error AJAX triage:', status, error, xhr.status);
                                handleAssignResponse(xhr, 'triageForm', 'triageBtn', function() {
                                    updateStats();
                                    updateEquipmentList('disponibles');
                                    updateEquipmentList('proceso');
                                    clearSelectionEquipment('triage');
                                });
                            }
                        });
                    });
                    // PROCESS form - MODIFICADO para selección múltiple
                    document.getElementById('processForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const selectedEquipos = getSelectedEquipment('process');
                        const tecnicoId = document.getElementById('processTechnicianSelect').value;
                        if (selectedEquipos.length === 0) {
                            showAlert('Por favor seleccione al menos un equipo', 'warning');
                            return;
                        }
                        if (!tecnicoId) {
                            showAlert('Por favor seleccione un técnico', 'warning');
                            return;
                        }
                        toggleButton('processBtn', true);
                        $.ajax({
                            url: window.location.href,
                            method: 'POST',
                            data: {
                                action: 'assign_equipment',
                                equipo_ids: selectedEquipos, // Enviar array de IDs
                                tecnico_id: tecnicoId,
                                tipo: 'process'
                            },
                            dataType: 'json',
                            timeout: 10000,
                            cache: false,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(response) {
                                console.log('Respuesta proceso (success):', response);
                                handleAssignResponse(response, 'processForm', 'processBtn', function() {
                                    updateStats();
                                    updateEquipmentList('proceso');
                                    updateEquipmentList('disponibles');
                                    // Limpiar selección después de asignar
                                    clearSelectionEquipment('process');
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error AJAX proceso:', status, error, xhr.status);
                                handleAssignResponse(xhr, 'processForm', 'processBtn', function() {
                                    updateStats();
                                    updateEquipmentList('proceso');
                                    updateEquipmentList('disponibles');
                                    clearSelectionEquipment('process');
                                });
                            }
                        });
                    });
                    // Función para actualizar estadísticas
                    function updateStats() {
                        $.ajax({
                            url: window.location.href,
                            method: 'POST',
                            data: {
                                action: 'get_stats'
                            },
                            dataType: 'json',
                            success: function(stats) {
                                console.log('Estadísticas actualizadas:', stats);
                                // Actualizar datos del gráfico
                                technicianData = {};
                                stats.forEach(function(stat) {
                                    technicianData[stat.nombre] = parseInt(stat.total_equipos);
                                });
                                // Actualizar gráfico
                                techChart.data.labels = Object.keys(technicianData);
                                techChart.data.datasets[0].data = Object.values(technicianData);
                                techChart.update();
                                // Actualizar tabla de estadísticas
                                const statsContainer = document.getElementById('techStats');
                                statsContainer.innerHTML = '';
                                stats.forEach(function(stat) {
                                const container = document.createElement('div');
                                container.className = 'tech-row-container';
                                
                                let listItems = '<li>No hay equipos asignados.</li>';
                                if (stat.equipos_asignados) {
                                    listItems = stat.equipos_asignados.split('|').map(item => `<li>${item}</li>`).join('');
                                }
                                container.innerHTML = `
                                    <div class="tech-row">
                                        <span class="tech-name">${stat.nombre.toUpperCase()}</span>
                                        <span class="tech-count">${stat.total_equipos}</span>
                                        <span class="tech-btn" data-target="tech-details-${stat.id}">▼</span>
                                    </div>
                                    <div class="tech-equipment-details" id="tech-details-${stat.id}">
                                        <input type="text" class="tech-equipment-search" placeholder="Buscar por código o serial...">
                                        <ul class="tech-equipment-list">
                                            ${listItems}
                                        </ul>
                                    </div>`;
                                statsContainer.appendChild(container);
                                });
                            },
                            error: function() {
                                console.warn('Error actualizando estadísticas');
                            }
                        });
                    }
                    // Inicialización
                    $(document).ready(function() {
                        console.log('Dashboard cargado correctamente');
                        console.log('Datos iniciales de técnicos:', technicianData);
                        // Inicializar contadores
                        updateSelectedCount('triage');
                        updateSelectedCount('process');
                        // Agregar funcionalidad de búsqueda en tiempo real con debounce
                        let searchTimeout;
                        // Función de debounce para optimizar búsqueda
                        function debounceSearch(func, delay) {
                            return function(...args) {
                                clearTimeout(searchTimeout);
                                searchTimeout = setTimeout(() => func.apply(this, args), delay);
                            };
                        }
                        // Aplicar debounce a las funciones de filtrado
                        const debouncedTriageFilter = debounceSearch(() => filterEquipment('triage'), 300);
                        const debouncedProcessFilter = debounceSearch(() => filterEquipment('process'), 300);
                        // Agregar eventos de teclado para mejor UX
                        const triageSearchInput = document.getElementById('triageSearchInput');
                        const processSearchInput = document.getElementById('processSearchInput');
                        if (triageSearchInput) {
                            triageSearchInput.addEventListener('input', debouncedTriageFilter);
                            // Limpiar búsqueda con Escape
                            triageSearchInput.addEventListener('keydown', function(e) {
                                if (e.key === 'Escape') {
                                    this.value = '';
                                    filterEquipment('triage');
                                    this.blur();
                                }
                            });
                        }
                        if (processSearchInput) {
                            processSearchInput.addEventListener('input', debouncedProcessFilter);
                            // Limpiar búsqueda con Escape
                            processSearchInput.addEventListener('keydown', function(e) {
                                if (e.key === 'Escape') {
                                    this.value = '';
                                    filterEquipment('process');
                                    this.blur();
                                }
                            });
                        }
                        // Event listeners para desplegables y búsqueda de equipos de técnico
                        $('#techStats').on('click', '.tech-btn', function() {
                            const targetId = $(this).data('target');
                            const detailsPanel = $(`#${targetId}`);
                            detailsPanel.slideToggle(200);
                            $(this).toggleClass('active');
                        });
                         $('#techStats').on('input', '.tech-equipment-search', function() {
                            const searchTerm = $(this).val().toLowerCase();
                            const list = $(this).siblings('.tech-equipment-list').find('li');s
                            list.each(function() {
                                const equipmentCode = $(this).text().toLowerCase();
                                if (equipmentCode.includes(searchTerm)) {
                                    $(this).show();
                                } else {
                                    $(this).hide();
                                }
                            });
                        });
                        // Agregar tooltips informativos
                        triageSearchInput?.setAttribute('title', 'Presiona Escape para limpiar la búsqueda');
                        processSearchInput?.setAttribute('title', 'Presiona Escape para limpiar la búsqueda');
                    });
                    // Actualizar estadísticas cada 20 minutos
                    setInterval(updateStats, 1200000);
                </script>
            </div>
            <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
            <script src="../assets/js/popper.min.js"></script>
            <script src="../assets/js/bootstrap.min.js"></script>
            <script src="../assets/js/jquery-3.3.1.min.js"></script>
            <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
            <script type="text/javascript" src="../assets/js/datatable.js"></script>
            <script type="text/javascript" src="../assets/js/datatablebuttons.js"></script>
            <script type="text/javascript" src="../assets/js/jszip.js"></script>
            <script type="text/javascript" src="../assets/js/pdfmake.js"></script>
            <script type="text/javascript" src="../assets/js/vfs_fonts.js"></script>
            <script type="text/javascript" src="../assets/js/buttonshtml5.js"></script>
            <script type="text/javascript" src="../assets/js/buttonsprint.js"></script>
            <script type="text/javascript" src="../assets/js/example.js"></script>
            <script type="text/javascript" src="../assets/js/charts-loader.js"></script>
    </body>
    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>