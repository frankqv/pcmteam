<?php
require_once('../../config/ctconex.php');
// Server-side processing parameters
$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$searchValue = $_POST['search']['value'];
$orderColumnIndex = $_POST['order'][0]['column'];
$orderColumnName = $_POST['columns'][$orderColumnIndex]['data'];
$orderDir = $_POST['order'][0]['dir'];
// Custom filters
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$cliente = $_POST['cliente'];
$vendedor = $_POST['vendedor'];
$metodo_pago = $_POST['metodo_pago'];
// Base query
$baseQuery = " FROM bodega_ordenes bo JOIN clientes c ON bo.cliente_id = c.idclie JOIN usuarios u ON bo.vendedor_id = u.id";
// WHERE conditions
$where = " WHERE 1=1";
if (!empty($fecha_inicio)) {
    $where .= " AND DATE(bo.fecha_creacion) >= '" . $fecha_inicio . "'";
}
if (!empty($fecha_fin)) {
    $where .= " AND DATE(bo.fecha_creacion) <= '" . $fecha_fin . "'";
}
if (!empty($cliente)) {
    $where .= " AND bo.cliente_id = " . intval($cliente);
}
if (!empty($vendedor)) {
    $where .= " AND bo.vendedor_id = " . intval($vendedor);
}
if (!empty($metodo_pago)) {
    $where .= " AND bo.metodo_pago = '" . $metodo_pago . "'";
}
// Global search
if (!empty($searchValue)) {
    $where .= " AND (bo.id_orden LIKE '%$searchValue%' OR c.nomcli LIKE '%$searchValue%' OR c.apecli LIKE '%$searchValue%' OR u.nombre LIKE '%$searchValue%' OR u.apellido LIKE '%$searchValue%')";
}
// Total records without filtering
$stmtTotal = $connect->query("SELECT COUNT(bo.id_orden) as total " . $baseQuery);
$recordsTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
// Total records with filtering
$stmtFiltered = $connect->query("SELECT COUNT(bo.id_orden) as total " . $baseQuery . $where);
$recordsFiltered = $stmtFiltered->fetch(PDO::FETCH_ASSOC)['total'];
// Data query
$sql = "SELECT bo.id_orden, CONCAT(c.nomcli, ' ', c.apecli) as cliente_nombre, bo.total_venta, bo.metodo_pago, bo.estado_pago, bo.fecha_creacion, CONCAT(u.nombre, ' ', u.apellido) as vendedor_nombre";
$sql .= $baseQuery . $where;
$sql .= " ORDER BY $orderColumnName $orderDir LIMIT $start, $length";
$stmtData = $connect->prepare($sql);
$stmtData->execute();
$data = [];
while ($row = $stmtData->fetch(PDO::FETCH_ASSOC)) {
    $row['acciones'] = '<button class="btn btn-primary btn-sm btn-view" data-id="' . $row['id_orden'] . '">Ver</button>';
    $data[] = $row;
}
// Response
$response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($recordsTotal),
    "recordsFiltered" => intval($recordsFiltered),
    "data" => $data
];
header('Content-Type: application/json');
echo json_encode($response);
?>
