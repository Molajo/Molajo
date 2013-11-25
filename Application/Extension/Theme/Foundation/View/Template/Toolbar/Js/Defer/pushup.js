$(document).ready(function () {
    $('#right-demo').pupslider({ stick: 'right', speed: 500, opacity: 0.9 });
    $('#left-demo').pupslider({ stick: 'left', speed: 500, opacity: 0.9 });

    $('#left-demo').css('display', 'none');

    $('#left-align').click(function () {
        $('#right-demo').css('display', 'none');
        $('#left-demo').css('display', '');
    });
    $('#right-align').click(function () {
        $('#left-demo').css('display', 'none');
        $('#right-demo').css('display', '');
    });

    var latlng = new google.maps.LatLng(31.5497222, 74.3436111);
    var myOptions = {
        zoom: 8,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map($('.map').get(0), myOptions);
    $('.name').focus(function () {
        $(this).val('');
    }).blur(function () {
            if ($(this).val() == '') $(this).val('Name');
        });
    $('.email').focus(function () {
        $(this).val('');
    }).blur(function () {
            if ($(this).val() == '') $(this).val('Email');
        });
    $('.message').focus(function () {
        $(this).val('');
    }).blur(function () {
            if ($(this).val() == '') $(this).val('Message');
        });

});
