<?php
// /backend/php/st_add_entrada.php
session_start();
header('Content-Type: application/json');

// Verificar sesión y permisos
if (!isset($_SESSION['id']) || !isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
    exit();
}

require_once '../bd/ctconex.php';

try {
    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar y limpiar datos de entrada
    $required_fields = ['codigo_g', 'ubse', 'posicion', 'producto', 'marca', 'serial', 'modelo', 'ram', 'grado', 'disposicion', 'proveedor', 'tactil'];
    $data = [];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            throw new Exception("El campo '$field' es requerido");
        }
        $data[$field] = trim($_POST[$field]);
    }

    // Validaciones específicas
    if (strpos($data['codigo_g'], ' ') !== false) {
        throw new Exception('El código general no puede contener espacios');
    }

    if (strlen($data['codigo_g']) < 3) {
        throw new Exception('El código general debe tener al menos 3 caracteres');
    }

    // Campos opcionales
    $optional_fields = ['procesador', 'disco', 'pulgadas', 'observaciones'];
    foreach ($optional_fields as $field) {
        $data[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : null;
    }

    // El estado siempre es 'activo' para nuevas entradas
    $data['estado'] = 'activo';
    
    // Usuario actual
    $usuario_id = $_SESSION['id'];

    // Iniciar transacción
    $connect->beginTransaction();

    // Verificar que el código no exista
    $stmt_check = $connect->prepare("SELECT id FROM bodega_inventario WHERE codigo_g = ?");
    $stmt_check->execute([$data['codigo_g']]);
    if ($stmt_check->rowCount() > 0) {
        throw new Exception('Ya existe un equipo con este código general');
    }

    // Verificar que el serial no exista
    $stmt_check_serial = $connect->prepare("SELECT id FROM bodega_inventario WHERE serial = ?");
    $stmt_check_serial->execute([$data['serial']]);
    if ($stmt_check_serial->rowCount() > 0) {
        throw new Exception('Ya existe un equipo con este número de serie');
    }

    // Verificar que el proveedor existe
    $stmt_prov = $connect->prepare("SELECT id FROM proveedores WHERE id = ?");
    $stmt_prov->execute([$data['proveedor']]);
    if ($stmt_prov->rowCount() === 0) {
        throw new Exception('Proveedor no válido');
    }

    // Insertar en bodega_inventario
    $sql_inventario = "INSERT INTO bodega_inventario (
        codigo_g, ubicacion, posicion, producto, marca, serial, modelo, 
        procesador, ram, disco, pulgadas, observaciones, grado, disposicion, 
        estado, tactil, fecha_ingreso, fecha_modificacion
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

    $stmt_inventario = $connect->prepare($sql_inventario);
    $stmt_inventario->execute([
        $data['codigo_g'],
        $data['ubse'],
        $data['posicion'],
        $data['producto'],
        $data['marca'],
        $data['serial'],
        $data['modelo'],
        $data['procesador'],
        $data['ram'],
        $data['disco'],
        $data['pulgadas'],
        $data['observaciones'],
        $data['grado'],
        $data['disposicion'],
        $data['estado'],
        $data['tactil']
    ]);

    // Obtener el ID del inventario insertado
    $inventario_id = $connect->lastInsertId();

    // Insertar en bodega_entradas
    $sql_entrada = "INSERT INTO bodega_entradas (
        inventario_id, proveedor_id, usuario_id, cantidad, observaciones, fecha_entrada
    ) VALUES (?, ?, ?, 1, ?, NOW())";

    $stmt_entrada = $connect->prepare($sql_entrada);
    $stmt_entrada->execute([
        $inventario_id,
        $data['proveedor'],
        $usuario_id,
        $data['observaciones']
    ]);

    // Confirmar transacción
    $connect->commit();

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Entrada registrada exitosamente',
        'inventario_id' => $inventario_id,
        'codigo_g' => $data['codigo_g']
    ]);

} catch (PDOException $e) {
    // Rollback en caso de error de base de datos
    if ($connect->inTransaction()) {
        $connect->rollBack();
    }
    
    http_response_code(500);
    error_log("Error PDO en st_add_entrada: " . $e->getMessage());
    
    // Mensajes de error más específicos para problemas comunes
    $error_message = 'Error de base de datos';
    if (strpos($e->getMessage(), 'codigo_g') !== false) {
        $error_message = 'El código general ya existe';
    } elseif (strpos($e->getMessage(), 'serial') !== false) {
        $error_message = 'El número de serie ya existe';
    } elseif (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        $error_message = 'Ya existe un registro con estos datos';
    }
    
    echo json_encode(['success' => false, 'error' => $error_message]);

} catch (Exception $e) {
    // Rollback en caso de error general
    if ($connect->inTransaction()) {
        $connect->rollBack();
    }
    
    http_response_code(400);
    error_log("Error en st_add_entrada: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>