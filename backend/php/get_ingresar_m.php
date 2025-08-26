<?php
// backend/php/get_inventario_details.php
header('Content-Type: application/json; charset=utf-8');
// Opcional: permitir peticiones desde frontend si hace falta (ajustar origen)
// header('Access-Control-Allow-Origin: http://192.168.2.10');
// header('Access-Control-Allow-Credentials: true');
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'message' => 'Falta parámetro id']);
    exit;
}
$id = (int) $_GET['id'];
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'message' => 'id inválido']);
    exit;
}
// Incluir conexión (ajusta ruta según tu estructura)
require_once dirname(__DIR__, 2) . '/config/ctconex.php';
// Asumimos $connect es PDO o mysqli
if (!isset($connect)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'message' => 'Conexión a BD no disponible']);
    exit;
}
// Helper para obtener última fila de una tabla por campo de fecha
function fetch_last($connect, $table, $inventario_id, $date_field, $extra_columns = '*')
{
    $sql = "SELECT {$extra_columns} FROM {$table} WHERE inventario_id = ? ORDER BY {$date_field} DESC LIMIT 1";
    if ($connect instanceof PDO) {
        $stmt = $connect->prepare($sql);
        if (!$stmt)
            return null;
        $stmt->execute([$inventario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // mysqli
        $stmt = $connect->prepare($sql);
        if (!$stmt)
            return null;
        $stmt->bind_param('i', $inventario_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }
}
try {
    // Inventario principal
    if ($connect instanceof PDO) {
        $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $inventario = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // mysqli
        $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $inventario = $res ? $res->fetch_assoc() : null;
        $stmt->close();
    }
    if (!$inventario) {
        http_response_code(404);
        echo json_encode(['success' => false, 'data' => null, 'message' => "Inventario no encontrado para id = {$id}"]);
        exit;
    }
    // Último mantenimiento
    $mantenimiento = fetch_last($connect, 'bodega_mantenimiento', $id, 'fecha_registro');
    // Último diagnóstico (puede venir desde bodega_diagnosticos)
    $diagnostico = fetch_last($connect, 'bodega_diagnosticos', $id, 'fecha_diagnostico');
    // Última entrada
    $entrada = fetch_last($connect, 'bodega_entradas', $id, 'fecha_entrada');
    // Último control calidad (opcional)
    $control_calidad = fetch_last($connect, 'bodega_control_calidad', $id, 'fecha_control');
    $payload = [
        'success' => true,
        'data' => [
            'inventario' => $inventario,
            'mantenimiento_ultimo' => $mantenimiento,
            'diagnostico_ultimo' => $diagnostico,
            'entrada_ultima' => $entrada,
            'control_calidad_ultimo' => $control_calidad
        ],
        'message' => ''
    ];
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Exception $e) {
    error_log("get_inventario_details error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'message' => 'Error interno del servidor']);
}
?>