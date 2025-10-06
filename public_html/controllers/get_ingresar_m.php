<?php
// Evitar cualquier salida previa
ob_start();
header('Content-Type: application/json; charset=utf-8');
// Validar que se recibió el ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Falta parámetro id']);
    exit;
}
$id = (int) $_GET['id'];
if ($id <= 0) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}
// Incluir conexión
require_once dirname(__DIR__, 2) . '/config/ctconex.php';
// Verificar conexión
if (!isset($conn)) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Conexión a BD no disponible']);
    exit;
}
// Limpiar buffer
ob_end_clean();
try {
    // Consulta para obtener el mantenimiento completo con datos del equipo
    $sql = "
    SELECT 
        m.*,
        inv.codigo_g,
        inv.producto,
        inv.marca,
        inv.modelo,
        inv.serial,
        inv.grado,
        inv.disposicion,
        ut.nombre AS tecnico_nombre,
        ur.nombre AS usuario_registro_nombre
    FROM bodega_mantenimiento m
    LEFT JOIN bodega_inventario inv ON m.inventario_id = inv.id
    LEFT JOIN usuarios ut ON m.tecnico_id = ut.id
    LEFT JOIN usuarios ur ON m.usuario_registro = ur.id
    WHERE m.id = ?
    LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar consulta: ' . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        // Asegurar que todos los campos existan (evitar undefined)
        $response = [
            'success' => true,
            'data' => [
                // Información del equipo
                'codigo_g' => $data['codigo_g'] ?? '',
                'producto' => $data['producto'] ?? '',
                'marca' => $data['marca'] ?? '',
                'modelo' => $data['modelo'] ?? '',
                'serial' => $data['serial'] ?? '',
                'grado' => $data['grado'] ?? '',
                'disposicion' => $data['disposicion'] ?? '',
                // Información del mantenimiento
                'fecha_registro' => $data['fecha_registro'] ?? '',
                'tecnico_id' => $data['tecnico_id'] ?? '',
                'tecnico_nombre' => $data['tecnico_nombre'] ?? '',
                'usuario_registro' => $data['usuario_registro'] ?? '',
                'usuario_registro_nombre' => $data['usuario_registro_nombre'] ?? '',
                'estado' => $data['estado'] ?? '',
                // Fallas
                'falla_electrica' => $data['falla_electrica'] ?? '',
                'detalle_falla_electrica' => $data['detalle_falla_electrica'] ?? '',
                'falla_estetica' => $data['falla_estetica'] ?? '',
                'detalle_falla_estetica' => $data['detalle_falla_estetica'] ?? '',
                // Proceso de mantenimiento
                'limpieza_electronico' => $data['limpieza_electronico'] ?? '',
                'observaciones_limpieza_electronico' => $data['observaciones_limpieza_electronico'] ?? '',
                'mantenimiento_crema_disciplinaria' => $data['mantenimiento_crema_disciplinaria'] ?? '',
                'observaciones_mantenimiento_crema' => $data['observaciones_mantenimiento_crema'] ?? '',
                'mantenimiento_partes' => $data['mantenimiento_partes'] ?? '',
                'cambio_piezas' => $data['cambio_piezas'] ?? '',
                'piezas_solicitadas_cambiadas' => $data['piezas_solicitadas_cambiadas'] ?? '',
                'proceso_reconstruccion' => $data['proceso_reconstruccion'] ?? '',
                'parte_reconstruida' => $data['parte_reconstruida'] ?? '',
                'limpieza_general' => $data['limpieza_general'] ?? '',
                // Información adicional
                'referencia_externa' => $data['referencia_externa'] ?? '',
                'partes_solicitadas' => $data['partes_solicitadas'] ?? '',
                'remite_otra_area' => $data['remite_otra_area'] ?? '',
                'area_remite' => $data['area_remite'] ?? '',
                'proceso_electronico' => $data['proceso_electronico'] ?? '',
                'observaciones_globales' => $data['observaciones_globales'] ?? '',
                'observaciones' => $data['observaciones'] ?? '',
            ]
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Mantenimiento no encontrado'
        ]);
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Error en get_ingresar_m.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
$conn->close();
