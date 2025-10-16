<?php
// public_html/bodega/lista_reserva_venta.php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../cuenta/login.php");
    exit;
}
// Verificar roles permitidos: Admin [1], Comercial [3], Comercial Senior [4]
$rol = $_SESSION['rol'] ?? 0;
if (!in_array($rol, [1, 3, 4])) {
    header("Location: ../cuenta/sin_permiso.php");
    exit;
}
require_once('../../config/ctconex.php');
date_default_timezone_set('America/Bogota');
// Procesar acciones (cancelar, completar, extender)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $reserva_id = intval($_POST['reserva_id']);
        $action = $_POST['action'];
        $connect->beginTransaction();
        if ($action === 'cancelar') {
            // Cancelar reserva
            $stmt = $connect->prepare("UPDATE reserva_venta SET estado = 'cancelada' WHERE id = :id");
            $stmt->execute([':id' => $reserva_id]);
            // Liberar equipo
            $stmt = $connect->prepare("UPDATE bodega_inventario SET disposicion = 'Para Venta', pedido_id = NULL WHERE pedido_id = :reserva_id");
            $stmt->execute([':reserva_id' => $reserva_id]);
            $mensaje = "Reserva cancelada exitosamente";
        } elseif ($action === 'completar') {
            // Completar reserva (marcar como vendida)
            $stmt = $connect->prepare("UPDATE reserva_venta SET estado = 'completada' WHERE id = :id");
            $stmt->execute([':id' => $reserva_id]);
            $mensaje = "Reserva completada exitosamente";
        } elseif ($action === 'extender') {
            // Extender reserva por 5 días más (máximo 1 extensión)
            // Verificar que no se haya extendido antes
            $stmt = $connect->prepare("SELECT fecha_vencimiento, fecha_reserva, observaciones FROM reserva_venta WHERE id = :id");
            $stmt->execute([':id' => $reserva_id]);
            $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($reserva) {
                // Verificar si ya fue extendida (observación contiene "EXTENDIDA")
                if (strpos($reserva['observaciones'], '[EXTENDIDA]') !== false) {
                    throw new Exception("Esta reserva ya fue extendida anteriormente. Solo se permite 1 extensión.");
                }
                $nueva_fecha = date('Y-m-d', strtotime($reserva['fecha_vencimiento'] . ' +5 days'));
                $nueva_obs = $reserva['observaciones'] . "\n[EXTENDIDA] " . date('Y-m-d H:i:s') . " por usuario " . $_SESSION['usuario_id'];
                $stmt = $connect->prepare("UPDATE reserva_venta SET fecha_vencimiento = :nueva_fecha, observaciones = :obs WHERE id = :id");
                $stmt->execute([
                    ':nueva_fecha' => $nueva_fecha,
                    ':obs' => $nueva_obs,
                    ':id' => $reserva_id
                ]);
                $mensaje = "Reserva extendida por 5 días más hasta " . date('d/m/Y', strtotime($nueva_fecha));
            } else {
                throw new Exception("Reserva no encontrada");
            }
        }
        $connect->commit();
        $tipo_mensaje = "success";
    } catch (Exception $e) {
        $connect->rollBack();
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
}
// Obtener todas las reservas con información completa
$sql = "SELECT
    rv.id,
    rv.fecha_reserva,
    rv.fecha_vencimiento,
    rv.observaciones,
    rv.estado,
    DATEDIFF(rv.fecha_vencimiento, CURDATE()) as dias_restantes,
    bi.codigo_general,
    bi.serial,
    bi.marca,
    bi.modelo,
    bi.procesador,
    bi.ram,
    bi.disco,
    bi.pulgada,
    bi.grado,
    bi.precio,
    CONCAT(c.nomcli, ' ', c.apecli) as cliente_nombre,
    c.numid as cliente_documento,
    c.celu as cliente_telefono,
    u.nombre as comercial_nombre
