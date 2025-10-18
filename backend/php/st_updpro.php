<?php
require_once __DIR__ . '/../../config/ctconex.php';
// st_updpro.php
if (isset($_POST['stupdprof'])) {
    $id = $_POST['txtidadm'];
    $nombre = $_POST['txtnaame'];
    $usuario = $_POST['txtusr'];
    $correo = $_POST['txtcorr'];

    // Solo actualizar rol si está presente en el POST
    $incluir_rol = isset($_POST['txtcarr']) && !empty($_POST['txtcarr']);

    try {
        if ($incluir_rol) {
            $rol = $_POST['txtcarr'];
            $query = "UPDATE usuarios SET nombre=:nombre, usuario=:usuario, correo=:correo, rol=:rol WHERE id=:id LIMIT 1";
            $data = [
                ':nombre' => $nombre,
                ':usuario' => $usuario,
                ':correo' => $correo,
                ':rol' => $rol,
                ':id' => $id
            ];
        } else {
            // No actualizar el campo rol si no está presente
            $query = "UPDATE usuarios SET nombre=:nombre, usuario=:usuario, correo=:correo WHERE id=:id LIMIT 1";
            $data = [
                ':nombre' => $nombre,
                ':usuario' => $usuario,
                ':correo' => $correo,
                ':id' => $id
            ];
        }

        $statement = $connect->prepare($query);
        $query_execute = $statement->execute($data);

        if ($query_execute) {
            echo '<script type="text/javascript">
swal("¡Actualizado!", "Actualizado correctamente", "success").then(function() {
            window.location = "../cuenta/perfil.php";
        });
        </script>';
            exit(0);
        } else {
            echo '<script type="text/javascript">
swal("Error!", "Error al actualizar", "error").then(function() {
            window.location = "../cuenta/perfil.php";
        });
        </script>';
            exit(0);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>
