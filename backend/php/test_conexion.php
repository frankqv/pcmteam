<?php
session_start();
require_once '../bd/ctconex.php';

header('Content-Type: application/json');

// Simular una respuesta exitosa
echo json_encode([
    'success' => true,
    'message' => 'Conexión exitosa - Backend funcionando correctamente',
    'timestamp' => date('Y-m-d H:i:s'),
    'database' => 'Conectado a: ' . dbname
]);
?>
