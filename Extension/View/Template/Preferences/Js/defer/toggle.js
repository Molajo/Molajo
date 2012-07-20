$(document).ready(function(){
    $('#top').toggle(function(){
        $('#mid').slideDown();
    }, function(){
    $('#mid').slideUp();
    });
});
