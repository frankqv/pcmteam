<?php
// public_html/bodega/preventa.php
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

// Procesar acción de apartar equipo para solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apartar_equipo') {
    try {
        $connect->beginTransaction();

        $solicitud_id = intval($_POST['solicitud_id']);
        $inventario_id = intval($_POST['inventario_id']);
        $cliente_id = intval($_POST['cliente_id']);

        // Verificar que el equipo esté disponible
        $stmt = $connect->prepare("SELECT id, disposicion, grado FROM bodega_inventario WHERE id = :id AND disposicion = 'Para Venta' AND grado IN ('A', 'B')");
        $stmt->execute([':id' => $inventario_id]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$equipo) {
            throw new Exception("El equipo no está disponible para reserva o no es grado A/B");
        }

        // Calcular fecha de vencimiento (5 días)
        $fecha_vencimiento = date('Y-m-d', strtotime('+5 days'));

        // Crear reserva vinculada a la solicitud
        $stmt = $connect->prepare("INSERT INTO reserva_venta (
            inventario_id, usuario_id, cliente_id, fecha_vencimiento, observaciones, estado
        ) VALUES (
            :inventario_id, :usuario_id, :cliente_id, :fecha_vencimiento, :observaciones, 'activa'
        )");

        $observaciones = "[APARTADO DESDE PREVENTA] Para solicitud #" . $solicitud_id;

        $stmt->execute([
            ':inventario_id' => $inventario_id,
            ':usuario_id' => $_SESSION['usuario_id'],
            ':cliente_id' => $cliente_id,
            ':fecha_vencimiento' => $fecha_vencimiento,
            ':observaciones' => $observaciones
        ]);

        $reserva_id = $connect->lastInsertId();

        // Actualizar disposición del equipo
        $stmt = $connect->prepare("UPDATE bodega_inventario SET disposicion = 'Reservado', pedido_id = :reserva_id WHERE id = :id");
        $stmt->execute([
            ':reserva_id' => $reserva_id,
            ':id' => $inventario_id
        ]);

        // Actualizar estado de la solicitud a "en proceso"
        $stmt = $connect->prepare("UPDATE solicitud_alistamiento SET estado = 'en proceso' WHERE id = :id");
        $stmt->execute([':id' => $solicitud_id]);

        $connect->commit();
        $mensaje = "Equipo apartado exitosamente. Reserva válida por 5 días.";
        $tipo_mensaje = "success";

    } catch (Exception $e) {
        $connect->rollBack();
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
}

// Obtener solicitudes pendientes
$sql_solicitudes = "SELECT
    sa.*,
    u.nombre as solicitante_nombre
FROM solicitud_alistamiento sa
INNER JOIN usuarios u ON sa.usuario_id = u.id
WHERE sa.estado = 'pendiente'
ORDER BY sa.fecha_solicitud DESC";

$stmt_sol = $connect->query($sql_solicitudes);
$solicitudes = $stmt_sol->fetchAll(PDO::FETCH_ASSOC);

