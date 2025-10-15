<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../cuenta/login.php");
    exit;
}

$rol = $_SESSION['rol'] ?? 0;
if (!in_array($rol, [1, 5])) {
    header("Location: ../cuenta/sin_permiso.php");
    exit;
}

require_once('../../config/ctconex.php
');
date_default_timezone_set('America/Bogota');

$sql = "SELECT cc.id, cc.fecha_inspeccion, cc.resultado_final, cc.observaciones, cc.defectos_encontrados, cc.recomendaciones,
    bi.codigo_general, bi.serial, bi.marca, bi.modelo, bi.procesador, bi.ram, bi.disco, bi.pulgada, bi.grado,
    u.nombre as tecnico_nombre
FROM bodega_control_calidad cc
INNER JOIN bodega_inventario bi ON cc.inventario_id = bi.id
LEFT JOIN usuarios u ON cc.tecnico_id = u.id
ORDER BY cc.fecha_inspeccion DESC";

$stmt = $pdo->query($sql);
$inspecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$aprobados = array_filter($inspecciones, fn($i) => $i['resultado_final'] === 'aprobado');
$rechazados = array_filter($inspecciones, fn($i) => $i['resultado_final'] === 'rechazado');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Control de Calidad - PCM Team</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
        .main-container{padding:30px 15px}.content-card{background:white;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,0.2);padding:30px;margin-bottom:30px}
        .page-header{background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);color:white;padding:20px;border-radius:10px;margin-bottom:30px;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
        .page-header h1{margin:0;font-size:2rem;font-weight:700;display:flex;align-items:center;gap:15px}
        .btn-back{background:white;color:#11998e;border:2px solid white;font-weight:600;padding:10px 20px;border-radius:8px;transition:all 0.3s}
        .btn-back:hover{background:#11998e;color:white}
        .badge-aprobado{background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);color:white;padding:6px 14px;border-radius:5px;font-weight:600;font-size:0.9rem}
        .badge-rechazado{background:linear-gradient(135deg,#eb3349 0%,#f45c43 100%);color:white;padding:6px 14px;border-radius:5px;font-weight:600;font-size:0.9rem}
        .badge-grado-a{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:4px 8px;border-radius:5px;font-size:0.8rem;font-weight:600}
        .badge-grado-b{background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);color:white;padding:4px 8px;border-radius:5px;font-size:0.8rem;font-weight:600}
        .stats-card{background:white;border-radius:10px;padding:20px;box-shadow:0 5px 15px rgba(0,0,0,0.1);margin-bottom:20px}
        .stats-number{font-size:2.5rem;font-weight:700;margin:0}.stats-label{font-size:1rem;color:#6c757d;margin-top:5px}
        table.dataTable tbody tr:hover{background-color:#f8f9fa!important}.material-icons{vertical-align:middle}
    </style>
</head>
<body>
    <div class="main-container">
        <div class="container-fluid">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><span class="material-icons" style="font-size:2.5rem;">verified</span>Historial Control de Calidad</h1>
                    <button onclick="window.history.back()" class="btn btn-back"><span class="material-icons">arrow_back</span> Volver</button>
                </div>
                <p class="mb-0 mt-2" style="font-size:1.1rem;">Registro completo de inspecciones realizadas</p>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card" style="border-left:5px solid #38ef7d;">
                        <p class="stats-number" style="color:#38ef7d;"><?php echo count($aprobados); ?></p>
                        <p class="stats-label">Aprobados</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card" style="border-left:5px solid #f45c43;">
                        <p class="stats-number" style="color:#f45c43;"><?php echo count($rechazados); ?></p>
                        <p class="stats-label">Rechazados</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card" style="border-left:5px solid #667eea;">
                        <p class="stats-number" style="color:#667eea;"><?php echo count($inspecciones); ?></p>
                        <p class="stats-label">Total Inspecciones</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card" style="border-left:5px solid #f093fb;">
                        <p class="stats-number" style="color:#f093fb;"><?php echo count($inspecciones) > 0 ? number_format((count($aprobados)/count($inspecciones))*100, 1) . '%' : '0%'; ?></p>
                        <p class="stats-label">Tasa de Aprobación</p>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="table-responsive">
                    <table id="tablaHistorial" class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th><th>Fecha</th><th>Código</th><th>Equipo</th><th>Especificaciones</th><th>Grado</th><th>Técnico</th><th>Resultado</th><th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inspecciones as $i): ?>
                            <tr>
                                <td><strong>#<?php echo $i['id']; ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($i['fecha_inspeccion'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($i['codigo_general']); ?></strong></td>
                                <td><?php echo htmlspecialchars($i['marca'] . ' ' . $i['modelo']); ?><br><small class="text-muted">Serial: <?php echo htmlspecialchars($i['serial']); ?></small></td>
                                <td><small><?php echo htmlspecialchars($i['procesador']); ?><br><?php echo htmlspecialchars($i['ram']); ?> RAM | <?php echo htmlspecialchars($i['disco']); ?><br><?php echo htmlspecialchars($i['pulgada']); ?>"</small></td>
                                <td><?php echo $i['grado'] === 'A' ? '<span class="badge-grado-a">Grado A</span>' : '<span class="badge-grado-b">Grado B</span>'; ?></td>
                                <td><?php echo htmlspecialchars($i['tecnico_nombre'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if ($i['resultado_final'] === 'aprobado'): ?>
                                        <span class="badge-aprobado"><span class="material-icons" style="font-size:16px;">check_circle</span> APROBADO</span>
                                    <?php else: ?>
                                        <span class="badge-rechazado"><span class="material-icons" style="font-size:16px;">cancel</span> RECHAZADO</span>
                                    <?php endif; ?>
                                </td>
                                <td><button class="btn btn-info btn-sm" onclick="verDetalles(<?php echo $i['id']; ?>)"><span class="material-icons" style="font-size:16px;">visibility</span></button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detallesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Detalles de Inspección</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body" id="modalBody"><div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function(){$('#tablaHistorial').DataTable({language:{url:'//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'},order:[[1,'desc']],pageLength:25,responsive:true,dom:'Bfrtip',buttons:[{extend:'excel',text:'<span class="material-icons">download</span> Excel',className:'btn btn-success btn-sm'},{extend:'pdf',text:'<span class="material-icons">picture_as_pdf</span> PDF',className:'btn btn-danger btn-sm'},{extend:'print',text:'<span class="material-icons">print</span> Imprimir',className:'btn btn-info btn-sm'}]})});

        function verDetalles(id){const inspecciones=<?php echo json_encode($inspecciones); ?>;const i=inspecciones.find(x=>x.id==id);if(i){const resultadoBadge=i.resultado_final==='aprobado'?'<span class="badge-aprobado">APROBADO</span>':'<span class="badge-rechazado">RECHAZADO</span>';const gradoBadge=i.grado==='A'?'<span class="badge-grado-a">Grado A</span>':'<span class="badge-grado-b">Grado B</span>';const html=`<div class="row"><div class="col-md-6"><h6><strong>Información del Equipo</strong></h6><p><strong>Código:</strong> ${i.codigo_general}</p><p><strong>Serial:</strong> ${i.serial}</p><p><strong>Marca:</strong> ${i.marca}</p><p><strong>Modelo:</strong> ${i.modelo}</p><p><strong>Procesador:</strong> ${i.procesador}</p><p><strong>RAM:</strong> ${i.ram}</p><p><strong>Disco:</strong> ${i.disco}</p><p><strong>Pulgadas:</strong> ${i.pulgada}"</p><p><strong>Grado:</strong> ${gradoBadge}</p></div><div class="col-md-6"><h6><strong>Información de Inspección</strong></h6><p><strong>Fecha:</strong> ${new Date(i.fecha_inspeccion).toLocaleString('es-CO')}</p><p><strong>Técnico:</strong> ${i.tecnico_nombre||'N/A'}</p><p><strong>Resultado:</strong> ${resultadoBadge}</p><hr><h6><strong>Defectos Encontrados</strong></h6><p>${i.defectos_encontrados||'Ninguno'}</p><h6><strong>Recomendaciones</strong></h6><p>${i.recomendaciones||'Ninguna'}</p><h6><strong>Observaciones</strong></h6><p>${i.observaciones||'Sin observaciones'}</p></div></div>`;$('#modalBody').html(html);new bootstrap.Modal(document.getElementById('detallesModal')).show()}}
    </script>
</body>
</html>
