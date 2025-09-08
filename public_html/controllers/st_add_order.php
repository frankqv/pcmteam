<?php
/* controllers/st_add_order.php */
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1,4,5,6,7])) {
    header('location: ../error404.php');
    exit();
}
require_once '../../config/ctconex.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../bodega/orden_nueva.php');
    exit();
}

$cliente_id   = (int)($_POST['cliente_id'] ?? 0);
$total_items  = (int)($_POST['total_items'] ?? 0);
$total_pago   = (float)($_POST['total_pago'] ?? 0);
$fecha_pago   = trim($_POST['fecha_pago'] ?? '') ?: null;
$metodo_pago  = trim($_POST['metodo_pago'] ?? '') ?: null;
$estado_pago  = trim($_POST['estado_pago'] ?? 'Pendiente');
$tipo_doc     = trim($_POST['tipo_doc'] ?? 'ticket');
$num_documento= trim($_POST['num_documento'] ?? '') ?: null;
$despachado_en= trim($_POST['despachado_en'] ?? '') ?: null;
$responsable  = (int)($_SESSION['id'] ?? 0);

if ($cliente_id <= 0 || $responsable <= 0 || $total_items < 0 || $total_pago < 0) {
    header('Location: ../bodega/orden_nueva.php?err=invalid');
    exit();
}

// evidencia_pago
$evidencia = null;
if (!empty($_FILES['evidencia_pago']['name']) && is_uploaded_file($_FILES['evidencia_pago']['tmp_name'])) {
    $ext = strtolower(pathinfo($_FILES['evidencia_pago']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','pdf'];
    if (in_array($ext, $allowed)) {
        $safe = 'evid_ord_' . $cliente_id . '_' . time() . '.' . $ext;
        $dest = realpath(__DIR__ . '/../assets/img');
        if ($dest && move_uploaded_file($_FILES['evidencia_pago']['tmp_name'], $dest . DIRECTORY_SEPARATOR . $safe)) {
            $evidencia = $safe;
        }
    }
}

try {
    $stmt = $connect->prepare("INSERT INTO bodega_ordenes (cliente_id, responsable, total_items, total_pago, fecha_pago, metodo_pago, estado_pago, tipo_doc, num_documento, evidencia_pago, despachado_en, creado_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cliente_id, $responsable, $total_items, $total_pago, $fecha_pago, $metodo_pago, $estado_pago, $tipo_doc, $num_documento, $evidencia, $despachado_en, $responsable]);
} catch (Exception $e) {
    header('Location: ../bodega/orden_nueva.php?err=db');
    exit();
}

header('Location: ../bodega/despacho.php');
exit();
?>


