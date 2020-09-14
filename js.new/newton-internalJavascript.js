// :random() selector //
// from http://blog.mastykarz.nl/jquery-random-filter/   //
jQuery.jQueryRandom = 0;
jQuery.extend(jQuery.expr[":"],
{
	random: function(a, i, m, r) {
		if (i == 0) {
			jQuery.jQueryRandom = Math.floor(Math.random() * r.length);
		};
		return i == jQuery.jQueryRandom;
	}
});	

$(document).ready(function() {
	
	/******** MAKE AUTO LIST OF ITEMS IN GLOSSARY ********/	
	if($('#myList').length > 0) {
		$('#myList').listnavTB({ 
			//initLetter: 'c', 
			includeNums: false, 		
			includeAll: true, 
			includeOther: false, 
			flagDisabled: true, 
			noMatchText: 'Nothing matched your filter, please click another letter.', 
			showCounts: true, 
			prefixes: ['the','a'] 
		});
	}
	/******** END MAKE AUTO LIST OF ITEMS IN GLOSSARY ********/	

	/******** FLOATING ADVERT BOX ********/	
	/*
	if($('#ajaxBox').length > 0) {
		/* CHANGE HERE TO MAKE IT FOLLOW ALWAYS 
		 /* $('#ajaxBox').stickyfloat({'duration': 400}); 
		 
		 var fixed = false;

		$(document).scroll(function() {
			if( $(this).scrollTop() >= 100 ) {
				if( !fixed ) {
					fixed = true;
					$('#ajaxBox').css({position:'fixed',top:80});
				}
			} else {
				if( fixed ) {
					fixed = false;
					$('#ajaxBox').css({position:'static'});
				}
			}
		});
		 
	}
	*/
	/******** END FLOATING ADVERT BOX ********/	

	/******** AJAX GLOSSARY SEARCH ********/	
	if($('dt').length > 0) {
		$('dt').each(function(i) {
			
			$(this).click(function() {
				$('#ajaxHolder').empty().css({'background':'url("/newton-dev/images/icons/loading.gif") no-repeat center center'});
				var $item = $(this).html();
	
				// begin AJAX search 
				var $ajaxObj = {
					url: '/newton-dev/search?&',	
					data: 'field1=text&text1='+encodeURI($item),
					dataType: 'html'	
				}
				
				$("#ajaxHolder").load($ajaxObj.url+$ajaxObj.data+" .browseRowWrapper", function(response, status, xhr) {
					if (status == "error") {
						var msg = "Sorry but there was an error: ";
						$('#ajaxTitle').text('ERROR');
						$("#axaxHolder").html(msg + xhr.status + " " + xhr.statusText);
					} 
					
					if (status == "success") {
						$('#ajaxHolder').css({'background':'none'}).removeClass('mediumPadding');
						$("#ajaxTitle h3").text($item); 
						
						$(this).children('.browseRowWrapper').each(function(i,e) {
							$(this).children('.browseRowLinks a:contains("Introduction")').css({'display':'none'});
							$(this).find('.longDisplayButton').css({'display':'none'});
							$(this).children('.browseRowInfo').css({'display':'none'});
							$(this).children('.snippets').css({'display':'none'});	
							$(this).css({'margin-bottom':'1em'});										
						});
										
						
						$('div.normDiplButton-Holder').children('.norm.').each(function() {
							var $myHref = $(this).attr('href');		
							$(this).attr({'href':'/newton-dev/'+$myHref});						
						});
						
						$('div.normDiplButton-Holder').children('.dipl.').each(function() {
							var $myHref = $(this).attr('href');		
							$(this).attr({'href':'/newton-dev/'+$myHref});						
						});
							
						
											
						
					}
				});
			});		
		});
	}
	/******** END AJAX GLOSSARY SEARCH ********/		
	
	
	/******** SYMBOL GUIDE TABLE SORT ********/
	if($('#symbolTableGuideOne').length > 0) {
		$("#symbolTableGuideOne").tablesorter(); 
		$("#symbolTableGuideTwo").tablesorter(); 	
	}
	/******** END SYMBOL GUIDE TABLE SORT ********/	
	
	/******** MAKE AUTO LIST OF ITEMS IN INDEX CHEMICUS ********/
	if($('#chemicusList').length > 0) {
		
		$('#chemicusList').listnav({ 
			initLetter: 'a',
			includeNums: false, 
			includeAll: false, 
			includeOther: false, 
			flagDisabled: true, 
			noMatchText: 'Nothing matched your filter, please click another letter.', 
			showCounts: true, 
			prefixes: ['the','a'] 
		});
		/******** END MAKE AUTO LIST OF ITEMS IN GLOSSARY ********/		

		/******** AJAX INDEX CHEMICUS ********/	
		$('#chemicusList li').each(function(i) {
			
			$(this).on('click', function(e) {	
				
				$('#ajaxHolder').css({'background':'url("/newton-dev/images/icons/loading.gif") no-repeat center center'});
				var $anchorID, $myURL, $myTitle;
				$anchorID = $(this).children('a').attr('id');
				$myTitle = $(this).children('a').text();
				$myUrl = 'indexChemicus/pages/' + $anchorID + '.txt';
	
				$("#ajaxHolder").load($myUrl + ' .contentShell:not(".headingIndexChemicus")', function(response, status, xhr) {
					if (status == "error") {
						var msg = "Sorry but there was an error: ";
						$('#ajaxTitle').text('ERROR');
						$("#axaxHolder").html(msg + xhr.status + " " + xhr.statusText);
					} 
					
					if (status == "success") {
						$('#ajaxHolder').css({'background':'none'});
						$('#ajaxTitle h3').text($myTitle); 
					}
					
				});
				return false;
			});
	
		});
		
	}//end if
	/******** END AJAX INDEX CHEMICUS ********/	


	/******** AJAX SUSSEX RSS FEED ***********/
	if($defaults.goVal == "related") {
		var $sussexFeed = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20rss%20where%20url%20%3D%20%22http://www.newtonproject.sussex.ac.uk/rss/featured.xml%22";
		
		$.ajax({
			url: $sussexFeed,
			type: 'GET',
			cache: false,
			dataType: 'xml',
			success: function(xml) {
			
				var $myItem = $(xml).find('item:random');
					$myTitle = $($myItem).find('title').text(),
					$myLink = $($myItem).find('link').text(),
					$myDescription = $($myItem).find('description').text(),	
					
					$disclaimer = $('<p></p>').addClass('smaller').html('...from <a href="http://www.newtonproject.sussex.ac.uk/" target="_blank">The Newton Project</a>, University of Sussex, <a href="http://www.newtonproject.sussex.ac.uk/rss/featured.xml" target="_blank">RSS Feed</a>');
					$paraElement = $('<p></p>').addClass('smaller').text($myDescription),
					$anchElement = $('<p></p>').addClass('smaller').append($('<a/>').attr({'src':$myLink, 'target':'_blank'}).text($myLink));
					
					
				$('#ajaxHolder').css({'background':'none'}).empty();
				$('#ajaxTitle h3').text($myTitle).addClass('smaller').css({'padding':'0.5em'});; 
				$('#ajaxHolder').append($anchElement, $paraElement, $disclaimer);			
				
			}
		});
	}
	/******** END AJAX SUSSEX RSS FEED ********/

	/******** TOGGLE BIO DISPLAYS (personnel.do) ********/
	if($('a[id^="bio-"]').length > 0) {
		
		$('a[id^="bio-"]').on('click', function() {
			$('#ajaxHolder p.default').hide();
			$('#ajaxHolder').children('div[id=^"bio-"]').each(function(e) {
				$(this).hide();
			});
			
			var $teamMember = $(this).attr('id');
			$('#'+$teamMember+'-Display').toggle();
			return false;
		});
	}
	/******** END TOGGLE BIO DISPLAYS (personnel.do) ********/		


	/*******************************************************************/
	/****************** BROWSE / VIEW / SEARCH *************************/
	/*******************************************************************/	
	/******** TOGGLE LONG/SHORT BROWSE & SEARCH DISPLAYS ********/
	if($('a.browseLongDisplay').length > 0) {	
		$('a.browseLongDisplay').on('click', function(e) {
			var $myTitle = $(this).attr('title');
			$('.longDisplay.show_'+$myTitle).toggle();
			var theButtonText = $(this).text();
			if(theButtonText == "Short Display") {
			$(this).text('Long Display');
			} else {
			$(this).text('Short Display');
			}
			return false;
		});
	}
	/******** END TOGGLE LONG/SHORT BROWSE & SEARCH DISPLAYS ********/		
	
	/******** TOGGLE MSS INFORMATION DISPLAY ********/
	if($('a.manuInfo').length > 0) {	
		$('a.manuInfo').on('click', function(e) {
			$('#metadataDialog').slideToggle('slow',function() {
				// animation complete				
			});
			return false;
		});
	}
	/******** END TOGGLE MSS INFORMATION DISPLAY ********/		
});