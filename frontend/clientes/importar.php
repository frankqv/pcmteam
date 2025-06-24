<?php 

include('../../backend/bd/ctconex.php');
require_once __DIR__ . '/../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;

if(isset($_POST['importar']))
{
  
  $allowedFileType = [
        'application/vnd.ms-excel',
        'text/xls',
        'text/xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (in_array($_FILES["exceldata"]["type"], $allowedFileType)) {
  $filename=$_FILES['exceldata']['name'];
  $tempname=$_FILES['exceldata']['tmp_name'];
  $uploadDir = '../../backend/uploads/';
  if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
  }
  move_uploaded_file($tempname, $uploadDir . $filename);

 // Verificar si la clase existe antes de usarla y manejar errores de carga
 try {
     if (!class_exists('\PhpOffice\PhpSpreadsheet\Reader\Xlsx')) {
         throw new Exception('La clase PhpSpreadsheet\\Reader\\Xlsx no está disponible. Verifique la instalación de la librería.');
     }
     $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
     $spreadsheet = $reader->load('../../backend/uploads/' . $filename);
     $excelSheet = $spreadsheet->getActiveSheet();
     $spreadSheetAry = $excelSheet->toArray();
     $sheetCount = count($spreadSheetAry);
 } catch (Exception $e) {
     echo '<script type="text/javascript">
         swal("¡Error!", "No se pudo procesar el archivo Excel: ' . addslashes($e->getMessage()) . '", "error");
         </script>';
     exit;
 }
 
 for($i=1;$i<$sheetCount;$i++)
 {
    // Verificar si el numid ya existe
    $check = $connect->prepare("SELECT COUNT(*) FROM clientes WHERE numid = ?");
    $check->execute([$spreadSheetAry[$i][0]]);
    if ($check->fetchColumn() > 0) {
        // Salta este registro duplicado
        continue;
    }

    $d4 = $connect->prepare("INSERT INTO clientes (numid, nomcli, apecli, naci, correo, celu, estad, dircli, ciucli, idsede) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $inserted = $d4->execute([
        $spreadSheetAry[$i][0], // numid
        $spreadSheetAry[$i][1], // nomcli
        $spreadSheetAry[$i][2], // apecli
        $spreadSheetAry[$i][3], // naci
        $spreadSheetAry[$i][4], // correo
        $spreadSheetAry[$i][5], // celu
        $spreadSheetAry[$i][6], // estad
        $spreadSheetAry[$i][7], // dircli
        $spreadSheetAry[$i][8], // ciucli
        $spreadSheetAry[$i][9]  // idsede
    ]);

    if ($inserted>0) {
      echo '<script type="text/javascript">
swal("¡Registrado!", "Se agrego correctamente", "success").then(function() {
            window.location = "../clientes/mostrar.php";
        });
        </script>';
    }
 }
  }
  else 
  {
    echo 'Please Upload Excel File; Check File Extenstion';
  }
}
?>

<!-- Formulario de importación y enlace a plantilla -->
<form action="" method="post" enctype="multipart/form-data">
    <label for="exceldata">Selecciona archivo Excel (.xlsx):</label>
    <input type="file" name="exceldata" accept=".xlsx,.xls" required>
    <button type="submit" name="importar">Importar</button>
    <a href="plantilla_excel.php" class="btn btn-success">Descargar plantilla Excel</a>
</form>
