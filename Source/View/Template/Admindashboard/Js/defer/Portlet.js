var portlets = ["feeds", "shopping", "news", "links", "images"];

$(document).ready(function () {
    $('#menu2').click(function (event) {
        $('#window_dialog').dialog({
            autoOpen: true,
            draggable: false,
            modal: true,
            title: 'Settings',
            buttons: {
                "Save": function () {

                    $(portlets).each(function () {
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

    function set_window_visibility(name) {
        if ($('#' + name + '-visible').is(':checked'))
            $('#' + name + '-portlet').show();
        else
            $('#' + name + '-portlet').hide();
    }

    function set_visible_check(name) {
        if ($('#' + name + '-portlet').is(":visible"))
            $('#' + name + '-visible').each(function () {
                this.checked = true;
            });
        else
            $('#' + name + '-visible').attr("checked", false);
    }

    $("#settings_dialog").bind("dialogopen", function (event, ui) {
    });

    $("#window_dialog").bind("dialogopen", function (event, ui) {
        $(portlets).each(function () {
            set_visible_check(this);
        });
    });
});

$(function () {
    $(".sortable").sortable({});

    $(".portlet").addClass("portlet portlet-content ui-helper-clearfix ui-corner-all")
        .find(".portlet-header")
        .addClass("portlet-header ui-corner-all")
        .prepend("<span  class='icon icon-close icon-close'></span><span class='icon icon-minus icon-vis'></span>")
        .end()
        .find(".portlet-content");

    $(".icon-vis").click(function () {
        $(this).toggleClass("icon-minus").toggleClass("icon-plus");
        $(this).parents(".portlet:first").find(".portlet-content").toggle();
    });

    $(".icon-close").click(function () {
        $(this).parents(".portlet:first").hide();
    });

    $(".portlet").disableSelection();
});
