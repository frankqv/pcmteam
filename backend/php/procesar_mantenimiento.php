<?php
session_start();
require_once '../bd/ctconex.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Datos no válidos');
    }
    
    // Validar datos requeridos
    $inventario_id = $data['inventario_id'] ?? null;
    $tecnico_diagnostico = $data['tecnico_diagnostico'] ?? null;
    
    if (!$inventario_id) {
        throw new Exception('ID de inventario es requerido');
    }
    
    // Preparar datos para inserción
    $sql = "INSERT INTO bodega_mantenimiento (
        inventario_id, 
        tecnico_diagnostico,
        usuario_registro,
        limpieza_electronico,
        observaciones_limpieza_electronico,
        mantenimiento_crema_disciplinaria,
        observaciones_mantenimiento_crema,
        mantenimiento_partes,
        cambio_piezas,
        piezas_solicitadas_cambiadas,
        proceso_reconstruccion,
        parte_reconstruida,
        limpieza_general,
        remite_otra_area,
        area_remite,
        proceso_electronico,
        observaciones_globales,
        estado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
    
    $stmt = $connect->prepare($sql);
    
    $result = $stmt->execute([
        $inventario_id,
        $tecnico_diagnostico,
        $_SESSION['user_id'] ?? 1, // ID del usuario actual
        $data['limpieza_electronico'] ?? 'pendiente',
        $data['observaciones_limpieza_electronico'] ?? null,
        $data['mantenimiento_crema_disciplinaria'] ?? 'pendiente',
        $data['observaciones_mantenimiento_crema'] ?? null,
        $data['mantenimiento_partes'] ?? 'pendiente',
        $data['cambio_piezas'] ?? 'no',
        $data['piezas_solicitadas_cambiadas'] ?? null,
        $data['proceso_reconstruccion'] ?? 'no',
        $data['parte_reconstruida'] ?? null,
        $data['limpieza_general'] ?? 'pendiente',
        $data['remite_otra_area'] ?? 'no',
        $data['area_remite'] ?? null,
        $data['proceso_electronico'] ?? null,
        $data['observaciones_globales'] ?? null
    ]);
    
    if ($result) {
        $mantenimiento_id = $connect->lastInsertId();
        
        // Actualizar estado del inventario
        $update_sql = "UPDATE bodega_inventario SET disposicion = 'en_mantenimiento' WHERE id = ?";
        $update_stmt = $connect->prepare($update_sql);
        $update_stmt->execute([$inventario_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Mantenimiento y limpieza registrado exitosamente en la base de datos',
            'mantenimiento_id' => $mantenimiento_id
        ]);
    } else {
        throw new Exception('Error al insertar en la base de datos');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
