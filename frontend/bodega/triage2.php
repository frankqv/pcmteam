<?php
ob_start();
session_start();

/* -------------------- Seguridad -------------------- */
if (!isset($_SESSION['rol']) || !in_array((int)$_SESSION['rol'], [1, 2, 7, 6])) {
    header('location: ../error404.php');
    exit();
}

/* -------------------- Conexión (normaliza PDO / MySQLi / fallback) -------------------- */
$pdo = null;
$mysqli = null;
$db_type = null; // 'pdo' or 'mysqli'

// intenta incluir ctconex.php (ruta relativa desde /frontend/bodega/)
$root = dirname(__DIR__, 2); // .../pcmteam
$ctconex = $root . '/backend/bd/ctconex.php';
if (file_exists($ctconex)) {
    require_once $ctconex;
}

// detecta objetos expuestos por ctconex.php
if (isset($conexion)) {
    // puede ser PDO o mysqli
    if ($conexion instanceof PDO) {
        $pdo = $conexion;
        $db_type = 'pdo';
    } elseif ($conexion instanceof mysqli) {
        $mysqli = $conexion;
        $db_type = 'mysqli';
    }
}
if (!$db_type && isset($con) && $con instanceof PDO) { $pdo = $con; $db_type='pdo'; }
if (!$db_type && isset($con) && $con instanceof mysqli) { $mysqli = $con; $db_type='mysqli'; }
if (!$db_type && isset($db) && $db instanceof PDO) { $pdo = $db; $db_type='pdo'; }
if (!$db_type && isset($db) && $db instanceof mysqli) { $mysqli = $db; $db_type='mysqli'; }

// fallback PDO típico XAMPP (localhost, usuario root sin pass)
// si ya existe $pdo lo respetamos
if (!$db_type) {
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=u171145084_pcmteam;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db_type = 'pdo';
    } catch (Throwable $e) {
        die('No se pudo conectar a la base de datos. Detalle: ' . htmlspecialchars($e->getMessage()));
    }
}

/* Helper: fetchAll para PDO o MySQLi */
function db_fetch_all($sql, $params = []) {
    global $db_type, $pdo, $mysqli;
    if ($db_type === 'pdo') {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } else {
        // mysqli
        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) throw new Exception('MySQLi prepare error: ' . $mysqli->error);
        if (!empty($params)) {
            // bind dynamically as strings
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...array_values($params));
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
}

/* Helper: execute (INSERT/UPDATE) con params */
function db_execute($sql, $params = []) {
    global $db_type, $pdo, $mysqli;
    if ($db_type === 'pdo') {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } else {
        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) throw new Exception('MySQLi prepare error: ' . $mysqli->error);
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...array_values($params));
        }
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}

