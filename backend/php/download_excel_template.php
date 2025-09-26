<?php
// /backend/php/download_excel_template.php
session_start();

// Verificar sesión y permisos
if (!isset($_SESSION['id']) || !isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    header('Location: ../../public_html/error404.php');
    exit();
}

require_once __DIR__ . '/../../config/ctconex.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

// Limpiar cualquier salida previa que pueda corromper el archivo
ob_clean();

try {
    // Crear nuevo documento Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Configurar título
    $sheet->setTitle('Plantilla Importación Equipos');
    
    // Estilos para encabezados
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    // Estilos para campos requeridos
    $requiredStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'DC3545'], // Rojo para campos obligatorios
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    // Estilos para campos opcionales
    $optionalStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '28A745'], // Verde para campos opcionales
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    // Encabezados de columnas (MAPEO EXACTO CON EL CÓDIGO DE IMPORTACIÓN)
    $headers = [
        'A1' => 'CÓDIGO GENERAL *',      // codigo_g
        'B1' => 'LOTE',                  // lote
        'C1' => 'UBICACIÓN EN SEDE *',   // ubicacion
        'D1' => 'POSICIÓN *',            // posicion
        'E1' => 'TIPO DE PRODUCTO *',    // producto
        'F1' => 'MARCA *',               // marca
        'G1' => 'SERIAL *',              // serial
        'H1' => 'MODELO *',              // modelo
        'I1' => 'PROCESADOR',            // procesador
        'J1' => 'MEMORIA RAM *',         // ram
        'K1' => 'DISCO',                 // disco
        'L1' => 'PULGADAS',              // pulgadas
        'M1' => 'OBSERVACIONES',         // observaciones
        'N1' => 'GRADO *',               // grado
        'O1' => 'DISPOSICIÓN *',         // disposicion
        'P1' => 'TÁCTIL *',              // tactil
        'Q1' => 'PROVEEDOR ID *',        // proveedor_id
        'R1' => 'CANTIDAD'               // cantidad
    ];
    
    // Aplicar encabezados
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }
    
    // Aplicar estilos específicos por tipo de campo
    $requiredFields = ['A1', 'C1', 'D1', 'E1', 'F1', 'G1', 'H1', 'J1', 'N1', 'O1', 'P1', 'Q1'];
    $optionalFields = ['B1', 'I1', 'K1', 'L1', 'M1', 'R1'];
    
    foreach ($requiredFields as $cell) {
        $sheet->getStyle($cell)->applyFromArray($requiredStyle);
    }
    
    foreach ($optionalFields as $cell) {
        $sheet->getStyle($cell)->applyFromArray($optionalStyle);
    }
    
    // Obtener proveedores para los datos de ejemplo
    $proveedores = [];
    try {
        $stmt = $connect->prepare("SELECT id, nombre, nomenclatura FROM proveedores WHERE nombre IS NOT NULL ORDER BY nombre ASC");
        $stmt->execute();
        $primer_proveedor_id = null;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($primer_proveedor_id === null) {
                $primer_proveedor_id = $row['id'];
            }
            $proveedores[] = $row['id'] . ' - ' . $row['nombre'] . ' (' . $row['nomenclatura'] . ')';
        }
    } catch (Exception $e) {
        $primer_proveedor_id = '1';
        $proveedores = ['Error al cargar proveedores: ' . $e->getMessage()];
    }

    // Agregar datos de ejemplo en la fila 2
    $exampleData = [
        'A2' => 'EQ001',                    // codigo_g
        'B2' => 'LOTE-2025-01',            // lote
        'C2' => 'Principal',                // ubicacion
        'D2' => 'ESTANTE-1-A',             // posicion
        'E2' => 'Portatil',                 // producto
        'F2' => 'Dell',                     // marca
        'G2' => 'DL123456789',             // serial
        'H2' => 'Latitude 5520',           // modelo
        'I2' => 'Intel i5-1135G7',         // procesador
        'J2' => '8GB',                      // ram
        'K2' => '256GB SSD',               // disco
        'L2' => '15.6',                     // pulgadas
        'M2' => 'Equipo en buen estado',   // observaciones
        'N2' => 'A',                        // grado
        'O2' => 'En revisión',             // disposicion
        'P2' => 'NO',                       // tactil
        'Q2' => $primer_proveedor_id ?? '1', // proveedor_id
        'R2' => '1'                         // cantidad
    ];
    
    foreach ($exampleData as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }
    
    // Agregar segundo ejemplo en la fila 3
    $exampleData2 = [
        'A3' => 'EQ002',                    // codigo_g
        'B3' => 'LOTE-2025-01',            // lote
        'C3' => 'Unilago',                  // ubicacion
        'D3' => 'ESTANTE-2-B',             // posicion
        'E3' => 'Desktop',                  // producto
        'F3' => 'HP',                       // marca
        'G3' => 'HP987654321',             // serial
        'H3' => 'EliteDesk 800',           // modelo
        'I3' => 'Intel i7-10700',          // procesador
        'J3' => '16GB',                     // ram
        'K3' => '512GB SSD',               // disco
        'L3' => '',                         // pulgadas (vacío para desktop)
        'M3' => 'EQUIPO LISTO',            // observaciones
        'N3' => 'A',                        // grado
        'O3' => 'Para Venta',              // disposicion
        'P3' => 'NO',                       // tactil
        'Q3' => $primer_proveedor_id ?? '1', // proveedor_id
        'R3' => '1'                         // cantidad
    ];
    
    foreach ($exampleData2 as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }
    
    // Agregar instrucciones detalladas
    $instructionStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => '000000'],
            'size' => 10,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF99'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_TOP,
            'wrapText' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    $sheet->setCellValue('A5', 'INSTRUCCIONES IMPORTANTES:');
    $sheet->setCellValue('A6', '1. Los campos marcados con * (ROJOS) son OBLIGATORIOS');
    $sheet->setCellValue('A7', '2. Los campos VERDES son opcionales');
    $sheet->setCellValue('A8', '3. MUCHOS CAMPOS SON DESPLEGABLES - Haga clic en la flecha ▼');
    $sheet->setCellValue('A9', '4. El CÓDIGO GENERAL y SERIAL deben ser únicos');
    $sheet->setCellValue('A10', '5. ELIMINE estas filas de instrucciones antes de importar');
    $sheet->setCellValue('A11', '6. Si un código ya existe, se omitirá ese equipo');
    $sheet->setCellValue('A12', '7. Las listas desplegables evitan errores de escritura');
    
    $sheet->getStyle('A5:A12')->applyFromArray($instructionStyle);
    
    // Agregar valores válidos
    $validValuesStyle = [
        'font' => [
            'color' => ['rgb' => '006600'],
            'size' => 9,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E6FFE6'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_TOP,
            'wrapText' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '006600'],
            ],
        ],
    ];
    
    $sheet->setCellValue('A14', 'CAMPOS CON LISTAS DESPLEGABLES:');
    $sheet->setCellValue('A15', 'UBICACIONES: Principal, Unilago, Cúcuta, Medellín');
    $sheet->setCellValue('A16', 'TIPOS: Portatil, Desktop, Monitor, AIO, Tablet, Celular, Impresora, Periferico, otro');
    $sheet->setCellValue('A17', 'MARCAS: HP, Dell, Lenovo, Acer, CompuMax, Otro');
    $sheet->setCellValue('A18', 'RAM: 4GB, 8GB, 16GB, 32GB, otro');
    $sheet->setCellValue('A19', 'GRADOS: A, B, C, SCRAP, #N/D');
    $sheet->setCellValue('A20', 'DISPOSICIONES: En revisión, Por Alistamiento, En Laboratorio, En Bodega, Disposicion final, Para Venta');
    $sheet->setCellValue('A21', 'TÁCTIL: SI, NO');
    $sheet->setCellValue('A22', 'CANTIDAD: Solo números enteros positivos');
    
    $sheet->getStyle('A14:A22')->applyFromArray($validValuesStyle);
    
    // Información de proveedores
    $sheet->setCellValue('A24', 'PROVEEDORES DISPONIBLES (use solo el ID numérico):');
    $sheet->setCellValue('A25', implode(' | ', $proveedores));
    $sheet->getStyle('A24:A25')->applyFromArray($validValuesStyle);
    
    // Información de proveedores
    $sheet->setCellValue('A23', 'PROVEEDORES DISPONIBLES (use solo el ID numérico):');
    $sheet->setCellValue('A24', implode(' | ', $proveedores));
    $sheet->getStyle('A23:A24')->applyFromArray($validValuesStyle);
    
    // AGREGAR VALIDACIONES DE DATOS (LISTAS DESPLEGABLES)
    
    // 1. Validación para UBICACIÓN EN SEDE (Columna C)
    $ubicacionValidation = $sheet->getCell('C2')->getDataValidation();
    $ubicacionValidation->setType(DataValidation::TYPE_LIST);
    $ubicacionValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
    $ubicacionValidation->setAllowBlank(false);
    $ubicacionValidation->setShowInputMessage(true);
    $ubicacionValidation->setShowErrorMessage(true);
    $ubicacionValidation->setErrorTitle('Valor inválido');
    $ubicacionValidation->setError('Por favor seleccione una ubicación válida de la lista');
    $ubicacionValidation->setPromptTitle('Ubicación en Sede');
    $ubicacionValidation->setPrompt('Seleccione la ubicación donde se almacenará el equipo');
    $ubicacionValidation->setFormula1('"Principal,Unilago,Cúcuta,Medellín"');
    // Copiar validación a las filas 2-1000
    $sheet->setDataValidation('C2:C1000', clone $ubicacionValidation);
    
    // 2. Validación para TIPO DE PRODUCTO (Columna E)
    $productoValidation = $sheet->getCell('E2')->getDataValidation();
    $productoValidation->setType(DataValidation::TYPE_LIST);
    $productoValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
    $productoValidation->setAllowBlank(false);
    $productoValidation->setShowInputMessage(true);
    $productoValidation->setShowErrorMessage(true);
    $productoValidation->setErrorTitle('Valor inválido');
    $productoValidation->setError('Por favor seleccione un tipo de producto válido de la lista');
    $productoValidation->setPromptTitle('Tipo de Producto');
    $productoValidation->setPrompt('Seleccione el tipo de equipo');
    $productoValidation->setFormula1('"Portatil,Desktop,Monitor,AIO,Tablet,Celular,Impresora,Periferico,otro"');
    $sheet->setDataValidation('E2:E1000', clone $productoValidation);
    
    // 3. Validación para MARCA (Columna F)
    $marcaValidation = $sheet->getCell('F2')->getDataValidation();
    $marcaValidation->setType(DataValidation::TYPE_LIST);
    $marcaValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
    $marcaValidation->setAllowBlank(false);
    $marcaValidation->setShowInputMessage(true);
    $marcaValidation->setShowErrorMessage(true);
    $marcaValidation->setErrorTitle('Valor inválido');
    $marcaValidation->setError('Por favor seleccione una marca válida de la lista');
    $marcaValidation->setPromptTitle('Marca');
    $marcaValidation->setPrompt('Seleccione la marca del equipo');
    $marcaValidation->setFormula1('"HP,Dell,Lenovo,Acer,CompuMax,Otro"');
    $sheet->setDataValidation('F2:F1000', clone $marcaValidation);
    
    // 4. Validación para MEMORIA RAM (Columna J)
    $ramValidation = $sheet->getCell('J2')->getDataValidation();
    $ramValidation->setType(DataValidation::TYPE_LIST);
    $ramValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
    $ramValidation->setAllowBlank(false);
    $ramValidation->setShowInputMessage(true);
    $ramValidation->setShowErrorMessage(true);
    $ramValidation->setErrorTitle('Valor inválido');
    $ramValidation->setError('Por favor seleccione una capacidad de RAM válida de la lista');
    $ramValidation->setPromptTitle('Memoria RAM');
    $ramValidation->setPrompt('Seleccione la cantidad de memoria RAM');
    $ramValidation->setFormula1('"4GB,8GB,16GB,32GB,otro"');
    $sheet->setDataValidation('J2:J1000', clone $ramValidation);
    
    // 5. Validación para GRADO (Columna N)
    $gradoValidation = $sheet->getCell('N2')->getDataValidation();
    $gradoValidation->setType(DataValidation::TYPE_LIST);
    $gradoValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
    $gradoValidation->setAllowBlank(false);
    $gradoValidation->setShowInputMessage(true);
    $gradoValidation->setShowErrorMessage(true);
    $gradoValidation->setErrorTitle('Valor inválido');
    $gradoValidation->setError('Por favor seleccione un grado válido de la lista');
    $gradoValidation->setPromptTitle('Grado');
    $gradoValidation->setPrompt('Seleccione la clasificación del equipo');
    $gradoValidation->setFormula1('"A,B,C,SCRAP,#N/D"');
    $sheet->setDataValidation('N2:N1000', clone $gradoValidation);
    
    // 6. Validación para DISPOSICIÓN (Columna O)
    $disposicionValidation = $sheet->getCell('O2')->getDataValidation();
    $disposicionValidation->setType(DataValidation::TYPE_LIST);
    $disposicionValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
    $disposicionValidation->setAllowBlank(false);
    $disposicionValidation->setShowInputMessage(true);
    $disposicionValidation->setShowErrorMessage(true);
    $disposicionValidation->setErrorTitle('Valor inválido');
    $disposicionValidation->setError('Por favor seleccione una disposición válida de la lista');
    $disposicionValidation->setPromptTitle('Disposición');
    $disposicionValidation->setPrompt('Seleccione el estado actual del equipo');
    $disposicionValidation->setFormula1('"En revisión,Por Alistamiento,En Laboratorio,En Bodega,Disposicion final,Para Venta"');
    $sheet->setDataValidation('O2:O1000', clone $disposicionValidation);
    
    // 7. Validación para TÁCTIL (Columna P)
    $tactilValidation = $sheet->getCell('P2')->getDataValidation();
    $tactilValidation->setType(DataValidation::TYPE_LIST);
    $tactilValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
    $tactilValidation->setAllowBlank(false);
    $tactilValidation->setShowInputMessage(true);
    $tactilValidation->setShowErrorMessage(true);
    $tactilValidation->setErrorTitle('Valor inválido');
    $tactilValidation->setError('Por favor seleccione SI o NO');
    $tactilValidation->setPromptTitle('Táctil');
    $tactilValidation->setPrompt('¿El equipo tiene pantalla táctil?');
    $tactilValidation->setFormula1('"SI,NO"');
    $sheet->setDataValidation('P2:P1000', clone $tactilValidation);
    
    // 8. Validación para PROVEEDOR ID (Columna Q) - Lista dinámica basada en BD
    if (!empty($proveedores)) {
        // Crear lista de IDs de proveedores
        $proveedorIds = [];
        try {
            $stmt = $connect->prepare("SELECT id FROM proveedores WHERE nombre IS NOT NULL ORDER BY nombre ASC");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $proveedorIds[] = $row['id'];
            }
        } catch (Exception $e) {
            $proveedorIds = ['1']; // Valor por defecto
        }
        
        if (!empty($proveedorIds)) {
            $proveedorValidation = $sheet->getCell('Q2')->getDataValidation();
            $proveedorValidation->setType(DataValidation::TYPE_LIST);
            $proveedorValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $proveedorValidation->setAllowBlank(false);
            $proveedorValidation->setShowInputMessage(true);
            $proveedorValidation->setShowErrorMessage(true);
            $proveedorValidation->setErrorTitle('Valor inválido');
            $proveedorValidation->setError('Por favor seleccione un ID de proveedor válido de la lista');
            $proveedorValidation->setPromptTitle('Proveedor ID');
            $proveedorValidation->setPrompt('Seleccione el ID del proveedor (vea la lista de proveedores abajo)');
            $proveedorValidation->setFormula1('"' . implode(',', $proveedorIds) . '"');
            $sheet->setDataValidation('Q2:Q1000', clone $proveedorValidation);
        }
    }
    
    // 9. Validación para CANTIDAD (Columna R) - Solo números enteros positivos
    $cantidadValidation = $sheet->getCell('R2')->getDataValidation();
    $cantidadValidation->setType(DataValidation::TYPE_WHOLE);
    $cantidadValidation->setErrorStyle(DataValidation::STYLE_STOP);
    $cantidadValidation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
    $cantidadValidation->setFormula1('1');
    $cantidadValidation->setAllowBlank(true);
    $cantidadValidation->setShowInputMessage(true);
    $cantidadValidation->setShowErrorMessage(true);
    $cantidadValidation->setErrorTitle('Valor inválido');
    $cantidadValidation->setError('La cantidad debe ser un número entero mayor o igual a 1');
    $cantidadValidation->setPromptTitle('Cantidad');
    $cantidadValidation->setPrompt('Ingrese la cantidad (número entero positivo, deje vacío para 1)');
    $sheet->setDataValidation('R2:R1000', clone $cantidadValidation);
    
    // Ajustar ancho de columnas
    $columnWidths = [
        'A' => 18, 'B' => 15, 'C' => 18, 'D' => 15, 'E' => 18, 'F' => 12,
        'G' => 15, 'H' => 20, 'I' => 18, 'J' => 12, 'K' => 12, 'L' => 10,
        'M' => 25, 'N' => 8, 'O' => 18, 'P' => 8, 'Q' => 12, 'R' => 8
    ];
    
    foreach ($columnWidths as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
    }
    
    // Establecer altura para las filas de encabezados
    $sheet->getRowDimension('1')->setRowHeight(25);
    
    // Configurar encabezados HTTP para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Plantilla_Importacion_Equipos_' . date('Y-m-d_H-i-s') . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');
    
    // Crear archivo Excel y enviarlo
    $writer = new Xlsx($spreadsheet);
    
    // Usar buffer de salida para capturar el contenido
    ob_start();
    $writer->save('php://output');
    $content = ob_get_contents();
    ob_end_clean();
    
    // Enviar el contenido
    echo $content;
    exit();
    
} catch (Exception $e) {
    // En caso de error, limpiar buffer y mostrar mensaje
    ob_clean();
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(500);
    
    echo '<!DOCTYPE html>';
    echo '<html lang="es">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Error al generar Excel</title>';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }';
    echo '.error-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }';
    echo '.error-title { color: #d32f2f; margin-bottom: 15px; }';
    echo '.error-message { color: #666; margin-bottom: 20px; }';
    echo '.back-button { background: #1976d2; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }';
    echo '.back-button:hover { background: #1565c0; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<div class="error-container">';
    echo '<h2 class="error-title">Error al generar plantilla Excel</h2>';
    echo '<div class="error-message">';
    echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
    echo '</div>';
    echo '<a href="javascript:history.back()" class="back-button">Volver atrás</a>';
    echo '</div>';
    echo '</body>';
    echo '</html>';
    
    // Log del error
    error_log("Error generando plantilla Excel: " . $e->getMessage() . " - Archivo: " . $e->getFile() . " - Línea: " . $e->getLine());
}
?>