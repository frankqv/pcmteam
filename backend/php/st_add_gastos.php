<?php
require_once __DIR__ . '/../../config/ctconex.php';

if (isset($_POST['staddgast'])) {
    try {
        // Obtener datos del formulario
        $gastos = $_POST['gastos'] ?? [];
        $metodo_pago = trim($_POST['metodo_pago']);
        $idcliente = !empty($_POST['idcliente']) ? intval($_POST['idcliente']) : 0;
        $gasto_por = $_SESSION['id']; // Usuario que registra el gasto
        $observacion_general = trim($_POST['observacion_general'] ?? '');

        // Validar que haya gastos
        if (empty($gastos)) {
            echo '<script type="text/javascript">
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Debe agregar al menos un gasto"
            }).then(function() {
                window.location = "../gastos/mostrar.php";
            });
            </script>';
            exit;
        }

        // Procesar archivo de foto (si existe)
        $foto_nombre = '';
        if (isset($_FILES['foto_comprobante']) && $_FILES['foto_comprobante']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['foto_comprobante'];
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'pdf'];

            if (in_array($extension, $extensiones_permitidas)) {
                if ($archivo['size'] <= 5242880) { // 5MB
                    $foto_nombre = 'gasto_' . uniqid() . '_' . time() . '.' . $extension;
                    $ruta_destino = __DIR__ . '/../../public_html/assets/uploads/' . $foto_nombre;

                    // Crear carpeta si no existe
                    $carpeta = dirname($ruta_destino);
                    if (!is_dir($carpeta)) {
                        mkdir($carpeta, 0777, true);
                    }

                    if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                        $foto_nombre = ''; // Si falla la subida, continuar sin foto
                    }
                }
            }
        }

        // Calcular total y preparar JSON de gastos
        $total_general = 0;
        $gastos_array = [];

        foreach ($gastos as $gasto) {
            if (!empty($gasto['descripcion']) && !empty($gasto['monto'])) {
                $monto = floatval($gasto['monto']);
                $total_general += $monto;

                $gastos_array[] = [
                    'descripcion' => trim($gasto['descripcion']),
                    'monto' => $monto
                ];
            }
        }

        // Convertir a JSON
        $detalle_json = json_encode($gastos_array, JSON_UNESCAPED_UNICODE);

        // Insertar en la base de datos usando MySQLi
        $sql = "INSERT INTO gastos (
            detalle,
            total,
            metodo_pago,
            gasto_por,
            idcliente,
            foto,
            observacion_general,
            fecha_resgistro
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Error al preparar la consulta: ' . $conn->error);
        }

        $stmt->bind_param(
            'sdsiiss',
            $detalle_json,
            $total_general,
            $metodo_pago,
            $gasto_por,
            $idcliente,
            $foto_nombre,
            $observacion_general
        );

        if ($stmt->execute()) {
            echo '<script type="text/javascript">
            Swal.fire({
                icon: "success",
                title: "¡Registrado!",
                text: "Gastos agregados correctamente. Total: $' . number_format($total_general, 0, ',', '.') . '",
                showConfirmButton: true
            }).then(function() {
                window.location = "../gastos/mostrar.php";
            });
            </script>';
        } else {
            throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
        }

        $stmt->close();

    } catch (Exception $e) {
        echo '<script type="text/javascript">
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "' . addslashes($e->getMessage()) . '"
        }).then(function() {
            window.location = "../gastos/mostrar.php";
        });
        </script>';
    }
}
?>
