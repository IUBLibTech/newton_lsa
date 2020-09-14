$(document).ready(function() {
	// WALLY'S INITIALIZE FUNCTION
		init();	
		

	// ************************************************* //		
	// ROW TWO - CHOOSE SEARCH TYPE
		$('#lsa-searchtype input[type=radio]').on('click', function(e) {

			searchTypeClick(e);

			$('#lsa-chunkSizeDiv, #lsa-chunksize').removeClass('lsa-halfOpacity').addClass('lsa-opacityNormal');		
		});
	
	// ROW TWO - CHOOSE CHUNK SIZE	
		$('#lsa-chunksize input[type=radio]').on('click', function(e) {

			$('#lsa-searchChunkDiv, #lsa-chunkSizeDiv').removeClass('lsa-boxOne lsa-opacityNormal').addClass('lsa-halfOpacity');

			$('#lsa-outputEnv').addClass('lsa-boxTwo lsa-opacityNormal')
				.removeClass('lsa-halfOpacity');
		});
	
	// ROW TWO - CHOOSE OUTPUT TYPE	
		$('#lsa-outputtype input[type=radio]').on('click', function(e) {
			outputTypeClick(e);
			$('#lsa-outputEnv').removeClass('lsa-boxTwo lsa-opacityNormal')
				.addClass('lsa-halfOpacity');
			
			$('#lsa-scopetypediv').addClass('lsa-boxThree lsa-opacityNormal')
				.removeClass('lsa-halfOpacity');
		});
	// ROW TWO - CHOOSE SCOPE
		$('#lsa-scopetype input[type=radio]').on('click', function(e) {
			scopeTypeClick(e);
		});
	// ROW TWO - SUBMIT SELECTIONS [CONTINUE BUTTON]
		$('#lsa-submitQuery input[type=submit]').on('click', function(e) {
			letUserWork();
			$('#lsa-scopetypediv').removeClass('lsa-boxThree lsa-opacityNormal')
				.addClass('lsa-halfOpacity');	
			//$(this).attr('disabled', 'disabled').addClass('halfOpacity');
			$('#lsa-rowThree').show().addClass('lsa-boxFour lsa-opacityNormal')
				.removeClass('lsa-halfOpacity');
			
			
			$('#lsa-rowTwo').toggle();	
			$('span.instructions').text('Add documents or chunks or terms, then click the Continue button and proceed to step 5.');
			return false;
		});
		
	// ************************************************* //	
	// ROW THREE - ADD DOC
		$('#lsa-appendDocPress').on('click', function() {
			addDocToQuery();
			return false;
		});
	// ROW THREE - ADD CHUNK
		$('#lsa-appendChunkPress').on('click', function(i,e) {
			addChunkToQuery()
			return false;
		});
	// ROW THREE - ADD TERM	
		$('#lsa-appendTerm250Button input[type=button]').on('click', function() {
			addTerm250ToQuery();
			return false;
		});
	// ROW THREE - ADD TERM	
		$('#lsa-appendTerm1000Button input[type=button]').on('click', function() {
			addTerm1000ToQuery();
			return false;
		});
	// ROW THREE - ADD MATCHES	
		$('#lsa-appendregexTermButton input[type=button]').on('click', function() {
			addRegexPatternToQuery();
			return false;
		});	
		
	// ROW THREE - ADD TERMS & REGULAR EXPRESSION
	$('#lsa-queryFormContinue input[type=submit]').on('click', function(i,e) {
			$('#lsa-rowThree').removeClass('lsa-boxFour lsa-opacityNormal').addClass('lsa-halfOpacity').toggle();	
			$('#lsa-rowFour').show().addClass('lsa-boxFive lsa-opacityNormal').removeClass('lsa-halfOpacity');
			$('span.instructions').text('Select a Threshold (we recommend 0.6 as a starting point) and click the Run button to generate query results.');
			return false;
		});		
	// ROW THREE - QUERY SET CLEAR
	$('#lsa-queryFormClearButton input[type=submit]').on('click', function(i,e) {
		clearQuerySet();
		return false;
	});	
		
		
	
	// ************************************************* //	
	// ROW FOUR - RUN QUERY
		$('#lsa-runSearchPress').on('click', function(i,e) {
			$('#lsa-rowFour').removeClass('lsa-boxFive lsa-opacityNormal')
					.addClass('lsa-halfOpacity').toggle();	
	
			$('span.instructions').text('Clicking on a result will open a new window.');
	
			doSearch();
			return false;
		});
	// ROW FOUR - CLEAR SEARCH
		$('#lsa-clearSearchPress').on('click', function(i,e) {
			clearQuery();
			resetDisplay();
			return false;
		});
	
	// ************************************************* //	
	// TAB TO SHOW QUERY ROWS 
		$('#lsa-rightSideTab a').on('click', function() {
			$(this).children('span[title="show"]').toggle().end()
				   .children('span[title="hide"]').toggle();
				   
			$('#lsa-rowTwo, #lsa-rowThree, #lsa-rowFour').toggle().removeClass('lsa-halfOpacity');
			$('#lsa-rowTwo, #lsa-rowThree, #lsa-rowFour').children().removeClass('lsa-halfOpacity');
			
			$('#lsa-rowThree, #lsa-rowFour').css({'background-color':'none'});
			$('#lsa-chunkSizeDiv, #lsa-searchChunkDiv, #lsa-outputEnv, #lsa-scopetypediv').removeClass('lsa-halfOpacity').css({'background-color':''});		
			
		});

	// *************************************************//
			
	// ************************************************* //	
	// POPUP jQuery CODE
			$('a.helpLink').popupWindow({ 
				height:500, 
				width:800, 
				resizable:1,
				scrollbars:1,
				centerBrowser:1  
			}); 

	// *************************************************//
	
		
	
	
	});