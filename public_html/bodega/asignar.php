<!-- Asignar Tecnico para el triage -->
<!--public_html/bodega/asignar.php -->
<!-- Mientras tanto, btn linea 85 | WHERE u.rol IN ('1','6',) AND u.estado = '1' -->
<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('location: ../error404.php');
    exit();
}
require_once __DIR__ . '../../../config/ctconex.php';
// Procesar asignación AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    // Solo para AJAX: podemos verificar header X-Requested-With si lo deseas
    if ($_POST['action'] == 'assign_equipment') {
        // Evitar que cualquier salida previa rompa JSON
        while (ob_get_level()) {
            ob_end_clean();
        }
        $equipoId = intval($_POST['equipo_id'] ?? 0);
        $tecnicoId = intval($_POST['tecnico_id'] ?? 0);
        $tipo = trim($_POST['tipo'] ?? 'triage');
        $usuarioId = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
        if ($equipoId <= 0 || $tecnicoId <= 0 || $usuarioId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parámetros inválidos (equipo/tecnico/sesión).']);
            exit();
        }
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn->begin_transaction();
            $disposicion = ($tipo === 'triage' || $tipo === 'triage') ? 'en_diagnostico' : 'en_proceso';
            // UPDATE inventario
            $stmt = $conn->prepare("UPDATE bodega_inventario SET tecnico_id = ?, disposicion = ?, fecha_modificacion = NOW() WHERE id = ?");
            $stmt->bind_param("isi", $tecnicoId, $disposicion, $equipoId);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            if ($affected <= 0) {
                throw new Exception('No se pudo actualizar el inventario. Verifique que el equipo existe.');
            }
            // INSERT en bodega_salidas (log)
            $stmt2 = $conn->prepare("INSERT INTO bodega_salidas (inventario_id, tecnico_id, usuario_id, razon_salida, observaciones) VALUES (?, ?, ?, ?, ?)");
            $razon = "Asignación para " . $tipo;
            $observaciones = "Asignado desde dashboard por usuario ID: " . $usuarioId;
            $stmt2->bind_param("iiiss", $equipoId, $tecnicoId, $usuarioId, $razon, $observaciones);
            $stmt2->execute();
            $stmt2->close();
            $conn->commit();
            // Responder JSON limpio y código 200
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'DATOS REGISTRADOS CORRECTAMENTE']);
            exit();
        } catch (Throwable $e) {
            // Intentar rollback si es posible
            try {
                if ($conn && $conn->connect_errno === 0)
                    $conn->rollback();
            } catch (Throwable $_e) {
                error_log('Rollback falló: ' . $_e->getMessage());
            }
            // Registrar error en log del servidor
            error_log("Asignar equipo error: " . $e->getMessage() . " | equipoId={$equipoId}, tecnicoId={$tecnicoId}, usuarioId={$usuarioId}");
            // Preparar mensaje para el cliente
            $errorMessage = 'Error al asignar equipo. Intente nuevamente.';
            if (stripos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorMessage = 'El equipo ya está asignado a este técnico.';
            } elseif (stripos($e->getMessage(), 'foreign key') !== false) {
                $errorMessage = 'Error de referencia. Verifique que el técnico y equipo existan.';
            }
            // Responder JSON aun si ocurre error (evita salida HTML/stacktrace en la respuesta)
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            exit();
        }
    }
    // NUEVO: Endpoint para obtener estadísticas actualizadas
    if ($_POST['action'] == 'get_stats') {
        try {
            $techStats = [];
            $sqlStats = "SELECT u.nombre, COUNT(bi.id) as total_equipos 
                        FROM usuarios u 
                        LEFT JOIN bodega_inventario bi ON u.id = bi.tecnico_id 
                        WHERE u.rol IN ('1','6',) AND u.estado = '1'
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
$sqlStats = "SELECT u.nombre, COUNT(bi.id) as total_equipos 
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
$sqlEquipos = "SELECT id, codigo_g, producto, marca, modelo 
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
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <!-- title -->
        <title>Dashboard Asignación Equipos - PCMARKETTEAM</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <!----css3---->
        <link rel="stylesheet" href="../assets/css/custom.css">
        <link rel="stylesheet" href="../assets/css/loader.css">
        <!-- Data Tables -->
        <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
        <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
        <!--google material icon-->
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
            .tech-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid #444;
            }
            .tech-row:last-child {
                border-bottom: none;
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
            <!-- layouts nav.php  |  Sidebar -->
            <div class="body-overlay"></div>
            <?php include_once '../layouts/nav.php';
            include_once '../layouts/menu_data.php'; ?>
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
                </div>
                <?php renderMenu($menu); ?>
            </nav>
            <!-- Page Content -->
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
            <!-- Navbar -->
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
                                <li><?php echo htmlspecialchars(trim($userInfo['idsede'] ?? '') !== '' ? $userInfo['idsede'] : 'Sede sin definir'); ?>
                                </li>
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
            <!-- Page Content  -->
            <div id="content">
                <div class="container-fluid">
                    <div class="dashboard-grid">
                        <!-- Panel Izquierdo - Asignación Equipos para Triage -->
                        <div class="panel panel-triage">
                            <div class="panel-header">
                                ASIGNACION EQUIPOS PARA TRIAGE
                            </div>
                            <div class="panel-content">
                                <form id="triageForm">
                                    <div class="equipment-section">
                                        <select class="equipment-input" id="equipmentSelect" required>
                                            <option value="">Seleccionar Equipo</option>
                                            <?php foreach ($equiposDisponibles as $equipo): ?>
                                                <option value="<?php echo $equipo['id']; ?>">
                                                    <?php echo htmlspecialchars($equipo['codigo_g'] . ' - ' . $equipo['producto'] . ' ' . $equipo['marca']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
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
                        <!-- Panel Central - Total Equipos Asignados por Técnico -->
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
                                    <div class="tech-row">
                                        <span
                                            class="tech-name"><?php echo strtoupper(htmlspecialchars($stat['nombre'])); ?></span>
                                        <span class="tech-count"><?php echo $stat['total_equipos']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <!-- Panel Derecho - Asignación Equipos Proceso -->
                        <div class="panel panel-process">
                            <div class="panel-header">
                                ASIGNACION EQUIPOS PROCESO
                            </div>
                            <div class="panel-content">
                                <form id="processForm">
                                    <div class="equipment-section">
                                        <select class="equipment-input" id="processEquipmentSelect" required>
                                            <option value="">Seleccionar Equipo</option>
                                            <?php
                                            // Equipos en diagnóstico para proceso
                                            $sqlProceso = "SELECT id, codigo_g, producto, marca, modelo 
                                                FROM bodega_inventario 
                                                WHERE disposicion IN ('en_diagnostico', 'Business Room') 
                                                ORDER BY fecha_modificacion";
                                            $resultProceso = $conn->query($sqlProceso);
                                            if ($resultProceso) {
                                                while ($equipoProceso = $resultProceso->fetch_assoc()): ?>
                                                    <option value="<?php echo $equipoProceso['id']; ?>">
                                                        <?php echo htmlspecialchars($equipoProceso['codigo_g'] . ' - ' . $equipoProceso['producto'] . ' ' . $equipoProceso['marca']); ?>
                                                    </option>
                                                <?php endwhile;
                                            } ?>
                                        </select>
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
                <!-- Alert Container -->
                <div id="alertContainer"></div>
                <!-- Scripts -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <script>
                    // Datos iniciales de técnicos desde PHP
                    let technicianData = {};
                    <?php foreach ($techStats as $stat): ?>
                        technicianData['<?php echo addslashes($stat['nombre']); ?>'] = <?php echo $stat['total_equipos']; ?>;
                    <?php endforeach; ?>
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
                    // Función para actualizar lista de equipos
                    function updateEquipmentList(selectId, tipo = 'disponibles') {
                        $.ajax({
                            url: window.location.href,
                            method: 'POST',
                            data: {
                                action: 'get_equipos',
                                tipo: tipo
                            },
                            dataType: 'json',
                            success: function (equipos) {
                                const select = document.getElementById(selectId);
                                const currentValue = select.value;
                                // Limpiar opciones actuales excepto la primera
                                select.innerHTML = '<option value="">Seleccionar Equipo</option>';
                                // Agregar nuevos equipos
                                equipos.forEach(function (equipo) {
                                    const option = document.createElement('option');
                                    option.value = equipo.id;
                                    option.textContent = equipo.codigo_g + ' - ' + equipo.producto + ' ' + equipo.marca;
                                    select.appendChild(option);
                                });
                            },
                            error: function () {
                                console.warn('No se pudo actualizar la lista de equipos');
                            }
                        });
                    }
                    // Manejo del formulario de Triage
                    // Util: intenta parsear JSON limpio desde responseText (tolerante)
                    function safeParseJSON(text) {
                        if (!text) return null;
                        // Intento directo
                        try { return JSON.parse(text); } catch (e) { }
                        // Si hay contenido antes, buscar primer { ... } válido
                        const first = text.indexOf('{');
                        const last = text.lastIndexOf('}');
                        if (first !== -1 && last !== -1 && last > first) {
                            try { return JSON.parse(text.slice(first, last + 1)); } catch (e) { }
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
                            showAlert('✅ DATOS REGISTRADOS CORRECTAMENTE', 'success');
                            document.getElementById(formId).reset();
                            if (typeof onSuccessCallback === 'function') onSuccessCallback();
                        } else {
                            const msg = (data && data.message) ? data.message : 'Error de conexión. Intente nuevamente.';
                            showAlert('❌ ' + msg, 'danger');
                        }
                        toggleButton(successButtonId, false);
                    }
                    // TRIAGE form
                    document.getElementById('triageForm').addEventListener('submit', function (e) {
                        e.preventDefault();
                        const equipoId = document.getElementById('equipmentSelect').value;
                        const tecnicoId = document.getElementById('technicianSelect').value;
                        if (!equipoId || !tecnicoId) {
                            showAlert('Por favor seleccione un equipo y un técnico', 'warning');
                            return;
                        }
                        toggleButton('triageBtn', true);
                        $.ajax({
                            url: window.location.href,
                            method: 'POST',
                            data: {
                                action: 'assign_equipment',
                                equipo_id: equipoId,
                                tecnico_id: tecnicoId,
                                tipo: 'triage'
                            },
                            dataType: 'json',
                            timeout: 10000,
                            cache: false,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            success: function (response) {
                                console.log('Respuesta triage (success):', response);
                                handleAssignResponse(response, 'triageForm', 'triageBtn', function () {
                                    updateStats();
                                    updateEquipmentList('equipmentSelect', 'disponibles');
                                    updateEquipmentList('processEquipmentSelect', 'proceso');
                                });
                            },
                            error: function (xhr, status, error) {
                                console.error('Error AJAX triage:', status, error, xhr.status);
                                // Intentar leer JSON dentro de responseText por si hubo warnings antes del JSON
                                handleAssignResponse(xhr, 'triageForm', 'triageBtn', function () {
                                    updateStats();
                                    updateEquipmentList('equipmentSelect', 'disponibles');
                                    updateEquipmentList('processEquipmentSelect', 'proceso');
                                });
                            }
                        });
                    });
                    // PROCESS form (similar)
                    document.getElementById('processForm').addEventListener('submit', function (e) {
                        e.preventDefault();
                        const equipoId = document.getElementById('processEquipmentSelect').value;
                        const tecnicoId = document.getElementById('processTechnicianSelect').value;
                        if (!equipoId || !tecnicoId) {
                            showAlert('Por favor seleccione un equipo y un técnico', 'warning');
                            return;
                        }
                        toggleButton('processBtn', true);
                        $.ajax({
                            url: window.location.href,
                            method: 'POST',
                            data: {
                                action: 'assign_equipment',
                                equipo_id: equipoId,
                                tecnico_id: tecnicoId,
                                tipo: 'process'
                            },
                            dataType: 'json',
                            timeout: 10000,
                            cache: false,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            success: function (response) {
                                console.log('Respuesta proceso (success):', response);
                                handleAssignResponse(response, 'processForm', 'processBtn', function () {
                                    updateStats();
                                    updateEquipmentList('processEquipmentSelect', 'proceso');
                                    updateEquipmentList('equipmentSelect', 'disponibles');
                                });
                            },
                            error: function (xhr, status, error) {
                                console.error('Error AJAX proceso:', status, error, xhr.status);
                                handleAssignResponse(xhr, 'processForm', 'processBtn', function () {
                                    updateStats();
                                    updateEquipmentList('processEquipmentSelect', 'proceso');
                                    updateEquipmentList('equipmentSelect', 'disponibles');
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
                            success: function (stats) {
                                console.log('Estadísticas actualizadas:', stats);
                                // Actualizar datos del gráfico
                                technicianData = {};
                                stats.forEach(function (stat) {
                                    technicianData[stat.nombre] = parseInt(stat.total_equipos);
                                });
                                // Actualizar gráfico
                                techChart.data.labels = Object.keys(technicianData);
                                techChart.data.datasets[0].data = Object.values(technicianData);
                                techChart.update();
                                // Actualizar tabla de estadísticas
                                const statsContainer = document.getElementById('techStats');
                                statsContainer.innerHTML = '';
                                stats.forEach(function (stat) {
                                    const row = document.createElement('div');
                                    row.className = 'tech-row';
                                    row.innerHTML = `
                                        <span class="tech-name">${stat.nombre.toUpperCase()}</span>
                                        <span class="tech-count">${stat.total_equipos}</span>`;
                                    statsContainer.appendChild(row);
                                });
                            },
                            error: function () {
                                console.warn('Error actualizando estadísticas');
                            }
                        });
                    }
                    // Actualizar estadísticas cada 30 segundos
                    setInterval(updateStats, 30000);
                    // Inicialización
                    $(document).ready(function () {
                        console.log('Dashboard cargado correctamente');
                        console.log('Datos iniciales de técnicos:', technicianData);
                    });
                </script>
            </div>
            <!---  Contenido de MAIN -->
            <!-- Optional JavaScript -->
            <!-- jQuery first, then Popper.js, then Bootstrap JS -->
            <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
            <script src="../assets/js/popper.min.js"></script>
            <script src="../assets/js/bootstrap.min.js"></script>
            <script src="../assets/js/jquery-3.3.1.min.js"></script>
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
            <script type="text/javascript" src="../assets/js/example.js"></script>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script src="../assets/js/chart/Chart.js"></script>
            <script>
                google.charts.load('current', {
                    'packages': ['corechart']
                });
                google.charts.setOnLoadCallback(drawChart);
            </script>
    </body>
    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>