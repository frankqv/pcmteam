<?php
// /backend/php/st_add_entrada.php
session_start();
header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no válida']);
    exit;
}
try {
    // Probar la conexión
    require_once __DIR__ . '../../../config/ctconex.php';
   if (!$connect) {
        throw new Exception('No se pudo establecer conexión a la base de datos');
    }
   // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
   // Validar campos requeridos
    $required_fields = ['codigo_g', 'ubse', 'posicion', 'producto', 'marca', 'serial', 'modelo', 'ram', 'grado', 'disposicion', 'proveedor', 'tactil'];
   foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }
   // Preparar datos
    $data = [
        'codigo_g' => trim($_POST['codigo_g']),
        'ubse' => trim($_POST['ubse']),
        'posicion' => trim($_POST['posicion']),
        'producto' => trim($_POST['producto']),
        'marca' => trim($_POST['marca']),
        'serial' => trim($_POST['serial']),
        'modelo' => trim($_POST['modelo']),
        'procesador' => trim($_POST['procesador'] ?? ''),
        'ram' => trim($_POST['ram']),
        'disco' => trim($_POST['disco'] ?? ''),
        'pulgadas' => trim($_POST['pulgadas'] ?? ''),
        'observaciones' => trim($_POST['observaciones'] ?? ''),
        'grado' => trim($_POST['grado']),
        'disposicion' => trim($_POST['disposicion']),
        'estado' => 'activo',
        'tactil' => trim($_POST['tactil']),
        'lote' => trim($_POST['lote'] ?? ''),
        'proveedor_id' => intval($_POST['proveedor'])
    ];
   // Usuario actual
    $usuario_id = $_SESSION['id'];
   // Iniciar transacción
    $connect->beginTransaction();
   // Insertar en bodega_inventario
    $sql_inventario = "INSERT INTO bodega_inventario (
        codigo_g, ubicacion, posicion, producto, marca, serial, modelo, 
        procesador, ram, disco, pulgadas, observaciones, grado, disposicion, 
        estado, tactil, lote, fecha_ingreso, fecha_modificacion
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
   $stmt_inventario = $connect->prepare($sql_inventario);
    if (!$stmt_inventario) {
        throw new Exception("Error en la preparación de la consulta de inventario");
    }
   $stmt_inventario->execute([
        $data['codigo_g'],
        $data['ubse'],           // Se mapea a 'ubicacion' en la BD
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
        $data['tactil'],
        $data['lote']
    ]);
   $inventario_id = $connect->lastInsertId();
   // Insertar en bodega_entradas
    $sql_entrada = "INSERT INTO bodega_entradas (
        inventario_id, proveedor_id, usuario_id, cantidad, observaciones
    ) VALUES (?, ?, ?, 1, ?)";
   $stmt_entrada = $connect->prepare($sql_entrada);
    if (!$stmt_entrada) {
        throw new Exception("Error en la preparación de la consulta de entrada");
    }
   $stmt_entrada->execute([
        $inventario_id,
        $data['proveedor_id'],
        $usuario_id,
        $data['observaciones']
    ]);
   // Confirmar transacción
    $connect->commit();
   echo json_encode([
        'success' => true,
        'message' => 'Equipo registrado exitosamente',
        'inventario_id' => $inventario_id
    ]);
    
} catch (Exception $e) {
    // Rollback en caso de error
    if (isset($connect) && $connect->inTransaction()) {
        $connect->rollback();
    }
    error_log("Error en st_add_entrada.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al registrar el equipo: ' . $e->getMessage()
    ]);
}
?>