<?php
require_once '../../config/ctconex.php';
if (isset($_POST['pedido_id'], $_POST['equipo_id'])) {
    $pedido_id = intval($_POST['pedido_id']);
    $equipo_id = intval($_POST['equipo_id']);
    $stmt = $conn->prepare("UPDATE bodega_inventario SET pedido_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $pedido_id, $equipo_id);
    $stmt->execute();
}
header('Location: mostrar.php');
exit;
?>