/* -------------------- Datos / Defaults -------------------- */
$tecnico_id = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;
$mensaje = '';
$resultadoTriage = [
    'componentes_portatil' => [
        'Camara'     => 'BUENO',
        'Teclado'    => 'BUENO',
        'Parlantes'  => 'BUENO',
        'Bateria'    => 'BUENO',
        'Microfono'  => 'BUENO',
        'Pantalla'   => 'BUENO',
        'Disco'      => 'BUENO',
    ],
    'componentes_computador' => [
        'VGA'  => 'BUENO',
        'DVI'  => 'BUENO',
        'HDMI' => 'BUENO',
        'USB'  => 'BUENO',
        'Red'  => 'BUENO',
    ],
    'vida_util_disco' => '100%',
    'observaciones'   => '',
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
    $id_equipo = (int)$_GET['inventario_id'];
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
    $sel = array_map('intval', (array)$sel);
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
        $compPortatil   = $_POST['componentes_portatil']   ?? [];
        $compComputador = $_POST['componentes_computador'] ?? [];
        $vidaUtilDisco  = trim($_POST['vida_util_disco'] ?? '');
        $observaciones  = trim($_POST['observaciones'] ?? '');
        $estadoRep      = trim($_POST['estado_reparacion'] ?? 'aprobado');

        // destinos: si se indicó bulk_hidden cargamos la lista del hidden
        $bulk_mode = isset($_POST['bulk_hidden']) && $_POST['bulk_hidden'] == '1';
        $targets = [];

        if ($bulk_mode) {
            $csv = trim($_POST['bulk_list'] ?? '');
            $targets = array_filter(array_map('intval', explode(',', $csv)));
            if (empty($targets)) throw new Exception('No hay equipos seleccionados para guardar en modo múltiple.');
        } else {
            $inv = isset($_POST['inventario_id']) ? (int)$_POST['inventario_id'] : 0;
            if ($inv <= 0) throw new Exception('Falta el parámetro inventario_id.');
            $targets[] = $inv;
        }

        // Normaliza campos de portátil
        $camara    = $compPortatil['Camara']    ?? ($compPortatil['Cámara'] ?? 'N/D');
        $teclado   = $compPortatil['Teclado']   ?? 'N/D';
        $parlantes = $compPortatil['Parlantes'] ?? ($compPortatil['Parlante'] ?? 'N/D');
        $bateria   = $compPortatil['Bateria']   ?? 'N/D';
        $microfono = $compPortatil['Microfono'] ?? ($compPortatil['Micrófono'] ?? 'N/D');
        $pantalla  = $compPortatil['Pantalla']  ?? 'N/D';
        $discoEst  = $compPortatil['Disco']     ?? 'N/D';

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
                    ':inventario_id'     => $inv_id,
                    ':tecnico_id'        => $tecnico_id,
                    ':camara'            => $camara,
                    ':teclado'           => $teclado,
                    ':parlantes'         => $parlantes,
                    ':bateria'           => $bateria,
                    ':microfono'         => $microfono,
                    ':pantalla'          => $pantalla,
                    ':puertos'           => $puertosJSON,
                    ':disco'             => $discoTexto,
                    ':estado_reparacion' => $estadoRep,
                    ':observaciones'     => $observaciones
                ]);
                if ($ok) $inserted[] = $inv_id; else $failed[] = $inv_id;
            } else {
                $stmt = $mysqli->prepare(str_replace(':inventario_id', '?', $sqlInsert));
                if ($stmt === false) throw new Exception('MySQLi prepare error: ' . $mysqli->error);
                $params = [$inv_id, $tecnico_id, $camara, $teclado, $parlantes, $bateria, $microfono, $pantalla, $puertosJSON, $discoTexto, $estadoRep, $observaciones];
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
                $ok = $stmt->execute();
                $stmt->close();
                if ($ok) $inserted[] = $inv_id; else $failed[] = $inv_id;
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
        if (!empty($rows)) $inventarioInfo = $rows[0];
    } catch (Throwable $e) {
        $mensaje .= "<div class='alert alert-warning'>No se pudo cargar info del inventario: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
if (!empty($ids_equipo) && $inventarioInfo === null) {
    // en bulk podemos mostrar primer elemento info si queremos
    try {
        $first = $ids_equipo[0];
        $rows = db_fetch_all("SELECT id, codigo_g, serial, producto, marca, modelo, ubicacion FROM bodega_inventario WHERE id = ?", [$first]);
        if (!empty($rows)) $inventarioInfo = $rows[0];
    } catch (Throwable $e) {}
}

/* -------------------- Render HTML -------------------- */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INGRESAR TRIAGE 2 - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
    <style>
        .form-section h2{font-size:1.05rem;margin-top:1rem}
        .form-section ul{list-style:none;padding-left:0}
        .form-section li{margin-bottom:.5rem}
        .small-muted{font-size:.85rem;color:#6c757d}
        .table-fixed { max-height: 340px; overflow: auto; display: block; }
        .select-checkbox { width:18px; height:18px; }
    </style>
        <!--google material icon-->
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <div class="body-overlay"></div>
    <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><img src="../../backend/img/favicon.png" class="img-fluid"><span>PCMARKETTEAM</span></h3>
        </div>
        <?php if (function_exists('renderMenu')) { renderMenu($menu); } ?>
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
        <b>INGRESAR TRIAGE 2
        </a>
        <!-- Menú derecho (usuario) -->
        <ul class="nav navbar-nav ml-auto">
        <li class="dropdown nav-item active">
        <a href="#" class="nav-link" data-toggle="dropdown">
        <img src="../../backend/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.png'); ?>"
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
            <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi
                perfil</a>
        </li>
        </ul>
        </li>
        </ul>
        </div>
        <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
            data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="material-icons">more_vert</span>
        </button>
    </nav>
</div>
<!--- end:: top_navbar -->
        <div class="main-content p-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="card p-2">
                        <h5>Equipos asignados a mí (Técnico ID: <?= htmlspecialchars((string)$tecnico_id) ?>)</h5>
                        <?= $mensaje ?>
                        <form method="POST" action="" id="form-seleccion">
                            <div class="form-group">
                                <input id="filtro" class="form-control" placeholder="Buscar por código, serial, producto..." onkeyup="filterTable()">
                            </div>
                            <div class="table-fixed">
                                <table class="table table-sm table-striped">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAll" title="Seleccionar todos" onchange="toggleAll(this)"></th>
                                        <th>Id</th>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Serial</th>
                                    </tr>
                                    </thead>
                                    <tbody id="equipos-tbody">
                                    <?php if (!empty($equiposAsignados)): ?>
                                        <?php foreach ($equiposAsignados as $row): 
                                            $iid = (int)$row['inventario_id'];
                                            $codigo = $row['codigo_g'] ?? '';
                                            $producto = $row['producto'] ?? '';
                                            $serial = $row['serial'] ?? '';
                                        ?>
                                        <tr>
                                            <td><input class="select-one" type="checkbox" name="seleccion[]" value="<?= $iid ?>" /></td>
                                            <td><?= $iid ?></td>
                                            <td><?= htmlspecialchars($codigo) ?></td>
                                            <td><?= htmlspecialchars($producto) ?></td>
                                            <td><?= htmlspecialchars($serial) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5">No tienes equipos asignados.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2 d-flex gap-2">
                                <button type="submit" name="cargar_seleccionados" class="btn btn-secondary btn-sm">Cargar seleccionados</button>
                                <a href="?inventario_id=0" class="btn btn-light btn-sm">Limpiar selección</a>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>O carga un equipo por Id</label>
                                <div class="input-group">
                                    <input type="number" name="buscar_id" id="buscar_id" class="form-control" placeholder="Ingrese inventario id">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" onclick="goToId()">Cargar</button>
                                    </div>
                                </div>
                                <small class="small-muted">También puedes hacer clic en un equipo en la tabla y luego en "Cargar seleccionados".</small>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <h4>INGRESAR TRIAGE 2</h4>
                            <div class="small-muted">
                                Inventario ID: <b><?= $id_equipo > 0 ? htmlspecialchars((string)$id_equipo) : '0' ?></b>
                            </div>
                        </div>
                        <?php if ($id_equipo <= 0 && empty($ids_equipo)): ?>
                            <div class="alert alert-info mt-2">Selecciona uno o varios equipos en la columna izquierda para cargar el formulario. Si no aparece nada, proporciona <code>?inventario_id=ID</code> en la URL.</div>
                        <?php endif; ?>
                        <?php if ($inventarioInfo): ?>
                            <div class="border rounded p-2 mb-2 small-muted">
                                <b>Equipo:</b> <?= htmlspecialchars($inventarioInfo['codigo_g'] ?? '') ?> —
                                <?= htmlspecialchars($inventarioInfo['producto'] ?? '') ?> /
                                <?= htmlspecialchars($inventarioInfo['marca'] ?? '') ?> <?= htmlspecialchars($inventarioInfo['modelo'] ?? '') ?>
                                <br><b>Ubicación:</b> <?= htmlspecialchars($inventarioInfo['ubicacion'] ?? '') ?>
                                <br><b>Serial:</b> <?= htmlspecialchars($inventarioInfo['serial'] ?? '') ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="" class="form-section">
                            <!-- Hidden para modo bulk -->
                            <?php if (!empty($ids_equipo)): ?>
                                <input type="hidden" name="bulk_hidden" value="1">
                                <input type="hidden" name="bulk_list" value="<?= htmlspecialchars(implode(',', $ids_equipo)) ?>">
                                <div class="alert alert-warning">Estás en modo <b>múltiple</b>. Se guardará el mismo triage para los equipos: <?= implode(', ', $ids_equipo) ?></div>
                            <?php else: ?>
                                <input type="hidden" name="inventario_id" value="<?= htmlspecialchars((string)$id_equipo) ?>">
                            <?php endif; ?>
                            <h5>Componentes (Portátil)</h5>
                            <div class="row">
                                <?php foreach ($resultadoTriage['componentes_portatil'] as $comp => $estado): ?>
                                    <div class="col-md-6 mb-2">
                                        <label class="d-block"><?= htmlspecialchars($comp) ?></label>
                                        <select name="componentes_portatil[<?= htmlspecialchars($comp) ?>]" class="form-control">
                                            <option value="BUENO" <?= $estado === 'BUENO' ? 'selected' : '' ?>>BUENO</option>
                                            <option value="MALO"  <?= $estado === 'MALO' ? 'selected' : '' ?>>MALO</option>
                                            <option value="N/D"   <?= $estado === 'N/D' ? 'selected' : '' ?>>N/D</option>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <h5>Puertos (Computador de Mesa)</h5>
                            <div class="row">
                                <?php foreach ($resultadoTriage['componentes_computador'] as $comp => $estado): ?>
                                    <div class="col-md-4 mb-2">
                                        <label class="d-block"><?= htmlspecialchars($comp) ?></label>
                                        <select name="componentes_computador[<?= htmlspecialchars($comp) ?>]" class="form-control">
                                            <option value="BUENO" <?= $estado === 'BUENO' ? 'selected' : '' ?>>BUENO</option>
                                            <option value="MALO"  <?= $estado === 'MALO' ? 'selected' : '' ?>>MALO</option>
                                            <option value="N/D"   <?= $estado === 'N/D' ? 'selected' : '' ?>>N/D</option>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-group">
                                <label>Vida útil disco (ej. 95%)</label>
                                <input type="text" name="vida_util_disco" class="form-control" value="<?= htmlspecialchars($resultadoTriage['vida_util_disco']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Estado reparación</label>
                                <select name="estado_reparacion" class="form-control">
                                    <?php
                                    $enums = ['aprobado','falla_mecanica','falla_electrica','reparacion_cosmetica'];
                                    foreach ($enums as $opt):
                                    ?>
                                    <option value="<?= $opt ?>" <?= $resultadoTriage['estado_reparacion']===$opt?'selected':''; ?>>
                                        <?= strtoupper(str_replace('_',' ', $opt)) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea name="observaciones" rows="4" class="form-control"><?= htmlspecialchars($resultadoTriage['observaciones']) ?></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
                                <a href="?inventario_id=0" class="btn btn-light">Cancelar / Nueva selección</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> <!-- /main-content -->
    </div> <!-- /content -->
</div> <!-- /wrapper -->

<script src="../../backend/js/jquery-3.3.1.min.js"></script>
<script src="../../backend/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>
<script>
    // Filtrar tabla de equipos asignados
    function filterTable() {
        const q = document.getElementById('filtro').value.toLowerCase();
        const tbody = document.getElementById('equipos-tbody');
        for (const tr of tbody.querySelectorAll('tr')) {
            const text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(q) ? '' : 'none';
        }
    }
    // Seleccionar todos
    function toggleAll(master) {
        const checked = master.checked;
        document.querySelectorAll('.select-one').forEach(el => el.checked = checked);
    }
    // redirige a ?inventario_id=X
    function goToId() {
        const v = document.getElementById('buscar_id').value;
        if (v && parseInt(v) > 0) {
            location.href = '?inventario_id=' + parseInt(v);
        } else {
            alert('Ingresa un inventario id válido.');
        }
    }

    // Haz clic en fila para marcar checkbox
    document.querySelectorAll('#equipos-tbody tr').forEach(row => {
        row.addEventListener('click', function(e){
            if (e.target.tagName.toLowerCase() === 'input') return;
            const cb = this.querySelector('.select-one');
            if (cb) cb.checked = !cb.checked;
        });
    });
</script>
</body>
</html>
