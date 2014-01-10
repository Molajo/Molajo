$(document).ready(function () {

    $('div.grid-batch').hide();
    $('div.grid-ordering').show();
    $('ul.section-submenu li a').removeClass('active');
    $('#ordering').addClass('active');

    $('#ordering').click(function () {
        $('div.grid-batch').hide();
        $('div.grid-ordering').show();
        $('ul.section-submenu li a').removeClass('active');
        $('#ordering').addClass('active');
    });

});
