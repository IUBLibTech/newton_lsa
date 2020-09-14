$(document).ready(function() {
	
	// set up jQuery modal dialog box for symbol search
	if($('#symbolDrownDown').length > 0) {
		$("#symbolDrownDown").dialog({
			autoOpen: false,
			height: 300,
			width: 580,
			modal: true,
			buttons: {
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			open: function(event,ui) {
				 $(this)
					.parents(".ui-dialog:first")
					.find(".ui-widget-header")
					.removeClass("ui-widget-header")
					.addClass("ui-widget-header-custom");
			},
			close: function() {
				$(this).attr('checked','');
				$('#search1').attr('checked','checked');
			}
		});
	}
	
	if($('#search2').length > 0) {
		$('#search2').on('click', function(i,e) {
			// jQuery modal dialog box 
			$("#symbolDrownDown").dialog("open");
			// now load the symbol images by changing the 'src' attribute value...
			$('img.symbolimage').each(function(i) {
				var $imgSource = $(this).attr('data-original');
				$(this).attr('src', $imgSource);
			});
		});
	}
	
	if($('#advSearch').length > 0) {
		$('#advSearch').on('click', function(i,e) {
			e.preventDefault(); // cancel default behavior
			window.location = './advsearch';
		});
	}	
});