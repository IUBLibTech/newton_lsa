/* DEFAULTS */
var $searchType,//		= 	"wholedocs", 
	$chunkSize,//		= 	"ch250",  
	$outputType,//		= 	"ranked", 
	$scopeType,//		= 	"all",  
	$boundRatio,//		=	"", // <<<<<< 
	$queryElements	= 	new Array(),
	$queryString	=	'',
	$getData 		= 	'',
	$regexPattern	=	'',
	$searchURL 		= 	'docsearch.php';
	// $mdb 			= 	2;

function init() {
	
	clearQuery();
	
	var chunksAlive = new Array(0,' ');

	disableRadioButtons('lsa-chunkradio', chunksAlive);
	disableRadioButtons('lsa-outputradio');	
	disableRadioButtons('lsa-scoperadio');	
}



// tdbowman add
/* RESET QUERY */
function resetDisplay() {
	// hide rows that are by default hidden
	$('#lsa-rowThree, #lsa-rowFour, #lsa-rowFive').css({'display':'none'}).removeClass('lsa-halfOpacity');
	
	$('#lsa-selectDocEnv, #lsa-appendDocEnv, #lsa-docBoundEnv, #lsa-selectChunk250Env, #lsa-selectChunk1000Env, #lsa-appendChunkEnv, #lsa-chunkBoundEnv, #lsa-selectTerm250Env, #lsa-appendTerm250Env, #lsa-regexTermEnv, #lsa-appendregexTermEnv, #lsa-term250BoundEnv, #lsa-termdoc250BoundEnv, #lsa-selectTerm1000Env, #lsa-appendTerm1000Env, #lsa-term1000BoundEnv, #lsa-termdoc1000BoundEnv').removeClass('lsa-halfOpacity');
	
	
	$('#lsa-searchChunkDiv')
		.css({'display':'block','background-color':'#f7f6f2', 'background-image':'url("images/one.png")', 'background-position':'85% top', 'background-repeat':'no-repeat'})
		.removeClass('lsa-halfOpacity')
		.addClass('lsa-opacityNormal');

	$('#lsa-searchtype').removeClass('lsa-halfOpacity');
	$('#lsa-chunkSizeDiv, #lsa-outputEnv, #lsa-scopetypediv').css({'background-color':''}).addClass('lsa-halfOpacity');
	
	
	init();
	

}

/* GRAB ALL USER DATA */
function getUserValues() {
	
	$searchType	= 	$('input[name=lsa-searchradio]:checked','#lsa-searchtype').val(), //radio
	$chunkSize	= 	$('input[name=lsa-chunkradio]:checked',	'#lsa-chunksize').val(),  //radio
	$outputType	= 	$('input[name=lsa-outputradio]:checked','#lsa-outputtype').val(), //radio
	$scopeType	= 	$('input[name=lsa-scoperadio]:checked', '#lsa-scopetype').val();  //radio	
	// $mdb		= 	$('#lsa-mdbValue').val();
	// $hs			= 	$('#lsa-hsValue').val();
	
	
	// set boundRatio
	if ($searchType == "wholedocs") {
		$boundRatio	=	$('#lsa-bounddocs option:selected').val();	
	} else if ($searchType == "chunks") {
		$boundRatio	=	$('#lsa-boundchunk option:selected').val();
	} else if ($searchType == "terms") {
		if ($chunkSize == "ch250") {
			$boundRatio	=	$('#lsa-bound250 option:selected').val(); 
		} else if ($chunkSize == "ch1000") {
			$boundRatio	=	$('#lsa-bound1000 option:selected').val();
		}
	} else if ($searchType == 'termdoc' || $searchType == 'termquery') {
		if ($chunkSize == "ch250") {
			$boundRatio	=	$('#lsa-tdbound250 option:selected').val();
		} else if ($chunkSize == "ch1000") {
			$boundRatio = $('#lsa-tdbound1000 option:selected').val();
		}
	} else if ($searchType == "chunkterm" || $searchType == "chunkquery") {
		$boundRatio	=	$('#lsa-boundchunk option:selected').val();
	}
	
	
	// TESTING
	/*	
		console.log($searchType);
		console.log($chunkSize);
		console.log($outputType);
		console.log($scopeType);
		console.log($boundRatio);
		console.log(' ');
	*/
}
// end tdbowman add


