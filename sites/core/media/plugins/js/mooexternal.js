window.onload = function() {
    external = document.getElementsByClassName('external');
    for (var i = 0; i < external.length; i++) external[i].onclick = function() {
        window.open(this.href, '_blank');
        return false;
    }
}