FROM reserva_venta rv
INNER JOIN bodega_inventario bi ON rv.inventario_id = bi.id
INNER JOIN clientes c ON rv.cliente_id = c.idclie
INNER JOIN usuarios u ON rv.usuario_id = u.id
ORDER BY rv.fecha_reserva DESC";
$stmt = $connect->query($sql);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas de Venta - PCM Team</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-container {
            padding: 30px 15px;
        }
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            margin-bottom: 30px;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn-back {
            background: white;
            color: #667eea;
            border: 2px solid white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #667eea;
            color: white;
        }
        .btn-nueva-reserva {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-nueva-reserva:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 147, 251, 0.4);
            color: white;
        }
        /* Estados de reserva */
        .badge-activa {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .badge-vencida {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .badge-completada {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .badge-cancelada {
            background: linear-gradient(135deg, #bdc3c7 0%, #2c3e50 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        /* Indicadores de tiempo */
        .tiempo-verde {
            color: #38ef7d;
            font-weight: 700;
        }
        .tiempo-amarillo {
            color: #f39c12;
            font-weight: 700;
        }
        .tiempo-rojo {
            color: #e74c3c;
            font-weight: 700;
        }
        .badge-grado-a {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-grado-b {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        table.dataTable tbody tr:hover {
            background-color: #f8f9fa !important;
        }
        .material-icons {
            vertical-align: middle;
        }
        .btn-action {
            padding: 5px 10px;
            font-size: 0.85rem;
            margin: 2px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="container-fluid">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>
                        <span class="material-icons" style="font-size: 2.5rem;">bookmark</span>
                        Reservas de Venta
                    </h1>
                    <div>
                        <a href="reserva_venta.php" class="btn btn-nueva-reserva me-2">
                            <span class="material-icons">add</span> Nueva Reserva
                        </a>
                        <button onclick="window.history.back()" class="btn btn-back">
                            <span class="material-icons">arrow_back</span> Volver
                        </button>
                    </div>
                </div>
            </div>
            <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <strong><?php echo $tipo_mensaje === 'success' ? '¡Éxito!' : '¡Error!'; ?></strong> <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            <div class="content-card">
                <div class="table-responsive">
                    <table id="tablaReservas" class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Fecha Reserva</th>
                                <th>Código Equipo</th>
                                <th>Equipo</th>
                                <th>Grado</th>
                                <th>Cliente</th>
                                <th>Comercial</th>
                                <th>Vencimiento</th>
                                <th>Tiempo</th>
                                <th>Estado</th>
                                <th>Precio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><strong>#<?php echo $reserva['id']; ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($reserva['fecha_reserva'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($reserva['codigo_general']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($reserva['marca']); ?>
                                    <?php echo htmlspecialchars($reserva['modelo']); ?>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($reserva['procesador']); ?> |
                                        <?php echo htmlspecialchars($reserva['ram']); ?> |
                                        <?php echo htmlspecialchars($reserva['disco']); ?> |
                                        <?php echo htmlspecialchars($reserva['pulgada']); ?>"
                                    </small>
                                </td>
                                <td>
                                    <?php if ($reserva['grado'] === 'A'): ?>
                                        <span class="badge-grado-a">A</span>
                                    <?php else: ?>
                                        <span class="badge-grado-b">B</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($reserva['cliente_nombre']); ?>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($reserva['cliente_documento']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($reserva['comercial_nombre']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($reserva['fecha_vencimiento'])); ?></td>
                                <td>
                                    <?php
                                    $dias_restantes = $reserva['dias_restantes'];
                                    if ($reserva['estado'] === 'activa') {
                                        if ($dias_restantes > 2) {
                                            $clase = 'tiempo-verde';
                                            $texto = "$dias_restantes días";
                                        } elseif ($dias_restantes >= 0) {
                                            $clase = 'tiempo-amarillo';
                                            $texto = $dias_restantes == 0 ? "Hoy" : "$dias_restantes días";
                                        } else {
                                            $clase = 'tiempo-rojo';
                                            $texto = "Vencida";
                                        }
                                        echo "<span class='$clase'>$texto</span>";
                                    } else {
                                        echo "<span class='text-muted'>-</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // Actualizar estado a vencida automáticamente
                                    $estado_display = $reserva['estado'];
                                    if ($reserva['estado'] === 'activa' && $dias_restantes < 0) {
                                        $estado_display = 'vencida';
                                    }
                                    $badge_class = 'badge-' . $estado_display;
                                    echo "<span class='$badge_class'>" . ucfirst($estado_display) . "</span>";
                                    ?>
                                </td>
                                <td><strong>$<?php echo number_format($reserva['precio'], 0, ',', '.'); ?></strong></td>
                                <td>
                                    <?php if ($reserva['estado'] === 'activa'): ?>
                                        <button class="btn btn-success btn-sm btn-action" onclick="completarReserva(<?php echo $reserva['id']; ?>)">
                                            <span class="material-icons" style="font-size: 14px;">check_circle</span>
                                            Completar
                                        </button>
                                        <?php
                                        // Verificar si ya fue extendida
                                        $ya_extendida = strpos($reserva['observaciones'], '[EXTENDIDA]') !== false;
                                        ?>
                                        <?php if (!$ya_extendida): ?>
                                        <button class="btn btn-warning btn-sm btn-action" onclick="extenderReserva(<?php echo $reserva['id']; ?>)">
                                            <span class="material-icons" style="font-size: 14px;">update</span>
                                            Extender
                                        </button>
                                        <?php endif; ?>
                                        <button class="btn btn-danger btn-sm btn-action" onclick="cancelarReserva(<?php echo $reserva['id']; ?>)">
                                            <span class="material-icons" style="font-size: 14px;">cancel</span>
                                            Cancelar
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                    <button class="btn btn-info btn-sm btn-action" onclick="verDetalles(<?php echo $reserva['id']; ?>)">
                                        <span class="material-icons" style="font-size: 14px;">info</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            $('#tablaReservas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true
            });
        });
        function completarReserva(id) {
            Swal.fire({
                title: '¿Completar reserva?',
                text: 'Esto marcará la reserva como completada',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, completar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="completar">
                        <input type="hidden" name="reserva_id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        function extenderReserva(id) {
            Swal.fire({
                title: '¿Extender reserva?',
                html: 'Se agregará <strong>5 días más</strong> a la reserva.<br><small class="text-muted">Solo se permite 1 extensión por reserva</small>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, extender',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="extender">
                        <input type="hidden" name="reserva_id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        function cancelarReserva(id) {
            Swal.fire({
                title: '¿Cancelar reserva?',
                text: 'El equipo será liberado y estará disponible nuevamente',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar reserva',
                cancelButtonText: 'No',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="cancelar">
                        <input type="hidden" name="reserva_id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        function verDetalles(id) {
            Swal.fire({
                title: 'Detalles de Reserva #' + id,
                text: 'Cargando información...',
                icon: 'info',
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#667eea'
            });
        }
    </script>
</body>
</html>
