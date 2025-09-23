<?php
// Verificar si se han recibido los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir el archivo de conexión a la base de datos
    require_once "../config/ctconex.php";
   // Obtener los datos del formulario
    $nombre = $_POST["nombre"];
    $usuario = $_POST["usuario"];
    $correo = $_POST["correo"];
    $clave = md5($_POST["clave"]); // Hash MD5 de la contraseña
   // Valores por defecto
    $rol = 2; // Rol de usuario_generico
    $foto = 1; // Foto por defecto
    $estado = 1; // Estado activo por defecto
   // Insertar los datos en la base de datos
    $sql = "INSERT INTO usuarios (nombre, usuario, correo, clave, rol, foto, estado) VALUES (:nombre, :usuario, :correo, :clave, :rol, :foto, :estado)";
    $stmt = $connect->prepare($sql);
   // Bind parameters
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':clave', $clave);
    $stmt->bindParam(':rol', $rol);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':estado', $estado);
   try {
        // Ejecutar la consulta preparada
        $stmt->execute();
        echo "<h1 style='display: inline-block; font-size: 24px; color:#000000 ; background:#ffec00; border-radius: 6px; padding: 10px 20px;'>Usuario registrado correctamente</h1>";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { // Verificar el código de error específico para duplicados
            if (strpos($e->getMessage(), 'usuario') !== false) {
                echo "<h1 style='display: inline-block; font-size: 24px; color:#ff0000 ; background:#ffffff; border-radius: 6px; padding: 10px 20px;'>El nombre de usuario ya está registrado</h1>";
            } elseif (strpos($e->getMessage(), 'correo') !== false) {
                echo "<h1 style='display: inline-block; font-size: 24px; color:#ff0000 ; background:#ffffff; border-radius: 6px; padding: 10px 20px;'>El correo electrónico ya está registrado</h1>";
            } else {
                echo "Error al registrar el usuario: " . $e->getMessage();
            }
        } else {
            echo "Error al registrar el usuario: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - PCMARKETTEAM</title>
   <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/img/favicon.webp" />

</head>
<body>
    <div class="register-wrapper">
        <div class="register-form">
            <div class="title">Registro de Usuario</div>
            <form action="registrar.php" method="post">
                <div class="form-group">
                    <div class="label-text">Nombre:</div>
                    <input type="text" name="nombre" required>
                </div>
                <div class="form-group">
                    <div class="label-text">Usuario:</div>
                    <input type="text" name="usuario" required>
                </div>
                <div class="form-group">
                    <div class="label-text">Correo electrónico:</div>
                    <input type="email" name="correo" required>
                </div>
                <div class="form-group">
                    <div class="label-text">Contraseña:</div>
                    <input type="password" name="clave" required>
                </div>
                <div class="actions">
                    <input type="submit" value="Registrarse" class="btn-submit">
                    <button onclick="window.location.href='home.php'" class="btn-submit">Home</button>
                </div>
            </form>
       </div>
    </div>
</body>
</html>
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="assets/js/reenvio.js"></script>