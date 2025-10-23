/**
 * ALISTAMIENTO DE VENTAS - JavaScript
 * Maneja toda la lógica de UI y comunicación con API
 */
let itemsVenta = [];
let itemCounter = 0;
$(document).ready(function () {
  // ========== INICIALIZAR DATATABLE ==========
  const tablaVentas = $('#tablaVentas').DataTable({
    ajax: {
      url: '../../backend/php/alistamiento_api.php?action=listar_ventas',
      dataSrc: 'data'
    },
    columns: [
      { data: 'idventa' },
      { data: 'ticket' },
      { data: 'cliente' },
      {
        data: 'fecha_venta',
        render: function (data) {
          return new Date(data).toLocaleDateString('es-CO');
        }
      },
      {
        data: 'total_venta',
        render: function (data) {
          return '$' + parseFloat(data).toLocaleString('es-CO');
        }
      },
      {
        data: 'valor_abono',
        render: function (data) {
          return '$' + parseFloat(data).toLocaleString('es-CO');
        }
      },
      {
        data: 'saldo',
        render: function (data) {
          return '$' + parseFloat(data).toLocaleString('es-CO');
        }
      },
      {
        data: 'estado',
        render: function (data) {
          const badges = {
            'borrador': 'badge-borrador',
            'pendiente': 'badge-pendiente',
            'aprobado': 'badge-aprobado',
            'en_alistamiento': 'badge-en_alistamiento',
            'alistado': 'badge-alistado',
            'despachado': 'badge-despachado',
            'entregado': 'badge-entregado',
            'cancelado': 'badge-cancelado'
          };
          return '<span class="badge ' + badges[data] + '">' + data.replace('_', ' ') + '</span>';
        }
      },
      {
        data: null,
        render: function (data, type, row) {
          return `
                        <button class="btn btn-sm btn-info btnVerDetalle" data-id="${row.id}" title="Ver">
                            <i class="material-icons" style="font-size: 18px;">visibility</i>
                        </button>
                        <button class="btn btn-sm btn-warning btnCambiarEstado" data-id="${row.id}" title="Cambiar Estado">
                            <i class="material-icons" style="font-size: 18px;">sync</i>
                        </button>
                        <button class="btn btn-sm btn-danger btnEliminar" data-id="${row.id}" title="Eliminar">
                            <i class="material-icons" style="font-size: 18px;">delete</i>
                        </button>
                    `;
        }
      }
    ],
    language: {
      url: '../assets/js/spanish.json'
    },
    order: [[3, 'desc']]
  });
  // ========== INICIALIZAR SELECT2 CLIENTES ==========
  // IMPORTANTE: Inicializar cuando se abre el modal
  $('#modalNuevaVenta').on('shown.bs.modal', function () {
    console.log('🔵 Modal Nueva Venta abierto');
    // Destruir Select2 si ya existe
    if ($('#buscarCliente').hasClass('select2-hidden-accessible')) {
      $('#buscarCliente').select2('destroy');
    }
    // Inicializar Select2
    $('#buscarCliente').select2({
      placeholder: 'Buscar cliente por nombre, NIT, correo o celular...',
      allowClear: true,
      dropdownParent: $('#modalNuevaVenta'), // Renderizar dentro del modal
      minimumInputLength: 2, // Mínimo 2 caracteres para buscar
      language: {
        inputTooShort: function () {
          return 'Por favor escribe al menos 2 caracteres';
        },
        searching: function () {
          return 'Buscando...';
        },
        noResults: function () {
          return 'No se encontraron clientes';
        }
      },
      ajax: {
        url: '../../backend/php/alistamiento_api.php?action=buscar_clientes',
        dataType: 'json',
        delay: 300,
        data: function (params) {
          console.log('🔍 Buscando cliente:', params.term);
          return { q: params.term };
        },
        processResults: function (data) {
          console.log('✅ Resultados recibidos:', data);
          if (!data.results || data.results.length === 0) {
            console.warn('⚠️ No se encontraron clientes');
          }
          return {
            results: data.results || []
          };
        },
        error: function (xhr, status, error) {
          console.error('❌ Error en búsqueda:', status, error);
          console.error('Respuesta:', xhr.responseText);
        }
      }
    });
    console.log('✅ Select2 inicializado');
  });
  // ========== SELECCIONAR CLIENTE ==========
  $('#buscarCliente').on('select2:select', function (e) {
    const data = e.params.data;
    $('#hiddenClienteId').val(data.id);
    // Cargar info del cliente
    $.get('../../backend/php/alistamiento_api.php?action=obtener_cliente&id=' + data.id, function (response) {
      if (response.success) {
        const cliente = response.data;
        $('#clienteNombre').text(cliente.nombre_completo);
        $('#clienteTelefono').text('Tel: ' + (cliente.celu || 'N/A'));
        $('#clienteCanal').text('Sede: ' + (cliente.idsede || 'N/A'));
        // Auto-rellenar ubicación si existe dirección
        if (cliente.dircli && !$('#txtUbicacion').val()) {
          $('#txtUbicacion').val(cliente.dircli + (cliente.ciucli ? ', ' + cliente.ciucli : ''));
        }
        $('#infoCliente').fadeIn();
      }
    });
  });
  // ========== BUSCAR EN INVENTARIO ==========
  $('#btnBuscarInventario').click(function () {
    $('#modalBuscarInventario').modal('show');
    $('#txtBuscarInventario').focus();
  });
  let searchTimeout;
  $('#txtBuscarInventario').on('input', function () {
    clearTimeout(searchTimeout);
    const searchTerm = $(this).val();
    if (searchTerm.length < 2) {
      $('#resultadosInventario').html('<p class="text-muted text-center">Escriba al menos 2 caracteres...</p>');
      return;
    }
    searchTimeout = setTimeout(function () {
      $.post('../../backend/php/alistamiento_api.php', {
        action: 'buscar_inventario',
        search: searchTerm
      }, function (response) {
        if (response.success && response.data.length > 0) {
          let html = '';
          response.data.forEach(function (producto) {
            const precio = parseFloat(producto.precio || 0);
            const tactilBadge = producto.tactil === 'SI' ? '<span class="badge badge-info ml-2">Táctil</span>' : '';
            const disposicionColor = {
              'en_proceso': 'warning',
              'en_diagnostico': 'info',
              'en_revision': 'secondary',
              'Por Alistamiento': 'primary',
              'disponible': 'success'
            };
            const dispColor = disposicionColor[producto.disposicion] || 'light';
            html += `
                            <div class="producto-card" data-producto='${JSON.stringify(producto)}'>
                                <div class="row align-items-center">
                                    <div class="col-md-1 text-center">
                                        <span class="grado-badge grado-${producto.grado}">${producto.grado}</span>
                                    </div>
                                    <div class="col-md-9">
                                        <h6 class="mb-1">
                                            <strong>${producto.marca || ''} ${producto.modelo || ''}</strong>
                                            ${tactilBadge}
                                        </h6>
                                        <p class="mb-1"><strong>${producto.producto}</strong> | ${producto.pulgadas || ''}</p>
                                        <small class="text-muted">
                                            ${producto.procesador || 'N/A'} | RAM: ${producto.ram || 'N/A'} | Disco: ${producto.disco || 'N/A'}<br>
                                            <strong>Código:</strong> ${producto.codigo_g} |
                                            <strong>Serial:</strong> ${producto.serial || 'N/A'} |
                                            <strong>Ubicación:</strong> ${producto.ubicacion}<br>
                                            <span class="badge badge-${dispColor}">${producto.disposicion}</span>
                                            ${producto.lote ? ' | <strong>Lote:</strong> ' + producto.lote : ''}
                                        </small>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <h5 class="mb-0" style="color: #00CC54;">
                                            $${precio.toLocaleString('es-CO')}
                                        </h5>
                                        <small class="text-muted">Precio Unit.</small>
                                    </div>
                                </div>
                            </div>
                        `;
          });
          $('#resultadosInventario').html(html);
        } else {
          $('#resultadosInventario').html('<p class="text-muted text-center">No se encontraron productos</p>');
        }
      });
    }, 300);
  });
  // Seleccionar producto de inventario
  $(document).on('click', '.producto-card', function () {
    const producto = JSON.parse($(this).attr('data-producto'));
    const descripcionCompleta = `${producto.marca || ''} ${producto.modelo || ''} - ${producto.procesador || ''} | RAM: ${producto.ram || ''} | Disco: ${producto.disco || ''} | ${producto.pulgadas || ''} | Táctil: ${producto.tactil || 'NO'}`;
    agregarItem({
      inventario_id: producto.id,
      producto: producto.producto,
      marca: producto.marca,
      modelo: producto.modelo,
      procesador: producto.procesador,
      ram: producto.ram,
      disco: producto.disco,
      pulgadas: producto.pulgadas,
      tactil: producto.tactil,
      grado: producto.grado,
      codigo_g: producto.codigo_g,
      serial: producto.serial,
      descripcion: descripcionCompleta,
      cantidad: 1,
      precio_unitario: parseFloat(producto.precio || 0)
    });
    $('#modalBuscarInventario').modal('hide');
    $('#txtBuscarInventario').val('');
    $('#resultadosInventario').html('<p class="text-muted text-center">Escriba para buscar productos...</p>');
    Swal.fire({
      icon: 'success',
      title: 'Producto agregado',
      text: producto.codigo_g + ' agregado correctamente',
      timer: 1500,
      showConfirmButton: false
    });
  });
  // ========== AGREGAR PRODUCTO MANUAL ==========
  $('#btnAgregarManual').click(function () {
    $('#modalAgregarManual').modal('show');
    $('#formProductoManual')[0].reset();
  });
  $('#btnAgregarProductoManual').click(function () {
    if (!$('#formProductoManual')[0].checkValidity()) {
      $('#formProductoManual')[0].reportValidity();
      return;
    }
    agregarItem({
      inventario_id: null,
      producto: $('#txtManualProducto').val(),
      marca: $('#txtManualMarca').val(),
      modelo: $('#txtManualModelo').val(),
      procesador: null,
      ram: $('#txtManualRam').val(),
      disco: $('#txtManualDisco').val(),
      grado: null,
      descripcion: $('#txtManualDescripcion').val(),
      cantidad: parseInt($('#txtManualCantidad').val()),
      precio_unitario: parseFloat($('#txtManualPrecio').val())
    });
    $('#modalAgregarManual').modal('hide');
  });
  // ========== AGREGAR ITEM A LA LISTA ==========
  function agregarItem(item) {
    item.id = ++itemCounter;
    itemsVenta.push(item);
    renderizarItems();
    calcularTotales();
  }
  // ========== RENDERIZAR ITEMS ==========
  function renderizarItems() {
    if (itemsVenta.length === 0) {
      $('#listaItems').html('<p class="text-muted text-center">No hay items agregados. Use los botones de arriba para agregar productos.</p>');
      return;
    }
    let html = '<div class="table-responsive"><table class="table table-sm table-bordered"><thead class="thead-light"><tr><th width="3%">#</th><th width="40%">Producto</th><th width="15%">Cant.</th><th width="17%">Precio Unit.</th><th width="15%">Subtotal</th><th width="10%">Acción</th></tr></thead><tbody>';
    itemsVenta.forEach(function (item, index) {
      const subtotal = item.cantidad * item.precio_unitario;
      const gradoBadge = item.grado ? `<span class="grado-badge grado-${item.grado}" style="width: 20px; height: 20px; line-height: 20px; font-size: 11px;">${item.grado}</span>` : '';
      const tactilBadge = item.tactil === 'SI' ? '<span class="badge badge-info badge-sm">Táctil</span>' : '';
      const fromInventory = item.inventario_id ? '<span class="badge badge-success badge-sm">Inventario</span>' : '<span class="badge badge-secondary badge-sm">Manual</span>';
      html += `
                <tr>
                    <td class="text-center"><strong>${index + 1}</strong></td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${gradoBadge}
                            <div class="ml-2">
                                <strong>${item.producto}</strong> ${tactilBadge} ${fromInventory}<br>
                                <small><strong>${item.marca || ''} ${item.modelo || ''}</strong></small><br>
                                <small class="text-muted">
                                    ${item.procesador || ''} | ${item.ram || ''} | ${item.disco || ''} | ${item.pulgadas || ''}
                                </small>
                                ${item.codigo_g ? '<br><small class="text-muted"><strong>Código:</strong> ' + item.codigo_g + '</small>' : ''}
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm item-cantidad"
                               data-id="${item.id}" value="${item.cantidad}" min="1">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm item-precio"
                               data-id="${item.id}" value="${item.precio_unitario}" min="0" step="1000">
                    </td>
                    <td>
                        <strong style="color: #00CC54;">$${subtotal.toLocaleString('es-CO')}</strong>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-danger btnEliminarItem" data-id="${item.id}" title="Eliminar">
                            <i class="material-icons" style="font-size: 18px;">delete</i>
                        </button>
                    </td>
                </tr>
            `;
    });
    html += '</tbody></table></div>';
    $('#listaItems').html(html);
  }
  // ========== ACTUALIZAR CANTIDAD/PRECIO ==========
  $(document).on('change', '.item-cantidad, .item-precio', function () {
    const itemId = parseInt($(this).data('id'));
    const item = itemsVenta.find(i => i.id === itemId);
    if ($(this).hasClass('item-cantidad')) {
      item.cantidad = parseInt($(this).val());
    } else {
      item.precio_unitario = parseFloat($(this).val());
    }
    renderizarItems();
    calcularTotales();
  });
  // ========== ELIMINAR ITEM ==========
  $(document).on('click', '.btnEliminarItem', function () {
    const itemId = parseInt($(this).data('id'));
    itemsVenta = itemsVenta.filter(i => i.id !== itemId);
    renderizarItems();
    calcularTotales();
  });
  // ========== CALCULAR TOTALES ==========
  function calcularTotales() {
    const subtotal = itemsVenta.reduce((sum, item) => sum + (item.cantidad * item.precio_unitario), 0);
    const descuento = parseFloat($('#txtDescuento').val() || 0);
    const total = subtotal - descuento;
    const abono = parseFloat($('#txtAbono').val() || 0);
    const saldo = total - abono;
    $('#displaySubtotal').text('$' + subtotal.toLocaleString('es-CO'));
    $('#displayTotal').text('$' + total.toLocaleString('es-CO'));
    $('#displaySaldo').text('$' + saldo.toLocaleString('es-CO'));
  }
  $('#txtDescuento, #txtAbono').on('input', calcularTotales);
  // ========== GUARDAR VENTA ==========
  function guardarVenta(estado) {
    // Validaciones
    if (!$('#hiddenClienteId').val()) {
      Swal.fire('Error', 'Debe seleccionar un cliente', 'error');
      return;
    }
    if (!$('#txtTicket').val()) {
      Swal.fire('Error', 'Debe ingresar un ticket', 'error');
      return;
    }
    if (itemsVenta.length === 0) {
      Swal.fire('Error', 'Debe agregar al menos un producto', 'error');
      return;
    }
    const formData = new FormData();
    formData.append('action', 'crear_venta');
    formData.append('cliente_id', $('#hiddenClienteId').val());
    formData.append('sede', $('#txtSede').val());
    formData.append('ticket', $('#txtTicket').val());
    formData.append('ubicacion', $('#txtUbicacion').val());
    formData.append('items', JSON.stringify(itemsVenta));
    formData.append('descuento', $('#txtDescuento').val());
    formData.append('abono', $('#txtAbono').val());
    formData.append('medio_abono', $('#txtMedioAbono').val());
    formData.append('observacion', $('#txtObservacion').val());
    formData.append('estado', estado);
    $.ajax({
      url: '../../backend/php/alistamiento_api.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          Swal.fire('Éxito', response.message, 'success');
          $('#modalNuevaVenta').modal('hide');
          tablaVentas.ajax.reload();
          limpiarFormulario();
        } else {
          Swal.fire('Error', response.message, 'error');
        }
      },
      error: function () {
        Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
      }
    });
  }
  $('#btnGuardarBorrador').click(function () {
    guardarVenta('borrador');
  });
  $('#btnGuardarAprobar').click(function () {
    guardarVenta('aprobado');
  });
  // ========== LIMPIAR FORMULARIO ==========
  function limpiarFormulario() {
    $('#formNuevaVenta')[0].reset();
    $('#buscarCliente').val(null).trigger('change');
    $('#infoCliente').hide();
    $('#hiddenClienteId').val('');
    itemsVenta = [];
    itemCounter = 0;
    renderizarItems();
    calcularTotales();
  }
  $('#modalNuevaVenta').on('hidden.bs.modal', function () {
    limpiarFormulario();
  });
  // ========== VER DETALLE ==========
  $(document).on('click', '.btnVerDetalle', function () {
    const id = $(this).data('id');
    $.get('../../backend/php/alistamiento_api.php?action=obtener_venta&id=' + id, function (response) {
      if (response.success) {
        const venta = response.data;
        let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información General</h6>
                            <table class="table table-sm">
                                <tr><th>ID Venta:</th><td>${venta.idventa}</td></tr>
                                <tr><th>Ticket:</th><td>${venta.ticket}</td></tr>
                                <tr><th>Cliente:</th><td>${venta.cliente}</td></tr>
                                <tr><th>Teléfono:</th><td>${venta.telefono_cliente}</td></tr>
                                <tr><th>Solicitante:</th><td>${venta.solicitante}</td></tr>
                                <tr><th>Sede:</th><td>${venta.sede}</td></tr>
                                <tr><th>Ubicación:</th><td>${venta.ubicacion}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Información Financiera</h6>
                            <table class="table table-sm">
                                <tr><th>Subtotal:</th><td>$${parseFloat(venta.subtotal).toLocaleString('es-CO')}</td></tr>
                                <tr><th>Descuento:</th><td>$${parseFloat(venta.descuento).toLocaleString('es-CO')}</td></tr>
                                <tr><th>Total:</th><td><strong>$${parseFloat(venta.total_venta).toLocaleString('es-CO')}</strong></td></tr>
                                <tr><th>Abono:</th><td>$${parseFloat(venta.valor_abono).toLocaleString('es-CO')}</td></tr>
                                <tr><th>Medio Abono:</th><td>${venta.medio_abono || 'N/A'}</td></tr>
                                <tr><th>Saldo:</th><td style="color: #CC0618;"><strong>$${parseFloat(venta.saldo).toLocaleString('es-CO')}</strong></td></tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <h6>Productos</h6>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
        venta.items.forEach(function (item) {
          html += `
                        <tr>
                            <td>${item.item_numero}</td>
                            <td>${item.producto} ${item.marca || ''} ${item.modelo || ''}<br>
                                <small>${item.ram || ''} ${item.disco || ''}</small>
                            </td>
                            <td>${item.cantidad}</td>
                            <td>$${parseFloat(item.precio_unitario).toLocaleString('es-CO')}</td>
                            <td>$${parseFloat(item.subtotal).toLocaleString('es-CO')}</td>
                        </tr>
                    `;
        });
        html += `
                        </tbody>
                    </table>
                `;
        if (venta.observacion_global) {
          html += `<hr><h6>Observaciones</h6><p>${venta.observacion_global}</p>`;
        }
        $('#detalleVentaBody').html(html);
        $('#modalVerDetalle').modal('show');
      }
    });
  });
  // ========== CAMBIAR ESTADO ==========
  $(document).on('click', '.btnCambiarEstado', function () {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Cambiar Estado',
      html: `
                <select id="swalEstado" class="form-control mb-3">
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
                <textarea id="swalObservacion" class="form-control" placeholder="Observación..."></textarea>
            `,
      showCancelButton: true,
      confirmButtonText: 'Cambiar',
      cancelButtonText: 'Cancelar',
      preConfirm: () => {
        return {
          estado: $('#swalEstado').val(),
          observacion: $('#swalObservacion').val()
        };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('../../backend/php/alistamiento_api.php', {
          action: 'cambiar_estado',
          id: id,
          estado: result.value.estado,
          observacion: result.value.observacion
        }, function (response) {
          if (response.success) {
            Swal.fire('Éxito', response.message, 'success');
            tablaVentas.ajax.reload();
          } else {
            Swal.fire('Error', response.message, 'error');
          }
        });
      }
    });
  });
  // ========== ELIMINAR VENTA ==========
  $(document).on('click', '.btnEliminar', function () {
    const id = $(this).data('id');
    Swal.fire({
      title: '¿Está seguro?',
      text: 'Solo se pueden eliminar ventas en borrador o canceladas',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('../../backend/php/alistamiento_api.php', {
          action: 'eliminar_venta',
          id: id
        }, function (response) {
          if (response.success) {
            Swal.fire('Eliminado', response.message, 'success');
            tablaVentas.ajax.reload();
          } else {
            Swal.fire('Error', response.message, 'error');
          }
        });
      }
    });
  });
});
