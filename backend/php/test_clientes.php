<?php
/**
 * ARCHIVO DE PRUEBA - Verificar búsqueda de clientes
 */

session_start();
header('Content-Type: application/json');
require_once '../../config/ctconex.php';

try {
    echo "<!-- Conexión establecida -->\n";

    // Probar query simple
    $sql = "SELECT idclie, numid, nomcli, apecli, celu FROM clientes LIMIT 5";
    $stmt = $connect->query($sql);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'total_clientes' => count($clientes),
        'clientes' => $clientes,
        'mensaje' => 'Query ejecutada correctamente'
    ], JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
