<?php
// Alias de creación de equipos que reutiliza el formulario de entradas
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    header('location: ../error404.php');
    exit();
}
// Redirige a la pantalla de registro de entradas (nuevo equipo)
header('Location: entradas.php');
exit();
?>