// Obtener clientes para el modal
$stmt_clientes = $connect->query("SELECT idclie as id, CONCAT(nomcli, ' ', apecli) as nombre FROM clientes WHERE estad = 'Activo' ORDER BY nomcli ASC");
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Venta - PCM Team</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px 15px;
        }
        .main-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
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
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .solicitud-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .solicitud-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        .equipo-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            transition: all 0.3s;
        }
        .equipo-card:hover {
            background: #e7f1ff;
            border-color: #667eea;
        }
        .badge-grado-a {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .badge-grado-b {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .btn-apartar {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border: none;
            padding: 8px 15px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-apartar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-card">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>
                        <span class="material-icons" style="font-size: 2rem;">request_quote</span>
                        Pre-Venta - Solicitudes Pendientes
                    </h1>
                    <button onclick="window.history.back()" class="btn btn-light">
                        <span class="material-icons">arrow_back</span> Volver
                    </button>
                </div>
            </div>

            <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <strong><?php echo $tipo_mensaje === 'success' ? '¡Éxito!' : '¡Error!'; ?></strong> <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (count($solicitudes) > 0): ?>
                <?php foreach ($solicitudes as $solicitud): ?>
                <div class="solicitud-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="mb-1">Solicitud #<?php echo $solicitud['id']; ?></h4>
                            <p class="mb-0 text-muted">
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">person</span>
                                Solicitado por: <strong><?php echo htmlspecialchars($solicitud['solicitante_nombre']); ?></strong>
                            </p>
                            <p class="mb-0 text-muted">
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">access_time</span>
                                <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?>
                            </p>
                        </div>
                        <span class="badge bg-warning text-dark">Pendiente</span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Cliente:</strong> <?php echo htmlspecialchars($solicitud['cliente']); ?></p>
                            <p class="mb-1"><strong>Cantidad:</strong> <?php echo htmlspecialchars($solicitud['cantidad']); ?></p>
                            <p class="mb-0"><strong>Sede:</strong> <?php echo htmlspecialchars($solicitud['sede']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <?php if ($solicitud['marca']): ?>
                            <p class="mb-1"><strong>Marca:</strong> <?php echo htmlspecialchars($solicitud['marca']); ?></p>
                            <?php endif; ?>
                            <?php if ($solicitud['modelo']): ?>
                            <p class="mb-1"><strong>Modelo:</strong> <?php echo htmlspecialchars($solicitud['modelo']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Descripción:</strong></p>
                            <p class="text-muted small"><?php echo nl2br(htmlspecialchars(substr($solicitud['descripcion'], 0, 100))); ?>...</p>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Equipos Sugeridos (Grado A y B)</h5>
                        <button class="btn btn-sm btn-primary" onclick="buscarEquipos(<?php echo $solicitud['id']; ?>, '<?php echo htmlspecialchars($solicitud['marca'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($solicitud['modelo'] ?? '', ENT_QUOTES); ?>')">
                            <span class="material-icons" style="font-size: 14px;">search</span>
                            Buscar Equipos
                        </button>
                    </div>

                    <div id="equipos-solicitud-<?php echo $solicitud['id']; ?>" class="mt-3">
                        <!-- Los equipos se cargarán aquí vía AJAX -->
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="alert alert-info text-center">
                <h5><span class="material-icons" style="vertical-align: middle;">info</span> No hay solicitudes pendientes</h5>
                <p>Cuando se generen nuevas solicitudes de alistamiento, aparecerán aquí para que puedas apartarlas.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Apartar Equipo -->
    <div class="modal fade" id="apartarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apartar Equipo para Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formApartar">
                    <input type="hidden" name="action" value="apartar_equipo">
                    <input type="hidden" name="solicitud_id" id="modal_solicitud_id">
                    <input type="hidden" name="inventario_id" id="modal_inventario_id">
                    <div class="modal-body">
                        <div id="equipo-info" class="mb-3"></div>
                        <div class="form-group">
                            <label>Cliente <span class="text-danger">*</span></label>
                            <select name="cliente_id" id="modal_cliente_id" class="form-select" required>
                                <option value="">Seleccionar cliente...</option>
                                <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Reserva válida por 5 días. Puede extenderse 1 vez por 5 días adicionales.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-apartar">
                            <span class="material-icons" style="font-size: 16px; vertical-align: middle;">bookmark_add</span>
                            Apartar Equipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function buscarEquipos(solicitudId, marca, modelo) {
            const container = $(`#equipos-solicitud-${solicitudId}`);
            container.html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p>Buscando equipos...</p></div>');

            $.ajax({
                url: '../../backend/php/buscar_equipos_preventa.php',
                type: 'POST',
                data: {
                    marca: marca,
                    modelo: modelo,
                    solicitud_id: solicitudId
                },
                success: function(response) {
                    if (response.equipos && response.equipos.length > 0) {
                        let html = '';
                        response.equipos.forEach(eq => {
                            const gradoBadge = eq.grado === 'A' ?
                                '<span class="badge-grado-a">Grado A</span>' :
                                '<span class="badge-grado-b">Grado B</span>';

                            html += `
                                <div class="equipo-card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>${eq.codigo_general}</strong> - ${eq.marca} ${eq.modelo}
                                            <br>
                                            <small class="text-muted">
                                                ${eq.procesador} | ${eq.ram} RAM | ${eq.disco} | ${eq.pulgada}"
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            ${gradoBadge}
                                            <div class="mt-1"><strong>$${Number(eq.precio).toLocaleString()}</strong></div>
                                            <button class="btn btn-apartar btn-sm mt-2" onclick="abrirModalApartar(${solicitudId}, ${eq.id}, '${eq.codigo_general}', '${eq.marca} ${eq.modelo}', '${eq.precio}')">
                                                <span class="material-icons" style="font-size: 14px;">bookmark_add</span>
                                                Apartar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        container.html(html);
                    } else {
                        container.html('<div class="alert alert-warning">No se encontraron equipos disponibles con esas características.</div>');
                    }
                },
                error: function() {
                    container.html('<div class="alert alert-danger">Error al buscar equipos. Intente nuevamente.</div>');
                }
            });
        }

        function abrirModalApartar(solicitudId, inventarioId, codigo, nombre, precio) {
            $('#modal_solicitud_id').val(solicitudId);
            $('#modal_inventario_id').val(inventarioId);
            $('#equipo-info').html(`
                <div class="alert alert-info">
                    <strong>${codigo}</strong> - ${nombre}<br>
                    <strong>Precio:</strong> $${Number(precio).toLocaleString()}
                </div>
            `);
            new bootstrap.Modal(document.getElementById('apartarModal')).show();
        }

        $('#formApartar').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: '¿Apartar este equipo?',
                text: 'Se creará una reserva de 5 días para este equipo',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, apartar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#11998e',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
</body>
</html>