/* CLEAR QUERY */
function clearQuery() {
	// loop through all select options and deselect
	$('input[type=select]').each(function(i) {
		$(this).removeAttr("selected");
	});
	// loop through all radio buttons and deselect
	$('input[type=radio]').each(function(i) {
		$(this).prop('checked', false);	
	});
	
	$('#lsa-theQuery').val('');
	$('#lsa-results').html('');
	$('#lsa-rowOneA').hide().children('span[title="show"]').show().end()
				 .children('span[title="hide"]').hide();
	$('#lsa-spinningImageHolder').children('span[title="message"]').empty();

	// back to default
	$searchType,//		= 	"wholedocs", 
	$chunkSize,//		= 	"ch250",  
	$outputType,//		= 	"ranked", 
	$scopeType,//		= 	"all",  
	$boundRatio,//		=	"", // <<<<<< 
	$queryElements	= 	new Array(),
	$queryString	=	'',
	$getData 		= 	'',
	$regexPattern	=	'',
	$searchURL 		= 	'docsearch.php';
	// $mdb			=	2;
	// $hs				= 	1;


}

function clearQuerySet() {
		$queryElements	= 	new Array(),
		$queryString	='';

		$('#lsa-theQuery').val('');
}

function doSearch() {

	getUserValues();
	
	/* HIDE QUERY ROWS */
	$('#lsa-rowTwo, #lsa-rowThree, #lsa-rowFour').hide();
	$('span[title="show"]').show();
    $('span[title="hide"]').hide();


	/* SHOW TAB */
	$('#lsa-rowOneA').show();
	/* SHOW SPINNING ICON */	
	$('#lsa-rowFive').show();
	
	$('#lsa-spinningImageHolder')
		.ajaxStart(function() {

			$(this).show();
			if ($searchType == "wholedocs" || $searchType == "chunks") {
				$(this).children('span[title="message"]').html('Working through 2.3 million document-document correlations greater than 0.<br/><br/>');
			}
			else if ($searchType == "terms") {
				$(this).children('span[title="message"]').html('Working through 28.3 million term-term correlations greater than 0.2.<br/><br/>');
			}
			else if ($searchType == "termdoc") {
				$(this).children('span[title="message"]').html('Working through 40.3 million term-document correlations greater than 0.<br/><br/>');
			}
			else if ($searchType == "chunkterm") {
				$(this).children('span[title="message"]').html('Working through 40.3 million chunk-term correlations greater than 0.<br/><br/>');
			}
			else if ($searchType == "termquery" || $searchType == "chunkquery") {
				$(this).children('span[title="message"]').html('Doing real-time calculations across 24,027 term vectors and 2975 chunk vectors.<br/><br/>');
			}
		})
		.ajaxStop(function() {
			$(this).hide().children('span[title="message"]').empty();		
		});


	if ($queryString == "") {
		$('#lsa-spinningImageHolder').toggle().children('span[title="message"]').html("<h2>Empty query! Please construct a query.</h2>");
		return;
	}
	if ($queryString == "ALL" && $boundRatio < 0.6) {
		alert("To search 'All documents', set the lower bound to 0.6 or higher.");
		return;
	}
	if ($outputType == "pages" && $queryElements.length > 1) {
		alert("Only one document may be selected when Output type is 'one document in page order'.");
		return;
	}
	if ($scopeType == "onlyselected" && $queryElements.length < 2) {
		alert("Select at least two documents when Scope is 'between selected documents or terms'.");
		return;
	}

	if ($searchType == "terms") { 	$searchURL = 'termsearch.php';
	} else if ($searchType == "termdoc") { $searchURL = 'termdocsearch.php';
	} else if ($searchType == "chunkterm") { $searchURL = 'chunktermsearch.php';
	} else if ($searchType == "termquery") { $searchURL = 'usersearch.php';
	} else if ($searchType == "chunkquery") { $searchURL = 'usersearch.php';
	}
	
	// $getData = "hs="+$hs;
	// $getData += "&mdb="+$mdb;
	$getData = "&list="+$searchType;
	$getData += "&frags="+ $chunkSize;
	$getData += "&scope="+ $scopeType;
	$getData += "&outf="+	$outputType;
	$getData += "&bound="+ $boundRatio;
	$getData += "&qs="+	$queryString;
	
/* CREATE AJAX CALL */
	$.ajax({
		type: "GET",
		url: $searchURL,
		data: $getData,
		success: function(data){
			$('#lsa-results').html(data);
		}
	});
	

}

