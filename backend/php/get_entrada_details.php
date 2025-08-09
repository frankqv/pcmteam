<?php
// /backend/php/get_entrada_details.php
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
    // Verificar que sea una petición GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método no permitido');
    }
    // Validar parámetros
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('ID de entrada no válido');
    }
    $entrada_id = intval($_GET['id']);
    // Consulta para obtener detalles completos
    $sql = "SELECT 
        e.id,
        e.fecha_entrada,
        e.cantidad,
        e.observaciones as entrada_observaciones,
        i.codigo_g,
        i.ubicacion,
        i.posicion,
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
        i.tactil,
        p.nombre as proveedor_nombre,
        p.nomenclatura as proveedor_nomenclatura,
        u.nombre as usuario_nombre
    FROM bodega_entradas e 
    LEFT JOIN bodega_inventario i ON e.inventario_id = i.id 
    LEFT JOIN proveedores p ON e.proveedor_id = p.id 
    LEFT JOIN usuarios u ON e.usuario_id = u.id
    WHERE e.id = ?";

    $stmt = $connect->prepare($sql);
    $stmt->execute([$entrada_id]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        throw new Exception('Entrada no encontrada');
    }

    // Formatear fecha
    if ($data['fecha_entrada']) {
        $data['fecha_entrada'] = date('d/m/Y H:i', strtotime($data['fecha_entrada']));
    }

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error PDO en get_entrada_details: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error de base de datos']);

} catch (Exception $e) {
    http_response_code(400);
    error_log("Error en get_entrada_details: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>