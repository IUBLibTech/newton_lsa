$(document).ready(function () {

	/* EXTEND STRING FUNCTIONALITY */
	$.string(String.prototype);

	/* set up variables and arrays the array to hold the symbols */
	var completedString;
	var symbolArr = new Array();

	/* remove item from array */
	Array.prototype.remove = function (s) {
		$.each(this, function (intIndex, objValue) {
			if (s == objValue) {
				this.splice(intIndex, 1);
			}
		})
	}

	/* add trim functionality */
	String.prototype.trim = function () {
		return this.replace(/^\s*|\s*$/g, '');
	}



	/* CLEAR FORM */
	$.fn.clearForm = function () {
		return this.each(function () {
			var type = this.type,
				tag = this.tagName.toLowerCase();
			
			if (tag == 'form') return
			$(':input', this).clearForm();
			
			if (type == 'text' || type == 'password' || tag == 'textarea') this.value = '';
			else if (type == 'checkbox' || type == 'radio') this.checked = false;
			else if (tag == 'select') this.selectedIndex = -1;
		});
	};

	var msie = false;



	/*
	 detect browser
	 must do something different in IE
	*/
	jQuery.each(jQuery.browser, function (i) {
		if (!$.support.cssFloat) {
			msie = true;
		}
	});

	/* fix for IE because val() returns garbage in IE */
	if (msie != true) {
		/* give symbols	curvy corner */
		$('.symbolImage').corner('2px').css('padding', '4px');
	}
	
	
	

	$('.key').toggle(function () {
			var giveBorder = $(this).children().filter(
				function() { 
					return $(this).css("display") == "inline-block" 
					}
				);
			$(giveBorder).css({
				"border": "2px solid #CC0000"
			});
		}, function () {
			var giveBorder = $(this).children().filter(
				function() { 
					return $(this).css("display") == "inline-block" 
					}
				);
			$(giveBorder).css({
				"border": "1px solid #CCC"
			});
	});


		/*
		add
		onClick functionality
		to symbols
		*/
	$('.key').on('click', function (e) {

		var theSymbol = $(this).children().filter(function() { return $(this).css("display") == "inline-block" });


		/* fade out effect */
		$(theSymbol).fadeOut('fast', function () {
			$(theSymbol).fadeIn('fast');
		});

		/* set up vars */
		if($(theSymbol).attr('n')) {
			var thisAlt = $(theSymbol).attr('n');
			var thisFlag = "span";
		} else {
			var thisAlt = $(theSymbol).attr('alt');
			var thisFlag = "image";
		}
		
		console.log(thisAlt);
		
		var finder = jQuery.inArray(thisAlt, symbolArr);

		if (finder == -1) {
			symbolArr.push(thisAlt);
		} else {
			symbolArr = jQuery.grep(symbolArr, function (n, i) {
				return (i != finder);
			});
		}

		/* empty div html */
		$('#symbolDrop').empty();
		$('#Symbols').empty();


		var searchString;
		var htmlString;

		/* 
		 loop through array and 
		 spit out string of images
		*/
		for (j = 0; j < symbolArr.length; j++) {
			
			if(thisFlag == "image") {
				
				var displayString = ('<img src="/newton-dev/img/unicode/pua_newton/' + symbolArr[j] + '.png" alt="' + symbolArr[j] + '" border="0" />');
				
			} else if(thisFlag == "span") {
				
				var displayString = ('<span class="symbolDisplay">&#x' + symbolArr[j] + ';</span>');		
			
			}
			if (j == 0) {
				searchString = 'UNx' + symbolArr[j].trim();
				htmlString = displayString.trim();
			} else {
				searchString += ' ' + 'UNx' + symbolArr[j];
				htmlString += ' ' + displayString;
			}
		};

		/* replace the values */
		$('#symbolDrop').html(htmlString);
		$('#Symbols').val(searchString);

		/* reset htmlString variable */
		htmlString = ' ';
		searchString = ' ';
	});


	/* For clear button	*/
	$('#clearDiv').click(function (e) {
		// empty div html
		$('#symbolDrop').empty().html(' ');
		$('#SearchForm').clearForm()

		$('.symbolImage').css({
			"border": "1px solid #CCC"
		});

		symbolArr = new Array();
		searchString = '';
		htmlString = '';
	});
});						