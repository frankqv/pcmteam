<?php /*
session_start();
    if (isset($_SESSION['id'])){
        header('administrador/escritorio.php');
    }elseif (isset($_SESSION['id'])){
        header('cliente/escritorio.php');
    }
    include_once '../backend/php/ctlogx.php'
 */?>
 <?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['rol'])) {
    switch ($_SESSION['rol']) {
        case 1:
            header('Location: administrador/escritorio.php');
            break;
        case 2:
            header('Location: cliente/escritorio.php');
            break;
        case 3:
            header('Location: comercial/escritorio.php');
            break;
        case 4:
            header('Location: jtecnico/escritorio.php');
            break;
        case 5:
            header('Location: tecnico/escritorio.php');
            break;
        case 6:
            header('Location: bodega/escritorio.php');
            break;
        default:
            $errMsg = "Rol no definido.";
            session_destroy(); // cerrar sesión por seguridad
    }
}
include_once '../backend/php/ctlogx.php';
?>
 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCMARKETTEAM</title>
    <link rel="stylesheet" href="../backend/css/style.css">
    <link rel="icon" type="image/png" href="../backend/img/favicon.png"/>
</head>
<body>
    <div class="login-wrapper">

        <div class=" box-image box-col">
 <img src="../backend/img/sideimage.png" alt="sideimage"> 
        </div>
        <div class="box-col">
           <div class="box-form">
               <div class="inner">
                   <div class="form-head">
                       <div class="title">
                           Bienvenido de nuevo
                       </div>
                       <br>
                       <?php 
                            if (isset($errMsg)) {
                                echo '
    <div style="color:#FF0000;text-align:center;font-size:20px; font-weight:bold;">'.$errMsg.'</div>
    ';  ;
                            }

                        ?>
                       <form class="login-form" autocomplete="off" method="post"  role="form">
                       <div class="form-group">
                           <div class="label-text">Nombre de usuario</div>
                           <input type="text" name="usuario" value="<?php if(isset($_POST['usuario'])) echo $_POST['usuario'] ?>"  autocomplete="off" required class="form-control" placeholder="usuario01">
                       </div>
                       <div class="form-group">
                           <div class="label-text">Contraseña</div>
                           <input name="clave" value="<?php if(isset($_POST['clave'])) echo MD5($_POST['clave']) ?>" type="password" required class="form-control" placeholder="********">
                       </div>
                       
                       <div class="actions">
                           <button name='ctglog' type="submit" class="btn btn-submit">Acceder</button>
                       </div>

                       </form>
                       <div class="actions">
                           <button onclick="window.location.href='./registrar.php'" class="btn btn-submit">Registrar</button>
                       </div>
                   </div>
                   
               </div>
               <button onclick="window.location.href='../home.php'" class="btnHome">Home</button>
                      
           </div>

        </div>
    </div>
  <script src="../backend/js/jquery-3.3.1.min.js"></script>
  <script type="text/javascript" src="../backend/js/reenvio.js"></script>