/* tdbowman */
function disableRadioButtons($radioButton, $items) {
	
	$items = $items || null; // if no items

	$("input[name=lsa-"+$radioButton+"]").each(function($i,$domElem) {

        if(($items) && ($.inArray($i, $items) > -1)) { 
			$($domElem).removeAttr('disabled');	
		} else {
			$($domElem).attr('disabled', 'disabled');
		}
	});	
}


function searchTypeClick() {

	getUserValues();

	//$('input:radio[name=lsa-outputradio][value=ranked]').attr('checked', true);
	//$('input:radio[name=lsa-scoperadio][value=allcorrs]').attr('checked', true);
	
	if ($searchType == "wholedocs") {
		
		var outputAlive = new Array(0,1,4),
			scopeAlive = new Array(0,1,2);
			
		disableRadioButtons('outputradio',outputAlive);	
		disableRadioButtons('scoperadio',scopeAlive);
		
		//$('input:radio[name=lsa-outputradio][value=ranked]').attr('checked', true);		
		//$('input:radio[name=lsa-scoperadio][value=allcorrs]').attr('checked', true);
	}
	if ($searchType == "chunks") {
		var outputAlive = new Array(0,4),
			scopeAlive = new Array(0,' ');
			
		disableRadioButtons('outputradio',outputAlive);	
		disableRadioButtons('scoperadio',scopeAlive);
		
		//$('input:radio[name=lsa-outputradio][value=ranked]').attr('checked', true);		
		//$('input:radio[name=lsa-scoperadio][value=allcorrs]').attr('checked', true);	
	}
	if ($searchType == "terms") {
		var outputAlive = new Array(0,4),
			scopeAlive = new Array(0,1);
			
		disableRadioButtons('outputradio',outputAlive);	
		disableRadioButtons('scoperadio',scopeAlive);

		//$('input:radio[name=lsa-outputradio][value=ranked]').attr('checked', true);	
		//$('input:radio[name=lsa-scoperadio][value=allcorrs]').attr('checked', true);
	}
	if ($searchType == "termdoc") {
		var outputAlive = new Array(0,2,3,5),
			scopeAlive = new Array(0,3,4);
			
		disableRadioButtons('outputradio',outputAlive);	
		disableRadioButtons('scoperadio',scopeAlive);

		//$('input:radio[name=lsa-outputradio][value=bychunks]').attr('checked', true);			
		//$('input:radio[name=lsa-scoperadio][value=presence]').attr('checked', true);
	}
	if ($searchType == "chunkterm") {
		var outputAlive = new Array(0,2,3,5),
			scopeAlive = new Array(0,3,4);
			
		disableRadioButtons('outputradio',outputAlive);	
		disableRadioButtons('scoperadio',scopeAlive);
		
		//$('input:radio[name=lsa-outputradio][value=byterms]').attr('checked', true);		
		//$('input:radio[name=lsa-scoperadio][value=presentonly]').attr('checked', true);		
	}
	if ($searchType == "termquery") {
		var outputAlive = new Array(0,' '),
			scopeAlive = new Array(0,3,4);
			
		disableRadioButtons('outputradio',outputAlive);	
		disableRadioButtons('scoperadio',scopeAlive);

		//$('input:radio[name=lsa-outputradio][value=ranked]').attr('checked', true);
		//$('input:radio[name=lsa-scoperadio][value=presence]').attr('checked', true);	
	}
	if ($searchType == "chunkquery") {
		var outputAlive = new Array(0,' '),
			scopeAlive = new Array(0,3,4);
			
		disableRadioButtons('outputradio',outputAlive);	
		disableRadioButtons('scoperadio',scopeAlive);

		//$('input:radio[name=lsa-outputradio][value=ranked]').attr('checked', true);		
		//$('input:radio[name=lsa-scoperadio][value=allcorrs]').attr('checked', true);
	}
}

