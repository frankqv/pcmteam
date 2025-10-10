<?php
/* controllers/update_price.php */
session_start();
require_once '../../config/ctconex.php';
// Verificar autenticación
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el ID del inventario
    $inventario_id = isset($_POST['inventario_id']) ? intval($_POST['inventario_id']) : 0;
    if ($inventario_id <= 0) {
        header('Location: ../b_room/mostrar.php?error=id_invalido');
        exit();
    }
    // Obtener el precio (puede venir como precio_clean o precio)
    $precio = '';
    if (isset($_POST['precio_clean']) && $_POST['precio_clean'] !== '') {
        $precio = preg_replace('/[^0-9]/', '', $_POST['precio_clean']);
    } elseif (isset($_POST['precio']) && $_POST['precio'] !== '') {
        $precio = preg_replace('/[^0-9]/', '', $_POST['precio']);
    }
    // Validar que el precio no esté vacío
    if (empty($precio)) {
        header('Location: ../b_room/mostrar.php?error=precio_vacio');
        exit();
    }
    // Convertir a decimal
    $precio = floatval($precio);
    // Procesar la foto si se subió
    $foto_nombre = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_original = $_FILES['foto']['name'];
        $foto_ext = strtolower(pathinfo($foto_original, PATHINFO_EXTENSION));
        // Validar extensión
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($foto_ext, $extensiones_permitidas)) {
            header('Location: ../b_room/mostrar.php?error=extension_invalida');
            exit();
        }
        // Generar nombre único
        $foto_nombre = 'inv_' . $inventario_id . '_' . time() . '.' . $foto_ext;
        $foto_ruta = '../../uploads/inventario/' . $foto_nombre;
        // Crear directorio si no existe
        if (!is_dir('../../uploads/inventario')) {
            mkdir('../../uploads/inventario', 0777, true);
        }
        // Mover archivo
        if (!move_uploaded_file($foto_tmp, $foto_ruta)) {
            header('Location: ../b_room/mostrar.php?error=error_subida');
            exit();
        }
    }
    // Iniciar transacción
    $conn->begin_transaction();
    try {
        // Actualizar precio
        if ($foto_nombre) {
            // Actualizar precio y foto
            $sql = "UPDATE bodega_inventario SET precio = ?, foto = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("dsi", $precio, $foto_nombre, $inventario_id);
        } else {
            // Actualizar solo precio
            $sql = "UPDATE bodega_inventario SET precio = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("di", $precio, $inventario_id);
        }
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar el precio");
        }
        // Registrar el cambio en el log
        $sql_log = "INSERT INTO bodega_log_cambios 
                    (inventario_id, usuario_id, campo_modificado, valor_nuevo, tipo_cambio) 
                    VALUES (?, ?, 'precio', ?, 'edicion_manual')";
        $stmt_log = $conn->prepare($sql_log);
        $stmt_log->bind_param("iis", $inventario_id, $_SESSION['id'], $precio);
        $stmt_log->execute();
        // Si se subió foto, registrar también
        if ($foto_nombre) {
            $sql_log_foto = "INSERT INTO bodega_log_cambios 
                            (inventario_id, usuario_id, campo_modificado, valor_nuevo, tipo_cambio) 
                            VALUES (?, ?, 'foto', ?, 'edicion_manual')";
            $stmt_log_foto = $conn->prepare($sql_log_foto);
            $stmt_log_foto->bind_param("iis", $inventario_id, $_SESSION['id'], $foto_nombre);
            $stmt_log_foto->execute();
        }
        // Confirmar transacción
        $conn->commit();
        // Redirigir con éxito
        header('Location: ../b_room/mostrar.php?success=precio_actualizado');
        exit();
    } catch (Exception $e) {
        // Revertir transacción
        $conn->rollback();
        // Eliminar foto si se subió
        if ($foto_nombre && file_exists($foto_ruta)) {
            unlink($foto_ruta);
        }
        header('Location: ../b_room/mostrar.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: ../b_room/mostrar.php?error=metodo_invalido');
    exit();
}
