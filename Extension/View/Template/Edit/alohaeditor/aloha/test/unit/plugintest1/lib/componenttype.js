define(
[], // no dependency
function() {
    
 
    var resourcetype = Class.extend({
        doSome: function() {
            return 'didSome';
        }
    });
 
    return resourcetype;
 
});
