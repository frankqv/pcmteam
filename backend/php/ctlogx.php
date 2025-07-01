<?php
session_start();
error_reporting(0);

// Corregir la ruta del archivo de conexión
require_once __DIR__ . '/../bd/ctconex.php';

// Comentado temporalmente para evitar redirecciones no deseadas
/*
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion == '') {
    header("Location: ../../index.php");
    die();
}
*/

if (isset($_POST['ctglog'])) {
  $errMsg = '';

  $usuario = $_POST['usuario'];
  $clave = MD5($_POST['clave']);

  if ($usuario == '') $errMsg = 'Digite su usuario';
  if ($clave == '') $errMsg = 'Digite su contraseña';

  if ($errMsg == '') {
    try {
      $stmt = $connect->prepare('SELECT id, nombre, usuario, correo, clave, rol, estado FROM usuarios WHERE usuario = :usuario');
      $stmt->execute([':usuario' => $usuario]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($data == false) {
        $errMsg = "El nombre de usuario: $usuario no se encuentra, puede solicitarlo con el administrador.";
      } else {
        if ($clave == $data['clave']) {
          $_SESSION['id'] = $data['id'];
          $_SESSION['nombre'] = $data['nombre'];
          $_SESSION['usuario'] = $data['usuario'];
          $_SESSION['correo'] = $data['correo'];
          $_SESSION['clave'] = $data['clave'];
          $_SESSION['rol'] = $data['rol'];
          $_SESSION['estado'] = $data['estado'];

          switch ($_SESSION['rol']) {
            case 1:
              header('Location: ../frontend/administrador/escritorio.php'); //ADMINISTRADOR
              exit;
            case 2:
              header('Location: ../frontend/u_generico/escritorio.php'); //CLIENTE
              exit;
            case 3:
              header('Location: ../frontend/contable/escritorio.php'); //Contable
              exit;
            case 4:
              header('Location: ../frontend/comercial/escritorio.php'); //COMERCIAL
              exit;
            case 5:
              header('Location: ../frontend/jtecnico/escritorio.php'); //JEFE TECNICO
              exit;
            case 6:
              header('Location: ../frontend/tecnico/escritorio.php'); //TECNICO
              exit;
            case 7:
              header('Location: ../frontend/bodega/escritorio.php'); //BODEGA
              exit;
            default:
              $errMsg = 'Rol no definido. Contacte con el administrador.'; //DEFAULT
          }
        } else {
          $errMsg = 'Contraseña incorrecta.';
        }
      }
    } catch (PDOException $e) {
      $errMsg = $e->getMessage();
    }
  }
}

// Mostrar el error en pantalla si existe
if (!empty($errMsg)) {
  // echo "<script>alert('$errMsg'); window.location.href = 'login.php';</script>";
  echo "<div style='color: red; text-align: center; margin: 10px;'>$errMsg</div>";
}
?>
