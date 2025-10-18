<?php
// ✅ Intentar cargar PHPMailer desde diferentes ubicaciones
$phpmailer_paths = [
    __DIR__ . '/PHPMailer/Exception.php',
    __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php',
    __DIR__ . '/../../../vendor/phpmailer/phpmailer/src/Exception.php'
];
$loaded = false;
foreach ($phpmailer_paths as $path) {
    if (file_exists($path)) {
        require_once dirname($path) . '/Exception.php';
        require_once dirname($path) . '/PHPMailer.php';
        require_once dirname($path) . '/SMTP.php';
        $loaded = true;
        break;
    }
}
if (!$loaded) {
    // Si PHPMailer no está disponible, mostrar mensaje y redirigir
    echo '<script>alert("PHPMailer no está instalado. Contacte al administrador."); window.location.href = "../servicio/mostrar.php";</script>';
    exit;
}
// ✅ Usar las clases después de cargarlas
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Credenciales de Gmail
$correo = 'pcmarkettbackup@gmail.com';
$clave = 'PCcomercial2025*';
// Configurar PHPMailer
$mail = new PHPMailer(true); // Pasa true para activar excepciones
try {
    // Configuración del servidor SMTP de Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $correo;
    $mail->Password = $clave;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    // Configurar remitente y destinatario
    $mail->setFrom($correo, 'GTB');
    $mail->addAddress($_POST["email"]);
    // Adjuntar archivo
    $mail->addAttachment($_FILES['my_file']['tmp_name'], $_FILES['my_file']['name']);
    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = $_POST["mensaje"];
    $mail->Body = 'Nombre: ' . $_POST["nombre"] . '<br>Email: ' . $_POST["email"];
    // Enviar correo
    $mail->send();
    echo '<script>alert("El mensaje fue enviado con éxito."); window.location.href = "../servicio/mostrar.php";</script>';
} catch (Exception $e) {
    echo '<script>alert("Error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde."); window.location.href = "../servicio/mostrar.php";</script>';
}
