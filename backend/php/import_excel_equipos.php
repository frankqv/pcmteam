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
        // Validar el archivo
        if (!file_exists($tmp_name)) {
            throw new Exception("El archivo temporal no existe");
        }
        $spreadsheet = IOFactory::load($tmp_name);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        
        // Debug: registrar información básica del archivo
        error_log("Importación Excel - Filas encontradas: {$highestRow}");
        
        $results['total_rows'] = $highestRow - 1; // Asumiendo que la primera fila es de encabezados
        
        if ($highestRow < 2) {
            throw new Exception("El archivo no contiene datos para importar (solo encabezados o está vacío)");
        }
        // Mapeo correcto de columnas según la plantilla
        $columnMapping = [
            'A' => 'codigo_g',           // CÓDIGO GENERAL *
            'B' => 'lote',               // LOTE
            'C' => 'ubicacion',          // UBICACIÓN EN SEDE *
            'D' => 'posicion',           // POSICIÓN *
            'E' => 'producto',           // TIPO DE PRODUCTO *
            'F' => 'marca',              // MARCA *
            'G' => 'serial',             // SERIAL *
            'H' => 'modelo',             // MODELO *
            'I' => 'procesador',         // PROCESADOR
            'J' => 'ram',                // MEMORIA RAM *
            'K' => 'disco',              // DISCO
            'L' => 'pulgadas',           // PULGADAS
            'M' => 'observaciones',      // OBSERVACIONES
            'N' => 'grado',              // GRADO *
            'O' => 'disposicion',        // DISPOSICIÓN *
            'P' => 'tactil',             // TÁCTIL *
            'Q' => 'proveedor_id',       // PROVEEDOR ID *
            'R' => 'cantidad'            // CANTIDAD
        ];
        // Iterar sobre cada fila del excel (empezando desde la 2 para saltar encabezados)
        for ($row = 2; $row <= $highestRow; $row++) {
            $codigo_g = trim($worksheet->getCell('A' . $row)->getValue() ?? '');
            
            // Debug: registrar información de cada fila
            error_log("Procesando fila {$row}: código = '{$codigo_g}'");
            // Si el código está vacío, saltar la fila
            if (empty($codigo_g)) {
                error_log("Fila {$row} omitida: código vacío");
                $results['skipped']++;
                $results['details'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'status' => 'skipped', 
                    'message' => 'Código vacío, fila omitida.'
                ];
                continue;
            }
            // 1. Verificar si el equipo ya existe
            $stmt_check = $connect->prepare("SELECT id FROM bodega_inventario WHERE codigo_g = :codigo_g");
            $stmt_check->execute(['codigo_g' => $codigo_g]);
            if ($stmt_check->fetch()) {
                $results['skipped']++;
                $results['details'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'status' => 'skipped', 
                    'message' => 'El código ya existe.'
                ];
                continue;
            }
            // 2. Obtener datos de las celdas con mapeo correcto
            $data = [];
            foreach ($columnMapping as $col => $field) {
                $cellValue = trim($worksheet->getCell($col . $row)->getValue() ?? '');
                $data[$field] = $cellValue;
            }
            
            // Debug: registrar datos extraídos
            error_log("Fila {$row} datos: " . json_encode($data));
            // Validar campos obligatorios
            $requiredFields = ['codigo_g', 'ubicacion', 'posicion', 'producto', 'marca', 'serial', 'modelo', 'ram', 'grado', 'disposicion', 'tactil', 'proveedor_id'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $missingFields[] = $field;
                }
            }
            if (!empty($missingFields)) {
                error_log("Fila {$row} error: campos faltantes - " . implode(', ', $missingFields));
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => 'Campos obligatorios faltantes: ' . implode(', ', $missingFields)
                ];
                continue;
            }
            // Verificar si el serial ya existe (debe ser único)
            $stmt_check_serial = $connect->prepare("SELECT id FROM bodega_inventario WHERE serial = :serial");
            $stmt_check_serial->execute(['serial' => $data['serial']]);
            if ($stmt_check_serial->fetch()) {
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => "El serial '{$data['serial']}' ya existe en la base de datos."
                ];
                continue;
            }
            // 3. Verificar que el proveedor existe
            $stmt_prov = $connect->prepare("SELECT id FROM proveedores WHERE id = :id");
            $stmt_prov->execute(['id' => $data['proveedor_id']]);
            $proveedor_exists = $stmt_prov->fetch();
            if (!$proveedor_exists) {
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => "Proveedor con ID '{$data['proveedor_id']}' no encontrado."
                ];
                continue;
            }
            // 4. Validar valores de campos específicos
            $validUbicaciones = ['Principal', 'Unilago', 'Cúcuta', 'Medellín'];
            if (!in_array($data['ubicacion'], $validUbicaciones)) {
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => "Ubicación '{$data['ubicacion']}' no válida. Valores permitidos: " . implode(', ', $validUbicaciones)
                ];
                continue;
            }
            $validProductos = ['Portatil', 'Desktop', 'Monitor', 'AIO', 'Tablet', 'Celular', 'Impresora', 'Periferico', 'otro'];
            if (!in_array($data['producto'], $validProductos)) {
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => "Tipo de producto '{$data['producto']}' no válido."
                ];
                continue;
            }
            $validMarcas = ['HP', 'Dell', 'Lenovo', 'Acer', 'CompuMax', 'Otro'];
            if (!in_array($data['marca'], $validMarcas)) {
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => "Marca '{$data['marca']}' no válida."
                ];
                continue;
            }
            $validGrados = ['A', 'B', 'C', 'SCRAP', '#N/D'];
            if (!in_array($data['grado'], $validGrados)) {
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => "Grado '{$data['grado']}' no válido."
                ];
                continue;
            }
            $validDisposiciones = ['En revisión', 'Por Alistamiento', 'En Laboratorio', 'En Bodega', 'Disposicion final', 'Para Venta'];
            if (!in_array($data['disposicion'], $validDisposiciones)) {
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => "Disposición '{$data['disposicion']}' no válida."
                ];
                continue;
            }
            $validTactil = ['SI', 'NO'];
            if (!in_array($data['tactil'], $validTactil)) {
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => "Valor táctil '{$data['tactil']}' no válido. Use SI o NO."
                ];
                continue;
            }
            try {
                // 5. Insertar en bodega_inventario
                $sql_inventario = "INSERT INTO bodega_inventario (
                    codigo_g, producto, marca, modelo, serial, procesador, ram, disco, pulgadas, 
                    tactil, grado, disposicion, observaciones, ubicacion, posicion, lote, estado
                ) VALUES (
                    :codigo_g, :producto, :marca, :modelo, :serial, :procesador, :ram, :disco, :pulgadas,
                    :tactil, :grado, :disposicion, :observaciones, :ubicacion, :posicion, :lote, 'activo'
                )";
                
                $stmt_inventario = $connect->prepare($sql_inventario);
                $stmt_inventario->execute([
                    ':codigo_g' => $data['codigo_g'],
                    ':producto' => $data['producto'],
                    ':marca' => $data['marca'],
                    ':modelo' => $data['modelo'],
                    ':serial' => $data['serial'],
                    ':procesador' => $data['procesador'],
                    ':ram' => $data['ram'],
                    ':disco' => $data['disco'],
                    ':pulgadas' => $data['pulgadas'],
                    ':tactil' => $data['tactil'],
                    ':grado' => $data['grado'],
                    ':disposicion' => $data['disposicion'],
                    ':observaciones' => $data['observaciones'],
                    ':ubicacion' => $data['ubicacion'],
                    ':posicion' => $data['posicion'],
                    ':lote' => $data['lote']
                ]);
                
                $inventario_id = $connect->lastInsertId();
                // 6. Insertar en bodega_entradas
                $cantidad = !empty($data['cantidad']) ? intval($data['cantidad']) : 1;
                $sql_entrada = "INSERT INTO bodega_entradas (
                    inventario_id, proveedor_id, usuario_id, cantidad, observaciones
                ) VALUES (
                    :inventario_id, :proveedor_id, :usuario_id, :cantidad, :observaciones
                )";
                
                $stmt_entrada = $connect->prepare($sql_entrada);
                $stmt_entrada->execute([
                    ':inventario_id' => $inventario_id,
                    ':proveedor_id' => $data['proveedor_id'],
                    ':usuario_id' => $usuario_id,
                    ':cantidad' => $cantidad,
                    ':observaciones' => "Importado desde Excel - Fila {$row}"
                ]);
                $results['success']++;
                $results['details'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'status' => 'success', 
                    'message' => 'Importado correctamente.'
                ];
            } catch (PDOException $e) {
                $results['errors'][] = [
                    'row' => $row, 
                    'codigo' => $codigo_g, 
                    'error' => 'Error de base de datos: ' . $e->getMessage()
                ];
                continue;
            }
        }
        $connect->commit();
        
        if ($results['success'] > 0) {
            $response['success'] = true;
            $response['message'] = "Importación completada. {$results['success']} equipos importados, {$results['skipped']} omitidos" . 
                (count($results['errors']) > 0 ? ", " . count($results['errors']) . " errores" : "") . ".";
        } else {
            $response['success'] = false;
            $response['error'] = "No se importó ningún equipo. Revise los errores reportados.";
        }
        
        $response['results'] = $results;
    } catch (Exception $e) {
        if ($connect->inTransaction()) {
            $connect->rollBack();
        }
        $response['error'] = "Error durante la importación: " . $e->getMessage();
        $response['results'] = $results;
        
        // Log del error para debugging
        error_log("Error en importación Excel: " . $e->getMessage() . " - Archivo: " . $e->getFile() . " - Línea: " . $e->getLine());
    }
} else {
    $file_error = $_FILES['excel_file']['error'] ?? 'No se recibió archivo';
    $response['error'] = 'No se recibió ningún archivo o hubo un error en la subida. Error: ' . $file_error;
}
echo json_encode($response);
exit();
?>