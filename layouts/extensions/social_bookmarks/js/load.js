// Molajo Project
// Load Social Bookmarks Buttons
// Copyright (C) 2011 Amy Stephen. All rights reserved.
// License GNU General Public License version 2 or later http://www.gnu.org/licenses/gpl.html
$.ajax({
	url: "reload-content.html",
	cache: false,
	success: function(html){
		$("#results").html(html);
    	twttr.widgets.load();
    	try{
			FB.XFBML.parse();
    	}catch(ex){}
  	}
});

