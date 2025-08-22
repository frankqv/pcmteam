<!-- frontend/compra/limpiar_carrito.php -->
<?php
ob_start();
session_start();

// Verificar que el usuario esté autenticado y tenga permisos
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7])){
    header('location: ../error404.php');
    exit();
}

// Verificar que el usuario tenga una sesión válida
if(!isset($_SESSION['id'])) {
    header('Location: ../error404.php');
    exit();
}

// Incluir conexión a la base de datos
require_once('../../config/ctconex.php');

try {
    // Preparar consulta para eliminar todos los productos del carrito del usuario actual
    $delete_all_cart = $connect->prepare("DELETE FROM cart_compra WHERE user_id = ?");
    $result = $delete_all_cart->execute([$_SESSION['id']]);
    
    if($result) {
        // Verificar cuántos registros se eliminaron
        $deleted_count = $delete_all_cart->rowCount();
        
        if($deleted_count > 0) {
            $_SESSION['mensaje'] = "Se eliminaron $deleted_count productos del carrito exitosamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "El carrito ya estaba vacío.";
            $_SESSION['tipo_mensaje'] = "info";
        }
    } else {
        $_SESSION['mensaje'] = "Error al limpiar el carrito.";
        $_SESSION['tipo_mensaje'] = "error";
    }
    
} catch(PDOException $e) {
    // Error en la base de datos
    $_SESSION['mensaje'] = "Error de base de datos: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "error";
}

// Redireccionar de vuelta a la página de compras
header('Location: nuevo.php');
exit();

ob_end_flush();
?>