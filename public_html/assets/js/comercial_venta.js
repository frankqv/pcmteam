/**
 * ====================================================
 * COMERCIAL VENTA - Sistema de Ventas PCMARKETTEAM
 * ====================================================
 *
 * Sistema completo de gestión de ventas con:
 * - Búsqueda de clientes con Select2
 * - Búsqueda de productos en inventario
 * - Agregado manual de productos
 * - Sistema de abonos progresivos
 * - Cálculos en tiempo real
 * - Validaciones y alertas
 */

(function($) {
    'use strict';

    // ==========================================
    // VARIABLES GLOBALES
    // ==========================================
    let productos = [];
    let searchTimeout = null;

    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    $(document).ready(function() {
        initSidebar();
        initSelect2Cliente();
        initEventListeners();
    });

    // ==========================================
    // SIDEBAR
    // ==========================================
    function initSidebar() {
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });

        $('.more-button,.body-overlay').on('click', function() {
            $('#sidebar,.body-overlay').toggleClass('show-nav');
        });
    }

    // ==========================================
    // SELECT2 PARA CLIENTES
    // ==========================================
    function initSelect2Cliente() {
        $('#buscarCliente').select2({
            ajax: {
                url: 'nueva_venta.php',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'buscar_cliente',
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return data;
                }
            },
            placeholder: 'Buscar cliente por NIT, nombre, correo...',
            minimumInputLength: 2
        });

        // Evento al seleccionar cliente
        $('#buscarCliente').on('select2:select', function(e) {
            const data = e.params.data;
            $('#hiddenClienteId').val(data.id);
            $('#hiddenCanalVenta').val(data.canal_venta);
            $('#txtUbicacion').val(data.direccion);

            $('#clienteNombre').text(data.nombre);
            $('#clienteTelefono').text('Tel: ' + data.telefono);
            $('#clienteCanal').text('Canal: ' + data.canal_venta);
            $('#infoCliente').show();
        });
    }

    // ==========================================
    // EVENT LISTENERS
    // ==========================================
    function initEventListeners() {
        // Modal buscar inventario
        $('#btnBuscarInventario').on('click', function() {
            $('#modalBuscarInventario').modal('show');
            cargarInventario('');
        });

        // Buscar en inventario con delay
        $('#txtBuscarInventario').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                cargarInventario($(this).val());
            }, 300);
        });

        // Modal agregar manual
        $('#btnAgregarManual').on('click', function() {
            $('#formProductoManual')[0].reset();
            $('#modalAgregarManual').modal('show');
        });

        // Agregar producto manual
        $('#btnAgregarProductoManual').on('click', agregarProductoManual);

        // Eventos de cambio en valores financieros
        $('#txtDescuento, #txtAbono').on('input', calcularTotales);

        // Guardar como borrador
        $('#btnGuardarBorrador').on('click', function() {
            guardarVenta('borrador');
        });

        // Guardar y aprobar
        $('#btnGuardarAprobar').on('click', function() {
            guardarVenta('aprobado');
        });
    }

    // ==========================================
    // CARGAR INVENTARIO
    // ==========================================
    function cargarInventario(busqueda) {
        $.ajax({
            url: 'nueva_venta.php',
            type: 'POST',
            data: {
                action: 'buscar_inventario',
                q: busqueda
            },
            dataType: 'json',
            success: function(data) {
                let html = '';
                if (data.length === 0) {
                    html = '<p class="text-muted text-center">No se encontraron productos</p>';
                } else {
                    data.forEach(item => {
                        const precio = new Intl.NumberFormat('es-CO', {
                            style: 'currency',
                            currency: 'COP',
                            minimumFractionDigits: 0
                        }).format(item.precio);

                        html += `
                            <div class="producto-card" onclick='VentaManager.agregarDesdeInventario(${JSON.stringify(item)})'>
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong style="font-size: 16px;">${item.producto}</strong><br>
                                        <small><b>Marca:</b> ${item.marca} | <b>Modelo:</b> ${item.modelo}</small><br>
                                        <small>${item.procesador} - ${item.ram} - ${item.disco}</small>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <span class="grado-badge grado-${item.grado}">${item.grado}</span><br>
                                        <strong style="font-size: 18px; color: #00CC54;">${precio}</strong>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                $('#resultadosInventario').html(html);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar el inventario'
                });
            }
        });
    }

    // ==========================================
    // AGREGAR PRODUCTO MANUAL
    // ==========================================
    function agregarProductoManual() {
        if (!$('#txtManualProducto').val() || !$('#txtManualPrecio').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos requeridos',
                text: 'Complete los campos obligatorios'
            });
            return;
        }

        const producto = {
            id_inventario: null,
            cantidad: parseInt($('#txtManualCantidad').val()),
            descripcion: $('#txtManualProducto').val(),
            marca: $('#txtManualMarca').val(),
            modelo: $('#txtManualModelo').val(),
            observacion: $('#txtManualObservacion').val(),
            precio_unitario: parseFloat($('#txtManualPrecio').val())
        };

        productos.push(producto);
        renderizarProductos();
        $('#modalAgregarManual').modal('hide');

        Swal.fire({
            icon: 'success',
            title: 'Producto agregado',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }

    // ==========================================
    // RENDERIZAR PRODUCTOS
    // ==========================================
    function renderizarProductos() {
        if (productos.length === 0) {
            $('#listaItems').html('<p class="text-muted text-center">No hay items agregados. Use los botones de arriba para agregar productos.</p>');
            calcularTotales();
            return;
        }

        let html = '';
        productos.forEach((prod, index) => {
            const total = prod.cantidad * prod.precio_unitario;
            const totalFormateado = formatCurrency(total);
            const precioFormateado = formatCurrency(prod.precio_unitario);

            html += `
                <div class="item-producto" data-index="${index}">
                    <button type="button" class="btn btn-danger btn-sm btn-remove" onclick="VentaManager.eliminarProducto(${index})">
                        <span class="material-icons" style="font-size: 18px;">delete</span>
                    </button>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Producto</label>
                            <input type="text" class="form-control" value="${prod.descripcion}" onchange="VentaManager.actualizarProducto(${index}, 'descripcion', this.value)">
                        </div>
                        <div class="col-md-3">
                            <label>Marca</label>
                            <input type="text" class="form-control" value="${prod.marca}" onchange="VentaManager.actualizarProducto(${index}, 'marca', this.value)">
                        </div>
                        <div class="col-md-3">
                            <label>Modelo</label>
                            <input type="text" class="form-control" value="${prod.modelo}" onchange="VentaManager.actualizarProducto(${index}, 'modelo', this.value)">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Observación</label>
                            <input type="text" class="form-control" value="${prod.observacion}" placeholder="Ej: con mouse, con programas..." onchange="VentaManager.actualizarProducto(${index}, 'observacion', this.value)">
                        </div>
                        <div class="col-md-2">
                            <label>Cantidad</label>
                            <input type="number" class="form-control" value="${prod.cantidad}" min="1" onchange="VentaManager.actualizarProducto(${index}, 'cantidad', this.value)">
                        </div>
                        <div class="col-md-2">
                            <label>Precio Unit.</label>
                            <input type="number" class="form-control" value="${prod.precio_unitario}" min="0" step="1000" onchange="VentaManager.actualizarProducto(${index}, 'precio_unitario', this.value)">
                        </div>
                        <div class="col-md-2">
                            <label>Total</label>
                            <input type="text" class="form-control" value="${totalFormateado}" readonly style="font-weight: bold; background: #e9ecef;">
                        </div>
                    </div>
                </div>
            `;
        });

        $('#listaItems').html(html);
        calcularTotales();
    }

    // ==========================================
    // CALCULAR TOTALES
    // ==========================================
    function calcularTotales() {
        let subtotal = 0;
        productos.forEach(prod => {
            subtotal += prod.cantidad * prod.precio_unitario;
        });

        const descuento = parseFloat($('#txtDescuento').val()) || 0;
        const total = subtotal - descuento;
        const abono = parseFloat($('#txtAbono').val()) || 0;
        const saldo = total - abono;

        $('#displaySubtotal').text(formatCurrency(subtotal));
        $('#displayTotal').text(formatCurrency(total));
        $('#displaySaldo').text(formatCurrency(saldo));
    }

    // ==========================================
    // GUARDAR VENTA
    // ==========================================
    function guardarVenta(estado) {
        // Validaciones
        if (!$('#hiddenClienteId').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Cliente requerido',
                text: 'Debe seleccionar un cliente'
            });
            return;
        }

        if (!$('#txtSede').val() || !$('#txtConcepto').val() || !$('#txtUbicacion').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos requeridos',
                text: 'Complete todos los campos obligatorios'
            });
            return;
        }

        if (productos.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin productos',
                text: 'Debe agregar al menos un producto'
            });
            return;
        }

        // Preparar FormData
        const formData = new FormData();
        formData.append('action', 'guardar_venta');
        formData.append('idcliente', $('#hiddenClienteId').val());
        formData.append('sede', $('#txtSede').val());
        formData.append('tipo_cliente', 'Cliente Regular');
        formData.append('direccion', $('#txtUbicacion').val());
        formData.append('canal_venta', $('#hiddenCanalVenta').val() || 'Tienda Física');
        formData.append('concepto_salida', $('#txtConcepto').val());
        formData.append('observacion_global', $('#txtObservacion').val());
        formData.append('productos', JSON.stringify(productos));
        formData.append('descuento', $('#txtDescuento').val());
        formData.append('valor_abono', $('#txtAbono').val());
        formData.append('metodo_pago_abono', $('#txtMedioAbono').val());
        formData.append('estado', estado);

        // Agregar archivos
        const archivos = $('#fotoComprobante')[0].files;
        for (let i = 0; i < archivos.length; i++) {
            formData.append('comprobantes[]', archivos[i]);
        }

        // Mostrar loading
        Swal.fire({
            title: 'Guardando venta...',
            html: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Enviar petición
        $.ajax({
            url: 'nueva_venta.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Venta guardada',
                        html: `<p>${response.message}</p><p><strong>ID Venta:</strong> ${response.idventa}</p><p><strong>Ticket:</strong> ${response.ticket}</p>`,
                        confirmButtonColor: '#00CC54'
                    }).then(() => {
                        window.location.href = 'historico_venta.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: 'Ocurrió un error al guardar la venta: ' + error
                });
            }
        });
    }

    // ==========================================
    // UTILIDADES
    // ==========================================
    function formatCurrency(value) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        }).format(value);
    }

    // ==========================================
    // API PÚBLICA
    // ==========================================
    window.VentaManager = {
        agregarDesdeInventario: function(item) {
            const producto = {
                id_inventario: item.id,
                cantidad: 1,
                descripcion: item.producto,
                marca: item.marca,
                modelo: item.modelo,
                observacion: '',
                precio_unitario: item.precio
            };
            productos.push(producto);
            renderizarProductos();
            $('#modalBuscarInventario').modal('hide');

            Swal.fire({
                icon: 'success',
                title: 'Producto agregado',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        },

        actualizarProducto: function(index, campo, valor) {
            if (campo === 'cantidad' || campo === 'precio_unitario') {
                productos[index][campo] = parseFloat(valor);
            } else {
                productos[index][campo] = valor;
            }
            renderizarProductos();
        },

        eliminarProducto: function(index) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    productos.splice(index, 1);
                    renderizarProductos();
                    Swal.fire({
                        icon: 'success',
                        title: 'Producto eliminado',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        }
    };

})(jQuery);
