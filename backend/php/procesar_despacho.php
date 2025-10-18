<?php
/**
 * Script para procesar despachos de órdenes
 */
session_start();
require_once '../../config/ctconex.php';
// Verificar permisos (solo roles de despacho)
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
    exit();
}
// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit();
}
// Obtener el ID de la orden
$input = json_decode(file_get_contents('php://input'), true);
$orden_id = intval($input['orden_id'] ?? 0);
if ($orden_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de orden inválido']);
    exit();
}
try {
    // Iniciar transacción
    $conn->begin_transaction();
    // Verificar que la orden existe y está pendiente
    $stmt_check = $conn->prepare("
        SELECT idord, despacho, payment_status, total_price, user_cli
        FROM orders 
        WHERE idord = ? AND despacho = 'Pendiente' AND payment_status = 'Aceptado'
    ");
    $stmt_check->bind_param("i", $orden_id);
    $stmt_check->execute();
    $orden_result = $stmt_check->get_result();
    if (!$orden = $orden_result->fetch_assoc()) {
        echo json_encode(['status' => 'error', 'message' => 'Orden no encontrada o ya procesada']);
        exit();
    }
    // Actualizar estado de la orden a 'Enviado'
    $stmt_update = $conn->prepare("
        UPDATE orders 
        SET despacho = 'Enviado', 
            responsable = ?
        WHERE idord = ?
    ");
    $responsable = $_SESSION['nombre'] ?? 'Usuario';
    $stmt_update->bind_param("si", $responsable, $orden_id);
    $stmt_update->execute();
    // Obtener detalles de la venta para actualizar inventario
    $stmt_detalles = $conn->prepare("
        SELECT inventario_id, serial, codigo_g
        FROM venta_detalles 
        WHERE orden_id = ?
    ");
    $stmt_detalles->bind_param("i", $orden_id);
    $stmt_detalles->execute();
    $detalles_result = $stmt_detalles->get_result();
    $equipos_despachados = [];
    while ($detalle = $detalles_result->fetch_assoc()) {
        $equipos_despachados[] = $detalle;
        // Actualizar estado del equipo en inventario a 'Despachado'
        $stmt_inventario = $conn->prepare("
            UPDATE bodega_inventario 
            SET disposicion = 'Despachado', 
                fecha_modificacion = NOW()
            WHERE id = ?
        ");
        $stmt_inventario->bind_param("i", $detalle['inventario_id']);
        $stmt_inventario->execute();
        // Registrar en log de cambios
        $stmt_log = $conn->prepare("
            INSERT INTO bodega_log_cambios 
            (inventario_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo, tipo_cambio)
            VALUES (?, ?, 'disposicion', 'Vendido', 'Despachado', 'sistema')
        ");
        $stmt_log->bind_param("ii", $detalle['inventario_id'], $_SESSION['id']);
        $stmt_log->execute();
    }
    // Crear registro de despacho si no existe la tabla
    $sql_create_table = "CREATE TABLE IF NOT EXISTS despachos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        orden_id INT NOT NULL,
        fecha_despacho TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        responsable VARCHAR(100) NOT NULL,
        observaciones TEXT,
        INDEX idx_orden_id (orden_id)
    )";
    $conn->query($sql_create_table);
    // Registrar el despacho
    $stmt_despacho = $conn->prepare("
        INSERT INTO despachos (orden_id, responsable, observaciones)
        VALUES (?, ?, ?)
    ");
    $observaciones = "Despacho procesado automáticamente desde el sistema";
    $stmt_despacho->bind_param("iss", $orden_id, $responsable, $observaciones);
    $stmt_despacho->execute();
    // Confirmar transacción
    $conn->commit();
    // Preparar respuesta
    $response = [
        'status' => 'success',
        'message' => 'Despacho procesado exitosamente',
        'orden_id' => $orden_id,
        'equipos_despachados' => count($equipos_despachados),
        'seriales' => array_column($equipos_despachados, 'serial')
    ];
    echo json_encode($response);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    error_log("Error en procesar_despacho.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor. Intente nuevamente.'
    ]);
} ?>