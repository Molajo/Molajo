jQuery(document).ready(function($){

	// Level one menu logic
	$(".level-one dt>a").click(function(e){
		$(this).parent('dt').toggleClass('current');
		alert('You clicked a level one link');
		e.preventDefault();
	});

	// Level two menu logic
	

});