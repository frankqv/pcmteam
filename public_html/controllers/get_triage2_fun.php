<?php
// backend/php/get_triage_details.php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
    exit;
}

require_once '../../config/ctconex.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de inventario requerido']);
    exit;
}

$inventario_id = intval($_GET['id']);

try {
    // Primero verificar si existe la tabla bodega_diagnosticos
    $table_check = $conn->query("SHOW TABLES LIKE 'bodega_diagnosticos'");
    
    if ($table_check->num_rows == 0) {
        echo json_encode(['success' => false, 'error' => 'Tabla bodega_diagnosticos no existe']);
        exit;
    }

    // Consulta para obtener el último diagnóstico y los datos del inventario
    $sql = "SELECT 
                i.codigo_g, i.producto, i.marca, i.modelo, i.serial, 
                i.observaciones as inventario_observaciones,
                d.fecha_diagnostico,
                d.camara, d.teclado, d.parlantes, d.bateria, d.microfono,
                d.pantalla, d.puertos, d.disco,
                d.falla_electrica, d.detalle_falla_electrica,
                d.falla_estetica, d.detalle_falla_estetica,
                d.estado_reparacion, d.observaciones,
                u.nombre as tecnico_nombre
            FROM bodega_inventario i
            LEFT JOIN (
                SELECT d1.*
                FROM bodega_diagnosticos d1
                INNER JOIN (
                    SELECT inventario_id, MAX(fecha_diagnostico) AS max_fecha
                    FROM bodega_diagnosticos
                    GROUP BY inventario_id
                ) d2 ON d1.inventario_id = d2.inventario_id AND d1.fecha_diagnostico = d2.max_fecha
            ) d ON i.id = d.inventario_id
            LEFT JOIN usuarios u ON d.tecnico_id = u.id
            WHERE i.id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $inventario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_assoc();
    
    if (!$details) {
        echo json_encode(['success' => false, 'error' => 'No se encontraron detalles para este equipo.']);
        exit;
    }
    
    // Formatear la fecha si existe
    if ($details['fecha_diagnostico']) {
        $details['fecha_diagnostico'] = date('d/m/Y H:i', strtotime($details['fecha_diagnostico']));
    }
    
    echo json_encode(['success' => true, 'data' => $details]);
    
} catch (Exception $e) {
    error_log("Error en get_triage_details.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error al consultar la base de datos: ' . $e->getMessage()]);
}
?>