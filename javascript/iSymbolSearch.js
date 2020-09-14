$(document).ready(
function () {
    // EXTEND STRING FUNCTIONALITY
    $.string(String.prototype);
    
    /*
    set up variables and arrays
    the array to hold the symbols
    */
    var completedString;
    var symbolArr = new Array();
    
    /* remove item from array */
    Array.prototype.remove = function (s) {
        $.each(this,
        function (intIndex, objValue) {
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
            var type = this.type, tag = this.tagName.toLowerCase();
            if (tag == 'form')
            return $(':input', this).clearForm();
            if (type == 'text' || type == 'password' || tag == 'textarea')
            this.value = ''; else if (type == 'checkbox' || type == 'radio')
            this.checked = false; else if (tag == 'select')
            this.selectedIndex = - 1;
        });
    };
    
    var msie = false;
    
    // detect browser
    // must do something different in IE
    jQuery.each(jQuery.browser, function (i) {
        if ($.browser.msie) {
            msie = true;
        }
    });
    
    // fix for IE because val() returns garbage in IE
    if (msie != true) {
        /* give symbols curvy corners */
        $('.symbolImage').corners();
    }
    
    $('.symbolImage').toggle(
    function () {
        $(this).css({
            "border": "2px solid #CC0000"
        });
    },
    function () {
        $(this).css({
            "border": "1px solid #CCC"
        });
    });
    
    /*
    add onClick functionality
    to symbols
    */
    $('.symbolImage').click(function (e) {
        
        // fade out effect
        $(this).fadeOut('fast', function () {
            $(this).fadeIn('fast');
        });
        
        // set up vars
        var thisAlt = $(this).attr('alt');
        var thisName = $(this).attr('name');
        var finder = jQuery.inArray(thisAlt, symbolArr);
        
        if (finder == - 1) {
            symbolArr.push(thisAlt);
        } else {
            symbolArr = jQuery.grep(symbolArr, function (n, i) {
                return (i != finder);
            });
        }
        
        // empty div html
        $('#symbolDrop').empty();
        $('#Symbols').empty();
        
        
        var searchString;
        var htmlString;
        
        // loop through array and spit out string of images
        for (j = 0; j < symbolArr.length; j++) {
            var imageString = ('<img src="/newton-dev/img/unicode/pua_newton/' + symbolArr[j] + '.gif" alt="' + symbolArr[j] + '" border="0"/>');
            if (j == 0) {
                searchString = 'UNx' + symbolArr[j].trim();
                htmlString = imageString.trim();
            } else {
                searchString += ' ' + 'UNx' + symbolArr[j];
                htmlString += ' ' + imageString;
            }
        };
        
        // replace the values
        $('#symbolDrop').html(htmlString);
        $('#Symbols').val(searchString);
        
        // reset htmlString variable
        htmlString = ' ';
        searchString = ' ';
    });
    
    
    /*
    For clear button
    */
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