define(
['./resourcetype'], // dependency in the same path
function( resourcetype ) {
    
 
    var resource = resourcetype.extend({
        doOther: function() {
            return 'didOther';
        }
    });
    return new resource();
 
});
