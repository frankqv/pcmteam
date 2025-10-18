<?php
/**
 * Script para procesar el envío masivo de equipos desde Business Room a Ventas
 * Cambia el estado de múltiples equipos a 'Para Venta'
 */
session_start();
require_once '../../config/ctconex.php';
// Verificar permisos (solo administradores y roles autorizados)
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2,3,4, 5, 6, 7])) {
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
// Obtener los IDs de los equipos a enviar
$input = json_decode(file_get_contents('php://input'), true);
$equipos_ids = $input['equipos_ids'] ?? [];
if (empty($equipos_ids) || !is_array($equipos_ids)) {
    echo json_encode(['status' => 'error', 'message' => 'No se proporcionaron equipos válidos']);
    exit();
}
try {
    // Iniciar transacción
    $conn->begin_transaction();
    $equipos_procesados = 0;
    $equipos_no_calificados = [];
    $equipos_ya_en_venta = [];
    foreach ($equipos_ids as $equipo_id) {
        // Validar que el ID sea numérico
        if (!is_numeric($equipo_id)) {
            continue;
        }
        // Verificar que el equipo existe y está en estado apropiado
        $stmt_check = $conn->prepare("
            SELECT id, disposicion, precio, codigo_g, marca, modelo 
            FROM bodega_inventario 
            WHERE id = ? AND estado = 'activo'
        ");
        $stmt_check->bind_param("i", $equipo_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($equipo = $result_check->fetch_assoc()) {
            // Verificar que tenga precio asignado
            if (empty($equipo['precio']) || $equipo['precio'] <= 0) {
                $equipos_no_calificados[] = $equipo['codigo_g'] . ' (' . $equipo['marca'] . ' ' . $equipo['modelo'] . ')';
                continue;
            }
            // Verificar que no esté ya en venta
            if ($equipo['disposicion'] === 'Para Venta') {
                $equipos_ya_en_venta[] = $equipo['codigo_g'] . ' (' . $equipo['marca'] . ' ' . $equipo['modelo'] . ')';
                continue;
            }
            // Actualizar el estado a 'Para Venta'
            $stmt_update = $conn->prepare("
                UPDATE bodega_inventario 
                SET disposicion = 'Para Venta', 
                    fecha_modificacion = NOW(),
                    tecnico_id = ?
                WHERE id = ? AND estado = 'activo'
            ");
            $stmt_update->bind_param("ii", $_SESSION['id'], $equipo_id);
            if ($stmt_update->execute()) {
                $equipos_procesados++;
                // Registrar en log de cambios
                $stmt_log = $conn->prepare("
                    INSERT INTO bodega_log_cambios 
                    (inventario_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo, tipo_cambio)
                    VALUES (?, ?, 'disposicion', ?, 'Para Venta', 'sistema')
                ");
                $stmt_log->bind_param("iis", $equipo_id, $_SESSION['id'], $equipo['disposicion']);
                $stmt_log->execute();
            }
        }
    }
    // Confirmar transacción
    $conn->commit();
    // Preparar respuesta
    $response = [
        'status' => 'success',
        'message' => "Se procesaron {$equipos_procesados} equipos exitosamente",
        'equipos_procesados' => $equipos_procesados,
        'equipos_no_calificados' => $equipos_no_calificados,
        'equipos_ya_en_venta' => $equipos_ya_en_venta
    ];
    // Agregar mensajes adicionales si hay equipos no procesados
    if (!empty($equipos_no_calificados)) {
        $response['warnings'] = 'Algunos equipos no tienen precio asignado: ' . implode(', ', $equipos_no_calificados);
    }
    if (!empty($equipos_ya_en_venta)) {
        $response['info'] = 'Algunos equipos ya estaban en venta: ' . implode(', ', $equipos_ya_en_venta);
    }
    echo json_encode($response);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    error_log("Error en procesar_envio_ventas.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor. Intente nuevamente.'
    ]);
} ?>