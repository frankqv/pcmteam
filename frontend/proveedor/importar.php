<?php
ob_start();
session_start();
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 5])){
    header('location: ../error404.php');
    exit();
}


// Mensajes de resultado
$mensaje = '';
$tipo_mensaje = '';

// Procesar descarga de plantilla
if (isset($_GET['descargar_plantilla'])) {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="plantilla_proveedores.xlsx"');
    require_once '../../vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Encabezados
    $headers = ['id', 'privado', 'nombre', 'celu', 'dire', 'cuiprov', 'nomenclatura', 'nit', 'email', 'fecha_creacion', 'fecha_actualizacion'];
    $sheet->fromArray($headers, NULL, 'A1');
    // Fila de ejemplo
    $ejemplo = ['8','1','Ejemplo S.A.S.','3202344974', 'Calle 123 #45-67','Bogota','EJEMSAS','901234567','ejemplo@email.com' , '2025-07-14 12:20:31', '2025-07-14 12:20:31'];
    $sheet->fromArray($ejemplo, NULL, 'A2');
    // Fila de Ejemplo dos
    $ejemplo = ['9','1','PcShek Tecnologia Y Servicios S A S','3186890437', 'TV 66 # 35 - 11 MD 3 BG 9','Bogota','PCSH','900413420','comercial@pcshek.com' , '2025-07-14 12:20:31', '2025-07-14 12:20:31'];
    $sheet->fromArray($ejemplo, NULL, 'A3');
    // Formato visual para encabezados
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'D9E1F2']
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ]
    ];
    $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
    // Ajustar ancho de columnas
    foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}

// Procesar importación
if (isset($_POST['importar']) && isset($_FILES['archivo_excel']['tmp_name'])) {
    require_once '../../vendor/autoload.php';
    $archivo = $_FILES['archivo_excel']['tmp_name'];
    $ext = pathinfo($_FILES['archivo_excel']['name'], PATHINFO_EXTENSION);
    if ($ext !== 'xlsx') {
        $mensaje = 'El archivo debe ser formato .xlsx';
        $tipo_mensaje = 'danger';
    } else {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            require_once '../../config/ctconex.php';
            $con = $connect;
            $insertados = 0;
            $errores = [];
            foreach ($rows as $i => $row) {
                if ($i == 0) continue; // Saltar encabezado
                $id = trim($row[0] ?? '');
                $privado = trim($row[1] ?? '');
                $nombre = trim($row[2] ?? '');
                $celu = trim($row[3] ?? '');
                $dire = trim($row[4] ?? '');
                $cuiprov = trim($row[5] ?? '');
                $nomenclatura = trim($row[6] ?? '');
                $nit = trim($row[7] ?? '');
                $correo = trim($row[8] ?? '');
                $fecha_creacion = trim($row[9] ?? '');
                $fecha_actualizacion = trim($row[10] ?? '');

                
                if ($nombre == '' || $nit == '') {
                    $errores[] = "Fila ".($i+1).": Nombre y NIT son obligatorios.";
                    continue;
                }
                // Validación básica de email
                if ($correo && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $errores[] = "Fila ".($i+1).": Email inválido.";
                    continue;
                }
                // Insertar en la base de datos
                $stmt = $con->prepare("INSERT INTO proveedores (id, privado, nombre, celu, dire, cuiprov, nomenclatura, nit, correo, fecha_creacion, fecha_actualizacion)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
privado=VALUES(privado), nombre=VALUES(nombre), celu=VALUES(celu), dire=VALUES(dire), cuiprov=VALUES(cuiprov), nomenclatura=VALUES(nomenclatura), nit=VALUES(nit), correo=VALUES(correo), fecha_creacion=VALUES(fecha_creacion), fecha_actualizacion=VALUES(fecha_actualizacion)");
                if ($stmt->execute([$id, $privado, $nombre, $celu, $dire, $cuiprov, $nomenclatura, $nit, $correo, $fecha_creacion, $fecha_actualizacion])) {
                    $insertados++;
                } else {
                    $errores[] = "Fila ".($i+1).": Error al insertar en la base de datos.";
                }
            }
            if ($insertados > 0) {
                $mensaje = "Importación completada. Proveedores insertados: $insertados.";
                $tipo_mensaje = 'success';
            }
            if (count($errores) > 0) {
                $mensaje .= "<br>Errores:<br>".implode('<br>', $errores);
                $tipo_mensaje = 'warning';
            }
        } catch (Exception $e) {
            $mensaje = 'Error al procesar el archivo: ' . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
}

if (isset($_GET['descargar_plantilla_generica'])) {
    require_once '../../vendor/autoload.php';
    $headers = ['columna1', 'columna2', 'columna3', 'columna4', 'columna5']; // Cambia los nombres si quieres
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($headers, NULL, 'A1');

    // Formato visual para encabezados
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'D9E1F2']
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ]
    ];
    $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
    foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="plantilla_generica.xlsx"');
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Importar Proveedores | PCMARKETTEAM</title>
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/font.css">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.webp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
</head>
<body>V
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Importar Proveedores desde Excel</h5>
            <a href="?descargar_plantilla=1" class="btn btn-info btn-sm">📥 Descargar plantilla Excel</a>
        </div>
        <div class="card-body">
            <?php if($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?>" role="alert">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="archivo_excel">Selecciona archivo Excel (.xlsx):</label>
                    <input type="file" class="form-control-file" id="archivo_excel" name="archivo_excel" accept=".xlsx" required>
                </div>
                <button type="submit" name="importar" class="btn btn-success mt-2">📊 Importar Proveedores</button>
                <a href="mostrar.php" class="btn btn-secondary mt-2">Volver</a>
            </form>
            <hr>
            <p>Porfavor sr usuario, subir nit de la empresa,  el cual puede encontrar en <b>https://www.datacreditoempresas.com.co/</b></p>
            <p class="text-muted">La plantilla debe tener las siguientes columnas: <b>nombre, nit, direccion, telefono, email</b>.</p>
        </div>
    </div>
</div>
</div>
<script src="../../backend/js/jquery-3.3.1.slim.min.js"></script>
<script src="../../backend/js/popper.min.js"></script>
<script src="../../backend/js/bootstrap.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>


