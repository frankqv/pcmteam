<?php
// public_html/bodega/ver_triage_2.php
// Activar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
session_start();
// Permisos (mismos que lista_triage_2.php)
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], [1, 2, 7])) {
    header('Location: ../error404.php');
    exit;
}

// Incluir archivo de conexión
$possible_paths = [
    __DIR__ . '/../../config/ctconex.php',
    dirname(__DIR__, 2) . '/config/ctconex.php'
];

$conn_included = false;
foreach ($possible_paths as $p) {
    if (file_exists($p)) {
        include_once $p;
        $conn_included = true;
        break;
    }
}

if (!$conn_included) {
    echo "<h3>Error: no se encontró ctconex.php. Buscado en:</h3><pre>" . implode("\n", $possible_paths) . "</pre>";
    exit;
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    echo "<h3>Error: la conexión (\$conn) no está definida o no es mysqli. Revisa ctconex.php</h3>";
    exit;
}

$conn->set_charset('utf8mb4');

// Debug: Verificar conexión de base de datos
echo "<div style='background:#e8f5e8; padding:10px; margin:10px 0;'>";
echo "<strong>Debug Info:</strong><br>";
echo "✓ Conexión exitosa a: " . $conn->server_info . "<br>";
echo "✓ Base de datos conectada<br>";
echo "✓ Charset configurado: utf8mb4<br>";
echo "</div>";

// Helpers
function fetch_one_ps($conn, $sql, $types = '', $params = [])
{
    $stmt = $conn->prepare($sql);
    if ($stmt === false)
        return null;
    if ($types !== '')
        $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        $stmt->close();
        return null;
    }
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row;
}

function fetch_all_ps($conn, $sql, $types = '', $params = [])
{
    $stmt = $conn->prepare($sql);
    if ($stmt === false)
        return [];
    if ($types !== '')
        $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        $stmt->close();
        return [];
    }
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
}

function table_exists($conn, $table)
{
    $safe = $conn->real_escape_string($table);
    $res = $conn->query("SHOW TABLES LIKE '{$safe}'");
    return ($res && $res->num_rows > 0);
}

// Obtener id (compatibilidad)
$inventario_id = 0;
if (isset($_GET['id']))
    $inventario_id = intval($_GET['id']);
elseif (isset($_GET['inventario_id']))
    $inventario_id = intval($_GET['inventario_id']);
elseif (isset($_POST['id']))
    $inventario_id = intval($_POST['id']);
elseif (isset($_POST['inventario_id']))
    $inventario_id = intval($_POST['inventario_id']);

echo "<div style='background:#fff3cd; padding:10px; margin:10px 0;'>";
echo "<strong>Parámetros recibidos:</strong><br>";
echo "inventario_id: " . $inventario_id . "<br>";
echo "GET: " . print_r($_GET, true) . "<br>";
echo "POST: " . print_r($_POST, true) . "<br>";
echo "</div>";

if (!$inventario_id) {
    echo "<h3>Error: falta el parámetro id / inventario_id</h3>";
    echo "<p>URL actual: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";
    echo "<p>Parámetros GET: " . print_r($_GET, true) . "</p>";
    exit;
}

// 0) Datos del inventario
$inv_sql = "SELECT i.*, u.nombre AS tecnico_nombre
FROM bodega_inventario i
LEFT JOIN usuarios u ON i.tecnico_id = u.id
WHERE i.id = ?";
$inventario = fetch_one_ps($conn, $inv_sql, 'i', [$inventario_id]);

if (!$inventario) {
    echo "<h3>No se encontró el inventario con id=" . htmlspecialchars($inventario_id) . "</h3>";
    exit;
}

elseif (table_exists($conn, 'bodega_diagnosticos')) {
    // Mapear campos de diagnósticos como triage
    $sql = "SELECT bd.id, bd.fecha_diagnostico AS fecha_registro, bd.tecnico_id, bd.estado_reparacion AS estado, bd.observaciones, u.nombre AS tecnico_nombre
FROM bodega_diagnosticos bd
LEFT JOIN usuarios u ON bd.tecnico_id = u.id
WHERE bd.inventario_id = ?
ORDER BY bd.fecha_diagnostico DESC
LIMIT 1";
    $triage = fetch_one_ps($conn, $sql, 'i', [$inventario_id]);
}

