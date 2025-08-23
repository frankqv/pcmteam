<?php
session_start();
require_once __DIR__ . '../../../config/ctconex.php';

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
    
    $inventario_id = $data['inventario_id'] ?? null;
    
    if (!$inventario_id) {
        throw new Exception('ID de inventario es requerido');
    }
    
    // Verificar que el equipo existe
    $sql_verificar = "SELECT id FROM bodega_inventario WHERE id = ?";
    $stmt_verificar = $connect->prepare($sql_verificar);
    $stmt_verificar->execute([$inventario_id]);
    
    if (!$stmt_verificar->fetch()) {
        throw new Exception('Equipo no encontrado');
    }
    
    // Preparar campos para actualización
    $campos_actualizar = [];
    $valores = [];
    
    // Campos editables
    $campos_permitidos = [
        'edit_modelo' => 'modelo',
        'edit_procesador' => 'procesador', 
        'edit_ram' => 'ram',
        'edit_disco' => 'disco',
        'edit_pulgadas' => 'pulgadas',
        'edit_grado' => 'grado',
        'edit_tactil' => 'tactil',
        'edit_activo_fijo' => 'activo_fijo'
    ];
    
    foreach ($campos_permitidos as $campo_form => $campo_bd) {
        if (isset($data[$campo_form]) && $data[$campo_form] !== '') {
            $campos_actualizar[] = "`$campo_bd` = ?";
            $valores[] = $data[$campo_form];
        }
    }
    
    if (empty($campos_actualizar)) {
        throw new Exception('No hay campos para actualizar');
    }
    
    // Agregar fecha_modificacion
    $campos_actualizar[] = "`fecha_modificacion` = NOW()";
    $valores[] = $inventario_id; // Para el WHERE
    
    // Construir y ejecutar query de actualización
    $sql_update = "UPDATE bodega_inventario SET " . implode(', ', $campos_actualizar) . " WHERE id = ?";
    $stmt_update = $connect->prepare($sql_update);
    
    $result = $stmt_update->execute($valores);
    
    if ($result) {
        // Crear log de cambios si existe la tabla
        try {
            $sql_log = "INSERT INTO bodega_log_cambios (
                inventario_id, 
                usuario_id, 
                fecha_cambio, 
                campo_modificado, 
                valor_anterior, 
                valor_nuevo, 
                tipo_cambio
            ) VALUES (?, ?, NOW(), ?, ?, ?, 'edicion_desde_mantenimiento')";
            
            $stmt_log = $connect->prepare($sql_log);
            
            // Obtener valores anteriores para el log
            $sql_anterior = "SELECT modelo, procesador, ram, disco, pulgadas, grado, tactil, activo_fijo FROM bodega_inventario WHERE id = ?";
            $stmt_anterior = $connect->prepare($sql_anterior);
            $stmt_anterior->execute([$inventario_id]);
            $valores_anteriores = $stmt_anterior->fetch();
            
            // Registrar cada cambio en el log
            foreach ($campos_permitidos as $campo_form => $campo_bd) {
                if (isset($data[$campo_form]) && $data[$campo_form] !== '') {
                    $valor_anterior = $valores_anteriores[$campo_bd] ?? '';
                    $valor_nuevo = $data[$campo_form];
                    
                    if ($valor_anterior !== $valor_nuevo) {
                        $stmt_log->execute([
                            $inventario_id,
                            $_SESSION['user_id'] ?? 1,
                            $campo_bd,
                            $valor_anterior,
                            $valor_nuevo
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            // Si no existe la tabla de log, continuar sin problemas
            // Solo registrar en el log del sistema
            error_log("No se pudo crear log de cambios: " . $e->getMessage());
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Datos del equipo actualizados exitosamente',
            'inventario_id' => $inventario_id,
            'campos_actualizados' => array_values($campos_permitidos)
        ]);
        
    } else {
        throw new Exception('Error al actualizar el equipo');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
