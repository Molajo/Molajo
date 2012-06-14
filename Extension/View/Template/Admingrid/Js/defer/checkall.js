$(document).ready(function(){

    $("input[type=checkbox].checkall").on("click.chkAll", function( event ){
        $(this).parents('.responsive:eq(0)').find(':checkbox').prop('checked', this.checked);
    });

});
