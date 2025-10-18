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
// Limpiar cualquier salida previa
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();
try {
    // Crear nuevo documento Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Configurar título
    $sheet->setTitle('Plantilla Importación Equipos');
    // Estilos para campos requeridos (ROJOS)
    $requiredStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'DC3545'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    // Estilos para campos opcionales (VERDES)
    $optionalStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '28A745'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    // Estilo para campos con lista desplegable (AZUL)
    $dropdownStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '0066CC'], // Azul
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    // Encabezados de columnas con indicador de tipo
    $headers = [
        'A1' => ['text' => 'CÓDIGO GENERAL *', 'type' => 'required'],
        'B1' => ['text' => 'LOTE', 'type' => 'optional'],
        'C1' => ['text' => 'UBICACIÓN ▼ *', 'type' => 'dropdown'],
        'D1' => ['text' => 'POSICIÓN *', 'type' => 'required'],
        'E1' => ['text' => 'TIPO PRODUCTO ▼ *', 'type' => 'dropdown'],
        'F1' => ['text' => 'MARCA ▼ *', 'type' => 'dropdown'],
        'G1' => ['text' => 'SERIAL *', 'type' => 'required'],
        'H1' => ['text' => 'MODELO *', 'type' => 'required'],
        'I1' => ['text' => 'PROCESADOR', 'type' => 'optional'],
        'J1' => ['text' => 'MEMORIA RAM ▼ *', 'type' => 'dropdown'],
        'K1' => ['text' => 'DISCO', 'type' => 'optional'],
        'L1' => ['text' => 'PULGADAS', 'type' => 'optional'],
        'M1' => ['text' => 'OBSERVACIONES', 'type' => 'optional'],
        'N1' => ['text' => 'GRADO ▼ *', 'type' => 'dropdown'],
        'O1' => ['text' => 'DISPOSICIÓN ▼ *', 'type' => 'dropdown'],
        'P1' => ['text' => 'TÁCTIL ▼ *', 'type' => 'dropdown'],
        'Q1' => ['text' => 'PROVEEDOR ID ▼ *', 'type' => 'dropdown'],
        'R1' => ['text' => 'CANTIDAD', 'type' => 'optional']
    ];
    // Aplicar encabezados con estilos según tipo
    foreach ($headers as $cell => $data) {
        $sheet->setCellValue($cell, $data['text']);
        if ($data['type'] === 'dropdown') {
            $style = $dropdownStyle;
        } elseif ($data['type'] === 'required') {
            $style = $requiredStyle;
        } else {
            $style = $optionalStyle;
        }
        $sheet->getStyle($cell)->applyFromArray($style);
    }
    // Obtener proveedores de la base de datos
    $proveedores = [];
    $proveedorIds = [];
    $proveedoresTexto = [];
    $primer_proveedor_id = '1';
    try {
        $stmt = $connect->prepare("SELECT id, nombre, nomenclatura FROM proveedores WHERE nombre IS NOT NULL ORDER BY nombre ASC");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (empty($primer_proveedor_id) || $primer_proveedor_id === '1') {
                $primer_proveedor_id = $row['id'];
            }
            $proveedorIds[] = $row['id'];
            $proveedoresTexto[] = $row['id'] . ' - ' . $row['nombre'] . ' (' . $row['nomenclatura'] . ')';
        }
    } catch (Exception $e) {
        error_log("Error al cargar proveedores: " . $e->getMessage());
        $proveedoresTexto = ['1 - Error al cargar proveedores'];
        $proveedorIds = ['1'];
    }
    // ===== CREAR HOJA OCULTA PARA LAS LISTAS =====
    $listSheet = $spreadsheet->createSheet();
    $listSheet->setTitle('_Listas');
    // Poblar la hoja oculta con las listas
    $listSheet->setCellValue('A1', 'Ubicaciones');
    $ubicaciones = ['Principal', 'Unilago', 'Cúcuta', 'Medellín'];
    foreach ($ubicaciones as $i => $val) {
        $listSheet->setCellValue('A' . ($i + 2), $val);
    }
    $listSheet->setCellValue('B1', 'Tipos');
    $tipos = ['Desktop', 'Portatil', 'CPU', 'Monitor', 'AIO', 'Tablet', 'Celular', 'Impresora', 'Periferico', 'otro'];
    foreach ($tipos as $i => $val) {
        $listSheet->setCellValue('B' . ($i + 2), $val);
    }
    $listSheet->setCellValue('C1', 'Marcas');
    $marcas = ['HP', 'Dell', 'Lenovo', 'Acer', 'CompuMax', 'Otro'];
    foreach ($marcas as $i => $val) {
        $listSheet->setCellValue('C' . ($i + 2), $val);
    }
    $listSheet->setCellValue('D1', 'RAM');
    $rams = ['4GB', '8GB', '16GB', '32GB', 'otro'];
    foreach ($rams as $i => $val) {
        $listSheet->setCellValue('D' . ($i + 2), $val);
    }
    $listSheet->setCellValue('E1', 'Grados');
    $grados = ['A', 'B', 'C', 'SCRAP', '#N/D'];
    foreach ($grados as $i => $val) {
        $listSheet->setCellValue('E' . ($i + 2), $val);
    }
    $listSheet->setCellValue('F1', 'Disposiciones');
    $disposiciones = ['En revisión', 'Por Alistamiento', 'En Laboratorio', 'En Bodega', 'Disposicion final', 'Para Venta'];
    foreach ($disposiciones as $i => $val) {
        $listSheet->setCellValue('F' . ($i + 2), $val);
    }
    $listSheet->setCellValue('G1', 'Táctil');
    $tactiles = ['SI', 'NO'];
    foreach ($tactiles as $i => $val) {
        $listSheet->setCellValue('G' . ($i + 2), $val);
    }
    $listSheet->setCellValue('H1', 'ProveedorIDs');
    foreach ($proveedorIds as $i => $val) {
        $listSheet->setCellValue('H' . ($i + 2), $val);
    }
    // Ocultar la hoja de listas
    $listSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
    // Volver a la hoja principal
    $spreadsheet->setActiveSheetIndex(0);
    // Agregar ejemplos en las filas 2 y 3
    $ejemplos = [
        2 => [
            'A' => 'LOTE-5-001',
            'B' => 'COLSOF-LOTE-5',
            'C' => 'Principal',
            'D' => 'ESTANTE-1-A',
            'E' => 'Portatil',
            'F' => 'Dell',
            'G' => 'DL123456789',
            'H' => 'Latitude 5520',
            'I' => 'Intel i5-1135G7',
            'J' => '8GB',
            'K' => '256GB SSD',
            'L' => '15.6',
            'M' => 'Equipo en buen estado',
            'N' => 'A',
            'O' => 'En revisión',
            'P' => 'NO',
            'Q' => $primer_proveedor_id,
            'R' => '1'
        ],
        3 => [
            'A' => 'LOTE-5-002',
            'B' => 'COLSOF-LOTE-5',
            'C' => 'Unilago',
            'D' => 'ESTANTE-2-B',
            'E' => 'Desktop',
            'F' => 'HP',
            'G' => 'HP987654321',
            'H' => 'EliteDesk 800',
            'I' => 'Intel i7-10700',
            'J' => '16GB',
            'K' => '512GB SSD',
            'L' => '',
            'M' => 'EQUIPO LISTO',
            'N' => 'A',
            'O' => 'Para Venta',
            'P' => 'NO',
            'Q' => $primer_proveedor_id,
            'R' => '1'
        ]
    ];
    foreach ($ejemplos as $row => $datos) {
        foreach ($datos as $col => $valor) {
            $sheet->setCellValue($col . $row, $valor);
        }
    }
    // Instrucciones (estilos mejorados)
    $instructionStyle = [
        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '000000']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF99']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
    ];
    $instrucciones = [
        5 => 'INSTRUCCIONES IMPORTANTES:',
        6 => '1. Los campos marcados con * son OBLIGATORIOS',
        7 => '2. Los campos con ▼ (AZULES) tienen listas desplegables - Haga clic en la celda para ver las opciones',
        8 => '3. Los campos VERDES son opcionales',
        9 => '4. El CÓDIGO GENERAL y SERIAL deben ser únicos',
        10 => '5. ELIMINE estas filas de instrucciones (5-27) antes de importar',
        11 => '6. Si un código ya existe, se omitirá ese equipo',
        12 => '7. Las listas desplegables evitan errores de escritura'
    ];
    foreach ($instrucciones as $row => $texto) {
        $sheet->setCellValue('A' . $row, $texto);
        $sheet->getStyle('A' . $row)->applyFromArray($instructionStyle);
    }
    // Valores válidos
    $validValuesStyle = [
        'font' => ['color' => ['rgb' => '006600'], 'size' => 9],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6FFE6']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '006600']]],
    ];
    $valoresValidos = [
        14 => 'CAMPOS CON LISTAS DESPLEGABLES (AZULES):',
        15 => 'UBICACIONES: Principal, Unilago, Cúcuta, Medellín',
        16 => 'TIPOS: Portatil, Desktop, Monitor, AIO, Tablet, Celular, Impresora, Periferico, otro',
        17 => 'MARCAS: HP, Dell, Lenovo, Acer, CompuMax, Otro',
        18 => 'RAM: 4GB, 8GB, 16GB, 32GB, otro',
        19 => 'GRADOS: A, B, C, SCRAP, #N/D',
        20 => 'DISPOSICIONES: En revisión, Por Alistamiento, En Laboratorio, En Bodega, Disposicion final, Para Venta',
        21 => 'TÁCTIL: SI, NO',
        22 => 'CANTIDAD: Solo números enteros positivos (deje vacío para 1)',
        23 => '',
        24 => 'PROVEEDORES DISPONIBLES (use solo el ID numérico):',
        25 => implode(' | ', $proveedoresTexto),
        26 => '',
        27 => '*** HAGA CLIC EN LAS CELDAS AZULES PARA VER LA FLECHA DE DESPLEGABLE ***'
    ];
    foreach ($valoresValidos as $row => $texto) {
        $sheet->setCellValue('A' . $row, $texto);
        $sheet->getStyle('A' . $row)->applyFromArray($validValuesStyle);
    }
    // ===== VALIDACIONES DE DATOS CON REFERENCIAS A HOJA OCULTA =====
    // Configuración de validaciones con referencias a la hoja oculta
    $validaciones = [
        'C' => [
            'rango' => '_Listas!$A$2:$A$' . (count($ubicaciones) + 1),
            'titulo' => 'Ubicación en Sede',
            'prompt' => 'Seleccione la ubicación donde se almacenará el equipo'
        ],
        'E' => [
            'rango' => '_Listas!$B$2:$B$' . (count($tipos) + 1),
            'titulo' => 'Tipo de Producto',
            'prompt' => 'Seleccione el tipo de equipo'
        ],
        'F' => [
            'rango' => '_Listas!$C$2:$C$' . (count($marcas) + 1),
            'titulo' => 'Marca',
            'prompt' => 'Seleccione la marca del equipo'
        ],
        'J' => [
            'rango' => '_Listas!$D$2:$D$' . (count($rams) + 1),
            'titulo' => 'Memoria RAM',
            'prompt' => 'Seleccione la cantidad de memoria RAM'
        ],
        'N' => [
            'rango' => '_Listas!$E$2:$E$' . (count($grados) + 1),
            'titulo' => 'Grado',
            'prompt' => 'Seleccione la clasificación del equipo'
        ],
        'O' => [
            'rango' => '_Listas!$F$2:$F$' . (count($disposiciones) + 1),
            'titulo' => 'Disposición',
            'prompt' => 'Seleccione el estado actual del equipo'
        ],
        'P' => [
            'rango' => '_Listas!$G$2:$G$' . (count($tactiles) + 1),
            'titulo' => 'Táctil',
            'prompt' => '¿El equipo tiene pantalla táctil?'
        ]
    ];
    foreach ($validaciones as $columna => $config) {
        $validation = $sheet->getCell($columna . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true); // IMPORTANTE: Mostrar la flecha del desplegable
        $validation->setErrorTitle('Valor inválido');
        $validation->setError('Por favor seleccione un valor válido de la lista desplegable');
        $validation->setPromptTitle($config['titulo']);
        $validation->setPrompt($config['prompt']);
        $validation->setFormula1($config['rango']);
        // Aplicar a las filas 2-1000
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell($columna . $i)->setDataValidation(clone $validation);
        }
    }
    // Validación especial para PROVEEDOR ID (Columna Q)
    if (!empty($proveedorIds)) {
        $proveedorValidation = $sheet->getCell('Q2')->getDataValidation();
        $proveedorValidation->setType(DataValidation::TYPE_LIST);
        $proveedorValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $proveedorValidation->setAllowBlank(false);
        $proveedorValidation->setShowInputMessage(true);
        $proveedorValidation->setShowErrorMessage(true);
        $proveedorValidation->setShowDropDown(true); // IMPORTANTE: Mostrar la flecha
        $proveedorValidation->setErrorTitle('Valor inválido');
        $proveedorValidation->setError('Por favor seleccione un ID de proveedor válido de la lista');
        $proveedorValidation->setPromptTitle('Proveedor ID');
        $proveedorValidation->setPrompt('Seleccione el ID del proveedor (vea la lista de proveedores en la fila 25)');
        $proveedorValidation->setFormula1('_Listas!$H$2:$H$' . (count($proveedorIds) + 1));
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell('Q' . $i)->setDataValidation(clone $proveedorValidation);
        }
    }
    // Validación para CANTIDAD (Columna R) - Solo números enteros positivos
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
    for ($i = 2; $i <= 1000; $i++) {
        $sheet->getCell('R' . $i)->setDataValidation(clone $cantidadValidation);
    }
    // Ajustar ancho de columnas
    $columnWidths = [
        'A' => 18,
        'B' => 15,
        'C' => 20,
        'D' => 15,
        'E' => 20,
        'F' => 15,
        'G' => 15,
        'H' => 20,
        'I' => 18,
        'J' => 16,
        'K' => 12,
        'L' => 10,
        'M' => 25,
        'N' => 12,
        'O' => 22,
        'P' => 12,
        'Q' => 16,
        'R' => 10
    ];
    foreach ($columnWidths as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
    }
    // Altura de fila de encabezados
    $sheet->getRowDimension('1')->setRowHeight(35);
    // Congelar panel (primera fila)
    $sheet->freezePane('A2');
    // Configurar encabezados HTTP para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Plantilla_Importacion_Equipos_' . date('Y-m-d_His') . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    header('Pragma: public');
    // Crear y enviar archivo
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    // Limpiar recursos
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    exit();
} catch (Exception $e) {
    // Limpiar buffer en caso de error
    if (ob_get_level()) {
        ob_end_clean();
    }
    // Log del error
    error_log("Error generando plantilla Excel: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
    // Mostrar página de error
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(500);
?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error al generar Excel</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .error-container {
                background: white;
                padding: 40px;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 600px;
                width: 100%;
            }
            .error-icon {
                text-align: center;
                font-size: 64px;
                color: #d32f2f;
                margin-bottom: 20px;
            }
            .error-title {
                color: #d32f2f;
                margin-bottom: 15px;
                text-align: center;
                font-size: 24px;
            }
            .error-message {
                color: #666;
                margin-bottom: 20px;
                line-height: 1.6;
                background: #f5f5f5;
                padding: 15px;
                border-radius: 8px;
                border-left: 4px solid #d32f2f;
            }
            .error-details {
                font-size: 14px;
                color: #999;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #e0e0e0;
            }
            .back-button {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                font-weight: 500;
                transition: transform 0.2s, box-shadow 0.2s;
                width: 100%;
                text-align: center;
                margin-top: 20px;
            }
            .back-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <h2 class="error-title">Error al generar plantilla Excel</h2>
            <div class="error-message">
                <strong>Error:</strong> <?php echo htmlspecialchars($e->getMessage()); ?>
            </div>
            <div class="error-details">
                <strong>Archivo:</strong> <?php echo htmlspecialchars(basename($e->getFile())); ?><br>
                <strong>Línea:</strong> <?php echo $e->getLine(); ?>
            </div>
            <a href="javascript:history.back()" class="back-button">← Volver atrás</a>
        </div>
    </body>
    </html>
<?php
    exit();
}
?>