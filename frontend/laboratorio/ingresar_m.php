<?php
// ingresar_m.php (inicio)
// 1) Output buffering para prevenir "headers already sent"
ob_start();
// 2) Iniciar sesión si aún no lo está
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 3) Conexión: ruta robusta desde frontend/laboratorio -> ../../config/ctconex.php
// Ajusta el número 2 si la ubicación real del archivo difiere.
require_once dirname(__DIR__, 2) . '/config/ctconex.php';
// 4) (Opcional pero recomendado) Validación de roles/permiso temprana
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], [1, 2, 5, 6, 7], true)) {
    // Si quieres enviar a una página de error o login
    header('Location: ../error404.php');
    exit;
}
// NAVBAR
// Obtener información del usuario
$userInfo = null;
if (isset($_SESSION['id'])) {
    try {
        $stmt = $connect->prepare("SELECT id, nombre, usuario, correo, foto, idsede FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['id']]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error obteniendo información del usuario: " . $e->getMessage());
        $userInfo = [
            'nombre' => 'PCUsuario',
            'usuario' => 'pc_usuario',
            'correo' => 'correo@pcmarkett.co',
            'foto' => 'reere.webp',
            'idsede' => 'Sede sin definir'
        ];
    }
}
?>
<?php
// ingresar_m.php (actualizado) - Página frontend (mantiene layout lateral)
// -------------------------------------------------
ob_start();
if (session_status() === PHP_SESSION_NONE)
    session_start();
