/**
 * NUEVA VENTA - JavaScript
 * Maneja la lógica de creación de ventas (página standalone)
 */

let itemsVenta = [];
let itemCounter = 0;

$(document).ready(function () {

  console.log('🔵 Nueva Venta - Inicializando...');

  // ========== INICIALIZAR SELECT2 CLIENTES ==========
  if ($('#buscarCliente').length > 0) {
    console.log('🔍 Inicializando Select2 para búsqueda de clientes...');

    $('#buscarCliente').select2({
      placeholder: 'Buscar cliente por nombre, NIT, correo o celular...',
      allowClear: true,
      minimumInputLength: 2,
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
  }

  // ========== SELECCIONAR CLIENTE ==========
  $('#buscarCliente').on('select2:select', function (e) {
    const data = e.params.data;
    console.log('📋 Cliente seleccionado:', data);

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
        console.log('✅ Información de cliente cargada');
      } else {
        console.error('❌ Error al cargar cliente:', response.message);
      }
    });
  });

  // ========== BUSCAR EN INVENTARIO ==========
  $('#btnBuscarInventario').click(function () {
    console.log('🔍 Abriendo modal de búsqueda en inventario');
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
      console.log('🔍 Buscando en inventario:', searchTerm);

      $.post('../../backend/php/alistamiento_api.php', {
        action: 'buscar_inventario',
        search: searchTerm
      }, function (response) {
        if (response.success && response.data.length > 0) {
          console.log('✅ Productos encontrados:', response.data.length);
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
          console.log('⚠️ No se encontraron productos');
          $('#resultadosInventario').html('<p class="text-muted text-center">No se encontraron productos</p>');
        }
      });
    }, 300);
  });

  // Seleccionar producto de inventario
  $(document).on('click', '.producto-card', function () {
    const producto = JSON.parse($(this).attr('data-producto'));
    console.log('➕ Agregando producto de inventario:', producto.codigo_g);

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
    console.log('➕ Abriendo modal para producto manual');
    $('#modalAgregarManual').modal('show');
    $('#formProductoManual')[0].reset();
  });

  $('#btnAgregarProductoManual').click(function () {
    if (!$('#formProductoManual')[0].checkValidity()) {
      $('#formProductoManual')[0].reportValidity();
      return;
    }

    console.log('➕ Agregando producto manual');

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
    console.log('✅ Item agregado. Total items:', itemsVenta.length);
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
      console.log('📝 Cantidad actualizada:', item.cantidad);
    } else {
      item.precio_unitario = parseFloat($(this).val());
      console.log('💰 Precio actualizado:', item.precio_unitario);
    }

    renderizarItems();
    calcularTotales();
  });

  // ========== ELIMINAR ITEM ==========
  $(document).on('click', '.btnEliminarItem', function () {
    const itemId = parseInt($(this).data('id'));
    itemsVenta = itemsVenta.filter(i => i.id !== itemId);
    console.log('🗑️ Item eliminado. Total items:', itemsVenta.length);
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

    console.log('💵 Totales - Subtotal:', subtotal, '| Total:', total, '| Saldo:', saldo);
  }

  $('#txtDescuento, #txtAbono').on('input', calcularTotales);

  // ========== GUARDAR VENTA ==========
  function guardarVenta(estado) {
    console.log('💾 Guardando venta con estado:', estado);

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
          console.log('✅ Venta guardada exitosamente');
          Swal.fire({
            title: 'Éxito',
            text: response.message,
            icon: 'success'
          }).then(() => {
            // Redirigir al listado
            window.location.href = 'alistamiento_venta.php';
          });
        } else {
          console.error('❌ Error al guardar venta:', response.message);
          Swal.fire('Error', response.message, 'error');
        }
      },
      error: function (xhr, status, error) {
        console.error('❌ Error de comunicación:', status, error);
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

  // ========== LIMPIAR FORMULARIO (para reutilización) ==========
  function limpiarFormulario() {
    $('#formNuevaVenta')[0].reset();
    $('#buscarCliente').val(null).trigger('change');
    $('#infoCliente').hide();
    $('#hiddenClienteId').val('');
    itemsVenta = [];
    itemCounter = 0;
    renderizarItems();
    calcularTotales();
    console.log('🧹 Formulario limpiado');
  }

  // Exponer función globalmente para uso externo si es necesario
  window.limpiarFormularioVenta = limpiarFormulario;

  console.log('✅ Nueva Venta - Inicialización completa');
});
