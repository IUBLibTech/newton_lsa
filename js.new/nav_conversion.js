// DOM ready
$(function() {
	
	// Create the dropdown base
	$("<select />").appendTo("nav").append('\n');

	
	// Create default option "Go to..."
	$("<option />", {
	 "selected": "selected",
	 "value"   : "",
	 "text"    : "Go to..."
	}).append('\n').appendTo("nav select").append('\n');

	
	// Populate dropdown with menu items
	$("ul#navigation li a.primaryNav").each(function() {
	
		var el = $(this);
		$("<option />", {
		   "value"   : el.attr("href"),
		   "text"    : el.text()
		}).append('\n').appendTo("nav select").append('\n');			
				
		if ($(this).parent().has("span.subNav").length){
			$(this).parent().find('span.subNav a').each(function() {	
				var el = $(this);
				$("<option />", {
				   "value"   : el.attr("href"),
				   "text"    : " - "+el.text()
				}).append('\n').appendTo("nav select").append('\n');
			});
		} 

	});

	
	// To make dropdown actually work
	// To make more unobtrusive: http://css-tricks.com/4064-unobtrusive-page-changer/
	$("nav select").change(function() {
		window.location = $(this).find("option:selected").val();
	});

});