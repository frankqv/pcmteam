<?php
// Cargar el autoloader de Composer para usar PhpSpreadsheet
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/ctconex.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Iniciar la sesión solo si no hay una activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurar la cabecera para devolver JSON
header('Content-Type: application/json');

// Preparar la respuesta por defecto
$response = [
    'success' => false,
    'error' => 'Petición inválida.',
    'results' => null
];

// Validar rol y sesión
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    $response['error'] = 'Acceso no autorizado.';
    echo json_encode($response);
    exit();
}

if (!isset($_SESSION['id'])) {
    $response['error'] = 'ID de usuario no encontrado en la sesión.';
    echo json_encode($response);
    exit();
}

$usuario_id = $_SESSION['id'];

// Validar que se haya subido un archivo
if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == UPLOAD_ERR_OK) {
    
    $tmp_name = $_FILES['excel_file']['tmp_name'];
    $results = [
        'total_rows' => 0,
        'success' => 0,
        'skipped' => 0,
        'errors' => [],
        'details' => []
    ];

    try {
        $connect->beginTransaction();

        $spreadsheet = IOFactory::load($tmp_name);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        
        $results['total_rows'] = $highestRow - 1; // Asumiendo que la primera fila es de encabezados

        // Iterar sobre cada fila del excel (empezando desde la 2 para saltar encabezados)
        for ($row = 2; $row <= $highestRow; $row++) {
            $codigo_g = trim($worksheet->getCell('A' . $row)->getValue() ?? '');

            // Si el código está vacío, saltar la fila
            if (empty($codigo_g)) {
                continue;
            }

            // 1. Verificar si el equipo ya existe
            $stmt_check = $connect->prepare("SELECT id FROM bodega_inventario WHERE codigo_g = :codigo_g");
            $stmt_check->execute(['codigo_g' => $codigo_g]);

            if ($stmt_check->fetch()) {
                $results['skipped']++;
                $results['details'][] = ['row' => $row, 'codigo' => $codigo_g, 'status' => 'skipped', 'message' => 'El código ya existe.'];
                continue;
            }

            // 2. Obtener datos de las celdas
            $proveedor_nombre = trim($worksheet->getCell('B' . $row)->getValue() ?? '');
            $lote = trim($worksheet->getCell('C' . $row)->getValue() ?? '');
            $ubse = trim($worksheet->getCell('D' . $row)->getValue() ?? '');
            $posicion = trim($worksheet->getCell('E' . $row)->getValue() ?? '');
            $producto = trim($worksheet->getCell('F' . $row)->getValue() ?? '');
            $marca = trim($worksheet->getCell('G' . $row)->getValue() ?? '');
            $modelo = trim($worksheet->getCell('H' . $row)->getValue() ?? '');
            $serial = trim($worksheet->getCell('I' . $row)->getValue() ?? '');
            $procesador = trim($worksheet->getCell('J' . $row)->getValue() ?? '');
            $ram = trim($worksheet->getCell('K' . $row)->getValue() ?? '');
            $disco = trim($worksheet->getCell('L' . $row)->getValue() ?? '');
            $pulgadas = trim($worksheet->getCell('M' . $row)->getValue() ?? '');
            $tactil = trim($worksheet->getCell('N' . $row)->getValue() ?? '');
            $grado = trim($worksheet->getCell('O' . $row)->getValue() ?? '');
            $disposicion = trim($worksheet->getCell('P' . $row)->getValue() ?? '');
            $observaciones = trim($worksheet->getCell('Q' . $row)->getValue() ?? '');

            // 3. Buscar ID del proveedor
            $stmt_prov = $connect->prepare("SELECT id FROM proveedores WHERE nombre = :nombre OR nomenclatura = :nombre LIMIT 1");
            $stmt_prov->execute(['nombre' => $proveedor_nombre]);
            $proveedor_id = $stmt_prov->fetchColumn();

            if (!$proveedor_id) {
                $results['errors'][] = ['row' => $row, 'codigo' => $codigo_g, 'error' => "Proveedor '{$proveedor_nombre}' no encontrado."];
                continue;
            }

            // 4. Insertar en bodega_inventario
            $sql_inventario = "INSERT INTO bodega_inventario (codigo_g, producto, marca, modelo, serial, procesador, ram, disco, pulgadas, tactil, grado, disposicion, observaciones, ubicacion, posicion, lote, estado) VALUES (:codigo_g, :producto, :marca, :modelo, :serial, :procesador, :ram, :disco, :pulgadas, :tactil, :grado, :disposicion, :observaciones, :ubicacion, :posicion, :lote, 'activo')";
            $stmt_inventario = $connect->prepare($sql_inventario);
            $stmt_inventario->execute([
                ':codigo_g' => $codigo_g,
                ':producto' => $producto,
                ':marca' => $marca,
                ':modelo' => $modelo,
                ':serial' => $serial,
                ':procesador' => $procesador,
                ':ram' => $ram,
                ':disco' => $disco,
                ':pulgadas' => $pulgadas,
                ':tactil' => $tactil,
                ':grado' => $grado,
                ':disposicion' => $disposicion,
                ':observaciones' => $observaciones,
                ':ubicacion' => $ubse,
                ':posicion' => $posicion,
                ':lote' => $lote
            ]);
            $inventario_id = $connect->lastInsertId();

            // 5. Insertar en bodega_entradas
            $sql_entrada = "INSERT INTO bodega_entradas (inventario_id, proveedor_id, usuario_id, cantidad, observaciones) VALUES (:inventario_id, :proveedor_id, :usuario_id, 1, :observaciones)";
            $stmt_entrada = $connect->prepare($sql_entrada);
            $stmt_entrada->execute([
                ':inventario_id' => $inventario_id,
                ':proveedor_id' => $proveedor_id,
                ':usuario_id' => $usuario_id,
                ':observaciones' => "Importado desde Excel."
            ]);

            $results['success']++;
            $results['details'][] = ['row' => $row, 'codigo' => $codigo_g, 'status' => 'success', 'message' => 'Importado correctamente.'];

        }

        $connect->commit();
        $response['success'] = true;
        $response['message'] = "Importación completada. " . $results['success'] . " equipos importados, " . $results['skipped'] . " omitidos.";
        $response['results'] = $results;

    } catch (Exception $e) {
        if ($connect->inTransaction()) {
            $connect->rollBack();
        }
        $response['error'] = "Error durante la importación: " . $e->getMessage();
        $response['results'] = $results;
    }

} else {
    $response['error'] = 'No se recibió ningún archivo o hubo un error en la subida.';
}

echo json_encode($response);
exit();
?>
