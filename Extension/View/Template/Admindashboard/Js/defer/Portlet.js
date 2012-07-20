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
                    $(".column1").width(parseInt($('#c1-width').val()));
                    $(".column2").width(parseInt($('#c2-width').val()));
                    $(".column3").width(parseInt($('#c3-width').val()));
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
        $('#c1-width').val($(".column1").width());
        $('#c2-width').val($(".column2").width());
        $('#c3-width').val($(".column3").width());
    });
});

$(function() {
    $( ".column1" ).sortable({
        connectWith: ".column1, .column2, .column3"
    });
    $( ".column2" ).sortable({
        connectWith: ".column1, .column2, .column3"
    });
    $( ".column3" ).sortable({
        connectWith: ".column1, .column2, .column3"
    });
    $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
        .find( ".portlet-header" )
        .addClass( "ui-widget-header ui-corner-all" )
        .prepend( "<span  class='ui-icon ui-icon-closethick icon-close'></span><span class='ui-icon ui-icon-minusthick icon-vis'></span>")
        .end()
        .find( ".portlet-content" );

    $( ".icon-vis" ).click(function() {
        $( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
        $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
    });
    $( ".icon-close" ).click(function() {
        //$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
        $( this ).parents( ".portlet:first" ).hide();
    });
    $( ".column" ).disableSelection();
});
