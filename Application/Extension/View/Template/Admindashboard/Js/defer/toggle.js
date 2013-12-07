$(document).ready(function () {
    $('#t-options').toggle(function () {
        $('#m-options').slideDown();
    }, function () {
        $('#m-options').slideUp();
    });
});
