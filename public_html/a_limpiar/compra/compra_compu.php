<?php
// public_html/bodega/salidas.php
require_once '../../config/ctconex.php';
session_start();
$db = new PDO("mysql:host=" . dbhost . ";dbname=" . dbname . ";charset=utf8", dbuser, dbpass);
// Consultar salidas con joins
$stmt = $db->prepare("
SELECT s.*, i.codigo_g, i.producto, i.marca, i.modelo, 
       c.nomcli, c.apecli, u.nombre as usuario_nombre,
       t.nombre as tecnico_nombre, o.idord as orden_numero
FROM bodega_salidas s
LEFT JOIN bodega_inventario i ON s.inventario_id = i.id
LEFT JOIN clientes c ON s.cliente_id = c.idclie  
LEFT JOIN usuarios u ON s.usuario_id = u.id
LEFT JOIN usuarios t ON s.tecnico_id = t.id
LEFT JOIN bodega_ordenes o ON s.orden_id = o.idord
ORDER BY s.fecha_salida DESC
LIMIT 100
");
$salidas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->execute();
// Consultar datos para formulario
$stmt_equipos = $db->prepare("SELECT id, codigo_g, producto, marca FROM bodega_inventario WHERE estado = 'activo' ORDER BY codigo_g");
$stmt_equipos->execute();
$equipos = $stmt_equipos->fetchAll(PDO::FETCH_ASSOC);
$stmt_clientes = $db->prepare("SELECT idclie, nomcli, apecli FROM clientes WHERE estad = 'Activo' ORDER BY nomcli");
$stmt_clientes->execute();

$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

$stmt_tecnicos = $db->prepare("SELECT id, nombre FROM usuarios WHERE rol IN ('2','3') AND estado = '1' ORDER BY nombre");
$stmt_tecnicos->execute();
$tecnicos = $stmt_tecnicos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salidas/Despachos - PCM Team</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-arrow-up text-danger"></i> SALIDAS Y DESPACHOS</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaSalidaModal">
            <i class="fas fa-plus"></i> Nueva Salida
        </button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaSalidas" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Equipo</th>
                            <th>Cliente</th>
                            <th>Cantidad</th>
                            <th>Estado Despacho</th>
                            <th>Orden #</th>
                            <th>Técnico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salidas as $salida): ?>
                        <tr>
                            <td><strong><?= $salida['id'] ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($salida['fecha_salida'])) ?></td>
                            <td>
                                <?= htmlspecialchars($salida['codigo_g'] ?? 'N/A') ?><br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($salida['producto'] . ' ' . $salida['marca']) ?>
                                </small>
                            </td>
                            <td><?= htmlspecialchars($salida['nomcli'] . ' ' . $salida['apecli']) ?></td>
                            <td><?= $salida['cantidad'] ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($salida['estado_despacho']) {
                                        'pendiente' => 'warning',
                                        'en_ruta' => 'info', 
                                        'entregado' => 'success',
                                        'cancelado' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?= $salida['estado_despacho'] ?? 'N/A' ?>
                                </span>
                            </td>
                            <td><?= $salida['orden_numero'] ?? '-' ?></td>
                            <td><?= htmlspecialchars($salida['tecnico_nombre'] ?? 'N/A') ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="verDetalle(<?= $salida['id'] ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarSalida(<?= $salida['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($salida['estado_despacho'] !== 'entregado'): ?>
                                <button class="btn btn-sm btn-success" onclick="marcarEntregado(<?= $salida['id'] ?>)">
                                    <i class="fas fa-check"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Salida -->
<div class="modal fade" id="nuevaSalidaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="/backend/php/st_add_salida.php" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nueva Salida/Despacho</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Equipo *</label>
                        <select name="inventario_id" class="form-select" required>
                            <option value="">Seleccionar equipo</option>
                            <?php foreach ($equipos as $equipo): ?>
                                <option value="<?= $equipo['id'] ?>">
                                    <?= htmlspecialchars($equipo['codigo_g'] . ' - ' . $equipo['producto'] . ' ' . $equipo['marca']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" class="form-select">
                            <option value="">Seleccionar cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['idclie'] ?>">
                                    <?= htmlspecialchars($cliente['nomcli'] . ' ' . $cliente['apecli']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Precio Unitario</label>
                        <input type="text" name="precio_unit" class="form-control" placeholder="0.00">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Técnico</label>
                        <select name="tecnico_id" class="form-select" required>
                            <option value="">Seleccionar técnico</option>
                            <?php foreach ($tecnicos as $tecnico): ?>
                                <option value="<?= $tecnico['id'] ?>">
                                    <?= htmlspecialchars($tecnico['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Estado Despacho</label>
                        <select name="estado_despacho" class="form-select">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_ruta">En Ruta</option>
                            <option value="entregado">Entregado</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guía/Transportista</label>
                        <input type="text" name="guia_remision" class="form-control" placeholder="Número de guía">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Razón de Salida *</label>
                    <textarea name="razon_salida" class="form-control" rows="2" placeholder="Venta, reparación, devolución..." required></textarea>
                </div>

                <div class="mt-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2"></textarea>
                </div>

                <div class="mt-3">
                    <label class="form-label">Evidencia Foto</label>
                    <input type="file" name="evidencia_foto" class="form-control" accept="image/*">
                    <small class="text-muted">Opcional: foto de entrega o evidencia</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar Salida
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaSalidas').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
});

function verDetalle(id) {
    // Implementar modal de detalle
    alert('Ver detalle de salida #' + id);
}

function editarSalida(id) {
    // Implementar edición
    alert('Editar salida #' + id);
}

function marcarEntregado(id) {
    if (confirm('¿Marcar como entregado?')) {
        fetch('/backend/php/st_update_despacho.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id, estado: 'entregado'})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}
</script>

</body>
</html>