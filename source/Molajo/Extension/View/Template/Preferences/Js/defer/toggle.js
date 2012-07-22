$(document).ready(function(){
    $('#t-filters').toggle(function(){
        $('#m-batch').slideUp();
        $('#m-options').slideUp();
        $('#m-filters').slideDown();
    }, function(){
    $('#m-filters').slideUp();
    });
});

$(document).ready(function(){
    $('#t-options').toggle(function(){
        $('#m-filters').slideUp();
        $('#m-batch').slideUp();
        $('#m-options').slideDown();
    }, function(){
        $('#m-options').slideUp();
    });
});


$(document).ready(function(){
    $('#t-batch').toggle(function(){
        $('#m-options').slideUp();
        $('#m-filters').slideUp();
        $('#m-batch').slideDown();
    }, function(){
        $('#m-batch').slideUp();
    });
});
