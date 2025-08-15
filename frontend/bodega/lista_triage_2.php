<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])) {
    header('Location: ../error404.php');
    exit;
}

require_once '../../../backend/bd/ctconex.php';

// Obtener técnicos para asignación/mostrar nombre
$tecnicos = [];
$resTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN (5,6,7) ORDER BY nombre");
while ($r = $resTec->fetch_assoc()) {
    $tecnicos[$r['id']] = $r['nombre'];
}

// Consulta con último triage por equipo e inventario
$sql = "
SELECT 
    inv.id,
    inv.codigo_g,
    inv.producto,
    inv.marca,
    inv.modelo,
    inv.serial,
    inv.disposicion,
    inv.estado,
    inv.fecha_modificacion,
    inv.observaciones,
    u.nombre AS tecnico_inventario,
    t.estado AS triage_estado,
    t.categoria AS triage_categoria,
    t.observaciones AS triage_observaciones,
    t.fecha_registro AS triage_fecha,
    ut.nombre AS tecnico_triage,
    usr.nombre AS usuario_registra

FROM bodega_inventario inv

LEFT JOIN usuarios u ON inv.tecnico_id = u.id

LEFT JOIN (
    SELECT bt1.*
    FROM bodega_triages bt1
    INNER JOIN (
        SELECT inventario_id, MAX(fecha_registro) AS max_fecha
        FROM bodega_triages
        GROUP BY inventario_id
    ) bt2 ON bt1.inventario_id = bt2.inventario_id AND bt1.fecha_registro = bt2.max_fecha
) t ON t.inventario_id = inv.id

LEFT JOIN usuarios ut ON t.tecnico_id = ut.id
LEFT JOIN usuarios usr ON t.usuario_registro = usr.id

ORDER BY inv.fecha_modificacion DESC
";

$result = $conn->query($sql);
if (!$result) {
    die("Error en consulta: " . $conn->error);
}

/* -------------------- Render HTML -------------------- */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de Triages - PCMARKETTEAM</title>
<link rel="stylesheet" href="../../backend/css/bootstrap.min.css" />
<link rel="stylesheet" href="../../backend/css/datatable.css" />
<link rel="stylesheet" href="../../backend/css/buttonsdataTables.css" />
<link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet" />
<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
    table.dataTable thead th { background-color: #004080; color: white; }
</style>

<link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
            <!--google material icon-->
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <div class="body-overlay"></div>

    <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><img src="../../backend/img/favicon.png" class="img-fluid"><span>PCMARKETTEAM</span></h3>
        </div>
        <?php if (function_exists('renderMenu')) { renderMenu($menu); } ?>
    </nav>

    <div id="content">
        <div class="top-navbar">
            <nav class="navbar navbar-expand-lg" style="background: #f39c12">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                        <span class="material-icons">arrow_back_ios</span>
                    </button>
                    <a class="navbar-brand" href="#">INGRESAR TRIAGE 2</a>
                </div>
            </nav>
        </div>
        <div class="main-content p-3">
            
        
<h1>Listado de Triages</h1>

<div class="table-responsive">
<table id="triageTable" class="table table-striped table-bordered table-hover" style="width:100%">
    <thead>
        <tr>
            <th>Código</th>
            <th>Producto</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Serial</th>
            <th>Disposición</th>
            <th>Estado</th>
            <th>Últ. Modificación</th>
            <th>Estado Triag.</th>
            <th>Categoría Triag.</th>
            <th>Técnico Triag.</th>
            <th>Observaciones (Triage)</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['codigo_g']) ?></td>
            <td><?= htmlspecialchars($row['producto']) ?></td>
            <td><?= htmlspecialchars($row['marca']) ?></td>
            <td><?= htmlspecialchars($row['modelo']) ?></td>
            <td><?= htmlspecialchars($row['serial']) ?></td>
            <td><?= htmlspecialchars($row['disposicion']) ?></td>
            <td><?= htmlspecialchars($row['estado']) ?></td>
            <td><?= htmlspecialchars($row['fecha_modificacion']) ?></td>
            <td><?= htmlspecialchars($row['triage_estado'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['triage_categoria'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['tecnico_triage'] ?? '-') ?></td>
            <td><?= nl2br(htmlspecialchars($row['triage_observaciones'] ?? '-')) ?></td>
            <td>
                <a href="triage_2.php?id=<?= (int)$row['id'] ?>" class="btn btn-info btn-sm" title="Ver detalles">
                    <span class="material-icons">visibility</span>
                </a>
                <a href="editar_inventario.php?id=<?= (int)$row['id'] ?>" class="btn btn-primary btn-sm" title="Editar">
                    <span class="material-icons">edit</span>
                </a>
                <button class="btn btn-danger btn-sm delete-btn" data-id="<?= (int)$row['id'] ?>" title="Eliminar">
                    <span class="material-icons">delete</span>
                </button>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

<script src="../../backend/js/jquery-3.3.1.min.js"></script>
<script src="../../backend/js/bootstrap.min.js"></script>
<script src="../../backend/js/datatable.js"></script>
<script src="../../backend/js/datatablebuttons.js"></script>
<script>
$(document).ready(function() {
    var table = $('#triageTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        order: [[7, 'desc']]
    });
    
    // Eliminar registro
    $('#triageTable').on('click', '.delete-btn', function() {
        if(confirm('¿Confirma que desea eliminar este equipo?')) {
            const id = $(this).data('id');
            $.ajax({
                url: '../../../backend/php/delete_inventario.php',
                method: 'POST',
                data: { id },
                success: function() {
                    alert('Equipo eliminado correctamente');
                    location.reload();
                },
                error: function() {
                    alert('Error eliminando el equipo');
                }
            });
        }
    });
});
</script>

        </div> <!-- /main-content -->
    </div> <!-- /content -->
</div> <!-- /wrapper -->

<script src="../../backend/js/jquery-3.3.1.min.js"></script>
<script src="../../backend/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>
<script>
    // Filtrar tabla de equipos asignados
    function filterTable() {
        const q = document.getElementById('filtro').value.toLowerCase();
        const tbody = document.getElementById('equipos-tbody');
        for (const tr of tbody.querySelectorAll('tr')) {
            const text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(q) ? '' : 'none';
        }
    }
    // Seleccionar todos
    function toggleAll(master) {
        const checked = master.checked;
        document.querySelectorAll('.select-one').forEach(el => el.checked = checked);
    }
    // redirige a ?inventario_id=X
    function goToId() {
        const v = document.getElementById('buscar_id').value;
        if (v && parseInt(v) > 0) {
            location.href = '?inventario_id=' + parseInt(v);
        } else {
            alert('Ingresa un inventario id válido.');
        }
    }

    // Haz clic en fila para marcar checkbox
    document.querySelectorAll('#equipos-tbody tr').forEach(row => {
        row.addEventListener('click', function(e){
            if (e.target.tagName.toLowerCase() === 'input') return;
            const cb = this.querySelector('.select-one');
            if (cb) cb.checked = !cb.checked;
        });
    });
</script>
</body>
</html>
