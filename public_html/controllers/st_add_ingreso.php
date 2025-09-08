<?php
/* controllers/st_add_ingreso.php */
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1,3,4,5,7])) {
    header('location: ../error404.php');
    exit();
}
require_once '../../config/ctconex.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../bodega/orden_nueva.php');
    exit();
}

$orden_id   = (int)($_POST['orden_id'] ?? 0);
$monto      = (float)($_POST['monto'] ?? 0);
$metodo     = trim($_POST['metodo_pago'] ?? '') ?: null;
$ref        = trim($_POST['referencia_pago'] ?? '') ?: null;
$notas      = trim($_POST['notas'] ?? '') ?: null;
$recibido   = (int)($_SESSION['id'] ?? 0);

if ($orden_id <= 0 || $monto <= 0 || $recibido <= 0) {
    header('Location: ../bodega/orden_nueva.php?err=invalid');
    exit();
}

try {
    $stmt = $connect->prepare("INSERT INTO bodega_ingresos (orden_id, monto, metodo_pago, referencia_pago, recibido_por, notas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$orden_id, $monto, $metodo, $ref, $recibido, $notas]);
} catch (Exception $e) {
    header('Location: ../bodega/orden_nueva.php?err=db');
    exit();
}

header('Location: ../bodega/despacho.php');
exit();
?>


