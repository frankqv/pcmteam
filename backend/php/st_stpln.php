<?php
require_once __DIR__ . '../../../config/ctconex.php';
if (isset($_POST['staddplan'])) {
  //$username = $_POST['user_name'];// user name
  //$userjob = $_POST['user_job'];// user email
$imgFile = $_FILES['foto']['name'];
  $tmp_dir = $_FILES['foto']['tmp_name'];
  $imgSize = $_FILES['foto']['size'];
  $nompla = trim($_POST['txtnampla']);
  $estp = trim($_POST['txtesta']);
  $prec = trim($_POST['txtprepl']);
if (empty($nompla)) {
    $errMSG = "Please enter number.";
  } else {
    $upload_dir = '../../../public_html/img/subidas/'; // upload directory
   $imgExt = strtolower(pathinfo($imgFile, PATHINFO_EXTENSION)); // get image extension
   // valid image extensions
    $valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'webp', 'avif'); // valid extensions
   // rename uploading image
    $foto = rand(1000, 1000000) . "." . $imgExt;
   // allow valid image file formats
    if (in_array($imgExt, $valid_extensions)) {
      // Check file size '5MB'
      if ($imgSize < 5000000) {
        move_uploaded_file($tmp_dir, $upload_dir . $foto);
      } else {
        $errMSG = "Sorry, your file is too large.";
      }
    } else {
      $errMSG = "Sorry, only JPG, JPEG, PNG, WEBP & GIF files are allowed.";
    }
  }
 $stmt = "SELECT * FROM plan WHERE nompla ='$nompla'";
  if (empty($nompla)) {
    echo '<script type="text/javascript">
swal("Error!", "Ya existe el registro a agregar!", "error").then(function() {
            window.location = "../plan/nuevo.php";
        });
        </script>';
 } else {  // Validaremos primero que el document no exista
    $sql = "SELECT * FROM plan WHERE nompla ='$nompla'";
    $stmt = $connect->prepare($sql);
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) // Si $row_cnt es mayor de 0 es porque existe el registro
    {
      if (!isset($errMSG)) {
        $stmt = $connect->prepare("INSERT INTO plan(foto,nompla, estp,prec) VALUES(:foto,:nompla,:estp,:prec)");
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':nompla', $nompla);
        $stmt->bindParam(':estp', $estp);
        $stmt->bindParam(':prec', $prec);
        if ($stmt->execute()) {
          echo '<script type="text/javascript">
swal("¡Registrado!", "Se agrego correctamente", "success").then(function() {
            window.location = "../plan/mostrar.php";
        });
        </script>';
        } else {
          $errMSG = "error while inserting....";
        }
      }
    } else {
      echo '<script type="text/javascript">
swal("Error!", "No se pueden agregar datos,  comuníquese con el administrador ", "error").then(function() {
            window.location = "../plan/nuevo.php";
        });
        </script>';
      // if no error occured, continue ....
   }
 }
}
?>