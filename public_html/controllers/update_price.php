<?php
// controllers/update_price.php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 4, 5, 6, 7])) {
    header('location: ../error404.php');
    exit();
}
require_once '../../config/ctconex.php';

$inventarioId = isset($_POST['inventario_id']) ? (int)$_POST['inventario_id'] : 0;
$precio = isset($_POST['precio']) ? trim($_POST['precio']) : '';
if ($inventarioId <= 0 || $precio === '') {
    header('Location: ../b_room/mostrar.php');
    exit();
}

$fotoNombre = null;
if (!empty($_FILES['foto']['name']) && is_uploaded_file($_FILES['foto']['tmp_name'])) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if (in_array($ext, $allowed)) {
        $safeName = 'inv_' . $inventarioId . '_' . time() . '.' . $ext;
        $destDir = realpath(__DIR__ . '/../assets/img');
        if ($destDir && move_uploaded_file($_FILES['foto']['tmp_name'], $destDir . DIRECTORY_SEPARATOR . $safeName)) {
            $fotoNombre = $safeName;
        }
    }
}

// Actualizar
try {
    if ($fotoNombre) {
        $stmt = $connect->prepare("UPDATE bodega_inventario SET precio = ?, foto = ?, fecha_modificacion = NOW() WHERE id = ?");
        $stmt->execute([$precio, $fotoNombre, $inventarioId]);
    } else {
        $stmt = $connect->prepare("UPDATE bodega_inventario SET precio = ?, fecha_modificacion = NOW() WHERE id = ?");
        $stmt->execute([$precio, $inventarioId]);
    }
} catch (Exception $e) {
    // fallback
}

header('Location: ../b_room/mostrar.php');
exit;
?>


