<?php
// /backend/php/get_entrada_details.php
header('Content-Type: application/json');
session_start();
// Verificar autenticación
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
    exit;
}
require_once '../../config/ctconex.php';
// Verificar que se recibió el ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de entrada requerido']);
    exit;
}
$entrada_id = intval($_GET['id']);
try {
    // Consulta para obtener detalles completos de la entrada
    $sql = "SELECT 
                e.id,
                e.fecha_entrada,
                e.cantidad,
                e.observaciones as entrada_observaciones,
                i.codigo_g,
                i.producto,
                i.marca,
                i.modelo,
                i.serial,
                i.procesador,
                i.ram,
                i.disco,
                i.pulgadas,
                i.observaciones,
                i.grado,
                i.disposicion,
                i.ubicacion,
                i.posicion,
                i.lote,
                i.tactil,
                i.estado,
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
    $entrada = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$entrada) {
        echo json_encode(['success' => false, 'error' => 'Entrada no encontrada']);
        exit;
    }
    // Formatear fecha para mejor visualización
    if ($entrada['fecha_entrada']) {
        $entrada['fecha_entrada'] = date('d/m/Y H:i', strtotime($entrada['fecha_entrada']));
    }
    echo json_encode([
        'success' => true,
        'data' => $entrada
    ]);
} catch (PDOException $e) {
    error_log("Error en get_entrada_details.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al consultar la base de datos'
    ]);
} catch (Exception $e) {
    error_log("Error general en get_entrada_details.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor'
    ]);
}
?>