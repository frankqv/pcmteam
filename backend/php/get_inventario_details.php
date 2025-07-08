<?php
session_start();
require_once '../bd/ctconex.php';

// Validar autenticación
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    http_response_code(403);
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    exit;
}

// Validar que sea GET y que tenga el ID
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    http_response_code(400);
    echo '<div class="alert alert-danger">Parámetros inválidos</div>';
    exit;
}

$inventario_id = intval($_GET['id']);

if ($inventario_id <= 0) {
    http_response_code(400);
    echo '<div class="alert alert-danger">ID de inventario inválido</div>';
    exit;
}

try {
    // Consulta para obtener todos los detalles del equipo
    $sql = "SELECT i.*, 
            e.fecha_entrada,
            p.nombre as proveedor_nombre,
            u.nombre as usuario_nombre
            FROM bodega_inventario i
            LEFT JOIN bodega_entradas e ON i.id = e.inventario_id
            LEFT JOIN proveedores p ON e.proveedor_id = p.id
            LEFT JOIN usuarios u ON e.usuario_id = u.id
            WHERE i.id = ?";
    
    $stmt = $connect->prepare($sql);
    $stmt->execute([$inventario_id]);
    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipo) {
        echo '<div class="alert alert-warning">Equipo no encontrado</div>';
        exit;
    }

    // Obtener diagnóstico más reciente
    $sql_diag = "SELECT * FROM bodega_diagnosticos WHERE inventario_id = ? ORDER BY fecha_diagnostico DESC LIMIT 1";
    $stmt_diag = $connect->prepare($sql_diag);
    $stmt_diag->execute([$inventario_id]);
    $diagnostico = $stmt_diag->fetch(PDO::FETCH_ASSOC);

    // Obtener control de calidad más reciente
    $sql_cc = "SELECT * FROM bodega_control_calidad WHERE inventario_id = ? ORDER BY fecha_control DESC LIMIT 1";
    $stmt_cc = $connect->prepare($sql_cc);
    $stmt_cc->execute([$inventario_id]);
    $control_calidad = $stmt_cc->fetch(PDO::FETCH_ASSOC);

    ?>
    <div class="row">
        <div class="col-md-6">
            <h5>Información General</h5>
            <table class="table table-sm">
                <tr><td><strong>Código:</strong></td><td><?php echo htmlspecialchars($equipo['codigo_g']); ?></td></tr>
                <tr><td><strong>Producto:</strong></td><td><?php echo htmlspecialchars($equipo['producto']); ?></td></tr>
                <tr><td><strong>Marca:</strong></td><td><?php echo htmlspecialchars($equipo['marca']); ?></td></tr>
                <tr><td><strong>Modelo:</strong></td><td><?php echo htmlspecialchars($equipo['modelo']); ?></td></tr>
                <tr><td><strong>Serial:</strong></td><td><?php echo htmlspecialchars($equipo['serial']); ?></td></tr>
                <tr><td><strong>Ubicación:</strong></td><td><?php echo htmlspecialchars($equipo['ubicacion']); ?></td></tr>
                <tr><td><strong>Posición:</strong></td><td><?php echo htmlspecialchars($equipo['posicion']); ?></td></tr>
                <tr><td><strong>Lote:</strong></td><td><?php echo htmlspecialchars($equipo['codigo_lote']); ?></td></tr>
                <tr><td><strong>Técnico a cargo:</strong></td><td><?php echo htmlspecialchars($equipo['tecnico_id']); ?></td></tr>
              
            </table>
        </div>
        <div class="col-md-6">
            <h5>Especificaciones Técnicas</h5>
            <table class="table table-sm">
                <tr><td><strong>Procesador:</strong></td><td><?php echo htmlspecialchars($equipo['procesador'] ?: 'N/A'); ?></td></tr>
                <tr><td><strong>RAM:</strong></td><td><?php echo htmlspecialchars($equipo['ram']); ?></td></tr>
                <tr><td><strong>Disco:</strong></td><td><?php echo htmlspecialchars($equipo['disco'] ?: 'N/A'); ?></td></tr>
                <tr><td><strong>Pantalla:</strong></td><td><?php echo htmlspecialchars($equipo['pulgadas'] ?: 'N/A'); ?></td></tr>
                <tr><td><strong>Grado:</strong></td><td><span class="badge badge-<?php echo $equipo['grado'] == 'A' ? 'success' : ($equipo['grado'] == 'B' ? 'warning' : 'danger'); ?>"><?php echo htmlspecialchars($equipo['grado']); ?></span></td></tr>
                <tr><td><strong>Disposición:</strong></td><td><?php echo htmlspecialchars($equipo['disposicion']); ?></td></tr>
                <tr><td><strong>Estado:</strong></td><td><span class="badge badge-<?php echo $equipo['estado'] == 'activo' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($equipo['estado']); ?></span></td></tr>
            </table>
        </div>
    </div>

    <?php if ($equipo['observaciones']): ?>
    <div class="row mt-3">
        <div class="col-md-12">
            <h5>Observaciones</h5>
            <div class="alert alert-info">
                <?php echo nl2br(htmlspecialchars($equipo['observaciones'])); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mt-3">
        <div class="col-md-6">
            <h5>Información de Entrada</h5>
            <table class="table table-sm">
                <tr><td><strong>Fecha de entrada:</strong></td><td><?php echo htmlspecialchars($equipo['fecha_entrada'] ?: 'N/A'); ?></td></tr>
                <tr><td><strong>Proveedor:</strong></td><td><?php echo htmlspecialchars($equipo['proveedor_nombre'] ?: 'N/A'); ?></td></tr>
                <tr><td><strong>Registrado por:</strong></td><td><?php echo htmlspecialchars($equipo['usuario_nombre'] ?: 'N/A'); ?></td></tr>
                <tr><td><strong>Última modificación:</strong></td><td><?php echo htmlspecialchars($equipo['fecha_modificacion']); ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <h5>Historial Técnico</h5>
            <?php if ($diagnostico): ?>
            <div class="alert alert-info">
                <strong>Diagnóstico:</strong> <?php echo htmlspecialchars($diagnostico['estado_reparacion']); ?><br>
                <small>Fecha: <?php echo htmlspecialchars($diagnostico['fecha_diagnostico']); ?></small>
            </div>
            <?php endif; ?>
            
            <?php if ($control_calidad): ?>
            <div class="alert alert-<?php echo $control_calidad['estado_final'] == 'aprobado' ? 'success' : 'danger'; ?>">
                <strong>Control de Calidad:</strong> <?php echo htmlspecialchars($control_calidad['estado_final']); ?><br>
                <small>Fecha: <?php echo htmlspecialchars($control_calidad['fecha_control']); ?></small>
            </div>
            <?php endif; ?>
            
            <?php if (!$diagnostico && !$control_calidad): ?>
            <div class="alert alert-secondary">
                No hay historial técnico disponible
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12 text-center">
            <a href="editar_inventario.php?id=<?php echo $equipo['id']; ?>" class="btn btn-primary">
                <i class="material-icons">edit</i> Editar Equipo
            </a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="material-icons">close</i> Cerrar
            </button>
        </div>
    </div>
    <?php

} catch (PDOException $e) {
    http_response_code(500);
    echo '<div class="alert alert-danger">Error al obtener los detalles del equipo: ' . htmlspecialchars($e->getMessage()) . '</div>';
    error_log("Error en get_inventario_details.php: " . $e->getMessage());
}
?> 