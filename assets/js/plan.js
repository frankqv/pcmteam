$(function(){

    // Lista de docente
    $.post( '../../public_html/functions/plan.php' ).done( function(respuesta)
    {
        $( '#plan' ).html( respuesta );
    });
    
    
    // Lista de Ciudades
    $( '#plan' ).change( function()
    {
         var el_continente = $(this).val();   
        $.post( '../../public_html/functions/total.php', { continente: el_continente} ).done( function( respuesta )
        {
            $( '#total' ).html( respuesta );
        });

    });


    
    
    

})
