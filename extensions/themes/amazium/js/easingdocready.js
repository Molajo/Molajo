/* Smooth scrolling
 Changes links that link to other parts of this page to scroll
 smoothly to those links rather than jump to them directly, which
 can be a little disorienting.

 sil, http://www.kryogenix.org/

 v1.0 2003-11-11
 v1.1 2005-06-16 wrap it up in an object
 */
$(document).ready(function () {
    $().UItoTop({ easingType:'easeOutQuart' });
});