// 2) Mantenimientos
$m_sql = "SELECT id, fecha_registro, tecnico_id, usuario_registro, estado, tipo_proceso, observaciones, partes_solicitadas, referencia_externa
FROM bodega_mantenimiento
WHERE inventario_id = ?
ORDER BY fecha_registro DESC";
$mantenimientos = fetch_all_ps($conn, $m_sql, 'i', [$inventario_id]);

// 3) Control de calidad
$cc_sql = "SELECT id, fecha_control, tecnico_id, burning_test, sentinel_test, estado_final, categoria_rec, observaciones
FROM bodega_control_calidad
WHERE inventario_id = ?
ORDER BY fecha_control DESC";
$controles = fetch_all_ps($conn, $cc_sql, 'i', [$inventario_id]);

// 4) Buscar partes solicitadas (agregamos resultados para todo el mantenimiento)
$partes = [];
$partes_ids = [];
if (!empty($mantenimientos)) {
    foreach ($mantenimientos as $m) {
        $ps = trim($m['partes_solicitadas'] ?? '');
        if ($ps === '')
            continue;
        $tokens = array_filter(array_map('trim', explode(',', $ps)));
        foreach ($tokens as $token) {
            if ($token === '')
                continue;
            $like = '%' . $token . '%';
            $stmt = $conn->prepare("SELECT id, caja, cantidad, marca, referencia, numero_parte, condicion, precio, detalles, codigo, serial, producto
                        FROM bodega_partes
                        WHERE referencia LIKE ? OR numero_parte LIKE ? OR producto LIKE ?
                        LIMIT 50");
            if ($stmt === false)
                continue;
            $stmt->bind_param('sss', $like, $like, $like);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    if (!in_array($row['id'], $partes_ids, true)) {
                        $partes[] = $row;
                        $partes_ids[] = $row['id'];
                    }
                }
            }
            $stmt->close();
        }
    }
}