function outputTypeClick() {
	
	getUserValues();
	
	if ($outputType == "pages") {
		$('input:radio[name=lsa-scoperadio]:eq(1)').attr('disabled', 'disabled');
		$('input:radio[name=lsa-scoperadio]:eq(2)').attr('disabled', 'disabled');
	}
	else if ($searchType == "wholedocs") {
		$('input:radio[name=lsa-scoperadio]:eq(0)').removeAttr('disabled');
		$('input:radio[name=lsa-scoperadio]:eq(1)').removeAttr('disabled');	
		$('input:radio[name=lsa-scoperadio]:eq(2)').removeAttr('disabled');
	}
	
	if ($searchType == "terms") {
		if ($outputType == "pages") {
			$('input:radio[name=lsa-scoperadio][value=ranked]').attr('checked', true);
		}
	}
	if ($searchType == "onlyselected") {
		if ($outputType == "pages") {
			$('input:radio[name=lsa-scoperadio][value=allcorrs]').attr('checked', true);
		}
	}
	if ($searchType == "internal") {
		if ($outputType == "pages") {
			$('input:radio[name=lsa-scoperadio][value=allcorrs]').attr('checked', true);
		}
	}
}

function scopeTypeClick() {
	
	getUserValues();	
	
	if ($searchType == "terms") {
		if ($scopeType == "internal") {
			alert("'Correlations within one document' is only available when search type is 'Documents.'");
			$('#lsa-scoperadio[value=allcorrs]').attr('checked', true);
		}
	}
	if ($outputType == "pages") {
		if ($scopeType == "onlyselected") {
			alert("'Correlations between selected' not available when output type is 'Page order.'\
					Use 'All correlations' instead.");
			$('#lsa-scoperadio[value=allcorrs]').attr('checked', true);					
		}
		if ($scopeType == "internal") {
			alert("'Correlations within a document' not available when output type is 'Page order.'\
					Use 'All correlations' instead.");
			$('#lsa-scoperadio[value=allcorrs]').attr('checked', true);
		}
	}
}

