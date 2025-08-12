<?php
// /backend/php/import_excel_equipos.php
session_start();
header('Content-Type: application/json');

// Verificar sesión y permisos
if (!isset($_SESSION['id']) || !isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
    exit();
}

require_once '../bd/ctconex.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Verificar que se haya subido un archivo
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se ha subido ningún archivo válido');
    }

    $file = $_FILES['excel_file'];
    $observations = isset($_POST['import_observations']) ? trim($_POST['import_observations']) : '';
    $usuario_id = $_SESSION['id'];

    // Validar tipo de archivo
    $allowedExtensions = ['xlsx', 'xls'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Tipo de archivo no válido. Solo se permiten archivos .xlsx y .xls');
    }

    // Validar tamaño de archivo (10MB máximo)
    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        throw new Exception('El archivo es demasiado grande. Máximo 10MB permitido');
    }

    // Cargar el archivo Excel
    $spreadsheet = IOFactory::load($file['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Verificar que el archivo no esté vacío
    if (count($rows) < 2) {
        throw new Exception('El archivo Excel debe contener al menos una fila de datos además de los encabezados');
    }

    // Validar encabezados esperados
    $expectedHeaders = [
        'CÓDIGO GENERAL *',
        'LOTE',
        'UBICACIÓN EN SEDE *',
        'POSICIÓN *',
        'TIPO DE PRODUCTO *',
        'MARCA *',
        'SERIAL *',
        'MODELO *',
        'PROCESADOR',
        'MEMORIA RAM *',
        'DISCO',
        'PULGADAS',
        'OBSERVACIONES',
        'GRADO *',
        'DISPOSICIÓN *',
        'TÁCTIL *',
        'PROVEEDOR ID *',
        'CANTIDAD'
    ];

    $headers = array_map('trim', $rows[0]);
    $headerMismatch = array_diff($expectedHeaders, $headers);
    
    if (!empty($headerMismatch)) {
        throw new Exception('Los encabezados del archivo no coinciden con el formato esperado. Descargue la plantilla oficial.');
    }

    // Inicializar contadores y arrays de resultados
    $totalRows = count($rows) - 1; // Excluir encabezados
    $successCount = 0;
    $skipCount = 0;
    $errors = [];
    $details = [];

    // Mapeo de columnas (basado en los encabezados esperados)
    $columnMap = [
        'codigo_g' => 0,        // A - CÓDIGO GENERAL *
        'lote' => 1,           // B - LOTE
        'ubicacion' => 2,      // C - UBICACIÓN EN SEDE *
        'posicion' => 3,       // D - POSICIÓN *
        'producto' => 4,       // E - TIPO DE PRODUCTO *
        'marca' => 5,          // F - MARCA *
        'serial' => 6,         // G - SERIAL *
        'modelo' => 7,         // H - MODELO *
        'procesador' => 8,     // I - PROCESADOR
        'ram' => 9,           // J - MEMORIA RAM *
        'disco' => 10,        // K - DISCO
        'pulgadas' => 11,     // L - PULGADAS
        'observaciones' => 12, // M - OBSERVACIONES
        'grado' => 13,        // N - GRADO *
        'disposicion' => 14,  // O - DISPOSICIÓN *
        'tactil' => 15,       // P - TÁCTIL *
        'proveedor' => 16,    // Q - PROVEEDOR ID *
        'cantidad' => 17      // R - CANTIDAD
    ];

    // Valores válidos para validación
    $validValues = [
        'ubicacion' => ['Principal', 'Unilago', 'Cúcuta', 'Medellín'],
        'producto' => ['Portatil', 'Desktop', 'Monitor', 'AIO', 'Tablet', 'Celular', 'Impresora', 'Periferico', 'otro'],
        'marca' => ['HP', 'Dell', 'Lenovo', 'Acer', 'CompuMax', 'Otro'],
        'ram' => ['4GB', '8GB', '16GB', '32GB', 'otro'],
        'grado' => ['A', 'B', 'C', 'SCRAP', '#N/D'],
        'disposicion' => ['En revisión', 'Por Alistamiento', 'En Laboratorio', 'En Bodega', 'Disposicion final', 'Para Venta'],
        'tactil' => ['SI', 'NO']
    ];

    // Obtener proveedores válidos
    $validProviders = [];
    $stmt = $connect->prepare("SELECT id FROM proveedores");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $validProviders[] = (string)$row['id'];
    }

    // Procesar cada fila de datos
    for ($i = 1; $i < count($rows); $i++) {
        $rowNumber = $i + 1; // +1 porque Excel empieza en 1 y ya saltamos encabezados
        $row = array_map('trim', $rows[$i]);
        
        try {
            // Verificar que la fila no esté completamente vacía
            if (empty(array_filter($row))) {
                $skipCount++;
                $details[] = [
                    'row' => $rowNumber,
                    'codigo' => 'N/A',
                    'status' => 'skipped',
                    'message' => 'Fila vacía'
                ];
                continue;
            }

            // Extraer datos de la fila
            $data = [
                'codigo_g' => $row[$columnMap['codigo_g']] ?? '',
                'lote' => $row[$columnMap['lote']] ?? null,
                'ubicacion' => $row[$columnMap['ubicacion']] ?? '',
                'posicion' => $row[$columnMap['posicion']] ?? '',
                'producto' => $row[$columnMap['producto']] ?? '',
                'marca' => $row[$columnMap['marca']] ?? '',
                'serial' => $row[$columnMap['serial']] ?? '',
                'modelo' => $row[$columnMap['modelo']] ?? '',
                'procesador' => $row[$columnMap['procesador']] ?? null,
                'ram' => $row[$columnMap['ram']] ?? '',
                'disco' => $row[$columnMap['disco']] ?? null,
                'pulgadas' => $row[$columnMap['pulgadas']] ?? null,
                'observaciones' => $row[$columnMap['observaciones']] ?? null,
                'grado' => $row[$columnMap['grado']] ?? '',
                'disposicion' => $row[$columnMap['disposicion']] ?? '',
                'tactil' => $row[$columnMap['tactil']] ?? '',
                'proveedor' => $row[$columnMap['proveedor']] ?? '',
                'cantidad' => $row[$columnMap['cantidad']] ?? '1'
            ];

            // Validar campos requeridos
            $requiredFields = ['codigo_g', 'ubicacion', 'posicion', 'producto', 'marca', 'serial', 'modelo', 'ram', 'grado', 'disposicion', 'tactil', 'proveedor'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Campo requerido '$field' está vacío");
                }
            }

            // Validaciones específicas
            if (strpos($data['codigo_g'], ' ') !== false) {
                throw new Exception('El código general no puede contener espacios');
            }

            if (strlen($data['codigo_g']) < 3) {
                throw new Exception('El código general debe tener al menos 3 caracteres');
            }

            // Validar valores contra listas predefinidas
            foreach ($validValues as $field => $values) {
                if (isset($data[$field]) && !empty($data[$field]) && !in_array($data[$field], $values)) {
                    throw new Exception("Valor inválido para '$field': '{$data[$field]}'. Valores válidos: " . implode(', ', $values));
                }
            }

            // Validar proveedor
            if (!in_array($data['proveedor'], $validProviders)) {
                throw new Exception("ID de proveedor inválido: '{$data['proveedor']}'");
            }

            // Verificar si el código ya existe
            $stmt_check = $connect->prepare("SELECT id FROM bodega_inventario WHERE codigo_g = ?");
            $stmt_check->execute([$data['codigo_g']]);
            if ($stmt_check->rowCount() > 0) {
                $skipCount++;
                $details[] = [
                    'row' => $rowNumber,
                    'codigo' => $data['codigo_g'],
                    'status' => 'skipped',
                    'message' => 'Código ya existe en el sistema'
                ];
                continue;
            }

            // Verificar si el serial ya existe
            $stmt_check_serial = $connect->prepare("SELECT id FROM bodega_inventario WHERE serial = ?");
            $stmt_check_serial->execute([$data['serial']]);
            if ($stmt_check_serial->rowCount() > 0) {
                $skipCount++;
                $details[] = [
                    'row' => $rowNumber,
                    'codigo' => $data['codigo_g'],
                    'status' => 'skipped',
                    'message' => 'Serial ya existe en el sistema'
                ];
                continue;
            }

            // Iniciar transacción para esta fila
            $connect->beginTransaction();

            // Insertar en bodega_inventario
            $sql_inventario = "INSERT INTO bodega_inventario (
                codigo_g, ubicacion, posicion, producto, marca, serial, modelo, 
                procesador, ram, disco, pulgadas, observaciones, grado, disposicion, 
                estado, tactil, lote, fecha_ingreso, fecha_modificacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo', ?, ?, NOW(), NOW())";
            
            $stmt_inventario = $connect->prepare($sql_inventario);
            $stmt_inventario->execute([
                $data['codigo_g'],
                $data['ubicacion'],
                $data['posicion'],
                $data['producto'],
                $data['marca'],
                $data['serial'],
                $data['modelo'],
                $data['procesador'],
                $data['ram'],
                $data['disco'],
                $data['pulgadas'],
                $data['observaciones'] ? $data['observaciones'] . ($observations ? ' | ' . $observations : '') : $observations,
                $data['grado'],
                $data['disposicion'],
                $data['tactil'],
                $data['lote']
            ]);

            // Obtener el ID del inventario insertado
            $inventario_id = $connect->lastInsertId();

            // Insertar en bodega_entradas
            $sql_entrada = "INSERT INTO bodega_entradas (
                inventario_id, proveedor_id, usuario_id, cantidad, observaciones, fecha_entrada
            ) VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt_entrada = $connect->prepare($sql_entrada);
            $stmt_entrada->execute([
                $inventario_id,
                $data['proveedor'],
                $usuario_id,
                intval($data['cantidad']),
                "Importación masiva desde Excel - Fila $rowNumber" . ($observations ? " | $observations" : "")
            ]);

            // Confirmar transacción
            $connect->commit();

            $successCount++;
            $details[] = [
                'row' => $rowNumber,
                'codigo' => $data['codigo_g'],
                'status' => 'success',
                'message' => 'Importado correctamente'
            ];

        } catch (PDOException $e) {
            // Rollback en caso de error de base de datos
            if ($connect->inTransaction()) {
                $connect->rollBack();
            }
            
            $errorMessage = 'Error de base de datos: ' . $e->getMessage();
            $errors[] = [
                'row' => $rowNumber,
                'codigo' => $data['codigo_g'] ?? 'N/A',
                'error' => $errorMessage
            ];
            
            error_log("Error PDO en fila $rowNumber: " . $e->getMessage());
            
        } catch (Exception $e) {
            // Rollback en caso de error de validación
            if ($connect->inTransaction()) {
                $connect->rollBack();
            }
            
            $errors[] = [
                'row' => $rowNumber,
                'codigo' => $data['codigo_g'] ?? 'N/A',
                'error' => $e->getMessage()
            ];
        }
    }

    // Preparar respuesta
    $response = [
        'success' => true,
        'message' => "Importación completada. $successCount equipos importados exitosamente.",
        'results' => [
            'total_rows' => $totalRows,
            'success' => $successCount,
            'skipped' => $skipCount,
            'errors' => $errors,
            'details' => $details
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    error_log("Error en import_excel_equipos: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    // Limpiar archivo temporal si existe
    if (isset($file['tmp_name']) && file_exists($file['tmp_name'])) {
        @unlink($file['tmp_name']);
    }
}
?> 