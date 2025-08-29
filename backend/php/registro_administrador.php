<?php
session_start();
// Verificar si el usuario ya ha iniciado sesión, redirigirlo si es así
if (isset($_SESSION['id'])) {
    header('Location: administrador/escritorio.php');
    exit();
}
// Incluir el archivo de conexión a la base de datos
require_once __DIR__ . '../../../config/ctconex.php';
// Mensaje de error por defecto
$errMsg = '';
// Procesar el formulario de registro
if (isset($_POST['registro_administrador'])) {
    // Obtener datos del formulario
    $nombre_usuario = $_POST['nombre_usuario'];
    $contraseña = $_POST['contraseña'];
   // Validar los datos del formulario
    if (!empty($nombre_usuario) && !empty($contraseña)) {
        try {
            // Preparar la consulta para insertar un nuevo administrador en la base de datos
            $sql = "INSERT INTO administradores (nombre_usuario, contraseña) VALUES (:nombre_usuario, :contraseña)";
            $stmt = $connect->prepare($sql);
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt->bindParam(':contraseña', $contraseña);
           // Ejecutar la consulta
            $stmt->execute();
           // Redirigir al administrador a la página de inicio de sesión
            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            // Si ocurre un error al ejecutar la consulta, mostrar un mensaje de error
            $errMsg = 'Error al registrar el administrador. Inténtelo de nuevo.';
        }
    } else {
        $errMsg = 'Por favor, complete todos los campos.';
    }
}
?>
<!-- Estructura HTML del formulario de registro -->
<html lang="es">
<head>
    <!-- Encabezado omitido por brevedad -->
</head>
<body>
    <div class="registro-wrapper">
        <!-- Formulario de registro -->
        <form class="registro-form" method="post">
            <div class="form-group">
                <label for="nombre_usuario">Nombre de usuario:</label>
                <input type="text" name="nombre_usuario" required>
            </div>
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" name="contraseña" required>
            </div>
            <div class="form-group">
                <button type="submit" name="registro_administrador">Registrarse</button>
            </div>
        </form>
        
        <!-- Mostrar mensaje de error si es necesario -->
        <?php if (!empty($errMsg)): ?>
            <div style="color: #FF0000; text-align: center; font-size: 20px; font-weight: bold;">
                <?php echo $errMsg; ?>
            </div>
        <?php endif; ?>
        
        <!-- Enlace para volver al inicio de sesión -->
        <div class="volver-login">
            <a href="login.php">Volver al inicio de sesión</a>
        </div>
    </div>
</body>
</html>
