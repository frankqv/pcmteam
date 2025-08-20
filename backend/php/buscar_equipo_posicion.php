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
    $posicion = $_GET['posicion'] ?? null;
    
    if (!$posicion) {
        throw new Exception('Posición del equipo es requerida');
    }
    
    // Buscar el equipo por posición
    $sql = "SELECT 
                id, codigo_g, producto, marca, modelo, serial, 
                posicion, procesador, ram, disco, pulgadas, 
                grado, tactil, activo_fijo, fecha_ingreso, 
                fecha_modificacion, disposicion, ubicacion
            FROM bodega_inventario 
            WHERE posicion = ?";
    
    $stmt = $connect->prepare($sql);
    $stmt->execute([$posicion]);
    $equipo = $stmt->fetch();
    
    if (!$equipo) {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró equipo en la posición: ' . $posicion,
            'posicion_buscada' => $posicion
        ]);
        exit;
    }
    
    // Obtener historial de cambios si existe la tabla
    $historial = [];
    try {
        $sql_historial = "SELECT 
                            campo_modificado, 
                            valor_anterior, 
                            valor_nuevo, 
                            fecha_cambio,
                            u.nombre as usuario
                         FROM bodega_log_cambios lc
                         LEFT JOIN usuarios u ON lc.usuario_id = u.id
                         WHERE lc.inventario_id = ?
                         ORDER BY lc.fecha_cambio DESC
                         LIMIT 10";
        
        $stmt_historial = $connect->prepare($sql_historial);
        $stmt_historial->execute([$equipo['id']]);
        $historial = $stmt_historial->fetchAll();
    } catch (Exception $e) {
        // Si no existe la tabla de log, continuar sin historial
        $historial = [];
    }
    
    echo json_encode([
        'success' => true,
        'equipo' => $equipo,
        'historial' => $historial
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
