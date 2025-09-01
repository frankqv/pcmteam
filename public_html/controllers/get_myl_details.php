<?php
// get_myl_details.php
// Versión extendida: devuelve TODOS los datos del equipo + triage + mantenimiento + control calidad + partes
// --- Inclusión robusta del archivo de conexión (ctconex.php) ---
$possible_paths = [

    __DIR__ . '/config/ctconex.php',
    __DIR__ . '../../../config/ctconex.php',
    __DIR__ . '/../../../config/ctconex.php'
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
    header('HTTP/1.1 500 Internal Server Error');
    echo "<h3>Error: no se encontró ctconex.php. Buscado en:</h3><pre>" . implode("\n", $possible_paths) . "</pre>";
    exit;
}
if (!isset($conn) || !($conn instanceof mysqli)) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "<h3>Error: la conexión (\$conn) no está definida o no es mysqli. Revisa ctconex.php</h3>";
    exit;
}
$conn->set_charset('utf8mb4');
// --- Obtener inventario_id (GET, POST, JSON) ---
// Aceptamos inventario_id o id (compatibilidad)
$inventario_id = 0;
if (isset($_GET['inventario_id'])) $inventario_id = intval($_GET['inventario_id']);
elseif (isset($_POST['inventario_id'])) $inventario_id = intval($_POST['inventario_id']);
elseif (isset($_GET['id'])) $inventario_id = intval($_GET['id']);
elseif (isset($_POST['id'])) $inventario_id = intval($_POST['id']);
else {
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($json['inventario_id'])) $inventario_id = intval($json['inventario_id']);
            elseif (isset($json['id'])) $inventario_id = intval($json['id']);
        }
    }
}
if (!$inventario_id) {
    header('HTTP/1.1 400 Bad Request');
    echo '<h3>Error: falta inventario_id</h3>';
    exit;
}
// --- Helpers ---
function fetch_all_ps($conn, $sql, $types = '', $params = []) {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return ['error' => true, 'msg' => $conn->error];
    }
    if ($types !== '') $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        return ['error' => true, 'msg' => $err];
    }
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
}
function fetch_one_ps($conn, $sql, $types = '', $params = []) {
    $rows = fetch_all_ps($conn, $sql, $types, $params);
    if (is_array($rows) && isset($rows['error'])) return $rows;
    return (count($rows) > 0) ? $rows[0] : null;
}
// --- 0) DATOS COMPLETOS DEL INVENTARIO (bodega_inventario) ---
$inv_sql = "SELECT i.*, u.nombre AS tecnico_nombre
            FROM bodega_inventario i
            LEFT JOIN usuarios u ON i.tecnico_id = u.id
            WHERE i.id = ?";
$inventario = fetch_one_ps($conn, $inv_sql, 'i', [$inventario_id]);
if (is_array($inventario) && isset($inventario['error']) && $inventario['error']) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Error en consulta INVENTARIO: " . htmlspecialchars($inventario['msg'] ?? '');
    exit;
}
if (!$inventario) {
    header('HTTP/1.1 404 Not Found');
    echo "<h3>No se encontró el inventario con id={$inventario_id}</h3>";
    exit;
}
// --- 1) TRIAGE (bodega_diagnosticos) ---
$triage_sql = "SELECT id, fecha_diagnostico, tecnico_id, estado_reparacion, observaciones
    FROM bodega_diagnosticos
    WHERE inventario_id = ?
    ORDER BY fecha_diagnostico DESC";
$triage = fetch_all_ps($conn, $triage_sql, 'i', [$inventario_id]);
if (is_array($triage) && isset($triage['error']) && $triage['error']) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Error en consulta TRIAGE: " . htmlspecialchars($triage['msg'] ?? '');
    exit;
}
// --- 2) MANTENIMIENTO (bodega_mantenimiento) ---
$m_sql = "SELECT id, fecha_registro, tecnico_id, usuario_registro, estado, tipo_proceso, observaciones, partes_solicitadas, referencia_externa
            FROM bodega_mantenimiento
            WHERE inventario_id = ?
            ORDER BY fecha_registro DESC";
