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
    
    $posicion = $data['posicion'] ?? null;
    $campos_editados = $data['campos_editados'] ?? [];
    
    if (!$posicion) {
        throw new Exception('Posición del equipo es requerida');
    }
    
    if (empty($campos_editados)) {
        throw new Exception('No hay campos para editar');
    }
    
    // Buscar el equipo por posición
    $sql_buscar = "SELECT id, codigo_g, producto, marca, modelo, serial, posicion FROM bodega_inventario WHERE posicion = ?";
    $stmt_buscar = $connect->prepare($sql_buscar);
    $stmt_buscar->execute([$posicion]);
    $equipo = $stmt_buscar->fetch();
    
    if (!$equipo) {
        throw new Exception('No se encontró equipo en la posición: ' . $posicion);
    }
    
    // Preparar campos para actualización
    $campos_actualizar = [];
    $valores = [];
    $campos_permitidos = [
        'modelo', 'procesador', 'ram', 'disco', 'pulgadas', 
        'grado', 'tactil', 'activo_fijo'
    ];
    
    foreach ($campos_editados as $campo => $nuevo_valor) {
        if (in_array($campo, $campos_permitidos)) {
            $campos_actualizar[] = "`$campo` = ?";
            $valores[] = $nuevo_valor;
        }
    }
    
    if (empty($campos_actualizar)) {
        throw new Exception('No hay campos válidos para editar');
    }
    
    // Agregar fecha_modificacion y ID del equipo
    $campos_actualizar[] = "`fecha_modificacion` = NOW()";
    $valores[] = $equipo['id'];
    
    // Construir y ejecutar query de actualización
    $sql_update = "UPDATE bodega_inventario SET " . implode(', ', $campos_actualizar) . " WHERE id = ?";
    $stmt_update = $connect->prepare($sql_update);
    
    $result = $stmt_update->execute($valores);
    
    if ($result) {
        // Crear log de cambios
        $sql_log = "INSERT INTO bodega_log_cambios (
            inventario_id, 
            usuario_id, 
            fecha_cambio, 
            campo_modificado, 
            valor_anterior, 
            valor_nuevo, 
            tipo_cambio
        ) VALUES (?, ?, NOW(), ?, ?, ?, 'edicion_manual')";
        
        $stmt_log = $connect->prepare($sql_log);
        
        // Obtener valores anteriores para el log
        $sql_anterior = "SELECT modelo, procesador, ram, disco, pulgadas, grado, tactil, activo_fijo FROM bodega_inventario WHERE id = ?";
        $stmt_anterior = $connect->prepare($sql_anterior);
        $stmt_anterior->execute([$equipo['id']]);
        $valores_anteriores = $stmt_anterior->fetch();
        
        // Registrar cada cambio en el log
        foreach ($campos_editados as $campo => $nuevo_valor) {
            if (in_array($campo, $campos_permitidos)) {
                $valor_anterior = $valores_anteriores[$campo] ?? '';
                $stmt_log->execute([
                    $equipo['id'],
                    $_SESSION['user_id'] ?? 1,
                    $campo,
                    $valor_anterior,
                    $nuevo_valor
                ]);
            }
        }
        
        // Obtener datos actualizados
        $sql_actualizado = "SELECT * FROM bodega_inventario WHERE id = ?";
        $stmt_actualizado = $connect->prepare($sql_actualizado);
        $stmt_actualizado->execute([$equipo['id']]);
        $equipo_actualizado = $stmt_actualizado->fetch();
        
        echo json_encode([
            'success' => true,
            'message' => 'Equipo actualizado exitosamente',
            'equipo' => [
                'id' => $equipo_actualizado['id'],
                'codigo_g' => $equipo_actualizado['codigo_g'],
                'posicion' => $equipo_actualizado['posicion'],
                'modelo' => $equipo_actualizado['modelo'],
                'procesador' => $equipo_actualizado['procesador'],
                'ram' => $equipo_actualizado['ram'],
                'disco' => $equipo_actualizado['disco'],
                'pulgadas' => $equipo_actualizado['pulgadas'],
                'grado' => $equipo_actualizado['grado'],
                'tactil' => $equipo_actualizado['tactil'],
                'activo_fijo' => $equipo_actualizado['activo_fijo']
            ],
            'campos_editados' => array_keys($campos_editados)
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
