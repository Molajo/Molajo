$(document).ready(function(){
    $('#t-filters').toggle(function(){
        $('#m-filters').slideDown();
        $('#m-batch').slideUp();
        $('#m-view').slideUp();
        $('#m-options').slideUp();
    }, function(){
    $('#m-filters').slideUp();
    });
});

$(document).ready(function(){
    $('#t-batch').toggle(function(){
        $('#m-filters').slideUp();
        $('#m-batch').slideDown();
        $('#m-view').slideUp();
        $('#m-options').slideUp();
    }, function(){
        $('#m-batch').slideUp();
    });
});

$(document).ready(function(){
    $('#t-view').toggle(function(){
        $('#m-filters').slideUp();
        $('#m-batch').slideUp();
        $('#m-view').slideDown();
        $('#m-options').slideUp();
    }, function(){
        $('#m-view').slideUp();
    });
});

$(document).ready(function(){
    $('#t-options').toggle(function(){
        $('#m-filters').slideUp();
        $('#m-batch').slideUp();
        $('#m-view').slideUp();
        $('#m-options').slideDown();
    }, function(){
        $('#m-options').slideUp();
    });
});