function letUserWork() {

	getUserValues();
	
	/* HIDE THEM ALL */
	$('#lsa-selectDocEnv, #lsa-appendDocEnv, #lsa-docBoundEnv, #lsa-selectChunk250Env, #lsa-selectChunk1000Env, #lsa-appendChunkEnv, #lsa-chunkBoundEnv, #lsa-selectTerm250Env, #lsa-appendTerm250Env, #lsa-regexTermEnv, #lsa-appendregexTermEnv, #lsa-term250BoundEnv, #lsa-termdoc250BoundEnv, #lsa-selectTerm1000Env, #lsa-appendTerm1000Env, #lsa-term1000BoundEnv, #lsa-termdoc1000BoundEnv').hide();	
		
	/* SHOW THE MAIN ITEMS */
	$('#lsa-queryEnv, #lsa-queryButtonArea, #lsa-queryButtons').show();


	/* CLEAR OUT RESULTS AREA */
	$('#lsa-results').html('');	
	
	/* LOGIC */
	if ($searchType == "wholedocs") { 
		$('#lsa-selectDocEnv, #lsa-appendDocEnv, #lsa-docBoundEnv').show(); 
		
	} else if ($searchType == "chunks") {
		if ($chunkSize == "ch250") { 
			$('#lsa-selectChunk250Env, #lsa-appendChunkEnv, #lsa-chunkBoundEnv').show();			
		} else { 
			$('#lsa-selectChunk1000Env, #lsa-appendChunkEnv, #lsa-chunkBoundEnv').show(); 
		}
	} else if ($searchType == "terms") {
		if ($chunkSize == "ch250") { 
			$('#lsa-selectTerm250Env, #lsa-appendTerm250Env, #lsa-term250BoundEnv').show();
		} else { 
			$('#lsa-selectTerm1000Env, #lsa-appendTerm1000Env, #lsa-term1000BoundEnv, #lsa-regexTermEnv, #lsa-appendregexTermEnv').show(); 
		}
	
		$('#lsa-regexTermEnv, #lsa-appendregexTermEnv').show();
	} else if ($searchType == "termdoc") {
		if ($chunkSize == "ch250") { 
			$('#lsa-selectTerm250Env, #lsa-appendTerm250Env, #lsa-termdoc250BoundEnv').show();				
		} else { 
			$('#lsa-selectTerm1000Env, #lsa-appendTerm1000Env, #lsa-termdoc1000BoundEnv').show();
		}

		$('#lsa-regexTermEnv, #lsa-appendregexTermEnv').show();
	} else if ($searchType == "chunkterm") {
		if ($chunkSize == "ch250") {
			$('#lsa-selectChunk250Env, #lsa-appendChunkEnv, #lsa-chunkBoundEnv').show();			
		}
		else {
			$('#lsa-selectChunk1000Env, #lsa-appendChunkEnv, #lsa-chunkBoundEnv').show();				
		}
	} else if ($searchType == "termquery") {
		if ($chunkSize == "ch250") {
			$('#lsa-selectTerm250Env, #lsa-appendTerm250Env, #lsa-termdoc250BoundEnv').show();				
		} else {
			$('#lsa-selectTerm1000Env, #lsa-appendTerm1000Env, #lsa-termdoc1000BoundEnv').show();				
		}

		$('#lsa-regexTermEnv, #lsa-appendregexTermEnv').show();
	} else if ($searchType == "chunkquery") {
		if ($chunkSize == "ch250") {
			$('#lsa-selectChunk250Env, #lsa-appendChunkEnv, #lsa-chunkBoundEnv').show();	
		} else {
			$('#lsa-selectChunk1000Env, #lsa-appendChunkEnv, #lsa-chunkBoundEnv').show();			
		}
	}
}

function addTerm250ToQuery() {
	var $selectedIndex = $("#lsa-selectterm250 option").index($("#lsa-selectterm250 option:selected"));

	addItemToQuery($selectedIndex);
	updateQueryDisplay("terms250");
	return;	
}

function addTerm1000ToQuery() {
	var $selectedIndex = $("#lsa-selectterm1000 option").index($("#lsa-selectterm1000 option:selected"));
	
	addItemToQuery($selectedIndex);
	updateQueryDisplay("terms1000");
	return;	
}

function addDocToQuery() {

	getUserValues();

	var $selectedIndex = $("#lsa-selectdoc option").index($("#lsa-selectdoc option:selected"));

	if ($queryElements.length == 1 || $selectedIndex == 0) {
		if ($scopeType == "internal") {
			alert("More than one document is not an option for 'Correlations within one document.'");
			return;
		}
		if ($outputType == "pages") {
			alert("More than one document is not an option for 'Page order' output.");
			return;
		}
	}

	// if the user selects or has already selected "All documents" zero out the existing queryelements array
	if ($selectedIndex == 0 || $queryElements[0] == 0) {
		$queryElements.length = 0;
	}
	addItemToQuery($selectedIndex);
	updateQueryDisplay("docs");
	return;
}

