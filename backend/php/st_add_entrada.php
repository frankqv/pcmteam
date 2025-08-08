<?php
// Activar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errores.log');

session_start();
require_once '../bd/ctconex.php';

header('Content-Type: application/json');

// 1. Validar autenticación
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit;
}

// 2. Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// 3. Validar campos requeridos
$required = [
    'codigo_g', 'ubse', 'posicion', 'producto', 'marca', 'serial', 'modelo',
    'ram', 'grado', 'disposicion', 'proveedor'
];

foreach ($required as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        http_response_code(400);
        echo json_encode(['error' => "Falta el campo requerido: $field"]);
        exit;
    }
}

try {
    // 4. Iniciar transacción
    $connect->beginTransaction();

    // 5. Insertar en bodega_inventario
    $sql_inv = "INSERT INTO bodega_inventario 
        (codigo_g, ubicacion, posicion, producto, marca, serial, modelo, procesador, ram, disco, pulgadas, observaciones, grado, disposicion, estado)
        VALUES
        (:codigo_g, :ubicacion, :posicion, :producto, :marca, :serial, :modelo, :procesador, :ram, :disco, :pulgadas, :observaciones, :grado, :disposicion, :estado)";
    
    $stmt_inv = $connect->prepare($sql_inv);
    $stmt_inv->execute([
        ':codigo_g' => $_POST['codigo_g'],
        ':ubicacion' => $_POST['ubse'],
        ':posicion' => $_POST['posicion'],
        ':producto' => $_POST['producto'],
        ':marca' => $_POST['marca'],
        ':serial' => $_POST['serial'],
        ':modelo' => $_POST['modelo'],
        ':procesador' => $_POST['procesador'] ?? '',
        ':ram' => $_POST['ram'],
        ':disco' => $_POST['disco'] ?? '',
        ':pulgadas' => $_POST['pulgadas'] ?? '',
        ':observaciones' => $_POST['observaciones'] ?? '',
        ':grado' => $_POST['grado'],
        ':disposicion' => $_POST['disposicion'],
        ':estado' => 'activo'
    ]);

    $inventario_id = $connect->lastInsertId();

    // 6. Insertar en bodega_entradas
    $sql_ent = "INSERT INTO bodega_entradas 
        (inventario_id, proveedor_id, usuario_id, observaciones)
        VALUES
        (:inventario_id, :proveedor_id, :usuario_id, :observaciones)";
    
    $stmt_ent = $connect->prepare($sql_ent);
    $stmt_ent->execute([
        ':inventario_id' => $inventario_id,
        ':proveedor_id' => $_POST['proveedor'],
        ':usuario_id' => $_SESSION['id'] ?? null,
        ':observaciones' => $_POST['observaciones'] ?? ''
    ]);

    // 7. Confirmar transacción
    $connect->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Entrada registrada exitosamente',
        'inventario_id' => $inventario_id
    ]);

} catch (PDOException $e) {
    // Revertir transacción en caso de error
    if ($connect->inTransaction()) {
        $connect->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'error' => 'Error al guardar en la base de datos',
        'detalle' => $e->getMessage()
    ]);

    error_log("Error en st_add_entrada.php: " . $e->getMessage());
}
?>


