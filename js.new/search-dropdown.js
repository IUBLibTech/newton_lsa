$(document).ready(function() {
	// http://tympanus.net/Tutorials/UIElements/SearchBox/
	//
	// the element
	// set keyword field to a default value
	//
	var $ui = $('#sb');
	$ui.find('#sb_keyword').val('Click to Search').css({'color':'#CCC'});
	
	$ui.on({
	  click: function(){
		$(this).find('#sb_keyword').val('').css({'color':'#000'});
	
		$ui.find('.sb_arrow_down')
		   .addClass('sb_arrow_up')
		   .removeClass('sb_arrow_down')
		   .andSelf()
		   .find('#sb_dropdown')
		   .show();
		   
		
	  },
	   mouseleave: function(){

		$ui.find('.sb_arrow_up')
		   .addClass('sb_arrow_down')
		   .removeClass('sb_arrow_up')
		   .andSelf()
		   .find('#sb_dropdown')
		   .slideUp("slow");  
	  }
	});	
	
	$ui.find('#sb_keyword').bind('focus click',function(){
		$(this).val('').css({'color':'#000'});
		$ui.find('.sb_arrow_down')
		   .addClass('sb_arrow_up')
		   .removeClass('sb_arrow_down')
		   .andSelf()
		   .find('#sb_dropdown')
		    .show();
	});
	$ui.bind('mouseleave',function(){
		$ui.find('.sb_arrow_up')
		   .addClass('sb_arrow_down')
		   .removeClass('sb_arrow_up')
		   .andSelf()
		   .find('#sb_dropdown')
		   .slideUp("slow");  
	});	

	$ui.find('#arrow').on('click', function() {
		$(this).toggle(function() {
			$(this)
			   .addClass('sb_arrow_up')
			   .removeClass('sb_arrow_down')
			   .andSelf()
			   .parent().find('#sb_dropdown')
			   .slideDown("slow");
		}, function(event) {
			$(this)
			   .addClass('sb_arrow_down')
			   .removeClass('sb_arrow_up')
			   .andSelf()
			   .parent().find('#sb_dropdown')
				.slideUp("slow")			
		});
	});	
	//
	// on mouse leave hide the dropdown, 
	// and change the arrow image
	//
	
	$ui.find('#sb_keyword').blur(function(){
		$(this).val('Click to Search').css({'color':'#CCC'});
	})
	//
	// selecting all checkboxes
	//
	$ui.find('#sb_dropdown').find('label[for="all"]').next().bind('click',function(){
		$(this).parent().siblings().find(':checkbox').attr('checked',this.checked).attr('disabled',this.checked);
	});

});