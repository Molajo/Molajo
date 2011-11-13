$(function(){

				// Accordion
				$('.accordion .head').click(function() {
					$(this).next().toggle('slow');
					return false;
				}).next().hide();
				
				$( "#accordion" )
					.accordion({
						header: "> div > h2"
					})
					.sortable({
						axis: "y",
						handle: "h2",
						stop: function() {
							stop = true;
					}
				});
				
				$( "#accordion" ).accordion({
					fillSpace: true,
					autoHeight: false,
					navigation: true,
					collapsible: true
				});
				
				// Tabs
				$('#tabs').tabs();
				
				// Toggle editor action buttons
				// will need to add check for actual state
				$('.editor .actions li a').click(function () {
				      $(this).toggleClass("enabled");
				});
				
				//Search field hide and slide
				$('span.lp_search').hide();
				$('a.icon_search').click(function () {
					//smoothen toggle, possibly add animate function
				      $('span.lp_search').slideToggle('fast');
				      $('.lp_search input').attr("autofocus","autofocus");
				});
});