<?php
/**
 * Script para procesar ventas de equipos del inventario con trazabilidad de seriales
 */
session_start();
require_once '../../config/ctconex.php';
// Verificar permisos (solo roles de ventas)
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2,3, 4,5,6, 7])) {
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
// Obtener datos de la venta
$input = json_decode(file_get_contents('php://input'), true);
$producto_data = $input['producto_data'] ?? [];
$cantidad = intval($input['cantidad'] ?? 0);
$cliente_id = intval($input['cliente_id'] ?? 0);
$metodo_pago = $input['metodo_pago'] ?? '';
$tipo_comprobante = $input['tipo_comprobante'] ?? '';
// Validaciones
if (empty($producto_data) || $cantidad <= 0 || $cliente_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Datos de venta incompletos']);
    exit();
}
try {
    // Iniciar transacción
    $conn->begin_transaction();
    // Verificar stock disponible
    $sql_stock = "SELECT COUNT(*) as stock_disponible 
                  FROM bodega_inventario 
                  WHERE disposicion = 'Para Venta' 
                  AND estado = 'activo'
                  AND marca = ? AND modelo = ? AND procesador = ? 
                  AND ram = ? AND disco = ? AND grado = ? AND precio = ?";
    $stmt_stock = $conn->prepare($sql_stock);
    $stmt_stock->bind_param(
        "ssssssd",
        $producto_data['marca'],
        $producto_data['modelo'],
        $producto_data['procesador'],
        $producto_data['ram'],
        $producto_data['disco'],
        $producto_data['grado'],
        $producto_data['precio']
    );
    $stmt_stock->execute();
    $stock_result = $stmt_stock->get_result();
    $stock_data = $stock_result->fetch_assoc();
    if ($stock_data['stock_disponible'] < $cantidad) {
        echo json_encode(['status' => 'error', 'message' => 'Stock insuficiente. Disponible: ' . $stock_data['stock_disponible']]);
        exit();
    }
    // Obtener equipos específicos para la venta
    $sql_equipos = "SELECT id, serial, codigo_g 
                    FROM bodega_inventario 
                    WHERE disposicion = 'Para Venta' 
                    AND estado = 'activo'
                    AND marca = ? AND modelo = ? AND procesador = ? 
                    AND ram = ? AND disco = ? AND grado = ? AND precio = ?
                    LIMIT ?";
    $stmt_equipos = $conn->prepare($sql_equipos);
    $stmt_equipos->bind_param(
        "ssssssdi",
        $producto_data['marca'],
        $producto_data['modelo'],
        $producto_data['procesador'],
        $producto_data['ram'],
        $producto_data['disco'],
        $producto_data['grado'],
        $producto_data['precio'],
        $cantidad
    );
    $stmt_equipos->execute();
    $equipos_result = $stmt_equipos->get_result();
    $equipos_vendidos = [];
    $seriales_vendidos = [];
    while ($equipo = $equipos_result->fetch_assoc()) {
        $equipos_vendidos[] = $equipo['id'];
        $seriales_vendidos[] = $equipo['serial'];
        // Actualizar estado del equipo a 'Vendido'
        $stmt_update = $conn->prepare("
            UPDATE bodega_inventario 
            SET disposicion = 'Vendido', 
                fecha_modificacion = NOW()
            WHERE id = ?
        ");
        $stmt_update->bind_param("i", $equipo['id']);
        $stmt_update->execute();
        // Registrar en log de cambios
        $stmt_log = $conn->prepare("
            INSERT INTO bodega_log_cambios 
            (inventario_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo, tipo_cambio)
            VALUES (?, ?, 'disposicion', 'Para Venta', 'Vendido', 'sistema')
        ");
        $stmt_log->bind_param("ii", $equipo['id'], $_SESSION['id']);
        $stmt_log->execute();
    }
    // Calcular totales
    $precio_unitario = floatval($producto_data['precio']);
    $total_venta = $precio_unitario * $cantidad;
    // Crear orden de venta
    $sql_orden = "INSERT INTO orders 
                  (user_id, user_cli, method, total_products, total_price, placed_on, payment_status, tipc, despacho, responsable)
                  VALUES (?, ?, ?, ?, ?, NOW(), 'Aceptado', ?, 'Pendiente', ?)";
    $producto_descripcion = $producto_data['marca'] . ' ' . $producto_data['modelo'] . ' (' . $cantidad . ')';
    $responsable = $_SESSION['nombre'] ?? 'Usuario';
    $stmt_orden = $conn->prepare($sql_orden);
    $stmt_orden->bind_param(
        "iisdsis",
        $_SESSION['id'],
        $cliente_id,
        $metodo_pago,
        $producto_descripcion,
        $total_venta,
        $tipo_comprobante,
        $responsable
    );
    $stmt_orden->execute();
    $orden_id = $conn->insert_id;
    // Crear tabla de detalles de venta si no existe
    $sql_create_table = "CREATE TABLE IF NOT EXISTS venta_detalles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        orden_id INT NOT NULL,
        inventario_id INT NOT NULL,
        serial VARCHAR(100) NOT NULL,
        codigo_g VARCHAR(50) NOT NULL,
        precio_unitario DECIMAL(10,2) NOT NULL,
        fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_orden_id (orden_id),
        INDEX idx_inventario_id (inventario_id)
    )";
    $conn->query($sql_create_table);
    // Insertar detalles de la venta
    foreach ($equipos_vendidos as $index => $inventario_id) {
        $stmt_detalle = $conn->prepare("
            INSERT INTO venta_detalles 
            (orden_id, inventario_id, serial, codigo_g, precio_unitario)
            VALUES (?, ?, ?, ?, ?)
        ");
        $codigo_g = $conn->query("SELECT codigo_g FROM bodega_inventario WHERE id = $inventario_id")->fetch_assoc()['codigo_g'];
        $stmt_detalle->bind_param(
            "iissd",
            $orden_id,
            $inventario_id,
            $seriales_vendidos[$index],
            $codigo_g,
            $precio_unitario
        );
        $stmt_detalle->execute();
    }
    // Registrar ingreso
    $stmt_ingreso = $conn->prepare("
        INSERT INTO ingresos (detalle, total, fec)
        VALUES (?, ?, CURDATE())
    ");
    $detalle_ingreso = "VENTA DE PRODUCTOS - Orden #$orden_id";
    $stmt_ingreso->bind_param("sd", $detalle_ingreso, $total_venta);
    $stmt_ingreso->execute();
    // Confirmar transacción
    $conn->commit();
    // Preparar respuesta
    $response = [
        'status' => 'success',
        'message' => "Venta procesada exitosamente",
        'orden_id' => $orden_id,
        'total_venta' => $total_venta,
        'cantidad_vendida' => $cantidad,
        'seriales_vendidos' => $seriales_vendidos,
        'equipos_vendidos' => $equipos_vendidos
    ];
    echo json_encode($response);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    error_log("Error en procesar_venta_inventario.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor. Intente nuevamente.'
    ]);
} ?>