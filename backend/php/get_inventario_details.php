<?php
session_start();

// ✅ RUTA CORREGIDA - Usando __DIR__ de manera segura
require_once __DIR__ . '../../../config/ctconex.php';

// Verificar que la conexión exista (esperamos $conn como mysqli)
if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    echo "<div class='alert alert-danger'>Error de configuración: no se encontró la conexión a la BD (\$conn).</div>";
    error_log("get_inventario_details.php: \$conn no existe o no es mysqli.");
    exit;
}

// Permisos (ajusta roles si quieres otros)
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1,2,5,6,7])) {
    http_response_code(403);
    echo "<div class='alert alert-danger'>Acceso no autorizado</div>";
    exit;
}

// Validar ID
if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "<div class='alert alert-danger'>ID inválido</div>";
    exit;
}
$id = intval($_GET['id']);

$sql = "
SELECT i.*, 
    e.fecha_entrada,
    p.nombre AS proveedor_nombre,
    u.nombre AS usuario_nombre,
    t.nombre AS tecnico_nombre
FROM bodega_inventario i
LEFT JOIN bodega_entradas e ON i.id = e.inventario_id
LEFT JOIN proveedores p ON e.proveedor_id = p.id
LEFT JOIN usuarios u ON e.usuario_id = u.id
LEFT JOIN usuarios t ON i.tecnico_id = t.id
WHERE i.id = ?
LIMIT 1
";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $equipo = $res->fetch_assoc();
    $stmt->close();

    if (!$equipo) {
        echo "<div class='alert alert-warning'>Equipo no encontrado</div>";
        exit;
    }

    // Obtener diagnóstico más reciente (mysqli)
    $diag = null;
    $sql_diag = "SELECT * FROM bodega_diagnosticos WHERE inventario_id = ? ORDER BY fecha_diagnostico DESC LIMIT 1";
    if ($s2 = $conn->prepare($sql_diag)) {
        $s2->bind_param("i", $id);
        $s2->execute();
        $r2 = $s2->get_result();
        $diag = $r2->fetch_assoc();
        $s2->close();
    }

    // Obtener control de calidad más reciente
    $cc = null;
    $sql_cc = "SELECT * FROM bodega_control_calidad WHERE inventario_id = ? ORDER BY fecha_control DESC LIMIT 1";
    if ($s3 = $conn->prepare($sql_cc)) {
        $s3->bind_param("i", $id);
        $s3->execute();
        $r3 = $s3->get_result();
        $cc = $r3->fetch_assoc();
        $s3->close();
    }

    // Generar HTML (puedes adaptar diseño)
    ?>
    <div class="row">
        <div class="col-md-6">
            <h5>Información General</h5>
            <table class="table table-sm">
                <tr><td><strong>Código:</strong></td><td><?= htmlspecialchars($equipo['codigo_g'] ?? $equipo['codigo'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Producto:</strong></td><td><?= htmlspecialchars($equipo['producto'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Marca:</strong></td><td><?= htmlspecialchars($equipo['marca'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Modelo:</strong></td><td><?= htmlspecialchars($equipo['modelo'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Serial:</strong></td><td><?= htmlspecialchars($equipo['serial'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Ubicación:</strong></td><td><?= htmlspecialchars($equipo['ubicacion'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Técnico a cargo:</strong></td><td><?= htmlspecialchars($equipo['tecnico_nombre'] ?? 'Sin asignar') ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <h5>Especificaciones</h5>
            <table class="table table-sm">
                <tr><td><strong>Procesador:</strong></td><td><?= htmlspecialchars($equipo['procesador'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>RAM:</strong></td><td><?= htmlspecialchars($equipo['ram'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Disco:</strong></td><td><?= htmlspecialchars($equipo['disco'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Grado:</strong></td><td><?= htmlspecialchars($equipo['grado'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Disposición:</strong></td><td><?= htmlspecialchars($equipo['disposicion'] ?? 'N/A') ?></td></tr>
            </table>
        </div>
    </div>

    <?php if (!empty($equipo['observaciones'])): ?>
    <div class="row mt-2">
        <div class="col-md-12">
            <h5>Observaciones</h5>
            <div class="alert alert-info"><?= nl2br(htmlspecialchars($equipo['observaciones'])) ?></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mt-3">
        <div class="col-md-6">
            <h5>Información de Entrada</h5>
            <table class="table table-sm">
                <tr><td><strong>Fecha entrada:</strong></td><td><?= htmlspecialchars($equipo['fecha_entrada'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Proveedor:</strong></td><td><?= htmlspecialchars($equipo['proveedor_nombre'] ?? 'N/A') ?></td></tr>
                <tr><td><strong>Registrado por:</strong></td><td><?= htmlspecialchars($equipo['usuario_nombre'] ?? 'N/A') ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <h5>Historial Técnico</h5>
            <?php if ($diag): ?>
                <div class="alert alert-info">
                    <strong>Diagnóstico:</strong> <?= htmlspecialchars($diag['estado_reparacion'] ?? $diag['descripcion'] ?? 'N/A') ?><br>
                    <small>Fecha: <?= htmlspecialchars($diag['fecha_diagnostico'] ?? 'N/A') ?></small>
                </div>
            <?php endif; ?>

            <?php if ($cc): ?>
                <div class="alert <?= ($cc['estado_final'] ?? '') === 'aprobado' ? 'alert-success' : 'alert-danger' ?>">
                    <strong>Control de Calidad:</strong> <?= htmlspecialchars($cc['estado_final'] ?? 'N/A') ?><br>
                    <small>Fecha: <?= htmlspecialchars($cc['fecha_control'] ?? 'N/A') ?></small>
                </div>
            <?php endif; ?>

            <?php if (!$diag && !$cc): ?>
                <div class="alert alert-secondary">No hay historial técnico disponible</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12 text-center">
            <a href="../bodega/editar_inventario.php?id=<?= intval($equipo['id']) ?>" class="btn btn-primary"><i class="material-icons">edit</i> Editar Equipo</a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons">close</i> Cerrar</button>
        </div>
    </div>
    <?php

} else {
    http_response_code(500);
    echo "<div class='alert alert-danger'>Error en la preparación de la consulta</div>";
    error_log("get_inventario_details.php prepare failed: " . $conn->error);
}
?>