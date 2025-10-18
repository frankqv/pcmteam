<?php
require_once __DIR__ . '../../../config/ctconex.php';
// /backend/php/test_connection.php
session_start();
header('Content-Type: application/json');
try {
    // Probar la conexión
    require_once '../../config/ctconex.php';
    // Verificar que la conexión esté activa
    if ($connect) {
        // Probar una consulta simple
        $stmt = $connect->query("SELECT 1 as test");
        if ($stmt) {
            echo json_encode([
                'success' => true,
                'message' => 'Conexión exit<osa a la base de datos',
                'database_info' => [
                    'server_info' => $connect->getAttribute(PDO::ATTR_SERVER_VERSION),
                    'connection_status' => $connect->getAttribute(PDO::ATTR_CONNECTION_STATUS)
                ]
            ]);
        } else {
            throw new Exception('No se pudo ejecutar consulta de prueba');
        }
    } else {
        throw new Exception('No se pudo establecer conexión');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error de conexión: ' . $e->getMessage(),
        'file_path' => __FILE__,
        'current_dir' => getcwd()
    ]);
}
