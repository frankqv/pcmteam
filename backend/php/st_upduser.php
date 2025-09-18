<?php
// backend/php/st_upduser.php
require_once __DIR__ . '../../../config/ctconex.php';
if (isset($_POST['stupduser'])) {
    $id = $_POST['txtidc'];
    $nombre = $_POST['txtnaame'];
    $usuario = $_POST['txtuse'];
    $correo = $_POST['txtmail'];
    $rol = $_POST['txtrol'];
    $sede = $_POST['txtsede']; 
    $cumple = $_POST['txtcumple'];
    try {
        $query = "UPDATE usuarios SET nombre=:nombre, usuario=:usuario, correo=:correo, rol=:rol, idsede=:sede, cumple=:cumple WHERE id=:id LIMIT 1";
        $statement = $connect->prepare($query);
        $data = [
            ':nombre' => $nombre,
            ':usuario' => $usuario,
            ':correo' => $correo,
            ':rol' => $rol,
            ':sede' => $sede,
            ':cumple' => $cumple,
            ':id' => $id
        ];
        $query_execute = $statement->execute($data);
        if ($query_execute) {
            echo '<script type="text/javascript">
                    swal("¡Actualizado!", "Usuario actualizado correctamente", "success").then(function() {
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
        }    } catch (PDOException $e) {
        echo $e->getMessage();
    } }
?>