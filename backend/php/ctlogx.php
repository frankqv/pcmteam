<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../backend/bd/ctconex.php';

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
              header('Location: administrador/escritorio.php'); //ADMINISTRADOR
              exit;
            case 2:
              header('Location: cliente/escritorio.php'); //CLIENTE
              exit;
            case 3:
              header('Location: comercial/escritorio.php'); //COMERCIAL
              exit;
            case 4:
              header('Location: jtecnico/escritorio.php'); //JTECNICO
              exit;
            case 5:
              header('Location: tecnico/escritorio.php'); //TECNICO
              exit;
            case 6:
              header('Location: bodega/escritorio.php'); //BODEGA
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
  echo "<script>alert('$errMsg'); window.location.href = 'login.php';</script>";
}
?>
