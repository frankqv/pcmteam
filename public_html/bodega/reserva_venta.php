<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../cuenta/login.php");
    exit;
}

// Verificar roles permitidos: Admin [1], Comercial [4]
$rol = $_SESSION['rol'] ?? 0;
if (!in_array($rol, [1, 4])) {
    header("Location: ../cuenta/sin_permiso.php");
    exit;


    
}

require_once('../../config/db.php');
date_default_timezone_set('America/Bogota');

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear_reserva') {
    try {
        $pdo->beginTransaction();

        $inventario_id = intval($_POST['inventario_id']);
        $cliente_id = intval($_POST['cliente_id']);
        $observaciones = trim($_POST['observaciones']);
        $dias_reserva = intval($_POST['dias_reserva']);

        // Validar días de reserva (máximo 7)
        if ($dias_reserva < 1 || $dias_reserva > 7) {
            throw new Exception("Los días de reserva deben estar entre 1 y 7");
        }

        // Calcular fecha de vencimiento
        $fecha_vencimiento = date('Y-m-d', strtotime("+$dias_reserva days"));

        // Verificar que el equipo esté disponible para reserva
        $stmt = $pdo->prepare("SELECT id, disposicion FROM bodega_inventario WHERE id = :id AND disposicion = 'Para Venta'");
        $stmt->execute([':id' => $inventario_id]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$equipo) {
            throw new Exception("El equipo no está disponible para reserva");
        }

        // Crear reserva
        $stmt = $pdo->prepare("INSERT INTO reserva_venta (
            inventario_id, usuario_id, cliente_id, fecha_vencimiento, observaciones, estado
        ) VALUES (
            :inventario_id, :usuario_id, :cliente_id, :fecha_vencimiento, :observaciones, 'activa'
        )");

        $stmt->execute([
            ':inventario_id' => $inventario_id,
            ':usuario_id' => $_SESSION['usuario_id'],
            ':cliente_id' => $cliente_id,
            ':fecha_vencimiento' => $fecha_vencimiento,
            ':observaciones' => $observaciones
        ]);

        $reserva_id = $pdo->lastInsertId();

        // Actualizar disposición del equipo
        $stmt = $pdo->prepare("UPDATE bodega_inventario SET disposicion = 'Reservado', pedido_id = :reserva_id WHERE id = :id");
        $stmt->execute([
            ':reserva_id' => $reserva_id,
            ':id' => $inventario_id
        ]);

        $pdo->commit();

        $mensaje = "Reserva creada exitosamente";
        $tipo_mensaje = "success";
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
}

