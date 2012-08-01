jQuery(document).ready(function($){

	$(".level-one dt").mouseover(function(){
		var next = $(this).next();
		if(next.children().length) {
			$(this).addClass('current');
			next.show();
			next.siblings('dd').hide().prev('dt').removeClass('current');
		}
	});
});