// Carga conexión (asegúrate que ctconex.php define $connect (PDO) o $conn (mysqli))
require_once dirname(__DIR__, 2) . '/config/ctconex.php';
// Validación básica de roles (mantén la tuya si usas otra lógica)
$allowedRoles = [1, 2, 5, 6, 7];
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], $allowedRoles, true)) {
    header('Location: ../error404.php');
    exit;
}
// Obtener info usuario para el navbar (intentar con PDO $connect)
$userInfo = null;
try {
    if (isset($_SESSION['id'])) {
        if (isset($connect) && $connect instanceof PDO) {
            $stmt = $connect->prepare("SELECT id,nombre,usuario,correo,foto,idsede FROM usuarios WHERE id = ? LIMIT 1");
            $stmt->execute([$_SESSION['id']]);
            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif (isset($conn) && $conn instanceof mysqli) {
            $stmt = $conn->prepare("SELECT id,nombre,usuario,correo,foto,idsede FROM usuarios WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $_SESSION['id']);
            $stmt->execute();
            $res = $stmt->get_result();
            $userInfo = $res ? $res->fetch_assoc() : null;
            $stmt->close();
        }
    }
} catch (Exception $e) {
    error_log("Error usuario: " . $e->getMessage());
    $userInfo = null;
}
// Cargar datos iniciales para la página (inventario, último diagnóstico y mantenimiento, técnicos, partes)
$inventario = null;
$diagnostico_ultimo = null;
$mantenimiento_ultimo = null;
$tecnicos = [];
$partesDisponibles = [];
$marcasUnicas = [];
$productosUnicos = [];
$inventario_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
try {
    if ($inventario_id > 0) {
        if (isset($connect) && $connect instanceof PDO) {
            // Inventario
            $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ? LIMIT 1");
            $stmt->execute([$inventario_id]);
            $inventario = $stmt->fetch(PDO::FETCH_ASSOC);
            // Último diagnóstico
            $stmt = $connect->prepare("SELECT * FROM bodega_diagnosticos WHERE inventario_id = ? ORDER BY fecha_diagnostico DESC LIMIT 1");
            $stmt->execute([$inventario_id]);
            $diagnostico_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
            // Último mantenimiento
            $stmt = $connect->prepare("SELECT * FROM bodega_mantenimiento WHERE inventario_id = ? ORDER BY fecha_registro DESC LIMIT 1");
            $stmt->execute([$inventario_id]);
            $mantenimiento_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif (isset($conn) && $conn instanceof mysqli) {
            $stmt = $conn->prepare("SELECT * FROM bodega_inventario WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $inventario_id);
            $stmt->execute();
            $inventario = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $stmt = $conn->prepare("SELECT * FROM bodega_diagnosticos WHERE inventario_id = ? ORDER BY fecha_diagnostico DESC LIMIT 1");
            $stmt->bind_param('i', $inventario_id);
            $stmt->execute();
            $diagnostico_ultimo = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $stmt = $conn->prepare("SELECT * FROM bodega_mantenimiento WHERE inventario_id = ? ORDER BY fecha_registro DESC LIMIT 1");
            $stmt->bind_param('i', $inventario_id);
            $stmt->execute();
            $mantenimiento_ultimo = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
    }
    // Técnicos (roles 5 y 6)
    if (isset($connect) && $connect instanceof PDO) {
        $stmt = $connect->query("SELECT id,nombre FROM usuarios WHERE rol IN (5,6) ORDER BY nombre");
        $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($conn) && $conn instanceof mysqli) {
        $res = $conn->query("SELECT id,nombre FROM usuarios WHERE rol IN (5,6) ORDER BY nombre");
        $tecnicos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    // Partes disponibles
    if (isset($connect) && $connect instanceof PDO) {
        $stmt = $connect->prepare("SELECT id,caja,cantidad,marca,referencia,producto,condicion,precio,detalles,codigo,serial FROM bodega_partes WHERE cantidad > 0 ORDER BY marca,referencia");
        $stmt->execute();
        $partesDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($conn) && $conn instanceof mysqli) {
        $stmt = $conn->prepare("SELECT id,caja,cantidad,marca,referencia,producto,condicion,precio,detalles,codigo,serial FROM bodega_partes WHERE cantidad > 0 ORDER BY marca,referencia");
        $stmt->execute();
        $res = $stmt->get_result();
        $partesDisponibles = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
    }
    foreach ($partesDisponibles as $p) {
        if (!empty($p['marca']) && !in_array($p['marca'], $marcasUnicas))
            $marcasUnicas[] = $p['marca'];
        if (!empty($p['producto']) && !in_array(strtolower($p['producto']), array_map('strtolower', $productosUnicos)))
            $productosUnicos[] = $p['producto'];
    }
    sort($marcasUnicas);
    sort($productosUnicos);
} catch (Exception $e) {
    error_log("Carga inicial error: " . $e->getMessage());
}
// Helper de badges
function badgeClass(string $v): string
{
    $v = strtoupper(trim($v ?? ''));
    if ($v === 'BUENO' || $v === 'APROBADO')
        return 'status-bueno';
    if ($v === 'MALO' || $v === 'RECHAZADO')
        return 'status-malo';
    return 'status-nd';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpieza y Mantenimiento - Rediseño</title>
    <!-- CSS3 personalizado -->
    <link rel="stylesheet" href="../../backend/css//ingresar.css" />
    <link rel="stylesheet" href="../../backend/css/datatable.css" />
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.webp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet" />
</head>
<body>
    <div id="content">
        <script src="../../backend/js/jquery-3.3.1.min.js"></script>
        <script src="../../backend/js/bootstrap.min.js"></script>
        <script src="../../backend/js/datatable.js"></script>
        <script src="../../backend/js/datatablebuttons.js"></script>
        <script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>
        <script>
            $(document).ready(function () {
                var table = $('#triageTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' },
                    order: [[5, 'desc']],
                    columnDefs: [
                        { targets: [5], type: 'datetime' } // fecha de triage
                    ]
                });
                // Custom filter: filtrar por data-tecnico (ID)
                $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                    // Solo aplicar filtro en esta tabla específica
                    if (settings.nTable.id !== 'triageTable') return true;
                    var selected = $('#filterTecnico').val();
                    if (!selected) return true; // sin filtro -> mostrar todo
                    // obtener el nodo TR y su data-tecnico
                    var rowNode = table.row(dataIndex).node();
                    var rowTecnico = $(rowNode).data('tecnico') || 0;
                    return parseInt(selected, 10) === parseInt(rowTecnico, 10s);
                });
                // Cuando cambie el filtro, redibujar tabla
                $('#filterTecnico').on('change', function () {
                    table.draw();
                });
            });
        </script>
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php';
        include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php if (function_exists('renderMenu')) {
                renderMenu($menu);
            } ?>
        </nav>
        <!-- begin:: top-navbar -->
        <div class="top-navbar">
            <nav class="navbar navbar-expand-lg" style="background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%)">
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
                    } ?>
                    <!-- Branding -->
                    <a class="navbar-brand" href="#" style="color: #fff; font-weight: bold;">
                        <i class="fas fa-tools" style="margin-right: 8px; color: #f39c12;"></i>
                        <b>🔧 LIMPIEZA Y MANTENIMIENTO |</b><?php echo htmlspecialchars($titulo); ?>
                    </a>
                    <!-- Menú derecho (usuario) -->
                    <ul class="nav navbar-nav ml-auto">
                        <li class="dropdown nav-item active">
                            <a href="#" class="nav-link" data-toggle="dropdown">
                                <img src="../../backend/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
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
                </div>
                <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="material-icons">more_vert</span>
                </button>
            </nav>
        </div>
        <!--- end:: top_navbar -->
        <div class="container" style="width: 100vh;">
            <!-- Header -->
            <script src="../../backend/js/jquery-3.3.1.min.js"></script>
            <script src="../../backend/js/bootstrap.min.js"></script>
            <div class="main-grid">
                <!-- Diagnosis Panel -->
                <?php
// Antes de la salida HTML, asegura que obtienes el último diagnóstico
$tecnicoResponsable = 'No asignado';
if (!empty($diagnostico_ultimo['tecnico_id'])) {
    $stmtTecnico = $connect->prepare("SELECT nombre FROM usuarios WHERE id = ? LIMIT 1");
    $stmtTecnico->execute([$diagnostico_ultimo['tecnico_id']]);
    $rowTecnico = $stmtTecnico->fetch(PDO::FETCH_ASSOC);
    if ($rowTecnico) {
        $tecnicoResponsable = $rowTecnico['nombre'];
    }
}
?>
<div class="card diagnosis-panel">
    <div class="card-header">
        <div class="card-icon">📋</div>
        <h3>Resultados del TRIAGE</h3>
    </div>
    <?php if (!$inventario): ?>
        <div class="diagnosis-item">
            <span class="diagnosis-label">Equipo</span>
            <span class="status-badge status-malo">No hay equipo (ID inválido)</span>
        </div>
    <?php else: ?>
        <div class="diagnosis-item">
            <span class="diagnosis-label">Equipo</span>
            <span><?= htmlspecialchars($inventario['codigo_g'] . ' — ' . ($inventario['marca'] ?? '') . ' ' . ($inventario['modelo'] ?? '')) ?></span>
        </div>
        <div class="diagnosis-item">
            <span class="diagnosis-label">Fecha Diagnóstico</span>
            <span>
                <?= htmlspecialchars(isset($diagnostico_ultimo['fecha_diagnostico']) ? (new DateTime($diagnostico_ultimo['fecha_diagnostico']))->format('d/m/Y H:i') : 'N/D') ?>
            </span>
        </div>
        <?php
        $fields = [
            'Cámara' => $diagnostico_ultimo['camara'] ?? 'N/D',
            'Teclado' => $diagnostico_ultimo['teclado'] ?? 'N/D',
            'Parlantes' => $diagnostico_ultimo['parlantes'] ?? 'N/D',
            'Batería' => $diagnostico_ultimo['bateria'] ?? 'N/D',
            'Micrófono' => $diagnostico_ultimo['microfono'] ?? 'N/D',
            'Pantalla' => $diagnostico_ultimo['pantalla'] ?? 'N/D'
        ];
        foreach ($fields as $label => $val) {
            $cls = badgeClass((string) $val);
            echo "<div class='diagnosis-item'><span class='diagnosis-label'>{$label}</span><span class='status-badge {$cls}'>" . htmlspecialchars(strtoupper($val)) . "</span></div>";
        }
        $estado = $diagnostico_ultimo['estado_reparacion'] ?? 'N/D';
        ?>
        <div class="diagnosis-item">
            <span class="diagnosis-label">Estado</span>
            <span class="status-badge <?= badgeClass($estado) ?>"><?= htmlspecialchars(strtoupper($estado)) ?></span>
        </div>
        <?php if (!empty($inventario['observaciones'])): ?>
            <div class="diagnosis-item">
                <span class="diagnosis-label">Observaciones Inventario</span>
                <span><?= nl2br(htmlspecialchars($inventario['observaciones'])) ?></span>
            </div>
        <?php endif; ?>
        <div class="diagnosis-item">
            <span class="diagnosis-label">RESPONSABLE ULTIMO TRIAGE 2</span>
            <span class="status-badge status-nd"><?= htmlspecialchars($tecnicoResponsable) ?></span>
        </div>
    <?php endif; ?>
</div>



















                <!-- Main Form -->
                <div class="card">
                    <!-- Equipment Info -->
                    <div class="equipment-info">
                        <div class="equipment-main">
                            <div class="equipment-code"><?php echo htmlspecialchars($inventario['codigo_g'] ?? 'N/A'); ?></div>
                            <div class="equipment-description"><?php echo htmlspecialchars(($inventario['marca'] ?? '') . ' ' . ($inventario['modelo'] ?? '') . ' - ' . ($inventario['procesador'] ?? '')); ?></div>
                        </div>
                        <div class="equipment-details">
                            <div class="detail-item">
                                <span class="detail-label">Serial</span>
                                <span class="detail-value"><?php echo htmlspecialchars($inventario['serial'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Ubicación</span>
                                <span class="detail-value"><?php echo htmlspecialchars($inventario['ubicacion'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Posición</span>
                                <span class="detail-value"><?php echo htmlspecialchars($inventario['posicion'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Lote</span>
                                <span class="detail-value"><?php echo htmlspecialchars($inventario['lote'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>
                    <!-- Equipment Edit Section -->
                    <!-- Equipment Edit Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="card-icon">✏️</div>
                            <h4>Editar Datos del Equipo</h4>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="edit_modelo">Modelo</label>
                                <input type="text" id="edit_modelo" value="<?php echo htmlspecialchars($inventario['modelo'] ?? ''); ?>" placeholder="Ej: Dell Latitude 3420">
                            </div>
                            <div class="form-group">
                                <label for="edit_procesador">Procesador</label>
                                <input type="text" id="edit_procesador" value="<?php echo htmlspecialchars($inventario['procesador'] ?? ''); ?>" placeholder="Ej: Intel i5 11th Gen">
                            </div>
                            <div class="form-group">
                                <label for="edit_ram">RAM</label>
                                <input type="text" id="edit_ram" value="<?php echo htmlspecialchars($inventario['ram'] ?? ''); ?>" placeholder="Ej: 8GB, 16GB">
                            </div>
                            <div class="form-group">
                                <label for="edit_disco">Disco</label>
                                <input type="text" id="edit_disco" value="<?php echo htmlspecialchars($inventario['disco'] ?? ''); ?>" placeholder="Ej: 256GB SSD">
                            </div>
                            <div class="form-group">
                                <label for="edit_pulgadas">Pulgadas</label>
                                <input type="text" id="edit_pulgadas" value="<?php echo htmlspecialchars($inventario['pulgadas'] ?? ''); ?>" placeholder="Ej: 14, 15.6">
                            </div>
                            <div class="form-group">
                                <label for="edit_grado">Grado</label>
                                <select id="edit_grado">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="A" <?php echo ($inventario['grado'] ?? '') === 'A' ? 'selected' : ''; ?>>A - Excelente</option>
                                    <option value="B" <?php echo ($inventario['grado'] ?? '') === 'B' ? 'selected' : ''; ?>>B - Bueno</option>
                                    <option value="C" <?php echo ($inventario['grado'] ?? '') === 'C' ? 'selected' : ''; ?>>C - Regular</option>
                                    <option value="SCRAP" <?php echo ($inventario['grado'] ?? '') === 'SCRAP' ? 'selected' : ''; ?>>SCRAP</option>
                                </select>
                            </div>
                        </div>
                    </div>
<!-- Technician Assignment -->
<div class="form-section">
    <div class="section-title">
        <div class="card-icon">👨‍🔧</div>
        <h4>Asignar Técnico Diagnóstico</h4>
    </div>
    <div class="form-group">
        <label for="tecnico_diagnostico">Técnico Diagnóstico</label>
        <select id="tecnico_diagnostico" name="tecnico_diagnostico" disabled>
            <option value="<?php echo $_SESSION['id']; ?>" selected>
                <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </option>
        </select>
        <!-- Input oculto para enviar el ID del usuario -->
        <input type="hidden" name="tecnico_diagnostico" value="<?php echo $_SESSION['id']; ?>">
    </div>
</div>
<!-- Technician Assignment -->
<div class="form-section">
    <div class="section-title">
        <div class="card-icon">📝</div>
        <h4>NUEVO DIAGNÓSTICO TÉCNICO</h4>
    </div>
    <div class="form-group">









<?php

/* -------------------- Datos / Defaults -------------------- */
$tecnico_id = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
$mensaje = '';
$resultadoTriage = [
    'componentes_portatil' => [
        'Camara' => 'BUENO',
        'Teclado' => 'BUENO',
        'Parlantes' => 'BUENO',
        'Bateria' => 'BUENO',
        'Microfono' => 'BUENO',
        'Pantalla' => 'BUENO',
    ],
    'selecionador_Disco' => [
        'Disco' => 'BUENO',
    ],
    'componentes_computador' => [
        'VGA' => 'BUENO',
        'DVI' => 'BUENO',
        'HDMI' => 'BUENO',
        'USB' => 'BUENO',
        'Red' => 'BUENO',
    ],
    'vida_util_disco' => '100%',
    'observaciones' => '',
    'estado_reparacion' => 'aprobado',
];
/* -------------------- Lista de equipos asignados al técnico -------------------- */
$equiposAsignados = [];
try {
    // buscamos en bodega_salidas equipos asignados a este técnico (últimas salidas)
    $sql = "SELECT s.inventario_id, i.codigo_g, i.serial, i.producto, i.marca, i.modelo, s.fecha_salida
            FROM bodega_salidas s
            LEFT JOIN bodega_inventario i ON i.id = s.inventario_id
            WHERE s.tecnico_id = ?
            ORDER BY s.fecha_salida DESC";
    $equiposAsignados = db_fetch_all($sql, [$tecnico_id]);
} catch (Throwable $e) {
    // no bloquear: mostrar mensaje
    $mensaje .= "<div class='alert alert-warning'>No se pudieron cargar los equipos asignados: " . htmlspecialchars($e->getMessage()) . "</div>";
}
/* -------------------- Determinar inventarios seleccionados (single o bulk) -------------------- */
$id_equipo = 0;
$ids_equipo = []; // array de ids si vienen varios
if (isset($_GET['inventario_id'])) {
    $id_equipo = (int) $_GET['inventario_id'];
}
if (isset($_GET['inventario_ids'])) {
    // formato: CSV "1,2,3"
    $ids_equipo = array_filter(array_map('intval', explode(',', $_GET['inventario_ids'])));
    if (count($ids_equipo) === 1 && $id_equipo === 0) {
        $id_equipo = $ids_equipo[0];
    }
}
// si llegan por POST (cuando cargamos desde la tabla con checkboxes)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cargar_seleccionados'])) {
    $sel = $_POST['seleccion'] ?? [];
    $sel = array_map('intval', (array) $sel);
    if (!empty($sel)) {
        // redirige con GET para mantener estado
        header('Location: ?inventario_ids=' . implode(',', $sel));
        exit();
    } else {
        $mensaje .= "<div class='alert alert-danger'>Selecciona al menos un equipo.</div>";
    }
}
/* -------------------- Guardado (single o bulk) -------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    try {
        // datos comunes
        $compPortatil = $_POST['componentes_portatil'] ?? [];
        $compComputador = $_POST['componentes_computador'] ?? [];
        $vidaUtilDisco = trim($_POST['vida_util_disco'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');
        $estadoRep = trim($_POST['estado_reparacion'] ?? 'aprobado');
        // destinos: si se indicó bulk_hidden cargamos la lista del hidden
        $bulk_mode = isset($_POST['bulk_hidden']) && $_POST['bulk_hidden'] == '1';
        $targets = [];
        if ($bulk_mode) {
            $csv = trim($_POST['bulk_list'] ?? '');
            $targets = array_filter(array_map('intval', explode(',', $csv)));
            if (empty($targets))
                throw new Exception('No hay equipos seleccionados para guardar en modo múltiple.');
        } else {
            $inv = isset($_POST['inventario_id']) ? (int) $_POST['inventario_id'] : 0;
            if ($inv <= 0)
                throw new Exception('Falta el parámetro inventario_id.');
            $targets[] = $inv;
        }
        // Normaliza campos de portátil
        $camara = $compPortatil['Camara'] ?? ($compPortatil['Cámara'] ?? 'N/D');
        $teclado = $compPortatil['Teclado'] ?? 'N/D';
        $parlantes = $compPortatil['Parlantes'] ?? ($compPortatil['Parlante'] ?? 'N/D');
        $bateria = $compPortatil['Bateria'] ?? 'N/D';
        $microfono = $compPortatil['Microfono'] ?? ($compPortatil['Micrófono'] ?? 'N/D');
        $pantalla = $compPortatil['Pantalla'] ?? 'N/D';
        $seleccionadorDisco = $_POST['selecionador_Disco'] ?? [];
        $discoEst = $seleccionadorDisco['Disco'] ?? $compPortatil['Disco'] ?? 'N/D';
        $puertosJSON = json_encode($compComputador, JSON_UNESCAPED_UNICODE);
        $discoTexto = "Estado: $discoEst; Vida útil: $vidaUtilDisco";
        $inserted = [];
        $failed = [];
        $sqlInsert = "INSERT INTO bodega_diagnosticos
                (inventario_id, tecnico_id, camara, teclado, parlantes, bateria, microfono, pantalla, puertos, disco, estado_reparacion, observaciones)
                VALUES (:inventario_id, :tecnico_id, :camara, :teclado, :parlantes, :bateria, :microfono, :pantalla, :puertos, :disco, :estado_reparacion, :observaciones)";
        foreach ($targets as $inv_id) {
            if ($db_type === 'pdo') {
                $stmt = $pdo->prepare($sqlInsert);
                $ok = $stmt->execute([
                    ':inventario_id' => $inv_id,
                    ':tecnico_id' => $tecnico_id,
                    ':camara' => $camara,
                    ':teclado' => $teclado,
                    ':parlantes' => $parlantes,
                    ':bateria' => $bateria,
                    ':microfono' => $microfono,
                    ':pantalla' => $pantalla,
                    ':puertos' => $puertosJSON,
                    ':disco' => $discoTexto,
                    ':estado_reparacion' => $estadoRep,
                    ':observaciones' => $observaciones
                ]);
                if ($ok)
                    $inserted[] = $inv_id;
                else
                    $failed[] = $inv_id;
            } else {
                $stmt = $mysqli->prepare(str_replace(':inventario_id', '?', $sqlInsert));
                if ($stmt === false)
                    throw new Exception('MySQLi prepare error: ' . $mysqli->error);
                $params = [$inv_id, $tecnico_id, $camara, $teclado, $parlantes, $bateria, $microfono, $pantalla, $puertosJSON, $discoTexto, $estadoRep, $observaciones];
                $types = str_repeat('s', count($params));
                $refs = [];
                foreach ($params as $key => $value) {
                    $refs[$key] = &$params[$key];
                }
                $stmt->bind_param($types, ...$refs);
                $ok = $stmt->execute();
                $stmt->close();
                if ($ok)
                    $inserted[] = $inv_id;
                else
                    $failed[] = $inv_id;
            }
        }
        if (!empty($inserted)) {
            $mensaje .= "<div class='alert alert-success'>✅ Guardado correctamente para inventario(s): " . implode(', ', $inserted) . ".</div>";
        }
        if (!empty($failed)) {
            $mensaje .= "<div class='alert alert-danger'>❌ Error al guardar para inventario(s): " . implode(', ', $failed) . ".</div>";
        }
        // Si single, actualiza variables para mostrar datos reflejados
        if (!$bulk_mode && count($inserted) > 0) {
            // actualizar defaults con lo enviado
            $resultadoTriage['componentes_portatil'] = array_merge($resultadoTriage['componentes_portatil'], $compPortatil);
            $resultadoTriage['selecionador_Disco'] = array_merge($resultadoTriage['selecionador_Disco'], $seleccionadorDisco);
            $resultadoTriage['componentes_computador'] = array_merge($resultadoTriage['componentes_computador'], $compComputador);
            $resultadoTriage['vida_util_disco'] = $vidaUtilDisco;
            $resultadoTriage['observaciones'] = $observaciones;
            $resultadoTriage['estado_reparacion'] = $estadoRep;
            $id_equipo = $targets[0];
        }
    } catch (Throwable $e) {
        $mensaje .= "<div class='alert alert-danger'>❌ Error al guardar: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
/* -------------------- Si hay inventario seleccionado, cargamos algunos datos del inventario para mostrar en encabezado -------------------- */
$inventarioInfo = null;
if ($id_equipo > 0) {
    try {
        $rows = db_fetch_all("SELECT id, codigo_g, serial, producto, marca, modelo, ubicacion FROM bodega_inventario WHERE id = ?", [$id_equipo]);
        if (!empty($rows))
            $inventarioInfo = $rows[0];
    } catch (Throwable $e) {
        $mensaje .= "<div class='alert alert-warning'>No se pudo cargar info del inventario: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
if (!empty($ids_equipo) && $inventarioInfo === null) {
    // en bulk podemos mostrar primer elemento info si queremos
    try {
        $first = $ids_equipo[0];
        $rows = db_fetch_all("SELECT id, codigo_g, serial, producto, marca, modelo, ubicacion FROM bodega_inventario WHERE id = ?", [$first]);
        if (!empty($rows))
            $inventarioInfo = $rows[0];
    } catch (Throwable $e) {
    }
}
/* -------------------- Render HTML -------------------- */
?>




    <form method="POST" action="" class="form-section">
<!-- Hidden para modo bulk -->
<?php if (!empty($ids_equipo)): ?>
    <input type="hidden" name="bulk_hidden" value="1">
    <input type="hidden" name="bulk_list"
        value="<?= htmlspecialchars(implode(',', $ids_equipo)) ?>">
    <div class="alert alert-warning">Estás en modo <b>múltiple</b>. Se guardará el mismo
        triage para los equipos: <?= implode(', ', $ids_equipo) ?></div>
<?php else: ?>
    <input type="hidden" name="inventario_id"
        value="<?= htmlspecialchars((string) $id_equipo) ?>">
<?php endif; ?>
<h5>Componentes (Portátil)</h5>
<div class="row">
    <?php foreach ($resultadoTriage['componentes_portatil'] as $comp => $estado): ?>
        <div class="col-md-6 mb-2">
            <label class="d-block"><?= htmlspecialchars($comp) ?></label>
            <select name="componentes_portatil[<?= htmlspecialchars($comp) ?>]"
                class="form-control" id="select-<?= htmlspecialchars($comp) ?>">
                <option value="BUENO" <?= $estado === 'BUENO' ? 'selected' : '' ?>>BUENO
                </option>
                <option value="MALO" <?= $estado === 'MALO' ? 'selected' : '' ?>>MALO</option>
                <option value="N/D" <?= $estado === 'N/D' ? 'selected' : '' ?>>N/D</option>
            </select>
        </div>
    <?php endforeach; ?>
</div>
<?php
// valor inicial: solo dígitos (ej. "95" desde "95%" o "95")
$initial = isset($resultadoTriage['vida_util_disco']) ? preg_replace('/\D/', '', $resultadoTriage['vida_util_disco']) : '';
if ($initial !== '') {
    $initial = (int) $initial;
    if ($initial > 100)
        $initial = 100;
}
?>
<!-- Reemplaza por este bloque -->
<div class="form-group col-md-12 mt-2">
    <div class="row">
        <!-- IZQUIERDA: input con % -->
        <div class="col-md-6">
            <label for="vida_util_disco">Vida útil disco (ej. 95%)</label>
            <div class="input-group mb-6" style="max-width:220px;">
                <input type="number" id="vida_util_disco" name="vida_util_disco"
                    class="form-control" min="0" max="100" placeholder="Ej: 95"
                    value="<?= ($initial !== '' ? $initial : '') ?>" required>
                <span class="input-group-text">%</span>
            </div>
            <small class="text-muted d-block">
                El valor que escribas aquí seleccionará automáticamente el estado del
                <strong>Disco</strong>.
            </small>
        </div>
        <!-- DERECHA: selector y semáforo -->
        <div class="col-md-6">
            <label for="select-Disco">Disco</label>
            <select name="selecionador_Disco[Disco]" id="select-Disco"
                class="form-control mb-2" disabled>
                <option value="BUENO"
                    <?= (isset($resultadoTriage['selecionador_Disco']['Disco']) && $resultadoTriage['selecionador_Disco']['Disco'] === 'BUENO') ? 'selected' : '' ?>>BUENO</option>
                <option value="REGULAR"
                    <?= (isset($resultadoTriage['selecionador_Disco']['Disco']) && $resultadoTriage['selecionador_Disco']['Disco'] === 'REGULAR') ? 'selected' : '' ?>>REGULAR</option>
                <option value="MALO"
                    <?= (isset($resultadoTriage['selecionador_Disco']['Disco']) && $resultadoTriage['selecionador_Disco']['Disco'] === 'MALO') ? 'selected' : '' ?>>MALO</option>
                <option value="N/D"
                    <?= (isset($resultadoTriage['selecionador_Disco']['Disco']) && $resultadoTriage['selecionador_Disco']['Disco'] === 'N/D') ? 'selected' : '' ?>>N/D</option>
            </select>
            <div class="small text-muted">
                Valores: <b>Bueno</b> (70% - 100%), <b>Regular</b> (50% - 69%),
                <b>Malo</b> (0% - 49%).
                <span id="diskIndicator"
                    style="display:inline-block;width:12px;height:12px;border-radius:50%;margin-left:8px;vertical-align:middle;background:#6c757d;border:1px solid #ccc;"></span>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('vida_util_disco');
        const select = document.getElementById('select-Disco');
        const indicator = document.getElementById('diskIndicator');
        function setIndicator(state) {
            indicator.style.background = '#6c757d'; // default N/D (gris)
            if (state === 'good') indicator.style.background = '#28a745';
            else if (state === 'regular') indicator.style.background = '#ffc107';
            else if (state === 'bad') indicator.style.background = '#dc3545';
        }
        function updateFromValue(val) {
            if (val === '' || isNaN(val)) {
                select.value = 'N/D';
                setIndicator('nd');
                return;
            }
            let n = Math.max(0, Math.min(100, parseInt(val, 10)));
            if (n >= 70) { select.value = 'BUENO'; setIndicator('good'); }
            else if (n >= 50) { select.value = 'REGULAR'; setIndicator('regular'); }
            else { select.value = 'MALO'; setIndicator('bad'); }
        }
        // inicializar según valor cargado
        updateFromValue(input.value);
        // limpiar entrada y limitar 0-100 en tiempo real, luego actualizar selector/indicador
        input.addEventListener('input', function () {
            this.value = this.value.toString().replace(/\D/g, '').slice(0, 3);
            if (this.value !== '' && parseInt(this.value, 10) > 100) this.value = '100';
            updateFromValue(this.value);
        });
        // si cambian manualmente el selector, actualizar solo el indicador
        select.addEventListener('change', function () {
            const v = this.value;
            if (v === 'BUENO') setIndicator('good');
            else if (v === 'REGULAR') setIndicator('regular');
            else if (v === 'MALO') setIndicator('bad');
            else setIndicator('nd');
        });
    });
</script>
<h5>Puertos (Computador de Mesa)</h5>
<div class="row">
    <?php foreach ($resultadoTriage['componentes_computador'] as $comp => $estado): ?>
        <div class="col-md-4 mb-2">
            <label class="d-block"><?= htmlspecialchars($comp) ?></label>
            <select name="componentes_computador[<?= htmlspecialchars($comp) ?>]"
                class="form-control">
                <option value="BUENO" <?= $estado === 'BUENO' ? 'selected' : '' ?>>BUENO
                </option>
                <option value="MALO" <?= $estado === 'MALO' ? 'selected' : '' ?>>MALO</option>
                <option value="N/D" <?= $estado === 'N/D' ? 'selected' : '' ?>>N/D</option>
            </select>
        </div>
    <?php endforeach; ?>
</div>
<div class="form-group">
    <label>Estado reparación</label>
    <select name="estado_reparacion" class="form-control">
        <?php
        $enums = ['aprobado', 'falla_mecanica', 'falla_electrica', 'reparacion_cosmetica', 'otro_tipo_falla'];
        foreach ($enums as $opt):
            ?>
            <option value="<?= $opt ?>"
                <?= $resultadoTriage['estado_reparacion'] === $opt ? 'selected' : ''; ?>>
                <?= strtoupper(str_replace('_', ' ', $opt)) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <label>Observaciones</label>
    <textarea required name="observaciones" rows="4"
        class="form-control"><?= htmlspecialchars($resultadoTriage['observaciones']) ?></textarea>
</div>
<div class="d-flex gap-2">
    <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
    <a href="?inventario_id=0" class="btn btn-light">Cancelar / Nueva selección</a>
</div>
</form>



















        <label for="tecnico_diagnostico">NUEVO DIAGNÓSTICO TÉCNICO</label>
        <select id="tecnico_diagnostico" name="tecnico_diagnostico" disabled>
            <option value="<?php echo $_SESSION['id']; ?>" selected>
                <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </option>
        </select>
        <!-- Input oculto para enviar el ID del usuario -->
        <input type="hidden" name="tecnico_diagnostico" value="<?php echo $_SESSION['id']; ?>">
    </div>
</div>




























<!-- Cleaning and Maintenance -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="card-icon">🧽</div>
                            <h4>Limpieza y Mantenimiento</h4>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="limpieza_electronico">Limpieza Electrónico</label>
                                <select id="limpieza_electronico">
                                    <option value="pendiente" selected>Pendiente</option>
                                    <option value="realizada">Realizada</option>
                                    <option value="no_aplica">No Aplica</option>
                                </select>
                                <div id="obs_limpieza_block" class="conditional-field hidden">
                                    <label for="obs_limpieza">Observaciones Limpieza</label>
                                    <textarea id="obs_limpieza" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mantenimiento_crema">Mantenimiento (Crema Disciplinaria)</label>
                                <select id="mantenimiento_crema">
                                    <option value="pendiente" selected>Pendiente</option>
                                    <option value="realizada">Realizada</option>
                                    <option value="no_aplica">No Aplica</option>
                                </select>
                                <div id="obs_crema_block" class="conditional-field hidden">
                                    <label for="obs_crema">Observaciones Crema</label>
                                    <textarea id="obs_crema" rows="2"></textarea>
                                </div>
                            </div>
<!-- Begin:: Cambio de piezas -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_solicitud'])) {
    if ($_POST['cambio_piezas'] === 'si') {
        $detalle = $_POST['detalle_solicitud'] ?? '';
        $cantidad = $_POST['cantidad_solicitada'] ?? 0;
        $codigo_equipo = $_POST['codigo_equipo'] ?? '';
        $serial_parte = $_POST['serial_parte'] ?? '';
        $marca_parte = $_POST['marca_parte'] ?? '';
        $nivel_urgencia = $_POST['nivel_urgencia'] ?? '';
        $referencia_parte = $_POST['referencia_parte'] ?? '';
        $ubicacion_pieza = $_POST['ubicacion_pieza'] ?? '';
        $tecnico_id = $_SESSION['id']; // técnico logueado
        $inventario_id = $id; // ID del equipo desde la URL
        $sql = "INSERT INTO solicitudes_piezas (inventario_id, detalle_solicitud, cantidad_solicitada, codigo_equipo, serial_parte, marca_parte, nivel_urgencia, referencia_parte, ubicacion_pieza, tecnico_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $ok = $stmt->execute([
            $inventario_id, $detalle, $cantidad, $codigo_equipo, $serial_parte, $marca_parte, $nivel_urgencia, $referencia_parte, $ubicacion_pieza, $tecnico_id
        ]);
        if ($ok) {
            echo "<div class='alert alert-success'>Solicitud de pieza registrada correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al registrar la solicitud.</div>";
        }
    }
}
?>
                            <div class="form-group">
                                <label for="cambio_piezas">Cambio Piezas</label>
                                <select id="cambio_piezas">
                                    <option value="no" selected>No</option>
                                    <option value="si">Sí</option>
                                </select>
                                <div id="piezas_block" class="conditional-field hidden">                                
                                    <input type="text" id="detalle_solicitud" placeholder="Detalle de la solicitud">


<div class="form-group">
<label for="edit_pulgadas">Pulgadas</label>
<input type="text" id="detalle_solicitud" value="<?php echo htmlspecialchars($inventario['codigo_g'] ?? ''); ?>" placeholder="Ej: 14, 15.6">







                                    <input type="text" id="cantidad_solicitada" placeholder="Cantidad solicitada">
                                    <input type="text" id="codigo_equipo"
                                        placeholder="Código del equipo que  .\n solicita la pieza">
                                    <input type="text" id="serial_parte" placeholder="Serial de la pieza">
                                    <input type="text" id="marca_parte" placeholder="Marca de la pieza">
                                    <label>Nivel de Urgencia</label>
                                    <select>
                                        <option value="Baja">Baja 7 dias</option>
                                        <option value="Media">Media 2-3 dias</option>
                                        <option value="Urgente">Urgente plazo 24h</option>
                                    </select>
                                    <input type="text" id="referencia_parte" placeholder="Referencia de la pieza">
                                    <input type="text" id="ubicacion_pieza"
                                        placeholder="Ubicación original de la pieza">
                                    <button class="btn btn-success text-white ml-2">Enviar solicitud de
                                        pieza/parte</button>
                                </div>
                            </div>
                            <script>
document.getElementById('cambio_piezas').addEventListener('change', function () {
    const piezasBlock = document.getElementById('piezas_block');
    if (this.value === 'si') {
        piezasBlock.classList.remove('hidden');
    } else {
        piezasBlock.classList.add('hidden');
    }
});
</script>

        <!-- End::  cambio de nuevo diagnostico tecnico -->
                            <!--- begin::listado de partes dispobles en el inventario de partes "bodega_partes" -->
                            <?php
                            // Obtener partes disponibles en bodega (después de la línea 50)
                            $partesDisponibles = [];
                            $marcasUnicas = [];
                            $productosUnicos = [];
                            try {
                                $stmt = $connect->prepare("
        SELECT id, caja, cantidad, marca, referencia, generacion, numero_parte, 
        condicion, precio, producto, detalles, codigo, serial 
        FROM bodega_partes 
        WHERE cantidad > 0 
        ORDER BY marca, referencia
    ");
                                $stmt->execute();
                                $partesDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                // Extraer marcas y productos únicos para los filtros
                                foreach ($partesDisponibles as $parte) {
                                    if (!empty($parte['marca']) && !in_array($parte['marca'], $marcasUnicas)) {
                                        $marcasUnicas[] = $parte['marca'];
                                    }
                                    if (!empty($parte['producto']) && !in_array(strtolower($parte['producto']), array_map('strtolower', $productosUnicos))) {
                                        $productosUnicos[] = $parte['producto'];
                                    }
                                }
                                sort($marcasUnicas);
                                sort($productosUnicos);
                            } catch (PDOException $e) {
                                error_log("Error obteniendo partes disponibles: " . $e->getMessage());
                            }
                            ?>
                            <!-- CSS para el sistema de filtros -->
                            <style>
                                .filtros-container {
                                    background-color: #f8f9fa;
                                    padding: 15px;
                                    border-radius: 8px;
                                    margin-bottom: 15px;
                                    border: 1px solid #dee2e6;
                                }
                                .filtros-row {
                                    display: flex;
                                    gap: 15px;
                                    align-items: end;
                                    flex-wrap: wrap;
                                }
                                .filtro-grupo {
                                    flex: 1;
                                    min-width: 200px;
                                }
                                .filtro-grupo label {
                                    display: block;
                                    font-weight: 500;
                                    margin-bottom: 5px;
                                    color: #495057;
                                    font-size: 0.9em;
                                }
                                .filtro-grupo select,
                                .filtro-grupo input {
                                    width: 100%;
                                    padding: 8px 12px;
                                    border: 1px solid #ced4da;
                                    border-radius: 4px;
                                    font-size: 0.9em;
                                }
                                .btn-filtros {
                                    padding: 8px 15px;
                                    margin: 0 5px;
                                    border: none;
                                    border-radius: 4px;
                                    font-size: 0.9em;
                                    cursor: pointer;
                                }
                                .btn-limpiar {
                                    background-color: #6c757d;
                                    color: white;
                                }
                                .btn-limpiar:hover {
                                    background-color: #5a6268;
                                }
                                .tabla-partes-container {
                                    max-height: 500px;
                                    overflow-y: auto;
                                    border: 1px solid #ddd;
                                    border-radius: 4px;
                                }
                                .tabla-partes {
                                    margin-bottom: 0;
                                }
                                .tabla-partes thead th {
                                    background-color: #f8f9fa;
                                    position: sticky;
                                    top: 0;
                                    z-index: 10;
                                    border-bottom: 2px solid #dee2e6;
                                }
                                .sin-resultados {
                                    text-align: center;
                                    padding: 40px;
                                    color: #6c757d;
                                    font-style: italic;
                                }
                                .contador-resultados {
                                    margin-bottom: 10px;
                                    font-size: 0.9em;
                                    color: #6c757d;
                                }
                            </style>
                            <!-- JavaScript mejorado -->
                            <script>
                                $(document).ready(function () {
                                    // Mostrar/ocultar listado de partes disponibles
                                    $('#lista_partes_bodega').on('change', function () {
                                        const value = $(this).val();
                                        const container = $('#partes_disponibles_container');
                                        if (value === 'si') {
                                            container.removeClass('hidden');
                                            filtrarPartes(); // Aplicar filtros iniciales
                                        } else {
                                            container.addClass('hidden');
                                        }
                                    });
                                    // Event listeners para los filtros
                                    $('#filtro_marca, #filtro_producto, #filtro_busqueda').on('change keyup', function () {
                                        if (!$('#partes_disponibles_container').hasClass('hidden')) {
                                            filtrarPartes();
                                        }
                                    });
                                    // Limpiar filtros
                                    $('#btn_limpiar_filtros').on('click', function () {
                                        $('#filtro_marca').val('');
                                        $('#filtro_producto').val('');
                                        $('#filtro_busqueda').val('');
                                        filtrarPartes();
                                    });
                                    function filtrarPartes() {
                                        const filtroMarca = $('#filtro_marca').val().toLowerCase();
                                        const filtroProducto = $('#filtro_producto').val().toLowerCase();
                                        const filtroBusqueda = $('#filtro_busqueda').val().toLowerCase();
                                        let contador = 0;
                                        $('#tabla_partes_body tr').each(function () {
                                            const fila = $(this);
                                            const marca = fila.find('[data-marca]').data('marca').toLowerCase();
                                            const producto = fila.find('[data-producto]').data('producto').toLowerCase();
                                            const referencia = fila.find('[data-referencia]').data('referencia').toLowerCase();
                                            let mostrar = true;
                                            // Filtro por marca
                                            if (filtroMarca && marca.indexOf(filtroMarca) === -1) {
                                                mostrar = false;
                                            }
                                            // Filtro por producto
                                            if (filtroProducto && producto.indexOf(filtroProducto) === -1) {
                                                mostrar = false;
                                            }
                                            // Filtro de búsqueda general (referencia)
                                            if (filtroBusqueda && referencia.indexOf(filtroBusqueda) === -1) {
                                                mostrar = false;
                                            }
                                            if (mostrar) {
                                                fila.show();
                                                contador++;
                                            } else {
                                                fila.hide();
                                            }
                                        });
                                        // Actualizar contador
                                        $('#contador_resultados').text(`Mostrando ${contador} de ${$('#tabla_partes_body tr').length} partes`);
                                        // Mostrar mensaje si no hay resultados
                                        if (contador === 0) {
                                            $('#sin_resultados').show();
                                        } else {
                                            $('#sin_resultados').hide();
                                        }
                                    }
                                });
                                function seleccionarParte(parteId, referencia, marca, producto) {
                                    // Llenar campos del formulario principal
                                    if (document.getElementById('referencia_parte')) {
                                        document.getElementById('referencia_parte').value = referencia;
                                    }
                                    if (document.getElementById('marca_parte')) {
                                        document.getElementById('marca_parte').value = marca;
                                    }
                                    if (document.getElementById('producto_parte')) {
                                        document.getElementById('producto_parte').value = producto;
                                    }
                                    // Mostrar confirmación
                                    alert(`Parte seleccionada: ${marca} - ${referencia}` );
                                    // Opcional: cerrar el listado
                                    document.getElementById('lista_partes_bodega').value = 'no';
                                    document.getElementById('partes_disponibles_container').classList.add('hidden');
                                }
                            </script>
                            <!-- HTML mejorado -->
                            <div class="form-group">
                                <label for="lista_partes_bodega">¿Ver listado de partes disponibles en bodega?</label>
                                <select id="lista_partes_bodega" name="lista_partes_bodega" class="form-control">
                                    <option value="no" selected>No</option>
                                    <option value="si">Sí</option>
                                </select>
                                <div id="partes_disponibles_container" class="conditional-field hidden"
                                    style="margin-top: 15px;">
                                    <label>Partes Disponibles en Bodega</label>
                                    <!-- Panel de Filtros -->
                                    <div class="filtros-container">
                                        <div class="filtros-row">
                                            <div class="filtro-grupo">
                                                <label for="filtro_marca">Filtrar por Marca:</label>
                                                <select id="filtro_marca" class="form-control">
                                                    <option value="">Todas las marcas</option>
                                                    <?php foreach ($marcasUnicas as $marca): ?>
                                                        <option value="<?php echo htmlspecialchars($marca); ?>">
                                                            <?php echo htmlspecialchars($marca); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="filtro-grupo">
                                                <label for="filtro_producto">Filtrar por Producto:</label>
                                                <select id="filtro_producto" class="form-control">
                                                    <option value="">Todos los productos</option>
                                                    <?php foreach ($productosUnicos as $producto): ?>
                                                        <option value="<?php echo htmlspecialchars($producto); ?>">
                                                            <?php echo htmlspecialchars(ucfirst(strtolower($producto))); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="filtro-grupo">
                                                <label for="filtro_busqueda">Buscar por Referencia:</label>
                                                <input type="text" id="filtro_busqueda" class="form-control"
                                                    placeholder="Escriba referencia...">
                                            </div>
                                            <div class="filtro-grupo">
                                                <button type="button" id="btn_limpiar_filtros"
                                                    class="btn-filtros btn-limpiar">
                                                    Limpiar Filtros
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Contador de resultados -->
                                    <div class="contador-resultados" id="contador_resultados">
                                        Mostrando <?php echo count($partesDisponibles); ?> partes
                                    </div>
                                    <!-- Tabla de partes -->
                                    <div class="tabla-partes-container">
                                        <table class="table table-sm table-striped tabla-partes">
                                            <thead>
                                                <tr>
                                                    <th>Caja</th>
                                                    <th>Cantidad</th>
                                                    <th>Marca</th>
                                                    <th>Referencia</th>
                                                    <th>Producto</th>
                                                    <th>Condición</th>
                                                    <th>Precio</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla_partes_body">
                                                <?php if (!empty($partesDisponibles)): ?>
                                                    <?php foreach ($partesDisponibles as $parte): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($parte['caja']); ?></td>
                                                            <td>
                                                                <span
                                                                    class="badge badge-<?php echo $parte['cantidad'] > 5 ? 'success' : ($parte['cantidad'] > 1 ? 'warning' : 'danger'); ?>">
                                                                    <?php echo $parte['cantidad']; ?>
                                                                </span>
                                                            </td>
                                                            <td data-marca="<?php echo htmlspecialchars($parte['marca']); ?>">
                                                                <?php echo htmlspecialchars($parte['marca']); ?>
                                                            </td>
                                                            <td
                                                                data-referencia="<?php echo htmlspecialchars($parte['referencia']); ?>">
                                                                <?php echo htmlspecialchars($parte['referencia']); ?>
                                                            </td>
                                                            <td
                                                                data-producto="<?php echo htmlspecialchars($parte['producto']); ?>">
                                                                <?php echo htmlspecialchars($parte['producto']); ?>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge badge-<?php echo $parte['condicion'] === 'Nuevo' ? 'primary' : 'secondary'; ?>">
                                                                    <?php echo htmlspecialchars($parte['condicion']); ?>
                                                                </span>
                                                            </td>
                                                            <td>$<?php echo number_format($parte['precio'], 0, ',', '.'); ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-primary" onclick="seleccionarParte(
                                                <?php echo $parte['id']; ?>, 
                                                '<?php echo htmlspecialchars($parte['referencia']); ?>',
                                                '<?php echo htmlspecialchars($parte['marca']); ?>',
                                                '<?php echo htmlspecialchars($parte['producto']); ?>'
                                            )">
                                                                    Seleccionar
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                        <!-- Mensaje cuando no hay resultados -->
                                        <div id="sin_resultados" class="sin-resultados" style="display: none;">
                                            <p>No se encontraron partes que coincidan con los filtros aplicados.</p>
                                        </div>
                                        <?php if (empty($partesDisponibles)): ?>
                                            <div class="sin-resultados">
                                                <p>No hay partes disponibles en bodega en este momento.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <!--- end::listado de partes dispobles en el inventario de partes "bodega_partes" -->
                            <div class="form-group">
                                <label for="proceso_reconstruccion">Proceso Reconstrucción</label>
                                <select id="proceso_reconstruccion">
                                    <option value="no" selected>No</option>
                                    <option value="si">Sí</option>
                                </select>
                                <div id="parte_block" class="conditional-field hidden">
                                    <label for="parte_reconstruida">Parte Reconstruida</label>
                                    <input type="text" id="parte_reconstruida">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remite_otra_area">Remite a Otra Área</label>
                            <select id="remite_otra_area">
                                <option value="no" selected>No</option>
                                <option value="si">Sí</option>
                            </select>
                            <div id="area_block" class="form-group hidden">
                                <label for="area_remite">Área a la que Remite</label>
                                <select id="area_remite">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="bodega">Bodega</option>
                                    <option value="laboratorio">Laboratorio</option>
                                    <option value="control_calidad">Control de Calidad</option>
                                    <option value="venta">Venta</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label for="proceso_electronico">Proceso Electrónico (Detalle)</label>
                            <textarea id="proceso_electronico" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="falla_electrica">¿El equipo tiene una falla Electrica?</label>
                            <select id="falla_electrica">
                                <option value="no" selected>No</option>
                                <option value="si">Sí</option>
                            </select>
                            <div id="detalle_falla_electrica" class="conditional-field hidden">
                                <label for="detalle_falla_electrica">¿Cual falla electrica presenta el equipo?</label>
                                <input type="text" id="detalle_falla_electrica">
                            </div>
                        </div>
                        <!-- HTML CORREGIDO -->
                        <div class="form-group">
                            <label for="falla_estetica">¿El equipo tiene una falla estética?</label>
                            <select id="falla_estetica">
                                <option value="no" selected>No</option>
                                <option value="si">Sí</option>
                            </select>
                            <!-- cambia el id del contenedor -->
                            <div id="falla_estetica_block" class="conditional-field hidden">
                                <label for="detalle_falla_estetica">¿Cuál falla estética presenta el equipo?</label>
                                <!-- cambia el id del input -->
                                <input type="text" id="detalle_falla_estetica">
                            </div>
                        </div>
                        <!-- Asegúrate de tener esta clase en tu CSS -->
                        <style>
                            .hidden {
                                display: none;
                            }
                        </style>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                // ==== Mostrar/Ocultar Área a la que se remite ====
                                const remiteOtraArea = document.getElementById('remite_otra_area');
                                const areaBlock = document.getElementById('area_block');
                                remiteOtraArea.addEventListener('change', function () {
                                    if (this.value === 'si') {
                                        areaBlock.classList.remove('hidden');
                                    } else {
                                        areaBlock.classList.add('hidden');
                                    }
                                });
                                // ==== Mostrar/Ocultar falla eléctrica ====
                                const fallaElectrica = document.getElementById('falla_electrica');
                                const parteBlock = document.getElementById('detalle_falla_electrica');
                                fallaElectrica.addEventListener('change', function () {
                                    if (this.value === 'si') {
                                        parteBlock.classList.remove('hidden');
                                    } else {
                                        parteBlock.classList.add('hidden');
                                    }
                                });
                                // ==== Mostrar/Ocultar falla estética ====
                                const fallaEstetica = document.getElementById('falla_estetica');
                                const fallaEsteticaBlock = document.getElementById('falla_estetica_block'); // ⚡ cambié el id para evitar duplicado
                                fallaEstetica.addEventListener('change', function () {
                                    if (this.value === 'si') {
                                        fallaEsteticaBlock.classList.remove('hidden');
                                    } else {
                                        fallaEsteticaBlock.classList.add('hidden');
                                    }
                                });
                                // ==== Guardar datos (ejemplo simple) ====
                                const btnGuardarEquipo = document.getElementById('btnGuardarEquipo');
                                const btnGuardar = document.getElementById('btnGuardar');
                                const alertas = document.getElementById('alertas');
                                btnGuardarEquipo.addEventListener('click', function () {
                                    alertas.innerHTML = `<div class="alert success">✅ Datos del equipo guardados</div>`;
                                });
                                btnGuardar.addEventListener('click', function () {
                                    alertas.innerHTML = `<div class="alert success">🔧 Mantenimiento y limpieza guardados</div>`;
                                });
                            });
                        </script>
                        <div class="form-group full-width">
                            <label for="observaciones_globales">Observaciones Globales</label>
                            <textarea id="observaciones_globales" rows="3"></textarea>
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="btn-container">
                        <button type="button" class="btn btn-secondary" id="btnGuardarEquipo">
                            💾 Guardar Cambios del Equipo
                        </button>
                        <button type="button" class="btn btn-primary" id="btnGuardar">
                            🔧 Guardar Mantenimiento y Limpieza
                        </button>
                    </div>
                    <div id="alertas"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cargar datos del equipo al cargar la página
            cargarDatosEquipo();
            // Show/hide conditional fields
            const limpiezaElectronico = document.getElementById('limpieza_electronico');
            const obsLimpiezaBlock = document.getElementById('obs_limpieza_block');
            limpiezaElectronico.addEventListener('change', function () {
                if (this.value === 'realizada') {
                    obsLimpiezaBlock.classList.remove('hidden');
                } else {
                    obsLimpiezaBlock.classList.add('hidden');
                }
            });
            const mantenimientoCrema = document.getElementById('mantenimiento_crema');
            const obsCremaBlock = document.getElementById('obs_crema_block');
            mantenimientoCrema.addEventListener('change', function () {
                if (this.value === 'realizada') {
                    obsCremaBlock.classList.remove('hidden');
                } else {
                    obsCremaBlock.classList.add('hidden');
                }
            });
            const cambioPiezas = document.getElementById('cambio_piezas');
            const piezasBlock = document.getElementById('piezas_block');
            cambioPiezas.addEventListener('change', function () {
                if (this.value === 'si') {
                    piezasBlock.classList.remove('hidden');
                } else {
                    piezasBlock.classList.add('hidden');
                }
            });
            const procesoReconstruccion = document.getElementById('proceso_reconstruccion');
            const parteBlock = document.getElementById('parte_block');
            procesoReconstruccion.addEventListener('change', function () {
                if (this.value === 'si') {
                    parteBlock.classList.remove('hidden');
                } else {
                    parteBlock.classList.add('hidden');
                }
            });
            const remiteOtraArea = document.getElementById('remite_otra_area');
            const areaBlock = document.getElementById('area_block');
            remiteOtraArea.addEventListener('change', function () {
                if (this.value === 'si') {
                    areaBlock.classList.remove('hidden');
                } else {
                    areaBlock.classList.add('hidden');
                }
            });
            // Button handlers
            document.getElementById('btnGuardar').addEventListener('click', function () {
                mostrarAlerta('✅ Mantenimiento guardado correctamente', 'success');
            });
            document.getElementById('btnGuardarEquipo').addEventListener('click', function () {
                mostrarAlerta('✅ Cambios del equipo guardados correctamente', 'success');
            });
        });
        // Función para cargar datos del equipo desde el endpoint
        function cargarDatosEquipo() {
            // Obtener el ID de la URL
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            if (!id) {
                mostrarAlerta('❌ No se especificó ID del equipo', 'error');
                return;
            }       // Mostrar loading
            mostrarAlerta('🔄 Cargando datos del equipo...', 'info');
            // Hacer petición al endpoint
            fetch(`../../backend/php/get_ingresar_m.php?id=${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    } return response.json();
                })
                .then(data => {
                    if (data.success) {
                        llenarFormularioConDatos(data.data);
                        mostrarAlerta('✅ Datos cargados correctamente', 'success');
                    } else {
                        mostrarAlerta(`❌ ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarAlerta('❌ Error al cargar datos del equipo', 'error');
                });
        }   // Función para llenar el formulario con los datos recibidos
        function llenarFormularioConDatos(data) {
            const inventario = data.inventario;
            const diagnostico = data.diagnostico_ultimo;
            const mantenimiento = data.mantenimiento_ultimo;
            const entrada = data.entrada_ultima;
            // Actualizar información del equipo en el header
            if (inventario) {
                // Actualizar título
                const headerTitle = document.querySelector('.header h1');
                if (headerTitle) {
                    headerTitle.textContent = `🔧 LIMPIEZA Y MANTENIMIENTO`;
                } const headerSubtitle = document.querySelector('.header p');
                if (headerSubtitle) {
                    headerSubtitle.textContent = `Equipo: ${inventario.codigo_g} - ${inventario.marca} ${inventario.modelo}`;
                }           // Actualizar código del equipo
                const equipmentCode = document.querySelector('.equipment-code');
                if (equipmentCode) {
                    equipmentCode.textContent = inventario.codigo_g || 'N/A';
                }           // Actualizar descripción del equipo
                const equipmentDescription = document.querySelector('.equipment-description');
                if (equipmentDescription) {
                    equipmentDescription.textContent = `${inventario.marca} ${inventario.modelo} - ${inventario.procesador || ''}`;
                }           // Actualizar detalles del equipo
                const serialValue = document.querySelector('.equipment-details .detail-item:nth-child(1) .detail-value');
                if (serialValue) {
                    serialValue.textContent = inventario.serial || 'N/A';
                } const ubicacionValue = document.querySelector('.equipment-details .detail-item:nth-child(2) .detail-value');
                if (ubicacionValue) {
                    ubicacionValue.textContent = inventario.ubicacion || 'N/A';
                } const posicionValue = document.querySelector('.equipment-details .detail-item:nth-child(3) .detail-value');
                if (posicionValue) {
                    posicionValue.textContent = inventario.posicion || 'N/A';
                } const loteValue = document.querySelector('.equipment-details .detail-item:nth-child(4) .detail-value');
                if (loteValue) {
                    loteValue.textContent = inventario.lote || 'N/A';
                }           // Llenar campos del formulario
                const editModelo = document.getElementById('edit_modelo');
                if (editModelo) {
                    editModelo.value = inventario.modelo || '';
                } const editProcesador = document.getElementById('edit_procesador');
                if (editProcesador) {
                    editProcesador.value = inventario.procesador || '';
                } const editRam = document.getElementById('edit_ram');
                if (editRam) {
                    editRam.value = inventario.ram || '';
                } const editDisco = document.getElementById('edit_disco');
                if (editDisco) {
                    editDisco.value = inventario.disco || '';
                } const editPulgadas = document.getElementById('edit_pulgadas');
                if (editPulgadas) {
                    editPulgadas.value = inventario.pulgadas || '';
                } const editGrado = document.getElementById('edit_grado');
                if (editGrado) {
                    editGrado.value = inventario.grado || '';
                }
            }       // Actualizar información del diagnóstico si existe
            if (diagnostico) {
                // Aquí puedes actualizar los campos relacionados con el diagnóstico
                console.log('Diagnóstico encontrado:', diagnostico);
            }       // Actualizar información del mantenimiento si existe
            if (mantenimiento) {
                // Aquí puedes actualizar los campos relacionados con el mantenimiento
                console.log('Mantenimiento encontrado:', mantenimiento);
            }
        } function mostrarAlerta(mensaje, tipo) {
            const alertasDiv = document.getElementById('alertas');
            const alerta = document.createElement('div');
            alerta.className = `alert alert-${tipo}`;
            alerta.textContent = mensaje;
            alertasDiv.appendChild(alerta);
            setTimeout(() => {
                alerta.remove();
            }, 5000);
        }
    </script>
</body>
</html>