<?php  
require_once __DIR__ . '../../../config/ctconex.php';
if(isset($_POST['staddcate'])) {
    $nomca = $_POST['txtnaame'];
    $estado = $_POST['txtesta'];
    if(empty($nomca)) {
        $errMSG = "Por favor ingresa el nombre de la categoría.";
    } else {
        // Verificar si la categoría ya existe
        $sql = "SELECT * FROM categoria WHERE nomca=:nomca";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':nomca', $nomca);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo '<script type="text/javascript">
                    swal("Error!", "La categoría ya está registrada!", "error").then(function() {
                        window.location = "../categoria/mostrar.php";
                    });
                </script>';
        } else {
            // Insertar la nueva categoría
            $sql_insert = "INSERT INTO categoria (nomca, estado) VALUES (:nomca, :estado)";
            $stmt_insert = $connect->prepare($sql_insert);
            $stmt_insert->bindParam(':nomca', $nomca);
            $stmt_insert->bindParam(':estado', $estado);
            if($stmt_insert->execute()) {
                echo '<script type="text/javascript">
                        swal("¡Registrado!", "Categoría agregada correctamente", "success").then(function() {
                            window.location = "../categoria/mostrar.php";
                        });
                    </script>';
            } else {
                $errMSG = "Error al insertar la categoría.";
            }
        }
    }
}
?>
