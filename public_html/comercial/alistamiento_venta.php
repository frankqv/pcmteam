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
    <title>Alistamiento de Ventas - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
      /* === BADGES DE ESTADO === */
      .badge {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 600;
      }
      .badge-borrador {
        background: #6c757d;
        color: white;
      }
      .badge-pendiente {
        background: #CC0618;
        color: white;
      }
      .badge-aprobado {
        background: #2B41CC;
        color: white;
      }
      .badge-en_alistamiento {
        background: #F0DD00;
        color: #333;
      }
      .badge-alistado {
        background: #00CC54;
        color: white;
      }
      .badge-despachado {
        background: #7B2CBF;
        color: white;
      }
      .badge-en_transito {
        background: #17a2b8;
        color: white;
      }
      .badge-entregado {
        background: #28a745;
        color: white;
      }
      .badge-cancelado {
        background: #dc3545;
        color: white;
      }
      /* === BOTONES === */
      .btn-nueva-venta {
        background: linear-gradient(135deg, #2B6B5D 0%, #1a4a3f 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(43, 107, 93, 0.3);
      }
      .btn-nueva-venta:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 107, 93, 0.4);
        color: white;
      }
      .btn-nueva-venta .material-icons {
        vertical-align: middle;
        margin-right: 5px;
      }
      /* === CARD PRINCIPAL === */
      .card-listado {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: none;
      }
      .card-listado .card-header {
        background: linear-gradient(135deg, #2B6B5D 0%, #1a4a3f 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
      }
      .card-listado .card-header h4 {
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
      }
      .card-listado .card-header h4 .material-icons {
        margin-right: 10px;
      }
      /* === TABLA === */
      #tablaVentas {
        font-size: 14px;
      }
      #tablaVentas thead th {
        background: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
      }
      #tablaVentas tbody td {
        vertical-align: middle;
      }
      /* === BOTONES DE ACCIÓN === */
      .btn-action {
        padding: 6px 10px;
        border-radius: 6px;
        border: none;
        margin: 0 2px;
        transition: all 0.2s;
      }
      .btn-action:hover {
        transform: scale(1.1);
      }
      .btn-action .material-icons {
        font-size: 18px;
        vertical-align: middle;
      }
      /* === MODAL DETALLE === */
      .modal-detalle .info-section {
        margin-bottom: 20px;
      }
      .modal-detalle .info-section h6 {
        color: #2B6B5D;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #2B6B5D;
      }
      .modal-detalle .info-table th {
        width: 40%;
        font-weight: 600;
        color: #495057;
      }
      .modal-detalle .info-table td {
        color: #212529;
      }
      .modal-detalle .productos-table {
        font-size: 13px;
      }
      .modal-detalle .productos-table thead {
        background: #f8f9fa;
      }
      /* === MODAL CAMBIAR ESTADO === */
      .swal2-html-container .form-control {
        margin-bottom: 10px;
      }
    </style>
  </head>
  <body>
    <div class="wrapper">
      <div class="body-overlay"></div>
      <?php
      include_once '../layouts/nav.php';
      include_once '../layouts/menu_data.php';
      ?>
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
                <span class="material-icons" style="vertical-align: middle;">shopping_cart</span>
                Alistamiento de Ventas
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
          <!-- BOTÓN NUEVA VENTA -->
          <div class="row mb-4">
            <div class="col-12">
              <a href="nueva_venta.php" class="btn btn-nueva-venta">
                <span class="material-icons">add_shopping_cart</span>
                Nueva Venta
              </a>
            </div>
          </div>
          <!-- TABLA DE VENTAS -->
          <div class="row">
            <div class="col-12">
              <div class="card card-listado">
                <div class="card-header">
                  <h4>
                    <span class="material-icons">list_alt</span>
                    Listado de Ventas
                  </h4>
                </div>
                <div class="card-body">
                  <table id="tablaVentas" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>ID Venta</th>
                        <th>Ticket</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Abono</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Se carga dinámicamente con DataTables -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ==================== MODAL: VER DETALLE ==================== -->
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" role="dialog">
      <div class="modal-dialog" style="max-width: 95%; width: 95%;" role="document">
        <div class="modal-content">
          <div class="modal-header" style="background: #2B6B5D; color: white;">
            <h5 class="modal-title">
              <span class="material-icons" style="vertical-align: middle;">visibility</span>
              Detalle de Venta
            </h5>
            <button type="button" class="close" data-dismiss="modal" style="color: white;">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body modal-detalle" id="detalleVentaBody">
            <!-- Se carga dinámicamente -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- ==================== SCRIPTS ==================== -->
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/loader.js"></script>
    <script type="text/javascript" src="../assets/js/datatable.js"></script>
    <script type="text/javascript" src="../assets/js/datatablebuttons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      $(document).ready(function() {
        console.log('🔵 Alistamiento Venta - Inicializando...');
        // ==================== DATATABLE ====================
        const tablaVentas = $('#tablaVentas').DataTable({
          ajax: {
            url: '../../backend/php/alistamiento_api.php?action=listar_ventas',
            dataSrc: 'data'
          },
          columns: [{
              data: 'idventa'
            },
            {
              data: 'ticket'
            },
            {
              data: 'cliente'
            },
            {
              data: 'fecha_venta',
              render: function(data) {
                return new Date(data).toLocaleDateString('es-CO', {
                  year: 'numeric',
                  month: '2-digit',
                  day: '2-digit'
                });
              }
            },
            {
              data: 'total_venta',
              render: function(data) {
                return '$' + parseFloat(data).toLocaleString('es-CO', {
                  minimumFractionDigits: 0,
                  maximumFractionDigits: 0
                });
              }
            },
            {
              data: 'valor_abono',
              render: function(data) {
                return '$' + parseFloat(data).toLocaleString('es-CO', {
                  minimumFractionDigits: 0,
                  maximumFractionDigits: 0
                });
              }
            },
            {
              data: 'saldo',
              render: function(data) {
                const saldo = parseFloat(data);
                const color = saldo > 0 ? '#CC0618' : '#00CC54';
                return '<strong style="color: ' + color + ';">$' + saldo.toLocaleString('es-CO', {
                  minimumFractionDigits: 0,
                  maximumFractionDigits: 0
                }) + '</strong>';
              }
            },
            {
              data: 'estado',
              render: function(data) {
                const estados = {
                  'borrador': 'badge-borrador',
                  'pendiente': 'badge-pendiente',
                  'aprobado': 'badge-aprobado',
                  'en_alistamiento': 'badge-en_alistamiento',
                  'alistado': 'badge-alistado',
                  'despachado': 'badge-despachado',
                  'en_transito': 'badge-en_transito',
                  'entregado': 'badge-entregado',
                  'cancelado': 'badge-cancelado'
                };
                const estadoTexto = data.replace(/_/g, ' ').toUpperCase();
                return '<span class="badge ' + (estados[data] || 'badge-secondary') + '">' + estadoTexto + '</span>';
              }
            },
            {
              data: null,
              orderable: false,
              render: function(data, type, row) {
                return `
                  <button class="btn btn-info btn-sm btn-action btnVerDetalle" data-id="${row.id}" title="Ver Detalle">
                    <span class="material-icons">visibility</span>
                  </button>
                  <button class="btn btn-warning btn-sm btn-action btnCambiarEstado" data-id="${row.id}" title="Cambiar Estado">
                    <span class="material-icons">sync</span>
                  </button>
                  <button class="btn btn-danger btn-sm btn-action btnEliminar" data-id="${row.id}" title="Eliminar">
                    <span class="material-icons">delete</span>
                  </button>
                `;
              }
            }
          ],
          language: {
            url: '../assets/js/spanish.json'
          },
          order: [
            [3, 'desc']
          ], // Ordenar por fecha descendente
          responsive: true,
          pageLength: 25
        });
        console.log('✅ DataTable inicializado');
        // ==================== VER DETALLE ====================
        $(document).on('click', '.btnVerDetalle', function() {
          const id = $(this).data('id');
          console.log('📋 Cargando detalle de venta ID:', id);
          $.get('../../backend/php/alistamiento_api.php?action=obtener_venta&id=' + id, function(response) {
            if (response.success) {
              const venta = response.data;
              console.log('✅ Venta cargada:', venta);
              let html = `
                <div class="row">
                  <!-- COLUMNA IZQUIERDA: INFORMACIÓN GENERAL -->
                  <div class="col-md-6">
                    <div class="info-section">
                      <h6><span class="material-icons" style="vertical-align: middle; font-size: 20px;">info</span> Información General</h6>
                      <table class="table table-sm info-table">
                        <tr><th>ID Venta:</th><td><strong>${venta.idventa}</strong></td></tr>
                        <tr><th>Ticket:</th><td>${venta.ticket}</td></tr>
                        <tr><th>Cliente:</th><td>${venta.cliente}</td></tr>
                        <tr><th>Teléfono:</th><td>${venta.telefono_cliente || 'N/A'}</td></tr>
                        <tr><th>Solicitante:</th><td>${venta.solicitante || 'N/A'}</td></tr>
                        <tr><th>Sede:</th><td>${venta.sede || 'N/A'}</td></tr>
                        <tr><th>Ubicación:</th><td>${venta.ubicacion || 'N/A'}</td></tr>
                        <tr><th>Fecha:</th><td>${new Date(venta.fecha_venta).toLocaleDateString('es-CO')}</td></tr>
                        <tr><th>Estado:</th><td><span class="badge badge-${venta.estado}">${venta.estado.replace(/_/g, ' ').toUpperCase()}</span></td></tr>
                      </table>
                    </div>
                  </div>
                  <!-- COLUMNA DERECHA: INFORMACIÓN FINANCIERA -->
                  <div class="col-md-6">
                    <div class="info-section">
                      <h6><span class="material-icons" style="vertical-align: middle; font-size: 20px;">payments</span> Información Financiera</h6>
                      <table class="table table-sm info-table">
                        <tr><th>Subtotal:</th><td>$${parseFloat(venta.subtotal).toLocaleString('es-CO')}</td></tr>
                        <tr><th>Descuento:</th><td>$${parseFloat(venta.descuento).toLocaleString('es-CO')}</td></tr>
                        <tr style="border-top: 2px solid #2B6B5D;"><th>Total:</th><td><strong style="font-size: 18px; color: #2B6B5D;">$${parseFloat(venta.total_venta).toLocaleString('es-CO')}</strong></td></tr>
                        <tr><th>Abono:</th><td>$${parseFloat(venta.valor_abono).toLocaleString('es-CO')}</td></tr>
                        <tr><th>Medio Abono:</th><td>${venta.medio_abono || 'N/A'}</td></tr>
                        <tr style="border-top: 2px solid #CC0618;"><th>Saldo:</th><td><strong style="font-size: 18px; color: ${parseFloat(venta.saldo) > 0 ? '#CC0618' : '#00CC54'};">$${parseFloat(venta.saldo).toLocaleString('es-CO')}</strong></td></tr>
                      </table>
                    </div>
                  </div>
                </div>
                <hr style="margin: 30px 0; border-top: 2px solid #dee2e6;">
                <!-- PRODUCTOS -->
                <div class="info-section">
                  <h6><span class="material-icons" style="vertical-align: middle; font-size: 20px;">inventory</span> Productos</h6>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered productos-table">
                      <thead>
                        <tr style="background: #f8f9fa;">
                          <th width="5%">#</th>
                          <th width="45%">Producto</th>
                          <th width="10%">Cantidad</th>
                          <th width="20%">Precio Unit.</th>
                          <th width="20%">Subtotal</th>
                        </tr>
                      </thead>
                      <tbody>
              `;
              venta.items.forEach(function(item) {
                html += `
                  <tr>
                    <td class="text-center">${item.item_numero}</td>
                    <td>
                      <strong>${item.producto}</strong>
                      ${item.marca ? '<br><small>' + item.marca + ' ' + (item.modelo || '') + '</small>' : ''}
                      ${item.ram || item.disco ? '<br><small class="text-muted">' + (item.ram || '') + ' | ' + (item.disco || '') + '</small>' : ''}
                      ${item.codigo_g ? '<br><small class="text-muted"><strong>Código:</strong> ' + item.codigo_g + '</small>' : ''}
                    </td>
                    <td class="text-center">${item.cantidad}</td>
                    <td class="text-right">$${parseFloat(item.precio_unitario).toLocaleString('es-CO')}</td>
                    <td class="text-right"><strong>$${parseFloat(item.subtotal).toLocaleString('es-CO')}</strong></td>
                  </tr>
                `;
              });
              html += `
                      </tbody>
                    </table>
                  </div>
                </div>
              `;
              // OBSERVACIONES
              if (venta.observacion_global) {
                html += `
                  <hr style="margin: 20px 0;">
                  <div class="info-section">
                    <h6><span class="material-icons" style="vertical-align: middle; font-size: 20px;">notes</span> Observaciones</h6>
                    <div class="alert alert-info">${venta.observacion_global}</div>
                  </div>
                `;
              }
              $('#detalleVentaBody').html(html);
              $('#modalVerDetalle').modal('show');
            } else {
              Swal.fire('Error', response.message, 'error');
            }
          }).fail(function() {
            Swal.fire('Error', 'Error al cargar el detalle de la venta', 'error');
          });
        });
        // ==================== CAMBIAR ESTADO ====================
        $(document).on('click', '.btnCambiarEstado', function() {
          const id = $(this).data('id');
          console.log('🔄 Cambiar estado de venta ID:', id);
          Swal.fire({
            title: 'Cambiar Estado',
            html: `
              <div style="text-align: left; margin-top: 20px;">
                <label for="swalEstado" style="font-weight: 600; margin-bottom: 8px; display: block;">Nuevo Estado:</label>
                <select id="swalEstado" class="form-control" style="margin-bottom: 15px;">
                  <option value="borrador">Borrador</option>
                  <option value="pendiente">Pendiente</option>
                  <option value="aprobado">Aprobado</option>
                  <option value="en_alistamiento">En Alistamiento</option>
                  <option value="alistado">Alistado</option>
                  <option value="despachado">Despachado</option>
                  <option value="en_transito">En Tránsito</option>
                  <option value="entregado">Entregado</option>
                  <option value="cancelado">Cancelado</option>
                </select>
                <label for="swalObservacion" style="font-weight: 600; margin-bottom: 8px; display: block;">Observación:</label>
                <textarea id="swalObservacion" class="form-control" rows="3" placeholder="Ingrese una observación sobre el cambio de estado..."></textarea>
              </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Cambiar Estado',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2B6B5D',
            cancelButtonColor: '#6c757d',
            width: '600px',
            preConfirm: () => {
              const estado = $('#swalEstado').val();
              const observacion = $('#swalObservacion').val();
              if (!estado) {
                Swal.showValidationMessage('Debe seleccionar un estado');
                return false;
              }
              return {
                estado: estado,
                observacion: observacion
              };
            }
          }).then((result) => {
            if (result.isConfirmed) {
              console.log('✅ Cambiando estado a:', result.value.estado);
              $.post('../../backend/php/alistamiento_api.php', {
                action: 'cambiar_estado',
                id: id,
                estado: result.value.estado,
                observacion: result.value.observacion
              }, function(response) {
                if (response.success) {
                  Swal.fire('Éxito', response.message, 'success');
                  tablaVentas.ajax.reload(null, false); // Recargar sin resetear paginación
                } else {
                  Swal.fire('Error', response.message, 'error');
                }
              }).fail(function() {
                Swal.fire('Error', 'Error al cambiar el estado', 'error');
              });
            }
          });
        });
        // ==================== ELIMINAR VENTA ====================
        $(document).on('click', '.btnEliminar', function() {
          const id = $(this).data('id');
          console.log('🗑️ Intentando eliminar venta ID:', id);
          Swal.fire({
            title: '¿Está seguro?',
            html: '<p>Solo se pueden eliminar ventas en estado <strong>Borrador</strong> o <strong>Cancelado</strong>.</p><p class="text-danger">Esta acción no se puede deshacer.</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
          }).then((result) => {
            if (result.isConfirmed) {
              console.log('✅ Confirmado - Eliminando venta ID:', id);
              $.post('../../backend/php/alistamiento_api.php', {
                action: 'eliminar_venta',
                id: id
              }, function(response) {
                if (response.success) {
                  Swal.fire('Eliminado', response.message, 'success');
                  tablaVentas.ajax.reload();
                } else {
                  Swal.fire('Error', response.message, 'error');
                }
              }).fail(function() {
                Swal.fire('Error', 'Error al eliminar la venta', 'error');
              });
            }
          });
        });
        // ==================== SIDEBAR ====================
        $('#sidebarCollapse').on('click', function() {
          $('#sidebar').toggleClass('active');
          $('#content').toggleClass('active');
        });
        $('.more-button,.body-overlay').on('click', function() {
          $('#sidebar,.body-overlay').toggleClass('show-nav');
        });
        console.log('✅ Alistamiento Venta - Inicialización completa');
      });
    </script>
  </body>
  </html>
<?php } else {
  header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>
