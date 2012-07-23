var widgets = ["feeds", "shopping", "news", "links", "images"];

$(document).ready(function () {
    $('#menu2').click(function(event) {
        $('#window_dialog').dialog({
            autoOpen: true,
            draggable: false,
            modal: true,
            title: 'Settings',
            buttons: {
                "Save": function () {

                    $(widgets).each(function() {
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
            $('#'+name+'-ui-widget').show();
        else
            $('#'+name+'-ui-widget').hide();
    }
    function set_visible_check(name){
        if($('#'+name+'-ui-widget').is(":visible"))
            $('#'+name+'-visible').each(function(){ this.checked = true; });
        else
            $('#'+name+'-visible').attr("checked", false);
    }
    $( "#settings_dialog" ).bind( "dialogopen", function(event, ui) {

    });
    $( "#window_dialog" ).bind( "dialogopen", function(event, ui) {
        $(widgets).each(function() {
            set_visible_check(this);
        });
    });
});

$(function() {
    $( ".ui-widget1" ).sortable({
        connectWith: ".ui-widget2, .ui-widget3, .ui-widget4, .ui-widget5"
    });
    $( ".ui-widget2" ).sortable({
        connectWith: ".ui-widget1, .ui-widget3, .ui-widget4, .ui-widget5"
    });
    $( ".ui-widget3" ).sortable({
        connectWith: ".ui-widget1, .ui-widget2, .ui-widget4, .ui-widget5"
    });
    $( ".ui-widget4" ).sortable({
        connectWith: ".ui-widget1, .ui-widget2, .ui-widget3, .ui-widget5"
    });
    $( ".ui-widget5" ).sortable({
        connectWith: ".ui-widget1, .ui-widget2, .ui-widget3, .ui-widget4"
    });

    $( ".ui-widget" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
        .find( ".ui-widget-header" )
        .addClass( "ui-widget-header ui-corner-all" )
        .prepend( "<span  class='ui-icon ui-icon-close icon-close'></span><span class='ui-icon ui-icon-minus icon-vis'></span>")
        .end()
        .find( ".ui-widget-content" );

    $( ".icon-vis" ).click(function() {
        $( this ).toggleClass( "ui-icon-minus" ).toggleClass( "ui-icon-plus" );
        $( this ).parents( ".ui-widget:first" ).find( ".ui-widget-content" ).toggle();
    });

    $( ".icon-close" ).click(function() {
        //$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
        $( this ).parents( ".ui-widget:first" ).hide();
    });

    $( ".ui-widget" ).disableSelection();
});
