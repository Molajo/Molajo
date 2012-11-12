$(document).ready(function () {

    $('div.grid-batch').hide();
    $('div.grid-ordering').show();
    $('ul.section-submenu li a').removeClass('active');
    $('#ordering').addClass('active');

    $('#ordering').click(function(){
        $('div.grid-batch').hide();
        $('div.grid-ordering').show();
        $('ul.section-submenu li a').removeClass('active');
        $('#ordering').addClass('active');
    });

    $('#status').click(function(){
        $('div.grid-batch').hide();
        $('div.grid-status').show();
        $('ul.section-submenu li a').removeClass('active');
        $('#status').addClass('active');
    });

    $('#categories').click(function(){
        $('div.grid-batch').hide();
        $('div.grid-categories').show();
        $('ul.section-submenu li a').removeClass('active');
        $('#categories').addClass('active');
    });

    $('#tags').click(function(){
        $('div.grid-batch').hide();
        $('div.grid-tags').show();
        $('ul.section-submenu li a').removeClass('active');
        $('#tags').addClass('active');
    });

    $('#permissions').click(function(){
        $('div.grid-batch').hide();
        $('div.grid-permissions').show();
        $('ul.section-submenu li a').removeClass('active');
        $('#permissions').addClass('active');
    });

    $('#feature').click(function(){
        $('div.grid-batch').hide();
        $('div.grid-feature').show();
        $('ul.section-submenu li a').removeClass('active');
        $('#feature').addClass('active');
    });

    $('#sticky').click(function(){
        $('div.grid-batch').hide();
        $('div.grid-sticky').show();
        $('ul.section-submenu li a').removeClass('active');
        $('#sticky').addClass('active');
    });

    $('#checkin').click(function(){
        $('div.grid-batch').hide();
        $('div.grid-checkin').show();
        $('ul.section-submenu li a').removeClass('active');
        $('#checkin').addClass('active');
    });

});
