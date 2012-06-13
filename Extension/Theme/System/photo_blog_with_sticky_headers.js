/* Foundation v2.1.4 http://foundation.zurb.com */
$(document).ready(function () {
	
	$(window).scroll(function() {
		var scroll = Math.max($('body').scrollTop(), $('html,body').scrollTop());
		
		$(".showcase .title").each(function(index, element) {
			var slide = $(this).closest("li");
			var top = slide.offset().top
			var bottom = top + slide.outerHeight();
			
			if (scroll > top && scroll < (top + $(this).siblings("img").outerHeight()))
				$(this).addClass("fixed");
			else $(this).removeClass("fixed");
		});
		
	});

});
