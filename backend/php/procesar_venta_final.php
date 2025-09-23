<?php
// backend/php/procesar_venta_final.php
session_start();
require_once '../../config/ctconex.php';

// Verificar autenticación
if (!isset($_SESSION['id']) || !in_array($_SESSION['rol'], [1, 2, 3, 4])) {
    header('Location: ../../frontend/error404.php');
    exit();
}

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../venta/nuevo.php');
    exit();
}

try {
    // Obtener datos del formulario
    $cliente_id = intval($_POST['cliente_id'] ?? 0);
    $metodo_pago = trim($_POST['metodo_pago'] ?? '');
    $carrito_json = $_POST['carrito_json'] ?? '';
    $total_venta = floatval($_POST['total_venta'] ?? 0);
    $usuario_id = $_SESSION['id'];
    
    // Validar datos básicos
    if (empty($cliente_id) || empty($metodo_pago) || empty($carrito_json) || $total_venta <= 0) {
        throw new Exception('Faltan datos requeridos para procesar la venta');
    }
    
    // Decodificar carrito
    $carrito = json_decode($carrito_json, true);
    if (!$carrito || !is_array($carrito) || empty($carrito)) {
        throw new Exception('El carrito está vacío o tiene formato inválido');
    }
    
    // Verificar que el cliente existe y está activo
    $stmt = $connect->prepare("SELECT idclie, nomcli, apecli FROM clientes WHERE idclie = ? AND estad = 'Activo'");
    $stmt->execute([$cliente_id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cliente) {
        throw new Exception('Cliente no encontrado o inactivo');
    }
    
    // Iniciar transacción
    $connect->beginTransaction();
    
    $total_calculado = 0;
    $total_items = 0;
    $productos_vendidos = [];
    
    // Procesar cada producto del carrito
    foreach ($carrito as $item_carrito) {
        $cantidad_solicitada = intval($item_carrito['cantidad'] ?? 0);
        $precio_unitario = floatval($item_carrito['precio'] ?? 0);
        
        if ($cantidad_solicitada <= 0 || $precio_unitario <= 0) {
            throw new Exception('Cantidad o precio inválido en el carrito');
        }
        
        // Buscar productos exactos en inventario disponibles para venta
        $stmt = $connect->prepare("
            SELECT id, codigo_g, serial, precio 
            FROM bodega_inventario 
            WHERE marca = ? AND modelo = ? 
            AND procesador = ? AND ram = ? AND disco = ? 
            AND disposicion = 'Para Venta' 
            AND estado = 'activo'
            ORDER BY id ASC
            LIMIT ?
        ");
        
        $stmt->execute([
            $item_carrito['marca'],
            $item_carrito['modelo'],
            $item_carrito['procesador'] ?? '',
            $item_carrito['ram'] ?? '',
            $item_carrito['disco'] ?? '',
            $cantidad_solicitada
        ]);
        
        $productos_encontrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($productos_encontrados) < $cantidad_solicitada) {
            throw new Exception("Stock insuficiente para {$item_carrito['marca']} {$item_carrito['modelo']}. " .
                "Disponible: " . count($productos_encontrados) . ", Solicitado: {$cantidad_solicitada}");
        }
        
        // Agregar productos encontrados a la lista de venta
        foreach ($productos_encontrados as $producto) {
            $productos_vendidos[] = [
                'inventario_id' => $producto['id'],
                'codigo_g' => $producto['codigo_g'],
                'serial' => $producto['serial'],
                'precio_unitario' => $precio_unitario,
                'marca' => $item_carrito['marca'],
                'modelo' => $item_carrito['modelo']
            ];
            
            $total_calculado += $precio_unitario;
            $total_items++;
        }
    }
    
    // Verificar que el total calculado coincide con el enviado
    if (abs($total_calculado - $total_venta) > 0.01) {
        throw new Exception("Discrepancia en el total de la venta. Calculado: $total_calculado, Enviado: $total_venta");
    }
    
    if (empty($productos_vendidos)) {
        throw new Exception('No se encontraron productos válidos para la venta');
    }
    
    // Crear orden principal en bodega_ordenes (uso interno de bodega)
    $stmt = $connect->prepare("
        INSERT INTO bodega_ordenes 
        (cliente_id, responsable, total_items, total_pago, metodo_pago, estado_pago, tipo_doc, creado_por, created_at) 
        VALUES (?, ?, ?, ?, ?, 'Aceptado', 'ticket', ?, NOW())
    ");
    $stmt->execute([
        $cliente_id,
        $usuario_id,
        $total_items,
        $total_calculado,
        $metodo_pago,
        $usuario_id
    ]);
    $orden_id_bodega = $connect->lastInsertId();

    // Crear orden en tabla legacy `orders` para flujo de despacho (pendientes)
    $responsable_nombre = '';
    try {
        $sUsr = $connect->prepare("SELECT nombre FROM usuarios WHERE id = ?");
        $sUsr->execute([$usuario_id]);
        $responsable_nombre = ($sUsr->fetch(PDO::FETCH_ASSOC)['nombre'] ?? '') ?: 'Usuario';
    } catch (Exception $e) { $responsable_nombre = 'Usuario'; }

    $stmt_legacy = $connect->prepare("
        INSERT INTO orders 
        (user_id, user_cli, method, total_products, total_price, placed_on, payment_status, tipc, despacho, responsable)
        VALUES (?, ?, ?, ?, ?, NOW(), 'Aceptado', '0', 'Pendiente', ?)
    ");
    $total_products_text = (string)$total_items;
    $stmt_legacy->execute([
        $usuario_id,
        $cliente_id,
        $metodo_pago,
        $total_products_text,
        $total_calculado,
        $responsable_nombre
    ]);
    $orden_id_legacy = $connect->lastInsertId();
    
    // Crear detalles de venta y procesar cada producto
    // Los detalles de venta deben referenciar `orders.idord` para que Despachos -> Pendientes los encuentre
    $stmt_detalle = $connect->prepare("
        INSERT INTO venta_detalles (orden_id, inventario_id, serial, codigo_g, precio_unitario, fecha_venta) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt_salida = $connect->prepare("
        INSERT INTO bodega_salidas 
        (inventario_id, cliente_id, tecnico_id, usuario_id, orden_id, cantidad, precio_unit, razon_salida, observaciones, estado_despacho, fecha_salida) 
        VALUES (?, ?, ?, ?, ?, 1, ?, 'Venta directa', ?, 'pendiente', NOW())
    ");
    
    $stmt_update_inventario = $connect->prepare("
        UPDATE bodega_inventario 
        SET disposicion = 'Por Alistamiento', fecha_modificacion = NOW() 
        WHERE id = ?
    ");
    
    $stmt_log = $connect->prepare("
        INSERT INTO bodega_log_cambios 
        (inventario_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo, tipo_cambio) 
        VALUES (?, ?, 'disposicion', 'Para Venta', 'Por Alistamiento', 'sistema')
    ");
    
    foreach ($productos_vendidos as $producto) {
        // Insertar detalle de venta (apunta a tabla legacy `orders`)
        $stmt_detalle->execute([
            $orden_id_legacy,
            $producto['inventario_id'],
            $producto['serial'],
            $producto['codigo_g'],
            $producto['precio_unitario']
        ]);
        
        // Crear registro de salida
        $observaciones = "Venta a {$cliente['nomcli']} {$cliente['apecli']} - Orden #{$orden_id_legacy} - {$metodo_pago}";
        
        $stmt_salida->execute([
            $producto['inventario_id'],
            $cliente_id,
            $usuario_id, // tecnico_id
            $usuario_id, // usuario_id  
            $orden_id_bodega,
            $producto['precio_unitario'],
            $observaciones
        ]);
        
        // Actualizar estado del inventario
        $stmt_update_inventario->execute([$producto['inventario_id']]);
        
        // Crear log de cambio
        $stmt_log->execute([$producto['inventario_id'], $usuario_id]);
    }
    
    // Crear registro de ingreso
    $stmt_ingreso = $connect->prepare("
        INSERT INTO bodega_ingresos (orden_id, monto, metodo_pago, recibido_por, notas, fecha_ingreso) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $notas_ingreso = "Venta procesada - {$total_items} productos - Cliente: {$cliente['nomcli']} {$cliente['apecli']}";
    $stmt_ingreso->execute([
        $orden_id_bodega,
        $total_calculado,
        $metodo_pago,
        $usuario_id,
        $notas_ingreso
    ]);
    
    // Confirmar transacción
    $connect->commit();
    
    // Redirigir con mensaje de éxito
    $_SESSION['mensaje_exito'] = "Venta procesada exitosamente. Orden #{$orden_id_legacy} por $" . number_format($total_calculado, 0, ',', '.');
    $_SESSION['orden_id'] = $orden_id_legacy;
    $_SESSION['total_venta'] = $total_calculado;
    $_SESSION['cliente_nombre'] = $cliente['nomcli'] . ' ' . $cliente['apecli'];
    
    header('Location: ../venta/exito.php');
    exit();
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($connect) && $connect->inTransaction()) {
        $connect->rollback();
    }
    
    $_SESSION['mensaje_error'] = 'Error al procesar la venta: ' . $e->getMessage();
    header('Location: ../venta/nuevo.php');
    exit();
    
} catch (PDOException $e) {
    // Error de base de datos
    if (isset($connect) && $connect->inTransaction()) {
        $connect->rollback();
    }
    
    $_SESSION['mensaje_error'] = 'Error de base de datos: ' . $e->getMessage();
    header('Location: ../venta/nuevo.php');
    exit();
}
?>