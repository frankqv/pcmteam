<?php
require_once __DIR__ . '../../../config/ctconex.php';
//
if (isset($_POST['stupduser'])) {
    $id = $_POST['txtidc'];
    $nombre = $_POST['txtnaame'];
    $usuario = $_POST['txtuse'];
    $correo = $_POST['txtmail'];
    $rol = $_POST['txtrol']; // Nuevo campo para el rol
    try {
        $query = "UPDATE usuarios SET nombre=:nombre, usuario=:usuario, correo=:correo, rol=:rol WHERE id=:id LIMIT 1"; // Se agrega el campo rol a la consulta
        $statement = $connect->prepare($query);
        $data = [
            ':nombre' => $nombre,
            ':usuario' => $usuario,
            ':correo' => $correo,
            ':rol' => $rol, // Se incluye el nuevo dato del rol en los datos para la consulta
            ':id' => $id
        ];
        $query_execute = $statement->execute($data);
        if ($query_execute) {
            echo '<script type="text/javascript">
                    swal("¡Actualizado!", "Actualizado correctamente", "success").then(function() {
                        window.location = "../usuario/mostrar.php";
                    });
                </script>';
            exit(0);
        } else {
            echo '<script type="text/javascript">
                    swal("Error!", "Error al actualizar", "error").then(function() {
                        window.location = "../usuario/mostrar.php";
                    });
                </script>';
            exit(0);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>