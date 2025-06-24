<?php
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados según la tabla clientes
$sheet->fromArray([
    ['numid', 'nomcli', 'apecli', 'naci', 'correo', 'celu', 'estad', 'dircli', 'ciucli', 'idsede']
], NULL, 'A1');

// Ejemplo de fila (opcional, puedes eliminarla si quieres la plantilla vacía)
$sheet->fromArray([
    ['12345678', 'Juan', 'Perez', '1990-01-01', 'juan@correo.com', '3001234567', 'Activo', 'Calle 1 #2-3', 'Bogotá', 'Unilago']
], NULL, 'A2');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="plantilla_clientes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 