<!-- bodega/asignar_tecnico.php -->

<?php
require_once '../../config/ctconex.php';

if (isset($_POST['equipo_id'], $_POST['tecnico_id'])) {
    $equipo_id = intval($_POST['equipo_id']);
    $tecnico_id = intval($_POST['tecnico_id']);
    $stmt = $conn->prepare("UPDATE bodega_inventario SET tecnico_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $tecnico_id, $equipo_id);
    $stmt->execute();
}
header('Location: inventario.php');
exit;
?>