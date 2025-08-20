<?php
session_start();
require_once '../bd/ctconex.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    $inventario_id = $_GET['inventario_id'] ?? null;
    
    if (!$inventario_id) {
        throw new Exception('ID de inventario es requerido');
    }
    
    // Obtener datos del inventario
    $sql_inventario = "SELECT * FROM bodega_inventario WHERE id = ?";
    $stmt_inventario = $connect->prepare($sql_inventario);
    $stmt_inventario->execute([$inventario_id]);
    $inventario = $stmt_inventario->fetch();
    
    if (!$inventario) {
        throw new Exception('Equipo no encontrado');
    }
    
    // Obtener el último diagnóstico
    $sql_diagnostico = "SELECT * FROM bodega_diagnosticos 
                        WHERE inventario_id = ? 
                        ORDER BY fecha_diagnostico DESC 
                        LIMIT 1";
    $stmt_diagnostico = $connect->prepare($sql_diagnostico);
    $stmt_diagnostico->execute([$inventario_id]);
    $diagnostico = $stmt_diagnostico->fetch();
    
    // Obtener técnicos disponibles
    $sql_tecnicos = "SELECT id, nombre FROM usuarios WHERE rol = '6' AND estado = '1'";
    $stmt_tecnicos = $connect->prepare($sql_tecnicos);
    $stmt_tecnicos->execute();
    $tecnicos = $stmt_tecnicos->fetchAll();
    
    // Obtener último mantenimiento si existe
    $sql_mantenimiento = "SELECT * FROM bodega_mantenimiento 
                          WHERE inventario_id = ? 
                          ORDER BY fecha_registro DESC 
                          LIMIT 1";
    $stmt_mantenimiento = $connect->prepare($sql_mantenimiento);
    $stmt_mantenimiento->execute([$inventario_id]);
    $ultimo_mantenimiento = $stmt_mantenimiento->fetch();
    
    echo json_encode([
        'success' => true,
        'inventario' => $inventario,
        'diagnostico' => $diagnostico,
        'tecnicos' => $tecnicos,
        'ultimo_mantenimiento' => $ultimo_mantenimiento
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
