
function splitTableVertically(original)
{
	original.wrap("<div class='vertical table-wrapper' />");
	
	var copy = original.clone();
	copy.find("tr:not(:first-child)").css("display", "none");
	copy.width(original.width());
	copy.addClass("pinned");
	
	original.closest(".table-wrapper").append(copy);
	original.wrap("<div class='vertical scrollable' />");
}

/* --------------------------------
   Debugging
   --------------------------------*/
	
	var cellWidths = new Array(5);

	//$("table.responsive .pinned").width(width);
	//setTableWidth($("table.responsive"));
	
	//saveCellWidths($("table.responsive"));
	//$("table.responsive").width($("table.responsive").width() + width);
	//setCellWidths($("table.responsive"));
	
	//var width = $("table.responsive .pinned:first-child").outerWidth();
	//$("table.responsive td:not(.pinned), table.responsive th:not(.pinned)").css("left", width + "px");


	function printSavedWidths() {
		for (var i = 0; i < cellWidths.length; i++) {
			console.log("cell " + i + ": " + cellWidths[i]);
		}
	}
	
	function printCellWidths(table) {
		table.find("tr:first-child").children("td, th").each(function(i, element) {
			console.log("Cell " + i + ": " + $(element).width());
		});
	}
	
		
	function setTableWidth(table) {
		var width = 0;
		table.find("tr:first-child").children("td, th").each(function(i, element) {
			cellWidths[i] = $(this).width();
			width += $(this).outerWidth();
			console.log("Width: " + width); 
		});
		table.width(width);
	}

	function saveCellWidths(table) {
		table.find("tr:first-child").children("td, th").each(function(i, element) {
			cellWidths[i] = $(element).outerWidth();
		});
	}
	
	function setCellWidths(table) {
		for (var i = 0; i < cellWidths.length; i++) {
			console.log("Setting max width to: " + cellWidths[i]);
			table.find("td:nth-child(" + i + "), th:nth-child(" + i + ")").width(cellWidths[i]);
		}
	}
	
	$(".table-wrapper").scroll(function(e) {
		
		//var left = $(this).scrollLeft();
		//var top = $(this).scrollTop();
		//console.log("Scroll top: " + top + ", left: " + left);
		
		
		
		
		/*if ($(this).scrollLeft() > 20)
			$("table.responsive th:first-child, table.responsive td:first-child").addClass("pinned");
		else
			$("table.responsive th:first-child, table.responsive td:first-child").removeClass("pinned");*/
		
		/*var scroll = Math.max($('body').scrollTop(), $('html,body').scrollTop());
		
			var slide = $(this).closest("li");
			var top = slide.offset().top
			var bottom = top + slide.outerHeight();
			
		if (scroll > top && scroll < (top + $(this).siblings("img").outerHeight()))
			$(this).addClass("fixed");
		else $(this).removeClass("fixed");*/
		
	});