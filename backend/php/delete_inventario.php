<?php
session_start();
require_once __DIR__ . '../../../config/ctconex.php';

header('Content-Type: application/json');

// Validar autenticación
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit;
}

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Validar que tenga el ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de equipo requerido']);
    exit;
}

$inventario_id = intval($_POST['id']);

if ($inventario_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de inventario inválido']);
    exit;
}

try {
    // Verificar que el equipo existe
    $stmt = $connect->prepare("SELECT id, codigo_g FROM bodega_inventario WHERE id = ?");
    $stmt->execute([$inventario_id]);
    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipo) {
        http_response_code(404);
        echo json_encode(['error' => 'Equipo no encontrado']);
        exit;
    }

    // Iniciar transacción
    $connect->beginTransaction();

    // Eliminar registros relacionados primero (por las foreign keys)
    $connect->prepare("DELETE FROM bodega_control_calidad WHERE inventario_id = ?")->execute([$inventario_id]);
    $connect->prepare("DELETE FROM bodega_diagnosticos WHERE inventario_id = ?")->execute([$inventario_id]);
    $connect->prepare("DELETE FROM bodega_entradas WHERE inventario_id = ?")->execute([$inventario_id]);
    $connect->prepare("DELETE FROM bodega_salidas WHERE inventario_id = ?")->execute([$inventario_id]);
    
    // Finalmente eliminar el equipo del inventario
    $stmt = $connect->prepare("DELETE FROM bodega_inventario WHERE id = ?");
    $stmt->execute([$inventario_id]);

    // Confirmar transacción
    $connect->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Equipo eliminado exitosamente',
        'codigo' => $equipo['codigo_g']
    ]);

} catch (PDOException $e) {
    // Revertir transacción en caso de error
    if ($connect->inTransaction()) {
        $connect->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al eliminar el equipo de la base de datos', 
        'detalle' => $e->getMessage()
    ]);
    error_log("Error en delete_inventario.php: " . $e->getMessage());
}
?> 