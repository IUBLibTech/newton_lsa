$(document).ready(function() {
	// WALLY'S INITIALIZE FUNCTION
		init();	
		
	// ************************************************* //	
	// initializing the five select2 lists
		// $("#lsa-wholeDocs-select2").select2({ placeholder: "Type to view documents, click to select or remove"});
		// $("#lsa-chunk250-select2").select2({ placeholder: "Type to view passages, click to select or remove"});
		// $("#lsa-chunk1000-select2").select2({ placeholder: "Type to view passages, click to select or remove"});
		// $("#lsa-term250-select2").select2({ placeholder: "Type to view terms, click to select or remove"});
		// $("#lsa-term1000-select2").select2({ placeholder: "Type to view terms, click to select or remove"});

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
		$('#lsa-beginQuery input[type=submit]').on('click', function(e) {
			letUserWork();  // clears and sets up interface
			
			const $searchType	= 	$('input[name=lsa-searchradio]:checked','#lsa-searchtype').val(); //radio
			const $chunkSize	= 	$('input[name=lsa-chunkradio]:checked',	'#lsa-chunksize').val();  //radio

			// console.log($searchType);
			// console.log($chunkSize);
			// alert("check type and size from init");

			let $el = null;
			let $sourceList = '';
			let cacheKey = '';

			if ($searchType == 'wholedocs') {
				$el = $('#lsa-wholeDocs-select2');
				$sourceList = 'json/corpus_list.json';
				cacheKey = 'lsaCorpusCache';
				promptText = "Type to view documents, click to select or remove";
				$("#lsa-wholeDocsEnv").show();
			}
			else if ($searchType == 'chunks'  && $chunkSize == 'ch250') {
				$el = $('#lsa-chunk250-select2');
				$sourceList = 'json/doc250_list.json';
				cacheKey = 'lsaDoc250Cache';
				promptText = "Type to view passages, click to select or remove";
				$("#lsa-selectChunk250Env").show();
			}
			else if ($searchType == 'chunks'  && $chunkSize == 'ch1000') {
				$el = $('#lsa-chunk1000-select2');
				$sourceList = 'json/doc1000_list.json';
				cacheKey = 'lsaDoc1000Cache';
				promptText = "Type to view passages, click to select or remove";
				$("#lsa-selectChunk1000Env").show();
			}
			else if ($searchType == 'terms' || $searchType == 'termdoc') {
				if ($chunkSize == 'ch250') {
					$el = $('#lsa-term250-select2');
					$sourceList = 'json/term250_list.json';
					cacheKey = 'lsaTerm250Cache';
					promptText = "Type to view terms, click to select or remove";
					$("#lsa-selectTerm250Env").show();
				}
				else if ($chunkSize == 'ch1000') {
					$el = $('#lsa-term1000-select2');
					$sourceList = 'json/term1000_list.json';
					cacheKey = 'lsaTerm1000Cache';
					promptText = "Type to view terms, click to select or remove";
					$("#lsa-selectTerm1000Env").show();
				}
			}
			else {
				console.log('parameters failed the searchType + chunkSize test');
				return false;
			}
			
			if (!$el.data('loaded')) {
				$el.html('<option>Loading options...</option>').trigger('change');

				$.getJSON($sourceList, function(response) {
					$el.empty();
					// sanitize int ids in json data
					const sanitizedResults = response.results.map(function(item) {
						return {
							id: String(item.id),
							text: item.text
						};
					});

					const isLargeDataset = (sanitizedResults.length > 500);

					$el.select2({
						data: sanitizedResults,
						placeholder: promptText,
						minimumInputLength: isLargeDataset ? 1 : 0,
						allowClear: true,
						closeOnSelect: false
					});

					// save the raw results onto the element for the regex function
					$el.data('rawDataset', sanitizedResults);

					$el.data('loaded', true);
					$el.select2('open');
				}).fail(function() {
					$el.html('<option>Error loading list. Please refresh.</option>');
				});
			} else {
				$el.select2('open');
			}
			
			// prepare main screen display changes
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
	// ROW THREE - REMOVE ITEM FROM QUERY SET
	$('#lsa-queryRemoveSelectedButton').on('click', function() {
		removeOptionsFromQuery();
	});		
	// ROW THREE - QUERY SET CLEAR
	$('#lsa-queryFormClearButton input[type=submit]').on('click', function(i,e) {
		clearQuerySet();
		return false;
	});	
		

	// ************************************************* //	
	// ROW FOUR - RUN QUERY
		$('#lsa-runSearchPress').on('click', function(i,e) {
			// alert("have entered Run button code.")
			$('#lsa-rowFour').removeClass('lsa-boxFive lsa-opacityNormal')
					.addClass('lsa-halfOpacity').toggle();	
	
			$('span.instructions').text('Click on SHOW_QUERY_TOOL at right to modify a query and further explore the results.  ———  Clicking on a result in a list will open a new window.');
	
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

		// ROW FIVE - GRAPH INTERACTION

			
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

$(document).on('select2:select select2:unselect',
	'#lsa-wholeDocs-select2, #lsa-chunk250-select2, #lsa-chunk1000-select2, #lsa-term250-select2, #lsa-term1000-select2',
	function(e) {
		// get the option that was clicked
		const clickedData = e.params.data;
		const optionId = clickedData.id;
		const optionText = clickedData.text;

		const $targetSelect = $('#theQuery');

		if (e.type === 'select2:select') {
			// prevent duplicates
			if ($targetSelect.find('option[value="' + optionId + '"]').length === 0) {
				const newOption = new Option(optionText, optionId, true, true);
				$targetSelect.append(newOption);
			}
		}
		else if (e.type === 'select2:unselect') {
			$targetSelect.find('option[value="' + optionId + '"]').remove();
		}

		const $options = $targetSelect.find('option');
		$options.sort(function(a, b) {
			const valA = a.value;
			const valB = b.value;

			const isNumericA = !isNaN(valA) && !isNaN(parseInt(valA, 10));
			const isNumericB = !isNaN(valB) && !isNaN(parseInt(valB, 10));

			if (isNumericA && isNumericB) {
				return parseInt(valA, 10) - parseInt(valB, 10);
			} else {
				return valA.localeCompare(valB, undefined, { numeric: true, sensitivity: 'base'});
			}
		});

		$targetSelect.empty().append($options);

		$targetSelect.trigger('change');
	}
);

$('#lsa-appendregexTermButton').on('click', function(e) {
	e.preventDefault();

	// 1. get the regex string
	const pattern = $('#lsa-thePattern').val().trim();
	if (!pattern) {
		alert("Please enter a valid regular expression pattern.");
		return;
	}

	// console.log(pattern);
	// alert("the pattern string");

	// create the case-insensitive regex object
	let regex;
	try {
		regex = new RegExp(pattern, 'i');
	} catch(err) {
		alert("Invalid Regular Expression syntax: " + err.message);
		return;
	}

	// 2. identify which Select2 term wrapper is currently active
	$chunkSize	= 	$('input[name=lsa-chunkradio]:checked',	'#lsa-chunksize').val();

	let $activeSelect = null;
	if ($chunkSize == 'ch250') {
		$activeSelect = $('#lsa-term250-select2');
	} else {
		$activeSelect = $('lsa-term1000-select2');
	}

	// console.dir($activeSelect);
	// alert("$activeSelect");

	if (!$activeSelect || !$activeSelect.data('loaded')) {
		alert("Please select a search category and load the options first.");
		return;
	}

	// 3. get a copy of the array from Select2
	const dataset = $activeSelect.data('rawDataset');
	if (!dataset) {
		alert("Please wait for the options to finish loading or type a character to initialize.");
		return;
	}

	// console.table(dataset);
	// alert("dataset");

	const $targetSelect = $('#theQuery');

	let currentSelect2Values = $activeSelect.val() || [];
	let addedToSelect2Count = 0;
	let addedToTargetCount = 0;

	// 4. iterate over the array to test items against pattern
	$.each(dataset, function(index, item) {
		if (regex.test(item.text)) {
			const optionId = String(item.id);
			const optionText = item.text;

			// A. Update the Select2 visual control state
			if (currentSelect2Values.indexOf(optionId) === -1) {
				if ($activeSelect.find('option[value="' + '"]').length === 0) {
					$activeSelect.append(new Option(optionText, optionId, false, false));
				}
				currentSelect2Values.push(optionId);
				addedToSelect2Count++;
			}

			// prevent duplicates in theQuery set
			if ($targetSelect.find('option[value="' + optionId + '"]').length === 0) {
				// const newOption = new Option(optionText, optionId, true, true);
				$targetSelect.append(new Option(optionText, optionId, true, true));
				addedToTargetCount++;
			}
		}
	});

	// 5. if new elements were caught, trigger the custom sorting block
	if (addedToTargetCount > 0) {
		const $options = $targetSelect.find("option");

		$options.sort(function(a, b) {
			const valA = a.value;
			const valB = b.value;

			const isNumericA = !isNaN(valA) && !isNaN(parseInt(valA, 10));
			const isNumericB = !isNaN(valB) && !isNaN(parseInt(valB, 10));

			if (isNumericA && isNumericB) {
				return parseInt(valA, 10) - parseInt(valB, 10);
			} else {
				return valA.localeCompare(valB, undefined, { numeric: true, sensitivity: 'base'});
			}
		});

		$targetSelect.empty().append($options).trigger('change');
		// alert("Matched and added " + addedToTargetCount + " items to the query.");
	} else {
		alert("No matches for the pattern " + pattern);
	}
});