$mantenimiento = fetch_all_ps($conn, $m_sql, 'i', [$inventario_id]);
if (is_array($mantenimiento) && isset($mantenimiento['error']) && $mantenimiento['error']) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Error en consulta MANTENIMIENTO: " . htmlspecialchars($mantenimiento['msg'] ?? '');
    exit;
}
// --- 3) CONTROL DE CALIDAD (bodega_control_calidad) ---
$cc_sql = "SELECT id, fecha_control, tecnico_id, burning_test, sentinel_test, estado_final, categoria_rec, observaciones
            FROM bodega_control_calidad
            WHERE inventario_id = ?
            ORDER BY fecha_control DESC";
$control_calidad = fetch_all_ps($conn, $cc_sql, 'i', [$inventario_id]);
if (is_array($control_calidad) && isset($control_calidad['error']) && $control_calidad['error']) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Error en consulta CONTROL DE CALIDAD: " . htmlspecialchars($control_calidad['msg'] ?? '');
    exit;
}
// --- 4) PARTES SOLICITADAS -> buscar en bodega_partes (por referencia, numero_parte, producto) ---
$partes = [];
$partes_ids = [];
if (!empty($mantenimiento) && is_array($mantenimiento)) {
    foreach ($mantenimiento as $m) {
        $ps = trim($m['partes_solicitadas'] ?? '');
        if ($ps === '') continue;
        $tokens = array_filter(array_map('trim', explode(',', $ps)));
        foreach ($tokens as $token) {
            if ($token === '') continue;
            $like = '%' . $token . '%';
            $stmt = $conn->prepare("SELECT id, caja, cantidad, marca, referencia, numero_parte, condicion, precio, detalles, codigo, serial, producto
                                            FROM bodega_partes
                                            WHERE referencia LIKE ? OR numero_parte LIKE ? OR producto LIKE ?
                                            LIMIT 50");
            if ($stmt === false) continue;
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
// --- Salida HTML (completa) ---
?>
<div style="font-family: Arial, sans-serif; line-height:1.4; color:#222;">
    <!-- Encabezado con datos principales -->
    <div style="display:flex; gap:12px; align-items:flex-start; margin-bottom:12px;">
        <div style="flex:1;">
            <h4 style="margin:0 0 6px 0;"><?= htmlspecialchars($inventario['producto'] ?? 'Equipo') ?> — <?= htmlspecialchars($inventario['codigo_g'] ?? '') ?></h4>
            <div><strong>Marca:</strong> <?= htmlspecialchars($inventario['marca'] ?? '') ?> |
                 <strong>Modelo:</strong> <?= htmlspecialchars($inventario['modelo'] ?? '') ?> |
                 <strong>Serial:</strong> <?= htmlspecialchars($inventario['serial'] ?? '') ?></div>
            <div><strong>Ubicación:</strong> <?= htmlspecialchars($inventario['ubicacion'] ?? '') ?> |
                 <strong>Posición:</strong> <?= htmlspecialchars($inventario['posicion'] ?? '') ?> |
                 <strong>Lote:</strong> <?= htmlspecialchars($inventario['lote'] ?? '') ?></div>
            <div><strong>Grado:</strong> <?= htmlspecialchars($inventario['grado'] ?? '') ?> |
                 <strong>Disposición:</strong> <?= htmlspecialchars($inventario['disposicion'] ?? '') ?> |
                 <strong>Estado:</strong> <?= htmlspecialchars($inventario['estado'] ?? '') ?></div>
            <div><strong>Técnico asignado:</strong> <?= htmlspecialchars($inventario['tecnico_nombre'] ?? $inventario['tecnico_id'] ?? '') ?> |
                 <strong>Fecha ingreso:</strong> <?= htmlspecialchars($inventario['fecha_ingreso'] ?? '') ?> |
                 <strong>Última modificación:</strong> <?= htmlspecialchars($inventario['fecha_modificacion'] ?? '') ?></div>
        </div>
        <div style="min-width:240px;">
            <h5 style="margin:0 0 6px 0;">Especificaciones</h5>
            <div><strong>Procesador:</strong> <?= htmlspecialchars($inventario['procesador'] ?? '') ?></div>
            <div><strong>RAM:</strong> <?= htmlspecialchars($inventario['ram'] ?? '') ?> |
                <strong>Disco:</strong> <?= htmlspecialchars($inventario['disco'] ?? '') ?></div>
            <div><strong>Pulgadas:</strong> <?= htmlspecialchars($inventario['pulgadas'] ?? '') ?> |
                <strong>Táctil:</strong> <?= htmlspecialchars($inventario['tactil'] ?? '') ?></div>
            <div><strong>Activo fijo:</strong> <?= htmlspecialchars($inventario['activo_fijo'] ?? '') ?></div>
        </div>
    </div>
    <!-- Observaciones generales del inventario -->
    <?php if (!empty($inventario['observaciones'])): ?>
        <div style="margin-bottom:12px;">
            <strong>Observaciones generales:</strong>
            <pre style="white-space:pre-wrap; background:#fafafa; padding:8px; border-radius:4px;"><?= htmlspecialchars($inventario['observaciones'] ?? '') ?></pre>
        </div>
    <?php endif; ?>
    <!-- TRIAGE -->
    <h4 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:6px;">=== OBSERVACIONES TRIAGE 2 (PRIORITIZADO) ===</h4>
    <?php if (empty($triage)): ?>
        <p>No hay registros de triage para este inventario.</p>
    <?php else: ?>
        <?php foreach ($triage as $t): ?>
            <div style="padding:8px; border-bottom:1px solid #f0f0f0; margin-bottom:6px;">
                <div><strong>Fecha:</strong> <?= htmlspecialchars($t['fecha_diagnostico'] ?? '') ?></div>
                <div><strong>Técnico ID:</strong> <?= htmlspecialchars($t['tecnico_id'] ?? '') ?> |
                    <strong>Estado:</strong> <?= htmlspecialchars($t['estado_reparacion'] ?? '') ?></div>
                <div style="margin-top:6px;"><strong>Observaciones:</strong>
                    <pre style="white-space:pre-wrap; background:#fafafa; padding:8px; border-radius:4px;"><?= htmlspecialchars($t['observaciones'] ?? '') ?></pre>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <!-- MANTENIMIENTO -->
    <h4 style="border-bottom:1px solid #eee; padding-bottom:6px;">=== MANTENIMIENTO Y LIMPIEZA ===</h4>
    <?php if (empty($mantenimiento)): ?>
        <p>No hay registros de mantenimiento para este inventario.</p>
    <?php else: ?>
        <?php foreach ($mantenimiento as $m): ?>
            <div style="padding:8px; border-bottom:1px solid #f7f7f7; margin-bottom:6px;">
                <div><strong>Fecha registro:</strong> <?= htmlspecialchars($m['fecha_registro'] ?? '') ?> |
                     <strong>Estado:</strong> <?= htmlspecialchars($m['estado'] ?? '') ?> |
                     <strong>Tipo proceso:</strong> <?= htmlspecialchars($m['tipo_proceso'] ?? '') ?></div>
                <div style="margin-top:6px;"><strong>Observaciones:</strong>
                    <pre style="white-space:pre-wrap; background:#fafafa; padding:8px; border-radius:4px;"><?= htmlspecialchars($m['observaciones'] ?? '') ?></pre>
                </div>
                <?php if (!empty($m['partes_solicitadas'])): ?>
                    <div style="margin-top:6px;"><strong>Partes solicitadas (texto):</strong> <?= htmlspecialchars($m['partes_solicitadas'] ?? '') ?></div>
                <?php endif; ?>
                <?php if (!empty($m['referencia_externa'])): ?>
                    <div><strong>Referencia externa:</strong> <?= htmlspecialchars($m['referencia_externa'] ?? '') ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <!-- CONTROL DE CALIDAD -->
    <h4 style="border-bottom:1px solid #eee; padding-bottom:6px;">=== CONTROL DE CALIDAD ===</h4>
    <?php if (empty($control_calidad)): ?>
        <p>No hay registros de control de calidad para este inventario.</p>
    <?php else: ?>
        <?php foreach ($control_calidad as $c): ?>
            <div style="padding:8px; border-bottom:1px solid #f7f7f7; margin-bottom:6px;">
                <div><strong>Fecha control:</strong> <?= htmlspecialchars($c['fecha_control'] ?? '') ?> |
                     <strong>Técnico ID:</strong> <?= htmlspecialchars($c['tecnico_id'] ?? '') ?></div>
                <div><strong>Estado final:</strong> <?= htmlspecialchars($c['estado_final'] ?? '') ?> |
                     <strong>Categoria REC:</strong> <?= htmlspecialchars($c['categoria_rec'] ?? '') ?></div>
                <div style="margin-top:6px;"><strong>Burning Test:</strong>
                    <pre style="white-space:pre-wrap; background:#fafafa; padding:8px; border-radius:4px;"><?= htmlspecialchars($c['burning_test'] ?? '') ?></pre>
                </div>
                <div style="margin-top:6px;"><strong>Sentinel Test:</strong>
                    <pre style="white-space:pre-wrap; background:#fafafa; padding:8px; border-radius:4px;"><?= htmlspecialchars($c['sentinel_test'] ?? '') ?></pre>
                </div>
                <div style="margin-top:6px;"><strong>Observaciones:</strong>
                    <pre style="white-space:pre-wrap; background:#fafafa; padding:8px; border-radius:4px;"><?= htmlspecialchars($c['observaciones'] ?? '') ?></pre>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <!-- PARTES -->
    <h4 style="border-bottom:1px solid #eee; padding-bottom:6px;">=== PARTES SOLICITADAS (bodega_partes) ===</h4>
    <?php if (empty($partes)): ?>
        <p>No se encontraron partes solicitadas relacionadas en <code>bodega_partes</code>.</p>
    <?php else: ?>
        <?php foreach ($partes as $p): ?>
            <div style="padding:8px; border-bottom:1px dashed #ddd; margin-bottom:6px;">
                <div>
                    <strong>ID:</strong> <?= htmlspecialchars($p['id'] ?? '') ?> |
                    <strong>Caja:</strong> <?= htmlspecialchars($p['caja'] ?? '') ?> |
                    <strong>Cantidad:</strong> <?= htmlspecialchars($p['cantidad'] ?? '') ?> |
                    <strong>Marca:</strong> <?= htmlspecialchars($p['marca'] ?? '') ?>
                </div>
                <div style="margin-top:4px;">
                    <strong>Referencia:</strong> <?= htmlspecialchars($p['referencia'] ?? '') ?> |
                    <strong>Nº Parte:</strong> <?= htmlspecialchars($p['numero_parte'] ?? '') ?> |
                    <strong>Condición:</strong> <?= htmlspecialchars($p['condicion'] ?? '') ?> |
                    <strong>Precio:</strong> <?= htmlspecialchars($p['precio'] ?? '') ?>
                </div>
                <div style="margin-top:4px;">
                    <strong>Detalles:</strong> <?= htmlspecialchars($p['detalles'] ?? '') ?> |
                    <strong>Código:</strong> <?= htmlspecialchars($p['codigo'] ?? '') ?> |
                    <strong>Serial:</strong> <?= htmlspecialchars($p['serial'] ?? '') ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
// fin del archivo
