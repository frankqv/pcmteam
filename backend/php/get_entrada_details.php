<?php
// /backend/php/get_entrada_details.php - VERSIÓN CON DEPURACIÓN
session_start();
require_once '../bd/ctconex.php';

header('Content-Type: application/json');

// === CÓDIGO DE DEPURACIÓN - TEMPORAL ===
$debug_info = [
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'NO_SET',
    'GET_params' => $_GET,
    'POST_params' => $_POST,
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'NO_SET',
    'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? 'NO_SET',
    'session_rol' => $_SESSION['rol'] ?? 'NO_SESSION'
];

// Log para el servidor
error_log("=== DEBUG ENTRADA DETAILS ===");
error_log(json_encode($debug_info, JSON_PRETTY_PRINT));

// Si quieres ver la info directamente en la respuesta (SOLO PARA DEBUG)
// Descomenta estas líneas temporalmente:
/*
echo json_encode([
    'debug' => true,
    'info' => $debug_info
]);
exit;
*/

// Validar autenticación
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit;
}

// Validar que sea GET y que tenga el ID
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit;
}

$entrada_id = intval($_GET['id']);

if ($entrada_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de entrada inválido']);
    exit;
}

try {
    // Consulta para obtener todos los detalles de la entrada
    $sql = "SELECT 
                e.id,
                e.fecha_entrada,
                e.cantidad,
                e.observaciones as entrada_observaciones,
                i.codigo_g,
                i.item,
                i.ubicacion,
                i.posicion,
                i.fecha_ingreso,
                i.fecha_modificacion,
                i.activo_fijo,
                i.codigo_lote,
                i.producto,
                i.marca,
                i.serial,
                i.modelo,
                i.procesador,
                i.ram,
                i.disco,
                i.pulgadas,
                i.observaciones,
                i.grado,
                i.disposicion,
                i.estado,
                p.nombre as proveedor_nombre,
                p.nomenclatura as proveedor_nomenclatura,
                p.correo as proveedor_correo,
                p.celu as proveedor_telefono,
                u.nombre as usuario_nombre,
                u.rol as usuario_rol
            FROM bodega_entradas e 
            LEFT JOIN bodega_inventario i ON e.inventario_id = i.id 
            LEFT JOIN proveedores p ON e.proveedor_id = p.id 
            LEFT JOIN usuarios u ON e.usuario_id = u.id
            WHERE e.id = ?";
    
    $stmt = $connect->prepare($sql);
    $stmt->execute([$entrada_id]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Formatear fechas para mejor presentación
        if ($result['fecha_entrada']) {
            $result['fecha_entrada'] = date('d/m/Y H:i', strtotime($result['fecha_entrada']));
        }
        if ($result['fecha_ingreso']) {
            $result['fecha_ingreso'] = date('d/m/Y H:i', strtotime($result['fecha_ingreso']));
        }
        if ($result['fecha_modificacion']) {
            $result['fecha_modificacion'] = date('d/m/Y H:i', strtotime($result['fecha_modificacion']));
        }
        
        // Mapear rol de usuario a texto legible
        $roles = [
            1 => 'Administrador',
            6 => 'Técnico',
            7 => 'Bodega'
        ];
        $result['usuario_rol_texto'] = $roles[$result['usuario_rol']] ?? 'Desconocido';
        
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Entrada no encontrada']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al consultar la base de datos',
        'details' => $e->getMessage()
    ]);
    error_log("Error en get_entrada_details.php: " . $e->getMessage());
}
?>