<?php 

include('../../backend/bd/ctconex.php');
require_once __DIR__ . '/../../vendor/autoload.php';

if(isset($_POST['importar']))
{
  
  $allowedFileType = [
        'application/vnd.ms-excel',
        'text/xls',
        'text/xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (in_array($_FILES["exceldata"]["type"], $allowedFileType)) {
  
  $tempname = $_FILES['exceldata']['tmp_name'];
  
  // Procesar directamente desde la memoria temporal sin guardar en servidor
  $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
  $spreadSheet = $Reader->load($tempname);
  $excelSheet = $spreadSheet->getActiveSheet();
  $spreadSheetAry = $excelSheet->toArray();
  $sheetCount = count($spreadSheetAry);

  $insertedCount = 0;
  $skippedCount = 0;
  $errorCount = 0;

  for($i=1;$i<$sheetCount;$i++)
  {
    // Verificar que la fila no esté vacía
    if (empty($spreadSheetAry[$i][0]) || empty($spreadSheetAry[$i][1])) {
      continue; // Saltar filas vacías
    }

    // Verificar si el numid ya existe
    $check = $connect->prepare("SELECT COUNT(*) FROM clientes WHERE numid = ?");
    $check->execute([$spreadSheetAry[$i][0]]);
    if ($check->fetchColumn() > 0) {
        // Salta este registro duplicado
        $skippedCount++;
        continue;
    }

    // Validar que los datos no estén vacíos
    $numid = trim($spreadSheetAry[$i][0]);
    $nomcli = trim($spreadSheetAry[$i][1]);
    $apecli = trim($spreadSheetAry[$i][2]);
    $naci = trim($spreadSheetAry[$i][3]);
    $correo = trim($spreadSheetAry[$i][4]);
    $celu = trim($spreadSheetAry[$i][5]);
    $estad = trim($spreadSheetAry[$i][6]);
    $dircli = trim($spreadSheetAry[$i][7]);
    $ciucli = trim($spreadSheetAry[$i][8]);
    $idsede = trim($spreadSheetAry[$i][9]);

    // Validar datos requeridos
    if (empty($numid) || empty($nomcli) || empty($apecli)) {
      $errorCount++;
      continue;
    }

    // Validar formato de fecha
    if (!empty($naci) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $naci)) {
      $naci = '1900-01-01'; // Fecha por defecto si no es válida
    }

    try {
      $d4 = $connect->prepare("INSERT INTO clientes (numid, nomcli, apecli, naci, correo, celu, estad, dircli, ciucli, idsede) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $inserted = $d4->execute([
          $numid,
          $nomcli,
          $apecli,
          $naci,
          $correo,
          $celu,
          $estad,
          $dircli,
          $ciucli,
          $idsede
      ]);

      if ($inserted) {
        $insertedCount++;
      } else {
        $errorCount++;
      }
    } catch (Exception $e) {
      $errorCount++;
      // Log del error para debugging
      error_log("Error insertando cliente: " . $e->getMessage());
    }
  }
  
  $mensaje = "Importación completada:\n";
  $mensaje .= "- Registros insertados: $insertedCount\n";
  $mensaje .= "- Registros duplicados ignorados: $skippedCount\n";
  $mensaje .= "- Errores: $errorCount";
  
  echo "<script>alert('$mensaje'); window.location.href='mostrar.php';</script>";
  
  } else {
    echo "<script>alert('Tipo de archivo no válido. Por favor, sube un archivo Excel (.xlsx)');</script>";
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
