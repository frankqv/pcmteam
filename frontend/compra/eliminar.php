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

// Verificar que se haya enviado un ID
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id_carrito = $_GET['id'];
    
    try {
        // Preparar consulta para eliminar el producto del carrito
        $delete_cart_item = $connect->prepare("DELETE FROM cart_compra WHERE idcarco = ? AND user_id = ?");
        $result = $delete_cart_item->execute([$id_carrito, $_SESSION['id']]);
        
        if($result) {
            // Éxito al eliminar
            $_SESSION['mensaje'] = "Producto eliminado del carrito exitosamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            // Error al eliminar
            $_SESSION['mensaje'] = "Error al eliminar el producto del carrito.";
            $_SESSION['tipo_mensaje'] = "error";
        }
        
    } catch(PDOException $e) {
        // Error en la base de datos
        $_SESSION['mensaje'] = "Error de base de datos: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "error";
    }
    
} else {
    // No se proporcionó ID
    $_SESSION['mensaje'] = "ID de producto no válido.";
    $_SESSION['tipo_mensaje'] = "error";
}

// Redireccionar de vuelta a la página de compras
header('Location: nuevo.php');
exit();

ob_end_flush();
?>