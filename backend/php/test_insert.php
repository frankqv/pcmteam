<?php
require_once __DIR__ . '../../../config/ctconex.php';
// /backend/php/test_insert.php
session_start();
header('Content-Type: application/json');
try {
    // Probar la conexión
    require_once '../../config/ctconex.php';
    // Verificar que la conexión esté activa
    if (!$connect) {
        throw new Exception('No se pudo establecer conexión');
    }
    // Datos de prueba
    $testData = [
        'codigo_g' => 'TEST001',
        'ubicacion' => 'Principal',
        'posicion' => 'ESTANTE-TEST',
        'producto' => 'Portatil',
        'marca' => 'Dell',
        'serial' => 'TEST123456789',
        'modelo' => 'Test Model',
        'procesador' => 'Intel Test',
        'ram' => '8GB',
        'disco' => '256GB SSD',
        'pulgadas' => '15.6',
        'observaciones' => 'Equipo de prueba',
        'grado' => 'A',
        'disposicion' => 'En revisión',
        'estado' => 'activo',
        'tactil' => 'NO',
        'lote' => 'TEST-LOTE-001'
    ];
    // Verificar que el código de prueba no exista
    $stmt_check = $connect->prepare("SELECT id FROM bodega_inventario WHERE codigo_g = ?");
    $stmt_check->execute([$testData['codigo_g']]);
    if ($stmt_check->rowCount() > 0) {
        // Si existe, eliminarlo para la prueba
        $stmt_delete = $connect->prepare("DELETE FROM bodega_inventario WHERE codigo_g = ?");
        $stmt_delete->execute([$testData['codigo_g']]);
        error_log("Registro de prueba eliminado para nueva prueba");
    }
    // Iniciar transacción
    $connect->beginTransaction();
    // Insertar en bodega_inventario
    $sql_inventario = "INSERT INTO bodega_inventario (
        codigo_g, ubicacion, posicion, producto, marca, serial, modelo, 
        procesador, ram, disco, pulgadas, observaciones, grado, disposicion, 
        estado, tactil, lote, fecha_ingreso, fecha_modificacion
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt_inventario = $connect->prepare($sql_inventario);
    $result = $stmt_inventario->execute([
        $testData['codigo_g'],
        $testData['ubicacion'],
        $testData['posicion'],
        $testData['producto'],
        $testData['marca'],
        $testData['serial'],
        $testData['modelo'],
        $testData['procesador'],
        $testData['ram'],
        $testData['disco'],
        $testData['pulgadas'],
        $testData['observaciones'],
        $testData['grado'],
        $testData['disposicion'],
        $testData['estado'],
        $testData['tactil'],
        $testData['lote']
    ]);
    if ($result) {
        $inventario_id = $connect->lastInsertId();
        error_log("Inserción exitosa en inventario. ID: " . $inventario_id);
        // Insertar en bodega_entradas
        $sql_entrada = "INSERT INTO bodega_entradas (
            inventario_id, proveedor_id, usuario_id, cantidad, observaciones, fecha_entrada
        ) VALUES (?, 1, 1, 1, 'Prueba de inserción', NOW())";
        $stmt_entrada = $connect->prepare($sql_entrada);
        $result_entrada = $stmt_entrada->execute([$inventario_id]);
        if ($result_entrada) {
            $entrada_id = $connect->lastInsertId();
            error_log("Inserción exitosa en entradas. ID: " . $entrada_id);
            // Confirmar transacción
            $connect->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Prueba de inserción exitosa',
                'inventario_id' => $inventario_id,
                'entrada_id' => $entrada_id,
                'test_data' => $testData
            ]);
        } else {
            throw new Exception('Error al insertar en bodega_entradas');
        }
    } else {
        throw new Exception('Error al insertar en bodega_inventario');
    }
} catch (Exception $e) {
    // Rollback en caso de error
    if (isset($connect) && $connect->inTransaction()) {
        $connect->rollBack();
    }
    http_response_code(500);
    error_log("Error en test_insert: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error de prueba: ' . $e->getMessage(),
        'file_path' => __FILE__,
        'current_dir' => getcwd()
    ]);
}
