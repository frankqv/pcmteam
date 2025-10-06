<?php
// Pasarela del frontend a backend/php/procesar_venta_final.php para evitar rutas mal calculadas
// Mantiene POST y redirige al script real
ob_start();
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: nuevo.php');
    exit();
}

require_once '../../config/ctconex.php';

// Incluye directamente el script real para conservar el contexto y $_POST
require_once '../../backend/php/procesar_venta_final.php';
exit();
?>