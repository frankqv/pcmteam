<?php
require_once 'ctlogx.php';
require_once '../bd/ctconex.php';

// Verificar si el usuario está logueado y tiene permisos
if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    // Obtener datos del formulario
    $codigo_g = $_POST['codigo_g'];
    $ubicacion = $_POST['ubicacion'];
    $posicion = $_POST['posicion'];
    $codigo_lote = $_POST['codigo_lote'];
    $producto = $_POST['producto'];
    $marca = $_POST['marca'];
    $serial = $_POST['serial'];
    $modelo = $_POST['modelo'];
    $procesador = $_POST['procesador'] ?? null;
    $ram = $_POST['ram'] ?? null;
    $disco = $_POST['disco'] ?? null;
    $pulgadas = $_POST['pulgadas'] ?? null;
    $observaciones = $_POST['observaciones'] ?? null;
    $grado = $_POST['grado'];
    $disposicion = 'recepcion'; // Estado inicial

    // Iniciar transacción
    $conn->begin_transaction();

    // Insertar en bodega_inventario
    $sql_inventario = "INSERT INTO bodega_inventario (
        codigo_g, item, ubicacion, posicion, codigo_lote, producto, 
        marca, serial, modelo, procesador, ram, disco, pulgadas, 
        observaciones, grado, disposicion
    ) VALUES (
        ?, (SELECT COALESCE(MAX(item), 0) + 1 FROM bodega_inventario bi), 
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";

    $stmt = $conn->prepare($sql_inventario);
    $stmt->bind_param(
        "sssssssssssssss",
        $codigo_g, $ubicacion, $posicion, $codigo_lote, $producto,
        $marca, $serial, $modelo, $procesador, $ram, $disco, $pulgadas,
        $observaciones, $grado, $disposicion
    );
    $stmt->execute();
    $inventario_id = $conn->insert_id;

    // Insertar en bodega_entradas
    $sql_entrada = "INSERT INTO bodega_entradas (
        inventario_id, proveedor_id, usuario_id, observaciones
    ) VALUES (?, 1, ?, ?)"; // Proveedor_id 1 por defecto, actualizar según necesidad

    $stmt = $conn->prepare($sql_entrada);
    $stmt->bind_param("iis", $inventario_id, $_SESSION['id'], $observaciones);
    $stmt->execute();

    // Confirmar transacción
    $conn->commit();

    // Responder éxito
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Entrada registrada exitosamente']);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close(); 