<?php
// st_updpropsd.php
require_once __DIR__ . '/../../config/ctconex.php';
// Importante: Iniciar sesión para poder escribir en $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_POST['stupdprofpsd'])) {
    $id = $_POST['txtidadm'];
    $clave = $_POST['txtpawd']; // La contraseña
    // VALIDACIÓN IMPORTANTE: No usar MD5
    // MD5 es inseguro. Usa password_hash()
    if (empty($clave)) {
        $_SESSION['message'] = '¡Error! La contraseña no puede estar vacía.';
        $_SESSION['msg_type'] = 'error';
        header('Location: ../cuenta/perfil.php');
        exit;
    }
    $clave_hasheada = password_hash($clave, PASSWORD_BCRYPT);
    try {
        $query = "UPDATE usuarios SET clave = :clave WHERE id = :id LIMIT 1";
        $statement = $connect->prepare($query);
        $data = [
            ':clave' => $clave_hasheada,
            ':id' => $id
        ];
        $query_execute = $statement->execute($data);
        if ($query_execute) {
            $_SESSION['message'] = '¡Actualizado! Contraseña actualizada correctamente.';
            $_SESSION['msg_type'] = 'success';
        } else {
            $_SESSION['message'] = '¡Error! No se pudo actualizar la contraseña.';
            $_SESSION['msg_type'] = 'error';
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error de base de datos: ' . $e->getMessage();
        $_SESSION['msg_type'] = 'error';
    }
    // Redirigir de vuelta al perfil
    header('Location: ../cuenta/perfil.php');
    exit;
}
?>