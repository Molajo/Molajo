$(function() {
	// Toggle the languages drop down
	$("#select-language").click(function(){ 
		$(this).toggleClass("active");
		$("#language ul").toggle();
		return false;
	});
	
	// Switch the selected language text and class
	var langClass;
	$("#language li a").click(function () {
		langClass = $(this).parent().attr("class");
		$('#language span strong').replaceWith( '<strong class="'+langClass+'">' + $(this).text() + '</strong>' );
	});
	
	// Inline labels
	$('input[title]').each(function() {
		if($(this).val() === '') {$(this).val($(this).attr('title'));}
		$(this).focus(function() {
		if($(this).val() === $(this).attr('title')) {$(this).val('').addClass('focused');}
			$(this).parents("li").addClass("focus");
		});
		$(this).blur(function() {
		if($(this).val() === '') {$(this).val($(this).attr('title')).removeClass('focused');}
			$(this).parents("li").removeClass("focus");
		});
	});
	
	// Faux radio toggle
	$('.radios input[type="radio"]').click( function(){
		$(this).attr('checked', 'checked');
		$('.radios label.label-selected').removeClass('label-selected');
		$(this).parent().addClass('label-selected');
	});
	
	
	$(document).keydown(handleKey);
	
	function handleKey(e)
	{
		var left_arrow = 37;
		var right_arrow = 39;
	
		if (e.target.localName == 'body' || e.target.localName == 'html')
		{
			if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey)
			{
				var code = e.which;
				if (code == left_arrow)
					prevPage();
				else if (code == right_arrow)
					nextPage();
			}
		}
	}
	
	function prevPage()
	{
		var href = $('.pageslist .prev').attr('href');
		if (href && href != document.location)
			document.location = href;
	}
	
	function nextPage()
	{
		var href = $('.pageslist .next').attr('href');
		if (href && href != document.location)
			document.location = href;
	}
});