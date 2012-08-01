var portlets = ["feeds", "shopping", "news", "links", "images"];

$(document).ready(function () {
    $('#menu2').click(function(event) {
        $('#window_dialog').dialog({
            autoOpen: true,
            draggable: false,
            modal: true,
            title: 'Settings',
            buttons: {
                "Save": function () {

                    $(portlets).each(function() {
                        set_window_visibility(this);
                    });

                    $(this).dialog('destroy');
                },
                "Cancel": function () {
                    $(this).dialog('destroy');
                }
            }
        });
    });
    function set_window_visibility(name){
        if($('#'+name+'-visible').is(':checked'))
            $('#'+name+'-portlet').show();
        else
            $('#'+name+'-portlet').hide();
    }
    function set_visible_check(name){
        if($('#'+name+'-portlet').is(":visible"))
            $('#'+name+'-visible').each(function(){ this.checked = true; });
        else
            $('#'+name+'-visible').attr("checked", false);
    }
    $( "#settings_dialog" ).bind( "dialogopen", function(event, ui) {

    });
    $( "#window_dialog" ).bind( "dialogopen", function(event, ui) {
        $(portlets).each(function() {
            set_visible_check(this);
        });
    });
});

$(function() {
    $( ".portlet1" ).sortable({
        connectWith: ".portlet2, .portlet3, .portlet4, .portlet5"
    });
    $( ".portlet2" ).sortable({
        connectWith: ".portlet1, .portlet3, .portlet4, .portlet5"
    });
    $( ".portlet3" ).sortable({
        connectWith: ".portlet1, .portlet2, .portlet4, .portlet5"
    });
    $( ".portlet4" ).sortable({
        connectWith: ".portlet1, .portlet2, .portlet3, .portlet5"
    });
    $( ".portlet5" ).sortable({
        connectWith: ".portlet1, .portlet2, .portlet3, .portlet4"
    });

    $( ".portlet" ).addClass( "ui-portlet ui-portlet-content ui-helper-clearfix ui-corner-all" )
        .find( ".portlet-header" )
        .addClass( "ui-portlet-header ui-corner-all" )
        .prepend( "<span  class='ui-icon ui-icon-close icon-close'></span><span class='ui-icon ui-icon-minus icon-vis'></span>")
        .end()
        .find( ".portlet-content" );

    $( ".icon-vis" ).click(function() {
        $( this ).toggleClass( "ui-icon-minus" ).toggleClass( "ui-icon-plus" );
        $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
    });

    $( ".icon-close" ).click(function() {
        //$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
        $( this ).parents( ".portlet:first" ).hide();
    });

    $( ".portlet" ).disableSelection();
});
