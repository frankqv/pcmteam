<?php
// backend/php/buscar_equipos_preventa.php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once('../../config/ctconex.php');

try {
    $marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
    $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';

    // Construir query para buscar equipos grado A y B disponibles
    $sql = "SELECT
        id, codigo_general, serial, marca, modelo, procesador, ram, disco, pulgada, grado, precio
    FROM bodega_inventario
    WHERE disposicion = 'Para Venta'
    AND grado IN ('A', 'B')";

    $params = [];

    // Agregar filtros opcionales
    if (!empty($marca)) {
        $sql .= " AND marca LIKE :marca";
        $params[':marca'] = '%' . $marca . '%';
    }

    if (!empty($modelo)) {
        $sql .= " AND modelo LIKE :modelo";
        $params[':modelo'] = '%' . $modelo . '%';
    }

    $sql .= " ORDER BY grado ASC, fecha_entrada DESC LIMIT 20";

    $stmt = $connect->prepare($sql);
    $stmt->execute($params);
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'equipos' => $equipos,
        'total' => count($equipos)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Error en la búsqueda: ' . $e->getMessage()
    ]);
}
?>
