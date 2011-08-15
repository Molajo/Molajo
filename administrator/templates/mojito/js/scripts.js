$(function(){

    // Accordion
    var stop = false;
    $( "#accordion h2" ).click(function( event ) {
        if ( stop ) {
            event.stopImmediatePropagation();
            event.preventDefault();
            stop = false;
        }
    });

    $( "#accordion" )
        .accordion({
            header: "> div > h2"
        })
        .sortable({
            axis: "y",
            handle: "h2",
            stop: function() {
                stop = true;
        }
    });

    $( "#accordion" ).accordion({
        fillSpace: true,
        autoHeight: false,
        navigation: true
    });

    // Tabs
    $('#tabs').tabs();

});