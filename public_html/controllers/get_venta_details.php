<?php
require_once('../../config/ctconex.php');
if (isset($_POST['id'])) {
    $saleId = $_POST['id'];
    try {
        $stmt = $connect->prepare("SELECT total_products FROM orders WHERE idord = :id");
        $stmt->bindParam(':id', $saleId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $products_string = $result['total_products'];
            // La cadena parece ser: Nombre (Cantidad) - Precio, ...
            $products = explode(', ', $products_string);
            
            $html = '<ul class="list-group">';
            foreach ($products as $product) {
                if(trim($product) != ''){
                    $html .= '<li class="list-group-item">' . htmlspecialchars(trim($product)) . '</li>';
                }
            }
            $html .= '</ul>';
            echo $html;
        } else {
            echo '<div class="alert alert-warning">No se encontraron detalles para esta venta.</div>';
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Error al consultar la base de datos.</div>';
    }
} else {
    echo '<div class="alert alert-danger">ID de venta no proporcionado.</div>';
}
?>
