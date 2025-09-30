<?php
// ===================================================================
// IMPORTATE para el archivo /pcmteam/public_html/bodega/inventario.php
// CONTROLADOR: update_ubicacion.php
// Actualiza la ubicación de un equipo con registro de cambios
// ===================================================================
session_start();
require_once '../../config/ctconex.php';
// Verificar autenticación
if (!isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    header('location: ../bodega/inventario_rol_sede.php?error=no_auth');
    exit;
}
// Verificar permisos (Admin, Default, Bodega)
if (!in_array($_SESSION['rol'], [1, 2, 7])) {
    header('location: ../bodega/inventario_rol_sede.php?error=no_permisos');
    exit;
}
// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipo_id'], $_POST['ubicacion'])) {
    $equipoId = intval($_POST['equipo_id']);
    $nuevaUbicacion = trim($_POST['ubicacion']);
    $usuarioId = $_SESSION['id'];
    $usuarioNombre = $_SESSION['nombre'] ?? 'Usuario';
    // Validar ubicación
    $ubicacionesValidas = ['Medellin', 'Cucuta', 'Unilago', 'Principal'];
    if (!in_array($nuevaUbicacion, $ubicacionesValidas)) {
        header('location: ../bodega/inventario_rol_sede.php?error=ubicacion_invalida');
        exit;
    }
    // Obtener ubicación actual
    $stmt = $conn->prepare("SELECT ubicacion, codigo_g FROM bodega_inventario WHERE id = ?");
    $stmt->bind_param("i", $equipoId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        header('location: ../bodega/inventario_rol_sede.php?error=equipo_no_encontrado');
        exit;
    }
    $equipo = $result->fetch_assoc();
    $ubicacionAnterior = $equipo['ubicacion'];
    $codigoEquipo = $equipo['codigo_g'];
    $stmt->close();
    // Solo actualizar si es diferente
    if ($ubicacionAnterior !== $nuevaUbicacion) {
        // Iniciar transacción
        $conn->begin_transaction();
        try {
            // Actualizar ubicación
            $updateStmt = $conn->prepare("UPDATE bodega_inventario SET ubicacion = ?, fecha_modificacion = NOW() WHERE id = ?");
            $updateStmt->bind_param("si", $nuevaUbicacion, $equipoId);
            if (!$updateStmt->execute()) {
                throw new Exception('Error al actualizar ubicación');
            }
            $updateStmt->close();
            // Registrar en log de cambios
            $logStmt = $conn->prepare("INSERT INTO bodega_log_cambios 
                (inventario_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo, tipo_cambio, fecha_cambio) 
                VALUES (?, ?, 'Ubicación', ?, ?, 'edicion_manual', NOW())");
            $logStmt->bind_param("iiss", $equipoId, $usuarioId, $ubicacionAnterior, $nuevaUbicacion);
            $logStmt->execute();
            $logStmt->close();
            // Agregar nota a observaciones
            $nota = "\n[" . date('Y-m-d H:i:s') . "] Ubicación cambiada de '{$ubicacionAnterior}' a '{$nuevaUbicacion}' por {$usuarioNombre}";
            $obsStmt = $conn->prepare("UPDATE bodega_inventario SET observaciones = CONCAT(IFNULL(observaciones, ''), ?) WHERE id = ?");
            $obsStmt->bind_param("si", $nota, $equipoId);
            $obsStmt->execute();
            $obsStmt->close();
            // Commit
            $conn->commit();
            header('location: ../bodega/inventario_rol_sede.php?success=ubicacion_actualizada');
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            header('location: ../bodega/inventario_rol_sede.php?error=update_failed');
            exit;
        }
    } else {
        // No hay cambio
        header('location: ../bodega/inventario_rol_sede.php');
        exit;
    }
} else {
    header('location: ../bodega/inventario_rol_sede.php?error=datos_invalidos');
    exit;
}
$conn->close();
