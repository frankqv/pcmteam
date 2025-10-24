<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 4])) {
    header('location: ../error404.php');
    exit;
}
?>
<?php if (isset($_SESSION['id'])) { ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Nueva Venta - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .grado-badge {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            color: white;
        }
        .grado-A { background: #00CC54; }
        .grado-B { background: #F0DD00; color: #333; }
        .grado-C { background: #CC0618; }
        .producto-card {
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .producto-card:hover {
            border-color: #00CC54;
            box-shadow: 0 2px 8px rgba(0, 204, 84, 0.2);
        }
        .total-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .total-box .row > div {
            padding: 10px 0;
        }
        .total-box label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .total-final {
            font-size: 2rem;
            font-weight: bold;
            color: #00CC54;
        }
        .section-card {
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .section-header {
            background: linear-gradient(135deg, #2B6B5D 0%, #1a4a3f 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <div id="content">
            <div class='pre-loader'>
                <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
            </div>
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#">
                            <span class="material-icons" style="vertical-align: middle;">add_shopping_cart</span>
                            Nueva Venta
                        </a>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
                            data-toggle="collapse" data-target="#navbarSupportedContent">
                            <span class="material-icons">more_vert</span>
                        </button>
                        <div class="collapse navbar-collapse d-lg-block d-xl-block d-sm-none d-md-none d-none">
                            <ul class="nav navbar-nav ml-auto">
                                <li class="dropdown nav-item active">
                                    <a href="#" class="nav-link" data-toggle="dropdown">
                                        <img src="../assets/img/reere.webp">
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="../cuenta/perfil.php">Mi perfil</a></li>
                                        <li><a href="../cuenta/salir.php">Salir</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="main-content">
                <!-- Botón Volver -->
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="alistamiento_venta.php" class="btn btn-secondary">
                            <span class="material-icons" style="vertical-align: middle;">arrow_back</span>
                            Volver al Listado
                        </a>
                    </div>
                </div>
                <form id="formNuevaVenta">
                    <!-- SECCIÓN 1: CLIENTE -->
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">person</span>
                                1. Información del Cliente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="buscarCliente">Buscar Cliente *</label>
                                    <select id="buscarCliente" class="form-control" style="width: 100%;" required></select>
                                    <small class="text-muted">Buscar por NIT, nombre, apellido, correo o celular</small>
                                </div>
                                <div class="col-md-6">
                                    <label>Información del Cliente</label>
                                    <div id="infoCliente" class="alert alert-info" style="display: none;">
                                        <strong id="clienteNombre"></strong><br>
                                        <span id="clienteTelefono"></span> | <span id="clienteCanal"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN 2: INFORMACIÓN GENERAL -->
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">info</span>
                                2. Información General
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="txtSede">Sede *</label>
                                    <input type="text" id="txtSede" class="form-control" value="<?php echo $_SESSION['sede'] ?? 'Bogotá'; ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="txtTicket">Ticket *</label>
                                    <input type="text" id="txtTicket" class="form-control" placeholder="TKT-2025-001">
                                </div>
                                <div class="col-md-4">
                                    <label for="txtUbicacion">Direccion de Envío *</label>
                                    <input type="text" id="txtUbicacion" class="form-control" placeholder="Dirección de Envio" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN 3: PRODUCTOS -->
                    <div class="card section-card">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">inventory</span>
                                3. Productos
                            </h5>
                            <div>
                                <button type="button" class="btn btn-light btn-sm" id="btnBuscarInventario">
                                    <span class="material-icons" style="vertical-align: middle; font-size: 18px;">search</span>
                                    Buscar en Inventario
                                </button>
                                <button type="button" class="btn btn-light btn-sm" id="btnAgregarManual">
                                    <span class="material-icons" style="vertical-align: middle; font-size: 18px;">edit</span>
                                    Agregar Manual
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="listaItems">
                                <p class="text-muted text-center">No hay items agregados. Use los botones de arriba para agregar productos.</p>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN 4: INFORMACIÓN FINANCIERA -->
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">payments</span>
                                4. Información Financiera
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="total-box">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Subtotal</label>
                                        <h3 id="displaySubtotal" style="color: #333;">$0</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="txtDescuento">Descuento</label>
                                        <input type="number" id="txtDescuento" class="form-control form-control-lg" value="0" min="0" step="1000">
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Total a Pagar</label>
                                        <div class="total-final" id="displayTotal">$0</div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="txtAbono">Valor Abono</label>
                                        <input type="number" id="txtAbono" class="form-control form-control-lg" value="0" min="0" step="1000">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="txtMedioAbono">Medio de Pago Abono</label>
                                        <select id="txtMedioAbono" class="form-control form-control-lg">
                                            <option value="">Seleccione</option>
                                            <option value="efectivo">Efectivo</option>
                                            <option value="transferencia">Transferencia</option>
                                            <option value="tarjeta_credito">Tarjeta Crédito</option>
                                            <option value="tarjeta_debito">Tarjeta Débito</option>
                                            <option value="nequi">Nequi</option>
                                            <option value="daviplata">Daviplata</option>
                                            <option value="bancolombia">Bancolombia</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Saldo Pendiente</label>
                                        <h3 id="displaySaldo" style="color: #CC0618;">$0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN 5: OBSERVACIONES -->
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">notes</span>
                                5. Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="txtObservacion">Observación Global</label>
                                    <textarea id="txtObservacion" class="form-control" rows="4" placeholder="Notas generales de la venta..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- BOTONES DE ACCIÓN -->
                    <div class="row mb-4">
                        <div class="col-12 text-right">
                            <a href="alistamiento_venta.php" class="btn btn-secondary btn-lg">
                                <span class="material-icons" style="vertical-align: middle;">close</span>
                                Cancelar
                            </a>
                            <button type="button" class="btn btn-primary btn-lg" id="btnGuardarBorrador">
                                <span class="material-icons" style="vertical-align: middle;">save</span>
                                Guardar como Borrador
                            </button>
                            <button type="button" class="btn btn-success btn-lg" id="btnGuardarAprobar">
                                <span class="material-icons" style="vertical-align: middle;">check_circle</span>
                                Guardar y Aprobar
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="hiddenClienteId">
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Buscar Inventario -->
    <div class="modal fade" id="modalBuscarInventario" tabindex="-1">
        <div class="modal-dialog" style="max-width: 90%; width: 90%;">
            <div class="modal-content">
                <div class="modal-header" style="background: #2B6B5D; color: white;">
                    <h5 class="modal-title">
                        <span class="material-icons" style="vertical-align: middle;">search</span>
                        Buscar Productos en Inventario
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" id="txtBuscarInventario" class="form-control form-control-lg" placeholder="Buscar por marca, modelo, procesador, ram, disco, código...">
                    </div>
                    <div id="resultadosInventario" style="max-height: 500px; overflow-y: auto;">
                        <p class="text-muted text-center">Escriba para buscar productos...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Agregar Manual -->
    <div class="modal fade" id="modalAgregarManual" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #2B6B5D; color: white;">
                    <h5 class="modal-title">Agregar Producto Manual</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formProductoManual">
                        <div class="form-group">
                            <label for="txtManualProducto">Producto *</label>
                            <input type="text" id="txtManualProducto" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="txtManualMarca">Marca</label>
                                <input type="text" id="txtManualMarca" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="txtManualModelo">Modelo</label>
                                <input type="text" id="txtManualModelo" class="form-control">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="txtManualRam">RAM</label>
                                <input type="text" id="txtManualRam" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="txtManualDisco">Disco</label>
                                <input type="text" id="txtManualDisco" class="form-control">
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label for="txtManualDescripcion">Descripción</label>
                            <textarea id="txtManualDescripcion" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="txtManualCantidad">Cantidad *</label>
                                <input type="number" id="txtManualCantidad" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="txtManualPrecio">Precio Unitario *</label>
                                <input type="number" id="txtManualPrecio" class="form-control" min="0" step="1000" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnAgregarProductoManual">Agregar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/loader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/nueva_venta.js"></script>
    <script>
    $(document).ready(function() {
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });
        $('.more-button,.body-overlay').on('click', function() {
            $('#sidebar,.body-overlay').toggleClass('show-nav');
        });
    });
    </script>
</body>
</html>
<?php } else { header('Location: ../error404.php'); } ?>
<?php ob_end_flush(); ?>
