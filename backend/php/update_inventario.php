<?php
session_start();
require_once '../bd/ctconex.php';

header('Content-Type: application/json');

// Validar autenticación
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit;
}

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Validar campos requeridos
$required = [
    'id', 'codigo_g', 'ubicacion', 'posicion', 'producto', 'marca', 'serial', 'modelo',
    'ram', 'grado', 'disposicion', 'estado'
];

foreach ($required as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] === '') {
        http_response_code(400);
        echo json_encode(['error' => "Falta el campo requerido: $field"]);
        exit;
    }
}

try {
    // Actualizar el equipo en bodega_inventario
    $sql = "UPDATE bodega_inventario SET 
            codigo_g = :codigo_g,
            ubicacion = :ubicacion,
            posicion = :posicion,
            producto = :producto,
            marca = :marca,
            serial = :serial,
            modelo = :modelo,
            procesador = :procesador,
            ram = :ram,
            disco = :disco,
            pulgadas = :pulgadas,
            observaciones = :observaciones,
            grado = :grado,
            disposicion = :disposicion,
            estado = :estado,
            fecha_modificacion = NOW()
            WHERE id = :id";
    
    $stmt = $connect->prepare($sql);
    $stmt->execute([
        ':id' => $_POST['id'],
        ':codigo_g' => $_POST['codigo_g'],
        ':ubicacion' => $_POST['ubicacion'],
        ':posicion' => $_POST['posicion'],
        ':producto' => $_POST['producto'],
        ':marca' => $_POST['marca'],
        ':serial' => $_POST['serial'],
        ':modelo' => $_POST['modelo'],
        ':procesador' => $_POST['procesador'] ?? '',
        ':ram' => $_POST['ram'],
        ':disco' => $_POST['disco'] ?? '',
        ':pulgadas' => $_POST['pulgadas'] ?? '',
        ':observaciones' => $_POST['observaciones'] ?? '',
        ':grado' => $_POST['grado'],
        ':disposicion' => $_POST['disposicion'],
        ':estado' => $_POST['estado']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Equipo actualizado exitosamente'
        ]);
    } else {
        echo json_encode([
            'error' => 'No se realizaron cambios en el equipo'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al actualizar el equipo en la base de datos', 
        'detalle' => $e->getMessage()
    ]);
    error_log("Error en update_inventario.php: " . $e->getMessage());
}
?> 