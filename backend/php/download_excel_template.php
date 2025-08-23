<?php
// /backend/php/download_excel_template.php
session_start();

// Verificar sesión y permisos
if (!isset($_SESSION['id']) || !isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    header('Location: ../../frontend/error404.php');
    exit();
}

require_once __DIR__ . '../../../config/ctconex.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
            'color' => ['rgb' => 'FF0000'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFE6E6'],
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
            'color' => ['rgb' => '0066CC'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E6F3FF'],
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
    
    // Encabezados de columnas
    $headers = [
        'A1' => 'CÓDIGO GENERAL *',
        'B1' => 'LOTE',
        'C1' => 'UBICACIÓN EN SEDE *',
        'D1' => 'POSICIÓN *',
        'E1' => 'TIPO DE PRODUCTO *',
        'F1' => 'MARCA *',
        'G1' => 'SERIAL *',
        'H1' => 'MODELO *',
        'I1' => 'PROCESADOR',
        'J1' => 'MEMORIA RAM *',
        'K1' => 'DISCO',
        'L1' => 'PULGADAS',
        'M1' => 'OBSERVACIONES',
        'N1' => 'GRADO *',
        'O1' => 'DISPOSICIÓN *',
        'P1' => 'TÁCTIL *',
        'Q1' => 'PROVEEDOR ID *',
        'R1' => 'CANTIDAD'
    ];
    
    // Aplicar encabezados y estilos base
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
        $sheet->getStyle($cell)->applyFromArray($headerStyle);
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
    
    // Agregar datos de ejemplo en la fila 2
    $exampleData = [
        'A2' => 'EQ001',
        'B2' => 'SITEC-2025-01',
        'C2' => 'Principal',
        'D2' => 'ESTANTE-1-A',
        'E2' => 'Portatil',
        'F2' => 'Dell',
        'G2' => 'DL123456789',
        'H2' => 'Latitude 5520',
        'I2' => 'Intel i5-1135G7',
        'J2' => '8GB',
        'K2' => '256GB SSD',
        'L2' => '15.6',
        'M2' => 'Equipo en buen estado',
        'N2' => 'A',
        'O2' => 'En revisión',
        'P2' => 'SI',
        'Q2' => '1',
        'R2' => '1'
    ];
    
    foreach ($exampleData as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }
    
    // Agregar datos de ejemplo en la fila 3
    $exampleData2 = [
        'A3' => 'EQ002',
        'B3' => 'SITEC-2025-01',
        'C3' => 'Unilago',
        'D3' => 'ESTANTE-2-B',
        'E3' => 'Desktop',
        'F3' => 'HP',
        'G3' => 'HP987654321',
        'H3' => 'EliteDesk 800',
        'I3' => 'Intel i7-10700',
        'J3' => '16GB',
        'K3' => '512GB SSD',
        'L3' => 'N/A',
        'M3' => 'EQUIPO LISTO',
        'N3' => 'A',
        'O3' => 'Para Venta',
        'P3' => 'NO',
        'Q3' => '1',
        'R3' => '1'
    ];
    
    foreach ($exampleData2 as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }
    
    // Agregar fila de instrucciones (empezando en fila 5 para dar espacio)
    $instructionStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => '000000'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF99'],
        ],
    ];
    
    $sheet->setCellValue('A5', 'INSTRUCCIONES:');
    $sheet->setCellValue('A6', '1. Los campos marcados con * son obligatorios');
    $sheet->setCellValue('A7', '2. El CÓDIGO GENERAL debe ser único y no contener espacios');
    $sheet->setCellValue('A8', '3. El SERIAL debe ser único para cada equipo');
    $sheet->setCellValue('A9', '4. Use los valores exactos de las listas desplegables');
    $sheet->setCellValue('A10', '5. Si un código ya existe, se omitirá ese equipo y continuará con los demás');
    $sheet->setCellValue('A11', '6. Porfavor, elimene las fialas de intruciones, antes de IMPORTAR el archivo al programa interno');
    
    $sheet->getStyle('A5:A11')->applyFromArray($instructionStyle);
    
    // Agregar listas de valores válidos
    $validValuesStyle = [
        'font' => [
            'color' => ['rgb' => '006600'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E6FFE6'],
        ],
    ];
    
    $sheet->setCellValue('A12', 'VALORES VÁLIDOS:');
    $sheet->setCellValue('A13', 'UBICACIONES: Principal, Unilago, Cúcuta, Medellín');
    $sheet->setCellValue('A14', 'TIPOS: Portatil, Desktop, Monitor, AIO, Tablet, Celular, Impresora, Periferico, otro');
    $sheet->setCellValue('A15', 'MARCAS: HP, Dell, Lenovo, Acer, CompuMax, Otro');
    $sheet->setCellValue('A16', 'RAM: 4GB, 8GB, 16GB, 32GB, otro');
    $sheet->setCellValue('A17', 'GRADOS: A, B, C, SCRAP, #N/D');
    $sheet->setCellValue('A18', 'DISPOSICIONES: En revisión, Por Alistamiento, En Laboratorio, En Bodega, Disposicion final, Para Venta');
    $sheet->setCellValue('A19', 'TÁCTIL: SI, NO');
    
    $sheet->getStyle('A12:A19')->applyFromArray($validValuesStyle);
    
    // Obtener proveedores para la información
    $proveedores = [];
    try {
        $stmt = $connect->prepare("SELECT id, nombre, nomenclatura FROM proveedores WHERE nombre IS NOT NULL ORDER BY nombre ASC");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $proveedores[] = $row['id'] . ' - ' . $row['nombre'] . ' (' . $row['nomenclatura'] . ')';
        }
    } catch (Exception $e) {
        $proveedores = ['Error al cargar proveedores: ' . $e->getMessage()];
    }
    
    $sheet->setCellValue('A21', 'PROVEEDORES DISPONIBLES:');
    $sheet->setCellValue('A22', implode(', ', $proveedores));
    $sheet->getStyle('A21:A22')->applyFromArray($validValuesStyle);
    
    // Ajustar ancho de columnas
    foreach (range('A', 'R') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Establecer altura mínima para la primera fila
    $sheet->getRowDimension('1')->setRowHeight(20);
    
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