<?php
session_start();
header('Content-Type: application/json');
// Incluir el archivo de conexión a la base de datos
require_once __DIR__ . '../../../config/ctconex.php';
// Verificar rol de usuario
$ALLOWED_ROLES = [1, 2, 5, 6, 7];
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], $ALLOWED_ROLES, true)) {
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
    exit();
}
// Asegurarse de que la conexión mysqli esté disponible
if (!isset($mysqli)) {
    if (isset($conn) && $conn instanceof mysqli) {
        $mysqli = $conn;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: No se pudo establecer la conexión a la base de datos.']);
        exit();
    }
}
// Función para obtener un valor de forma segura
function obtenerValorPost($clave, $defecto = '')
{
    return isset($_POST[$clave]) && $_POST[$clave] !== null ? $_POST[$clave] : $defecto;
}
$inventario_id = (int) obtenerValorPost('inventario_id');
if ($inventario_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de inventario inválido.']);
    exit();
}
// Recopilar datos del formulario
$tecnico_diagnostico = (int) obtenerValorPost('tecnico_diagnostico');
$limpieza_electronico = obtenerValorPost('limpieza_electronico');
$observaciones_limpieza_electronico = obtenerValorPost('observaciones_limpieza_electronico');
$mantenimiento_crema_disciplinaria = obtenerValorPost('mantenimiento_crema_disciplinaria');
$observaciones_mantenimiento_crema = obtenerValorPost('observaciones_mantenimiento_crema');
$mantenimiento_partes = obtenerValorPost('mantenimiento_partes');
$cambio_piezas = obtenerValorPost('cambio_piezas');
$piezas_solicitadas_cambiadas = obtenerValorPost('piezas_solicitadas_cambiadas');
$proceso_reconstruccion = obtenerValorPost('proceso_reconstruccion');
$parte_reconstruida = obtenerValorPost('parte_reconstruida');
$limpieza_general = obtenerValorPost('limpieza_general');
$remite_otra_area = obtenerValorPost('remite_otra_area');
$area_remite = obtenerValorPost('area_remite');
$proceso_electronico = obtenerValorPost('proceso_electronico');
$observaciones_globales = obtenerValorPost('observaciones_globales');
// Verificar si existe el registro
$stmt_check = $mysqli->prepare("SELECT id FROM bodega_mantenimiento WHERE inventario_id = ? LIMIT 1");
$stmt_check->bind_param('i', $inventario_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$existe_registro = $result_check->num_rows > 0;
$stmt_check->close();
// Obtener el ID del usuario actual
$usuario_id = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
if ($existe_registro) {
    // Actualizar registro existente
    $query = "UPDATE bodega_mantenimiento SET
                tecnico_diagnostico = ?,
                limpieza_electronico = ?,
                observaciones_limpieza_electronico = ?,
                mantenimiento_crema_disciplinaria = ?,
                observaciones_mantenimiento_crema = ?,
                mantenimiento_partes = ?,
                cambio_piezas = ?,
                piezas_solicitadas_cambiadas = ?,
                proceso_reconstruccion = ?,
                parte_reconstruida = ?,
                limpieza_general = ?,
                remite_otra_area = ?,
                area_remite = ?,
                proceso_electronico = ?,
                observaciones_globales = ?,
                fecha_registro = NOW()
              WHERE inventario_id = ?";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error preparando la actualización: ' . $mysqli->error]);
        exit();
    }
    $stmt->bind_param(
        'issssssssssssssssi',
        $tecnico_diagnostico,
        $limpieza_electronico,
        $observaciones_limpieza_electronico,
        $mantenimiento_crema_disciplinaria,
        $observaciones_mantenimiento_crema,
        $mantenimiento_partes,
        $cambio_piezas,
        $piezas_solicitadas_cambiadas,
        $proceso_reconstruccion,
        $parte_reconstruida,
        $limpieza_general,
        $remite_otra_area,
        $area_remite,
        $proceso_electronico,
        $observaciones_globales,
        $inventario_id
    );
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Mantenimiento actualizado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el mantenimiento: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    // Insertar nuevo registro
    $query = "INSERT INTO bodega_mantenimiento (
                inventario_id, tecnico_diagnostico, limpieza_electronico,
                observaciones_limpieza_electronico, mantenimiento_crema_disciplinaria,
                observaciones_mantenimiento_crema, mantenimiento_partes, cambio_piezas,
                piezas_solicitadas_cambiadas, proceso_reconstruccion, parte_reconstruida,
                limpieza_general, remite_otra_area, area_remite, proceso_electronico,
                observaciones_globales, fecha_registro
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error preparando la inserción: ' . $mysqli->error]);
        exit();
    }
    $stmt->bind_param(
        'isssssssssssssssi',
        $inventario_id,
        $tecnico_diagnostico,
        $limpieza_electronico,
        $observaciones_limpieza_electronico,
        $mantenimiento_crema_disciplinaria,
        $observaciones_mantenimiento_crema,
        $mantenimiento_partes,
        $cambio_piezas,
        $piezas_solicitadas_cambiadas,
        $proceso_reconstruccion,
        $parte_reconstruida,
        $limpieza_general,
        $remite_otra_area,
        $area_remite,
        $proceso_electronico,
        $observaciones_globales
    );
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Mantenimiento registrado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar el mantenimiento: ' . $stmt->error]);
    }
    $stmt->close();
}
$mysqli->close();
