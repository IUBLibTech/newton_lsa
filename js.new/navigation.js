/*************JAVASCRIPT*************************
Author: TDBowman
Version: 1.0
************************************************/
/* BASED ON NAVIGATION THAT LOOKS LIKE:
// nav
<ul id="navigation">
	<li class="noChildren"><a id="browse" href="<html:rewrite page="/browse"/>" class="primaryNav" title="Browse Newton's Manuscripts">Browse Manuscripts</a></li>
	<li><a id="tools" href="#footerNav" class="primaryNav" title="Online Tools (click to see navigation options)">Online Tools</a></li>
	...
</ul>
// sub nav
<ul id="sub_tools" class="subnavigation">
				<li><a id="sub_glossary" title="Online Tools - Alchemical Glossary" href="<html:rewrite page="/reference/glossary.do"/>">Alchemical Glossary</a></li>
				<li><a id="sub_indexChemicus" title="Online Tools - Index Chemicus" href="<html:rewrite page="/indexChemicus.do"/>">Index Chemicus</a></li>
	...
</ul>
*/

//from http://stackoverflow.com/a/7704678
function HoverWatcher(selector){
  this.hovering = false;
  var self = this; 
  this.isHoveringOver = function() { 
    return self.hovering; 
  } 
    $(selector).hover(function() { 
      self.hovering = true; 
    }, function() { 
      self.hovering = false; 
    }) 
} 

// default vars and object
var $subNavWatcher, $mySubID, $myID, $defaults = {
	goVal: '',
	mss: '',	
	showText: 	'&#x25BC;',
	hideText: 	'&#x25B2;'
}	
var showNav = function() {
	if($(this).hasClass('noChildren') ) { 
		// just open URL from href attribute like normal
		$(this).css({'background-color':'#2D2921'});
	} else {
		$('ul[id^="sub_"]').hide();
		$(this).children('.toggleLink').html($defaults.hideText);
		$(this).css({'background-color':'#2D2921'});
		$myID = $(this).children('a').attr('id'); // grab the ID we need
		$mySubID = 'sub_'+$myID; // change for our naming convention
		$('#'+$mySubID).show();
		$subNavWatcher = new HoverWatcher('ul#'+$mySubID); // watch this
	}
}	
var hideNav = function() {
	if($subNavWatcher.isHoveringOver()) {
		$('#'+$mySubID).hoverIntent({
			sensitivity: 5, // number = sensitivity threshold (must be 1 or higher)
			interval: 500,   // number = milliseconds of polling interval
			over: showSub,  // function = onMouseOver callback (required)
			timeout: 700,   // number = milliseconds delay before onMouseOut function call
			out: hideSub    // function = onMouseOut callback (required)
		}).mouseleave(function(){
			hideSub();
		});
	} else {
		$('ul[id^="sub_"]').hide();
		$('.toggleLink').html($defaults.showText);	
		$(this).css({'background-color':''})
	}
}
var showSub = function() {
	// keep subNav open by doing nothing
}	
var hideSub = function() {
	$('ul[id^="sub_"]').hide();
	$('.toggleLink').html($defaults.showText);
}

// jQuery
$(document).ready(function(){

/******** GET CURRENT PAGE AND CLEAN IT UP ********/
	// scrape URL and get current page to trigger navigation
	var pageDo, $URL, $goVal, $length, $parent, $getIt;
	$pageDo = window.location.pathname; // changed			

	if($pageDo.search(/.do/i) != -1) {
		//console.log('here');	
		$URL = $pageDo.split('/');
		$goVal = $URL.pop();
		$strLength = $goVal.length;
		$strLength = $strLength - 3;
		$goVal = $goVal.substr(0,$strLength);
		$defaults.goVal = $goVal;
	} else if($pageDo.search(/search/i) != -1) {
		$URL = $pageDo.split('/');
		$goVal = $URL.pop();
		if($goVal == 'search') {
			$goVal = 'browse';
		}
		$defaults.goVal = $goVal;
	} else if($pageDo.search(/browse/i) != -1) {
		$goVal = 'browse';
		$defaults.goVal = $goVal;
	} else if($pageDo.search(/mss/i) != -1) {
		$URL = $pageDo.split('/');
		$getIt = Number($URL.length - 2);
		$mss = $URL[$getIt];
		$goVal = 'browse';
	
		$defaults.goVal = $goVal;
		$defaults.mss = $mss;
	}
/******** END GET CURRENT PAGE AND CLEAN IT UP ********/		

/******** BEGIN NAVIGATION MACHNIERY ********/
	$('ul.subNav').hide();
	$('#navigation li').filter(':not(".noChildren")').each(function(i) {
		// $(this).append('<span class="toggleLink">'+$defaults.showText+'</span>');
		$(this).on('click', function(e) {
			return false;
		});
	});
		
	// hoverIntent jQuery plugin
	$("#navigation li").filter(':not(".noChildren")').hoverIntent({
		sensitivity: 7, // number = sensitivity threshold (must be 1 or higher)
		interval: 200,   // number = milliseconds of polling interval
		over: showNav,  // function = onMouseOver callback (required)
		timeout: 200,   // number = milliseconds delay before onMouseOut function call
		out: hideNav    // function = onMouseOut callback (required)
	});	
	
	$('li.noChildren').on('mouseover', function() {
		$(this).css({'background-color':'#2D2921'});
	}).on('mouseout', function() {
		$(this).css({'background-color':''});
	});
	
/******** END NAVIGATION MACHNIERY ********/
	
/******** TRIGGER NAVIGATION INTERNALLY ********/
	if($defaults.goVal) {
		if($defaults.goVal != 'browse') {
			// get parent
			$parent = $('#sub_'+$defaults.goVal).closest('ul').attr('id')+'';
			$parent = $parent.substr(4)
			// main nav
			$('#'+$parent).parent().css({'background-color':'#2D2921'}).children('.toggleLink').html($defaults.hideText);	
			// sub nav
			$('#sub_'+$defaults.goVal).css({'background-color':'#423C30', 'color':'#F7F6F0', 'border':'thin solid #F7F6F0','border-bottom':'none','font-weight':'bold'}).parent().prev().andSelf().css({'border':'none'}).parent().show();
			// footer nav
			$('#footer_'+$defaults.goVal).css({'background-color':'inherit', 'color':'#F7F6F0', 'border':'0','padding':'0.3em 0.3em', 'text-decoration':'underline'}).parent().prepend('<strong style="color:#F7F6F0;"> &raquo; </strong>');
		} else if($defaults.goVal == 'browse') {
			// browse nav
			$('#'+$defaults.goVal).parent().css({'background-color':'#2D2921'}).children('.toggleLink').html($defaults.hideText);			
		}
	}	
/******** END TRIGGER NAVIGATION ********/
});			