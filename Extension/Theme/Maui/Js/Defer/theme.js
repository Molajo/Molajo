jQuery(document).ready(function($){

	$('.level-one>dt').mouseover(function(){
		var next = $(this).next();
		if(next.children().length) {
			$(this).addClass('current');
			next.show();
			next.siblings('dd').hide().prev('dt').removeClass('current');
		}
	});
	// Hide submenu in focus mode
	$('#focus nav[role="navigation"]').mouseleave(function(){
		// $('.level-one>dt').removeClass('current').siblings('dd').hide();
	});
});