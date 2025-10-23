/**
 * SCRIPT DE DEBUG PARA SELECT2
 * Agregar este script DESPUÉS de alistamiento_venta.js para debug
 */

console.log('=== DEBUG SELECT2 ===');

// Verificar que jQuery está cargado
if (typeof jQuery !== 'undefined') {
    console.log('✅ jQuery cargado - Versión:', jQuery.fn.jquery);
} else {
    console.error('❌ jQuery NO está cargado');
}

// Verificar que Select2 está cargado
if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
    console.log('✅ Select2 cargado');
} else {
    console.error('❌ Select2 NO está cargado');
}

// Verificar que el elemento existe
$(document).ready(function() {
    console.log('DOM Ready');

    if ($('#buscarCliente').length > 0) {
        console.log('✅ Elemento #buscarCliente existe');
        console.log('Elemento:', $('#buscarCliente'));
    } else {
        console.error('❌ Elemento #buscarCliente NO existe');
    }

    // Probar la API directamente
    console.log('Probando API...');
    $.get('../../backend/php/alistamiento_api.php?action=buscar_clientes&q=test', function(response) {
        console.log('Respuesta de API buscar_clientes:', response);
    }).fail(function(xhr, status, error) {
        console.error('Error en API:', status, error);
        console.error('Respuesta completa:', xhr.responseText);
    });

    // Verificar si Select2 se inicializó
    setTimeout(function() {
        if ($('#buscarCliente').hasClass('select2-hidden-accessible')) {
            console.log('✅ Select2 inicializado correctamente');
        } else {
            console.error('❌ Select2 NO se inicializó');
        }
    }, 1000);

    // Escuchar eventos de Select2
    $('#buscarCliente').on('select2:opening', function() {
        console.log('🔵 Select2 abriéndose...');
    });

    $('#buscarCliente').on('select2:open', function() {
        console.log('✅ Select2 abierto');
    });

    $('#buscarCliente').on('select2:select', function(e) {
        console.log('✅ Cliente seleccionado:', e.params.data);
    });

    $('#buscarCliente').on('select2:error', function(e) {
        console.error('❌ Error en Select2:', e);
    });
});
