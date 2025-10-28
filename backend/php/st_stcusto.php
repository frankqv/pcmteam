<?php
// Ruta corregida
require_once __DIR__ . '/../../config/ctconex.php';
if (isset($_POST['staddcust'])) {
    // Capturar todos los campos del formulario
    $numid = trim($_POST['txtnum']);
    $nomcli = trim($_POST['txtnaame']);
    $apecli = trim($_POST['txtape']);
    $naci = !empty($_POST['txtnaci']) ? $_POST['txtnaci'] : '1900-01-01';
    $correo = trim($_POST['txtema']);
    $celu = trim($_POST['txtcel']);
    $estad = $_POST['txtesta'];
    // NUEVOS CAMPOS
    $dircli = trim($_POST['txtdire']);
    $ciucli = trim($_POST['txtciud']);
    $idsede = $_POST['txtsede'];
    $tipo_cliente = !empty($_POST['txttipo']) ? $_POST['txttipo'] : NULL;
    $canal_venta = !empty($_POST['txtcanal']) ? $_POST['txtcanal'] : NULL;
    // Validación básica
    if (empty($numid)) {
        echo '<script type="text/javascript">
                swal("Error!", "Por favor ingresa el número de identificación.", "error");
            </script>';
    } else {
        try {
            // Validar que el documento no exista
            $sql = "SELECT * FROM clientes WHERE numid = :numid";
            $stmt = $connect->prepare($sql);
            $stmt->bindParam(':numid', $numid);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                echo '<script type="text/javascript">
                        swal("Error!", "El número de identificación ya está registrado!", "error").then(function() {
                            window.location = "../clientes/mostrar.php";
                        });
                    </script>';
            } else {
                // Validar correo solo si no está vacío
                if (!empty($correo)) {
                    $sql_correo = "SELECT * FROM clientes WHERE correo = :correo";
                    $stmt_correo = $connect->prepare($sql_correo);
                    $stmt_correo->bindParam(':correo', $correo);
                    $stmt_correo->execute();
                    if ($stmt_correo->rowCount() > 0) {
                        echo '<script type="text/javascript">
                                swal("Error!", "El correo electrónico ya está registrado!", "error").then(function() {
                                    window.location = "../clientes/mostrar.php";
                                });
                            </script>';
                        exit;
                    }
                }
                // Insertar con TODOS los campos (incluyendo tipo_cliente y canal_venta)
                $sql = "INSERT INTO clientes(numid, nomcli, apecli, naci, correo, celu, estad, dircli, ciucli, idsede, tipo_cliente, canal_venta)
                        VALUES (:numid, :nomcli, :apecli, :naci, :correo, :celu, :estad, :dircli, :ciucli, :idsede, :tipo_cliente, :canal_venta)";
                $stmt = $connect->prepare($sql);
                $stmt->bindParam(':numid', $numid);
                $stmt->bindParam(':nomcli', $nomcli);
                $stmt->bindParam(':apecli', $apecli);
                $stmt->bindParam(':naci', $naci);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':celu', $celu);
                $stmt->bindParam(':estad', $estad);
                $stmt->bindParam(':dircli', $dircli);
                $stmt->bindParam(':ciucli', $ciucli);
                $stmt->bindParam(':idsede', $idsede);
                $stmt->bindParam(':tipo_cliente', $tipo_cliente);
                $stmt->bindParam(':canal_venta', $canal_venta);
                if ($stmt->execute()) {
                    echo '<script type="text/javascript">
                            swal("¡Registrado!", "Cliente agregado correctamente", "success").then(function() {
                                window.location = "../clientes/mostrar.php";
                            });
                        </script>';
                } else {
                    echo '<script type="text/javascript">
                            swal("Error!", "Error al insertar los datos en la base de datos.", "error");
                        </script>';
                }
            }
        } catch (PDOException $e) {
            echo '<script type="text/javascript">
                    swal("Error!", "Error de base de datos: ' . $e->getMessage() . '", "error");
                </script>';
        }
    }
}