// Obtener clientes
$stmt = $pdo->query("SELECT id, nombre, documento, telefono FROM clientes WHERE estado = 'activo' ORDER BY nombre ASC");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener equipos disponibles para venta
$stmt = $pdo->query("SELECT
    id, codigo_general, serial, marca, modelo, procesador, ram, disco, pulgada, grado, precio
FROM bodega_inventario
WHERE disposicion = 'Para Venta' AND estado = 'activo'
ORDER BY fecha_entrada DESC");
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Reserva de Venta - PCM Team</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
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
            max-width: 1200px;
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
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-back {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #667eea;
            color: white;
        }
        .badge-grado-a {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        .badge-grado-b {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        .equipo-item {
            padding: 10px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .equipo-item:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }
        .equipo-item.selected {
            border-color: #667eea;
            background: #e7f1ff;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .material-icons {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-card">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>
                        <span class="material-icons" style="font-size: 2rem;">bookmark_add</span>
                        Crear Reserva de Venta
                    </h1>
                    <button onclick="window.history.back()" class="btn btn-back">
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

            <form method="POST" id="reservaForm">
                <input type="hidden" name="action" value="crear_reserva">

                <div class="row">
                    <!-- Cliente -->
                    <div class="col-md-6 mb-3">
                        <label for="cliente_id" class="form-label required-field">Cliente</label>
                        <select name="cliente_id" id="cliente_id" class="form-select" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['id']; ?>">
                                <?php echo htmlspecialchars($cliente['nombre']); ?>
                                (<?php echo htmlspecialchars($cliente['documento']); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Días de Reserva -->
                    <div class="col-md-6 mb-3">
                        <label for="dias_reserva" class="form-label required-field">Días de Reserva</label>
                        <select name="dias_reserva" id="dias_reserva" class="form-select" required>
                            <option value="1">1 día</option>
                            <option value="2">2 días</option>
                            <option value="3">3 días</option>
                            <option value="4">4 días</option>
                            <option value="5" selected>5 días</option>
                            <option value="6">6 días</option>
                            <option value="7">7 días (máximo)</option>
                        </select>
                        <small class="text-muted">Máximo: 7 días</small>
                    </div>

                    <!-- Equipo -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Seleccionar Equipo</label>
                        <input type="hidden" name="inventario_id" id="inventario_id" required>
                        <input type="text" id="search_equipo" class="form-control mb-3" placeholder="Buscar por código, serial, marca, modelo...">

                        <div id="equipos_list" style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($equipos as $equipo): ?>
                            <div class="equipo-item" data-id="<?php echo $equipo['id']; ?>"
                                 data-search="<?php echo strtolower($equipo['codigo_general'] . ' ' . $equipo['serial'] . ' ' . $equipo['marca'] . ' ' . $equipo['modelo']); ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($equipo['codigo_general']); ?></strong> -
                                        <?php echo htmlspecialchars($equipo['marca']); ?>
                                        <?php echo htmlspecialchars($equipo['modelo']); ?>
                                        <br>
                                        <small class="text-muted">
                                            Serial: <?php echo htmlspecialchars($equipo['serial']); ?> |
                                            <?php echo htmlspecialchars($equipo['procesador']); ?> |
                                            <?php echo htmlspecialchars($equipo['ram']); ?> RAM |
                                            <?php echo htmlspecialchars($equipo['disco']); ?> |
                                            <?php echo htmlspecialchars($equipo['pulgada']); ?>"
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <?php if ($equipo['grado'] === 'A'): ?>
                                            <span class="badge-grado-a">Grado A</span>
                                        <?php else: ?>
                                            <span class="badge-grado-b">Grado B</span>
                                        <?php endif; ?>
                                        <div class="mt-1">
                                            <strong>$<?php echo number_format($equipo['precio'], 0, ',', '.'); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-md-12 mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="3"
                                  placeholder="Detalles adicionales sobre la reserva..."></textarea>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-submit btn-lg" id="btnSubmit" disabled>
                        <span class="material-icons">save</span> Crear Reserva
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('#cliente_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Seleccionar cliente...',
                allowClear: true
            });

            // Búsqueda de equipos
            $('#search_equipo').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.equipo-item').each(function() {
                    const searchData = $(this).data('search');
                    if (searchData.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Seleccionar equipo
            $('.equipo-item').on('click', function() {
                $('.equipo-item').removeClass('selected');
                $(this).addClass('selected');
                const equipoId = $(this).data('id');
                $('#inventario_id').val(equipoId);
                validateForm();
            });

            // Validar formulario
            function validateForm() {
                const clienteId = $('#cliente_id').val();
                const inventarioId = $('#inventario_id').val();

                if (clienteId && inventarioId) {
                    $('#btnSubmit').prop('disabled', false);
                } else {
                    $('#btnSubmit').prop('disabled', true);
                }
            }

            $('#cliente_id').on('change', validateForm);

            // Confirmar antes de enviar
            $('#reservaForm').on('submit', function(e) {
                e.preventDefault();

                const diasReserva = $('#dias_reserva option:selected').text();

                Swal.fire({
                    title: '¿Confirmar reserva?',
                    html: `Se creará una reserva por <strong>${diasReserva}</strong>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, crear reserva',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