function addChunkToQuery() {

	getUserValues();
	
	//$('#results').show();

	if ($chunkSize == "ch250") {
		var $selected = $("#lsa-selectchunk250 option").index($("#lsa-selectchunk250 option:selected"));		
	} else if ($chunkSize == "ch1000") {
		var $selected = $("#lsa-selectchunk1000 option").index($("#lsa-selectchunk1000 option:selected"));
	}

	if ($selected == 0 || $queryElements[0] == 0) {
		$queryElements.length = 0;
	}
	
	addItemToQuery($selected);
	
	if ($chunkSize == "ch250") {
		updateQueryDisplay("chunk250");
	}
	else if ($chunkSize == "ch1000") {
		updateQueryDisplay("chunk1000");
	}
	return;
}

function addRegexPatternToQuery() {

	if ($('#lsa-thePattern').val() == "") {
		return;
	}
	

	
	getUserValues();
	
	$regexPattern = new RegExp(document.getElementById('lsa-thePattern').value);


	if ($chunkSize == "ch250") {


		$i=0;
		$('#lsa-selectterm250 option').each(function() {
			if($regexPattern.test( $(this).val() )) {
				//console.log($i);
				addItemToQuery($i);
			}
			$i++;
		});
		
		updateQueryDisplay("terms250");
		
		
	} else if ($chunkSize == "ch1000") {

		$j=0;
		$('#lsa-selectterm1000 option').each(function() {
			if($regexPattern.test( $(this).val() )) {
				//console.log($j);
				addItemToQuery($j);
			}
			$j++;
		});
		
		updateQueryDisplay("terms1000");
		
	}
	return;	
}

function addItemToQuery($selectedOption) {

	if ($queryElements.indexOf($selectedOption) > -1) {
		return;
	}
	
	$queryElements.push($selectedOption);
	$queryElements.sort(function (a,b) {return a-b });

	return;
}



function updateQueryDisplay($selectSet) {

	$('#lsa-theQuery').empty();
	
	var $newString 	= "",
		$newDisplay = "";

	for (i = 0; i < $queryElements.length; i++) {
	
		if (i > 0) {
			$newString = $newString + "_";
			$newDisplay = $newDisplay + "\n";
		}
		if ($selectSet == "docs") {
			$newString = $newString + $('#lsa-selectdoc option:eq('+$queryElements[i]+')').val();
			$newDisplay = $newDisplay + $('#lsa-selectdoc option:eq('+$queryElements[i]+')').text();
		}
		else if ($selectSet == "terms250") {
			$newString = $newString + $('#lsa-selectterm250 option:eq('+$queryElements[i]+')').val();
			$newDisplay = $newDisplay + $('#lsa-selectterm250 option:eq('+$queryElements[i]+')').text();			
		}
		else if ($selectSet == "terms1000") {
			$newString = $newString + $('#lsa-selectterm1000 option:eq('+$queryElements[i]+')').val();
			$newDisplay = $newDisplay + $('#lsa-selectterm1000 option:eq('+$queryElements[i]+')').text();			
		}
		else if ($selectSet == "chunk250") {
			$newString = $newString + $('#lsa-selectchunk250 option:eq('+$queryElements[i]+')').val();
			$newDisplay = $newDisplay + $('#lsa-selectchunk250 option:eq('+$queryElements[i]+')').text();	
		}
		else if ($selectSet == "chunk1000") {
			$newString = $newString + $('#lsa-selectchunk1000 option:eq('+$queryElements[i]+')').val();
			$newDisplay = $newDisplay + $('#lsa-selectchunk1000 option:eq('+$queryElements[i]+')').text();	
		}
	}
	
	$queryString = $newString;
	$('#lsa-theQuery').val($newDisplay);
}

function openViewer($chset, $chunk1, $chunk2, $correlation) {
	window.open("displaycorrs.php?frags="+$chset+"&doc1="+$chunk1+"&doc2="+$chunk2+"&corr="+$correlation);
}

function openTDViewer($chset, $chunk, $term, $correlation) {
	window.open("displayTDdoc.php?frags="+$chset+"&doc="+$chunk+"&term="+$term+"&corr="+$correlation);
}
