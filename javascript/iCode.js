// JavaScript Document
/* jQUERY */

var $njq = jQuery.noConflict();

//simple popup function
function popwindow(e){
	e.preventDefault();
	var href=$njq(e.target).attr('href');
	window.open(href,'','menubar=1,location=1,toolbar=1,scrollbars=1,width=725,height=450,resizable');
};

$njq(document).ready(function(){	
	// taken from http://bassistance.de/2007/01/23/unobtrusive-clear-searchfield-on-focus/		   
	$njq.fn.search = function() {
		return this.focus(function() {
			if( this.value == this.defaultValue ) {
				this.value = "";
			}
		}).blur(function() {
			if( !this.value.length ) {
				this.value = this.defaultValue;
			}
		});
	};

	$njq('#newton-searchForm #s').search();

	$njq('#newton-searchForm #s').bind({
		focusin: function() {
			if ($njq("#searchHelp").is(":hidden")) {
				$njq("#searchHelp").slideDown("slow");
			}
		},
		focusout: function() {
			if($njq('#searchHelp').is(':visible')) {
				$njq("#searchHelp").delay(700).slideUp("slow");
			}
		}		
	});
	
	// for popup window
	$njq('.popupWindow').bind('click',function(e){
		popwindow(e);
	});
	
	
	$njq('ul.sf-menu').supersubs({ 
			minWidth:    12,   // minimum width of sub-menus in em units 
			maxWidth:    27,   // maximum width of sub-menus in em units 
			extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
												 // due to slight rounding differences and font-family 
	}).superfish();  // call supersubs first, then superfish, so that subs are 
									 // not display:none when measuring. Call before initialising 
									 // containing tabs for same reason.  
	
			
	/* BROWSE / SEARCH PAGE */		
	//Hide (Collapse) the toggle containers on load
	$njq('div.longDisplay').hide(); 
	
	
	//Slide up and down on click
	$njq('.dispLink').click(function(){
		var divID = $njq(this).attr('title');	
		$njq('div#'+divID).slideToggle('slow');
	
	}).toggle(  //Switch the 'Open' and 'Close' state per click
				function(){
					$njq(this).html('Short Display');
				}, function () {
					$njq(this).html('Long Display');
	});
	
	$njq('div.rowBrowseResult:even').addClass('browse-even');
	$njq('div.rowBrowseResult:odd').addClass('browse-odd');

	// manuscript information dialog popup
	$njq('a.manuInfo').click(function() {
		$njq('div#metadataDialog').dialog({ 
			autoOpen: false,
			width: '500px',
			title: 'Manuscript ' + $njq(this).attr('title')
		});
		
		$njq('div#metadataDialog').dialog('open');
	});

	// translation information dialog popup
	$njq('a.transLink').click(function() {
		$njq('div#panel_' + $njq(this).attr('id')).dialog({ 
			autoOpen: false,
			width: '300px',
			title: 'Translation'
		});
		
		$njq('div#panel_' + $njq(this).attr('id')).dialog('open');
	});
});