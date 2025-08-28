<?php
require __DIR__ . '/../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
// Encabezados
$sheet->fromArray([
    ['numid', 'nomcli', 'apecli', 'naci', 'correo', 'celu', 'estad', 'dircli', 'ciucli', 'idsede']
], NULL, 'A1');
// 4 ejemplos, uno para cada sede
$sheet->fromArray([
    ['12345678', 'Juan', 'Perez', '1990-01-01', 'juan@correo.com', '3001234567', 'Activo', 'Calle 1 #2-3', 'Bogotá', 'Medellin'],
    ['87654321', 'Maria', 'Garay', '1985-05-15', 'maria@correo.com', '3009876543', 'Activo', 'Carrera 5 #10-20', 'Bogotá', 'Unilago'],
    ['11223344', 'Carlos', 'Lopez Vanegas', '1992-08-22', 'carlos@correo.com', '3005556666', 'Activo', 'Avenida 3 #15-8', 'Cucuta', 'Cucuta'],
    ['55667788', 'Anyi', 'Rodriguez Vidal', '1988-12-10', 'ana@correo.com', '3001112222', 'Activo', 'Calle 8 #25-12', 'Bogotá', 'Principal']
], NULL, 'A2');
// Formato básico
$sheet->getStyle('A1:J1')->getFont()->setBold(true);
$sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('A1:J1')->getFill()->getStartColor()->setRGB('CCCCCC');
// Ajustar columnas
foreach(range('A','J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="plantilla_clientes.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 