<?php
// Alias de edición de equipo que reutiliza la pantalla de edición existente
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 5, 6, 7])) {
    header('location: ../error404.php');
    exit();
}

// Mantener el id si viene por querystring
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$target = 'editar_inventario.php' . ($id > 0 ? ('?id=' . $id) : '');
header('Location: ' . $target);
exit();
?>


