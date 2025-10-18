<?php
require_once __DIR__ . '../../../config/ctconex.php';
// Verificar si el usuario está logueado y tiene permisos
if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}
try {
    // Obtener datos del formulario
    $inventario_id = intval($_POST['inventario_id']);
    $tecnico_id = intval($_POST['tecnico_id']);
    $razon_salida = $_POST['razon_salida'];
    $observaciones = $_POST['observaciones'] ?? null;
    // Iniciar transacción
    $conn->begin_transaction();
    // Verificar que el equipo existe y está disponible
    $sql_check = "SELECT disposicion FROM bodega_inventario WHERE id = ? AND estado = 'activo'";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("i", $inventario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('Equipo no encontrado o no disponible');
    }
    $equipo = $result->fetch_assoc();
    if (in_array($equipo['disposicion'], ['entregado', 'baja'])) {
        throw new Exception('El equipo no está disponible para salida');
    }
    // Determinar nueva disposición según razón de salida
    $nueva_disposicion = match ($razon_salida) {
        'reparacion' => 'en_reparacion',
        'diagnostico' => 'en_diagnostico',
        'control_calidad' => 'en_control_calidad',
        'entrega' => 'entregado',
        'baja' => 'baja',
        default => $equipo['disposicion']
    };
    // Actualizar estado del equipo
    $sql_update = "UPDATE bodega_inventario SET disposicion = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("si", $nueva_disposicion, $inventario_id);
    $stmt->execute();
    // Registrar salida
    $sql_salida = "INSERT INTO bodega_salidas (
        inventario_id, tecnico_id, usuario_id, razon_salida, observaciones
    ) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_salida);
    $stmt->bind_param("iiiss", $inventario_id, $tecnico_id, $_SESSION['id'], $razon_salida, $observaciones);
    $stmt->execute();
    // Si es para diagnóstico, crear registro en bodega_diagnosticos
    if ($razon_salida === 'diagnostico') {
        $sql_diagnostico = "INSERT INTO bodega_diagnosticos (
            inventario_id, tecnico_id, estado_reparacion
        ) VALUES (?, ?, 'pendiente')";
        $stmt = $conn->prepare($sql_diagnostico);
        $stmt->bind_param("ii", $inventario_id, $tecnico_id);
        $stmt->execute();
    }
    // Si es para control de calidad, crear registro en bodega_control_calidad
    if ($razon_salida === 'control_calidad') {
        $sql_calidad = "INSERT INTO bodega_control_calidad (
            inventario_id, tecnico_id, estado_final
        ) VALUES (?, ?, 'pendiente')";
        $stmt = $conn->prepare($sql_calidad);
        $stmt->bind_param("ii", $inventario_id, $tecnico_id);
        $stmt->execute();
    }
    // Confirmar transacción
    $conn->commit();
    // Responder éxito
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Salida registrada exitosamente']);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
$conn->close(); 