// Función simple para mostrar campo con fallback
function h($v)
{
    return htmlspecialchars((string) ($v ?? ''));
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ver Triage 2 — <?= h($inventario['codigo_g'] ?? $inventario['producto']) ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: Arial, Helvetica, sans-serif
        }
        .card {
            margin-bottom: 12px
        }
        pre {
            background: #fafafa;
            padding: 8px;
            border-radius: 4px
        }
    </style>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/datatable.css" />
    <link rel="stylesheet" href="../assets/css/buttonsdataTables.css" />
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet" />
</head>
<body>
    <div class="body-overlay">
        <?php 
        // Debug para los includes
        echo "<!-- Debug: Intentando incluir layouts -->";
        
        $nav_file = '../layouts/nav.php';
        $menu_file = '../layouts/menu_data.php';
        
        if (file_exists($nav_file)) {
            include_once $nav_file;
            echo "<!-- ✓ nav.php incluido -->";
        } else {
            echo "<!-- ✗ nav.php NO encontrado en: $nav_file -->";
        }
        
        if (file_exists($menu_file)) {
            include_once $menu_file;
            echo "<!-- ✓ menu_data.php incluido -->";
        } else {
            echo "<!-- ✗ menu_data.php NO encontrado en: $menu_file -->";
        }
        ?>
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
                <nav class="navbar navbar-expand-lg" style="background: #f39c12;">
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
                        <a class="navbar-brand" href="#" style="color: #fff; font-weight: bold;">
                            <i class="fas fa-tools" style="margin-right: 8px; color: #f39c12;"></i>
                            <b>
                                <h4 class="card-title mb-2"><?= h($inventario['producto'] ?? 'Equipo') ?>
                                    <small><?= h($inventario['codigo_g'] ?? '') ?></small>
                                </h4>
                        </a>
                        <!-- Menú derecho (usuario) -->
                        <ul class="nav navbar-nav ml-auto">
                            <li class="dropdown nav-item active">
                                <a href="#" class="nav-link" data-toggle="dropdown">
                                    <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
                                        alt="Foto de perfil"
                                        style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                </a>
                                <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                    <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong>
                                    </li>
                                    <li><?php echo htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></li>
                                    <li><?php echo htmlspecialchars($userInfo['correo'] ?? 'correo@ejemplo.com'); ?>
                                    </li>
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
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="material-icons">more_vert</span>
                    </button>
                </nav>
            </div>
            <!--- end:: top_navbar -->
            <div class="container-fluid mt-3">
                <a href="lista_triage_2.php" class="btn btn-sm btn-secondary mb-3">← Volver al listado</a>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-2"><?= h($inventario['producto'] ?? 'Equipo') ?> <small
                                class="text-muted"><?= h($inventario['codigo_g'] ?? '') ?></small></h4>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Marca:</strong> <?= h($inventario['marca']) ?> |
                                    <strong>Modelo:</strong> <?= h($inventario['modelo']) ?> |
                                    <strong>Serial:</strong> <?= h($inventario['serial']) ?>
                                </p>
                                <p><strong>Ubicación:</strong> <?= h($inventario['ubicacion']) ?> |
                                    <strong>Posición:</strong> <?= h($inventario['posicion']) ?> |
                                    <strong>Lote:</strong> <?= h($inventario['lote']) ?>
                                </p>
                                <p><strong>Grado:</strong> <?= h($inventario['grado']) ?> |
                                    <strong>Disposición:</strong> <?= h($inventario['disposicion']) ?> |
                                    <strong>Estado:</strong> <?= h($inventario['estado']) ?>
                                </p>
                                <p> <strong>Técnico asignado:</strong>
                                    <?= h($inventario['tecnico_nombre'] ?? $inventario['tecnico_id']) ?> |
                                    <strong>Fecha ingreso:</strong> <?= h($inventario['fecha_ingreso']) ?> |
                                    <strong>Última modif.:</strong> <?= h($inventario['fecha_modificacion']) ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6>Especificaciones</h6>
                                <p><strong>CPU:</strong> <?= h($inventario['procesador']) ?></p>
                                <p><strong>RAM:</strong> <?= h($inventario['ram']) ?></p>
                                <p><strong>Disco:</strong> <?= h($inventario['disco']) ?></p>
                                <p><strong>Pulgadas:</strong> <?= h($inventario['pulgadas']) ?> |
                                    <strong>Táctil:</strong> <?= h($inventario['tactil']) ?>
                                </p>
                            </div>
                        </div>
                        <?php if (!empty($inventario['observaciones'])): ?>
                            <div class="mt-2"><strong>Observaciones generales:</strong>
                                <pre><?= h($inventario['observaciones']) ?></pre>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Triage -->
                <div class="card">
                    <div class="card-header"><strong>Observaciones TRIAGE 2 (PRIORITIZADO)</strong></div>
                    <div class="card-body">
                        <?php if (!$triage): ?>
                            <p class="text-muted">No hay registro de triage para este equipo.</p>
                        <?php else: ?>
                            <p><strong>Fecha:</strong>
                                <?= h($triage['fecha_registro'] ?? $triage['fecha_diagnostico'] ?? '') ?>
                            </p>
                            <p><strong>Técnico:</strong> <?= h($triage['tecnico_nombre'] ?? $triage['tecnico_id']) ?></p>
                            <p><strong>Estado:</strong> <?= h($triage['estado'] ?? $triage['estado_reparacion'] ?? '') ?>
                            </p>
                            <?php if (!empty($triage['categoria'])): ?>
                                <p><strong>Categoría:</strong> <?= h($triage['categoria']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($triage['observaciones'])): ?>
                                <div><strong>Observaciones:</strong>
                                    <pre><?= h($triage['observaciones']) ?></pre>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Mantenimientos -->
                <div class="card">
                    <div class="card-header"><strong>MANTENIMIENTO Y LIMPIEZA</strong></div>
                    <div class="card-body">
                        <?php if (empty($mantenimientos)): ?>
                            <p class="text-muted">No hay registros de mantenimiento para este equipo.</p>
                        <?php else: ?>
                            <?php foreach ($mantenimientos as $m): ?>
                                <div style="border-bottom:1px solid #eee; padding:8px 0;">
                                    <div><strong>Fecha:</strong> <?= h($m['fecha_registro']) ?> |
                                        <strong>Estado:</strong> <?= h($m['estado']) ?> |
                                        <strong>Tipo:</strong> <?= h($m['tipo_proceso']) ?>
                                    </div>
                                    <?php if (!empty($m['observaciones'])): ?>
                                        <div style="margin-top:6px;"><strong>Observaciones:</strong>
                                            <pre><?= h($m['observaciones']) ?></pre>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($m['partes_solicitadas'])): ?>
                                        <div><strong>Partes solicitadas:</strong> <?= h($m['partes_solicitadas']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($m['referencia_externa'])): ?>
                                        <div><strong>Referencia externa:</strong> <?= h($m['referencia_externa']) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Control de calidad -->
                <div class="card">
                    <div class="card-header"><strong>CONTROL DE CALIDAD</strong></div>
                    <div class="card-body">
                        <?php if (empty($controles)): ?>
                            <p class="text-muted">No hay registros de control de calidad para este equipo.</p>
                        <?php else: ?>
                            <?php foreach ($controles as $c): ?>
                                <div style="border-bottom:1px solid #eee; padding:8px 0;">
                                    <div><strong>Fecha control:</strong> <?= h($c['fecha_control']) ?> |
                                        <strong>Técnico ID:</strong> <?= h($c['tecnico_id']) ?> |
                                        <strong>Estado final:</strong> <?= h($c['estado_final']) ?> |
                                        <strong>Categoría REC:</strong> <?= h($c['categoria_rec']) ?>
                                    </div>
                                    <?php if (!empty($c['burning_test'])): ?>
                                        <div style="margin-top:6px;"><strong>Burning Test:</strong>
                                            <pre><?= h($c['burning_test']) ?></pre>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($c['sentinel_test'])): ?>
                                        <div style="margin-top:6px;"><strong>Sentinel Test:</strong>
                                            <pre><?= h($c['sentinel_test']) ?></pre>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($c['observaciones'])): ?>
                                        <div style="margin-top:6px;"><strong>Observaciones:</strong>
                                            <pre><?= h($c['observaciones']) ?></pre>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Partes -->
                <div class="card">
                    <div class="card-header"><strong>PARTES SOLICITADAS</strong></div>
                    <div class="card-body">
                        <?php if (empty($partes)): ?>
                            <p class="text-muted">No se encontraron partes relacionadas en <code>bodega_partes</code>.</p>
                        <?php else: ?>
                            <?php foreach ($partes as $p): ?>
                                <div style="border-bottom:1px dashed #ddd; padding:8px 0;">
                                    <div><strong>ID:</strong> <?= h($p['id']) ?> |
                                        <strong>Caja:</strong> <?= h($p['caja']) ?> |
                                        <strong>Cantidad:</strong> <?= h($p['cantidad']) ?> |
                                        <strong>Marca:</strong> <?= h($p['marca']) ?>
                                    </div>
                                    <div style="margin-top:6px;">
                                        <strong>Referencia:</strong> <?= h($p['referencia']) ?> |
                                        <strong>Nº Parte:</strong> <?= h($p['numero_parte']) ?> |
                                        <strong>Condición:</strong> <?= h($p['condicion']) ?> |
                                        <strong>Precio:</strong> <?= h($p['precio']) ?>
                                    </div>
                                    <div style="margin-top:6px;"><strong>Detalles:</strong> <?= h($p['detalles']) ?> |
                                        <strong>Código:</strong> <?= h($p['codigo']) ?> |
                                        <strong>Serial:</strong> <?= h($p['serial']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>