<?php
require_once '../bd/ctconex.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Consulta principal para obtener detalles del inventario
    $sql = "SELECT i.*, 
            e.fecha_entrada, e.proveedor, e.factura,
            d.fecha_diagnostico, d.estado_reparacion, d.observaciones as obs_diagnostico,
            cc.fecha_revision, cc.estado_final, cc.observaciones as obs_calidad,
            s.fecha_salida, s.destino, s.responsable
            FROM bodega_inventario i
            LEFT JOIN bodega_entradas e ON i.id = e.inventario_id
            LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id 
                AND d.id = (SELECT MAX(id) FROM bodega_diagnosticos WHERE inventario_id = i.id)
            LEFT JOIN bodega_control_calidad cc ON i.id = cc.inventario_id 
                AND cc.id = (SELECT MAX(id) FROM bodega_control_calidad WHERE inventario_id = i.id)
            LEFT JOIN bodega_salidas s ON i.id = s.inventario_id
                AND s.id = (SELECT MAX(id) FROM bodega_salidas WHERE inventario_id = i.id)
            WHERE i.id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Formatear la salida HTML
        echo "<div class='container-fluid'>";
        
        // Información básica
        echo "<div class='row mb-3'>";
        echo "<div class='col-12'><h5>Información Básica</h5></div>";
        echo "<div class='col-md-6'>";
        echo "<p><strong>Código:</strong> " . htmlspecialchars($row['codigo_g']) . "</p>";
        echo "<p><strong>Producto:</strong> " . htmlspecialchars($row['producto']) . "</p>";
        echo "<p><strong>Marca:</strong> " . htmlspecialchars($row['marca']) . "</p>";
        echo "<p><strong>Modelo:</strong> " . htmlspecialchars($row['modelo']) . "</p>";
        echo "</div>";
        echo "<div class='col-md-6'>";
        echo "<p><strong>Serial:</strong> " . htmlspecialchars($row['serial']) . "</p>";
        echo "<p><strong>Ubicación:</strong> " . htmlspecialchars($row['ubicacion']) . "</p>";
        echo "<p><strong>Grado:</strong> " . htmlspecialchars($row['grado']) . "</p>";
        echo "<p><strong>Estado:</strong> " . htmlspecialchars($row['disposicion']) . "</p>";
        echo "</div>";
        echo "</div>";
        
        // Información de entrada
        if ($row['fecha_entrada']) {
            echo "<div class='row mb-3'>";
            echo "<div class='col-12'><h5>Información de Entrada</h5></div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Fecha de Entrada:</strong> " . htmlspecialchars($row['fecha_entrada']) . "</p>";
            echo "<p><strong>Proveedor:</strong> " . htmlspecialchars($row['proveedor']) . "</p>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Factura:</strong> " . htmlspecialchars($row['factura']) . "</p>";
            echo "</div>";
            echo "</div>";
        }
        
        // Información de diagnóstico
        if ($row['fecha_diagnostico']) {
            echo "<div class='row mb-3'>";
            echo "<div class='col-12'><h5>Diagnóstico</h5></div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Fecha de Diagnóstico:</strong> " . htmlspecialchars($row['fecha_diagnostico']) . "</p>";
            echo "<p><strong>Estado de Reparación:</strong> " . htmlspecialchars($row['estado_reparacion']) . "</p>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Observaciones:</strong> " . htmlspecialchars($row['obs_diagnostico']) . "</p>";
            echo "</div>";
            echo "</div>";
        }
        
        // Información de control de calidad
        if ($row['fecha_revision']) {
            echo "<div class='row mb-3'>";
            echo "<div class='col-12'><h5>Control de Calidad</h5></div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Fecha de Revisión:</strong> " . htmlspecialchars($row['fecha_revision']) . "</p>";
            echo "<p><strong>Estado Final:</strong> " . htmlspecialchars($row['estado_final']) . "</p>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Observaciones:</strong> " . htmlspecialchars($row['obs_calidad']) . "</p>";
            echo "</div>";
            echo "</div>";
        }
        
        // Información de salida
        if ($row['fecha_salida']) {
            echo "<div class='row mb-3'>";
            echo "<div class='col-12'><h5>Información de Salida</h5></div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Fecha de Salida:</strong> " . htmlspecialchars($row['fecha_salida']) . "</p>";
            echo "<p><strong>Destino:</strong> " . htmlspecialchars($row['destino']) . "</p>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Responsable:</strong> " . htmlspecialchars($row['responsable']) . "</p>";
            echo "</div>";
            echo "</div>";
        }
        
        echo "</div>";
    } else {
        echo "<p class='text-center'>No se encontraron detalles para este equipo.</p>";
    }
    
    $stmt->close();
} else {
    echo "<p class='text-center'>ID de equipo no proporcionado.</p>";
}

$conn->close(); 