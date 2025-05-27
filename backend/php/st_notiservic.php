<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Credenciales de Gmail
$correo = 'infogtb2@gmail.com';
$clave = '123abcz@';

// Configurar PHPMailer
$mail = new PHPMailer(true); // Pasa true para activar excepciones
try {
    // Configuración del servidor SMTP de Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $correo;
    $mail->Password   = $clave;
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Configurar remitente y destinatario
    $mail->setFrom($correo, 'GTB');
    $mail->addAddress($_POST["email"]);

    // Adjuntar archivo
    $mail->addAttachment($_FILES['my_file']['tmp_name'], $_FILES['my_file']['name']);

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = $_POST["mensaje"];
    $mail->Body    = 'Nombre: ' . $_POST["nombre"] . '<br>Email: ' . $_POST["email"];

    // Enviar correo
    $mail->send();
    echo '<script>alert("El mensaje fue enviado con éxito."); window.location.href = "../servicio/mostrar.php";</script>';
} catch (Exception $e) {
    echo '<script>alert("Error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde."); window.location.href = "../servicio/mostrar.php";</script>';
}
